<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Toko - Tokomu</title>
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
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100/90 transition-all">
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 pt-8 pb-7 text-white text-center relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="w-14 h-14 bg-white/20 rounded-2xl mx-auto flex items-center justify-center mb-3 shadow-inner border border-white/25">
                <i data-lucide="store" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-xl font-black tracking-tight">Tokomu</h1>
            <p class="text-xs text-emerald-100 mt-1 font-medium">Sistem Kasir Pintar & Manajemen Toko</p>
        </div>

        <!-- Login Form -->
        <div class="p-6 sm:p-8 space-y-5">
            <div>
                <h2 class="text-base font-bold text-slate-800">Masuk ke Toko Anda</h2>
                <p class="text-xs text-slate-400 mt-0.5">Masukkan username dan password toko yang terdaftar</p>
            </div>

            <form id="login-form" onsubmit="handleLogin(event)" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Username Toko</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </span>
                        <input type="text" id="login-username" required placeholder="Contoh: tokomu" autocomplete="username" class="w-full pl-10 pr-3.5 py-3 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </span>
                        <input type="password" id="login-password" required placeholder="Masukkan password" autocomplete="current-password" class="w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition text-xs">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1" title="Lihat password">
                            <i data-lucide="eye" id="eye-icon" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- 1-Click Demo Fill for Store -->
                <div class="pt-1">
                    <div class="p-2.5 bg-emerald-50/70 border border-emerald-200/80 rounded-xl flex items-center justify-between text-[11px]">
                        <div class="text-emerald-800 font-medium">
                            <span class="font-bold">Demo Toko:</span> <span class="font-mono">tokomu</span> (Tokomu)
                        </div>
                        <button type="button" onclick="fillDemo('tokomu', 'tokomu123')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-[10px] transition active:scale-95 shadow-sm">
                            Isi Form
                        </button>
                    </div>
                </div>

                <button type="submit" id="btn-login" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold text-sm transition-all shadow-lg shadow-emerald-600/25 active:scale-[0.98] flex items-center justify-center space-x-2">
                    <span>Masuk ke Sistem</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                <span>Belum punya akun toko?</span>
                <a href="<?= $baseUrl ?? '' ?>/register" class="font-bold text-emerald-700 hover:text-emerald-800 ml-1 hover:underline">
                    Daftar Toko Baru
                </a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('login-password');
            const eyeIcon = document.getElementById('eye-icon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                pwdInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        function fillDemo(username, password) {
            document.getElementById('login-username').value = username;
            document.getElementById('login-password').value = password;
        }

        async function handleLogin(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-login');
            const username = document.getElementById('login-username').value.trim();
            const password = document.getElementById('login-password').value;

            btn.disabled = true;
            btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span> Memverifikasi...`;

            try {
                const res = await fetch('<?= $baseUrl ?? '' ?>/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });

                const data = await res.json();
                if (data.success) {
                    const targetUrl = data.redirect ? ('<?= $baseUrl ?? '' ?>' + data.redirect) : '<?= $baseUrl ?? '' ?>/';
                    Swal.fire({
                        icon: 'success',
                        title: data.is_super_admin ? 'Super Administrator!' : 'Selamat Datang!',
                        text: data.message || 'Membuka aplikasi...',
                        timer: 1200,
                        showConfirmButton: false
                    });
                    setTimeout(() => {
                        window.location.href = targetUrl;
                    }, 1000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Masuk',
                        text: data.message || 'Username atau password salah',
                        confirmButtonText: 'Coba Lagi',
                        customClass: { confirmButton: 'py-2 px-4 rounded-xl bg-emerald-600 text-white font-bold' }
                    });
                    btn.disabled = false;
                    btn.innerHTML = `<span>Masuk ke Sistem</span><i data-lucide="arrow-right" class="w-4 h-4"></i>`;
                    lucide.createIcons();
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Terjadi gangguan jaringan: ' + err.message
                });
                btn.disabled = false;
                btn.innerHTML = `<span>Masuk ke Sistem</span><i data-lucide="arrow-right" class="w-4 h-4"></i>`;
                lucide.createIcons();
            }
        }
    </script>
</body>
</html>
