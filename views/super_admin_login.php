<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Super Admin - Tokomu</title>
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
    </style>
</head>
<body class="bg-gradient-to-br from-slate-100 via-slate-50 to-emerald-50/60 min-h-screen flex items-center justify-center p-4 text-slate-800">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-200/90 transition-all">
        <!-- Header Banner (Clean Light Theme with Emerald Accent) -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 pt-8 pb-7 text-white text-center relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-emerald-500/10 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="w-14 h-14 bg-gradient-to-tr from-emerald-500 to-teal-400 rounded-2xl mx-auto flex items-center justify-center mb-3 shadow-lg shadow-emerald-500/25 border border-white/20">
                <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
            </div>
            
            <div class="inline-flex items-center gap-1.5 bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full mb-1 border border-emerald-500/30">
                <span>Portal Khusus</span>
            </div>
            <h1 class="text-xl font-black tracking-tight">Super Administrator</h1>
            <p class="text-xs text-slate-300 mt-1 font-medium">Kendali Sistem, Multi-Toko & Pembaruan Tokomu</p>
        </div>

        <!-- Separate Notice -->
        <div class="bg-amber-50/90 border-b border-amber-200/70 px-6 py-2.5 flex items-center gap-2.5 text-[11px] text-amber-800">
            <i data-lucide="info" class="w-4 h-4 text-amber-600 flex-shrink-0"></i>
            <div>
                Halaman ini terpisah khusus Super Admin. Penjual / Toko masuk lewat <a href="<?= $baseUrl ?? '' ?>/login" class="underline font-bold text-amber-900 hover:text-emerald-700">Login Toko</a>.
            </div>
        </div>

        <!-- Login Form -->
        <div class="p-6 sm:p-8 space-y-5">
            <div>
                <h2 class="text-base font-bold text-slate-900">Masuk Portal Admin</h2>
                <p class="text-xs text-slate-500 mt-0.5">Masukkan kredensial administrator sistem Anda</p>
            </div>

            <form id="admin-login-form" onsubmit="handleAdminLogin(event)" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Username Super Admin</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <i data-lucide="user-check" class="w-4 h-4"></i>
                        </span>
                        <input type="text" id="admin-username" required placeholder="Contoh: superadmin" value="superadmin" class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white text-xs transition">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-bold text-slate-700">Password</label>
                        <span class="text-[11px] text-slate-400">Default: <code class="bg-slate-100 text-slate-700 px-1 py-0.5 rounded font-mono">admin123</code></span>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" id="admin-password" required placeholder="••••••••" value="admin123" class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white text-xs transition">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition focus:outline-none" title="Lihat password">
                            <i data-lucide="eye" id="icon-eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="btn-submit" class="w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl transition shadow-lg shadow-emerald-600/25 flex items-center justify-center space-x-2 text-sm active:scale-[0.99]">
                    <i data-lucide="shield" class="w-4 h-4"></i>
                    <span>Masuk ke Portal Admin</span>
                </button>
            </form>

            <!-- Bottom Switch Link -->
            <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                <span>Ingin membuka kasir penjualan?</span>
                <a href="<?= $baseUrl ?? '' ?>/login" class="font-bold text-emerald-600 hover:text-emerald-700 ml-1 underline transition">
                    Buka Login Toko
                </a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function togglePasswordVisibility() {
            const input = document.getElementById('admin-password');
            const icon = document.getElementById('icon-eye');
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        async function handleAdminLogin(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit');
            const username = document.getElementById('admin-username').value.trim();
            const password = document.getElementById('admin-password').value;

            if (!username || !password) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Silakan isi username dan password Super Admin.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const originalHtml = btn.innerHTML;
            btn.innerHTML = `<span class="inline-block animate-spin mr-2">⟳</span> Memverifikasi...`;
            btn.disabled = true;

            try {
                const response = await fetch('<?= $baseUrl ?? '' ?>/api/gambut/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Akses Diterima!',
                        text: data.message || 'Membuka portal Super Admin...',
                        timer: 1000,
                        showConfirmButton: false
                    });
                    setTimeout(() => {
                        window.location.href = '<?= $baseUrl ?? '' ?>' + (data.redirect || '/gambut');
                    }, 800);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Masuk',
                        text: data.message || 'Username atau password salah.',
                        confirmButtonText: 'Coba Lagi'
                    });
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                    lucide.createIcons();
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Tidak dapat terhubung ke server. Pastikan server web aktif.',
                    confirmButtonText: 'Tutup'
                });
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                lucide.createIcons();
            }
        }
    </script>
</body>
</html>
