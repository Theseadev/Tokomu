/**
 * Thermal Printer Manager
 * Connects and prints via:
 * 1. Web Bluetooth API (Wireless ESC/POS thermal printers)
 * 2. Web Serial API (USB ESC/POS thermal printers)
 * 3. Browser System Print Fallback (Windows/Android/Mac print dialog)
 * 4. WhatsApp Digital Receipt generator
 */

class ThermalPrinterManager {
    constructor() {
        this.bluetoothDevice = null;
        this.bluetoothServer = null;
        this.bluetoothCharacteristic = null;

        this.serialPort = null;
        this.serialWriter = null;

        this.connectionType = localStorage.getItem('warung_printer_type') || 'bluetooth'; // 'bluetooth' | 'usb' | 'system'
        this.paperSize = localStorage.getItem('warung_paper_size') || '58mm'; // '58mm' | '80mm'
        this.printerName = localStorage.getItem('warung_printer_name') || '';

        this.encoder = new EscPosEncoder(this.paperSize);
        this.isPrinting = false;

        this.listeners = [];

        // Check browser capabilities
        this.hasBluetooth = !!(navigator.bluetooth);
        this.hasSerial = !!(navigator.serial);
    }

    setPaperSize(size) {
        this.paperSize = (size === '80mm') ? '80mm' : '58mm';
        this.encoder.paperSize = this.paperSize;
        this.encoder.maxChars = (this.paperSize === '80mm') ? 48 : 32;
        localStorage.setItem('warung_paper_size', this.paperSize);
        this.notifyStatus();
    }

    setConnectionType(type) {
        this.connectionType = type;
        localStorage.setItem('warung_printer_type', type);
        this.notifyStatus();
    }

    onStatusChange(fn) {
        this.listeners.push(fn);
    }

    notifyStatus() {
        const status = this.getStatus();
        this.listeners.forEach(fn => fn(status));
    }

    getStatus() {
        let isConnected = false;
        let info = 'Belum terhubung';

        if (this.connectionType === 'bluetooth') {
            isConnected = !!(this.bluetoothCharacteristic && this.bluetoothDevice?.gatt?.connected);
            info = isConnected ? `Bluetooth: ${this.bluetoothDevice.name || 'Printer'}` : 'Bluetooth: Terputus';
        } else if (this.connectionType === 'usb') {
            isConnected = !!(this.serialPort && this.serialPort.readable);
            info = isConnected ? `USB Serial: Terhubung` : 'USB Serial: Belum terhubung';
        } else {
            isConnected = true;
            info = 'Cetak Dialog Sistem / PDF';
        }

        return {
            connectionType: this.connectionType,
            paperSize: this.paperSize,
            isConnected: isConnected,
            info: info,
            hasBluetooth: this.hasBluetooth,
            hasSerial: this.hasSerial,
            printerName: this.printerName
        };
    }

    /**
     * Connect to Bluetooth Thermal Printer
     */
    async connectBluetooth() {
        if (!this.hasBluetooth) {
            throw new Error('Browser ini tidak mendukung Web Bluetooth. Gunakan Google Chrome atau Microsoft Edge di PC/Laptop atau Android!');
        }

        try {
            // Known thermal printer BLE service UUIDs
            const optionalServices = [
                '000018f0-0000-1000-8000-00805f9b34fb', // Standard POS BLE
                '0000ffe0-0000-1000-8000-00805f9b34fb', // Common HM-10 serial BLE
                '49535343-fe7d-4ae5-8fa9-9fafd205e455', // ISSC Microchip
                'e7810a71-73ae-499d-8c15-faa9aef0c3f2', // Posiflex / Rongta
                '0000ff00-0000-1000-8000-00805f9b34fb'
            ];

            const device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: optionalServices
            });

            this.bluetoothDevice = device;
            this.printerName = device.name || 'Thermal Printer';
            localStorage.setItem('warung_printer_name', this.printerName);

            device.addEventListener('gattserverdisconnected', () => {
                this.bluetoothCharacteristic = null;
                this.notifyStatus();
            });

            // Connect GATT
            this.bluetoothServer = await device.gatt.connect();

            // Find valid writable characteristic
            let foundChar = null;
            for (const serviceUuid of optionalServices) {
                try {
                    const service = await this.bluetoothServer.getPrimaryService(serviceUuid);
                    const characteristics = await service.getCharacteristics();
                    for (const char of characteristics) {
                        if (char.properties.write || char.properties.writeWithoutResponse) {
                            foundChar = char;
                            break;
                        }
                    }
                    if (foundChar) break;
                } catch (e) {
                    // Service not found on this device, check next
                }
            }

            if (!foundChar) {
                // If not in known list, discover all primary services
                const services = await this.bluetoothServer.getPrimaryServices();
                for (const service of services) {
                    const characteristics = await service.getCharacteristics();
                    for (const char of characteristics) {
                        if (char.properties.write || char.properties.writeWithoutResponse) {
                            foundChar = char;
                            break;
                        }
                    }
                    if (foundChar) break;
                }
            }

            if (!foundChar) {
                throw new Error('Tidak dapat menemukan karakteristik cetak (write) pada printer Bluetooth ini.');
            }

            this.bluetoothCharacteristic = foundChar;
            this.setConnectionType('bluetooth');
            this.notifyStatus();

            return { success: true, name: this.printerName };
        } catch (err) {
            console.error('Bluetooth connection error:', err);
            throw err;
        }
    }

    /**
     * Connect to USB Serial Thermal Printer
     */
    async connectUsbSerial() {
        if (!this.hasSerial) {
            throw new Error('Browser ini tidak mendukung Web Serial API. Gunakan Google Chrome atau Edge!');
        }

        try {
            const port = await navigator.serial.requestPort();
            // Default 9600 baud rate for thermal printers
            await port.open({ baudRate: 9600, dataBits: 8, stopBits: 1, parity: 'none' });
            
            this.serialPort = port;
            this.setConnectionType('usb');
            this.printerName = 'USB Thermal Printer';
            localStorage.setItem('warung_printer_name', this.printerName);
            this.notifyStatus();

            return { success: true, name: this.printerName };
        } catch (err) {
            console.error('USB Serial connection error:', err);
            throw err;
        }
    }

    /**
     * Disconnect active printer
     */
    async disconnect() {
        if (this.bluetoothDevice && this.bluetoothDevice.gatt.connected) {
            this.bluetoothDevice.gatt.disconnect();
        }
        this.bluetoothCharacteristic = null;

        if (this.serialPort) {
            try {
                if (this.serialWriter) {
                    await this.serialWriter.close();
                    this.serialWriter = null;
                }
                await this.serialPort.close();
            } catch (e) {}
            this.serialPort = null;
        }

        this.notifyStatus();
    }

    /**
     * Send raw binary ESC/POS data chunked to printer
     */
    async sendRawData(dataUint8Array) {
        if (this.connectionType === 'bluetooth') {
            if (!this.bluetoothCharacteristic || !this.bluetoothDevice?.gatt?.connected) {
                // Try reconnect or prompt
                await this.connectBluetooth();
            }

            // Write chunked to prevent hardware buffer overflow
            const chunkSize = 120; // 120 bytes chunks
            for (let i = 0; i < dataUint8Array.length; i += chunkSize) {
                const chunk = dataUint8Array.slice(i, i + chunkSize);
                if (this.bluetoothCharacteristic.writeValueWithoutResponse) {
                    await this.bluetoothCharacteristic.writeValueWithoutResponse(chunk);
                } else {
                    await this.bluetoothCharacteristic.writeValue(chunk);
                }
                // Delay 20ms between chunks for mini printer buffer safety
                await new Promise(r => setTimeout(r, 20));
            }
        } else if (this.connectionType === 'usb') {
            if (!this.serialPort || !this.serialPort.writable) {
                await this.connectUsbSerial();
            }

            const writer = this.serialPort.writable.getWriter();
            try {
                // Chunked write for serial buffer
                const chunkSize = 256;
                for (let i = 0; i < dataUint8Array.length; i += chunkSize) {
                    const chunk = dataUint8Array.slice(i, i + chunkSize);
                    await writer.write(chunk);
                    await new Promise(r => setTimeout(r, 15));
                }
            } finally {
                writer.releaseLock();
            }
        } else {
            throw new Error('Metode pengiriman biner tidak berlaku untuk cetak dialog sistem');
        }
    }

    /**
     * Main Print Receipt method
     */
    async printReceipt(receiptData) {
        if (this.isPrinting) return;
        this.isPrinting = true;

        try {
            if (this.connectionType === 'system') {
                this.printSystemDialog(receiptData);
                return { success: true, method: 'system' };
            }

            const binaryData = this.encoder.buildReceipt(receiptData);
            await this.sendRawData(binaryData);
            return { success: true, method: this.connectionType };
        } catch (err) {
            console.error('Print receipt failed:', err);
            // Fallback to system print if user wants
            throw err;
        } finally {
            this.isPrinting = false;
        }
    }

    /**
     * Print Kasbon Receipt
     */
    async printKasbonReceipt(kasbonData) {
        if (this.isPrinting) return;
        this.isPrinting = true;

        try {
            if (this.connectionType === 'system') {
                this.printKasbonSystemDialog(kasbonData);
                return { success: true, method: 'system' };
            }

            const binaryData = this.encoder.buildKasbonReceipt(kasbonData);
            await this.sendRawData(binaryData);
            return { success: true, method: this.connectionType };
        } catch (err) {
            console.error('Print kasbon failed:', err);
            throw err;
        } finally {
            this.isPrinting = false;
        }
    }

    /**
     * Test Print
     */
    async testPrint(storeName) {
        try {
            if (this.connectionType === 'system') {
                const sample = {
                    store: { name: storeName || 'Tokomu', address: 'Jl. Pemajatan Komp. Permata Hijau 2, Blok B No.2', phone: '0812-3456-7890' },
                    transaction: { invoice_no: 'TRX-TEST-0001', date: new Date().toLocaleString('id-ID'), customer_name: 'Tes Cetak', payment_method: 'TUNAI', subtotal: 15000, discount_amount: 0, grand_total: 15000, cash_amount: 20000, change_amount: 5000 },
                    items: [{ name: 'Beras Rojo Lele (1kg)', qty: 1, unit: 'kg', price: 15000, subtotal: 15000 }]
                };
                this.printSystemDialog(sample);
                return { success: true };
            }

            const data = this.encoder.buildTestPrint(storeName);
            await this.sendRawData(data);
            return { success: true };
        } catch (err) {
            console.error('Test print failed:', err);
            throw err;
        }
    }
    /**
     * Pure JS Code 39 Vector SVG Barcode Generator (100% offline, zero dependencies)
     */
    generateBarcodeSvg(text, height = 34) {
        if (!text) return '';
        const clean = String(text).toUpperCase().replace(/[^0-9A-Z\-.\ \$\/\+\%]/g, '');
        const fullText = '*' + clean + '*';
        const patterns = {
            '0': '000110100', '1': '100100001', '2': '001100001', '3': '101100000',
            '4': '000110001', '5': '100110000', '6': '001110000', '7': '000100101',
            '8': '100100100', '9': '001100100', 'A': '100001001', 'B': '001001001',
            'C': '101001000', 'D': '000011001', 'E': '100011000', 'F': '001011000',
            'G': '000001101', 'H': '100001100', 'I': '001001100', 'J': '000011100',
            'K': '100000011', 'L': '001000011', 'M': '101000010', 'N': '000010011',
            'O': '100010010', 'P': '001010010', 'Q': '000000111', 'R': '100000110',
            'S': '001000110', 'T': '000010110', 'U': '110000001', 'V': '011000001',
            'W': '111000000', 'X': '010010001', 'Y': '110010000', 'Z': '011010000',
            '-': '010000101', '.': '110000100', ' ': '011000100', '$': '010101000',
            '/': '010100010', '+': '010001010', '%': '000101010', '*': '010010100'
        };

        const rects = [];
        let currentX = 0;
        const narrowW = 1.2;
        const wideW = 2.8;

        for (let i = 0; i < fullText.length; i++) {
            const char = fullText[i];
            const pattern = patterns[char] || patterns['*'];
            for (let p = 0; p < 9; p++) {
                const isBar = (p % 2 === 0);
                const isWide = (pattern[p] === '1');
                const w = isWide ? wideW : narrowW;
                if (isBar) {
                    rects.push(`<rect x="${currentX.toFixed(1)}" y="0" width="${w.toFixed(1)}" height="${height}" fill="#000000" />`);
                }
                currentX += w;
            }
            currentX += narrowW;
        }

        return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${currentX.toFixed(1)} ${height}" style="width: 100%; max-width: 220px; height: ${height}px; display: block; margin: 0 auto;">${rects.join('')}</svg>`;
    }

    /**
     * Render QR code in containers with class .receipt-qr-target
     */
    renderQrCodesInContainer(container) {
        if (!container || !window.QRCode) return;
        const targets = container.querySelectorAll('.receipt-qr-target');
        targets.forEach(el => {
            if (el.getAttribute('data-rendered') === 'true') return;
            const text = el.getAttribute('data-qr');
            if (!text) return;
            el.innerHTML = '';
            new QRCode(el, {
                text: text,
                width: 60,
                height: 60,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
            el.setAttribute('data-rendered', 'true');
        });
    }

    /**
     * Generate Monochrome POS Sales Receipt HTML (Black & White Thermal Design)
     */
    generateReceiptHtml(receiptData, options = {}) {
        const store = receiptData.store || {};
        const trx = receiptData.transaction || {};
        const items = receiptData.items || [];
        const is80 = (this.paperSize === '80mm');
        const isPrint = !!options.isPrint;
        const width = isPrint ? (is80 ? '76mm' : '54mm') : '100%';

        const invoiceNo = trx.invoice_no || 'TRX-0000';
        const dateStr = trx.date || new Date().toLocaleString('id-ID');
        const custName = trx.customer_name || 'Pelanggan Umum';
        const payMethod = (trx.payment_method || 'TUNAI').toUpperCase();
        const barcodeSvg = this.generateBarcodeSvg(invoiceNo, 32);
        const qrData = `WARUNG-POS:${invoiceNo}:${trx.grand_total}`;

        let html = `
            <div class="thermal-print-container ${is80 ? 'paper-80mm' : ''}" style="width: ${width}; max-width: ${is80 ? '380px' : '300px'}; margin: 0 auto; padding: ${isPrint ? '4px 6px' : '10px 8px'}; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 11px; line-height: 1.35; color: #000; background: #fff;">
                
                <!-- Store Brand Header -->
                <div style="text-align: center; margin-bottom: 6px;">
                    <!-- Store Icon Monogram -->
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 50%; background: #000; color: #fff; margin-bottom: 4px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/>
                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
                            <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/>
                            <path d="M2 7h20"/>
                            <path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"/>
                        </svg>
                    </div>

                    <div style="font-size: 15px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; color: #000; line-height: 1.2;">
                        ${store.name || 'TOKOMU'}
                    </div>
                    ${store.tagline ? `<div style="font-size: 10px; color: #000; font-style: italic; margin-top: 1px;">${store.tagline}</div>` : ''}
                    <div style="font-size: 9.5px; color: #000; margin-top: 2px; line-height: 1.25;">
                        ${store.address || 'Jl. Pemajatan Komp. Permata Hijau 2, Blok B No.2'}
                    </div>
                    ${store.phone ? `<div style="font-size: 9.5px; color: #000; font-weight: 600; margin-top: 1px;">WhatsApp: ${store.phone}</div>` : ''}
                </div>

                <!-- Receipt Type Ribbon (Black & White) -->
                <div style="text-align: center; margin: 6px 0;">
                    <span style="display: inline-block; padding: 2px 10px; background: #000; color: #fff; font-size: 10.5px; font-weight: 800; letter-spacing: 0.8px; border-radius: 3px; text-transform: uppercase;">
                        ✦ STRUK PENJUALAN RESMI ✦
                    </span>
                </div>

                <!-- Transaction Info Table -->
                <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 4px 0; margin-bottom: 6px; font-size: 10.5px;">
                    <table style="width: 100%; border-collapse: collapse; line-height: 1.35; color: #000;">
                        <tr>
                            <td style="color: #000; width: 40%;">No. Struk</td>
                            <td style="text-align: right; font-weight: 700; font-family: 'JetBrains Mono', monospace;">${invoiceNo}</td>
                        </tr>
                        <tr>
                            <td style="color: #000;">Tanggal</td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace;">${dateStr}</td>
                        </tr>
                        <tr>
                            <td style="color: #000;">Pelanggan</td>
                            <td style="text-align: right; font-weight: 600;">${custName}</td>
                        </tr>
                        ${trx.customer_phone ? `
                        <tr>
                            <td style="color: #000;">No HP/WA</td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace;">${trx.customer_phone}</td>
                        </tr>` : ''}
                        <tr>
                            <td style="color: #000;">Metode Bayar</td>
                            <td style="text-align: right; font-weight: 700;">[ ${payMethod} ]</td>
                        </tr>
                    </table>
                </div>

                <!-- Items Header -->
                <div style="display: flex; justify-content: space-between; font-size: 9.5px; font-weight: 800; letter-spacing: 0.5px; border-bottom: 1px dashed #000; padding-bottom: 3px; margin-bottom: 4px; color: #000;">
                    <span>DESKRIPSI BARANG</span>
                    <span>TOTAL</span>
                </div>

                <!-- Items Table -->
                <div style="margin-bottom: 6px;">
        `;

        items.forEach(it => {
            html += `
                <div style="margin-bottom: 4px; color: #000;">
                    <div style="font-weight: 700; font-size: 11px; color: #000; line-height: 1.25;">${it.name}</div>
                    <div style="display: flex; justify-content: space-between; font-size: 10.5px; color: #000; font-family: 'JetBrains Mono', monospace;">
                        <span>${it.qty} ${it.unit} × ${Number(it.price).toLocaleString('id-ID')}</span>
                        <span style="font-weight: 700; color: #000;">Rp ${Number(it.subtotal).toLocaleString('id-ID')}</span>
                    </div>
                </div>
            `;
        });

        html += `
                </div>

                <!-- Financial Calculation & Totals -->
                <div style="border-top: 1px dashed #000; padding-top: 5px; font-size: 10.5px; color: #000;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                        <span>Subtotal:</span>
                        <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">Rp ${Number(trx.subtotal).toLocaleString('id-ID')}</span>
                    </div>
                    ${trx.discount_amount > 0 ? `
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                        <span>Diskon:</span>
                        <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">-Rp ${Number(trx.discount_amount).toLocaleString('id-ID')}</span>
                    </div>` : ''}

                    <!-- Grand Total Highlight Box (Monochrome Solid Border) -->
                    <div style="border: 2px solid #000; border-radius: 4px; padding: 5px 8px; margin: 6px 0; background: #fff; color: #000;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 800; font-size: 12px; letter-spacing: 0.5px;">TOTAL:</span>
                            <span style="font-weight: 900; font-size: 15px; font-family: 'JetBrains Mono', monospace;">Rp ${Number(trx.grand_total).toLocaleString('id-ID')}</span>
                        </div>
                    </div>

                    ${(payMethod === 'CASH' || payMethod === 'TUNAI') ? `
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                        <span>Tunai Diterima:</span>
                        <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">Rp ${Number(trx.cash_amount).toLocaleString('id-ID')}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px; font-weight: 700;">
                        <span>Kembalian:</span>
                        <span style="font-family: 'JetBrains Mono', monospace; font-weight: 800;">Rp ${Number(trx.change_amount).toLocaleString('id-ID')}</span>
                    </div>` : `
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px; font-weight: 700;">
                        <span>Status Transaksi:</span>
                        <span style="font-weight: 800;">${trx.status === 'kasbon' ? 'KASBON / UTANG' : 'LUNAS (' + payMethod + ')'}</span>
                    </div>`}
                </div>

                <!-- Code 39 Barcode -->
                <div style="text-align: center; margin: 10px 0 4px; border-top: 1px dashed #000; padding-top: 8px;">
                    <div style="margin: 0 auto; display: flex; justify-content: center;">
                        ${barcodeSvg}
                    </div>
                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 9.5px; font-weight: 700; letter-spacing: 1px; margin-top: 2px; color: #000;">
                        * ${invoiceNo} *
                    </div>
                </div>

                <!-- QR Code & E-Struk Verification (Monochrome) -->
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin: 8px 0; padding: 5px; background: #fff; border: 1px solid #000; border-radius: 4px;">
                    <div class="receipt-qr-target" data-qr="${qrData}" style="width: 60px; height: 60px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;"></div>
                    <div style="text-align: left; font-size: 9px; color: #000; line-height: 1.3;">
                        <div style="font-weight: 800; color: #000; text-transform: uppercase;">E-Struk & Verifikasi</div>
                        <div>Scan QR untuk cek transaksi sah & simpan bukti via HP.</div>
                    </div>
                </div>

                <!-- Footer Greetings & Policy -->
                <div style="border-top: 1px dashed #000; padding-top: 6px; margin-top: 6px; text-align: center; font-size: 9.5px; color: #000; line-height: 1.35;">
                    <div style="font-weight: 800; color: #000; margin-bottom: 2px;">
                        ${store.receipt_header || 'TERIMA KASIH ATAS KUNJUNGAN ANDA!'}
                    </div>
                    <div>${store.receipt_footer ? store.receipt_footer.replace(/\n/g, '<br>') : 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.<br>Simpan struk ini sebagai bukti pembayaran sah.'}</div>
                    <div style="margin-top: 5px; font-size: 8.5px; color: #000; letter-spacing: 0.5px;">
                        *** SISTEM WARUNG POS PINTAR ***
                    </div>
                </div>

            </div>
        `;

        return html;
    }

    /**
     * Generate Monochrome Kasbon Receipt HTML (Black & White Thermal Design)
     */
    generateKasbonReceiptHtml(kasbonData, options = {}) {
        const store = kasbonData.store || {};
        const kasbon = kasbonData.kasbon || {};
        const payments = kasbonData.payments || [];
        const is80 = (this.paperSize === '80mm');
        const isPrint = !!options.isPrint;
        const width = isPrint ? (is80 ? '76mm' : '54mm') : '100%';

        const invoiceNo = kasbon.invoice_no || `KB-${String(kasbon.id || '1').padStart(4, '0')}`;
        const dateStr = kasbon.created_at || new Date().toLocaleString('id-ID');
        const custName = kasbon.customer_name || 'Pelanggan Kasbon';
        const isPaid = (kasbon.status === 'paid' || Number(kasbon.remaining_debt) <= 0);
        const isPartial = (kasbon.status === 'partial');
        const barcodeSvg = this.generateBarcodeSvg(invoiceNo, 32);
        const qrData = `WARUNG-KASBON:${invoiceNo}:${custName}:${kasbon.remaining_debt}`;

        let html = `
            <div class="thermal-print-container ${is80 ? 'paper-80mm' : ''}" style="width: ${width}; max-width: ${is80 ? '380px' : '300px'}; margin: 0 auto; padding: ${isPrint ? '4px 6px' : '10px 8px'}; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 11px; line-height: 1.35; color: #000; background: #fff;">
                
                <!-- Store Brand Header -->
                <div style="text-align: center; margin-bottom: 6px;">
                    <!-- Store Awning Icon Badge -->
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 50%; background: #000; color: #fff; margin-bottom: 4px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                    </div>

                    <div style="font-size: 15px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; color: #000; line-height: 1.2;">
                        ${store.name || 'TOKOMU'}
                    </div>
                    ${store.tagline ? `<div style="font-size: 10px; color: #000; font-style: italic; margin-top: 1px;">${store.tagline}</div>` : ''}
                    <div style="font-size: 9.5px; color: #000; margin-top: 2px; line-height: 1.25;">
                        ${store.address || 'Jl. Pemajatan Komp. Permata Hijau 2, Blok B No.2'}
                    </div>
                    ${store.phone ? `<div style="font-size: 9.5px; color: #000; font-weight: 600; margin-top: 1px;">WhatsApp: ${store.phone}</div>` : ''}
                </div>

                <!-- Receipt Type Ribbon (Black & White) -->
                <div style="text-align: center; margin: 6px 0;">
                    <span style="display: inline-block; padding: 2px 10px; background: #000; color: #fff; font-size: 10.5px; font-weight: 800; letter-spacing: 0.8px; border-radius: 3px; text-transform: uppercase;">
                        ✦ BUKTI CATATAN KASBON ✦
                    </span>
                </div>

                <!-- Transaction Info Table -->
                <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 4px 0; margin-bottom: 6px; font-size: 10.5px;">
                    <table style="width: 100%; border-collapse: collapse; line-height: 1.35; color: #000;">
                        <tr>
                            <td style="color: #000; width: 40%;">No. Bukti</td>
                            <td style="text-align: right; font-weight: 700; font-family: 'JetBrains Mono', monospace;">${invoiceNo}</td>
                        </tr>
                        <tr>
                            <td style="color: #000;">Tanggal</td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace;">${dateStr}</td>
                        </tr>
                        <tr>
                            <td style="color: #000;">Pelanggan</td>
                            <td style="text-align: right; font-weight: 800; font-size: 11.5px; color: #000;">${custName}</td>
                        </tr>
                        ${kasbon.customer_phone ? `
                        <tr>
                            <td style="color: #000;">No HP/WA</td>
                            <td style="text-align: right; font-family: 'JetBrains Mono', monospace;">${kasbon.customer_phone}</td>
                        </tr>` : ''}
                        <tr>
                            <td style="color: #000;">Jatuh Tempo</td>
                            <td style="text-align: right; font-weight: 700; color: #000;">${kasbon.due_date || '-'}</td>
                        </tr>
                    </table>
                </div>

                <!-- Financial Balance Section -->
                <div style="padding: 4px 0; font-size: 10.5px; color: #000;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                        <span>Total Utang:</span>
                        <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">Rp ${Number(kasbon.total_debt).toLocaleString('id-ID')}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                        <span>Sudah Dibayar:</span>
                        <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">Rp ${Number(kasbon.paid_amount).toLocaleString('id-ID')}</span>
                    </div>

                    <!-- Highlight SISA KASBON box (Solid Monochrome) -->
                    <div style="background: #fff; border: 2px solid #000; border-radius: 4px; padding: 6px 8px; margin: 6px 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 800; font-size: 12px; letter-spacing: 0.5px; color: #000;">SISA KASBON:</span>
                            <span style="font-weight: 900; font-size: 16px; font-family: 'JetBrains Mono', monospace; color: #000;">
                                Rp ${Number(kasbon.remaining_debt).toLocaleString('id-ID')}
                            </span>
                        </div>
                    </div>

                    <!-- Prominent Status Stamp Badge (Monochrome Stamp) -->
                    <div style="text-align: center; margin: 8px 0 6px;">
                        ${isPaid ? `
                        <div style="display: inline-block; border: 2px solid #000; color: #000; font-weight: 900; font-size: 13px; letter-spacing: 2px; padding: 4px 14px; border-radius: 4px;">
                            ★ ★ ★ L U N A S ★ ★ ★
                        </div>` : (isPartial ? `
                        <div style="display: inline-block; border: 2px solid #000; color: #000; font-weight: 800; font-size: 12px; letter-spacing: 1px; padding: 4px 12px; border-radius: 4px;">
                            STATUS: DICICIL (BELUM LUNAS)
                        </div>` : `
                        <div style="display: inline-block; border: 2px solid #000; color: #000; font-weight: 800; font-size: 12px; letter-spacing: 1px; padding: 4px 12px; border-radius: 4px;">
                            STATUS: BELUM DIBAYAR
                        </div>`)}
                    </div>
                </div>

                <!-- Payment History / Riwayat Pembayaran (if any) -->
                ${payments && payments.length > 0 ? `
                <div style="border-top: 1px dashed #000; padding: 6px 0 4px; margin-top: 4px; color: #000;">
                    <div style="font-size: 9.5px; font-weight: 800; text-align: center; letter-spacing: 0.5px; margin-bottom: 4px; color: #000;">
                        · · · RIWAYAT ANGSURAN · · ·
                    </div>
                    <table style="width: 100%; font-size: 10px; font-family: 'JetBrains Mono', monospace; border-collapse: collapse; color: #000;">
                        ${payments.map(p => `
                        <tr>
                            <td style="color: #000; padding: 1.5px 0;">${p.date}</td>
                            <td style="text-align: right; font-weight: 700; color: #000;">+Rp ${Number(p.amount).toLocaleString('id-ID')}</td>
                        </tr>
                        ${p.notes ? `<tr><td colspan="2" style="font-size: 9px; color: #000; font-style: italic; padding-bottom: 3px;">↳ ${p.notes}</td></tr>` : ''}
                        `).join('')}
                    </table>
                </div>` : ''}

                <!-- Code 39 Barcode -->
                <div style="text-align: center; margin: 10px 0 4px; border-top: 1px dashed #000; padding-top: 8px;">
                    <div style="margin: 0 auto; display: flex; justify-content: center;">
                        ${barcodeSvg}
                    </div>
                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 9.5px; font-weight: 700; letter-spacing: 1px; margin-top: 2px; color: #000;">
                        * ${invoiceNo} *
                    </div>
                </div>

                <!-- QR Code & E-Struk Verification (Monochrome) -->
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin: 8px 0; padding: 5px; background: #fff; border: 1px solid #000; border-radius: 4px;">
                    <div class="receipt-qr-target" data-qr="${qrData}" style="width: 60px; height: 60px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;"></div>
                    <div style="text-align: left; font-size: 9px; color: #000; line-height: 1.3;">
                        <div style="font-weight: 800; color: #000; text-transform: uppercase;">E-Struk Kasbon Sah</div>
                        <div>Scan QR untuk verifikasi sisa tagihan & riwayat angsuran.</div>
                    </div>
                </div>

                <!-- Footer Note -->
                <div style="border-top: 1px dashed #000; padding-top: 6px; margin-top: 6px; text-align: center; font-size: 9.5px; color: #000; line-height: 1.35;">
                    <div style="font-weight: 700; color: #000;">Harap simpan bukti ini sebagai catatan kasbon yang sah.</div>
                    <div style="margin-top: 3px; font-weight: 800;">Terima Kasih atas Kerjasamanya!</div>
                    <div style="margin-top: 5px; font-size: 8.5px; color: #000; letter-spacing: 0.5px;">
                        *** SISTEM WARUNG POS PINTAR ***
                    </div>
                </div>

            </div>
        `;

        return html;
    }

    /**
     * System Print Dialog Fallback (window.print)
     */
    printSystemDialog(receiptData) {
        const html = this.generateReceiptHtml(receiptData, { isPrint: true });
        this.executePrintFrame(html);
    }

    /**
     * Print Kasbon via System Print
     */
    printKasbonSystemDialog(kasbonData) {
        const html = this.generateKasbonReceiptHtml(kasbonData, { isPrint: true });
        this.executePrintFrame(html);
    }

    /**
     * Clean iframe isolated print execution to prevent page mess
     */
    executePrintFrame(html) {
        let printFrame = document.getElementById('thermal-print-iframe');
        if (!printFrame) {
            printFrame = document.createElement('iframe');
            printFrame.id = 'thermal-print-iframe';
            printFrame.style.position = 'fixed';
            printFrame.style.right = '0';
            printFrame.style.bottom = '0';
            printFrame.style.width = '0';
            printFrame.style.height = '0';
            printFrame.style.border = '0';
            document.body.appendChild(printFrame);
        }

        const is80 = (this.paperSize === '80mm');
        const doc = printFrame.contentWindow.document;
        doc.open();
        doc.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Cetak Struk Warung</title>
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@400;600;700;800&display=swap" rel="stylesheet">
                <style>
                    @page {
                        size: ${is80 ? '80mm' : '58mm'} auto;
                        margin: 0;
                    }
                    * {
                        box-sizing: border-box;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                        background: #fff;
                        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    svg {
                        max-width: 100%;
                    }
                </style>
                <script src="${window.location.origin}/public/js/qrcode.min.js"></script>
            </head>
            <body>
                ${html}
                <script>
                    window.onload = function() {
                        const targets = document.querySelectorAll('.receipt-qr-target');
                        targets.forEach(el => {
                            const text = el.getAttribute('data-qr');
                            if (text && window.QRCode) {
                                el.innerHTML = '';
                                new QRCode(el, {
                                    text: text,
                                    width: 56,
                                    height: 56,
                                    colorDark: "#000000",
                                    colorLight: "#ffffff",
                                    correctLevel: QRCode.CorrectLevel.M
                                });
                            }
                        });
                        setTimeout(() => {
                            window.focus();
                            window.print();
                        }, 250);
                    };
                </script>
            </body>
            </html>
        `);
        doc.close();
    }

    /**
     * Generate formatted WhatsApp text receipt (Supports both Sales & Kasbon)
     */
    generateWhatsAppReceiptText(data) {
        const store = data.store || {};
        
        // If Kasbon
        if (data.type === 'kasbon' || data.kasbon) {
            const kasbon = data.kasbon || {};
            const payments = data.payments || [];
            const isPaid = (kasbon.status === 'paid' || Number(kasbon.remaining_debt) <= 0);

            let text = `🧾 *BUKTI CATATAN KASBON*\n`;
            text += `*${(store.name || 'TOKOMU').toUpperCase()}*\n`;
            if (store.tagline) text += `_${store.tagline}_\n`;
            if (store.address) text += `${store.address}\n`;
            if (store.phone) text += `WA: ${store.phone}\n`;
            text += `================================\n`;
            text += `No Bukti    : ${kasbon.invoice_no}\n`;
            text += `Tanggal     : ${kasbon.created_at}\n`;
            text += `Pelanggan   : *${kasbon.customer_name}*\n`;
            if (kasbon.customer_phone) text += `No HP/WA    : ${kasbon.customer_phone}\n`;
            text += `Jatuh Tempo : ${kasbon.due_date || '-'}\n`;
            text += `--------------------------------\n`;
            text += `Total Utang : Rp ${Number(kasbon.total_debt).toLocaleString('id-ID')}\n`;
            text += `Sudah Bayar : Rp ${Number(kasbon.paid_amount).toLocaleString('id-ID')}\n`;
            text += `================================\n`;
            text += `*SISA KASBON : Rp ${Number(kasbon.remaining_debt).toLocaleString('id-ID')}*\n`;
            text += `*STATUS      : ${isPaid ? '★★★ LUNAS ★★★' : (kasbon.status === 'partial' ? 'DICICIL (BELUM LUNAS)' : 'BELUM DIBAYAR')}*\n`;
            text += `================================\n`;

            if (payments.length > 0) {
                text += `*Riwayat Angsuran:*\n`;
                payments.forEach(p => {
                    text += `• ${p.date}: +Rp ${Number(p.amount).toLocaleString('id-ID')}${p.notes ? ' (' + p.notes + ')' : ''}\n`;
                });
                text += `--------------------------------\n`;
            }

            text += `_Harap simpan pesan ini sebagai bukti catatan kasbon yang sah._\n`;
            text += `_Terima kasih atas kerjasamanya! 🙏_\n`;
            return text;
        }

        // Otherwise POS Sales
        const trx = data.transaction || {};
        const items = data.items || [];

        let text = `🧾 *STRUK BELANJA RESMI*\n`;
        text += `*${(store.name || 'TOKOMU').toUpperCase()}*\n`;
        if (store.tagline) text += `_${store.tagline}_\n`;
        if (store.address) text += `${store.address}\n`;
        if (store.phone) text += `WA: ${store.phone}\n`;
        text += `================================\n`;
        text += `No. Struk : ${trx.invoice_no}\n`;
        text += `Tanggal   : ${trx.date}\n`;
        text += `Pelanggan : ${trx.customer_name}\n`;
        text += `Metode    : ${trx.payment_method}\n`;
        text += `--------------------------------\n`;

        items.forEach(it => {
            text += `*${it.name}*\n`;
            text += `  ${it.qty} ${it.unit} x Rp ${Number(it.price).toLocaleString('id-ID')} = *Rp ${Number(it.subtotal).toLocaleString('id-ID')}*\n`;
        });

        text += `--------------------------------\n`;
        text += `Subtotal : Rp ${Number(trx.subtotal).toLocaleString('id-ID')}\n`;
        if (trx.discount_amount > 0) {
            text += `Diskon   : -Rp ${Number(trx.discount_amount).toLocaleString('id-ID')}\n`;
        }
        text += `================================\n`;
        text += `*TOTAL    : Rp ${Number(trx.grand_total).toLocaleString('id-ID')}*\n`;

        if (trx.payment_method === 'CASH' || trx.payment_method === 'TUNAI') {
            text += `Tunai    : Rp ${Number(trx.cash_amount).toLocaleString('id-ID')}\n`;
            text += `Kembali  : Rp ${Number(trx.change_amount).toLocaleString('id-ID')}\n`;
        } else if (trx.payment_method === 'KASBON') {
            text += `*STATUS   : KASBON / UTANG*\n`;
        } else {
            text += `Status   : LUNAS (${trx.payment_method})\n`;
        }

        text += `================================\n`;
        text += `_Terima kasih telah berbelanja di Warung Kami! 🙏_\n`;
        text += `_Barang yang sudah dibeli tidak dapat ditukar._\n`;

        return text;
    }
}

window.ThermalPrinterManager = ThermalPrinterManager;
