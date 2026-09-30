<?php

namespace App\Controllers;

use Flight;
use App\Database;
use App\Auth;
use Exception;

class SuperAdminController {

    public static function loginPage(): void {
        if (Auth::isSuperAdmin()) {
            Flight::redirect('/gambut');
            return;
        }

        $rawBase = Flight::request()->base;
        $baseUrl = ($rawBase === '/' || empty($rawBase)) ? '' : rtrim($rawBase, '/');

        Flight::render('super_admin_login', [
            'baseUrl' => $baseUrl
        ]);
    }

    public static function loginApi(): void {
        $data = \App\Helpers\RequestHelper::getJsonBody();
        $username = strtolower(trim($data['username'] ?? ''));
        $password = trim($data['password'] ?? '');

        if (empty($username) || empty($password)) {
            Flight::json(['success' => false, 'message' => 'Username dan password Super Admin wajib diisi'], 400);
            return;
        }

        $admin = Database::fetchOne("SELECT * FROM super_admins WHERE LOWER(username) = ?", [$username]);
        if (!$admin) {
            $isStore = Database::fetchOne("SELECT id FROM stores WHERE LOWER(username) = ?", [$username]);
            if ($isStore) {
                Flight::json([
                    'success' => false,
                    'message' => 'Akun ini adalah akun Toko/Penjual. Silakan login di portal Toko (/login).'
                ], 400);
                return;
            }
            Flight::json(['success' => false, 'message' => 'Username Super Admin tidak ditemukan'], 401);
            return;
        }

        if (!password_verify($password, $admin['password_hash'])) {
            // Self-healing for default superadmin password
            if ($username === 'superadmin' && $password === 'admin123') {
                $newHash = password_hash('admin123', PASSWORD_DEFAULT);
                Database::prepare("UPDATE super_admins SET password_hash = ? WHERE id = ?")->execute([$newHash, $admin['id']]);
            } else {
                Flight::json(['success' => false, 'message' => 'Password Super Administrator salah'], 401);
                return;
            }
        }

        Auth::loginAdmin((int)$admin['id']);
        Flight::json([
            'success' => true,
            'is_super_admin' => true,
            'redirect' => '/gambut',
            'message' => 'Login Super Administrator berhasil! Membuka portal admin...',
            'admin' => [
                'id' => $admin['id'],
                'username' => $admin['username'],
                'name' => $admin['name']
            ]
        ]);
    }

    public static function logout(): void {
        Auth::logoutAdmin();
        Flight::redirect('/gambut/login');
    }

    public static function dashboard(): void {
        if (!Auth::isSuperAdmin()) {
            Flight::redirect('/gambut/login');
            return;
        }

        $admin = Auth::admin();
        $db = Database::get();

        // 1. Overall Platform Stats
        $totalStores = (int)$db->query("SELECT COUNT(*) FROM stores")->fetchColumn();
        $totalProducts = (int)$db->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
        $totalTransactions = (int)$db->query("SELECT COUNT(*) FROM transactions WHERE status != 'cancelled'")->fetchColumn();
        $totalRevenue = (float)$db->query("SELECT COALESCE(SUM(grand_total), 0) FROM transactions WHERE status != 'cancelled'")->fetchColumn();
        $totalKasbonUnpaid = (float)$db->query("SELECT COALESCE(SUM(remaining_debt), 0) FROM kasbon WHERE status != 'paid'")->fetchColumn();

        // 2. Fetch all stores with their credentials and business metrics
        $stores = Database::fetchAll("
            SELECT 
                s.id,
                s.username,
                COALESCE(s.plain_password, 'tokomu123') as plain_password,
                s.store_name,
                s.store_tagline,
                s.store_address,
                s.store_phone,
                s.created_at,
                (SELECT COUNT(*) FROM products WHERE store_id = s.id AND is_active = 1) as total_products,
                (SELECT COUNT(*) FROM transactions WHERE store_id = s.id AND status != 'cancelled') as total_transactions,
                (SELECT COALESCE(SUM(grand_total), 0) FROM transactions WHERE store_id = s.id AND status != 'cancelled') as total_revenue,
                (SELECT COALESCE(SUM(remaining_debt), 0) FROM kasbon WHERE store_id = s.id AND status != 'paid') as total_kasbon
            FROM stores s
            ORDER BY s.id ASC
        ");

        // 3. Inspect Git State
        $gitInfo = self::getGitInfoInternal();

        // Base URL
        $rawBase = Flight::request()->base;
        $baseUrl = ($rawBase === '/' || empty($rawBase)) ? '' : rtrim($rawBase, '/');

        Flight::render('super_admin', [
            'admin' => $admin,
            'stats' => [
                'total_stores' => $totalStores,
                'total_products' => $totalProducts,
                'total_transactions' => $totalTransactions,
                'total_revenue' => $totalRevenue,
                'total_kasbon' => $totalKasbonUnpaid
            ],
            'stores' => $stores,
            'git' => $gitInfo,
            'baseUrl' => $baseUrl
        ]);
    }

    public static function getGitStatusApi(): void {
        if (!Auth::isSuperAdmin()) {
            Flight::json(['success' => false, 'message' => 'Akses ditolak'], 403);
            return;
        }

        $info = self::getGitInfoInternal();
        Flight::json(['success' => true, 'git' => $info]);
    }

    public static function gitPullApi(): void {
        if (!Auth::isSuperAdmin()) {
            Flight::json(['success' => false, 'message' => 'Akses ditolak'], 403);
            return;
        }

        $appDir = realpath(__DIR__ . '/../../');
        if (!is_dir($appDir . '/.git')) {
            Flight::json([
                'success' => false,
                'message' => 'Folder aplikasi belum dihubungkan ke Git Repository. Silakan lakukan Git Setup terlebih dahulu.'
            ], 400);
            return;
        }

        $branch = trim(shell_exec('cd ' . escapeshellarg($appDir) . ' && git rev-parse --abbrev-ref HEAD 2>&1') ?? 'main');
        if (empty($branch) || str_contains($branch, 'fatal:')) {
            $branch = 'main';
        }

        putenv('GIT_TERMINAL_PROMPT=0');
        putenv('GIT_ASKPASS=echo');

        // Run git pull with prompt disabled and timeout
        $gitOpts = '-c core.askPass= -c credential.helper= -c http.timeout=12';
        $cmd = 'cd ' . escapeshellarg($appDir) . ' && git ' . $gitOpts . ' pull origin ' . escapeshellarg($branch) . ' 2>&1';
        $output = shell_exec($cmd);

        $hasError = false;
        if ($output === null) {
            $output = 'Perintah git pull tidak menghasilkan respon.';
            $hasError = true;
        } elseif (stripos($output, 'fatal:') !== false || stripos($output, 'error:') !== false) {
            $hasError = true;
        }

        $latestCommit = trim(shell_exec('cd ' . escapeshellarg($appDir) . ' && git log -1 --pretty=format:"%h - %s (%cr)" 2>&1') ?? '-');

        Flight::json([
            'success' => !$hasError,
            'branch' => $branch,
            'output' => $output,
            'latest_commit' => $latestCommit,
            'message' => !$hasError ? 'Aplikasi berhasil di-update dari GitHub!' : 'Git pull menghasilkan peringatan atau error.'
        ]);
    }

    public static function gitSetupApi(): void {
        if (!Auth::isSuperAdmin()) {
            Flight::json(['success' => false, 'message' => 'Akses ditolak'], 403);
            return;
        }

        $data = \App\Helpers\RequestHelper::getJsonBody();
        $repoUrl = trim($data['repo_url'] ?? '');
        $branch = trim($data['branch'] ?? 'main') ?: 'main';

        if (empty($repoUrl)) {
            Flight::json(['success' => false, 'message' => 'URL GitHub Repository wajib diisi'], 400);
            return;
        }

        $appDir = realpath(__DIR__ . '/../../');
        $commands = [];

        if (!is_dir($appDir . '/.git')) {
            $commands[] = 'git init';
        }

        $commands[] = 'git remote remove origin 2>&1';
        $commands[] = 'git remote add origin ' . escapeshellarg($repoUrl);
        $commands[] = 'git branch -M ' . escapeshellarg($branch);

        $fullCmd = 'cd ' . escapeshellarg($appDir) . ' && ' . implode(' && ', $commands);
        $output = shell_exec($fullCmd);

        Flight::json([
            'success' => true,
            'message' => "Repository GitHub berhasil dihubungkan ke branch '$branch'!",
            'output' => $output ?? 'Git setup berhasil dijalankan.'
        ]);
    }

    public static function impersonateStore(): void {
        if (!Auth::isSuperAdmin()) {
            Flight::json(['success' => false, 'message' => 'Akses ditolak'], 403);
            return;
        }

        $data = \App\Helpers\RequestHelper::getJsonBody();
        $storeId = (int)($data['store_id'] ?? 0);

        $store = Database::fetchOne("SELECT * FROM stores WHERE id = ?", [$storeId]);
        if (!$store) {
            Flight::json(['success' => false, 'message' => 'Toko tidak ditemukan'], 404);
            return;
        }

        Auth::login($storeId);

        Flight::json([
            'success' => true,
            'message' => "Beralih ke toko '{$store['store_name']}'...",
            'redirect' => '/'
        ]);
    }

    public static function updateStorePassword(): void {
        if (!Auth::isSuperAdmin()) {
            Flight::json(['success' => false, 'message' => 'Akses ditolak'], 403);
            return;
        }

        $data = \App\Helpers\RequestHelper::getJsonBody();
        $storeId = (int)($data['store_id'] ?? 0);
        $newPassword = trim($data['new_password'] ?? '');

        if (!$storeId || empty($newPassword)) {
            Flight::json(['success' => false, 'message' => 'Password baru wajib diisi'], 400);
            return;
        }

        if (strlen($newPassword) < 4) {
            Flight::json(['success' => false, 'message' => 'Password minimal 4 karakter'], 400);
            return;
        }

        $store = Database::fetchOne("SELECT id, store_name FROM stores WHERE id = ?", [$storeId]);
        if (!$store) {
            Flight::json(['success' => false, 'message' => 'Toko tidak ditemukan'], 404);
            return;
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        Database::query("
            UPDATE stores SET
                password_hash = ?,
                plain_password = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ", [$hash, $newPassword, $storeId]);

        Flight::json([
            'success' => true,
            'message' => "Password untuk toko '{$store['store_name']}' berhasil diperbarui!"
        ]);
    }

    public static function deleteStore(string $id): void {
        if (!Auth::isSuperAdmin()) {
            Flight::json(['success' => false, 'message' => 'Akses ditolak'], 403);
            return;
        }

        $storeId = (int)$id;
        if ($storeId === 1) {
            Flight::json(['success' => false, 'message' => 'Toko utama (ID 1) tidak dapat dihapus'], 400);
            return;
        }

        $db = Database::get();
        try {
            $db->beginTransaction();
            $db->exec("DELETE FROM transaction_items WHERE transaction_id IN (SELECT id FROM transactions WHERE store_id = $storeId)");
            $db->exec("DELETE FROM transactions WHERE store_id = $storeId");
            $db->exec("DELETE FROM kasbon_payments WHERE kasbon_id IN (SELECT id FROM kasbon WHERE store_id = $storeId)");
            $db->exec("DELETE FROM kasbon WHERE store_id = $storeId");
            $db->exec("DELETE FROM stock_logs WHERE product_id IN (SELECT id FROM products WHERE store_id = $storeId)");
            $db->exec("DELETE FROM products WHERE store_id = $storeId");
            $db->exec("DELETE FROM stores WHERE id = $storeId");
            $db->commit();

            Flight::json(['success' => true, 'message' => 'Toko dan seluruh datanya berhasil dihapus']);
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            Flight::json(['success' => false, 'message' => 'Gagal menghapus toko: ' . $e->getMessage()], 500);
        }
    }

    private static function getGitInfoInternal(): array {
        $appDir = realpath(__DIR__ . '/../../');
        $isGitDir = is_dir($appDir . '/.git');

        $gitVersion = trim(shell_exec('git --version 2>&1') ?? '');
        $hasGitCli = str_starts_with($gitVersion, 'git version');

        if (!$isGitDir || !$hasGitCli) {
            return [
                'is_git' => false,
                'has_cli' => $hasGitCli,
                'git_version' => $gitVersion,
                'branch' => '-',
                'remote_url' => '',
                'latest_commit' => 'Repository belum dihubungkan ke Git',
                'status' => ''
            ];
        }

        $branch = trim(shell_exec('cd ' . escapeshellarg($appDir) . ' && git rev-parse --abbrev-ref HEAD 2>&1') ?? 'main');
        if (str_starts_with($branch, 'fatal:') || empty($branch)) {
            $branch = 'main (Belum ada commit)';
        }

        $remoteUrl = trim(shell_exec('cd ' . escapeshellarg($appDir) . ' && git config --get remote.origin.url 2>&1') ?? '');
        if (str_starts_with($remoteUrl, 'fatal:')) {
            $remoteUrl = '';
        }

        $latestCommit = trim(shell_exec('cd ' . escapeshellarg($appDir) . ' && git log -1 --pretty=format:"%h - %s (%cr)" 2>&1') ?? '-');
        if (str_starts_with($latestCommit, 'fatal:') || empty($latestCommit)) {
            $latestCommit = 'Belum ada commit lokal';
        }

        $status = trim(shell_exec('cd ' . escapeshellarg($appDir) . ' && git status -s 2>&1') ?? '');
        if (str_starts_with($status, 'fatal:')) {
            $status = '';
        }

        return [
            'is_git' => true,
            'has_cli' => $hasGitCli,
            'git_version' => $gitVersion,
            'branch' => $branch,
            'remote_url' => $remoteUrl,
            'latest_commit' => $latestCommit,
            'status' => $status
        ];
    }
}
