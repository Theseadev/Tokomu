/**
 * Camera Barcode Scanner Controller (Optimized for Fast Auto-Focus & Macro Barcodes)
 * Uses html5-qrcode library with native hardware BarcodeDetector, continuous auto-focus,
 * tap-to-focus, and optical/digital zoom presets (1x, 1.5x, 2x, 2.5x).
 */

class CameraScannerController {
    constructor() {
        this.html5QrCode = null;
        this.isScanning = false;
        this.cameras = [];
        this.currentCameraIndex = 0;
        this.videoTrack = null;
        this.torchOn = false;
        this.continuous = true;
        this.isCooldown = false;
        this.lastScannedCode = '';
        this.lastScanTime = 0;
        this.onScanCallback = null;
        this.options = {};
        this.currentZoom = 1.5;
        this.supportsHardwareZoom = false;
        this.touchDistanceStart = 0;
        this.zoomStart = 1.0;
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

        // 1. Get available cameras
        try {
            const rawCameras = await Html5Qrcode.getCameras();
            if (rawCameras && rawCameras.length > 0) {
                // Filter and prioritize standard back/rear camera (skip ultra-wide or front if possible)
                this.cameras = rawCameras.sort((a, b) => {
                    const labelA = (a.label || '').toLowerCase();
                    const labelB = (b.label || '').toLowerCase();
                    const isBackA = labelA.includes('back') || labelA.includes('rear') || labelA.includes('environment') || labelA.includes('0, facing back');
                    const isBackB = labelB.includes('back') || labelB.includes('rear') || labelB.includes('environment') || labelB.includes('0, facing back');
                    const isWideA = labelA.includes('wide') || labelA.includes('ultra');
                    const isWideB = labelB.includes('wide') || labelB.includes('ultra');

                    if (isBackA && !isBackB) return -1;
                    if (!isBackA && isBackB) return 1;
                    if (!isWideA && isWideB) return -1;
                    if (isWideA && !isWideB) return 1;
                    return 0;
                });
            } else {
                this.cameras = [];
            }
        } catch (e) {
            console.warn('Could not enumerate cameras, falling back to facingMode:', e);
            this.cameras = [];
        }

        // 2. High-precision Barcode Configuration
        const config = {
            fps: 24, // High framerate minimizes hand-shake motion blur
            qrbox: (viewfinderWidth, viewfinderHeight) => {
                // Generous wide rectangle matching retail barcodes (EAN-13, UPC, Code-128, QR)
                const width = Math.min(380, Math.floor(viewfinderWidth * 0.92));
                const height = Math.min(220, Math.floor(width * 0.62));
                return { width, height };
            },
            aspectRatio: 1.0,
            disableFlip: false,
            experimentalFeatures: {
                useBarCodeDetectorIfSupported: true // Native hardware acceleration (10x faster & sharper)
            },
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

        // 3. HD Video Constraints with Continuous Auto-Focus
        let cameraParam = {
            facingMode: { ideal: 'environment' },
            focusMode: { ideal: 'continuous' },
            width: { min: 640, ideal: 1280, max: 1920 },
            height: { min: 480, ideal: 720, max: 1080 }
        };

        if (this.cameras && this.cameras.length > 0) {
            if (this.currentCameraIndex >= this.cameras.length) {
                this.currentCameraIndex = 0;
            }
            cameraParam = {
                deviceId: { exact: this.cameras[this.currentCameraIndex].id },
                focusMode: { ideal: 'continuous' },
                width: { min: 640, ideal: 1280, max: 1920 },
                height: { min: 480, ideal: 720, max: 1080 }
            };
        }

        // 4. Start scanning stream
        await this.html5QrCode.start(
            cameraParam,
            config,
            (decodedText, decodedResult) => {
                this.handleDecoded(decodedText);
            },
            (errorMessage) => {
                // Ignore per-frame decode misses
            }
        );

        this.isScanning = true;
        this.torchOn = false;
        this.updateTorchUI();

        // 5. Inspect and configure active MediaStreamTrack
        const video = document.querySelector('#camera-reader-viewport video');
        if (video && video.srcObject) {
            const tracks = video.srcObject.getVideoTracks();
            if (tracks && tracks.length > 0) {
                this.videoTrack = tracks[0];
                await this.applyContinuousFocus();
                this.initZoomAndPinch(video);
            }
        }

        // Check if camera switch button should be visible
        const btnSwitch = document.getElementById('btn-camera-switch');
        if (btnSwitch) {
            btnSwitch.classList.toggle('hidden', this.cameras.length <= 1);
        }
    }

    async applyContinuousFocus() {
        if (!this.videoTrack) return;
        try {
            const caps = this.videoTrack.getCapabilities ? this.videoTrack.getCapabilities() : {};
            if (caps.focusMode && caps.focusMode.includes('continuous')) {
                await this.videoTrack.applyConstraints({
                    advanced: [{ focusMode: 'continuous' }]
                });
            }
        } catch (e) {
            console.warn('Continuous focus error:', e);
        }
    }

    async refocus() {
        // Visual focus ring in center of viewfinder
        const container = document.getElementById('camera-reader-viewport');
        if (container) {
            const rect = container.getBoundingClientRect();
            this.showFocusRing(rect.width / 2, rect.height / 2);
        }

        if (!this.videoTrack) return;
        try {
            const caps = this.videoTrack.getCapabilities ? this.videoTrack.getCapabilities() : {};
            if (caps.focusMode) {
                // Cycle focus mode to trigger immediate hardware lens recalibration
                await this.videoTrack.applyConstraints({
                    advanced: [{ focusMode: 'single-shot' }]
                }).catch(() => {});
                await new Promise(r => setTimeout(r, 90));
                await this.videoTrack.applyConstraints({
                    advanced: [{ focusMode: 'continuous' }]
                }).catch(() => {});
            }
        } catch (err) {
            console.warn('Refocus constraint failed:', err);
        }
    }

    handleTapToFocus(e) {
        const container = document.getElementById('camera-reader-viewport');
        if (!container) return;

        const rect = container.getBoundingClientRect();
        const clientX = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : null);
        const clientY = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : null);

        if (clientX !== null && clientY !== null) {
            const x = clientX - rect.left;
            const y = clientY - rect.top;
            this.showFocusRing(x, y);
        }

        this.refocus();
    }

    showFocusRing(x, y) {
        const container = document.getElementById('camera-reader-viewport');
        if (!container) return;

        // Remove old rings
        const oldRings = container.querySelectorAll('.camera-focus-ring');
        oldRings.forEach(r => r.remove());

        const ring = document.createElement('div');
        ring.className = 'camera-focus-ring';
        ring.style.left = `${x}px`;
        ring.style.top = `${y}px`;
        container.appendChild(ring);

        setTimeout(() => ring.remove(), 850);
    }

    initZoomAndPinch(video) {
        const caps = this.videoTrack?.getCapabilities ? this.videoTrack.getCapabilities() : {};
        this.supportsHardwareZoom = !!caps.zoom;

        // Setup pinch-to-zoom on touch devices
        const viewport = document.getElementById('camera-reader-viewport');
        if (viewport && !viewport._pinchBound) {
            viewport._pinchBound = true;
            viewport.addEventListener('touchstart', (e) => {
                if (e.touches.length === 2) {
                    this.touchDistanceStart = Math.hypot(
                        e.touches[0].pageX - e.touches[1].pageX,
                        e.touches[0].pageY - e.touches[1].pageY
                    );
                    this.zoomStart = this.currentZoom;
                }
            }, { passive: true });

            viewport.addEventListener('touchmove', (e) => {
                if (e.touches.length === 2 && this.touchDistanceStart > 0) {
                    const dist = Math.hypot(
                        e.touches[0].pageX - e.touches[1].pageX,
                        e.touches[0].pageY - e.touches[1].pageY
                    );
                    const ratio = dist / this.touchDistanceStart;
                    const target = Math.min(3.0, Math.max(1.0, this.zoomStart * ratio));
                    this.setZoom(parseFloat(target.toFixed(1)));
                }
            }, { passive: true });

            viewport.addEventListener('touchend', () => {
                this.touchDistanceStart = 0;
            }, { passive: true });
        }

        // Apply 1.5x zoom as standard default for crystal-sharp retail barcodes
        // (Prevents holding phone too close (<10 cm) which causes optical macro blur)
        setTimeout(() => {
            this.setZoom(1.5);
        }, 250);
    }

    async setZoom(level) {
        this.currentZoom = level;
        if (this.videoTrack && this.supportsHardwareZoom) {
            const caps = this.videoTrack.getCapabilities ? this.videoTrack.getCapabilities() : {};
            const minZ = caps.zoom?.min || 1;
            const maxZ = caps.zoom?.max || 5;
            const targetZ = Math.min(maxZ, Math.max(minZ, level));
            try {
                await this.videoTrack.applyConstraints({
                    advanced: [{ zoom: targetZ }]
                });
            } catch (e) {
                console.warn('Hardware zoom failed, using CSS fallback:', e);
                this.applyCssZoom(level);
            }
        } else {
            this.applyCssZoom(level);
        }
        this.updateZoomUI(level);
    }

    applyCssZoom(level) {
        const video = document.querySelector('#camera-reader-viewport video');
        if (video) {
            video.style.transform = level > 1 ? `scale(${level})` : 'none';
            video.style.transformOrigin = 'center center';
        }
    }

    updateZoomUI(level) {
        const buttons = {
            1.0: document.getElementById('btn-zoom-1x'),
            1.5: document.getElementById('btn-zoom-15x'),
            2.0: document.getElementById('btn-zoom-2x'),
            2.5: document.getElementById('btn-zoom-25x'),
        };
        Object.keys(buttons).forEach(k => {
            const btn = buttons[k];
            if (!btn) return;
            const isCurrent = Math.abs(parseFloat(k) - level) < 0.25;
            if (isCurrent) {
                btn.className = 'px-2 py-0.5 text-xs font-bold rounded-lg bg-emerald-500 text-slate-950 font-black shadow-xs transition active-press';
            } else {
                btn.className = 'px-2 py-0.5 text-xs font-bold rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 transition active-press';
            }
        });
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

        // Hide feedback banner after 2.2s
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
        this.videoTrack = null;
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
