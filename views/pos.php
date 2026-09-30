<div class="flex-1 flex flex-col max-w-7xl w-full mx-auto px-2 pt-2.5 pb-2 sm:p-4 gap-2.5">

    <!-- Main Container: Side by side on Desktop, Full view on Mobile -->
    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden gap-3 sm:gap-4">

    <!-- Left Column: Product Catalog & Search (Scrollable) -->
    <div id="pos-catalog-section" class="flex-1 flex flex-col bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden min-h-[500px]">
        <!-- Search & Filter Header (Sleek single-line layout) -->
        <div class="p-2.5 sm:p-3.5 border-b border-slate-100 bg-white space-y-2">
            <div class="flex items-center gap-2">
                <!-- Smart Search Input (Searches by Name or Barcode) -->
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" id="pos-search-input" placeholder="Cari nama barang / barcode..." class="w-full pl-9 pr-8 py-2 bg-slate-100 hover:bg-slate-100/90 focus:bg-white border border-slate-200/80 focus:border-emerald-500 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition">
                    <button id="btn-clear-search" onclick="clearSearch()" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                <!-- Camera Barcode Scan Button -->
                <button type="button" onclick="openCameraScannerForPos()" class="px-2.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold flex items-center space-x-1 transition active-press flex-shrink-0" title="Buka Kamera untuk Scan Barcode">
                    <i data-lucide="camera" class="w-4 h-4 text-emerald-700"></i>
                    <span>Kamera</span>
                </button>

                <!-- Add Manual Item Button -->
                <button type="button" onclick="openCustomItemModal()" class="px-2.5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold flex items-center space-x-1 transition shadow-xs active-press flex-shrink-0" title="Tambah barang manual tanpa stok (contoh: sayur, es batu)">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Manual</span>
                </button>
            </div>

            <!-- Category Pills (Generous spacing & hidden ugly scrollbar) -->
            <div class="flex items-center space-x-2 overflow-x-auto pt-1 pb-2.5 text-xs no-scrollbar scroll-smooth" id="category-pills">
                <button onclick="filterCategory('')" class="category-pill active-cat px-3 py-1.5 rounded-xl font-bold bg-emerald-600 text-white transition flex-shrink-0 shadow-xs text-xs" data-cat="">
                    Semua
                </button>
                <?php foreach ($categories as $cat): ?>
                    <button onclick="filterCategory('<?= $cat['id'] ?>')" class="category-pill px-3 py-1.5 rounded-xl font-medium bg-slate-100 hover:bg-slate-200/70 border border-slate-200/60 text-slate-700 transition flex-shrink-0 text-xs" data-cat="<?= $cat['id'] ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Product Grid (Padded bottom on mobile for floating cart and bottom bar) -->
        <div class="flex-1 p-2.5 sm:p-4 overflow-y-auto pb-36 sm:pb-6">
            <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-3.5">
                <!-- Populated by JavaScript with product cards and photos -->
            </div>
            
            <div id="empty-product-state" class="hidden py-16 text-center text-slate-400">
                <i data-lucide="package-x" class="w-12 h-12 mx-auto text-slate-300 mb-2"></i>
                <div class="font-bold text-sm text-slate-600">Produk tidak ditemukan</div>
                <div class="text-xs">Coba kata kunci lain atau tambahkan produk baru</div>
            </div>
        </div>
    </div>

    <!-- Backdrop for Mobile Bottom Sheet Drawer -->
    <div id="cart-bottomsheet-backdrop" onclick="closeCartBottomSheet()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 hidden opacity-0 transition-opacity duration-300 lg:hidden"></div>

    <!-- Right Column on Desktop / Clean Slide-Up Bottom Sheet on Mobile -->
    <div id="pos-cart-section" class="fixed inset-x-0 bottom-0 z-50 max-h-[88vh] rounded-t-3xl shadow-2xl border-t border-slate-200 bg-white flex flex-col transition-transform duration-300 ease-out transform translate-y-full lg:relative lg:inset-auto lg:bottom-auto lg:z-0 lg:max-h-none lg:w-[420px] lg:rounded-2xl lg:shadow-sm lg:border lg:border-slate-200 lg:translate-y-0 lg:flex overflow-hidden">
        
        <!-- Mobile Bottom Sheet Drag Handle Bar -->
        <div class="lg:hidden pt-2.5 pb-1 flex flex-col items-center cursor-pointer select-none active:opacity-70" onclick="closeCartBottomSheet()" id="cart-bottomsheet-handle">
            <div class="w-12 h-1.5 bg-slate-300 rounded-full hover:bg-slate-400 transition"></div>
        </div>

        <!-- ================= STEP 1: CART ITEMS VIEW ================= -->
        <div id="cart-step-items" class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-white">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-slate-800">Keranjang Belanja</h3>
                        <span id="cart-item-count" class="text-xs text-slate-400">0 item</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="clearCart()" class="text-xs text-slate-400 hover:text-red-600 font-medium py-1 px-2 rounded-lg transition" title="Kosongkan keranjang">
                        Reset
                    </button>
                    <button type="button" onclick="closeCartBottomSheet()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition lg:hidden" title="Tutup Keranjang">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Customer Bar (Simple & Unobtrusive) -->
            <div class="px-4 py-2 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between text-xs">
                <div class="flex items-center space-x-2 flex-1 mr-2">
                    <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                    <input type="text" id="pos-customer-name" value="Pelanggan Umum" placeholder="Nama Pelanggan" class="w-full bg-transparent border-none text-slate-700 font-semibold focus:outline-none focus:ring-0 text-xs">
                </div>
                <button type="button" onclick="toggleCustomerExtra()" class="text-emerald-700 hover:text-emerald-800 font-medium text-[11px]">
                    + Info/WA
                </button>
            </div>

            <!-- Collapsible Customer Extra (Phone, Notes) -->
            <div id="customer-extra-box" class="hidden px-4 py-2.5 bg-slate-100/80 border-b border-slate-200/80 space-y-2 text-xs">
                <div class="flex items-center gap-2">
                    <input type="text" id="pos-customer-phone" placeholder="No. WhatsApp (struk via WA)" class="flex-1 px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs">
                    <input type="date" id="pos-due-date" class="px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs" title="Jatuh tempo kasbon">
                </div>
                <input type="text" id="pos-notes" placeholder="Catatan transaksi..." class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs">
            </div>

            <!-- Scrollable Items List (Generous Space) -->
            <div class="flex-1 overflow-y-auto p-3.5 space-y-2.5 min-h-[180px] max-h-[50vh] lg:max-h-none">
                <!-- Empty Message -->
                <div id="cart-empty-message" class="h-full flex flex-col items-center justify-center text-slate-400 text-center py-12">
                    <i data-lucide="shopping-cart" class="w-12 h-12 stroke-1 mb-2 text-slate-300"></i>
                    <div class="font-bold text-sm text-slate-600">Keranjang Masih Kosong</div>
                    <div class="text-xs text-slate-400 max-w-[200px] mt-1">Pilih barang di katalog atau scan barcode</div>
                </div>

                <!-- Items Container -->
                <div id="cart-items-container" class="space-y-2.5"></div>
            </div>

            <!-- Bottom Summary & Proceed Button -->
            <div class="border-t border-slate-100 bg-white p-4 space-y-3 shadow-[0_-4px_20px_rgba(0,0,0,0.03)]">
                <!-- Subtotal & Discount toggle -->
                <div class="space-y-1 text-xs text-slate-600">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Subtotal:</span>
                        <span id="cart-subtotal" class="font-semibold text-slate-700">Rp 0</span>
                    </div>
                    
                    <div id="discount-row" class="hidden flex justify-between items-center py-0.5">
                        <span class="text-slate-500">Diskon:</span>
                        <div class="flex items-center space-x-1">
                            <span class="text-slate-400">Rp</span>
                            <input type="number" id="pos-discount" value="0" min="0" oninput="calculateTotals()" class="w-24 text-right py-0.5 px-1.5 bg-slate-50 border border-slate-200 rounded text-xs font-semibold focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-1.5 border-t border-slate-100">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-slate-800 text-sm">TOTAL:</span>
                            <button type="button" onclick="toggleDiscountField()" id="btn-toggle-discount" class="text-[11px] text-emerald-700 hover:underline font-medium">
                                + Diskon
                            </button>
                        </div>
                        <span id="cart-grand-total" class="font-black text-xl text-emerald-700">Rp 0</span>
                    </div>
                </div>

                <!-- Proceed to Payment Button -->
                <button type="button" onclick="goToPaymentStep()" class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold text-sm transition-all duration-200 flex items-center justify-between shadow-lg shadow-emerald-600/25 active:scale-[0.98]">
                    <span class="text-emerald-100 text-xs font-medium">Lanjut Pembayaran</span>
                    <span id="btn-proceed-total" class="font-extrabold text-sm tracking-wide">Rp 0</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 text-emerald-200"></i>
                </button>
            </div>
        </div>

        <!-- ================= STEP 2: PAYMENT VIEW ================= -->
        <div id="cart-step-payment" class="hidden flex-1 flex flex-col overflow-hidden">
            <!-- Payment Header with Back Button -->
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <button type="button" onclick="goToItemsStep()" class="flex items-center space-x-1 text-xs font-bold text-slate-600 hover:text-emerald-700 py-1.5 px-2.5 rounded-xl hover:bg-slate-200/60 transition active-press">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-emerald-600"></i>
                    <span>Keranjang</span>
                </button>
                <h3 class="font-bold text-sm text-slate-800">Pilih Pembayaran</h3>
                <button type="button" onclick="closeCartBottomSheet()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition lg:hidden" title="Tutup">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Payment Body (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <!-- Tagihan Summary Card -->
                <div class="p-3.5 bg-gradient-to-br from-emerald-50 to-emerald-100/60 border border-emerald-200/80 rounded-2xl text-center">
                    <div class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Total Tagihan</div>
                    <div id="payment-display-total" class="text-2xl font-black text-emerald-800 tracking-tight mt-0.5">Rp 0</div>
                    <div id="payment-display-customer" class="text-xs text-emerald-700 mt-0.5 font-medium">Pelanggan Umum</div>
                </div>

                <!-- Payment Method Selection -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Metode</label>
                    <div class="grid grid-cols-4 gap-1.5 p-1 bg-slate-100 rounded-xl text-xs font-semibold">
                        <button type="button" onclick="setPaymentMethod('cash')" id="btn-pay-cash" class="py-2 rounded-lg bg-emerald-600 text-white shadow-sm transition">
                            Tunai
                        </button>
                        <button type="button" onclick="setPaymentMethod('qris')" id="btn-pay-qris" class="py-2 rounded-lg text-slate-600 hover:bg-white/60 transition">
                            QRIS
                        </button>
                        <button type="button" onclick="setPaymentMethod('transfer')" id="btn-pay-transfer" class="py-2 rounded-lg text-slate-600 hover:bg-white/60 transition">
                            Transfer
                        </button>
                        <button type="button" onclick="setPaymentMethod('kasbon')" id="btn-pay-kasbon" class="py-2 rounded-lg text-amber-700 hover:bg-amber-100 transition">
                            Kasbon
                        </button>
                    </div>
                </div>

                <!-- Cash Payment Section -->
                <div id="cash-payment-section" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Uang Tunai Diterima</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">Rp</span>
                            <input type="number" id="pos-cash-input" placeholder="0" oninput="calculateChange()" class="w-full pl-10 pr-3 py-2.5 bg-slate-50 focus:bg-white border border-slate-300 rounded-xl text-lg font-black text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                        </div>
                    </div>

                    <!-- Quick Cash Preset Pills -->
                    <div class="grid grid-cols-4 gap-1.5 text-xs font-bold">
                        <button type="button" onclick="setExactCash()" class="py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 transition active-press">Uang Pas</button>
                        <button type="button" onclick="setCashAmount(20000)" class="py-2 rounded-xl bg-slate-100 border border-slate-200 hover:bg-slate-200 text-slate-700 transition active-press">20.000</button>
                        <button type="button" onclick="setCashAmount(50000)" class="py-2 rounded-xl bg-slate-100 border border-slate-200 hover:bg-slate-200 text-slate-700 transition active-press">50.000</button>
                        <button type="button" onclick="setCashAmount(100000)" class="py-2 rounded-xl bg-slate-100 border border-slate-200 hover:bg-slate-200 text-slate-700 transition active-press">100.000</button>
                    </div>

                    <!-- Kembalian Display Box -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase">Kembalian:</span>
                        <span id="pos-change-display" class="font-extrabold text-base text-slate-800">Rp 0</span>
                    </div>
                </div>

                <!-- Kasbon Info Warning (Visible when method = kasbon) -->
                <div id="kasbon-payment-section" class="hidden p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
                    <div class="font-bold flex items-center space-x-1.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600"></i>
                        <span>Transaksi Kasbon / Utang</span>
                    </div>
                    <p class="text-[11px] text-amber-800 leading-tight">
                        Tagihan akan langsung dicatat ke <span class="font-semibold">Buku Kasbon</span> atas nama <span id="kasbon-target-name" class="font-semibold underline">Pelanggan Umum</span>.
                    </p>
                </div>
            </div>

            <!-- Confirm / Final Checkout Button -->
            <div class="p-4 border-t border-slate-100 bg-white">
                <button type="button" id="btn-checkout" onclick="processCheckout()" class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold text-sm transition-all flex items-center justify-center space-x-2 shadow-lg shadow-emerald-600/25 active:scale-[0.98]">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <span id="btn-checkout-label">SELESAIKAN PEMBAYARAN</span>
                </button>
            </div>
        </div>

    </div>
    </div>
</div>

<!-- Floating Cart Button for Mobile (Floating Action Button in Bottom Right Corner) -->
<div id="mobile-floating-cart" class="lg:hidden fixed bottom-20 right-4 z-40 hidden transition-all duration-300 ease-out">
    <button type="button" onclick="openCartBottomSheet()" class="group flex items-center bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 active:scale-95 text-white pl-3.5 pr-4 py-2.5 rounded-full shadow-2xl shadow-emerald-950/40 border border-emerald-400/40 transition-all duration-150 gap-2.5">
        <!-- Cart Icon with Floating Badge Counter -->
        <div class="relative flex items-center justify-center">
            <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center group-hover:scale-105 transition">
                <i data-lucide="shopping-cart" class="w-5 h-5 text-white"></i>
            </div>
            <!-- Item Count Badge -->
            <span id="floating-cart-count" class="absolute -top-1.5 -right-2 bg-amber-400 text-slate-950 font-black text-[11px] min-w-[20px] h-5 px-1 rounded-full flex items-center justify-center border-2 border-emerald-700 shadow-md">
                0
            </span>
        </div>

        <!-- Total Price & Action -->
        <div class="text-left">
            <div class="text-[10px] uppercase font-bold text-emerald-100 tracking-wider leading-none">Keranjang</div>
            <div id="floating-cart-total" class="font-extrabold text-sm text-white tracking-tight leading-tight mt-0.5">
                Rp 0
            </div>
        </div>

        <!-- Chevron / Open Icon -->
        <div class="w-6 h-6 rounded-full bg-white/15 flex items-center justify-center text-white/90 group-hover:translate-x-0.5 transition">
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </div>
    </button>
</div>

<!-- Modal: Add Manual / Custom Item (e.g. Sayur, Kerupuk, Es batu) -->
<div id="custom-item-modal" class="fixed inset-0 z-50 hidden modal-backdrop-blur flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl max-w-[360px] w-full my-auto overflow-hidden border border-slate-200/90 max-h-[88vh] flex flex-col">
        <div class="bg-amber-500 px-4 py-2.5 text-white flex items-center justify-between flex-shrink-0">
            <div class="flex items-center space-x-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <h3 class="font-bold text-xs sm:text-sm">Tambah Item Non-Katalog</h3>
            </div>
            <button onclick="closeCustomItemModal()" class="text-white/80 hover:text-white p-1 rounded-lg">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="p-3.5 sm:p-4 space-y-2.5 text-xs overflow-y-auto flex-1">
            <div>
                <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Nama Barang</label>
                <input type="text" id="custom-item-name" placeholder="Contoh: Sayur Bayam / Kerupuk Kaleng" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Harga Satuan (Rp)</label>
                    <input type="number" id="custom-item-price" placeholder="5000" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Satuan</label>
                    <input type="text" id="custom-item-unit" value="pcs" placeholder="pcs / ikat / bks" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Jumlah (Qty)</label>
                <input type="number" id="custom-item-qty" value="1" min="0.1" step="any" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>
            <button type="button" onclick="addCustomItemToCart()" class="w-full py-2 bg-amber-500 text-white rounded-lg font-bold text-xs hover:bg-amber-600 transition flex items-center justify-center space-x-1.5 shadow-xs active-press">
                <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                <span>Masukkan ke Keranjang</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal: New Product Detected from Master DB when scanning in POS -->
<div id="master-pos-modal" class="fixed inset-0 z-50 hidden modal-backdrop-blur flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl max-w-[360px] w-full my-auto overflow-hidden border border-slate-200/90 max-h-[88vh] flex flex-col animate-slide-up">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-4 py-2.5 text-white flex items-center justify-between flex-shrink-0">
            <div class="flex items-center space-x-1.5">
                <i data-lucide="sparkles" class="w-4 h-4 text-amber-300"></i>
                <h3 class="font-bold text-xs sm:text-sm">Produk Dikenali Otomatis!</h3>
            </div>
            <button onclick="closeMasterPosModal()" class="text-white/80 hover:text-white p-1 rounded-lg">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="p-3.5 sm:p-4 space-y-2.5 text-xs overflow-y-auto flex-1">
            <div class="flex items-center space-x-2.5 bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
                <img id="master-pos-img" src="" class="w-10 h-10 rounded-lg object-cover bg-white border border-slate-200 flex-shrink-0 shadow-xs" onerror="this.src=''; this.style.display='none';">
                <div class="min-w-0 flex-1">
                    <span id="master-pos-cat" class="text-[9px] font-bold text-emerald-700 uppercase tracking-wider block"></span>
                    <h4 id="master-pos-name" class="font-bold text-xs text-slate-800 leading-snug line-clamp-2"></h4>
                    <span id="master-pos-barcode" class="font-mono text-[9px] text-slate-400 block mt-0.5"></span>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Harga Jual di Toko Anda (Rp) <span class="text-red-500">*</span></label>
                <input type="number" id="master-pos-sell-price" required class="w-full px-2.5 py-1.5 bg-emerald-50/60 border border-emerald-300 rounded-lg font-black text-sm text-emerald-900 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Harga Modal / Beli</label>
                    <input type="number" id="master-pos-buy-price" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Stok Awal</label>
                    <input type="number" id="master-pos-stock" value="10" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <button type="button" onclick="confirmSaveMasterToPos()" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-xs transition shadow-md shadow-emerald-600/25 active:scale-95 flex items-center justify-center space-x-1.5">
                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                <span>Simpan ke Toko & Masuk Keranjang</span>
            </button>
        </div>
    </div>
</div>

<script>
    // State
    let catalog = [];
    let cart = [];
    let activeCategory = '';
    let paymentMethod = 'cash';
    let searchQuery = '';

    document.addEventListener('DOMContentLoaded', () => {
        loadCatalog();

        // Bind Search Input
        const searchInput = document.getElementById('pos-search-input');
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.trim();
            document.getElementById('btn-clear-search').classList.toggle('hidden', !searchQuery);
            renderCatalog();
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const val = searchInput.value.trim();
                if (val) {
                    const exactBarcode = catalog.find(p => p.barcode === val);
                    if (exactBarcode) {
                        addToCart(exactBarcode);
                        clearSearch();
                        return;
                    }
                    if (/^\d{4,}$/.test(val)) {
                        handleBarcodeScan(val);
                        clearSearch();
                        return;
                    }
                    const filtered = catalog.filter(p => p.name.toLowerCase().includes(val.toLowerCase()));
                    if (filtered.length === 1) {
                        addToCart(filtered[0]);
                        clearSearch();
                        return;
                    }
                }
            }
        });

        // Global key listener for hardware USB barcode scanner guns
        // USB Barcode scanners send keystrokes rapidly and finish with Enter
        let barcodeBuffer = '';
        let lastKeyTime = Date.now();

        window.addEventListener('keydown', (e) => {
            // Ignore if active element is a normal input field (except pos-search-input)
            if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName) && document.activeElement.id !== 'pos-search-input') {
                return;
            }

            const now = Date.now();
            if (now - lastKeyTime > 150) {
                barcodeBuffer = '';
            }
            lastKeyTime = now;

            if (e.key === 'Enter') {
                if (barcodeBuffer.length >= 4) {
                    e.preventDefault();
                    handleBarcodeScan(barcodeBuffer);
                    barcodeBuffer = '';
                }
            } else if (e.key.length === 1) {
                barcodeBuffer += e.key;
            }
        });

        // Customer name change sync
        document.getElementById('pos-customer-name').addEventListener('input', (e) => {
            document.getElementById('kasbon-target-name').innerText = e.target.value.trim() || 'Pelanggan Umum';
        });
    });

    async function loadCatalog() {
        try {
            const res = await fetch(`${baseUrl}/api/pos-catalog`);
            const data = await res.json();
            if (data.success) {
                catalog = data.products;
                window.catalog = catalog;
                renderCatalog();
            }
        } catch (err) {
            console.error('Failed to load catalog:', err);
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function renderCatalog() {
        const grid = document.getElementById('product-grid');
        const empty = document.getElementById('empty-product-state');

        const filtered = catalog.filter(p => {
            const matchCat = !activeCategory || String(p.category_id) === String(activeCategory);
            const matchQ = !searchQuery || 
                p.name.toLowerCase().includes(searchQuery.toLowerCase()) || 
                (p.barcode && p.barcode.includes(searchQuery));
            return matchCat && matchQ;
        });

        if (filtered.length === 0) {
            grid.innerHTML = '';
            empty.classList.remove('hidden');
            return;
        }

        empty.classList.add('hidden');
        grid.innerHTML = filtered.map(p => {
            const isLowStock = p.stock <= p.min_stock;
            const isOutOfStock = p.stock <= 0;
            const imgUrl = p.image ? escapeHtml(p.image) : '';
            const safeName = escapeHtml(p.name);
            const safeCat = escapeHtml(p.category_name || 'Umum');
            const safeUnit = escapeHtml(p.unit || 'pcs');

            // Check if product is in cart
            const inCartItem = cart.find(it => it.id === p.id && !it.is_custom);
            const inCartQty = inCartItem ? inCartItem.qty : 0;
            const inCartBadge = inCartQty > 0 ? `
                <div class="absolute top-1.5 left-1.5 bg-emerald-600/95 text-white font-extrabold text-[10px] px-2 py-0.5 rounded-full shadow-md flex items-center space-x-1 border border-white/70 backdrop-blur pointer-events-none z-10 animate-fade-in">
                    <span>✓</span>
                    <span>${inCartQty} di keranjang</span>
                </div>
            ` : '';

            // Stock badge UI
            let stockBadge = '';
            if (isOutOfStock) {
                stockBadge = `<span class="px-2 py-0.5 rounded-full bg-red-600/95 text-white font-bold text-[10px] shadow-sm tracking-tight backdrop-blur">Habis</span>`;
            } else if (isLowStock) {
                stockBadge = `<span class="px-2 py-0.5 rounded-full bg-amber-500/95 text-white font-bold text-[10px] shadow-sm tracking-tight backdrop-blur">Sisa ${p.stock}</span>`;
            } else {
                stockBadge = `<span class="px-2 py-0.5 rounded-full bg-slate-900/75 text-white font-semibold text-[10px] shadow-sm tracking-tight backdrop-blur">${p.stock} ${safeUnit}</span>`;
            }

            return `
                <div onclick="addToCartById(${p.id})" class="p-2.5 sm:p-3 bg-white rounded-2xl border ${inCartQty > 0 ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/15' : 'border-slate-200/90'} hover:border-emerald-500 hover:shadow-lg transition-all duration-200 cursor-pointer flex flex-col justify-between group active-press relative overflow-hidden select-none">
                    
                    <div>
                        <!-- Product Image Box with Fallback -->
                        <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden bg-slate-100 mb-2 border border-slate-100/90">
                            ${imgUrl ? `
                                <img src="${imgUrl}" alt="${safeName}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'w-full h-full flex flex-col items-center justify-center bg-emerald-50/80 text-emerald-700 text-xs font-bold\\'><span class=\\'text-2xl mb-0.5\\'>📦</span><span>${safeCat}</span></div>';">
                            ` : `
                                <div class="w-full h-full flex flex-col items-center justify-center bg-emerald-50/80 text-emerald-700 text-xs font-bold">
                                    <span class="text-2xl mb-0.5">📦</span>
                                    <span>${safeCat}</span>
                                </div>
                            `}
                            
                            <!-- Floating In-Cart Badge -->
                            ${inCartBadge}

                            <!-- Floating Stock Badge -->
                            <div class="absolute top-1.5 right-1.5 pointer-events-none">
                                ${stockBadge}
                            </div>
                        </div>

                        <!-- Category Tag -->
                        <div class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider truncate mb-0.5">
                            ${safeCat}
                        </div>

                        <!-- Product Title -->
                        <h4 class="font-bold text-xs text-slate-800 line-clamp-2 group-hover:text-emerald-700 transition leading-snug min-h-[32px]">
                            ${safeName}
                        </h4>
                    </div>

                    <!-- Price & Add Button -->
                    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between">
                        <div class="min-w-0 pr-1">
                            <div class="text-xs sm:text-sm font-extrabold text-slate-900 truncate">
                                Rp ${Number(p.sell_price).toLocaleString('id-ID')}
                            </div>
                            <div class="text-[10px] text-slate-400 truncate">
                                /${safeUnit}
                            </div>
                        </div>
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl ${inCartQty > 0 ? 'bg-emerald-600 text-white' : 'bg-emerald-50 group-hover:bg-emerald-600 group-hover:text-white text-emerald-700'} flex items-center justify-center transition shadow-sm active:scale-90 flex-shrink-0">
                            <i data-lucide="${inCartQty > 0 ? 'check' : 'plus'}" class="w-4 h-4"></i>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        lucide.createIcons();
    }

    function filterCategory(catId) {
        activeCategory = catId;
        document.querySelectorAll('.category-pill').forEach(btn => {
            if (btn.getAttribute('data-cat') === String(catId)) {
                btn.className = 'category-pill active-cat px-3 py-1.5 rounded-xl font-bold bg-emerald-600 text-white transition flex-shrink-0 shadow-xs text-xs';
            } else {
                btn.className = 'category-pill px-3 py-1.5 rounded-xl font-medium bg-slate-100 hover:bg-slate-200/70 border border-slate-200/60 text-slate-700 transition flex-shrink-0 text-xs';
            }
        });
        renderCatalog();
    }

    function clearSearch() {
        document.getElementById('pos-search-input').value = '';
        searchQuery = '';
        document.getElementById('btn-clear-search').classList.add('hidden');
        renderCatalog();
    }

    function openCameraScannerForPos() {
        if (!window.cameraScanner) return;
        window.cameraScanner.open({
            title: 'Scan Barcode Kamera (Kasir POS)',
            continuous: true,
            showCartInfo: true,
            onScan: (code) => {
                handleBarcodeScan(code);
            }
        });
    }

    async function handleBarcodeScan(code) {
        if (!code) return;
        
        // Find locally first
        const local = catalog.find(p => p.barcode === code);
        if (local) {
            addToCart(local);
            if (window.cameraScanner && window.cameraScanner.isScanning) {
                window.cameraScanner.showFeedback(code);
                window.cameraScanner.updateCartStatus();
            }
            return;
        }

        // Try API scan
        try {
            const res = await fetch(`${baseUrl}/api/pos-barcode?barcode=${encodeURIComponent(code)}`);
            const data = await res.json();
            if (data.success && data.product) {
                addToCart(data.product);
                if (window.cameraScanner && window.cameraScanner.isScanning) {
                    window.cameraScanner.showFeedback(code);
                    window.cameraScanner.updateCartStatus();
                }
            } else {
                // If not yet in store, check Master Sembako Database!
                try {
                    const masterRes = await fetch(`${baseUrl}/api/master-lookup?barcode=${encodeURIComponent(code)}`);
                    const masterData = await masterRes.json();
                    if (masterData.success && masterData.found && masterData.product && !masterData.already_in_store) {
                        promptAddMasterProductToPos(masterData.product);
                        return;
                    }
                } catch (e) {}

                AppAudio.error();
                if (window.cameraScanner && window.cameraScanner.isScanning) {
                    alert(`Barcode [${code}] belum terdaftar dalam katalog produk.`);
                } else {
                    alert(`Barcode [${code}] tidak ditemukan dalam katalog produk.`);
                }
            }
        } catch (err) {
            AppAudio.error();
            console.error('Scan error:', err);
        }
    }

    let pendingMasterProduct = null;

    function promptAddMasterProductToPos(product) {
        pendingMasterProduct = product;
        
        document.getElementById('master-pos-barcode').innerText = 'Barcode: ' + product.barcode;
        document.getElementById('master-pos-name').innerText = product.name;
        document.getElementById('master-pos-cat').innerText = product.category_name || 'Umum';
        document.getElementById('master-pos-sell-price').value = product.sell_price || '';
        document.getElementById('master-pos-buy-price').value = product.buy_price || '';
        document.getElementById('master-pos-stock').value = product.stock || 10;
        
        const imgEl = document.getElementById('master-pos-img');
        if (product.image) {
            imgEl.src = product.image;
            imgEl.classList.remove('hidden');
        } else {
            imgEl.classList.add('hidden');
        }

        document.getElementById('master-pos-modal').classList.remove('hidden');
        lucide.createIcons();

        setTimeout(() => {
            const sellInput = document.getElementById('master-pos-sell-price');
            if (sellInput) {
                sellInput.focus();
                sellInput.select();
            }
        }, 200);

        if (window.AppAudio && typeof window.AppAudio.beep === 'function') {
            window.AppAudio.beep();
        }
    }

    function closeMasterPosModal() {
        document.getElementById('master-pos-modal').classList.add('hidden');
        pendingMasterProduct = null;
    }

    async function confirmSaveMasterToPos() {
        if (!pendingMasterProduct) return;

        const sellPrice = parseFloat(document.getElementById('master-pos-sell-price').value);
        if (!sellPrice || sellPrice <= 0) {
            alert('Silakan masukkan harga jual yang valid');
            return;
        }

        const buyPrice = parseFloat(document.getElementById('master-pos-buy-price').value) || 0;
        const stock = parseFloat(document.getElementById('master-pos-stock').value) || 10;

        const payload = {
            barcode: pendingMasterProduct.barcode,
            name: pendingMasterProduct.name,
            category_id: pendingMasterProduct.category_id,
            unit: pendingMasterProduct.unit || 'pcs',
            buy_price: buyPrice,
            sell_price: sellPrice,
            stock: stock,
            min_stock: 5,
            image: pendingMasterProduct.image || ''
        };

        try {
            const res = await fetch(`${baseUrl}/api/products`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                closeMasterPosModal();
                
                // Immediately add to cart!
                payload.id = data.id;
                addToCart(payload);

                // Reload catalog silently in background
                loadCatalog();

                if (window.cameraScanner && window.cameraScanner.isScanning) {
                    window.cameraScanner.showFeedback(payload.barcode);
                    window.cameraScanner.updateCartStatus();
                }
            } else {
                alert('Gagal menyimpan ke toko: ' + data.message);
            }
        } catch (err) {
            alert('Terjadi kesalahan: ' + err.message);
        }
    }

    function addToCartById(id) {
        const prod = catalog.find(p => p.id === id);
        if (prod) {
            addToCart(prod);
        }
    }

    function addToCart(prod) {
        const existing = cart.find(it => it.id === prod.id && !it.is_custom);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({
                id: prod.id,
                name: prod.name,
                image: prod.image || '',
                sell_price: Number(prod.sell_price),
                buy_price: Number(prod.buy_price || 0),
                unit: prod.unit || 'pcs',
                qty: 1,
                is_custom: false
            });
        }
        window.cart = cart;
        AppAudio.beep(1000, 0.05);
        renderCart();
        renderCatalog();
    }

    function updateCartQty(index, change) {
        if (!cart[index]) return;
        cart[index].qty = Math.max(0.1, Number((cart[index].qty + change).toFixed(2)));
        window.cart = cart;
        renderCart();
        renderCatalog();
    }

    function setCartQtyDirect(index, value) {
        const val = parseFloat(value);
        if (isNaN(val) || val <= 0) {
            removeFromCart(index);
        } else {
            cart[index].qty = val;
            window.cart = cart;
            renderCart();
            renderCatalog();
        }
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        window.cart = cart;
        AppAudio.beep(400, 0.05);
        renderCart();
        renderCatalog();
    }

    async function clearCart() {
        if (cart.length === 0) return;
        const ok = await showConfirm('Yakin ingin mengosongkan semua barang di keranjang belanja?', 'Kosongkan Keranjang', 'Ya, Kosongkan', 'Batal');
        if (ok) {
            cart = [];
            window.cart = cart;
            renderCart();
            renderCatalog();
            showToast('Keranjang telah dikosongkan', 'info');
        }
    }

    function renderCart() {
        const container = document.getElementById('cart-items-container');
        const countBadge = document.getElementById('cart-item-count');
        const emptyMsg = document.getElementById('cart-empty-message');

        const totalPieces = cart.reduce((sum, it) => sum + Number(it.qty || 0), 0);
        const totalLines = cart.length;
        if (countBadge) {
            countBadge.innerText = totalLines > 0 ? `${totalPieces} item (${totalLines} jenis)` : '0 item';
        }

        if (cart.length === 0) {
            if (emptyMsg) emptyMsg.classList.remove('hidden');
            if (container) container.innerHTML = '';
            calculateTotals();
            goToItemsStep();
            return;
        }

        if (emptyMsg) emptyMsg.classList.add('hidden');
        if (container) {
            container.innerHTML = cart.map((it, idx) => {
                const subtotal = it.qty * it.sell_price;
                const safeName = escapeHtml(it.name);
                const imgUrl = it.image ? escapeHtml(it.image) : '';

                return `
                    <div class="p-3 rounded-2xl border border-slate-100 bg-slate-50/70 hover:bg-slate-100/80 transition flex items-center justify-between gap-3">
                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-white border border-slate-200/80 flex-shrink-0 flex items-center justify-center shadow-xs">
                            ${imgUrl ? `
                                <img src="${imgUrl}" alt="${safeName}" class="w-full h-full object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='📦';">
                            ` : `<span class="text-lg">📦</span>`}
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-xs text-slate-800 truncate">${safeName}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                Rp ${Number(it.sell_price).toLocaleString('id-ID')} / ${escapeHtml(it.unit)}
                            </div>
                        </div>

                        <!-- Stepper controls -->
                        <div class="flex items-center space-x-1 bg-white p-1 rounded-xl border border-slate-200/80 shadow-xs flex-shrink-0">
                            <button type="button" onclick="updateCartQty(${idx}, -1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition active-press text-xs">
                                -
                            </button>
                            <input type="number" step="any" value="${it.qty}" onchange="setCartQtyDirect(${idx}, this.value)" class="w-9 text-center py-0.5 bg-transparent border-none text-xs font-bold text-slate-800 focus:outline-none">
                            <button type="button" onclick="updateCartQty(${idx}, 1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition active-press text-xs">
                                +
                            </button>
                        </div>

                        <!-- Subtotal & Trash Icon -->
                        <div class="text-right flex items-center space-x-2 flex-shrink-0 min-w-[75px] justify-end">
                            <div class="font-extrabold text-xs text-slate-900 whitespace-nowrap">
                                Rp ${Number(subtotal).toLocaleString('id-ID')}
                            </div>
                            <button type="button" onclick="removeFromCart(${idx})" class="p-1 text-slate-300 hover:text-red-600 transition" title="Hapus">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
            lucide.createIcons();
        }

        calculateTotals();
    }

    let isCartBottomSheetOpen = false;

    function openCartBottomSheet() {
        const sheet = document.getElementById('pos-cart-section');
        const backdrop = document.getElementById('cart-bottomsheet-backdrop');
        if (!sheet) return;

        isCartBottomSheetOpen = true;
        goToItemsStep();

        if (backdrop) {
            backdrop.classList.remove('hidden');
            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
            });
        }

        sheet.classList.remove('translate-y-full');
        sheet.classList.add('translate-y-0');
        lucide.createIcons();
    }

    function closeCartBottomSheet() {
        const sheet = document.getElementById('pos-cart-section');
        const backdrop = document.getElementById('cart-bottomsheet-backdrop');
        if (!sheet) return;

        isCartBottomSheetOpen = false;

        sheet.classList.remove('translate-y-0');
        sheet.classList.add('translate-y-full');

        if (backdrop) {
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            setTimeout(() => {
                if (!isCartBottomSheetOpen) {
                    backdrop.classList.add('hidden');
                }
            }, 300);
        }
    }

    function goToPaymentStep() {
        if (cart.length === 0) {
            showWarning('Keranjang belanja masih kosong! Silakan pilih produk terlebih dahulu.', 'Keranjang Kosong');
            return;
        }
        document.getElementById('cart-step-items')?.classList.add('hidden');
        document.getElementById('cart-step-payment')?.classList.remove('hidden');

        // Sync customer name display
        const custName = document.getElementById('pos-customer-name')?.value.trim() || 'Pelanggan Umum';
        const displayCust = document.getElementById('payment-display-customer');
        if (displayCust) displayCust.innerText = custName;

        // Auto-set exact cash if method is cash
        const subtotal = cart.reduce((sum, it) => sum + (it.qty * it.sell_price), 0);
        const discount = parseFloat(document.getElementById('pos-discount')?.value) || 0;
        const grandTotal = Math.max(0, subtotal - discount);
        const cashInput = document.getElementById('pos-cash-input');
        if (cashInput && (!cashInput.value || parseFloat(cashInput.value) === 0)) {
            cashInput.value = grandTotal;
        }
        calculateChange();
        lucide.createIcons();
    }

    function goToItemsStep() {
        document.getElementById('cart-step-payment')?.classList.add('hidden');
        document.getElementById('cart-step-items')?.classList.remove('hidden');
        lucide.createIcons();
    }

    function toggleDiscountField() {
        const row = document.getElementById('discount-row');
        const btn = document.getElementById('btn-toggle-discount');
        if (!row || !btn) return;
        if (row.classList.contains('hidden')) {
            row.classList.remove('hidden');
            btn.innerText = '- Tutup';
            document.getElementById('pos-discount')?.focus();
        } else {
            row.classList.add('hidden');
            btn.innerText = '+ Diskon';
            const discInput = document.getElementById('pos-discount');
            if (discInput) discInput.value = '0';
            calculateTotals();
        }
    }

    function setCashAmount(amt) {
        const input = document.getElementById('pos-cash-input');
        if (input) input.value = amt;
        calculateChange();
    }

    // Backwards-compatible alias
    function switchMobilePosTab(tab) {
        if (tab === 'cart') {
            openCartBottomSheet();
        } else {
            closeCartBottomSheet();
        }
    }

    function updateFloatingCart() {
        const floatCart = document.getElementById('mobile-floating-cart');
        const totalPieces = cart.reduce((sum, it) => sum + Number(it.qty || 0), 0);
        const subtotal = cart.reduce((sum, it) => sum + (it.qty * it.sell_price), 0);
        const discount = parseFloat(document.getElementById('pos-discount')?.value) || 0;
        const grandTotal = Math.max(0, subtotal - discount);

        if (floatCart) {
            if (totalPieces > 0) {
                const countEl = document.getElementById('floating-cart-count');
                const totalEl = document.getElementById('floating-cart-total');
                if (countEl) countEl.innerText = totalPieces;
                if (totalEl) totalEl.innerText = 'Rp ' + Number(grandTotal).toLocaleString('id-ID');
                floatCart.classList.remove('hidden');

                // Animate pop
                floatCart.classList.add('scale-105');
                setTimeout(() => floatCart.classList.remove('scale-105'), 180);
            } else {
                floatCart.classList.add('hidden');
                closeCartBottomSheet();
            }
        }
    }

    // Touch gesture: Pull/drag down to close bottom sheet on mobile
    document.addEventListener('DOMContentLoaded', () => {
        const sheet = document.getElementById('pos-cart-section');
        let touchStartY = 0;

        if (sheet) {
            sheet.addEventListener('touchstart', (e) => {
                touchStartY = e.touches[0].clientY;
            }, { passive: true });

            sheet.addEventListener('touchmove', (e) => {
                if (!isCartBottomSheetOpen || window.innerWidth >= 1024) return;
                const touchCurrentY = e.touches[0].clientY;
                const deltaY = touchCurrentY - touchStartY;
                const scrollable = sheet.querySelector('.overflow-y-auto');
                const isAtTop = !scrollable || scrollable.scrollTop <= 0;

                // Dragged down > 65px when list is scrolled to top
                if (deltaY > 65 && isAtTop) {
                    closeCartBottomSheet();
                }
            }, { passive: true });
        }

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isCartBottomSheetOpen) {
                closeCartBottomSheet();
            }
        });
    });

    function calculateTotals() {
        const subtotal = cart.reduce((sum, it) => sum + (it.qty * it.sell_price), 0);
        const discountInput = document.getElementById('pos-discount');
        let discount = parseFloat(discountInput ? discountInput.value : 0) || 0;
        if (discount > subtotal) discount = subtotal;

        const grandTotal = Math.max(0, subtotal - discount);
        const formattedTotal = 'Rp ' + Number(grandTotal).toLocaleString('id-ID');

        const elSubtotal = document.getElementById('cart-subtotal');
        const elGrandTotal = document.getElementById('cart-grand-total');
        const elBtnProceed = document.getElementById('btn-proceed-total');
        const elPaymentTotal = document.getElementById('payment-display-total');
        const elBtnCheckout = document.getElementById('btn-checkout-label');

        if (elSubtotal) elSubtotal.innerText = 'Rp ' + Number(subtotal).toLocaleString('id-ID');
        if (elGrandTotal) elGrandTotal.innerText = formattedTotal;
        if (elBtnProceed) elBtnProceed.innerText = formattedTotal;
        if (elPaymentTotal) elPaymentTotal.innerText = formattedTotal;
        if (elBtnCheckout) elBtnCheckout.innerText = grandTotal > 0 ? `SELESAIKAN PEMBAYARAN (${formattedTotal})` : 'SELESAIKAN PEMBAYARAN';

        calculateChange();
        updateFloatingCart();
    }

    function setPaymentMethod(method) {
        paymentMethod = method;
        const btnCash = document.getElementById('btn-pay-cash');
        const btnQris = document.getElementById('btn-pay-qris');
        const btnTrf = document.getElementById('btn-pay-transfer');
        const btnKasbon = document.getElementById('btn-pay-kasbon');

        const activeCls = 'py-1.5 rounded-lg bg-emerald-600 text-white shadow-sm transition';
        const inactiveCls = 'py-1.5 rounded-lg text-slate-700 hover:bg-white/60 transition';

        btnCash.className = method === 'cash' ? activeCls : inactiveCls;
        btnQris.className = method === 'qris' ? activeCls : inactiveCls;
        btnTrf.className = method === 'transfer' ? activeCls : inactiveCls;
        btnKasbon.className = method === 'kasbon' ? 'py-1.5 rounded-lg bg-amber-600 text-white shadow-sm transition' : 'py-1.5 rounded-lg text-amber-700 hover:bg-amber-100 transition';

        document.getElementById('cash-payment-section').classList.toggle('hidden', method !== 'cash');
        document.getElementById('kasbon-payment-section').classList.toggle('hidden', method !== 'kasbon');

        calculateChange();
    }

    function calculateChange() {
        if (paymentMethod !== 'cash') return;

        const subtotal = cart.reduce((sum, it) => sum + (it.qty * it.sell_price), 0);
        const discount = parseFloat(document.getElementById('pos-discount').value) || 0;
        const grandTotal = Math.max(0, subtotal - discount);

        const cashInput = document.getElementById('pos-cash-input');
        const cash = parseFloat(cashInput.value) || 0;
        const change = Math.max(0, cash - grandTotal);

        const changeDisplay = document.getElementById('pos-change-display');
        changeDisplay.innerText = 'Rp ' + Number(change).toLocaleString('id-ID');

        if (cash >= grandTotal && grandTotal > 0) {
            changeDisplay.className = 'font-bold text-sm text-emerald-700';
        } else {
            changeDisplay.className = 'font-bold text-sm text-slate-800';
        }
    }

    function setExactCash() {
        const subtotal = cart.reduce((sum, it) => sum + (it.qty * it.sell_price), 0);
        const discount = parseFloat(document.getElementById('pos-discount').value) || 0;
        const grandTotal = Math.max(0, subtotal - discount);

        document.getElementById('pos-cash-input').value = grandTotal;
        calculateChange();
    }

    function addCash(amount) {
        const current = parseFloat(document.getElementById('pos-cash-input').value) || 0;
        document.getElementById('pos-cash-input').value = current + amount;
        calculateChange();
    }

    function toggleCustomerExtra() {
        const box = document.getElementById('customer-extra-box');
        box.classList.toggle('hidden');
    }

    // Custom Item Modal
    function openCustomItemModal() {
        document.getElementById('custom-item-modal').classList.remove('hidden');
        document.getElementById('custom-item-name').focus();
    }

    function closeCustomItemModal() {
        document.getElementById('custom-item-modal').classList.add('hidden');
    }

    function addCustomItemToCart() {
        const name = document.getElementById('custom-item-name').value.trim();
        const price = parseFloat(document.getElementById('custom-item-price').value) || 0;
        const unit = document.getElementById('custom-item-unit').value.trim() || 'pcs';
        const qty = parseFloat(document.getElementById('custom-item-qty').value) || 1;

        if (!name) {
            showWarning('Harap masukkan nama barang terlebih dahulu!', 'Nama Barang Kosong');
            return;
        }
        if (price <= 0) {
            showWarning('Harap masukkan nominal harga yang valid!', 'Harga Belum Diisi');
            return;
        }

        cart.push({
            id: 'custom_' + Date.now() + '_' + Math.random().toString(36).substr(2, 4),
            name: name,
            sell_price: price,
            buy_price: 0,
            unit: unit,
            qty: qty,
            is_custom: true
        });

        window.cart = cart;
        document.getElementById('custom-item-name').value = '';
        document.getElementById('custom-item-price').value = '';
        closeCustomItemModal();
        AppAudio.beep(1000, 0.05);
        showToast(`Item "${name}" ditambahkan`);
        renderCart();
        renderCatalog();
    }

    // Process Checkout
    async function processCheckout() {
        if (cart.length === 0) {
            showWarning('Keranjang belanja masih kosong! Silakan pilih produk terlebih dahulu.', 'Keranjang Kosong');
            return;
        }

        const subtotal = cart.reduce((sum, it) => sum + (it.qty * it.sell_price), 0);
        const discount = parseFloat(document.getElementById('pos-discount').value) || 0;
        const grandTotal = Math.max(0, subtotal - discount);

        const customerName = document.getElementById('pos-customer-name').value.trim() || 'Pelanggan Umum';
        const customerPhone = document.getElementById('pos-customer-phone').value.trim();
        const dueDate = document.getElementById('pos-due-date').value;
        const notes = document.getElementById('pos-notes').value.trim();

        let cashAmount = 0;
        if (paymentMethod === 'cash') {
            cashAmount = parseFloat(document.getElementById('pos-cash-input').value) || 0;
            if (cashAmount < grandTotal) {
                showWarning(`Nominal uang tunai (Rp ${Number(cashAmount).toLocaleString('id-ID')}) kurang dari total tagihan (Rp ${Number(grandTotal).toLocaleString('id-ID')})!`, 'Uang Tunai Kurang');
                document.getElementById('pos-cash-input').focus();
                return;
            }
        } else if (paymentMethod === 'kasbon') {
            if (customerName === 'Pelanggan Umum' || customerName === '') {
                const { value: namePrompt } = await WarungSwal.fire({
                    title: 'Catatan Kasbon',
                    text: 'Masukkan nama pelanggan yang berutang:',
                    input: 'text',
                    inputPlaceholder: 'Contoh: Bu Siti / Mas Joko',
                    showCancelButton: true,
                    confirmButtonText: 'Lanjutkan',
                    cancelButtonText: 'Batal',
                    inputValidator: (value) => {
                        if (!value || !value.trim()) {
                            return 'Nama pelanggan wajib diisi untuk transaksi kasbon!';
                        }
                    }
                });
                if (!namePrompt) {
                    return;
                }
                document.getElementById('pos-customer-name').value = namePrompt.trim();
            }
        }

        const btnCheckout = document.getElementById('btn-checkout');
        btnCheckout.disabled = true;
        btnCheckout.innerHTML = `<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Memproses...`;

        const payload = {
            items: cart,
            customer_name: document.getElementById('pos-customer-name').value.trim(),
            customer_phone: customerPhone,
            payment_method: paymentMethod,
            discount_amount: discount,
            cash_amount: cashAmount,
            notes: notes,
            due_date: dueDate
        };

        try {
            const res = await fetch(`${baseUrl}/api/pos-checkout`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (data.success) {
                // Confetti celebration
                try {
                    confetti({ particleCount: 50, spread: 60, origin: { y: 0.8 } });
                } catch(e) {}

                AppAudio.success();

                // Auto-print receipt if printer connected or auto_print enabled
                const status = window.printerManager.getStatus();
                if (data.receipt.store.auto_print && (status.isConnected || status.connectionType === 'system')) {
                    try {
                        await window.printerManager.printReceipt(data.receipt);
                    } catch (e) {
                        console.warn('Auto print failed:', e);
                    }
                }

                // Show receipt preview modal
                showReceiptModal(data.receipt);

                // Reset cart and inputs
                cart = [];
                window.cart = cart;
                renderCart();
                renderCatalog();
                switchMobilePosTab('catalog');
                document.getElementById('pos-discount').value = '0';
                document.getElementById('pos-cash-input').value = '';
                document.getElementById('pos-notes').value = '';
                document.getElementById('pos-customer-name').value = 'Pelanggan Umum';

                // Reload catalog to refresh stock counts
                loadCatalog();
            } else {
                AppAudio.error();
                alert('Gagal transaksi: ' + (data.message || 'Terjadi kesalahan'));
            }
        } catch (err) {
            AppAudio.error();
            alert('Kesalahan jaringan: ' + err.message);
        } finally {
            btnCheckout.disabled = false;
            calculateTotals();
        }
    }

    // Enable smooth horizontal scrolling with mouse wheel on category pills
    document.addEventListener('DOMContentLoaded', () => {
        const categoryPillsEl = document.getElementById('category-pills');
        if (categoryPillsEl) {
            categoryPillsEl.addEventListener('wheel', (e) => {
                if (e.deltaY !== 0) {
                    e.preventDefault();
                    categoryPillsEl.scrollLeft += e.deltaY;
                }
            }, { passive: false });
        }
    });
</script>
