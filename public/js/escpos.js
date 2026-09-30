/**
 * ESC/POS Binary Command Encoder for 58mm and 80mm Mini Thermal Printers
 * Supports Bluetooth, USB Serial, and Raw ESC/POS streams
 */

class EscPosEncoder {
    constructor(paperSize = '58mm') {
        this.paperSize = paperSize;
        this.maxChars = (paperSize === '80mm') ? 48 : 32;
        this.buffer = [];
    }

    reset() {
        this.buffer = [];
        return this;
    }

    // Initialize printer
    init() {
        this.buffer.push(0x1B, 0x40); // ESC @
        return this;
    }

    // Alignment: 'left', 'center', 'right'
    align(alignment = 'left') {
        let n = 0;
        if (alignment === 'center') n = 1;
        if (alignment === 'right') n = 2;
        this.buffer.push(0x1B, 0x61, n); // ESC a n
        return this;
    }

    // Bold text
    bold(enable = true) {
        this.buffer.push(0x1B, 0x45, enable ? 1 : 0); // ESC E n
        return this;
    }

    // Underline
    underline(enable = true) {
        this.buffer.push(0x1B, 0x2D, enable ? 1 : 0); // ESC - n
        return this;
    }

    // Text Size: normal, double-height, double-width, large
    size(mode = 'normal') {
        let n = 0x00;
        if (mode === 'double-height') n = 0x01;
        if (mode === 'double-width') n = 0x10;
        if (mode === 'large' || mode === 'double') n = 0x11;
        this.buffer.push(0x1D, 0x21, n); // GS ! n
        return this;
    }

    // Feed lines
    feed(lines = 1) {
        for (let i = 0; i < lines; i++) {
            this.buffer.push(0x0A); // LF
        }
        return this;
    }

    // Print raw string with simple ASCII / CP437 mapping
    text(str) {
        if (!str) return this;
        for (let i = 0; i < str.length; i++) {
            let code = str.charCodeAt(i);
            if (code > 255) {
                // Approximate non-ascii
                code = 63; // '?'
            }
            this.buffer.push(code);
        }
        return this;
    }

    // Text with newline
    line(str = '') {
        this.text(str);
        this.buffer.push(0x0A);
        return this;
    }

    // Separator line
    divider(char = '-') {
        if (char === '-') {
            const pattern = '- ';
            const times = Math.floor(this.maxChars / 2);
            this.line(pattern.repeat(times).trim());
        } else {
            this.line(char.repeat(this.maxChars));
        }
        return this;
    }

    doubleDivider() {
        return this.divider('=');
    }

    // Inverted text mode (white on black banner)
    reverse(enable = true) {
        this.buffer.push(0x1D, 0x42, enable ? 1 : 0); // GS B n
        return this;
    }

    // Modern Ribbon Banner
    banner(text) {
        this.align('center').reverse(true).bold(true).text(`  ${text}  `).reverse(false).bold(false).feed(1);
        return this;
    }

    // Left and Right aligned text in single line (e.g. "Subtotal:        Rp 50.000")
    row(left, right) {
        left = String(left || '');
        right = String(right || '');
        const spacesNeeded = this.maxChars - left.length - right.length;
        if (spacesNeeded >= 0) {
            this.line(left + ' '.repeat(spacesNeeded) + right);
        } else {
            // If text is too long, wrap
            this.line(left);
            this.align('right').line(right).align('left');
        }
        return this;
    }

    // POS Item row:
    // Name on line 1 (if long) or same line
    // Qty x Price on left, Subtotal on right
    item(name, qty, unit, price, subtotal) {
        this.align('left');
        this.bold(true).line(name).bold(false);
        
        const qtyPrice = `  ${qty} ${unit} x ${this.formatMoney(price)}`;
        const totalStr = this.formatMoney(subtotal);
        this.row(qtyPrice, totalStr);
        return this;
    }

    // Cut paper
    cut() {
        this.feed(3);
        this.buffer.push(0x1D, 0x56, 0x41, 0x10); // GS V 65 16 (cut)
        return this;
    }

    // Cash drawer kick
    pulseDrawer() {
        this.buffer.push(0x1B, 0x70, 0x00, 0x19, 0xFA);
        return this;
    }

    // Get Uint8Array
    encode() {
        return new Uint8Array(this.buffer);
    }

    formatMoney(num) {
        return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
    }

    /**
     * Build standard receipt ESC/POS payload from receipt data object
     */
    buildReceipt(data) {
        this.reset().init();

        const store = data.store || {};
        const trx = data.transaction || {};
        const items = data.items || [];

        // Store Header (Centered & Bold)
        this.align('center').bold(true).size('double-height');
        this.line(store.name || 'TOKOMU');
        this.size('normal').bold(false);

        if (store.tagline) {
            this.line(store.tagline);
        }
        if (store.address) {
            this.line(store.address);
        }
        if (store.phone) {
            this.line('WA: ' + store.phone);
        }

        this.feed(1);
        this.banner('STRUK PENJUALAN RESMI');

        // Transaction Info
        this.align('left');
        this.row('No. Struk', trx.invoice_no || '-');
        this.row('Tanggal  ', trx.date || '-');
        this.row('Pelanggan', trx.customer_name || 'Pelanggan Umum');
        if (trx.customer_phone) {
            this.row('No HP/WA ', trx.customer_phone);
        }
        this.row('Metode   ', '[' + (trx.payment_method || 'TUNAI') + ']');
        
        this.divider('-');

        // Items Header
        this.row('DESKRIPSI BARANG', 'TOTAL');
        this.divider('-');

        // Items
        items.forEach(it => {
            this.item(it.name, it.qty, it.unit, it.price, it.subtotal);
        });

        this.divider('-');

        // Totals
        this.row('Subtotal', this.formatMoney(trx.subtotal));
        if (trx.discount_amount > 0) {
            this.row('Diskon', '-' + this.formatMoney(trx.discount_amount));
        }
        
        this.divider('=');
        this.bold(true).size('double-height');
        this.row('TOTAL', this.formatMoney(trx.grand_total));
        this.size('normal').bold(false);
        this.divider('=');

        if (trx.payment_method === 'CASH' || trx.payment_method === 'TUNAI') {
            this.row('Tunai Diterima', this.formatMoney(trx.cash_amount));
            this.bold(true);
            this.row('Kembalian', this.formatMoney(trx.change_amount));
            this.bold(false);
        } else if (trx.payment_method === 'KASBON') {
            this.bold(true);
            this.row('STATUS', 'KASBON / UTANG');
            this.bold(false);
        } else {
            this.row('Status', 'LUNAS (' + trx.payment_method + ')');
        }

        this.divider('-');

        // Footer note
        this.align('center');
        if (store.receipt_header) {
            this.bold(true).line(store.receipt_header).bold(false);
        } else {
            this.bold(true).line('TERIMA KASIH ATAS KUNJUNGAN ANDA').bold(false);
        }
        if (store.receipt_footer) {
            const footerLines = store.receipt_footer.split('\n');
            footerLines.forEach(l => this.line(l.trim()));
        } else {
            this.line('Barang yang dibeli tidak dapat ditukar');
            this.line('Simpan struk ini sebagai bukti transaksi');
        }
        this.line('*** WARUNG POS PINTAR ***');

        this.feed(3);
        this.cut();

        return this.encode();
    }

    /**
     * Build Kasbon receipt ESC/POS payload
     */
    buildKasbonReceipt(data) {
        this.reset().init();

        const store = data.store || {};
        const kasbon = data.kasbon || {};
        const payments = data.payments || [];

        this.align('center').bold(true).size('double-height');
        this.line(store.name || 'TOKOMU');
        this.size('normal').bold(false);

        if (store.tagline) this.line(store.tagline);
        if (store.address) this.line(store.address);
        if (store.phone) this.line('WA: ' + store.phone);

        this.feed(1);
        this.banner('BUKTI CATATAN KASBON');

        this.align('left');
        this.row('No Bukti', kasbon.invoice_no);
        this.row('Tanggal ', kasbon.created_at);
        this.bold(true);
        this.row('Pelanggan', kasbon.customer_name);
        this.bold(false);
        if (kasbon.customer_phone) this.row('No HP/WA', kasbon.customer_phone);
        this.row('Jatuh Tempo', kasbon.due_date || '-');
        
        this.divider('-');
        this.row('Total Utang', this.formatMoney(kasbon.total_debt));
        this.row('Sudah Dibayar', this.formatMoney(kasbon.paid_amount));
        
        this.divider('=');
        this.bold(true).size('double-height');
        this.row('SISA KASBON', this.formatMoney(kasbon.remaining_debt));
        this.size('normal').bold(false);
        this.divider('=');

        const isPaid = (kasbon.status === 'paid' || Number(kasbon.remaining_debt) <= 0);
        const isPartial = (kasbon.status === 'partial');
        
        this.align('center');
        if (isPaid) {
            this.bold(true).line('★ ★ ★ L U N A S ★ ★ ★').bold(false);
            this.line('[ TERBAYAR PENUH ]');
        } else if (isPartial) {
            this.bold(true).line('[ STATUS: DICICIL ]').bold(false);
            this.line('Sisa tagihan: ' + this.formatMoney(kasbon.remaining_debt));
        } else {
            this.bold(true).line('[ STATUS: BELUM BAYAR ]').bold(false);
            this.line('Jatuh tempo: ' + (kasbon.due_date || '-'));
        }

        if (payments && payments.length > 0) {
            this.divider('-');
            this.align('center').bold(true).line('· RIWAYAT ANGSURAN ·').bold(false);
            this.align('left');
            payments.forEach(p => {
                this.row(p.date, '+ ' + this.formatMoney(p.amount));
                if (p.notes) {
                    this.line('  (' + p.notes + ')');
                }
            });
        }

        this.divider('-');
        this.align('center');
        this.line('Harap disimpan sebagai bukti kasbon sah');
        this.line('Terima Kasih atas kerjasamanya!');
        this.line('*** WARUNG POS PINTAR ***');
        this.feed(3);
        this.cut();

        return this.encode();
    }

    /**
     * Build Test Print payload
     */
    buildTestPrint(storeName = 'TOKOMU') {
        this.reset().init();
        this.align('center').bold(true).size('double-height');
        this.line(storeName);
        this.size('normal').bold(false);
        this.line('TEST PRINT THERMAL BERHASIL!');
        this.divider('=');
        this.align('left');
        this.line('Tanggal  : ' + new Date().toLocaleString('id-ID'));
        this.line('Ukuran   : ' + this.paperSize + ' (' + this.maxChars + ' Karakter/baris)');
        this.line('Koneksi  : Mini Thermal ESC/POS');
        this.divider('-');
        this.line('12345678901234567890123456789012');
        if (this.maxChars > 32) {
            this.line('3456789012345678');
        }
        this.divider('=');
        this.align('center');
        this.line('Printer siap digunakan untuk');
        this.line('mencetak struk transaksi!');
        this.feed(3);
        this.cut();
        return this.encode();
    }
}

window.EscPosEncoder = EscPosEncoder;
