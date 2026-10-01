<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Portal - Tokomu</title>
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
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup { border-radius: 1.5rem !important; font-family: inherit !important; }
        .swal2-confirm { background-color: #1e3a8a !important; border-radius: 0.75rem !important; font-weight: 700 !important; }
        .swal2-cancel { background-color: #f1f5f9 !important; color: #475569 !important; border-radius: 0.75rem !important; font-weight: 700 !important; }
        .terminal-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
        .terminal-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-100/70 text-slate-800 min-h-screen flex flex-col font-sans antialiased">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebar-backdrop" onclick="toggleSidebar(false)" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-30 hidden lg:hidden transition-opacity"></div>

    <!-- ========================================================= -->
    <!-- LEFT SIDEBAR -->
    <!-- ========================================================= -->
    <aside id="app-sidebar" class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-200/90 z-40 flex flex-col justify-between shadow-sm transition-transform duration-200 -translate-x-full lg:translate-x-0">
        <!-- Top Portion of Sidebar -->
        <div class="flex flex-col h-full overflow-y-auto">
            <!-- Brand Logo Header -->
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <a href="<?= $baseUrl ?>/gambut" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 group-hover:scale-105 transition">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-base tracking-tight text-slate-900">Tokomu</span>
                            <span class="bg-emerald-100 text-emerald-800 font-black text-[9px] px-1.5 py-0.5 rounded-full uppercase tracking-wider">Super Admin</span>
                        </div>
                        <div class="text-[10px] text-slate-400 font-medium">Control Center</div>
                    </div>
                </a>
                <!-- Close Button on Mobile -->
                <button onclick="toggleSidebar(false)" class="lg:hidden text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Admin Profile Card -->
            <div class="p-4 mx-3 my-3 bg-slate-50 border border-slate-200/80 rounded-2xl flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-slate-800 to-slate-700 text-white flex items-center justify-center font-black text-xs shadow-inner">
                    SA
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-bold text-xs text-slate-900 truncate"><?= htmlspecialchars($admin['name'] ?? 'Super Administrator') ?></div>
                    <div class="text-[10px] text-slate-500 font-mono truncate">@<?= htmlspecialchars($admin['username'] ?? 'superadmin') ?></div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[9px] font-semibold text-emerald-700">Online & Aktif</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="px-3 py-2 space-y-1 text-xs">
                <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    Menu Utama
                </div>

                <a href="#section-overview" onclick="navClick(this)" class="nav-link flex items-center justify-between px-3.5 py-2.5 rounded-xl font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition active-press group">
                    <span class="flex items-center space-x-2.5">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-slate-400 group-hover:text-emerald-600"></i>
                        <span>Ringkasan Platform</span>
                    </span>
                </a>

                <a href="#section-stores" onclick="navClick(this)" class="nav-link flex items-center justify-between px-3.5 py-2.5 rounded-xl font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition active-press group">
                    <span class="flex items-center space-x-2.5">
                        <i data-lucide="store" class="w-4 h-4 text-slate-400 group-hover:text-emerald-600"></i>
                        <span>Kelola Toko</span>
                    </span>
                    <span class="bg-slate-200/80 text-slate-700 text-[10px] font-bold px-2 py-0.5 rounded-full group-hover:bg-emerald-200 group-hover:text-emerald-900">
                        <?= count($stores) ?>
                    </span>
                </a>

                <a href="#section-git" onclick="navClick(this)" class="nav-link flex items-center justify-between px-3.5 py-2.5 rounded-xl font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition active-press group">
                    <span class="flex items-center space-x-2.5">
                        <i data-lucide="git-pull-request" class="w-4 h-4 text-slate-400 group-hover:text-emerald-600"></i>
                        <span>Update Git (GitHub)</span>
                    </span>
                    <span class="bg-emerald-100 text-emerald-800 text-[9px] font-mono font-bold px-1.5 py-0.5 rounded-full">
                        <?= htmlspecialchars(explode(' ', $git['branch'])[0]) ?>
                    </span>
                </a>

                <a href="#section-server" onclick="navClick(this)" class="nav-link flex items-center justify-between px-3.5 py-2.5 rounded-xl font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition active-press group">
                    <span class="flex items-center space-x-2.5">
                        <i data-lucide="server" class="w-4 h-4 text-slate-400 group-hover:text-emerald-600"></i>
                        <span>Info Server & DB</span>
                    </span>
                </a>

                <div class="pt-4 px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    Pintas Toko
                </div>

                <a href="<?= $baseUrl ?>/" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl font-bold text-emerald-700 hover:bg-emerald-50 transition active-press">
                    <i data-lucide="shopping-bag" class="w-4 h-4 text-emerald-600"></i>
                    <span>Buka Kasir Tokomu</span>
                </a>

                <a href="<?= $baseUrl ?>/register" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition active-press">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-slate-400"></i>
                    <span>Daftarkan Toko Baru</span>
                </a>
            </div>
        </div>

        <!-- Bottom Portion / Logout -->
        <div class="p-3 border-t border-slate-100 bg-slate-50/60">
            <a href="<?= $baseUrl ?>/gambut/logout" onclick="confirmAdminLogout(event)" class="w-full flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl font-bold text-rose-600 hover:bg-rose-50 transition active-press text-xs">
                <i data-lucide="log-out" class="w-4 h-4 text-rose-500"></i>
                <span>Keluar Super Admin</span>
            </a>
        </div>
    </aside>

    <!-- ========================================================= -->
    <!-- MAIN CONTENT AREA (Offset by sidebar width on desktop) -->
    <!-- ========================================================= -->
    <div class="lg:pl-64 flex flex-col flex-1 min-h-screen">
        
        <!-- Top Sticky Header -->
        <header class="bg-white/90 backdrop-blur border-b border-slate-200/90 sticky top-0 z-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <!-- Mobile Hamburger Toggle -->
                    <button onclick="toggleSidebar(true)" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-xl hover:bg-slate-100 focus:outline-none">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <div>
                        <div class="font-extrabold text-base text-slate-900 tracking-tight flex items-center gap-2">
                            <span>Portal Super Admin</span>
                            <span class="hidden sm:inline-block bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Tokomu v2.0</span>
                        </div>
                        <div class="text-[11px] text-slate-500 hidden sm:block">Kelola seluruh toko, akun, dan update sistem dari satu tempat</div>
                    </div>
                </div>

                <!-- Header Actions -->
                <div class="flex items-center space-x-2">
                    <button onclick="refreshGitStatus()" id="btn-refresh-git-top" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center space-x-1.5" title="Periksa status branch & commit">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Cek Git</span>
                    </button>
                    
                    <button onclick="triggerGitPull()" id="btn-git-pull-top" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center space-x-1.5 active:scale-95" title="Tarik pembaruan dari GitHub">
                        <i data-lucide="download-cloud" class="w-3.5 h-3.5"></i>
                        <span>Tarik Update</span>
                    </button>

                    <div class="h-6 w-px bg-slate-200 mx-1 hidden sm:block"></div>

                    <a href="<?= $baseUrl ?>/gambut/logout" onclick="confirmAdminLogout(event)" class="text-slate-400 hover:text-rose-600 p-2 rounded-xl hover:bg-rose-50 transition" title="Keluar Super Admin">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 py-6 space-y-6">

            <!-- ========================================================= -->
            <!-- 1. STATISTIK RINGKASAN PLATFORM -->
            <!-- ========================================================= -->
            <section id="section-overview" class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-500 flex items-center gap-2">
                        <i data-lucide="activity" class="w-4 h-4 text-emerald-600"></i>
                        <span>Ringkasan Keseluruhan Platform</span>
                    </h2>
                    <span class="text-xs text-slate-400">Update Real-time</span>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5">
                    <!-- Total Toko -->
                    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-sm hover:shadow transition">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-500">Total Toko</span>
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i data-lucide="store" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-slate-900"><?= number_format($stats['total_stores']) ?></div>
                        <div class="text-[11px] text-emerald-700 font-semibold mt-1 flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-3 h-3"></i>
                            <span>Semua Toko Aktif</span>
                        </div>
                    </div>

                    <!-- Total Produk -->
                    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-sm hover:shadow transition">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-500">Total Produk</span>
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i data-lucide="package" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-slate-900"><?= number_format($stats['total_products']) ?></div>
                        <div class="text-[11px] text-slate-400 mt-1">Di semua katalog toko</div>
                    </div>

                    <!-- Total Transaksi -->
                    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-sm hover:shadow transition">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-500">Total Transaksi</span>
                            <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                <i data-lucide="receipt" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="text-2xl font-black text-slate-900"><?= number_format($stats['total_transactions']) ?></div>
                        <div class="text-[11px] text-slate-400 mt-1">Struk berhasil</div>
                    </div>

                    <!-- Total Omzet -->
                    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-sm hover:shadow transition">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-500">Total Omzet</span>
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i data-lucide="wallet" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="text-xl sm:text-2xl font-black text-emerald-600">Rp <?= number_format($stats['total_revenue'], 0, ',', '.') ?></div>
                        <div class="text-[11px] text-slate-400 mt-1">Perputaran platform</div>
                    </div>

                    <!-- Total Kasbon Belum Lunas -->
                    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-sm hover:shadow transition col-span-2 lg:col-span-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-500">Kasbon Tertunggak</span>
                            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                                <i data-lucide="book-open" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="text-xl sm:text-2xl font-black text-rose-600">Rp <?= number_format($stats['total_kasbon'], 0, ',', '.') ?></div>
                        <div class="text-[11px] text-slate-400 mt-1">Total sisa utang</div>
                    </div>
                </div>
            </section>

            <!-- ========================================================= -->
            <!-- 2. DAFTAR SEMUA TOKO & KREDENSIAL (USERNAME & PASSWORD) -->
            <!-- ========================================================= -->
            <section id="section-stores" class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-7 shadow-sm space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-100">
                                <i data-lucide="users" class="w-4 h-4"></i>
                            </div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-900">Daftar Toko & Kredensial Akun</h2>
                            <span class="bg-slate-100 text-slate-700 text-[10px] font-bold px-2 py-0.5 rounded-full"><?= count($stores) ?> Toko Terdaftar</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Lihat siapa saja yang sudah mendaftar toko, username, dan password mereka. Anda dapat melihat, mengubah password, atau membuka kasir toko secara langsung.</p>
                    </div>

                    <!-- Search Input & Add Store Button -->
                    <div class="flex items-center gap-2">
                        <div class="relative w-full sm:w-64">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <i data-lucide="search" class="w-3.5 h-3.5"></i>
                            </span>
                            <input type="text" id="store-search-input" onkeyup="filterStoreTable()" placeholder="Cari toko atau username..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                        </div>
                        <a href="<?= $baseUrl ?>/register" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold flex items-center space-x-1.5 transition flex-shrink-0" title="Buka form pendaftaran toko baru">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span class="hidden sm:inline">Tambah Toko</span>
                        </a>
                    </div>
                </div>

                <!-- Stores Table -->
                <div class="overflow-x-auto rounded-2xl border border-slate-200/80">
                    <table class="w-full text-left text-xs" id="stores-table">
                        <thead class="bg-slate-50/80 text-slate-600 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">ID</th>
                                <th class="py-3 px-4">Nama Toko</th>
                                <th class="py-3 px-4">Username Login</th>
                                <th class="py-3 px-4">Password</th>
                                <th class="py-3 px-4 text-center">Produk</th>
                                <th class="py-3 px-4 text-right">Omzet</th>
                                <th class="py-3 px-4 text-right">Kasbon</th>
                                <th class="py-3 px-4 text-center">Aksi Super Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($stores)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-slate-400">Belum ada toko yang terdaftar di database.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($stores as $s): ?>
                                    <tr class="store-row hover:bg-slate-50/80 transition" data-search="<?= strtolower(htmlspecialchars($s['store_name'] . ' ' . $s['username'] . ' ' . ($s['store_address'] ?? ''))) ?>">
                                        <td class="py-3.5 px-4 font-mono font-bold text-slate-400">#<?= $s['id'] ?></td>
                                        
                                        <!-- Store Name & Details -->
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                                <span><?= htmlspecialchars($s['store_name']) ?></span>
                                                <?php if ($s['id'] == 1): ?>
                                                    <span class="bg-emerald-100 text-emerald-800 text-[9px] font-bold px-1.5 py-0.5 rounded-full">Default</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-[11px] text-slate-400 truncate max-w-xs mt-0.5">
                                                <?= htmlspecialchars($s['store_address'] ?: 'Alamat belum diisi') ?>
                                                <?php if ($s['store_phone']): ?>
                                                    &bull; <span class="font-mono text-slate-500"><?= htmlspecialchars($s['store_phone']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- Username -->
                                        <td class="py-3.5 px-4 font-mono">
                                            <div class="flex items-center space-x-1.5">
                                                <span class="font-bold text-slate-800 bg-slate-100 px-2 py-1 rounded-lg border border-slate-200"><?= htmlspecialchars($s['username']) ?></span>
                                                <button onclick="copyToClipboard('<?= htmlspecialchars($s['username']) ?>', 'Username tersalin!')" class="text-slate-400 hover:text-slate-700 p-1" title="Salin username">
                                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Password with Eye Toggle -->
                                        <td class="py-3.5 px-4 font-mono">
                                            <div class="flex items-center space-x-1.5">
                                                <div class="relative">
                                                    <input type="password" readonly id="pw-field-<?= $s['id'] ?>" value="<?= htmlspecialchars($s['plain_password']) ?>" class="bg-slate-50 border border-slate-200 text-slate-700 font-bold px-2 py-1 rounded-lg text-xs w-28 pr-7 focus:outline-none">
                                                    <button type="button" onclick="toggleStorePassword(<?= $s['id'] ?>)" class="absolute right-1.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5" title="Lihat password">
                                                        <i data-lucide="eye" id="pw-icon-<?= $s['id'] ?>" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </div>
                                                <button onclick="copyToClipboard('<?= htmlspecialchars($s['plain_password']) ?>', 'Password tersalin!')" class="text-slate-400 hover:text-slate-700 p-1" title="Salin password">
                                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Product Count -->
                                        <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                            <span class="inline-block bg-slate-100 px-2.5 py-0.5 rounded-full text-xs">
                                                <?= number_format($s['total_products']) ?>
                                            </span>
                                        </td>

                                        <!-- Total Revenue -->
                                        <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-700">
                                            Rp <?= number_format($s['total_revenue'], 0, ',', '.') ?>
                                            <div class="text-[10px] text-slate-400 font-sans font-normal"><?= number_format($s['total_transactions']) ?> struk</div>
                                        </td>

                                        <!-- Kasbon -->
                                        <td class="py-3.5 px-4 text-right font-mono font-bold <?= $s['total_kasbon'] > 0 ? 'text-rose-600' : 'text-slate-400' ?>">
                                            Rp <?= number_format($s['total_kasbon'], 0, ',', '.') ?>
                                        </td>

                                        <!-- Super Admin Actions -->
                                        <td class="py-3.5 px-4 text-center">
                                            <div class="flex items-center justify-center space-x-1.5">
                                                <!-- Impersonate Store -->
                                                <button onclick="impersonateStore(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['store_name'])) ?>')" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-[11px] transition shadow-sm flex items-center space-x-1" title="Buka dan operasikan kasir toko ini">
                                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                                    <span>Buka Kasir</span>
                                                </button>

                                                <!-- Ubah Password -->
                                                <button onclick="openChangePasswordModal(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['store_name'])) ?>')" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" title="Ubah Password Toko">
                                                    <i data-lucide="key-round" class="w-3.5 h-3.5"></i>
                                                </button>

                                                <!-- Delete Store (disabled for store 1) -->
                                                <?php if ($s['id'] != 1): ?>
                                                    <button onclick="deleteStore(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['store_name'])) ?>')" class="p-1.5 bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-lg transition" title="Hapus Toko">
                                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ========================================================= -->
            <!-- 3. PEMBARUAN SISTEM DARI GITHUB (GIT PULL) -->
            <!-- ========================================================= -->
            <section id="section-git" class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-7 shadow-sm space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                                <i data-lucide="git-pull-request" class="w-4 h-4"></i>
                            </div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-900">Pembaruan Sistem (GitHub Sync)</h2>
                            <?php if ($git['has_cli']): ?>
                                <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-200/60">Git CLI Terdeteksi</span>
                            <?php else: ?>
                                <span class="bg-rose-50 text-rose-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-rose-200">Git CLI Tidak Ditemukan</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Tarik fitur baru atau perbaikan bug langsung dari repository GitHub project tanpa perlu upload file manual.</p>
                    </div>

                    <!-- Git Action Buttons -->
                    <div class="flex items-center gap-2">
                        <button onclick="refreshGitStatus()" id="btn-refresh-git" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center space-x-1.5 border border-slate-200">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                            <span>Cek Status</span>
                        </button>
                        <button onclick="triggerGitPull()" id="btn-git-pull" class="px-5 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-emerald-600/20 flex items-center space-x-2 active:scale-95">
                            <i data-lucide="download-cloud" class="w-4 h-4"></i>
                            <span>Tarik Update (Git Pull)</span>
                        </button>
                    </div>
                </div>

                <!-- Git Metadata Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div class="bg-slate-50 border border-slate-200/80 p-3.5 rounded-xl">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Branch Aktif</span>
                        <span id="git-branch" class="font-mono font-bold text-emerald-700 text-sm mt-0.5 block truncate"><?= htmlspecialchars($git['branch']) ?></span>
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 p-3.5 rounded-xl">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Remote Repository</span>
                        <span id="git-remote" class="font-mono text-slate-700 text-xs mt-0.5 block truncate" title="<?= htmlspecialchars($git['remote_url'] ?: 'Belum dihubungkan') ?>">
                            <?= htmlspecialchars($git['remote_url'] ?: 'Belum dihubungkan') ?>
                        </span>
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 p-3.5 rounded-xl">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Commit Terakhir</span>
                        <span id="git-commit" class="font-mono text-slate-700 text-xs mt-0.5 block truncate" title="<?= htmlspecialchars($git['latest_commit']) ?>">
                            <?= htmlspecialchars($git['latest_commit']) ?>
                        </span>
                    </div>
                </div>

                <!-- GitHub Actions Auto-Deploy Banner -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/80 p-4 sm:p-5 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 text-blue-900 text-xs font-bold">
                            <i data-lucide="zap" class="w-4 h-4 text-blue-600"></i>
                            <span>Fitur Auto-Deploy GitHub Actions (InfinityFree)</span>
                        </div>
                        <p class="text-xs text-blue-700 leading-relaxed">
                            Sudah disiapkan workflow <strong>GitHub Actions</strong>. Setiap kali Anda <code>git push origin main</code> dari laptop, file baru otomatis disinkronkan ke server InfinityFree via FTP tanpa Anda perlu klik tombol apa pun!
                        </p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 shrink-0">
                        ⚡ Siap Pakai
                    </span>
                </div>

                <!-- Git Setup Box Form (Visible if no remote URL yet) -->
                <div id="git-setup-box" class="<?= $git['is_git'] && !empty($git['remote_url']) ? 'hidden' : '' ?> bg-amber-50/70 border border-amber-200/80 p-4 sm:p-5 rounded-2xl space-y-3">
                    <div class="flex items-center gap-2 text-amber-800 text-xs font-bold">
                        <i data-lucide="link" class="w-4 h-4 text-amber-600"></i>
                        <span>Hubungkan Aplikasi ke GitHub Repository</span>
                    </div>
                    <p class="text-xs text-amber-700">
                        Masukkan URL remote repository GitHub project Anda agar tombol <strong>Git Pull</strong> dapat bekerja otomatis:
                    </p>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" id="git-setup-url" placeholder="Contoh: https://github.com/username/tokomu.git" class="flex-1 bg-white border border-slate-200 px-3.5 py-2.5 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                        <input type="text" id="git-setup-branch" value="main" placeholder="Branch (main)" class="w-28 bg-white border border-slate-200 px-3 py-2.5 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono text-center">
                        <button onclick="submitGitSetup()" id="btn-submit-git-setup" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl transition flex items-center justify-center space-x-1.5 shadow-sm">
                            <i data-lucide="save" class="w-3.5 h-3.5"></i>
                            <span>Hubungkan Repo</span>
                        </button>
                    </div>
                </div>

                <!-- Modern Developer Terminal Box -->
                <div class="rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="bg-slate-100 border-b border-slate-200 px-4 py-2 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded-full bg-rose-400 inline-block"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span>
                            <span class="text-[11px] font-mono font-bold text-slate-600 ml-2">Console Live Git</span>
                        </div>
                        <button onclick="clearConsole()" class="text-slate-500 hover:text-slate-800 text-[10px] font-medium transition">Bersihkan Log</button>
                    </div>
                    <div class="bg-slate-950 p-4 font-mono text-xs text-emerald-400 leading-relaxed overflow-x-auto max-h-48 terminal-scroll" id="git-console-output">
                        <span class="text-slate-500">[System]</span> Portal Super Admin siap. Tekan "Tarik Update (Git Pull)" untuk memperbarui file aplikasi dari GitHub.
                    </div>
                </div>
            </section>

            <!-- ========================================================= -->
            <!-- 4. INFO SERVER & DATABASE -->
            <!-- ========================================================= -->
            <section id="section-server" class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-7 shadow-sm space-y-4">
                <div class="flex items-center gap-2.5 border-b border-slate-100 pb-4">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center border border-purple-100">
                        <i data-lucide="server" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Informasi Server & Lingkungan Sistem</h2>
                        <p class="text-xs text-slate-500">Spesifikasi runtime PHP, database SQLite, dan sistem operasi</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                        <span class="text-slate-400 text-[10px] block font-bold uppercase tracking-wider">PHP Version</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block font-mono"><?= PHP_VERSION ?></span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                        <span class="text-slate-400 text-[10px] block font-bold uppercase tracking-wider">Database Driver</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block font-mono">SQLite 3 (PDO)</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                        <span class="text-slate-400 text-[10px] block font-bold uppercase tracking-wider">Sistem Operasi</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block truncate"><?= PHP_OS_FAMILY ?> (<?= PHP_OS ?>)</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                        <span class="text-slate-400 text-[10px] block font-bold uppercase tracking-wider">Web Server</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block truncate font-mono"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'PHP CLI Server' ?></span>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 font-mono bg-slate-50 p-2.5 rounded-xl border border-slate-200/80 truncate">
                    <span class="font-bold text-slate-500">Database Path:</span> <?= realpath(__DIR__ . '/../data/warung.db') ?: 'C:\laragon\www\warung-pos\data\warung.db' ?>
                </div>
            </section>

        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-200 bg-white text-center py-4 text-slate-400 text-xs">
            Tokomu Super Administrator &copy; <?= date('Y') ?> &bull; Sistem Kasir Pintar & Multi-Tenant &bull; Built with PHP & SQLite
        </footer>
    </div>

    <!-- ========================================================= -->
    <!-- JAVASCRIPT ACTIONS -->
    <!-- ========================================================= -->
    <script>
        lucide.createIcons();

        // Mobile Sidebar Controls
        function toggleSidebar(show) {
            const sidebar = document.getElementById('app-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (show) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        // Close sidebar on mobile when nav link clicked
        function navClick(link) {
            if (window.innerWidth < 1024) {
                toggleSidebar(false);
            }
        }

        // Console Logging
        function logConsole(message, type = 'info') {
            const con = document.getElementById('git-console-output');
            const time = new Date().toLocaleTimeString();
            let prefix = `<span class="text-slate-500">[${time}]</span>`;
            if (type === 'error') {
                prefix += ` <span class="text-rose-400">[ERROR]</span>`;
            } else if (type === 'success') {
                prefix += ` <span class="text-emerald-400">[SUCCESS]</span>`;
            } else {
                prefix += ` <span class="text-blue-400">[INFO]</span>`;
            }
            con.innerHTML += `\n${prefix} ${message}`;
            con.scrollTop = con.scrollHeight;
        }

        function clearConsole() {
            document.getElementById('git-console-output').innerHTML = '<span class="text-slate-500">[System]</span> Log dibersihkan.';
        }

        // Toggle Password Visibility in Table
        function toggleStorePassword(storeId) {
            const input = document.getElementById('pw-field-' + storeId);
            const icon = document.getElementById('pw-icon-' + storeId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        function copyToClipboard(text, message = 'Tersalin!') {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: message,
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }

        // Filter Store Table
        function filterStoreTable() {
            const query = document.getElementById('store-search-input').value.toLowerCase().trim();
            const rows = document.querySelectorAll('.store-row');
            rows.forEach(row => {
                const text = row.getAttribute('data-search') || '';
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // 1. GIT PULL ACTION
        async function triggerGitPull() {
            const btn = document.getElementById('btn-git-pull');
            const btnTop = document.getElementById('btn-git-pull-top');
            
            if (btn) btn.disabled = true;
            if (btnTop) btnTop.disabled = true;

            const loadingHtml = `<span class="inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin mr-1.5"></span> Menarik...`;
            if (btn) btn.innerHTML = loadingHtml;
            if (btnTop) btnTop.innerHTML = loadingHtml;

            logConsole('Memulai proses git pull dari repository GitHub...');

            try {
                const res = await fetch('<?= $baseUrl ?>/api/gambut/git-pull', {
                    method: 'POST'
                });
                const data = await res.json();

                if (data.success) {
                    logConsole(`Hasil git pull:\n${data.output}`, 'success');
                    if (data.latest_commit) {
                        document.getElementById('git-commit').innerText = data.latest_commit;
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Update Berhasil!',
                        text: data.message || 'File aplikasi berhasil diperbarui dari GitHub.',
                        confirmButtonText: 'Mantap'
                    });
                } else {
                    logConsole(`Gagal git pull:\n${data.output || data.message}`, 'error');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Git Pull Perhatian',
                        html: `<div class="text-left text-xs font-mono bg-slate-900 text-amber-300 p-3 rounded-xl overflow-x-auto">${data.output || data.message}</div>`,
                        confirmButtonText: 'Tutup'
                    });
                }
            } catch (err) {
                logConsole(`Error jaringan saat git pull: ${err.message}`, 'error');
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: err.message
                });
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = `<i data-lucide="download-cloud" class="w-4 h-4"></i><span>Tarik Update (Git Pull)</span>`;
                }
                if (btnTop) {
                    btnTop.disabled = false;
                    btnTop.innerHTML = `<i data-lucide="download-cloud" class="w-3.5 h-3.5"></i><span>Tarik Update</span>`;
                }
                lucide.createIcons();
            }
        }

        // 2. REFRESH GIT STATUS
        async function refreshGitStatus() {
            const btn = document.getElementById('btn-refresh-git');
            const btnTop = document.getElementById('btn-refresh-git-top');
            if (btn) btn.disabled = true;
            if (btnTop) btnTop.disabled = true;
            logConsole('Memeriksa status git...');

            try {
                const res = await fetch('<?= $baseUrl ?>/api/gambut/git-status');
                const data = await res.json();
                if (data.success && data.git) {
                    const g = data.git;
                    document.getElementById('git-branch').innerText = g.branch || '-';
                    document.getElementById('git-remote').innerText = g.remote_url || 'Belum dihubungkan';
                    document.getElementById('git-commit').innerText = g.latest_commit || '-';
                    logConsole(`Git Status: Branch [${g.branch}], Remote [${g.remote_url || 'None'}]`);
                    if (g.status) {
                        logConsole(`Perubahan lokal (git status -s):\n${g.status}`);
                    } else {
                        logConsole('Working directory bersih (clean).');
                    }
                }
            } catch (err) {
                logConsole('Gagal mengambil status git: ' + err.message, 'error');
            } finally {
                if (btn) btn.disabled = false;
                if (btnTop) btnTop.disabled = false;
            }
        }

        // 3. GIT SETUP SUBMIT
        async function submitGitSetup() {
            const repoUrl = document.getElementById('git-setup-url').value.trim();
            const branch = document.getElementById('git-setup-branch').value.trim() || 'main';
            if (!repoUrl) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Masukkan URL Repository GitHub terlebih dahulu' });
                return;
            }

            const btn = document.getElementById('btn-submit-git-setup');
            btn.disabled = true;
            logConsole(`Menghubungkan repository ke ${repoUrl} [branch ${branch}]...`);

            try {
                const res = await fetch('<?= $baseUrl ?>/api/gambut/git-setup', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ repo_url: repoUrl, branch: branch })
                });
                const data = await res.json();
                if (data.success) {
                    logConsole(`Git setup berhasil:\n${data.output}`, 'success');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Dihubungkan!',
                        text: data.message,
                        confirmButtonText: 'OK'
                    });
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    logConsole(`Git setup error: ${data.message}`, 'error');
                    Swal.fire({ icon: 'error', title: 'Gagal Setup Git', text: data.message });
                }
            } catch (err) {
                logConsole('Error: ' + err.message, 'error');
            } finally {
                btn.disabled = false;
            }
        }

        // 4. IMPERSONATE STORE (Masuk ke Toko)
        async function impersonateStore(storeId, storeName) {
            const confirmed = await Swal.fire({
                title: 'Buka Kasir Toko?',
                text: `Anda akan langsung masuk ke sesi kasir toko '${storeName}'.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Buka Kasir',
                cancelButtonText: 'Batal'
            });

            if (!confirmed.isConfirmed) return;

            try {
                const res = await fetch('<?= $baseUrl ?>/api/gambut/store/impersonate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ store_id: storeId })
                });
                const data = await res.json();
                if (data.success) {
                    window.location.href = '<?= $baseUrl ?>' + (data.redirect || '/');
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: err.message });
            }
        }

        // 5. CHANGE STORE PASSWORD MODAL
        async function openChangePasswordModal(storeId, storeName) {
            const { value: newPassword } = await Swal.fire({
                title: `Ubah Password Toko`,
                text: `Atur password baru untuk ${storeName}:`,
                input: 'text',
                inputPlaceholder: 'Ketik password baru (minimal 4 karakter)',
                showCancelButton: true,
                confirmButtonText: 'Simpan Password',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value || value.trim().length < 4) {
                        return 'Password baru minimal 4 karakter!';
                    }
                }
            });

            if (newPassword) {
                try {
                    const res = await fetch('<?= $baseUrl ?>/api/gambut/store/update-password', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ store_id: storeId, new_password: newPassword.trim() })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message });
                        const pwField = document.getElementById('pw-field-' + storeId);
                        if (pwField) pwField.value = newPassword.trim();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                    }
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Kesalahan Sistem', text: err.message });
                }
            }
        }

        // 6. DELETE STORE
        async function deleteStore(storeId, storeName) {
            const confirmed = await Swal.fire({
                title: 'Hapus Toko Permanen?',
                text: `Seluruh data produk, transaksi, dan buku kasbon milik '${storeName}' akan dihapus secara permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                confirmButtonText: 'Ya, Hapus Toko',
                cancelButtonText: 'Batal'
            });

            if (!confirmed.isConfirmed) return;

            try {
                const res = await fetch(`<?= $baseUrl ?>/api/gambut/store/${storeId}`, {
                    method: 'DELETE'
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Terhapus', text: data.message });
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Kesalahan Sistem', text: err.message });
            }
        }

        // 7. CONFIRM ADMIN LOGOUT
        async function confirmAdminLogout(e) {
            e.preventDefault();
            const res = await Swal.fire({
                title: 'Keluar Super Admin?',
                text: 'Sesi super administrator Anda akan diakhiri.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal'
            });
            if (res.isConfirmed) {
                window.location.href = '<?= $baseUrl ?>/gambut/logout';
            }
        }
    </script>
</body>
</html>
