<?php

// If running via PHP CLI built-in web server, let static files be served directly
if (php_sapi_name() === 'cli-server') {
    $urlPath = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $filePath = __DIR__ . $urlPath;
    if ($urlPath !== '/' && file_exists($filePath) && !is_dir($filePath)) {
        return false;
    }
}

require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\Auth;
use App\Controllers\AuthController;
use App\Controllers\SuperAdminController;
use App\Controllers\PosController;
use App\Controllers\ProductController;
use App\Controllers\KasbonController;
use App\Controllers\ReportController;
use App\Controllers\SettingController;

// Initialize Database schema and seeds
Database::get();

// Initialize Session Auth
Auth::init();

// Configure views directory
Flight::set('flight.views.path', __DIR__ . '/views');
Flight::set('flight.views.extension', '.php');

// Register Error Handler for friendly messages on Cloud Hosting
Flight::map('error', function(\Throwable $ex) {
    http_response_code(500);
    $isJson = (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
              || (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], '/api/'));
    if ($isJson) {
        header('Content-Type: application/json');
        echo json_encode(['error' => true, 'message' => $ex->getMessage()]);
    } else {
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Kendala Sistem - Tokomu</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-slate-900 text-white min-h-screen flex items-center justify-center p-4'><div class='max-w-md w-full bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl text-center space-y-4'><div class='w-12 h-12 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center mx-auto text-2xl font-bold'>!</div><h1 class='text-lg font-bold'>Terjadi Kendala Sistem</h1><p class='text-sm text-slate-400'>" . htmlspecialchars($ex->getMessage()) . "</p><div class='pt-2'><a href='/' class='inline-block px-4 py-2 bg-emerald-600 hover:bg-emerald-500 rounded-xl text-sm font-semibold transition'>Kembali ke Beranda</a></div></div></body></html>";
    }
    exit;
});

// Helper to render page inside layout
function renderPage(string $view, array $data = [], string $title = 'Warung Sembako POS', string $activeNav = 'pos'): void {
    if (!Auth::check()) {
        if (Auth::isSuperAdmin()) {
            Flight::redirect('/gambut');
            exit;
        }
        Flight::redirect('/login');
        exit;
    }

    $store = Auth::store();
    $data['store'] = $store;
    $data['settings'] = $store ?: (Database::fetchOne("SELECT * FROM settings WHERE id = 1") ?: []);
    $data['isSuperAdmin'] = Auth::isSuperAdmin();
    
    // Normalize base URL
    $rawBase = Flight::request()->base;
    $baseUrl = ($rawBase === '/' || empty($rawBase)) ? '' : rtrim($rawBase, '/');
    $data['baseUrl'] = $baseUrl;

    // Fetch body content
    $content = Flight::view()->fetch($view, $data);
    
    // Render layout with content
    Flight::render('layout', [
        'content' => $content,
        'title' => $title,
        'activeNav' => $activeNav,
        'store' => $store,
        'settings' => $data['settings'],
        'isSuperAdmin' => Auth::isSuperAdmin(),
        'baseUrl' => $baseUrl
    ]);
}

// -------------------------------------------------------------
// STATIC FILE FALLBACK ROUTE (Ensures CSS/JS never 404)
// -------------------------------------------------------------
Flight::route('GET /public/*', function() {
    $url = Flight::request()->url;
    $filePath = __DIR__ . '/' . ltrim($url, '/');
    if (file_exists($filePath) && !is_dir($filePath)) {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimes = [
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'json' => 'application/json; charset=UTF-8',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff2' => 'font/woff2',
            'woff' => 'font/woff',
            'ttf' => 'font/ttf'
        ];
        $mime = $mimes[$ext] ?? 'application/octet-stream';
        header("Content-Type: $mime");
        readfile($filePath);
        exit;
    }
    Flight::notFound();
});

// -------------------------------------------------------------
// AUTHENTICATION & SECURITY MIDDLEWARE
// -------------------------------------------------------------
Flight::before('start', function(&$params = null, &$output = null) {
    $url = Flight::request()->url;
    // Allow static assets and public auth routes
    if (
        str_starts_with($url, '/public/') ||
        in_array($url, ['/login', '/register', '/logout', '/api/login', '/api/register', '/gambut/login', '/api/gambut/login', '/gambut/logout'])
    ) {
        return;
    }

    // Redirect legacy /admin to /gambut
    if ($url === '/admin' || str_starts_with($url, '/admin/')) {
        Flight::redirect('/gambut');
        exit;
    }

    // Protect Super Admin portal (/gambut)
    if (($url === '/gambut' || str_starts_with($url, '/gambut/')) && !in_array($url, ['/gambut/login', '/gambut/logout'])) {
        if (!Auth::isSuperAdmin()) {
            Flight::redirect('/gambut/login');
            exit;
        }
        return;
    }

    // Protect Super Admin APIs (except public login)
    if ((str_starts_with($url, '/api/gambut/') || str_starts_with($url, '/api/admin/')) && $url !== '/api/gambut/login') {
        if (!Auth::isSuperAdmin()) {
            header('Content-Type: application/json; charset=utf-8');
            Flight::halt(403, json_encode([
                'success' => false,
                'message' => 'Akses ditolak. Khusus Super Administrator.'
            ]));
        }
        return;
    }

    // Protect REST API endpoints
    if (str_starts_with($url, '/api/')) {
        if (!Auth::check() && !Auth::isSuperAdmin()) {
            header('Content-Type: application/json; charset=utf-8');
            Flight::halt(401, json_encode([
                'success' => false,
                'message' => 'Sesi login telah berakhir atau Anda belum masuk. Silakan login kembali.'
            ]));
        }
    }
});

// -------------------------------------------------------------
// SUPER ADMIN ROUTES (/gambut)
// -------------------------------------------------------------
Flight::route('GET /gambut/login', [SuperAdminController::class, 'loginPage']);
Flight::route('POST /api/gambut/login', [SuperAdminController::class, 'loginApi']);
Flight::route('GET /gambut/logout', [SuperAdminController::class, 'logout']);
Flight::route('POST /api/gambut/logout', [SuperAdminController::class, 'logout']);
Flight::route('GET /gambut', [SuperAdminController::class, 'dashboard']);
Flight::route('GET /admin', function() { Flight::redirect('/gambut'); });

// Super Admin APIs (/api/gambut/* & /api/admin/*)
Flight::route('GET /api/gambut/git-status', [SuperAdminController::class, 'getGitStatusApi']);
Flight::route('GET /api/admin/git-status', [SuperAdminController::class, 'getGitStatusApi']);

Flight::route('POST /api/gambut/git-pull', [SuperAdminController::class, 'gitPullApi']);
Flight::route('POST /api/admin/git-pull', [SuperAdminController::class, 'gitPullApi']);

Flight::route('POST /api/gambut/git-setup', [SuperAdminController::class, 'gitSetupApi']);
Flight::route('POST /api/admin/git-setup', [SuperAdminController::class, 'gitSetupApi']);

Flight::route('POST /api/gambut/store/impersonate', [SuperAdminController::class, 'impersonateStore']);
Flight::route('POST /api/admin/store/impersonate', [SuperAdminController::class, 'impersonateStore']);

Flight::route('POST /api/gambut/store/update-password', [SuperAdminController::class, 'updateStorePassword']);
Flight::route('POST /api/admin/store/update-password', [SuperAdminController::class, 'updateStorePassword']);

Flight::route('DELETE /api/gambut/store/@id', [SuperAdminController::class, 'deleteStore']);
Flight::route('DELETE /api/admin/store/@id', [SuperAdminController::class, 'deleteStore']);

// -------------------------------------------------------------
// AUTHENTICATION ROUTES (Multi-Store Login & Register)
// -------------------------------------------------------------
Flight::route('GET /login', [AuthController::class, 'loginPage']);
Flight::route('POST /api/login', [AuthController::class, 'loginApi']);
Flight::route('GET /register', [AuthController::class, 'registerPage']);
Flight::route('POST /api/register', [AuthController::class, 'registerApi']);
Flight::route('GET /logout', [AuthController::class, 'logout']);
Flight::route('POST /api/logout', [AuthController::class, 'logout']);

// -------------------------------------------------------------
// WEB UI ROUTES
// -------------------------------------------------------------

// POS Kasir Screen
Flight::route('GET /', function() {
    $categories = Database::fetchAll("SELECT * FROM categories ORDER BY sort_order ASC, name ASC");
    renderPage('pos', [
        'categories' => $categories
    ], 'Kasir POS - Warung Sembako', 'pos');
});

// Produk & Stok Management
Flight::route('GET /products', function() {
    $categories = Database::fetchAll("SELECT * FROM categories ORDER BY sort_order ASC, name ASC");
    renderPage('products', [
        'categories' => $categories
    ], 'Kelola Produk & Stok - Warung Sembako', 'products');
});

// Buku Kasbon / Utang Pelanggan
Flight::route('GET /kasbon', function() {
    renderPage('kasbon', [], 'Buku Kasbon Pelanggan - Warung Sembako', 'kasbon');
});

// Laporan & Riwayat Penjualan
Flight::route('GET /reports', function() {
    renderPage('reports', [], 'Laporan & Riwayat Penjualan - Warung Sembako', 'reports');
});

// Pengaturan Warung & Printer Mini
Flight::route('GET /settings', function() {
    $store = Auth::store() ?? Database::fetchOne("SELECT * FROM stores WHERE id = ?", [Auth::id() ?? 1]);
    renderPage('settings', [
        'settings' => $store
    ], 'Pengaturan Toko & Printer - Tokomu', 'settings');
});


// -------------------------------------------------------------
// REST API ENDPOINTS
// -------------------------------------------------------------

// POS Endpoints
Flight::route('GET /api/pos-catalog', [PosController::class, 'getPosCatalog']);
Flight::route('GET /api/pos-barcode', [PosController::class, 'scanBarcode']);
Flight::route('POST /api/pos-checkout', [PosController::class, 'checkout']);
Flight::route('GET /api/receipt/@id', [PosController::class, 'getReceipt']);

// Product & Stock Endpoints
Flight::route('GET /api/products', [ProductController::class, 'getAll']);
Flight::route('GET /api/products/@id', [ProductController::class, 'getOne']);
Flight::route('POST /api/products', [ProductController::class, 'save']);
Flight::route('POST /api/products/restock', [ProductController::class, 'restock']);
Flight::route('DELETE /api/products/@id', [ProductController::class, 'delete']);
Flight::route('GET /api/master-lookup', [ProductController::class, 'masterLookup']);
Flight::route('GET /api/master-search', [ProductController::class, 'masterSearch']);

// Kasbon Endpoints
Flight::route('GET /api/kasbon', [KasbonController::class, 'list']);
Flight::route('POST /api/kasbon/pay', [KasbonController::class, 'pay']);
Flight::route('GET /api/kasbon/@id/receipt', [KasbonController::class, 'getReceipt']);

// Report & Analytics Endpoints
Flight::route('GET /api/report-summary', [ReportController::class, 'getSummary']);
Flight::route('GET /api/transactions', [ReportController::class, 'getTransactions']);
Flight::route('GET /api/report-export-csv', [ReportController::class, 'exportCsv']);

// Settings, Backup & Restore
Flight::route('GET /api/settings', [SettingController::class, 'get']);
Flight::route('POST /api/settings', [SettingController::class, 'update']);
Flight::route('POST /api/settings/logo', [SettingController::class, 'uploadLogo']);
Flight::route('POST /api/settings/logo/remove', [SettingController::class, 'removeLogo']);
Flight::route('GET /api/backup', [SettingController::class, 'backup']);
Flight::route('POST /api/restore', [SettingController::class, 'restore']);
Flight::route('POST /api/reset-demo', [SettingController::class, 'resetDemo']);

// Start Flight
Flight::start();
