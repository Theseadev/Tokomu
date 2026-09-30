/**
 * Camera Barcode Scanner Controller
 * Uses html5-qrcode library for real-time mobile camera barcode detection (EAN-13, UPC, Code-128, QR)
 */

class CameraScannerController {
    constructor() {
        this.html5QrCode = null;
        this.isScanning = false;
        this.cameras = [];
        this.currentCameraIndex = 0;
        this.torchOn = false;
        this.continuous = true;
        this.isCooldown = false;
        this.lastScannedCode = '';
        this.lastScanTime = 0;
        this.onScanCallback = null;
        this.options = {};
    }

    async init() {
        if (!window.Html5Qrcode) {
            console.error('Html5Qrcode library not loaded');
            return false;
        }
        return true;
    }

    async open(options = {}) {
        this.options = {
            title: options.title || 'Scan Barcode Produk',
            continuous: options.continuous !== false,
            showCartInfo: options.showCartInfo || false,
            onScan: options.onScan || null
        };
        this.onScanCallback = this.options.onScan;
        this.continuous = this.options.continuous;

        const modal = document.getElementById('camera-scanner-modal');
        if (!modal) return;

        // Set title and UI
        document.getElementById('camera-modal-title').innerText = this.options.title;
        const contCheckbox = document.getElementById('camera-continuous-mode');
        if (contCheckbox) contCheckbox.checked = this.continuous;

        const cartBox = document.getElementById('camera-cart-status');
        if (cartBox) {
            cartBox.classList.toggle('hidden', !this.options.showCartInfo);
            this.updateCartStatus();
        }

        document.getElementById('camera-error-box').classList.add('hidden');
        document.getElementById('camera-scan-feedback').classList.add('hidden');
        document.getElementById('camera-manual-barcode-input').value = '';

        modal.classList.remove('hidden');

        try {
            await this.startScanner();
        } catch (err) {
            this.handleError(err);
        }
    }

    async startScanner() {
        if (this.isScanning) {
            await this.stopScanner();
        }

        const readerElem = document.getElementById('camera-reader-viewport');
        if (!readerElem) return;
        readerElem.innerHTML = ''; // Clear previous video

        this.html5QrCode = new Html5Qrcode('camera-reader-viewport');

        // Get available cameras
        try {
            this.cameras = await Html5Qrcode.getCameras();
        } catch (e) {
            console.warn('Could not enumerate cameras, falling back to facingMode:', e);
            this.cameras = [];
        }

        // Configuration
        const config = {
            fps: 15,
            qrbox: (viewfinderWidth, viewfinderHeight) => {
                // Wide rectangle tailored for 1D retail barcodes (EAN-13/UPC)
                const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                const width = Math.min(300, Math.floor(viewfinderWidth * 0.85));
                const height = Math.min(180, Math.floor(width * 0.6));
                return { width, height };
            },
            aspectRatio: 1.0,
            formatsToSupport: [
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E,
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.QR_CODE
            ]
        };

        // Select camera or facingMode
        let cameraParam = { facingMode: 'environment' };
        if (this.cameras && this.cameras.length > 0) {
            // Prefer rear/environment camera
            if (this.currentCameraIndex >= this.cameras.length) {
                this.currentCameraIndex = 0;
            }
            cameraParam = this.cameras[this.currentCameraIndex].id;
        }

        // Start scanning stream
        await this.html5QrCode.start(
            cameraParam,
            config,
            (decodedText, decodedResult) => {
                this.handleDecoded(decodedText);
            },
            (errorMessage) => {
                // Parse errors are normal for each non-barcode frame, suppress
            }
        );

        this.isScanning = true;
        this.torchOn = false;
        this.updateTorchUI();

        // Check if camera switch button should be visible
        const btnSwitch = document.getElementById('btn-camera-switch');
        if (btnSwitch) {
            btnSwitch.classList.toggle('hidden', this.cameras.length <= 1);
        }
    }

    handleDecoded(code) {
        if (!code || this.isCooldown) return;

        const now = Date.now();
        // Cooldown of 1.2s for identical code to prevent duplicate multi-scan
        if (code === this.lastScannedCode && (now - this.lastScanTime < 1400)) {
            return;
        }

        this.lastScannedCode = code;
        this.lastScanTime = now;
        this.isCooldown = true;

        // Visual and Audio feedback
        try {
            if (window.AppAudio) window.AppAudio.beep(1200, 0.08);
            if (navigator.vibrate) navigator.vibrate(80);
        } catch (e) {}

        this.showFeedback(code);

        // Execute callback
        if (typeof this.onScanCallback === 'function') {
            this.onScanCallback(code);
        }

        // Update live cart display
        this.updateCartStatus();

        // Cooldown reset
        setTimeout(() => {
            this.isCooldown = false;
        }, 1200);

        // If not in continuous scan mode, auto-close
        if (!this.continuous) {
            setTimeout(() => {
                this.close();
            }, 600);
        }
    }

    showFeedback(code) {
        const banner = document.getElementById('camera-scan-feedback');
        const textElem = document.getElementById('camera-scan-feedback-text');
        if (!banner || !textElem) return;

        // Check if catalog has product name
        let display = `Barcode: ${code}`;
        if (window.catalog && Array.isArray(window.catalog)) {
            const found = window.catalog.find(p => p.barcode === code);
            if (found) {
                display = `✓ ${found.name}`;
            }
        }

        textElem.innerText = display;
        banner.classList.remove('hidden');

        // Hide feedback banner after 2.5s
        clearTimeout(this._feedbackTimer);
        this._feedbackTimer = setTimeout(() => {
            banner.classList.add('hidden');
        }, 2200);
    }

    updateCartStatus() {
        if (!this.options.showCartInfo || !window.cart) return;
        const countElem = document.getElementById('camera-cart-items-count');
        const totalElem = document.getElementById('camera-cart-total');
        if (!countElem || !totalElem) return;

        const totalPieces = window.cart.reduce((sum, it) => sum + Number(it.qty || 0), 0);
        const subtotal = window.cart.reduce((sum, it) => sum + (it.qty * it.sell_price), 0);
        const discount = parseFloat(document.getElementById('pos-discount')?.value) || 0;
        const grandTotal = Math.max(0, subtotal - discount);

        countElem.innerText = `${totalPieces} Barang (${window.cart.length} jenis)`;
        totalElem.innerText = `Rp ${Number(grandTotal).toLocaleString('id-ID')}`;
    }

    toggleContinuous(val) {
        this.continuous = !!val;
    }

    async toggleTorch() {
        if (!this.html5QrCode || !this.isScanning) return;
        try {
            this.torchOn = !this.torchOn;
            await this.html5QrCode.applyVideoConstraints({
                advanced: [{ torch: this.torchOn }]
            });
            this.updateTorchUI();
        } catch (err) {
            console.warn('Torch not supported on this device/camera:', err);
            this.torchOn = false;
            this.updateTorchUI();
            alert('Lampu flash/senter tidak didukung pada kamera ini.');
        }
    }

    updateTorchUI() {
        const btnTorch = document.getElementById('btn-camera-torch');
        if (!btnTorch) return;
        if (this.torchOn) {
            btnTorch.className = 'p-2 rounded-xl bg-amber-400 text-slate-900 transition active-press';
        } else {
            btnTorch.className = 'p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white transition active-press';
        }
    }

    async switchCamera() {
        if (this.cameras.length <= 1) return;
        this.currentCameraIndex = (this.currentCameraIndex + 1) % this.cameras.length;
        await this.startScanner();
    }

    submitManual() {
        const input = document.getElementById('camera-manual-barcode-input');
        const code = input.value.trim();
        if (!code) {
            alert('Harap ketik barcode terlebih dahulu');
            return;
        }
        input.value = '';
        this.handleDecoded(code);
    }

    handleError(err) {
        console.error('Camera Scanner Error:', err);
        const errBox = document.getElementById('camera-error-box');
        const errTitle = document.getElementById('camera-error-title');
        const errDesc = document.getElementById('camera-error-desc');
        if (errBox) {
            errBox.classList.remove('hidden');
            if (err?.name === 'NotAllowedError' || String(err).includes('Permission')) {
                errTitle.innerText = 'Izin Kamera Diblokir';
                errDesc.innerText = 'Harap izinkan akses kamera di ikon gembok pada address bar browser Anda untuk memindai barcode.';
            } else if (err?.name === 'NotFoundError' || String(err).includes('NotFound')) {
                errTitle.innerText = 'Kamera Tidak Ditemukan';
                errDesc.innerText = 'Perangkat tidak memiliki kamera yang aktif. Anda dapat mengetikkan barcode secara manual di bawah.';
            } else {
                errTitle.innerText = 'Gagal Mengakses Kamera';
                errDesc.innerText = (err.message || String(err));
            }
        }
    }

    async retry() {
        document.getElementById('camera-error-box')?.classList.add('hidden');
        try {
            await this.startScanner();
        } catch (e) {
            this.handleError(e);
        }
    }

    async stopScanner() {
        if (this.html5QrCode && this.isScanning) {
            try {
                await this.html5QrCode.stop();
                this.html5QrCode.clear();
            } catch (e) {
                console.warn('Error stopping scanner:', e);
            }
        }
        this.isScanning = false;
        this.torchOn = false;
    }

    async close() {
        await this.stopScanner();
        const modal = document.getElementById('camera-scanner-modal');
        if (modal) modal.classList.add('hidden');
    }
}

// Global Singleton Instance
window.cameraScanner = new CameraScannerController();
