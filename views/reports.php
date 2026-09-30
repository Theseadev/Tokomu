<div class="max-w-7xl w-full mx-auto p-3 sm:p-6 space-y-3.5 sm:space-y-5">
    <!-- Header with Export: Sleek & Compact -->
    <div class="flex items-center justify-between gap-3 bg-white p-3.5 sm:p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center flex-shrink-0">
                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0">
                <h1 class="font-extrabold text-sm sm:text-base text-slate-900 leading-tight truncate">Laporan & Riwayat Penjualan</h1>
                <p class="text-[11px] text-slate-400 truncate">Ringkasan performa penjualan warung</p>
            </div>
        </div>
        <button onclick="downloadCsvReport()" class="px-3 sm:px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-xs active:scale-95 flex-shrink-0">
            <i data-lucide="download" class="w-3.5 h-3.5"></i>
            <span class="text-[11px] sm:text-xs">Export CSV</span>
        </button>
    </div>

    <!-- Summary KPI Cards: Balanced 2x2 Grid on Mobile, 4x1 on Desktop -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
        <!-- 1. Omzet Hari Ini -->
        <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400">Omzet Hari Ini</span>
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <div>
                <div id="stat-today-sales" class="text-base sm:text-xl font-black text-slate-900 font-mono">Rp 0</div>
                <div id="stat-today-trx" class="text-[10px] sm:text-[11px] text-slate-400 mt-0.5 truncate">0 transaksi berhasil</div>
            </div>
        </div>

        <!-- 2. Laba Hari Ini -->
        <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-emerald-700">Laba Hari Ini</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-100/80 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <div>
                <div id="stat-today-profit" class="text-base sm:text-xl font-black text-emerald-600 font-mono">+Rp 0</div>
                <div class="text-[10px] sm:text-[11px] text-emerald-700/80 mt-0.5 truncate">Keuntungan bersih</div>
            </div>
        </div>

        <!-- 3. Omzet Bulan Ini -->
        <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400">Omzet Bulan Ini</span>
                <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <div>
                <div id="stat-month-sales" class="text-base sm:text-xl font-black text-slate-900 font-mono">Rp 0</div>
                <div id="stat-month-profit" class="text-[10px] sm:text-[11px] text-indigo-600 font-semibold mt-0.5 truncate">Laba: Rp 0</div>
            </div>
        </div>

        <!-- 4. Sisa Kasbon -->
        <div class="bg-white p-3 sm:p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-amber-700">Sisa Kasbon</span>
                <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <div>
                <div id="stat-kasbon-unpaid" class="text-base sm:text-xl font-black text-amber-800 font-mono">Rp 0</div>
                <div id="stat-kasbon-count" class="text-[10px] sm:text-[11px] text-amber-700/80 mt-0.5 truncate">0 catatan kasbon</div>
            </div>
        </div>
    </div>

    <!-- Charts & Top Products Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3.5 sm:gap-4">
        <!-- 7 Days Sales Trend Chart -->
        <div class="lg:col-span-2 bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                <div>
                    <h3 class="font-extrabold text-sm text-slate-800 flex items-center gap-2">
                        <i data-lucide="trending-up" class="w-4 h-4 text-blue-600"></i>
                        <span>Tren Penjualan 7 Hari Terakhir</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Sentuh grafik untuk melihat detail nominal omzet & laba</p>
                </div>
                <div class="flex items-center gap-3 text-[11px] font-bold">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-md bg-blue-900 inline-block shadow-xs"></span>
                        <span class="text-slate-700">Omzet</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-md bg-emerald-500 inline-block shadow-xs"></span>
                        <span class="text-slate-700">Laba</span>
                    </div>
                </div>
            </div>

            <!-- Canvas Chart with Chart.js -->
            <div class="relative w-full h-52 sm:h-64 pt-1">
                <canvas id="sales-canvas-chart"></canvas>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col">
            <h3 class="font-bold text-sm text-slate-800 flex items-center gap-2 mb-3">
                <i data-lucide="award" class="w-4 h-4 text-amber-500"></i>
                <span>Produk Terlaris</span>
            </h3>

            <div class="flex-1 overflow-y-auto space-y-2.5 max-h-56 pr-1" id="top-products-list">
                <!-- Populated dynamically -->
            </div>
        </div>
    </div>

    <!-- Transaction History Table with Re-print -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden space-y-3 p-3.5 sm:p-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <h3 class="font-extrabold text-sm text-slate-800 flex items-center gap-2">
                <i data-lucide="receipt" class="w-4 h-4 text-slate-600"></i>
                <span>Riwayat Transaksi Terakhir</span>
            </h3>

            <!-- Filter Dates: 2 Inputs + Full-width Button on Mobile, Inline on Desktop (Never Cuts Off!) -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full sm:w-auto">
                <div class="grid grid-cols-2 gap-2 w-full sm:w-auto">
                    <input type="date" id="history-start-date" class="w-full sm:w-36 px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <input type="date" id="history-end-date" class="w-full sm:w-36 px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <button onclick="loadTransactions()" class="w-full sm:w-auto px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-xs active:scale-95 flex items-center justify-center space-x-1.5 flex-shrink-0">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </div>

        <!-- Desktop Table View (Hidden on mobile) -->
        <div class="hidden md:block overflow-x-auto border-t border-slate-100">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">No Invoice</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Metode Bayar</th>
                        <th class="py-3 px-4 text-right">Total Belanja</th>
                        <th class="py-3 px-4 text-right">Laba</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Cetak Struk</th>
                    </tr>
                </thead>
                <tbody id="transactions-table-body" class="divide-y divide-slate-100 font-medium">
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View for Transactions -->
        <div id="transactions-cards-container" class="md:hidden divide-y divide-slate-100 border-t border-slate-100">
            <!-- Populated by JS -->
        </div>
    </div>
</div>

<!-- Chart.js CDN for interactive data charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let salesChartInstance = null;

    document.addEventListener('DOMContentLoaded', () => {
        // Set default date range to current month
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
        const today = now.toISOString().split('T')[0];

        document.getElementById('history-start-date').value = firstDay;
        document.getElementById('history-end-date').value = today;

        loadDashboardSummary();
        loadTransactions();
    });

    async function loadDashboardSummary() {
        try {
            const res = await fetch(`${baseUrl}/api/report-summary`);
            const data = await res.json();
            if (data.success) {
                const s = data.summary;
                document.getElementById('stat-today-sales').innerText = 'Rp ' + Number(s.today_sales).toLocaleString('id-ID');
                document.getElementById('stat-today-profit').innerText = '+Rp ' + Number(s.today_profit).toLocaleString('id-ID');
                document.getElementById('stat-today-trx').innerText = `${s.today_trx} transaksi berhasil`;
                document.getElementById('stat-month-sales').innerText = 'Rp ' + Number(s.month_sales).toLocaleString('id-ID');
                document.getElementById('stat-month-profit').innerText = 'Laba: Rp ' + Number(s.month_profit).toLocaleString('id-ID');
                document.getElementById('stat-kasbon-unpaid').innerText = 'Rp ' + Number(s.kasbon_unpaid).toLocaleString('id-ID');
                document.getElementById('stat-kasbon-count').innerText = `${s.kasbon_count} catatan kasbon`;

                renderSalesChart(s.chart_data);
                renderTopProducts(s.top_products);
            }
        } catch (err) {
            console.error('Failed to load dashboard:', err);
        }
    }

    function renderSalesChart(data) {
        if (!data || data.length === 0) return;
        const canvas = document.getElementById('sales-canvas-chart');
        if (!canvas) return;

        const labels = data.map(d => d.label);
        const salesData = data.map(d => d.sales);
        const profitData = data.map(d => d.profit);

        if (salesChartInstance) {
            salesChartInstance.destroy();
        }

        const ctx = canvas.getContext('2d');

        // Linear Gradients for polished modern look
        const salesGradient = ctx.createLinearGradient(0, 0, 0, 220);
        salesGradient.addColorStop(0, '#1e3a8a'); // Tokomu Navy Blue
        salesGradient.addColorStop(1, '#3b82f6');

        const profitGradient = ctx.createLinearGradient(0, 0, 0, 220);
        profitGradient.addColorStop(0, '#059669'); // Emerald Green
        profitGradient.addColorStop(1, '#34d399');

        salesChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Penjualan (Omzet)',
                        data: salesData,
                        backgroundColor: salesGradient,
                        hoverBackgroundColor: '#172554',
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 22,
                        categoryPercentage: 0.65,
                        barPercentage: 0.85
                    },
                    {
                        label: 'Keuntungan (Laba)',
                        data: profitData,
                        backgroundColor: profitGradient,
                        hoverBackgroundColor: '#047857',
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 22,
                        categoryPercentage: 0.65,
                        barPercentage: 0.85
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        padding: 10,
                        cornerRadius: 12,
                        displayColors: true,
                        boxPadding: 4,
                        bodyFont: {
                            weight: '600',
                            size: 11
                        },
                        titleFont: {
                            weight: '800',
                            size: 12
                        },
                        callbacks: {
                            label: function(context) {
                                const val = Number(context.parsed.y || 0).toLocaleString('id-ID');
                                return ` ${context.dataset.label}: Rp ${val}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10,
                                weight: 'bold'
                            },
                            color: '#64748b'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 10,
                                weight: '600'
                            },
                            color: '#94a3b8',
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000) + 'jt';
                                if (value >= 1000) return (value / 1000) + 'rb';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }

    function renderTopProducts(products) {
        const container = document.getElementById('top-products-list');
        if (!products || products.length === 0) {
            container.innerHTML = `<div class="text-xs text-slate-400 py-4 text-center">Belum ada data penjualan</div>`;
            return;
        }

        container.innerHTML = products.map((p, idx) => {
            return `
                <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-50">
                    <div class="flex items-center space-x-2 min-w-0">
                        <span class="w-5 h-5 rounded-full ${idx < 3 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600'} font-bold text-[10px] flex items-center justify-center flex-shrink-0">
                            ${idx + 1}
                        </span>
                        <span class="font-bold text-slate-800 truncate">${p.product_name}</span>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="font-bold text-slate-900">${p.total_qty} ${p.unit}</span>
                        <div class="text-[10px] text-slate-400">Rp ${Number(p.total_revenue).toLocaleString('id-ID')}</div>
                    </div>
                </div>
            `;
        }).join('');
    }

    async function loadTransactions() {
        const start = document.getElementById('history-start-date').value;
        const end = document.getElementById('history-end-date').value;

        try {
            const res = await fetch(`${baseUrl}/api/transactions?start_date=${start}&end_date=${end}`);
            const data = await res.json();
            if (data.success) {
                renderTransactionsTable(data.transactions);
            }
        } catch (err) {
            console.error('Failed to load transactions:', err);
        }
    }

    function renderTransactionsTable(transactions) {
        const tbody = document.getElementById('transactions-table-body');
        const cardsContainer = document.getElementById('transactions-cards-container');

        if (!transactions || transactions.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="py-8 text-center text-slate-400">Tidak ada riwayat transaksi pada rentang tanggal ini.</td></tr>`;
            if (cardsContainer) {
                cardsContainer.innerHTML = `<div class="py-12 text-center text-slate-400">
                    <i data-lucide="receipt-text" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                    <div class="font-bold text-sm text-slate-600">Tidak ada transaksi</div>
                    <div class="text-xs">Ubah rentang tanggal atau buat transaksi baru di kasir</div>
                </div>`;
            }
            lucide.createIcons();
            return;
        }

        // 1. Desktop Table Rows
        tbody.innerHTML = transactions.map(t => {
            return `
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3 px-4 font-bold text-slate-800 font-mono text-[11px]">${t.invoice_no}</td>
                    <td class="py-3 px-4 text-slate-500">${new Date(t.created_at).toLocaleString('id-ID')}</td>
                    <td class="py-3 px-4 font-semibold text-slate-800">${t.customer_name}</td>
                    <td class="py-3 px-4 uppercase font-bold text-[11px]">${t.payment_method}</td>
                    <td class="py-3 px-4 text-right font-bold text-slate-900">Rp ${Number(t.grand_total).toLocaleString('id-ID')}</td>
                    <td class="py-3 px-4 text-right text-emerald-700 font-semibold">+Rp ${Number(t.total_profit).toLocaleString('id-ID')}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-2 py-0.5 rounded-full font-bold text-[10px] ${t.status === 'kasbon' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'}">
                            ${t.status.toUpperCase()}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <button onclick="reprintReceipt(${t.id})" class="px-2.5 py-1 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 rounded-lg font-bold text-[11px] transition flex items-center justify-center space-x-1 mx-auto" title="Cetak Ulang Struk">
                            <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                            <span>Struk</span>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        // 2. Mobile Cards
        if (cardsContainer) {
            cardsContainer.innerHTML = transactions.map(t => {
                const isKasbon = t.status === 'kasbon';
                return `
                    <div class="p-4 space-y-3 bg-white hover:bg-slate-50/70 transition">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="font-extrabold text-sm text-slate-900 leading-tight">
                                    ${t.customer_name || 'Pelanggan Umum'}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-1.5">
                                    <span>${t.invoice_no}</span>
                                    <span>&bull;</span>
                                    <span>${new Date(t.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })}</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-full font-bold text-[10px] uppercase bg-slate-100 text-slate-600">
                                    ${t.payment_method}
                                </span>
                                <span class="px-2 py-0.5 rounded-full font-bold text-[10px] uppercase ${isKasbon ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'}">
                                    ${t.status.toUpperCase()}
                                </span>
                            </div>
                        </div>

                        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-2.5 flex items-center justify-between">
                            <div>
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Belanja</div>
                                <div class="text-base font-black text-slate-900 font-mono">
                                    Rp ${Number(t.grand_total).toLocaleString('id-ID')}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Keuntungan / Laba</div>
                                <div class="text-xs font-bold text-emerald-700 font-mono">
                                    +Rp ${Number(t.total_profit).toLocaleString('id-ID')}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-0.5">
                            <button onclick="reprintReceipt(${t.id})" class="w-full py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition flex items-center justify-center space-x-1.5 active:scale-95 shadow-xs">
                                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                <span>Cetak Ulang Struk</span>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        lucide.createIcons();
    }

    async function reprintReceipt(id) {
        try {
            const res = await fetch(`${baseUrl}/api/receipt/${id}`);
            const data = await res.json();
            if (data.success && data.receipt) {
                showReceiptModal(data.receipt);
            }
        } catch (err) {
            alert('Gagal mengambil data struk: ' + err.message);
        }
    }

    function downloadCsvReport() {
        const start = document.getElementById('history-start-date').value;
        const end = document.getElementById('history-end-date').value;
        window.location.href = `${baseUrl}/api/report-export-csv?start_date=${start}&end_date=${end}`;
    }
</script>
