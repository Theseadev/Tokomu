<div class="max-w-7xl w-full mx-auto p-3 sm:p-6 space-y-4">
    <!-- Total Unpaid Debt Summary Card -->
    <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white p-4 sm:p-5 rounded-2xl shadow-sm flex items-center justify-between">
        <div>
            <div class="flex items-center gap-1.5 text-xs font-bold text-amber-100 uppercase tracking-wider">
                <i data-lucide="alert-circle" class="w-4 h-4 text-amber-200"></i>
                <span>Total Kasbon Belum Lunas</span>
            </div>
            <div id="summary-unpaid-debt" class="text-2xl sm:text-3xl font-black tracking-tight mt-1">Rp 0</div>
            <div class="text-[11px] text-amber-100 mt-0.5">Dari seluruh catatan utang pelanggan aktif &bull; Real-time</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-xs flex items-center justify-center flex-shrink-0">
            <i data-lucide="wallet" class="w-6 h-6 text-white"></i>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
        <div class="relative flex-1 w-full sm:w-auto">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" id="kasbon-search" placeholder="Cari nama atau no HP pelanggan..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div class="grid grid-cols-4 gap-1 w-full sm:w-auto sm:flex sm:gap-1.5 p-1 bg-slate-100/90 rounded-2xl border border-slate-200/60" id="kasbon-status-tabs">
            <button type="button" onclick="filterKasbon('')" class="kasbon-tab py-2 px-1 text-center rounded-xl font-bold text-[11px] sm:text-xs sm:px-3.5 bg-amber-600 text-white shadow-xs transition active:scale-95 leading-tight" data-status="">
                Semua
            </button>
            <button type="button" onclick="filterKasbon('unpaid')" class="kasbon-tab py-2 px-1 text-center rounded-xl font-bold text-[11px] sm:text-xs sm:px-3.5 text-slate-600 hover:text-slate-900 transition active:scale-95 leading-tight" data-status="unpaid">
                Belum Bayar
            </button>
            <button type="button" onclick="filterKasbon('partial')" class="kasbon-tab py-2 px-1 text-center rounded-xl font-bold text-[11px] sm:text-xs sm:px-3.5 text-slate-600 hover:text-slate-900 transition active:scale-95 leading-tight" data-status="partial">
                Dicicil
            </button>
            <button type="button" onclick="filterKasbon('paid')" class="kasbon-tab py-2 px-1 text-center rounded-xl font-bold text-[11px] sm:text-xs sm:px-3.5 text-slate-600 hover:text-slate-900 transition active:scale-95 leading-tight" data-status="paid">
                Lunas
            </button>
        </div>
    </div>

    <!-- Kasbon Responsive Container: Table on Desktop, Cards on Mobile -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Desktop Table View (Hidden on mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Tanggal & No</th>
                        <th class="py-3 px-4">Nama Pelanggan</th>
                        <th class="py-3 px-4">No HP/WA</th>
                        <th class="py-3 px-4 text-right">Total Utang</th>
                        <th class="py-3 px-4 text-right">Sudah Dibayar</th>
                        <th class="py-3 px-4 text-right">Sisa Kasbon</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi & Struk</th>
                    </tr>
                </thead>
                <tbody id="kasbon-table-body" class="divide-y divide-slate-100 font-medium">
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View with Dropdown Details (Visible on Mobile) -->
        <div id="kasbon-cards-container" class="md:hidden divide-y divide-slate-100">
            <!-- Populated by JS with interactive cards -->
        </div>

        <div id="kasbon-loading" class="py-12 text-center text-slate-400">
            <div class="animate-spin inline-block w-6 h-6 border-2 border-emerald-600 border-t-transparent rounded-full mb-2"></div>
            <div>Memuat data kasbon...</div>
        </div>
    </div>
</div>

<!-- Modal: Bayar / Cicil Kasbon -->
<div id="pay-kasbon-modal" class="fixed inset-0 z-50 hidden modal-backdrop-blur flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl max-w-[360px] w-full my-auto overflow-hidden border border-slate-200/90 max-h-[88vh] flex flex-col">
        <div class="bg-amber-600 px-4 py-2.5 text-white flex items-center justify-between flex-shrink-0">
            <h3 class="font-bold text-xs sm:text-sm flex items-center gap-1.5">
                <i data-lucide="wallet" class="w-4 h-4"></i>
                <span>Catat Pembayaran Kasbon</span>
            </h3>
            <button onclick="closePayModal()" class="text-amber-200 hover:text-white p-1 rounded-lg">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="pay-kasbon-form" onsubmit="submitPayKasbon(event)" class="p-3.5 sm:p-4 space-y-2.5 text-xs overflow-y-auto flex-1">
            <input type="hidden" id="pay-kasbon-id">
            
            <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 space-y-0.5">
                <div class="text-[10px] text-slate-500">Pelanggan:</div>
                <div id="pay-customer-name" class="font-bold text-xs text-slate-900">Bpk. Slamet</div>
                <div class="flex justify-between items-center pt-1 border-t border-amber-200/70 mt-1">
                    <span class="text-slate-600 text-[11px]">Sisa Tagihan:</span>
                    <span id="pay-remaining-amount" class="font-bold text-amber-900 text-xs">Rp 0</span>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Nominal Pembayaran (Rp) <span class="text-red-500">*</span></label>
                <input type="number" id="pay-amount-input" required min="100" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg font-bold text-sm text-slate-900 focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div>
                <button type="button" onclick="setFullPay()" class="w-full py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs text-center transition">
                    Lunasi Penuh
                </button>
            </div>

            <div>
                <label class="block font-semibold text-[11px] sm:text-xs text-slate-700 mb-0.5">Catatan Pembayaran</label>
                <input type="text" id="pay-notes-input" value="Pembayaran kasbon tunai" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div class="pt-2 flex justify-end gap-1.5 flex-shrink-0">
                <button type="button" onclick="closePayModal()" class="px-3.5 py-1.5 border border-slate-200 rounded-lg font-bold text-slate-700 text-xs hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-4 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-bold text-xs shadow-xs">Simpan & Cetak Struk</button>
            </div>
        </form>
    </div>
</div>

<script>
    let kasbonList = [];
    let activeKasbonStatus = '';

    document.addEventListener('DOMContentLoaded', () => {
        loadKasbon();
        document.getElementById('kasbon-search').addEventListener('input', debounce(loadKasbon, 300));
    });

    function debounce(fn, delay) {
        let timer = null;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    async function loadKasbon() {
        const q = document.getElementById('kasbon-search').value.trim();
        document.getElementById('kasbon-loading').classList.remove('hidden');

        try {
            const res = await fetch(`${baseUrl}/api/kasbon?status=${activeKasbonStatus}&q=${encodeURIComponent(q)}`);
            const data = await res.json();
            if (data.success) {
                kasbonList = data.kasbon;
                document.getElementById('summary-unpaid-debt').innerText = 'Rp ' + Number(data.total_unpaid || 0).toLocaleString('id-ID');
                renderKasbonTable();
            }
        } catch (err) {
            console.error('Failed to load kasbon:', err);
        } finally {
            document.getElementById('kasbon-loading').classList.add('hidden');
        }
    }

    function renderKasbonTable() {
        const tbody = document.getElementById('kasbon-table-body');
        const cardsContainer = document.getElementById('kasbon-cards-container');

        if (kasbonList.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="py-8 text-center text-slate-400">Tidak ada catatan kasbon.</td></tr>`;
            if (cardsContainer) {
                cardsContainer.innerHTML = `<div class="py-12 text-center text-slate-400">
                    <i data-lucide="book-x" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                    <div class="font-bold text-sm text-slate-600">Tidak ada data kasbon</div>
                    <div class="text-xs">Gunakan kolom cari atau ganti filter status</div>
                </div>`;
            }
            lucide.createIcons();
            return;
        }

        // 1. Render Desktop Table Rows
        tbody.innerHTML = kasbonList.map(k => {
            const isPaid = k.status === 'paid';
            const isPartial = k.status === 'partial';

            return `
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3 px-4">
                        <div class="font-bold text-slate-800">${k.invoice_no || ('KB-' + String(k.id).padStart(4, '0'))}</div>
                        <div class="text-[10px] text-slate-400">${new Date(k.created_at).toLocaleDateString('id-ID')}</div>
                    </td>
                    <td class="py-3 px-4 font-bold text-slate-800">${k.customer_name}</td>
                    <td class="py-3 px-4 text-slate-500 font-mono">${k.customer_phone || '-'}</td>
                    <td class="py-3 px-4 text-right text-slate-600 font-mono">Rp ${Number(k.total_debt).toLocaleString('id-ID')}</td>
                    <td class="py-3 px-4 text-right text-emerald-700 font-mono">Rp ${Number(k.paid_amount).toLocaleString('id-ID')}</td>
                    <td class="py-3 px-4 text-right font-bold font-mono ${isPaid ? 'text-slate-400' : 'text-rose-600 text-sm'}">
                        Rp ${Number(k.remaining_debt).toLocaleString('id-ID')}
                    </td>
                    <td class="py-3 px-4 text-center">
                        ${isPaid ? `
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-800 text-[10px]">
                                LUNAS
                            </span>` : (isPartial ? `
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold bg-blue-100 text-blue-800 text-[10px]">
                                DICICIL
                            </span>` : `
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold bg-amber-100 text-amber-800 text-[10px]">
                                BELUM BAYAR
                            </span>`)}
                    </td>
                    <td class="py-3 px-4 text-center">
                        <div class="inline-flex items-center space-x-1.5">
                            ${!isPaid ? `
                                <button onclick="openPayModal(${k.id})" class="px-2.5 py-1 bg-emerald-600 text-white hover:bg-emerald-700 rounded-lg font-bold text-[11px] transition shadow-xs flex items-center space-x-1 active:scale-95">
                                    <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                                    <span>Bayar</span>
                                </button>` : ''}
                            <button onclick="printKasbonReceipt(${k.id})" class="p-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg font-bold transition flex items-center space-x-1" title="Cetak Bukti Kasbon">
                                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                <span class="text-[11px]">Struk</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        // 2. Render Mobile Interactive Cards (Card Dropdown)
        if (cardsContainer) {
            cardsContainer.innerHTML = kasbonList.map(k => {
                const isPaid = k.status === 'paid';
                const isPartial = k.status === 'partial';
                const cleanPhone = (k.customer_phone || '').replace(/[^0-9]/g, '').replace(/^0/, '62');

                return `
                    <div class="p-4 space-y-3 bg-white hover:bg-slate-50/70 transition">
                        <!-- Header Card: Nama & Status Badge -->
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="font-extrabold text-sm text-slate-900 leading-tight">
                                    ${k.customer_name}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-1.5">
                                    <span>${k.invoice_no || ('KB-' + String(k.id).padStart(4, '0'))}</span>
                                    <span>&bull;</span>
                                    <span>${new Date(k.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                ${isPaid ? `
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-800 text-[10px]">
                                        <i data-lucide="check-circle" class="w-3 h-3"></i>
                                        <span>LUNAS</span>
                                    </span>` : (isPartial ? `
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold bg-blue-100 text-blue-800 text-[10px]">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        <span>DICICIL</span>
                                    </span>` : `
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold bg-amber-100 text-amber-800 text-[10px]">
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                        <span>BELUM BAYAR</span>
                                    </span>`)}
                            </div>
                        </div>

                        <!-- Card Body: Sisa Utang Highlight -->
                        <div class="bg-slate-50 border border-slate-200/70 rounded-2xl p-3 flex items-center justify-between">
                            <div>
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sisa Tagihan Utang</div>
                                <div class="text-base font-black ${isPaid ? 'text-slate-400' : 'text-rose-600'} font-mono">
                                    Rp ${Number(k.remaining_debt).toLocaleString('id-ID')}
                                </div>
                            </div>
                            <div class="text-right text-[11px] text-slate-500 space-y-0.5">
                                <div>Total: <span class="font-bold text-slate-700 font-mono">Rp ${Number(k.total_debt).toLocaleString('id-ID')}</span></div>
                                <div>Dibayar: <span class="font-bold text-emerald-700 font-mono">Rp ${Number(k.paid_amount).toLocaleString('id-ID')}</span></div>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="flex items-center gap-2 pt-0.5">
                            ${!isPaid ? `
                                <button onclick="openPayModal(${k.id})" class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs transition shadow-xs flex items-center justify-center space-x-1.5 active:scale-95">
                                    <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                                    <span>Bayar / Cicil</span>
                                </button>` : ''}
                            
                            <button onclick="printKasbonReceipt(${k.id})" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition flex items-center justify-center space-x-1 active:scale-95 ${isPaid ? 'flex-1' : ''}" title="Cetak Struk Kasbon">
                                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                <span>Struk</span>
                            </button>

                            ${cleanPhone ? `
                                <a href="https://wa.me/${cleanPhone}?text=${encodeURIComponent('Halo ' + k.customer_name + ', ini informasi tagihan kasbon di Tokomu sebesar Rp ' + Number(k.remaining_debt).toLocaleString('id-ID') + '. Terima kasih.')}" target="_blank" class="py-2 px-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl font-bold text-xs transition flex items-center justify-center space-x-1 active:scale-95" title="Kirim Pengingat WhatsApp">
                                    <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                    <span>WA</span>
                                </a>` : ''}

                            <!-- Dropdown Toggle Button -->
                            <button onclick="toggleCardDetail(${k.id})" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition active:scale-95" title="Lihat Rincian">
                                <i data-lucide="chevron-down" id="card-arrow-${k.id}" class="w-4 h-4 transition-transform duration-200"></i>
                            </button>
                        </div>

                        <!-- Expandable Card Dropdown Details -->
                        <div id="card-detail-${k.id}" class="hidden pt-2.5 space-y-2 text-[11px] text-slate-600 bg-slate-50/80 -mx-4 -mb-4 p-4 rounded-b-2xl border-t border-slate-100">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Jatuh Tempo:</span>
                                <span class="font-bold text-slate-700">${k.due_date ? new Date(k.due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : 'Tidak ditentukan'}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">No. WhatsApp / HP:</span>
                                <span class="font-mono font-bold text-slate-800">${k.customer_phone || 'Tidak dicatat'}</span>
                            </div>
                            ${k.notes ? `
                            <div class="pt-1.5 border-t border-slate-200/60">
                                <span class="text-slate-400 block mb-1">Catatan Tagihan:</span>
                                <div class="bg-white p-2 rounded-xl border border-slate-200/80 font-medium text-slate-700">${k.notes}</div>
                            </div>` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        }

        lucide.createIcons();
    }

    function toggleCardDetail(id) {
        const detailEl = document.getElementById('card-detail-' + id);
        const arrowEl = document.getElementById('card-arrow-' + id);
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

    function filterKasbon(status) {
        activeKasbonStatus = status;
        document.querySelectorAll('.kasbon-tab').forEach(btn => {
            if (btn.getAttribute('data-status') === status) {
                btn.className = 'kasbon-tab py-2 px-1 text-center rounded-xl font-bold text-[11px] sm:text-xs sm:px-3.5 bg-amber-600 text-white shadow-xs transition active:scale-95 leading-tight';
            } else {
                btn.className = 'kasbon-tab py-2 px-1 text-center rounded-xl font-bold text-[11px] sm:text-xs sm:px-3.5 text-slate-600 hover:text-slate-900 transition active:scale-95 leading-tight';
            }
        });
        loadKasbon();
    }

    let activePayItem = null;

    function openPayModal(id) {
        const item = kasbonList.find(k => k.id === id);
        if (!item) return;
        activePayItem = item;

        document.getElementById('pay-kasbon-id').value = item.id;
        document.getElementById('pay-customer-name').innerText = item.customer_name;
        document.getElementById('pay-remaining-amount').innerText = 'Rp ' + Number(item.remaining_debt).toLocaleString('id-ID');
        document.getElementById('pay-amount-input').value = item.remaining_debt;
        document.getElementById('pay-kasbon-modal').classList.remove('hidden');
        document.getElementById('pay-amount-input').focus();
    }

    function setFullPay() {
        if (!activePayItem) return;
        document.getElementById('pay-amount-input').value = activePayItem.remaining_debt;
    }

    function closePayModal() {
        document.getElementById('pay-kasbon-modal').classList.add('hidden');
    }

    async function submitPayKasbon(e) {
        e.preventDefault();
        const id = document.getElementById('pay-kasbon-id').value;
        const amount = parseFloat(document.getElementById('pay-amount-input').value);
        const notes = document.getElementById('pay-notes-input').value;

        try {
            const res = await fetch(`${baseUrl}/api/kasbon/pay`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, amount, notes })
            });
            const data = await res.json();
            if (data.success) {
                closePayModal();
                AppAudio.success();
                loadKasbon();

                // Print receipt proof
                printKasbonReceipt(id);
            } else {
                alert('Gagal: ' + data.message);
            }
        } catch (err) {
            alert('Terjadi kesalahan: ' + err.message);
        }
    }

    async function printKasbonReceipt(id) {
        try {
            const res = await fetch(`${baseUrl}/api/kasbon/${id}/receipt`);
            const data = await res.json();
            if (data.success && data.receipt) {
                // Show the realistic, beautiful receipt preview modal
                showReceiptModal(data.receipt);

                // If direct mini thermal printer (Bluetooth / USB) is connected, also print directly
                const status = window.printerManager.getStatus();
                if (status.isConnected && status.connectionType !== 'system') {
                    try {
                        await window.printerManager.printKasbonReceipt(data.receipt);
                        AppAudio.success();
                    } catch (e) {
                        console.warn('Direct printer failed:', e);
                    }
                }
            } else {
                alert('Gagal memuat struk: ' + (data.message || 'Data tidak ditemukan'));
            }
        } catch (err) {
            alert('Gagal mengambil data struk kasbon: ' + err.message);
        }
    }
</script>
