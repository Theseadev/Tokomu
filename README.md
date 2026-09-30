# 🛒 Tokomu - Sistem Kasir Pintar (POS) & Multi-Toko

Aplikasi web Sistem Kasir (Point of Sale) modern yang dirancang untuk mendukung multi-toko (multi-tenant), keranjang melayang, pencatatan kasbon, dan integrasi langsung ke **printer mini thermal (Bluetooth / USB Serial)** untuk mencetak struk belanja fisik secara instan.

Dibangun dengan **Flight PHP** yang sangat cepat, ringan, dan elegan, dipasang langsung di folder `C:\laragon\www\warung-pos`.

---

## 🌟 Fitur Utama

### 1. 🖨️ Integrasi Printer Mini Thermal (Bluetooth, USB & Dialog Cetak)
* **Web Bluetooth API**: Menghubungkan langsung ke printer thermal mini Bluetooth (misal: *Panda, Iware, RPP02N, GOOJPRT, PT-210, MPT-II*, dsb.) tanpa driver rumit langsung dari browser (Chrome / Edge).
* **Web Serial USB**: Sambungan kabel USB langsung dari laptop/PC ke port printer thermal.
* **ESC/POS Command Encoder**: Menghasilkan perintah biner ESC/POS asli dengan teks tebal, perataan tengah/kiri/kanan, garis putus-putus rapi, dan perintah potong kertas (*paper cut*).
* **Ukuran Kertas Fleksibel**: Mendukung kertas thermal **58 mm** (32 kolom) dan **80 mm** (48 kolom).
* **Cetak Dialog Sistem / PDF**: Kompatibel dengan semua printer yang terpasang di Windows atau cetak ke PDF.
* **Kirim Struk via WhatsApp**: Kasir dapat langsung mengirimkan struk digital berformat rapi ke nomor WA pembeli yang tidak membutuhkan struk kertas.

### 2. ⚡ Kasir POS Cepat & Ramah Sentuh (Touch-Friendly)
* **Katalog Bawaan 40+ Produk Sembako**: Beras Rojo Lele, Minyakita, Gulaku, Telur kg/butir, Indomie Goreng/Kuah, Kopi Kapal Api renteng, Gas LPG 3kg, Aqua Galon, Sabun, Rokok, Tepung, dll.
* **Pencarian Kilat & Barcode Scanner**: Pencarian real-time nama barang atau barcode. Mendukung **alat scanner barcode fisik USB (barcode gun)** secara otomatis tanpa perlu klik mouse!
* **Item Non-Katalog / Manual**: Tambahkan barang manual tanpa stok dengan cepat (misal: sayuran segar Rp 5.000, es batu Rp 1.000, kerupuk kaleng Rp 1.000).
* **Tombol Cepat Pembayaran Tunai**: `[Uang Pas]`, `[10k]`, `[20k]`, `[50k]`, `[100k]`, `[200k]`, dan perhitungan uang kembalian otomatis dengan angka besar.
* **Metode Pembayaran Lengkap**: Tunai (Cash), QRIS, Transfer Bank, dan **Kasbon**.
* **Efek Suara Kasir**: Notifikasi suara *beep* sintetis otomatis saat scan barcode atau pembayaran berhasil (100% offline via Web Audio API).

### 3. 📖 Buku Catatan Kasbon (Utang Pelanggan)
* Transaksi yang dipilih dengan metode **Kasbon** akan langsung tercatat otomatis ke buku kasbon.
* Menampilkan daftar nama pelanggan, nomor telepon, sisa tagihan, dan tanggal jatuh tempo.
* Fitur cicilan dan pelunasan kasbon.
* **Cetak Bukti Kasbon**: Kasir dapat mencetak bukti tagihan atau bukti pelunasan kasbon ke printer mini thermal!

### 4. 📦 Manajemen Produk & Stok Menipis
* Pemantauan sisa stok dengan indikator peringatan warna kuning/merah saat stok di bawah batas minimal.
* Tombol **Masuk Barang / Restock** cepat untuk kulakan barang baru.
* Kalkulator keuntungan (*profit margin*) otomatis saat menginput harga beli (modal) dan harga jual.

### 5. 📊 Laporan & Riwayat Penjualan
* Ringkasan harian: **Total Omzet**, **Keuntungan Bersih (Laba Kotor)**, **Jumlah Transaksi**, dan **Total Kasbon Belum Lunas**.
* Grafik tren penjualan & keuntungan 7 hari terakhir.
* Daftar **Produk Terlaris** berdasarkan jumlah terjual dan nilai rupiah.
* Riwayat transaksi lengkap dengan fitur **Cetak Ulang Struk (Re-print)** kapan saja.
* Fitur **Export Laporan ke Excel / CSV**.

### 6. ⚙️ Pengaturan & Pencadangan Data
* Kustomisasi nama warung, slogan, alamat, nomor telepon, dan catatan kaki struk.
* Pengaturan cetak otomatis (*auto-print*) setelah kasir menekan tombol bayar.
* **Backup & Restore Database**: Cadangkan seluruh data ke format file `.json` dan pulihkan kapan saja.
* **Reset Data Uji Coba**: Kembalikan katalog ke produk sembako bawaan.

---

## 🚀 Cara Menjalankan Aplikasi

Aplikasi berada di: `C:\laragon\www\warung-pos`

### Cara 1: Menggunakan Laragon (Rekomendasi)
1. Buka aplikasi **Laragon** di komputer Anda.
2. Klik tombol **Start All** di Laragon (untuk menyalakan Apache/Nginx).
3. Buka browser (Google Chrome atau Microsoft Edge) dan ketik URL:
   ```
   http://localhost/warung-pos
   ```
   *(Atau `http://warung-pos.test` jika fitur auto-virtualhost Laragon aktif)*.

### Cara 2: Menjalankan Menggunakan File Batch (1-Klik)
1. Buka folder `C:\laragon\www\warung-pos` di Windows Explorer.
2. Klik ganda file **`start-pos.bat`**.
3. Browser akan terbuka otomatis ke alamat:
   ```
   http://localhost:8080
   ```

---

## 🖨️ Panduan Menghubungkan Printer Mini Thermal

### A. Printer Bluetooth (RPP02N, Panda, Iware, GOOJPRT, dll.)
1. Pastikan Bluetooth di laptop/PC Anda sudah aktif dan printer thermal sudah dinyalakan.
2. Buka aplikasi POS di Google Chrome atau Microsoft Edge.
3. Klik tombol badge **Printer Thermal** di pojok kanan atas navigasi.
4. Klik **Hubungkan** pada bagian **Printer Mini Bluetooth**.
5. Jendela pencarian Bluetooth bawaan browser akan muncul:
   * Pilih nama printer Anda (misalnya `RPP02N`, `Printer001`, `MPT-II`, atau `Bluetooth Printer`).
   * Klik **Pair / Hubungkan**.
6. Status di pojok kanan atas akan berubah menjadi hijau: `Bluetooth: [Nama Printer]`.
7. Klik tombol **Tes Cetak Struk** untuk mencoba print struk pengujian.

### B. Printer Kabel USB
1. Tancapkan kabel USB printer ke port USB laptop/PC.
2. Klik tombol **Printer Thermal** di pojok kanan atas navigasi web.
3. Klik **Pilih Port USB** pada opsi **Printer USB Kabel (Serial)**.
4. Pilih port USB printer yang terdeteksi, lalu klik **Connect**.

### C. Dialog Cetak Browser / Windows Print Driver
* Jika printer Anda sudah memiliki driver resmi di Windows atau ingin mencetak ke kertas A4/PDF, pilih opsi **Cetak Windows / Browser Print**. Sistem akan memunculkan dialog cetak standar yang sudah diatur dengan layout monospaced struk 58mm/80mm.

---

## 📁 Struktur Berkas Proyek

```
C:\laragon\www\warung-pos\
├── app/
│   ├── Database.php              # Pengelola koneksi SQLite PDO, migrasi & data awal sembako
│   └── Controllers/
│       ├── PosController.php     # Logika kasir, checkout, pengurangan stok & format struk
│       ├── ProductController.php # CRUD produk, kulakan restock & pantau stok
│       ├── KasbonController.php  # Buku utang pelanggan, cicilan & cetak bukti kasbon
│       ├── ReportController.php  # Laporan omzet, laba kotor, grafik & export CSV
│       └── SettingController.php # Pengaturan warung, printer, backup & restore
├── data/
│   └── warung.db                 # Database SQLite mandiri (aman & mudah dicadangkan)
├── public/
│   ├── css/
│   │   └── app.css               # Gaya tampilan kasir & layout kertas struk thermal
│   └── js/
│       ├── escpos.js             # Encoder biner perintah ESC/POS printer thermal
│       └── thermal-printer.js    # Pengelola Web Bluetooth, Web Serial & dialog print
├── views/
│   ├── layout.php                # Tata letak dasar, modal printer & status koneksi
│   ├── pos.php                   # Layar Kasir Utama (sentuh & barcode)
│   ├── products.php              # Halaman kelola produk & stok
│   ├── kasbon.php                # Halaman buku kasbon & pembayaran utang
│   ├── reports.php               # Halaman laporan laba/rugi & riwayat transaksi
│   └── settings.php              # Halaman pengaturan toko & thermal printer
├── .htaccess                     # Konfigurasi Apache mod_rewrite Laragon
├── composer.json                 # Konfigurasi dependensi Flight PHP
├── index.php                     # Router utama aplikasi Flight PHP
├── start-pos.bat                 # Script peluncur 1-klik untuk Windows
└── README.md                     # Petunjuk penggunaan lengkap
```

---

## 💡 Tips Penggunaan di Warung
* **Barcode Scanner**: Arahkan scanner barcode USB ke barang apa saja, sistem akan langsung memasukkan barang tersebut ke keranjang belanja secara otomatis dengan suara *beep*.
* **Kasbon**: Saat tetangga atau pelanggan langganan berbelanja dan belum membawa uang, cukup ganti nama pelanggan, pilih metode **Kasbon**, dan klik **Bayar**. Tagihan akan otomatis masuk ke Buku Kasbon dan struk bukti kasbon bisa langsung dicetak untuk diberikan ke pembeli.
* **Kertas Thermal**: Jika hasil cetakan terlalu panjang atau teks terpotong, sesuaikan pilihan lebar kertas di pengaturan printer (antara **58mm** atau **80mm**).
