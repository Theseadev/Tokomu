<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($title ?? 'Tokomu - Sistem Kasir Pintar') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#f0f4f9',
                            100: '#dbe6f5',
                            200: '#b9d0ec',
                            300: '#87b0df',
                            400: '#538ece',
                            500: '#2563eb',
                            600: '#1e40af',
                            700: '#1e3a8a',
                            800: '#172554',
                            900: '#0f172a',
                            950: '#0a0f1d',
                        },
                        navy: {
                            50: '#f0f4f9',
                            100: '#dbe6f5',
                            200: '#b9d0ec',
                            300: '#87b0df',
                            400: '#538ece',
                            500: '#2563eb',
                            600: '#1e40af',
                            700: '#1e3a8a',
                            800: '#172554',
                            900: '#0f172a',
                            950: '#0a0f1d',
                        }
                    }
                }
            }
        }
    </script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* SweetAlert2 Tokomu Navy theme enhancements */
        .swal2-popup {
            font-family: inherit !important;
            border-radius: 1.5rem !important;
        }
        .swal2-title {
            font-size: 1.1rem !important;
            font-weight: 800 !important;
            color: #1e293b !important;
        }
        .swal2-html-container {
            font-size: 0.85rem !important;
            color: #475569 !important;
            line-height: 1.5 !important;
        }
        .swal2-actions {
            margin-top: 1.25rem !important;
            gap: 0.5rem !important;
        }
        .swal2-confirm {
            background-color: #1e3a8a !important;
            border-radius: 0.75rem !important;
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            padding: 0.65rem 1.25rem !important;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25) !important;
        }
        .swal2-cancel {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            padding: 0.65rem 1.15rem !important;
        }

        /* Clean Horizontal Scroll (Hides native scrollbars on pill buttons) */
        .no-scrollbar::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .no-scrollbar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }

        /* Mobile Bottom Nav Micro-Animations & Glow Effects */
        .bottom-nav-item {
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }
        .bottom-nav-item:hover .nav-icon-box {
            transform: translateY(-4px) scale(1.12);
            box-shadow: 0 10px 22px -3px rgba(30, 58, 138, 0.28);
        }
        .bottom-nav-item:active .nav-icon-box {
            transform: translateY(0px) scale(0.92);
            transition-duration: 0.1s;
        }

        /* Interactive Icon Hover Micro-Gestures */
        .bottom-nav-item:hover .icon-cart {
            animation: nav-cart-bounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .bottom-nav-item:hover .icon-product {
            animation: nav-box-pop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .bottom-nav-item:hover .icon-kasbon {
            animation: nav-book-flutter 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .bottom-nav-item:hover .icon-report {
            animation: nav-chart-surge 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .bottom-nav-item:hover .icon-account {
            animation: nav-user-wiggle 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes nav-cart-bounce {
            0%, 100% { transform: rotate(0deg) scale(1); }
            30% { transform: translateY(-2px) rotate(-14deg) scale(1.15); }
            65% { transform: translateY(-1px) rotate(10deg) scale(1.12); }
        }
        @keyframes nav-box-pop {
            0%, 100% { transform: scale(1) rotate(0deg); }
            40% { transform: translateY(-3px) scale(1.22) rotate(-8deg); }
            70% { transform: translateY(-1px) scale(1.12) rotate(4deg); }
        }
        @keyframes nav-book-flutter {
            0%, 100% { transform: scale(1) rotate(0deg); }
            35% { transform: translateY(-2px) scale(1.2) rotate(-10deg); }
            70% { transform: translateY(-1px) scale(1.12) rotate(6deg); }
        }
        @keyframes nav-chart-surge {
            0%, 100% { transform: translateY(0) scale(1); }
            45% { transform: translateY(-4px) scale(1.25); }
            75% { transform: translateY(-1px) scale(1.1); }
        }
        @keyframes nav-user-wiggle {
            0%, 100% { transform: scale(1) rotate(0deg); }
            30% { transform: translateY(-2px) rotate(-12deg) scale(1.15); }
            70% { transform: translateY(-1px) rotate(12deg) scale(1.12); }
        }
    </style>
    <script>
        // SweetAlert2 Warung Sembako Global Setup
        const WarungSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-3xl shadow-2xl border border-slate-100 p-6',
                title: 'text-base font-bold text-slate-800',
                htmlContainer: 'text-xs text-slate-600 font-medium',
                confirmButton: 'py-2.5 px-5 rounded-xl font-bold text-xs bg-emerald-600 hover:bg-emerald-700 text-white shadow-md active:scale-95 transition mx-1',
                cancelButton: 'py-2.5 px-4 rounded-xl font-bold text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 active:scale-95 transition mx-1'
            },
            buttonsStyling: false
        });

        window.showSuccess = function(message, title = 'Berhasil!') {
            return WarungSwal.fire({
                icon: 'success',
                title: title,
                text: message,
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true
            });
        };

        window.showError = function(message, title = 'Perhatian') {
            return WarungSwal.fire({
                icon: 'error',
                title: title,
                text: message,
                confirmButtonText: 'Mengerti'
            });
        };

        window.showWarning = function(message, title = 'Peringatan') {
            return WarungSwal.fire({
                icon: 'warning',
                title: title,
                text: message,
                confirmButtonText: 'Mengerti'
            });
        };

        window.showConfirm = async function(message, title = 'Konfirmasi', confirmBtn = 'Ya, Lanjutkan', cancelBtn = 'Batal') {
            const res = await WarungSwal.fire({
                icon: 'question',
                title: title,
                text: message,
                showCancelButton: true,
                confirmButtonText: confirmBtn,
                cancelButtonText: cancelBtn,
                reverseButtons: true
            });
            return res.isConfirmed;
        };

        window.showToast = function(message, icon = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            return Toast.fire({
                icon: icon,
                title: message
            });
        };

        window.confirmLogout = async function(e) {
            if (e) e.preventDefault();
            const confirmed = await window.showConfirm(
                'Keluar dari sesi toko saat ini? Anda dapat masuk kembali dengan username toko Anda.',
                'Ganti / Keluar Toko',
                'Ya, Keluar',
                'Tetap di Sini'
            );
            if (confirmed) {
                window.location.href = '<?= $baseUrl ?? '' ?>/logout';
            }
            return false;
        };

        // Seamless override for default window.alert
        window.alert = function(msg) {
            if (!msg) return;
            const str = String(msg);
            if (str.toLowerCase().includes('berhasil') || str.toLowerCase().includes('sukses')) {
                return window.showSuccess(str);
            }
            if (str.toLowerCase().includes('gagal') || str.toLowerCase().includes('kesalahan') || str.toLowerCase().includes('error')) {
                return window.showError(str);
            }
            return window.showWarning(str);
        };
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?= $baseUrl ?? '' ?>/public/css/app.css">
    <!-- Confetti for checkout celebration -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.4/dist/confetti.browser.min.js"></script>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen flex flex-col">

    <!-- Top Navigation Bar -->
    <header class="bg-emerald-700 text-white shadow-sm sticky top-0 z-40 border-b border-emerald-800/40">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 flex items-center justify-between h-14 sm:h-16 gap-2">
            <!-- Warung Logo & Name -->
            <div class="flex items-center min-w-0 flex-1 sm:flex-initial">
                <a href="<?= $baseUrl ?? '' ?>/" class="flex items-center space-x-2 sm:space-x-2.5 group min-w-0">
                    <div id="nav-store-avatar" class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white/15 flex items-center justify-center text-white border border-white/20 shadow-inner group-hover:bg-white/25 transition flex-shrink-0 overflow-hidden">
                        <?php if (!empty($settings['store_logo'])): ?>
                            <img src="<?= htmlspecialchars($settings['store_logo']) ?>" alt="Logo" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i data-lucide="store" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-sm sm:text-base leading-tight tracking-tight flex items-center gap-1.5 truncate">
                            <span id="nav-store-name" class="truncate"><?= htmlspecialchars($settings['store_name'] ?? 'Tokomu') ?></span>
                            <span class="hidden sm:inline-block text-[10px] bg-emerald-500 text-white font-bold px-1.5 py-0.5 rounded-full uppercase tracking-wider">POS</span>
                        </div>
                        <div class="text-[11px] text-emerald-200 truncate hidden sm:block" id="nav-store-tagline"><?= htmlspecialchars($settings['store_tagline'] ?? 'Sistem Kasir & Struk Thermal') ?></div>
                    </div>
                </a>
            </div>

            <!-- Main Navigation Links -->
            <nav class="hidden md:flex items-center space-x-1 font-medium text-sm">
                <a href="<?= $baseUrl ?? '' ?>/" class="px-3.5 py-2 rounded-lg flex items-center space-x-1.5 transition <?= ($activeNav ?? '') === 'pos' ? 'bg-emerald-800 text-white shadow-sm' : 'text-emerald-100 hover:bg-emerald-600 hover:text-white' ?>">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    <span>Kasir POS</span>
                </a>
                <a href="<?= $baseUrl ?? '' ?>/products" class="px-3.5 py-2 rounded-lg flex items-center space-x-1.5 transition <?= ($activeNav ?? '') === 'products' ? 'bg-emerald-800 text-white shadow-sm' : 'text-emerald-100 hover:bg-emerald-600 hover:text-white' ?>">
                    <i data-lucide="package" class="w-4 h-4"></i>
                    <span>Produk & Stok</span>
                </a>
                <a href="<?= $baseUrl ?? '' ?>/kasbon" class="px-3.5 py-2 rounded-lg flex items-center space-x-1.5 transition <?= ($activeNav ?? '') === 'kasbon' ? 'bg-emerald-800 text-white shadow-sm' : 'text-emerald-100 hover:bg-emerald-600 hover:text-white' ?>">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                    <span>Buku Kasbon</span>
                </a>
                <a href="<?= $baseUrl ?? '' ?>/reports" class="px-3.5 py-2 rounded-lg flex items-center space-x-1.5 transition <?= ($activeNav ?? '') === 'reports' ? 'bg-emerald-800 text-white shadow-sm' : 'text-emerald-100 hover:bg-emerald-600 hover:text-white' ?>">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                    <span>Laporan</span>
                </a>
                <a href="<?= $baseUrl ?? '' ?>/settings" class="px-3.5 py-2 rounded-lg flex items-center space-x-1.5 transition <?= ($activeNav ?? '') === 'settings' ? 'bg-emerald-800 text-white shadow-sm' : 'text-emerald-100 hover:bg-emerald-600 hover:text-white' ?>">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    <span>Akun</span>
                </a>
            </nav>

            <!-- Thermal Printer Status & Logout -->
            <div class="flex items-center flex-shrink-0 space-x-2">
                <?php if (!empty($isSuperAdmin)): ?>
                    <a href="<?= $baseUrl ?? '' ?>/gambut" class="flex items-center space-x-1 px-3 py-1.5 rounded-full text-xs font-black bg-gradient-to-r from-amber-500 to-orange-500 text-slate-950 transition shadow-md hover:brightness-110 active-press" title="Kembali ke Portal Super Admin">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        <span>Super Admin</span>
                    </a>
                <?php endif; ?>

                <button id="btn-printer-modal" onclick="openPrinterModal()" class="flex items-center space-x-1.5 px-2.5 sm:px-3 py-1.5 rounded-full text-xs font-semibold bg-white text-slate-800 hover:bg-slate-50 transition shadow-sm border border-emerald-200/50 flex-shrink-0 whitespace-nowrap active-press" title="Pengaturan Printer Mini">
                    <span id="printer-status-dot" class="w-2 h-2 rounded-full bg-slate-400"></span>
                    <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-600"></i>
                    <span id="printer-status-text" class="text-[11px] sm:text-xs font-bold">Printer</span>
                </button>

                <a href="<?= $baseUrl ?? '' ?>/logout" onclick="return confirmLogout(event)" class="flex items-center space-x-1 px-2.5 sm:px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-800/80 hover:bg-rose-600 text-white transition shadow-sm border border-white/10 flex-shrink-0 whitespace-nowrap active-press" title="Keluar / Ganti Toko">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Keluar</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Mobile Bottom Navigation Bar (Ultra-modern Glassmorphic Animated Style) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-xl border-t border-slate-200/90 shadow-[0_-8px_30px_rgba(15,23,42,0.08)] px-2 pt-2 pb-2.5 flex items-center justify-around select-none">
        <!-- 1. Kasir -->
        <a href="<?= $baseUrl ?? '' ?>/" class="bottom-nav-item group flex-1 flex flex-col items-center py-0.5 rounded-2xl transition-all">
            <div class="nav-icon-box w-10 h-10 rounded-2xl flex items-center justify-center transition-all duration-300 <?= ($activeNav ?? '') === 'pos' ? 'bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white shadow-lg shadow-blue-900/25 scale-105 border border-blue-500/30' : 'text-slate-400 group-hover:text-blue-900 group-hover:bg-blue-50/80' ?>">
                <i data-lucide="shopping-cart" class="w-5 h-5 icon-cart"></i>
            </div>
            <span class="text-[10px] tracking-tight mt-1 transition-colors <?= ($activeNav ?? '') === 'pos' ? 'text-blue-950 font-black' : 'text-slate-400 group-hover:text-slate-700 font-semibold' ?>">Kasir</span>
        </a>

        <!-- 2. Produk & Stok -->
        <a href="<?= $baseUrl ?? '' ?>/products" class="bottom-nav-item group flex-1 flex flex-col items-center py-0.5 rounded-2xl transition-all">
            <div class="nav-icon-box w-10 h-10 rounded-2xl flex items-center justify-center transition-all duration-300 <?= ($activeNav ?? '') === 'products' ? 'bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white shadow-lg shadow-blue-900/25 scale-105 border border-blue-500/30' : 'text-slate-400 group-hover:text-blue-900 group-hover:bg-blue-50/80' ?>">
                <i data-lucide="package" class="w-5 h-5 icon-product"></i>
            </div>
            <span class="text-[10px] tracking-tight mt-1 transition-colors <?= ($activeNav ?? '') === 'products' ? 'text-blue-950 font-black' : 'text-slate-400 group-hover:text-slate-700 font-semibold' ?>">Produk</span>
        </a>

        <!-- 3. Buku Kasbon -->
        <a href="<?= $baseUrl ?? '' ?>/kasbon" class="bottom-nav-item group flex-1 flex flex-col items-center py-0.5 rounded-2xl transition-all">
            <div class="nav-icon-box w-10 h-10 rounded-2xl flex items-center justify-center transition-all duration-300 <?= ($activeNav ?? '') === 'kasbon' ? 'bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white shadow-lg shadow-blue-900/25 scale-105 border border-blue-500/30' : 'text-slate-400 group-hover:text-blue-900 group-hover:bg-blue-50/80' ?>">
                <i data-lucide="book-open" class="w-5 h-5 icon-kasbon"></i>
            </div>
            <span class="text-[10px] tracking-tight mt-1 transition-colors <?= ($activeNav ?? '') === 'kasbon' ? 'text-blue-950 font-black' : 'text-slate-400 group-hover:text-slate-700 font-semibold' ?>">Kasbon</span>
        </a>

        <!-- 4. Laporan -->
        <a href="<?= $baseUrl ?? '' ?>/reports" class="bottom-nav-item group flex-1 flex flex-col items-center py-0.5 rounded-2xl transition-all">
            <div class="nav-icon-box w-10 h-10 rounded-2xl flex items-center justify-center transition-all duration-300 <?= ($activeNav ?? '') === 'reports' ? 'bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white shadow-lg shadow-blue-900/25 scale-105 border border-blue-500/30' : 'text-slate-400 group-hover:text-blue-900 group-hover:bg-blue-50/80' ?>">
                <i data-lucide="bar-chart-3" class="w-5 h-5 icon-report"></i>
            </div>
            <span class="text-[10px] tracking-tight mt-1 transition-colors <?= ($activeNav ?? '') === 'reports' ? 'text-blue-950 font-black' : 'text-slate-400 group-hover:text-slate-700 font-semibold' ?>">Laporan</span>
        </a>

        <!-- 5. Akun / Pengaturan -->
        <a href="<?= $baseUrl ?? '' ?>/settings" class="bottom-nav-item group flex-1 flex flex-col items-center py-0.5 rounded-2xl transition-all">
            <div class="nav-icon-box w-10 h-10 rounded-2xl flex items-center justify-center transition-all duration-300 <?= ($activeNav ?? '') === 'settings' ? 'bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white shadow-lg shadow-blue-900/25 scale-105 border border-blue-500/30' : 'text-slate-400 group-hover:text-blue-900 group-hover:bg-blue-50/80' ?>">
                <i data-lucide="user" class="w-5 h-5 icon-account"></i>
            </div>
            <span class="text-[10px] tracking-tight mt-1 transition-colors <?= ($activeNav ?? '') === 'settings' ? 'text-blue-950 font-black' : 'text-slate-400 group-hover:text-slate-700 font-semibold' ?>">Akun</span>
        </a>
    </nav>

    <!-- Main Content Container -->
    <main class="flex-1 flex flex-col pb-20 md:pb-2">
        <?= $content ?? '' ?>
    </main>

    <!-- Global Thermal Printer Connection Modal -->
    <div id="printer-modal" class="fixed inset-0 z-50 hidden modal-backdrop-blur flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm sm:max-w-md w-full my-auto overflow-hidden border border-slate-200/90 max-h-[88vh] flex flex-col">
            <div class="bg-emerald-700 px-4 py-2.5 sm:px-5 sm:py-3 text-white flex items-center justify-between flex-shrink-0">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="printer" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-xs sm:text-sm leading-tight">Pengaturan Printer Mini</h3>
                        <p class="text-[10px] text-emerald-200">Bluetooth, USB Serial & Dialog Cetak</p>
                    </div>
                </div>
                <button onclick="closePrinterModal()" class="text-emerald-200 hover:text-white p-1 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </button>
            </div>

            <div class="p-3.5 sm:p-4.5 space-y-3 text-xs overflow-y-auto flex-1">
                <!-- Status Box -->
                <div id="modal-printer-status-box" class="p-2.5 sm:p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Status Printer</div>
                        <div id="modal-printer-status-name" class="font-bold text-xs sm:text-sm text-slate-800">Memeriksa koneksi...</div>
                    </div>
                    <div id="modal-status-badge" class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                        Siap Terhubung
                    </div>
                </div>

                <!-- Connection Options -->
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider">Pilih Metode Sambungan</label>
                    
                    <!-- Bluetooth Option -->
                    <button type="button" onclick="connectPrinterBluetooth()" class="w-full flex items-center justify-between p-2.5 sm:p-3 rounded-xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition text-left group">
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition">
                                <i data-lucide="bluetooth" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-xs sm:text-sm text-slate-800 truncate">Printer Mini Bluetooth</div>
                                <div class="text-[10px] sm:text-xs text-slate-500 truncate">Panda, Iware, RPP02, GOOJPRT, dll.</div>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md flex-shrink-0">Hubungkan</span>
                    </button>

                    <!-- USB Serial Option -->
                    <button type="button" onclick="connectPrinterUsb()" class="w-full flex items-center justify-between p-2.5 sm:p-3 rounded-xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition text-left group">
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition">
                                <i data-lucide="usb" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-xs sm:text-sm text-slate-800 truncate">Printer USB Kabel (Serial)</div>
                                <div class="text-[10px] sm:text-xs text-slate-500 truncate">Kabel USB langsung laptop ke printer</div>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md flex-shrink-0">Pilih Port</span>
                    </button>

                    <!-- System Print Option -->
                    <button type="button" onclick="selectSystemPrint()" class="w-full flex items-center justify-between p-2.5 sm:p-3 rounded-xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition text-left group">
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-xs sm:text-sm text-slate-800 truncate">Cetak Windows / Browser Print</div>
                                <div class="text-[10px] sm:text-xs text-slate-500 truncate">Driver printer Windows / simpan PDF</div>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md flex-shrink-0">Pilih</span>
                    </button>
                </div>

                <!-- Paper Size Setting -->
                <div class="pt-2 border-t border-slate-100">
                    <label class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Ukuran Lebar Kertas Struk</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" id="btn-paper-58" onclick="setPaperWidth('58mm')" class="py-2 px-2.5 rounded-lg border text-center font-bold text-xs transition border-emerald-500 bg-emerald-50 text-emerald-800">
                            58 mm (Standar)
                        </button>
                        <button type="button" id="btn-paper-80" onclick="setPaperWidth('80mm')" class="py-2 px-2.5 rounded-lg border text-center font-bold text-xs transition border-slate-200 text-slate-600 hover:bg-slate-50">
                            80 mm (Lebar)
                        </button>
                    </div>
                </div>

                <!-- Action Buttons: Test Print & Disconnect -->
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2 flex-shrink-0">
                    <button type="button" onclick="testThermalPrint()" class="flex-1 py-2 px-3 rounded-lg bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition flex items-center justify-center space-x-1 shadow-xs active:scale-95">
                        <i data-lucide="play" class="w-3.5 h-3.5"></i>
                        <span>Tes Cetak Struk</span>
                    </button>
                    <button type="button" onclick="disconnectPrinter()" class="py-2 px-3 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold transition active:scale-95">
                        Putus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Receipt Preview Modal -->
    <div id="receipt-preview-modal" class="fixed inset-0 z-50 hidden modal-backdrop-blur flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full my-auto overflow-hidden border border-slate-200/90 flex flex-col max-h-[88vh]">
            <div class="bg-slate-900 px-4 py-2.5 text-white flex items-center justify-between flex-shrink-0">
                <div class="flex items-center space-x-2">
                    <i data-lucide="receipt" class="w-4 h-4 text-slate-300"></i>
                    <h3 id="receipt-modal-title" class="font-bold text-xs sm:text-sm">Struk Transaksi</h3>
                </div>
                <button onclick="closeReceiptModal()" class="text-slate-400 hover:text-white p-1 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </button>
            </div>

            <!-- Receipt Scroll Area -->
            <div class="p-3 overflow-y-auto flex-1 bg-slate-100 flex justify-center">
                <div id="receipt-modal-content" class="receipt-paper w-full max-w-[300px] p-2 text-xs text-slate-900">
                    <!-- Populated dynamically -->
                </div>
            </div>

            <!-- Receipt Actions Footer -->
            <div class="p-3 bg-white border-t border-slate-200 flex flex-col gap-1.5 flex-shrink-0">
                <div class="grid grid-cols-2 gap-1.5">
                    <button type="button" id="btn-modal-print-direct" onclick="printCurrentReceipt()" class="py-2 px-2.5 rounded-lg bg-slate-900 text-white font-bold text-xs hover:bg-black transition flex items-center justify-center space-x-1 shadow-xs active-press">
                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                        <span>Cetak Struk</span>
                    </button>
                    <button type="button" onclick="printReceiptSystemDialog()" class="py-2 px-2.5 rounded-lg bg-slate-100 text-slate-800 font-bold text-xs hover:bg-slate-200 border border-slate-200 transition flex items-center justify-center space-x-1 active-press">
                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                        <span>Dialog Cetak</span>
                    </button>
                </div>
                <button type="button" onclick="shareReceiptWhatsApp()" class="w-full py-2 px-3 rounded-lg bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition flex items-center justify-center space-x-1 shadow-xs active-press">
                    <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                    <span>Kirim via WhatsApp</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Global Camera Barcode Scanner Modal -->
    <div id="camera-scanner-modal" class="fixed inset-0 z-50 hidden bg-slate-950/95 backdrop-blur-md flex flex-col justify-between overflow-hidden">
        <!-- Scanner Top Bar -->
        <div class="p-3 sm:p-4 flex items-center justify-between text-white z-10 bg-gradient-to-b from-slate-950 via-slate-950/80 to-transparent">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center">
                    <i data-lucide="scan-line" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 id="camera-modal-title" class="font-bold text-sm leading-tight text-white">Scan Barcode Kamera</h3>
                    <p class="text-[11px] text-slate-400">Arahkan barcode ke kotak pemindai</p>
                </div>
            </div>
            <div class="flex items-center space-x-1.5">
                <!-- Refocus button -->
                <button id="btn-camera-refocus" onclick="window.cameraScanner.refocus()" class="p-2 rounded-xl bg-white/10 hover:bg-emerald-500/30 text-white transition active-press" title="Fokus Ulang Lensa Kamera">
                    <i data-lucide="crosshair" class="w-4 h-4"></i>
                </button>
                <!-- Torch / Flashlight button -->
                <button id="btn-camera-torch" onclick="window.cameraScanner.toggleTorch()" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white transition active-press" title="Nyalakan Lampu / Flash">
                    <i data-lucide="zap" class="w-4 h-4"></i>
                </button>
                <!-- Switch camera button -->
                <button id="btn-camera-switch" onclick="window.cameraScanner.switchCamera()" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white transition active-press" title="Ganti Kamera Depan / Belakang">
                    <i data-lucide="camera" class="w-4 h-4"></i>
                </button>
                <!-- Close button -->
                <button onclick="window.cameraScanner.close()" class="p-2 rounded-xl bg-white/10 hover:bg-red-600/80 text-white transition active-press ml-1" title="Tutup Kamera">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <!-- Scanner Viewfinder Box -->
        <div class="relative flex-1 flex flex-col items-center justify-center overflow-hidden px-4">
            <!-- html5-qrcode video viewport container with Tap-to-Focus -->
            <div id="camera-reader-viewport" onclick="window.cameraScanner.handleTapToFocus(event)" class="w-full max-w-sm rounded-2xl overflow-hidden shadow-2xl relative border-2 border-emerald-500/40 bg-black min-h-[220px] cursor-pointer" title="Ketuk untuk memfokuskan lensa">
                <!-- Video stream rendered dynamically -->
            </div>

            <!-- Scanning Reticle with Animated Laser Line -->
            <div id="camera-scan-overlay" onclick="window.cameraScanner.handleTapToFocus(event)" class="absolute cursor-pointer w-72 h-44 rounded-2xl shadow-[0_0_24px_rgba(16,185,129,0.25)] flex flex-col justify-between p-2">
                <!-- Top Corner Accents -->
                <div class="flex justify-between pointer-events-none">
                    <div class="w-6 h-6 border-t-4 border-l-4 border-emerald-400 rounded-tl-xl shadow-xs"></div>
                    <div class="w-6 h-6 border-t-4 border-r-4 border-emerald-400 rounded-tr-xl shadow-xs"></div>
                </div>
                <!-- Animated Sweeping Laser Line -->
                <div class="animate-laser h-0.5 bg-gradient-to-r from-transparent via-emerald-400 to-transparent shadow-[0_0_12px_#34d399] pointer-events-none"></div>
                <!-- Bottom Corner Accents -->
                <div class="flex justify-between pointer-events-none">
                    <div class="w-6 h-6 border-b-4 border-l-4 border-emerald-400 rounded-bl-xl shadow-xs"></div>
                    <div class="w-6 h-6 border-b-4 border-r-4 border-emerald-400 rounded-br-xl shadow-xs"></div>
                </div>
            </div>

            <!-- Focus and Zoom Quick Controls -->
            <div class="w-full max-w-sm flex items-center justify-between gap-2 mt-2.5 z-10">
                <div id="camera-zoom-bar" class="flex items-center gap-1 bg-slate-900/90 border border-slate-800 rounded-xl px-2 py-1">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mr-0.5">Zoom:</span>
                    <button type="button" onclick="window.cameraScanner.setZoom(1.0)" id="btn-zoom-1x" class="px-2 py-0.5 text-xs font-bold rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 transition active-press">1x</button>
                    <button type="button" onclick="window.cameraScanner.setZoom(1.5)" id="btn-zoom-15x" class="px-2 py-0.5 text-xs font-bold rounded-lg bg-emerald-500 text-slate-950 font-black transition active-press">1.5x</button>
                    <button type="button" onclick="window.cameraScanner.setZoom(2.0)" id="btn-zoom-2x" class="px-2 py-0.5 text-xs font-bold rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 transition active-press">2x</button>
                    <button type="button" onclick="window.cameraScanner.setZoom(2.5)" id="btn-zoom-25x" class="px-2 py-0.5 text-xs font-bold rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 transition active-press">2.5x</button>
                </div>
                <button type="button" onclick="window.cameraScanner.refocus()" class="px-2.5 py-1 bg-slate-900/90 hover:bg-slate-800 text-emerald-400 border border-slate-800 rounded-xl text-xs font-bold flex items-center gap-1 transition active-press">
                    <i data-lucide="crosshair" class="w-3.5 h-3.5"></i>
                    <span>Fokus</span>
                </button>
            </div>

            <!-- Focus Assistance Tip Guide -->
            <div class="text-center px-3 py-1 text-[11px] text-slate-400 bg-slate-900/70 rounded-xl border border-slate-800/80 flex items-center justify-center gap-1.5 max-w-sm mt-1.5 z-10">
                <span class="text-amber-400 text-xs">💡</span>
                <span>Jarak pas <strong>15–20 cm</strong> &bull; Ketuk layar jika buram</span>
            </div>

            <!-- Scan Success Feedback Banner -->
            <div id="camera-scan-feedback" class="hidden absolute top-4 left-4 right-4 max-w-sm mx-auto p-3 rounded-2xl bg-emerald-600 text-white shadow-2xl flex items-center justify-between text-xs font-bold transition-all duration-300">
                <div class="flex items-center space-x-2 truncate pr-2">
                    <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
                    <span id="camera-scan-feedback-text" class="truncate">Produk ditemukan!</span>
                </div>
                <span class="text-[10px] bg-emerald-800/90 px-2 py-0.5 rounded-full flex-shrink-0 font-extrabold">+1 Keranjang</span>
            </div>

            <!-- Camera Error Guide -->
            <div id="camera-error-box" class="hidden absolute p-5 rounded-2xl bg-slate-900/95 border border-red-500/50 text-white max-w-xs text-center space-y-2.5 shadow-2xl">
                <div class="w-10 h-10 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center mx-auto">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div class="font-bold text-sm text-red-300" id="camera-error-title">Kamera Tidak Aktif</div>
                <div class="text-xs text-slate-300 leading-relaxed" id="camera-error-desc">Pastikan izin kamera diaktifkan di browser Anda.</div>
                <button type="button" onclick="window.cameraScanner.retry()" class="mt-2 px-4 py-2 bg-emerald-600 rounded-xl text-xs font-bold hover:bg-emerald-500 text-white shadow-sm active-press">
                    Coba Lagi
                </button>
            </div>
        </div>

        <!-- Scanner Bottom Controls -->
        <div class="p-3 sm:p-4 bg-gradient-to-t from-slate-950 via-slate-950/90 to-transparent flex flex-col items-center space-y-2.5 z-10">
            <!-- Live Cart Status (if scanning from POS) -->
            <div id="camera-cart-status" class="hidden w-full max-w-sm py-2 px-3.5 rounded-xl bg-slate-900/90 border border-slate-800 text-white flex items-center justify-between text-xs">
                <div class="flex items-center space-x-2">
                    <i data-lucide="shopping-bag" class="w-4 h-4 text-emerald-400"></i>
                    <span id="camera-cart-items-count" class="font-semibold text-slate-300">0 Item</span>
                </div>
                <div id="camera-cart-total" class="font-bold text-emerald-400">Rp 0</div>
            </div>

            <!-- Manual Barcode Input Fallback -->
            <div class="w-full max-w-sm flex items-center space-x-2">
                <input type="text" id="camera-manual-barcode-input" placeholder="Ketik nomor barcode jika kamera sulit..." class="flex-1 px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                <button onclick="window.cameraScanner.submitManual()" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition active-press flex-shrink-0">
                    Input
                </button>
            </div>

            <!-- Mode Toggle & Finish Button -->
            <div class="w-full max-w-sm flex items-center justify-between gap-2 pt-0.5">
                <label class="flex items-center space-x-2 text-xs text-slate-300 cursor-pointer select-none">
                    <input type="checkbox" id="camera-continuous-mode" checked onchange="window.cameraScanner.toggleContinuous(this.checked)" class="rounded text-emerald-600 focus:ring-emerald-500">
                    <span class="text-[11px] text-slate-300">Mode Kasir Cepat (Scan Terus)</span>
                </label>
                <button onclick="window.cameraScanner.close()" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition active-press">
                    Selesai
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden iframe for clean system print without page reloading -->
    <iframe id="thermal-print-iframe" style="display:none;"></iframe>

    <!-- Audio Beep Generator (Pure Web Audio API, works offline without external files) -->
    <script>
        const AppAudio = {
            ctx: null,
            init() {
                if (!this.ctx && (window.AudioContext || window.webkitAudioContext)) {
                    this.ctx = new (window.AudioContext || window.webkitAudioContext)();
                }
            },
            beep(freq = 800, duration = 0.08, type = 'sine') {
                try {
                    this.init();
                    if (!this.ctx) return;
                    if (this.ctx.state === 'suspended') this.ctx.resume();
                    const osc = this.ctx.createOscillator();
                    const gain = this.ctx.createGain();
                    osc.type = type;
                    osc.frequency.value = freq;
                    gain.gain.setValueAtTime(0.15, this.ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + duration);
                    osc.connect(gain);
                    gain.connect(this.ctx.destination);
                    osc.start();
                    osc.stop(this.ctx.currentTime + duration);
                } catch(e) {}
            },
            success() {
                this.beep(880, 0.06);
                setTimeout(() => this.beep(1320, 0.1), 70);
            },
            error() {
                this.beep(300, 0.15, 'sawtooth');
            }
        };
        window.AppAudio = AppAudio;
    </script>

    <!-- ESC/POS & Thermal Printer Scripts -->
    <script src="<?= $baseUrl ?? '' ?>/public/js/qrcode.min.js"></script>
    <script src="<?= $baseUrl ?? '' ?>/public/js/escpos.js"></script>
    <script src="<?= $baseUrl ?? '' ?>/public/js/thermal-printer.js"></script>
    <!-- Barcode Camera Scanner (html5-qrcode) -->
    <script src="<?= $baseUrl ?? '' ?>/public/js/html5-qrcode.min.js"></script>
    <script src="<?= $baseUrl ?? '' ?>/public/js/camera-scanner.js"></script>

    <!-- Global App Initializer & Printer UI Controller -->
    <script>
        const baseUrl = '<?= $baseUrl ?? '' ?>';
        window.printerManager = new ThermalPrinterManager();
        window.currentReceiptPayload = null;

        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            updatePrinterUI();
            
            // Listen for status changes
            window.printerManager.onStatusChange(() => {
                updatePrinterUI();
            });
        });

        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        function openPrinterModal() {
            document.getElementById('printer-modal').classList.remove('hidden');
            updatePrinterUI();
        }

        function closePrinterModal() {
            document.getElementById('printer-modal').classList.add('hidden');
        }

        function updatePrinterUI() {
            const status = window.printerManager.getStatus();
            const dot = document.getElementById('printer-status-dot');
            const text = document.getElementById('printer-status-text');
            const modalName = document.getElementById('modal-printer-status-name');
            const modalBadge = document.getElementById('modal-status-badge');

            if (status.connectionType === 'bluetooth') {
                if (status.isConnected) {
                    dot.className = 'w-2 h-2 rounded-full bg-blue-500 pulse-connected';
                    text.innerText = status.printerName ? (status.printerName.length > 10 ? status.printerName.substring(0, 8) + '..' : status.printerName) : 'BT Ready';
                    modalName.innerText = `Bluetooth: ${status.printerName}`;
                    modalBadge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800';
                    modalBadge.innerText = 'Terhubung';
                } else {
                    dot.className = 'w-2 h-2 rounded-full bg-slate-400';
                    text.innerText = 'BT Putus';
                    modalName.innerText = 'Bluetooth Belum Terhubung';
                    modalBadge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600';
                    modalBadge.innerText = 'Terputus';
                }
            } else if (status.connectionType === 'usb') {
                if (status.isConnected) {
                    dot.className = 'w-2 h-2 rounded-full bg-emerald-500 pulse-connected';
                    text.innerText = 'USB Ready';
                    modalName.innerText = 'USB Serial Terhubung';
                    modalBadge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800';
                    modalBadge.innerText = 'Terhubung';
                } else {
                    dot.className = 'w-2 h-2 rounded-full bg-amber-500';
                    text.innerText = 'USB Port';
                    modalName.innerText = 'Port USB Belum Terbuka';
                    modalBadge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800';
                    modalBadge.innerText = 'Perlu Port';
                }
            } else {
                dot.className = 'w-2 h-2 rounded-full bg-emerald-500';
                text.innerText = 'PDF Dialog';
                modalName.innerText = 'Dialog Cetak Windows/Browser';
                modalBadge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800';
                modalBadge.innerText = 'Aktif';
            }

            // Paper button styles
            const btn58 = document.getElementById('btn-paper-58');
            const btn80 = document.getElementById('btn-paper-80');
            if (status.paperSize === '80mm') {
                btn80.className = 'py-2.5 px-3 rounded-xl border text-center font-bold text-xs transition border-emerald-500 bg-emerald-50 text-emerald-800';
                btn58.className = 'py-2.5 px-3 rounded-xl border text-center font-bold text-xs transition border-slate-200 text-slate-600 hover:bg-slate-50';
            } else {
                btn58.className = 'py-2.5 px-3 rounded-xl border text-center font-bold text-xs transition border-emerald-500 bg-emerald-50 text-emerald-800';
                btn80.className = 'py-2.5 px-3 rounded-xl border text-center font-bold text-xs transition border-slate-200 text-slate-600 hover:bg-slate-50';
            }
        }

        async function connectPrinterBluetooth() {
            try {
                const res = await window.printerManager.connectBluetooth();
                AppAudio.success();
                alert('Printer Bluetooth berhasil terhubung: ' + res.name);
                updatePrinterUI();
            } catch (err) {
                if (err.name !== 'NotFoundError') {
                    alert('Gagal menghubungkan Bluetooth: ' + err.message);
                }
            }
        }

        async function connectPrinterUsb() {
            try {
                await window.printerManager.connectUsbSerial();
                AppAudio.success();
                alert('Printer USB Serial berhasil terhubung!');
                updatePrinterUI();
            } catch (err) {
                if (err.name !== 'NotFoundError') {
                    alert('Gagal menghubungkan USB Serial: ' + err.message);
                }
            }
        }

        function selectSystemPrint() {
            window.printerManager.setConnectionType('system');
            AppAudio.success();
            updatePrinterUI();
            alert('Menggunakan mode dialog cetak sistem (kompatibel dengan semua printer & simpan ke PDF).');
        }

        function setPaperWidth(size) {
            window.printerManager.setPaperSize(size);
            updatePrinterUI();
        }

        async function testThermalPrint() {
            try {
                const storeName = document.getElementById('nav-store-name').innerText;
                await window.printerManager.testPrint(storeName);
                AppAudio.success();
            } catch (err) {
                alert('Gagal tes cetak: ' + err.message);
            }
        }

        async function disconnectPrinter() {
            await window.printerManager.disconnect();
            updatePrinterUI();
            alert('Koneksi printer diputuskan.');
        }

        // Receipt Modal Handlers (Unified for Sales & Kasbon)
        function showReceiptModal(receiptData) {
            window.currentReceiptPayload = receiptData;
            const container = document.getElementById('receipt-modal-content');
            const titleEl = document.getElementById('receipt-modal-title');
            
            const isKasbon = (receiptData.type === 'kasbon' || !!receiptData.kasbon);
            if (isKasbon) {
                if (titleEl) titleEl.innerText = 'Bukti Catatan Kasbon';
                container.innerHTML = window.printerManager.generateKasbonReceiptHtml(receiptData, { isPreview: true });
            } else {
                if (titleEl) titleEl.innerText = 'Struk Penjualan Resmi';
                container.innerHTML = window.printerManager.generateReceiptHtml(receiptData, { isPreview: true });
            }

            // Render vector QR codes inside container
            window.printerManager.renderQrCodesInContainer(container);

            document.getElementById('receipt-preview-modal').classList.remove('hidden');
            lucide.createIcons();
        }

        function closeReceiptModal() {
            document.getElementById('receipt-preview-modal').classList.add('hidden');
        }

        async function printCurrentReceipt() {
            if (!window.currentReceiptPayload) return;
            const isKasbon = (window.currentReceiptPayload.type === 'kasbon' || !!window.currentReceiptPayload.kasbon);
            
            try {
                if (isKasbon) {
                    await window.printerManager.printKasbonReceipt(window.currentReceiptPayload);
                } else {
                    await window.printerManager.printReceipt(window.currentReceiptPayload);
                }
                AppAudio.success();
            } catch (err) {
                console.warn('Direct printer failed, falling back to system dialog:', err);
                if (isKasbon) {
                    window.printerManager.printKasbonSystemDialog(window.currentReceiptPayload);
                } else {
                    window.printerManager.printSystemDialog(window.currentReceiptPayload);
                }
            }
        }

        function printReceiptSystemDialog() {
            if (!window.currentReceiptPayload) return;
            const isKasbon = (window.currentReceiptPayload.type === 'kasbon' || !!window.currentReceiptPayload.kasbon);
            if (isKasbon) {
                window.printerManager.printKasbonSystemDialog(window.currentReceiptPayload);
            } else {
                window.printerManager.printSystemDialog(window.currentReceiptPayload);
            }
        }

        function shareReceiptWhatsApp() {
            if (!window.currentReceiptPayload) return;
            const text = window.printerManager.generateWhatsAppReceiptText(window.currentReceiptPayload);
            
            let phone = '';
            if (window.currentReceiptPayload.transaction && window.currentReceiptPayload.transaction.customer_phone) {
                phone = window.currentReceiptPayload.transaction.customer_phone;
            } else if (window.currentReceiptPayload.kasbon && window.currentReceiptPayload.kasbon.customer_phone) {
                phone = window.currentReceiptPayload.kasbon.customer_phone;
            }

            const cleanPhone = String(phone).replace(/[^0-9]/g, '');
            const targetPhone = cleanPhone.startsWith('0') ? '62' + cleanPhone.slice(1) : cleanPhone;
            const waUrl = targetPhone ? `https://wa.me/${targetPhone}?text=${encodeURIComponent(text)}` : `https://wa.me/?text=${encodeURIComponent(text)}`;
            window.open(waUrl, '_blank');
        }
    </script>
</body>
</html>
