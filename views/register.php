<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Toko Baru - Tokomu</title>
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
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 pt-7 pb-6 text-white text-center relative overflow-hidden">
            <div class="w-12 h-12 bg-white/20 rounded-2xl mx-auto flex items-center justify-center mb-2 shadow-inner border border-white/25">
                <i data-lucide="store" class="w-6 h-6 text-white"></i>
            </div>
            <h1 class="text-lg font-black tracking-tight">Daftarkan Toko Baru</h1>
            <p class="text-xs text-emerald-100 mt-0.5 font-medium">Buka akun kasir terpisah untuk toko / warung Anda</p>
        </div>

        <!-- Registration Form -->
        <div class="p-6 sm:p-7 space-y-4">
            <form id="register-form" onsubmit="handleRegister(event)" class="space-y-3.5 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Toko / Warung <span class="text-red-500">*</span></label>
                    <input type="text" id="reg-store-name" required placeholder="Contoh: Toko Berkah / Warung Bu Ani" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white text-xs">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Username Login <span class="text-red-500">*</span></label>
                        <input type="text" id="reg-username" required placeholder="Contoh: tokoberkah" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white text-xs lowercase">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                        <input type="password" id="reg-password" required placeholder="Min 4 karakter" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Alamat Toko (Dicetak di Struk)</label>
                    <input type="text" id="reg-address" placeholder="Contoh: Jl. Mawar No. 12, Pasar Lama" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. WhatsApp Toko</label>
                    <input type="text" id="reg-phone" placeholder="Contoh: 08123456789" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white text-xs font-mono">
                </div>

                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl">
                    <label class="flex items-start space-x-2.5 cursor-pointer">
                        <input type="checkbox" id="reg-seed-products" checked class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <div class="text-[11px] leading-tight">
                            <span class="font-bold text-emerald-900 block">Muat Paket Sembako Siap Pakai</span>
                            <span class="text-emerald-700">Otomatis isi 30+ produk sembako populer lengkap dengan barcode & foto siap jual.</span>
                        </div>
                    </label>
                </div>

                <button type="submit" id="btn-register" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold text-sm transition-all shadow-lg shadow-emerald-600/25 active:scale-[0.98] flex items-center justify-center space-x-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Daftar Toko & Buka Kasir</span>
                </button>
            </form>

            <div class="pt-3 border-t border-slate-100 text-center text-xs text-slate-500">
                <span>Sudah punya akun toko?</span>
                <a href="<?= $baseUrl ?? '' ?>/login" class="font-bold text-emerald-700 hover:text-emerald-800 ml-1 hover:underline">
                    Masuk ke Toko
                </a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        async function handleRegister(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-register');
            const storeName = document.getElementById('reg-store-name').value.trim();
            const username = document.getElementById('reg-username').value.trim();
            const password = document.getElementById('reg-password').value;
            const address = document.getElementById('reg-address').value.trim();
            const phone = document.getElementById('reg-phone').value.trim();
            const seedProducts = document.getElementById('reg-seed-products').checked;

            btn.disabled = true;
            btn.innerHTML = `<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span> Mendaftarkan toko...`;

            try {
                const res = await fetch('<?= $baseUrl ?? '' ?>/api/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        store_name: storeName,
                        username: username,
                        password: password,
                        store_address: address,
                        store_phone: phone,
                        seed_products: seedProducts
                    })
                });

                const data = await res.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Toko Berhasil Didaftarkan!',
                        text: `Selamat datang di ${storeName}. Kasir siap digunakan!`,
                        timer: 1800,
                        showConfirmButton: false
                    });
                    setTimeout(() => {
                        window.location.href = '<?= $baseUrl ?? '' ?>/';
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Pendaftaran Gagal',
                        text: data.message || 'Terjadi kesalahan saat pendaftaran'
                    });
                    btn.disabled = false;
                    btn.innerHTML = `<i data-lucide="check-circle" class="w-4 h-4"></i><span>Daftar Toko & Buka Kasir</span>`;
                    lucide.createIcons();
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: err.message
                });
                btn.disabled = false;
                btn.innerHTML = `<i data-lucide="check-circle" class="w-4 h-4"></i><span>Daftar Toko & Buka Kasir</span>`;
                lucide.createIcons();
            }
        }
    </script>
</body>
</html>
