<div class="max-w-4xl w-full mx-auto p-3 sm:p-6 space-y-5">
    <!-- Active Store Profile & Avatar Card (Modern Navy Gradient) -->
    <div class="bg-gradient-to-br from-blue-950 via-blue-900 to-blue-800 text-white p-5 sm:p-6 rounded-3xl shadow-xl border border-blue-500/20 relative overflow-hidden">
        <!-- Subtle Glow Decoration -->
        <div class="absolute -top-12 -right-12 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex items-center justify-between">
            <!-- Left: Avatar & Store Details -->
            <div class="flex items-center space-x-4 sm:space-x-5">
                <!-- Interactive Avatar Container -->
                <div class="relative flex-shrink-0 group cursor-pointer" onclick="triggerLogoUpload()" title="Klik untuk mengubah foto profil toko">
                    <div id="avatar-container" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white/10 backdrop-blur-md border-2 border-white/30 overflow-hidden shadow-lg flex items-center justify-center transition-transform group-hover:scale-102">
                        <?php $hasLogo = !empty($settings['store_logo']); ?>
                        <img id="avatar-img" src="<?= $hasLogo ? htmlspecialchars($settings['store_logo']) : '' ?>" alt="Logo Toko" class="<?= $hasLogo ? '' : 'hidden' ?> w-full h-full object-cover">
                        <div id="avatar-fallback" class="<?= $hasLogo ? 'hidden' : '' ?> flex flex-col items-center justify-center text-white">
                            <span class="font-black text-2xl sm:text-3xl tracking-tight leading-none"><?= strtoupper(substr($settings['store_name'] ?? 'T', 0, 1)) ?></span>
                            <span class="text-[9px] uppercase tracking-wider text-blue-200 font-semibold mt-0.5">Toko</span>
                        </div>
                    </div>

                    <!-- Overlay on Hover -->
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition rounded-2xl flex flex-col items-center justify-center text-white text-[10px] font-bold">
                        <i data-lucide="camera" class="w-5 h-5 mb-0.5"></i>
                        <span>Ubah Foto</span>
                    </div>

                    <!-- Floating Camera Badge Button -->
                    <button type="button" class="absolute -bottom-1 -right-1 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-blue-600 hover:bg-blue-500 text-white flex items-center justify-center shadow-md border-2 border-white transition active:scale-90" title="Ubah Foto Profil">
                        <i data-lucide="camera" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                    </button>

                    <!-- Hidden File Input for Image Upload -->
                    <input type="file" id="logo-file-input" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden" onchange="uploadStoreLogo(event)">
                </div>

                <!-- Text Details -->
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 id="card-store-name" class="font-black text-base sm:text-xl text-white tracking-tight leading-tight truncate">
                            <?= htmlspecialchars($settings['store_name'] ?? 'Tokomu') ?>
                        </h2>
                        <span class="inline-flex items-center gap-1 text-[10px] sm:text-[11px] bg-emerald-500/20 text-emerald-300 font-bold px-2 py-0.5 rounded-full border border-emerald-400/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Aktif
                        </span>
                    </div>

                    <!-- Photo Action Buttons -->
                    <div class="mt-2.5 flex items-center gap-2 flex-wrap">
                        <button type="button" onclick="triggerLogoUpload()" class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white font-bold text-xs rounded-xl border border-white/30 transition flex items-center gap-1.5 active-press">
                            <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                            <span>Ganti Foto Toko</span>
                        </button>

                        <button type="button" id="btn-remove-logo" onclick="removeStoreLogo()" class="<?= $hasLogo ? '' : 'hidden' ?> px-3 py-1.5 bg-rose-500/30 hover:bg-rose-600 text-rose-100 hover:text-white font-bold text-xs rounded-xl border border-rose-400/40 transition flex items-center gap-1.5 active-press">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            <span>Hapus Foto</span>
                        </button>

                        <div id="logo-spinner" class="hidden text-xs text-blue-200 flex items-center gap-1.5">
                            <i data-lucide="loader-2" class="w-4 h-4 animate-spin text-white"></i>
                            <span>Mengunggah...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 overflow-hidden">
        <!-- Section Header -->
        <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-200/80 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-900 flex items-center justify-center border border-blue-200/60 font-bold">
                    <i data-lucide="store" class="w-4 h-4 text-blue-900"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm text-slate-800">Identitas & Kontak Warung</h3>
                    <p class="text-[11px] text-slate-500">Informasi ini dicetak pada bagian atas struk thermal penjualan</p>
                </div>
            </div>
        </div>

        <form id="settings-form" onsubmit="saveSettings(event)" class="p-5 sm:p-6 space-y-5 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 flex items-center gap-1">
                        <i data-lucide="store" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Nama Warung / Toko <span class="text-rose-500">*</span></span>
                    </label>
                    <input type="text" id="setting-store-name" required value="<?= htmlspecialchars($settings['store_name'] ?? 'Tokomu') ?>" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:bg-white focus:outline-none font-bold text-slate-800 transition">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 flex items-center gap-1">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Slogan / Tagline</span>
                    </label>
                    <input type="text" id="setting-store-tagline" value="<?= htmlspecialchars($settings['store_tagline'] ?? 'Lengkap, Murah & Bersahabat') ?>" placeholder="Contoh: Murah, Lengkap & Terpercaya" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:bg-white focus:outline-none font-medium text-slate-800 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 flex items-center gap-1">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Alamat Warung</span>
                    </label>
                    <input type="text" id="setting-store-address" value="<?= htmlspecialchars($settings['store_address'] ?? 'Jl. Pemajatan Komp. Permata Hijau 2, Blok B No.2') ?>" placeholder="Nama jalan, nomor, atau blok" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:bg-white focus:outline-none font-medium text-slate-800 transition">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5 flex items-center gap-1">
                        <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>No. WhatsApp / Telepon</span>
                    </label>
                    <input type="text" id="setting-store-phone" value="<?= htmlspecialchars($settings['store_phone'] ?? '0812-3456-7890') ?>" placeholder="08xxxxxxxxxx" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:bg-white focus:outline-none font-mono font-bold text-slate-800 transition">
                </div>
            </div>

            <!-- Struk Thermal Config -->
            <div class="pt-5 border-t border-slate-200">
                <div class="flex items-center gap-2 mb-3.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-900 flex items-center justify-center border border-blue-200">
                        <i data-lucide="printer" class="w-3.5 h-3.5 text-blue-900"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-xs text-slate-800 uppercase tracking-wider">Format Cetak Struk Thermal Mini</h4>
                        <p class="text-[11px] text-slate-500">Sesuaikan tulisan judul, catatan kaki, dan lebar kertas printer</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Judul Header Struk</label>
                        <input type="text" id="setting-receipt-header" value="<?= htmlspecialchars($settings['receipt_header'] ?? 'TERIMA KASIH TELAH BERBELANJA') ?>" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:bg-white focus:outline-none font-bold text-slate-800 transition">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Ukuran Kertas Bawaan Printer</label>
                        <select id="setting-paper-size" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:bg-white focus:outline-none font-bold text-slate-800 transition">
                            <option value="58mm" <?= ($settings['paper_size'] ?? '58mm') === '58mm' ? 'selected' : '' ?>>58 mm (Printer Mini Thermal Standar)</option>
                            <option value="80mm" <?= ($settings['paper_size'] ?? '') === '80mm' ? 'selected' : '' ?>>80 mm (Printer Thermal Kasir Lebar)</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3.5">
                    <label class="block font-bold text-slate-700 mb-1.5">Catatan Kaki Struk (Footer Note)</label>
                    <textarea id="setting-receipt-footer" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 focus:bg-white focus:outline-none font-medium leading-relaxed text-slate-800 transition"><?= htmlspecialchars($settings['receipt_footer'] ?? "Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.\nSemoga berkah & langganan terus!") ?></textarea>
                </div>

                <div class="mt-3 p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between">
                    <label for="setting-auto-print" class="flex items-center space-x-2.5 cursor-pointer font-bold text-slate-700 select-none">
                        <input type="checkbox" id="setting-auto-print" <?= !empty($settings['auto_print']) ? 'checked' : '' ?> class="w-4 h-4 rounded text-blue-900 focus:ring-blue-600">
                        <span>Cetak struk otomatis langsung setelah transaksi selesai</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end">
                <button type="submit" id="btn-save-settings" class="px-6 py-2.5 bg-blue-900 hover:bg-blue-800 text-white rounded-xl font-bold shadow-md hover:shadow-lg flex items-center space-x-2 active-press transition">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Backup, Restore & Demo Reset Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Backup & Restore -->
        <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 p-5 sm:p-6 space-y-3.5 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2.5 mb-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-900 flex items-center justify-center border border-blue-200/60 font-bold">
                        <i data-lucide="database" class="w-4 h-4 text-blue-900"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-800">Backup & Restore Data</h3>
                        <p class="text-[11px] text-slate-500">Amankan katalog, kasbon, dan transaksi</p>
                    </div>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Unduh salinan cadangan semua produk, transaksi, dan catatan kasbon agar data warung selalu aman dan bisa dipindahkan kapan saja.
                </p>
            </div>

            <div class="pt-3 space-y-2.5 border-t border-slate-100">
                <button onclick="downloadBackup()" class="w-full py-2.5 px-3 bg-blue-50 text-blue-900 hover:bg-blue-100 rounded-xl font-bold text-xs transition flex items-center justify-center space-x-1.5 border border-blue-200 active-press">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Unduh File Backup (.JSON)</span>
                </button>

                <div class="pt-2">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Pulihkan Data dari File Backup</label>
                    <input type="file" id="restore-file-input" accept=".json" class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 w-full">
                    <button onclick="uploadRestore()" class="w-full mt-2 py-2 px-3 bg-slate-900 text-white rounded-xl font-bold text-xs hover:bg-black transition flex items-center justify-center space-x-1 active-press">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span>Restore Data Sekarang</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Reset Demo Catalog -->
        <div class="bg-white rounded-3xl shadow-xs border border-slate-200/90 p-5 sm:p-6 space-y-3.5 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2.5 mb-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/60 font-bold">
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-amber-600"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-800">Reset Data Uji Coba</h3>
                        <p class="text-[11px] text-slate-500">Kembalikan katalog ke 40+ item sembako</p>
                    </div>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Gunakan tombol ini jika Anda ingin mengembalikan katalog barang ke 40+ produk bawaan sembako (Beras, Minyak, Telur, Indomie, Gas, Galon, Rokok, dll.).
                </p>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button onclick="resetDemoCatalog()" class="w-full py-2.5 px-3 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-xl font-bold text-xs transition flex items-center justify-center space-x-1.5 border border-rose-200 active-press">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    <span>Reset ke Katalog Bawaan Sembako</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function triggerLogoUpload() {
        document.getElementById('logo-file-input').click();
    }

    async function uploadStoreLogo(event) {
        const fileInput = event.target;
        if (!fileInput.files || fileInput.files.length === 0) return;

        const file = fileInput.files[0];

        // Validasi tipe file
        if (!file.type.startsWith('image/')) {
            showError('File yang dipilih harus berupa gambar (JPG, PNG, atau WebP)');
            fileInput.value = '';
            return;
        }

        // Validasi ukuran file (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showError('Ukuran gambar terlalu besar. Maksimal ukuran foto adalah 5 MB');
            fileInput.value = '';
            return;
        }

        const spinner = document.getElementById('logo-spinner');
        if (spinner) spinner.classList.remove('hidden');

        const formData = new FormData();
        formData.append('logo_file', file);

        try {
            const res = await fetch(`${baseUrl}/api/settings/logo`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                AppAudio.success();
                showToast('Foto profil toko berhasil diperbarui!');

                // Update Avatar Preview in Settings Card
                const avatarImg = document.getElementById('avatar-img');
                const avatarFallback = document.getElementById('avatar-fallback');
                const btnRemove = document.getElementById('btn-remove-logo');

                if (avatarImg) {
                    avatarImg.src = data.logo_url + '?t=' + Date.now();
                    avatarImg.classList.remove('hidden');
                }
                if (avatarFallback) {
                    avatarFallback.classList.add('hidden');
                }
                if (btnRemove) {
                    btnRemove.classList.remove('hidden');
                }

                // Update Top Navigation Bar Avatar immediately
                const navAvatar = document.getElementById('nav-store-avatar');
                if (navAvatar) {
                    navAvatar.innerHTML = `<img src="${data.logo_url}?t=${Date.now()}" alt="Logo" class="w-full h-full object-cover">`;
                }
            } else {
                showError('Gagal mengunggah foto: ' + data.message);
            }
        } catch (err) {
            showError('Terjadi kesalahan saat unggah foto: ' + err.message);
        } finally {
            if (spinner) spinner.classList.add('hidden');
            fileInput.value = '';
            lucide.createIcons();
        }
    }

    async function removeStoreLogo() {
        const ok = await showConfirm(
            'Hapus foto profil toko dan kembali menggunakan ikon standar?',
            'Hapus Foto Toko',
            'Ya, Hapus Foto',
            'Batal'
        );
        if (!ok) return;

        try {
            const res = await fetch(`${baseUrl}/api/settings/logo/remove`, {
                method: 'POST'
            });
            const data = await res.json();

            if (data.success) {
                AppAudio.success();
                showToast('Foto profil toko berhasil dihapus');

                // Update Avatar Preview in Settings Card
                const avatarImg = document.getElementById('avatar-img');
                const avatarFallback = document.getElementById('avatar-fallback');
                const btnRemove = document.getElementById('btn-remove-logo');

                if (avatarImg) {
                    avatarImg.src = '';
                    avatarImg.classList.add('hidden');
                }
                if (avatarFallback) {
                    avatarFallback.classList.remove('hidden');
                }
                if (btnRemove) {
                    btnRemove.classList.add('hidden');
                }

                // Update Top Navigation Bar Avatar
                const navAvatar = document.getElementById('nav-store-avatar');
                if (navAvatar) {
                    navAvatar.innerHTML = `<i data-lucide="store" class="w-4 h-4 sm:w-5 sm:h-5"></i>`;
                    lucide.createIcons();
                }
            } else {
                showError('Gagal menghapus foto: ' + data.message);
            }
        } catch (err) {
            showError('Terjadi kesalahan: ' + err.message);
        }
    }

    async function saveSettings(e) {
        e.preventDefault();
        const payload = {
            store_name: document.getElementById('setting-store-name').value.trim(),
            store_tagline: document.getElementById('setting-store-tagline').value.trim(),
            store_address: document.getElementById('setting-store-address').value.trim(),
            store_phone: document.getElementById('setting-store-phone').value.trim(),
            receipt_header: document.getElementById('setting-receipt-header').value.trim(),
            receipt_footer: document.getElementById('setting-receipt-footer').value.trim(),
            paper_size: document.getElementById('setting-paper-size').value,
            auto_print: document.getElementById('setting-auto-print').checked ? 1 : 0
        };

        const btn = document.getElementById('btn-save-settings');
        btn.disabled = true;

        try {
            const res = await fetch(`${baseUrl}/api/settings`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                AppAudio.success();
                showSuccess('Pengaturan warung berhasil disimpan!');
                
                // Update printer manager paper size
                if (window.printerManager) {
                    window.printerManager.setPaperSize(payload.paper_size);
                }

                // Update Top Nav Bar & Card Details dynamically
                const navName = document.getElementById('nav-store-name');
                const navTagline = document.getElementById('nav-store-tagline');
                const cardName = document.getElementById('card-store-name');

                if (navName) navName.innerText = payload.store_name;
                if (navTagline) navTagline.innerText = payload.store_tagline;
                if (cardName) cardName.innerText = payload.store_name;

                // Update fallback letter if visible
                const avatarFallback = document.getElementById('avatar-fallback');
                if (avatarFallback && !avatarFallback.classList.contains('hidden')) {
                    const firstLetter = payload.store_name.charAt(0).toUpperCase() || 'T';
                    avatarFallback.querySelector('span').innerText = firstLetter;
                }
            } else {
                showError('Gagal menyimpan: ' + data.message);
            }
        } catch (err) {
            showError('Terjadi kesalahan: ' + err.message);
        } finally {
            btn.disabled = false;
        }
    }

    function downloadBackup() {
        window.location.href = `${baseUrl}/api/backup`;
    }

    async function uploadRestore() {
        const fileInput = document.getElementById('restore-file-input');
        if (!fileInput.files || fileInput.files.length === 0) {
            showWarning('Pilih file backup (.json) terlebih dahulu');
            return;
        }

        const ok = await showConfirm(
            'Memulihkan backup akan menggantikan data yang ada saat ini. Lanjutkan pemulihan?',
            'Peringatan Restore Data',
            'Ya, Pulihkan',
            'Batal'
        );
        if (!ok) return;

        const formData = new FormData();
        formData.append('backup_file', fileInput.files[0]);

        try {
            const res = await fetch(`${baseUrl}/api/restore`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                AppAudio.success();
                await showSuccess('Data warung berhasil dipulihkan!');
                window.location.reload();
            } else {
                showError('Gagal restore: ' + data.message);
            }
        } catch (err) {
            showError('Terjadi kesalahan restore: ' + err.message);
        }
    }

    async function resetDemoCatalog() {
        const ok = await showConfirm(
            'Yakin ingin mereset seluruh data transaksi dan katalog kembali ke data standar bawaan?',
            'Reset Data Warung',
            'Ya, Reset Standar',
            'Batal'
        );
        if (!ok) return;

        try {
            const res = await fetch(`${baseUrl}/api/reset-demo`, { method: 'POST' });
            const data = await res.json();
            if (data.success) {
                AppAudio.success();
                await showSuccess('Katalog toko sembako berhasil di-reset!');
                window.location.href = `${baseUrl}/`;
            } else {
                showError('Gagal: ' + data.message);
            }
        } catch (err) {
            showError('Gagal: ' + err.message);
        }
    }
</script>
