<div class="max-w-7xl w-full mx-auto p-3 sm:p-6 space-y-4">
    <!-- Sleek Compact Header Bar -->
    <div class="flex items-center justify-between gap-2.5 bg-white p-3.5 sm:p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="package" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0">
                <h1 class="font-extrabold text-sm sm:text-base text-slate-900 leading-tight truncate">Produk & Stok</h1>
                <p class="text-[11px] text-slate-400 truncate">Kelola harga, barcode & stok barang</p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 flex-shrink-0">
            <button onclick="openProductModalWithScanner()" class="px-2.5 sm:px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200/80 rounded-xl text-xs font-bold transition flex items-center space-x-1 active:scale-95" title="Arahkan kamera ke kemasan untuk isi otomatis nama, foto & kategori">
                <i data-lucide="scan" class="w-3.5 h-3.5 text-blue-600"></i>
                <span class="hidden sm:inline">Scan Auto-Fill</span>
                <span class="sm:hidden">Scan</span>
            </button>
            <button onclick="openProductModal()" class="px-3 sm:px-3.5 py-2 bg-blue-900 hover:bg-blue-850 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1 shadow-xs active:scale-95">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Tambah</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Toolbar: Clean & Spacious -->
    <div class="bg-white p-3.5 sm:p-4 rounded-2xl shadow-sm border border-slate-200 space-y-2.5 text-xs">
        <!-- Row 1: Full-width Search Input with Camera Icon -->
        <div class="relative w-full">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
            <input type="text" id="prod-search" placeholder="Cari nama produk atau barcode..." class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium text-slate-800 text-xs">
            <button type="button" onclick="openCameraScannerForProductSearch()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600 p-1.5 rounded-lg active:scale-90 transition" title="Scan Barcode dengan Kamera">
                <i data-lucide="camera" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Row 2: Category Filter Dropdown & Low Stock Toggle -->
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <select id="prod-filter-cat" class="w-full appearance-none pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-slate-700 text-xs">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>

            <label class="flex items-center space-x-1.5 cursor-pointer font-bold text-slate-700 bg-amber-50 hover:bg-amber-100/80 px-3 py-2 rounded-xl border border-amber-200 flex-shrink-0 transition active:scale-95 select-none">
                <input type="checkbox" id="prod-filter-low-stock" class="rounded text-amber-600 focus:ring-amber-500 w-3.5 h-3.5">
                <span class="text-amber-900 text-[11px] sm:text-xs">Stok Menipis</span>
            </label>
        </div>
    </div>

    <!-- Product Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Desktop Table View (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Barcode / Kode</th>
                        <th class="py-3 px-4">Nama Produk</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-right">Harga Modal</th>
                        <th class="py-3 px-4 text-right">Harga Jual</th>
                        <th class="py-3 px-4 text-right">Laba/Margin</th>
                        <th class="py-3 px-4 text-center">Stok</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="product-table-body" class="divide-y divide-slate-100 font-medium">
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View with Dropdown Details (Visible on Mobile) -->
        <div id="product-cards-container" class="md:hidden divide-y divide-slate-100">
            <!-- Populated by JS -->
        </div>
        <div id="table-loading" class="py-12 text-center text-slate-400">
            <div class="animate-spin inline-block w-6 h-6 border-2 border-emerald-600 border-t-transparent rounded-full mb-2"></div>
            <div>Memuat data produk...</div>
        </div>
    </div>
</div>

<!-- Modal: Add / Edit Product -->
<div id="product-modal" class="fixed inset-0 z-50 hidden modal-backdrop-blur flex items-center justify-center p-3 sm:p-5 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl max-w-[420px] sm:max-w-md w-full my-auto overflow-hidden border border-slate-200/90 max-h-[88vh] sm:max-h-[92vh] flex flex-col transition-all">
        <div class="bg-emerald-700 px-4 py-2.5 sm:px-5 sm:py-3 text-white flex items-center justify-between flex-shrink-0">
            <div class="flex items-center space-x-2 min-w-0">
                <i data-lucide="package-plus" class="w-4 h-4 text-emerald-200 flex-shrink-0"></i>
                <h3 id="modal-product-title" class="font-bold text-xs sm:text-sm truncate">Tambah Produk Baru</h3>
            </div>
            <button type="button" onclick="closeProductModal()" class="text-emerald-200 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                <i data-lucide="x" class="w-4 h-4 sm:w-5 sm:h-5"></i>
            </button>
        </div>
        <form id="product-form" onsubmit="saveProduct(event)" class="p-3.5 sm:p-4.5 space-y-2 sm:space-y-2.5 text-xs overflow-y-auto flex-1">
            <input type="hidden" id="form-product-id">

            <!-- Auto-Fill Match Notification Banner -->
            <div id="master-match-banner" class="hidden p-2.5 rounded-xl bg-emerald-50 border border-emerald-300 flex items-center justify-between text-xs transition-all duration-200 shadow-xs">
                <div class="flex items-center space-x-2 min-w-0">
                    <img id="master-match-img" src="" class="w-8 h-8 rounded-lg object-cover border border-emerald-200 flex-shrink-0 bg-white shadow-xs" onerror="this.classList.add('hidden')">
                    <div class="min-w-0">
                        <div class="inline-flex items-center gap-1 text-[9px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-200/80 px-1.5 py-0.5 rounded-full">
                            <i data-lucide="sparkles" class="w-2.5 h-2.5 text-emerald-700"></i>
                            <span id="master-match-source">Master Sembako</span>
                        </div>
                        <div id="master-match-name" class="font-extrabold text-slate-900 text-[11px] truncate mt-0.5"></div>
                        <div class="text-[10px] text-emerald-700 font-medium">Nama, foto & kategori otomatis terisi ✨</div>
                    </div>
                </div>
                <button type="button" onclick="dismissMasterBanner()" class="text-slate-400 hover:text-slate-600 p-1 flex-shrink-0">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>

            <!-- Existing In Store Warning Banner -->
            <div id="master-existing-banner" class="hidden p-2.5 rounded-xl bg-amber-50 border border-amber-300 flex items-center justify-between text-xs shadow-xs">
                <div class="flex items-center space-x-2 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-amber-200 text-amber-800 flex items-center justify-center font-bold flex-shrink-0">
                        <i data-lucide="info" class="w-3.5 h-3.5"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-amber-900 text-xs truncate" id="master-existing-text">Produk ini sudah ada di tokomu!</div>
                        <div class="text-[10px] text-amber-700">Kamu bisa edit harga atau tambah stok.</div>
                    </div>
                </div>
                <button type="button" id="btn-open-existing-edit" class="px-2 py-1 bg-amber-600 text-white rounded-lg font-bold text-[10px] hover:bg-amber-700 flex-shrink-0">
                    Buka Edit
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5 flex items-center justify-between">
                        <span>Barcode / SKU</span>
                        <span id="barcode-lookup-spinner" class="hidden text-emerald-600 font-semibold text-[10px] items-center gap-1">
                            <span class="inline-block w-2.5 h-2.5 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"></span>
                            <span>Mencari...</span>
                        </span>
                    </label>
                    <div class="flex gap-1">
                        <input type="text" id="form-barcode" placeholder="Scan atau ketik barcode" class="flex-1 px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none" oninput="onBarcodeInputChange(this.value)">
                        <button type="button" onclick="openCameraScannerForProductForm()" class="px-2 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg font-bold flex items-center space-x-1 flex-shrink-0" title="Scan Barcode dengan Kamera">
                            <i data-lucide="camera" class="w-3.5 h-3.5"></i>
                            <span class="text-[11px]">Scan</span>
                        </button>
                        <button type="button" onclick="generateRandomBarcode()" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold text-[11px] flex-shrink-0" title="Generate barcode acak">Acak</button>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Kategori</label>
                    <select id="form-category-id" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="relative">
                <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5 flex items-center justify-between">
                    <span>Nama Produk <span class="text-red-500">*</span></span>
                    <span class="text-[10px] text-slate-400 font-normal">Ketik untuk auto-rekomendasi</span>
                </label>
                <input type="text" id="form-name" required placeholder="Contoh: Indomie Goreng / Beras Rojo Lele" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none" autocomplete="off" oninput="onProductNameInput(this.value)">
                <!-- Autocomplete suggestions dropdown -->
                <div id="name-suggestions-box" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-30 overflow-hidden divide-y divide-slate-100 max-h-48 overflow-y-auto">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">URL Gambar / Foto Produk (Opsional)</label>
                <div class="flex gap-1.5 items-center">
                    <input type="url" id="form-image" placeholder="https://... atau paste link foto" class="flex-1 px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none" oninput="updateImagePreview(this.value)">
                    <div id="image-preview-box" class="w-7 h-7 rounded-lg border border-slate-200 overflow-hidden bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-[9px] text-slate-400">Foto</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Satuan</label>
                    <select id="form-unit" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="pcs">pcs</option>
                        <option value="kg">kg</option>
                        <option value="sak">sak</option>
                        <option value="renteng">renteng</option>
                        <option value="bungkus">bungkus</option>
                        <option value="dus">dus</option>
                        <option value="btl">btl</option>
                        <option value="kaleng">kaleng</option>
                        <option value="kotak">kotak</option>
                        <option value="butir">butir</option>
                        <option value="galon">galon</option>
                        <option value="tabung">tabung</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5 truncate" title="Harga Modal">Harga Beli</label>
                    <input type="number" id="form-buy-price" value="0" min="0" oninput="calculateModalMargin()" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5 truncate">Harga Jual <span class="text-red-500">*</span></label>
                    <input type="number" id="form-sell-price" required min="100" oninput="calculateModalMargin()" class="w-full px-2 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold text-emerald-800">
                </div>
            </div>

            <!-- Profit preview banner -->
            <div id="form-margin-preview" class="py-1 px-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-900 flex justify-between font-bold text-[11px]">
                <span>Perkiraan Keuntungan:</span>
                <span id="form-margin-val">Rp 0 (0%)</span>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Stok Saat Ini</label>
                    <input type="number" id="form-stock" value="0" step="any" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Minimal Stok</label>
                    <input type="number" id="form-min-stock" value="5" step="any" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 flex justify-end gap-1.5 flex-shrink-0">
                <button type="button" onclick="closeProductModal()" class="px-3.5 py-1.5 border border-slate-200 rounded-lg font-bold text-slate-700 text-xs hover:bg-slate-50 transition active:scale-95">Batal</button>
                <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-xs shadow-xs transition active:scale-95">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Quick Restock (Barang Masuk) -->
<div id="restock-modal" class="fixed inset-0 z-50 hidden modal-backdrop-blur flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl max-w-[360px] w-full my-auto overflow-hidden border border-slate-200/90 max-h-[88vh] flex flex-col">
        <div class="bg-blue-600 px-4 py-2.5 text-white flex items-center justify-between flex-shrink-0">
            <h3 class="font-bold text-xs sm:text-sm flex items-center gap-1.5">
                <i data-lucide="package-plus" class="w-4 h-4"></i>
                <span>Barang Masuk / Restock</span>
            </h3>
            <button onclick="closeRestockModal()" class="text-blue-200 hover:text-white p-1 rounded-lg">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="restock-form" onsubmit="submitRestock(event)" class="p-3.5 sm:p-4 space-y-2.5 text-xs overflow-y-auto flex-1">
            <input type="hidden" id="restock-product-id">
            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
                <label class="block text-slate-400 text-[10px] mb-0.5">Produk Terpilih:</label>
                <div id="restock-product-name" class="font-bold text-xs text-slate-800 leading-snug"></div>
                <div id="restock-current-stock" class="text-slate-500 text-[11px] mt-0.5">Stok saat ini: 0</div>
            </div>
            <div>
                <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Jumlah Masuk <span class="text-red-500">*</span></label>
                <input type="number" id="restock-qty" required min="0.1" step="any" placeholder="Contoh: 10" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg font-bold text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Update Harga Modal / Beli (Opsional)</label>
                <input type="number" id="restock-buy-price" placeholder="Kosongkan jika harga tetap" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Catatan</label>
                <input type="text" id="restock-note" value="Kulakan / Masuk Barang" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div class="pt-2 flex justify-end gap-1.5 flex-shrink-0">
                <button type="button" onclick="closeRestockModal()" class="px-3.5 py-1.5 border border-slate-200 rounded-lg font-bold text-slate-700 text-xs hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-xs shadow-xs">Simpan Masuk</button>
            </div>
        </form>
    </div>
</div>

<script>
    let productsList = [];

    document.addEventListener('DOMContentLoaded', () => {
        loadProducts();

        document.getElementById('prod-search').addEventListener('input', debounce(loadProducts, 300));
        document.getElementById('prod-filter-cat').addEventListener('change', loadProducts);
        document.getElementById('prod-filter-low-stock').addEventListener('change', loadProducts);
    });

    function debounce(fn, delay) {
        let timer = null;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    async function loadProducts() {
        const q = document.getElementById('prod-search').value.trim();
        const cat = document.getElementById('prod-filter-cat').value;
        const low = document.getElementById('prod-filter-low-stock').checked ? '1' : '';

        document.getElementById('table-loading').classList.remove('hidden');

        try {
            const res = await fetch(`${baseUrl}/api/products?q=${encodeURIComponent(q)}&category_id=${cat}&low_stock=${low}`);
            const data = await res.json();
            if (data.success) {
                productsList = data.products;
                renderProductTable();
            }
        } catch (err) {
            console.error('Failed to load products:', err);
        } finally {
            document.getElementById('table-loading').classList.add('hidden');
        }
    }

    function updateImagePreview(url) {
        const box = document.getElementById('image-preview-box');
        if (!box) return;
        if (url && url.trim().length > 5) {
            box.innerHTML = `<img src="${url.trim()}" class="w-full h-full object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\\'text-[10px] text-red-500\\'>Error</span>';">`;
        } else {
            box.innerHTML = `<span class="text-[10px] text-slate-400">Foto</span>`;
        }
    }

    function renderProductTable() {
        const tbody = document.getElementById('product-table-body');
        const cardsContainer = document.getElementById('product-cards-container');

        if (productsList.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="py-8 text-center text-slate-400">Tidak ada produk yang cocok dengan pencarian.</td></tr>`;
            if (cardsContainer) {
                cardsContainer.innerHTML = `<div class="py-12 text-center text-slate-400">
                    <i data-lucide="package-search" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                    <div class="font-bold text-sm text-slate-600">Tidak ada produk ditemukan</div>
                    <div class="text-xs">Coba kata kunci lain atau tambah produk baru</div>
                </div>`;
            }
            lucide.createIcons();
            return;
        }

        // 1. Render Desktop Table Rows
        tbody.innerHTML = productsList.map(p => {
            const isLow = p.stock <= p.min_stock;
            const profit = p.sell_price - p.buy_price;
            const marginPct = p.buy_price > 0 ? ((profit / p.buy_price) * 100).toFixed(0) : 0;
            const imgUrl = p.image ? p.image : '';

            return `
                <tr class="hover:bg-slate-50 transition ${isLow ? 'bg-amber-50/40' : ''}">
                    <td class="py-3 px-4 font-mono text-[11px] text-slate-500">${p.barcode || '-'}</td>
                    <td class="py-2.5 px-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0 border border-slate-200 flex items-center justify-center">
                                ${imgUrl ? `
                                    <img src="${imgUrl}" alt="${p.name}" class="w-full h-full object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='📦';">
                                ` : `<span class="text-base">📦</span>`}
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-800 leading-snug">${p.name}</div>
                                <div class="text-[10px] text-slate-400 font-mono sm:hidden">${p.barcode || '-'}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-slate-500">${p.category_name || '-'}</td>
                    <td class="py-3 px-4 text-right text-slate-600">Rp ${Number(p.buy_price).toLocaleString('id-ID')}</td>
                    <td class="py-3 px-4 text-right font-bold text-slate-900">Rp ${Number(p.sell_price).toLocaleString('id-ID')}</td>
                    <td class="py-3 px-4 text-right text-emerald-700 font-semibold">
                        +Rp ${Number(profit).toLocaleString('id-ID')} <span class="text-[10px] text-slate-400">(${marginPct}%)</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        ${isLow ? `
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold bg-amber-100 text-amber-800">
                                ${p.stock} ${p.unit}
                            </span>` : `
                            <span class="text-slate-800 font-semibold">${p.stock} ${p.unit}</span>`}
                    </td>
                    <td class="py-3 px-4 text-center">
                        <div class="inline-flex items-center space-x-1.5">
                            <button onclick="openRestockModal(${p.id})" class="px-2 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg font-bold text-[11px] transition flex items-center space-x-1" title="Barang Masuk">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>Masuk</span>
                            </button>
                            <button onclick="editProduct(${p.id})" class="p-1 text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition" title="Edit Produk">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteProduct(${p.id})" class="p-1 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Produk">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        // 2. Render Mobile Interactive Cards (Card Dropdown)
        if (cardsContainer) {
            cardsContainer.innerHTML = productsList.map(p => {
                const isLow = p.stock <= p.min_stock;
                const profit = p.sell_price - p.buy_price;
                const marginPct = p.buy_price > 0 ? ((profit / p.buy_price) * 100).toFixed(0) : 0;
                const imgUrl = p.image ? p.image : '';

                return `
                    <div class="p-3 space-y-2 bg-white hover:bg-slate-50/70 transition ${isLow ? 'bg-amber-50/20' : ''}">
                        <!-- Top: Photo + Name + Stock Badge -->
                        <div class="flex items-start gap-2.5">
                            <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0 border border-slate-200 flex items-center justify-center">
                                ${imgUrl ? `
                                    <img src="${imgUrl}" alt="${p.name}" class="w-full h-full object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='📦';">
                                ` : `<span class="text-base">📦</span>`}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-1">
                                    <div class="font-bold text-xs sm:text-sm text-slate-900 leading-snug line-clamp-2">${p.name}</div>
                                    <div class="flex-shrink-0">
                                        ${isLow ? `
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full font-bold bg-amber-100 text-amber-800 text-[9px]">
                                                <i data-lucide="alert-triangle" class="w-2.5 h-2.5 text-amber-600"></i>
                                                <span>${p.stock} ${p.unit}</span>
                                            </span>` : `
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-700 text-[9px]">
                                                ${p.stock} ${p.unit}
                                            </span>`}
                                    </div>
                                </div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5 flex items-center gap-1 flex-wrap">
                                    <span class="bg-slate-100 text-slate-600 px-1 py-0.2 rounded text-[9px] font-sans font-medium">${p.category_name || 'Umum'}</span>
                                    <span>&bull;</span>
                                    <span>${p.barcode || 'Tanpa Barcode'}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Price & Margin Box -->
                        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-2 flex items-center justify-between">
                            <div>
                                <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Harga Jual</div>
                                <div class="text-sm font-black text-slate-900 font-mono">
                                    Rp ${Number(p.sell_price).toLocaleString('id-ID')}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Keuntungan / Laba</div>
                                <div class="text-[11px] font-bold text-emerald-700 font-mono">
                                    +Rp ${Number(profit).toLocaleString('id-ID')} <span class="text-[9px] text-slate-400">(${marginPct}%)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons & Dropdown Toggle -->
                        <div class="flex items-center gap-1.5 pt-0.5">
                            <button onclick="openRestockModal(${p.id})" class="flex-1 py-1.5 px-2 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200/60 rounded-lg font-bold text-xs transition flex items-center justify-center space-x-1 active:scale-95 shadow-xs">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>Restock</span>
                            </button>
                            <button onclick="editProduct(${p.id})" class="py-1.5 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold text-xs transition flex items-center justify-center space-x-1 active:scale-95">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                <span>Edit</span>
                            </button>
                            <button onclick="deleteProduct(${p.id})" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition active:scale-95" title="Hapus">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                            <button onclick="toggleProductDetail(${p.id})" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition active:scale-95" title="Rincian Lengkap">
                                <i data-lucide="chevron-down" id="prod-arrow-${p.id}" class="w-3.5 h-3.5 transition-transform duration-200"></i>
                            </button>
                        </div>

                        <!-- Dropdown Details -->
                        <div id="prod-detail-${p.id}" class="hidden pt-2 space-y-1.5 text-[11px] text-slate-600 bg-slate-50/80 -mx-3 -mb-3 p-3 rounded-b-xl border-t border-slate-100">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Harga Modal (Beli):</span>
                                <span class="font-mono font-bold text-slate-700">Rp ${Number(p.buy_price).toLocaleString('id-ID')}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Peringatan Min. Stok:</span>
                                <span class="font-bold ${isLow ? 'text-amber-700' : 'text-slate-700'}">${p.min_stock} ${p.unit}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Barcode / SKU:</span>
                                <span class="font-mono text-slate-700">${p.barcode || '-'}</span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        lucide.createIcons();
    }

    function toggleProductDetail(id) {
        const detailEl = document.getElementById('prod-detail-' + id);
        const arrowEl = document.getElementById('prod-arrow-' + id);
        if (!detailEl) return;
        const isHidden = detailEl.classList.contains('hidden');
        if (isHidden) {
            detailEl.classList.remove('hidden');
            if (arrowEl) arrowEl.classList.add('rotate-180');
        } else {
            detailEl.classList.add('hidden');
            if (arrowEl) arrowEl.classList.remove('rotate-180');
        }
    }

    function calculateModalMargin() {
        const buy = parseFloat(document.getElementById('form-buy-price').value) || 0;
        const sell = parseFloat(document.getElementById('form-sell-price').value) || 0;
        const profit = sell - buy;
        const pct = buy > 0 ? ((profit / buy) * 100).toFixed(0) : 0;
        document.getElementById('form-margin-val').innerText = `Rp ${Number(profit).toLocaleString('id-ID')} (${pct}%)`;
    }

    function generateRandomBarcode() {
        dismissMasterBanner();
        document.getElementById('form-barcode').value = '899' + Math.floor(100000000 + Math.random() * 900000000);
    }

    function dismissMasterBanner() {
        const match = document.getElementById('master-match-banner');
        const exist = document.getElementById('master-existing-banner');
        if (match) match.classList.add('hidden');
        if (exist) exist.classList.add('hidden');
    }

    function openProductModal() {
        document.getElementById('form-product-id').value = '';
        document.getElementById('modal-product-title').innerText = 'Tambah Produk Baru';
        document.getElementById('product-form').reset();
        document.getElementById('form-image').value = '';
        updateImagePreview('');
        dismissMasterBanner();
        const box = document.getElementById('name-suggestions-box');
        if (box) box.classList.add('hidden');
        
        generateRandomBarcode();
        calculateModalMargin();
        document.getElementById('product-modal').classList.remove('hidden');
    }

    function openProductModalWithScanner() {
        if (!window.cameraScanner) {
            openProductModal();
            return;
        }
        window.cameraScanner.open({
            title: 'Arahkan Kamera ke Barcode Kemasan',
            continuous: false,
            showCartInfo: false,
            onScan: (code) => {
                openProductModal();
                document.getElementById('form-barcode').value = code;
                lookupBarcodeAndAutoFill(code);
            }
        });
    }

    function closeProductModal() {
        document.getElementById('product-modal').classList.add('hidden');
        dismissMasterBanner();
        const box = document.getElementById('name-suggestions-box');
        if (box) box.classList.add('hidden');
    }

    // Auto-Lookup when typing or scanning barcode
    let barcodeLookupTimer = null;
    function onBarcodeInputChange(val) {
        clearTimeout(barcodeLookupTimer);
        const clean = val.trim();
        if (clean.length >= 8) {
            barcodeLookupTimer = setTimeout(() => {
                lookupBarcodeAndAutoFill(clean);
            }, 350);
        }
    }

    async function lookupBarcodeAndAutoFill(code) {
        if (!code || code.trim().length < 4) return;
        const cleanBarcode = code.trim();
        const spinner = document.getElementById('barcode-lookup-spinner');
        if (spinner) spinner.classList.remove('hidden');

        try {
            const res = await fetch(`${baseUrl}/api/master-lookup?barcode=${encodeURIComponent(cleanBarcode)}`);
            const data = await res.json();

            dismissMasterBanner();

            if (data.success && data.found) {
                const p = data.product;

                if (data.already_in_store) {
                    const existBanner = document.getElementById('master-existing-banner');
                    const existText = document.getElementById('master-existing-text');
                    existText.innerText = `"${p.name}" sudah ada di tokomu (Stok: ${p.stock} ${p.unit})!`;
                    document.getElementById('btn-open-existing-edit').onclick = () => {
                        closeProductModal();
                        editProduct(p.id);
                    };
                    existBanner.classList.remove('hidden');
                    return;
                }

                // Auto-fill form fields!
                if (p.name) document.getElementById('form-name').value = p.name;
                if (p.category_id) document.getElementById('form-category-id').value = p.category_id;
                if (p.unit) document.getElementById('form-unit').value = p.unit;
                if (p.buy_price) document.getElementById('form-buy-price').value = p.buy_price;
                if (p.sell_price) document.getElementById('form-sell-price').value = p.sell_price;
                if (p.image) {
                    document.getElementById('form-image').value = p.image;
                    updateImagePreview(p.image);
                }
                document.getElementById('form-stock').value = p.stock || 10;
                document.getElementById('form-min-stock').value = p.min_stock || 5;

                calculateModalMargin();

                // Show match banner
                const matchBanner = document.getElementById('master-match-banner');
                const matchName = document.getElementById('master-match-name');
                const matchImg = document.getElementById('master-match-img');
                const matchSource = document.getElementById('master-match-source');

                matchName.innerText = p.name;
                matchSource.innerText = data.source === 'open_food_facts' ? 'Database Barcode Online' : 'Master Database Sembako';
                if (p.image) {
                    matchImg.src = p.image;
                    matchImg.classList.remove('hidden');
                } else {
                    matchImg.classList.add('hidden');
                }
                matchBanner.classList.remove('hidden');

                // Focus directly on Harga Jual for quick review
                setTimeout(() => {
                    const sellInput = document.getElementById('form-sell-price');
                    if (sellInput) {
                        sellInput.focus();
                        sellInput.select();
                    }
                }, 300);

                if (window.AppAudio && typeof window.AppAudio.beep === 'function') {
                    window.AppAudio.beep();
                }

                lucide.createIcons();
            }
        } catch (err) {
            console.error('Master lookup failed:', err);
        } finally {
            if (spinner) spinner.classList.add('hidden');
        }
    }

    // Live search by name from master database
    let nameSearchTimer = null;
    function onProductNameInput(val) {
        clearTimeout(nameSearchTimer);
        const box = document.getElementById('name-suggestions-box');
        if (!val || val.trim().length < 2) {
            if (box) {
                box.classList.add('hidden');
                box.innerHTML = '';
            }
            return;
        }

        nameSearchTimer = setTimeout(async () => {
            try {
                const res = await fetch(`${baseUrl}/api/master-search?q=${encodeURIComponent(val.trim())}`);
                const data = await res.json();
                if (data.success && data.results && data.results.length > 0) {
                    box.innerHTML = data.results.map(item => `
                        <div onclick='selectMasterSuggestion(${JSON.stringify(item).replace(/'/g, "&#39;")})' class="p-2.5 hover:bg-emerald-50 cursor-pointer flex items-center justify-between transition">
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <img src="${item.image || ''}" class="w-8 h-8 rounded-lg object-cover bg-slate-100 flex-shrink-0 border border-slate-200" onerror="this.src=''; this.style.display='none';">
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 truncate text-xs">${item.name}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">${item.barcode} &bull; ${item.category_name}</div>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 pl-2">
                                <div class="font-extrabold text-emerald-700 text-xs">Rp ${Number(item.sell_price).toLocaleString('id-ID')}</div>
                                <span class="text-[9px] bg-emerald-100 text-emerald-800 font-bold px-1.5 py-0.5 rounded">Pilih Auto-Fill</span>
                            </div>
                        </div>
                    `).join('');
                    box.classList.remove('hidden');
                } else {
                    box.classList.add('hidden');
                }
            } catch (e) {
                if (box) box.classList.add('hidden');
            }
        }, 250);
    }

    function selectMasterSuggestion(item) {
        document.getElementById('form-barcode').value = item.barcode;
        document.getElementById('form-name').value = item.name;
        if (item.category_id) document.getElementById('form-category-id').value = item.category_id;
        if (item.unit) document.getElementById('form-unit').value = item.unit;
        if (item.buy_price) document.getElementById('form-buy-price').value = item.buy_price;
        if (item.sell_price) document.getElementById('form-sell-price').value = item.sell_price;
        if (item.image) {
            document.getElementById('form-image').value = item.image;
            updateImagePreview(item.image);
        }
        document.getElementById('form-stock').value = 10;
        document.getElementById('name-suggestions-box').classList.add('hidden');
        calculateModalMargin();

        dismissMasterBanner();
        const matchBanner = document.getElementById('master-match-banner');
        document.getElementById('master-match-name').innerText = item.name;
        document.getElementById('master-match-source').innerText = 'Pilihan Master Sembako';
        const matchImg = document.getElementById('master-match-img');
        if (item.image) {
            matchImg.src = item.image;
            matchImg.classList.remove('hidden');
        } else {
            matchImg.classList.add('hidden');
        }
        matchBanner.classList.remove('hidden');

        lucide.createIcons();

        const sellInput = document.getElementById('form-sell-price');
        if (sellInput) {
            sellInput.focus();
            sellInput.select();
        }
    }

    function editProduct(id) {
        const p = productsList.find(item => item.id === id);
        if (!p) return;

        document.getElementById('form-product-id').value = p.id;
        document.getElementById('modal-product-title').innerText = 'Edit Produk: ' + p.name;
        document.getElementById('form-barcode').value = p.barcode || '';
        document.getElementById('form-category-id').value = p.category_id || '';
        document.getElementById('form-name').value = p.name;
        document.getElementById('form-image').value = p.image || '';
        updateImagePreview(p.image || '');
        document.getElementById('form-unit').value = p.unit || 'pcs';
        document.getElementById('form-buy-price').value = p.buy_price || 0;
        document.getElementById('form-sell-price').value = p.sell_price || 0;
        document.getElementById('form-stock').value = p.stock || 0;
        document.getElementById('form-min-stock').value = p.min_stock || 5;

        dismissMasterBanner();
        const box = document.getElementById('name-suggestions-box');
        if (box) box.classList.add('hidden');

        calculateModalMargin();
        document.getElementById('product-modal').classList.remove('hidden');
    }

    async function saveProduct(e) {
        e.preventDefault();
        const payload = {
            id: document.getElementById('form-product-id').value || null,
            barcode: document.getElementById('form-barcode').value.trim(),
            category_id: document.getElementById('form-category-id').value,
            name: document.getElementById('form-name').value.trim(),
            image: document.getElementById('form-image').value.trim(),
            unit: document.getElementById('form-unit').value,
            buy_price: document.getElementById('form-buy-price').value,
            sell_price: document.getElementById('form-sell-price').value,
            stock: document.getElementById('form-stock').value,
            min_stock: document.getElementById('form-min-stock').value
        };

        try {
            const res = await fetch(`${baseUrl}/api/products`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                closeProductModal();
                showToast(data.message || 'Produk berhasil disimpan!');
                loadProducts();
            } else {
                showError('Gagal menyimpan: ' + data.message);
            }
        } catch (err) {
            showError('Terjadi kesalahan: ' + err.message);
        }
    }

    async function deleteProduct(id) {
        const ok = await showConfirm('Yakin ingin menghapus produk ini dari daftar warung?', 'Hapus Produk', 'Ya, Hapus', 'Batal');
        if (!ok) return;
        try {
            const res = await fetch(`${baseUrl}/api/products/${id}`, { method: 'DELETE' });
            const data = await res.json();
            if (data.success) {
                showToast('Produk berhasil dihapus');
                loadProducts();
            } else {
                showError('Gagal menghapus: ' + data.message);
            }
        } catch (err) {
            showError('Gagal menghapus: ' + err.message);
        }
    }

    function openRestockModal(id) {
        const p = productsList.find(item => item.id === id);
        if (!p) return;

        document.getElementById('restock-product-id').value = p.id;
        document.getElementById('restock-product-name').innerText = p.name;
        document.getElementById('restock-current-stock').innerText = `Stok saat ini: ${p.stock} ${p.unit}`;
        document.getElementById('restock-qty').value = '';
        document.getElementById('restock-buy-price').value = '';
        document.getElementById('restock-modal').classList.remove('hidden');
        document.getElementById('restock-qty').focus();
    }

    function closeRestockModal() {
        document.getElementById('restock-modal').classList.add('hidden');
    }

    async function submitRestock(e) {
        e.preventDefault();
        const id = document.getElementById('restock-product-id').value;
        const addQty = parseFloat(document.getElementById('restock-qty').value);
        const buyPrice = document.getElementById('restock-buy-price').value;
        const note = document.getElementById('restock-note').value;

        try {
            const res = await fetch(`${baseUrl}/api/products/restock`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, add_qty: addQty, buy_price: buyPrice, note })
            });
            const data = await res.json();
            if (data.success) {
                closeRestockModal();
                showToast(data.message || 'Stok berhasil ditambahkan!');
                loadProducts();
            } else {
                showError('Gagal: ' + data.message);
            }
        } catch (err) {
            showError('Terjadi kesalahan: ' + err.message);
        }
    }

    function openCameraScannerForProductForm() {
        if (!window.cameraScanner) return;
        window.cameraScanner.open({
            title: 'Scan Barcode Produk Baru',
            continuous: false,
            showCartInfo: false,
            onScan: (code) => {
                document.getElementById('form-barcode').value = code;
                lookupBarcodeAndAutoFill(code);
            }
        });
    }

    function openCameraScannerForProductSearch() {
        if (!window.cameraScanner) return;
        window.cameraScanner.open({
            title: 'Scan Cari Produk',
            continuous: false,
            showCartInfo: false,
            onScan: (code) => {
                document.getElementById('prod-search').value = code;
                loadProducts();
            }
        });
    }
</script>
