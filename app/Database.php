<?php

namespace App;

use PDO;
use Exception;

class Database {
    private static ?PDO $pdo = null;
    private static string $driver = 'sqlite'; // 'sqlite' or 'mysql'
    private static string $dbFile = __DIR__ . '/../data/warung.db';

    public static function isMysql(): bool {
        return self::$driver === 'mysql';
    }

    public static function getDriver(): string {
        return self::$driver;
    }

    public static function get(): PDO {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $configFile = __DIR__ . '/../config.php';
        $config = file_exists($configFile) ? require $configFile : [];
        $connectionType = $config['db_connection'] ?? 'auto';

        // 1. Coba koneksi ke MySQL InfinityFree jika mode 'mysql' atau 'auto'
        if ($connectionType === 'mysql' || $connectionType === 'auto') {
            $mysql = $config['mysql'] ?? [];
            $host = $mysql['host'] ?? 'sql107.infinityfree.com';
            $dbname = $mysql['database'] ?? 'if0_39237979_Tokomu';
            $user = $mysql['username'] ?? 'if0_39237979';
            $pass = $mysql['password'] ?? 'Fahrul200505';
            $port = (int)($mysql['port'] ?? 3306);
            $charset = $mysql['charset'] ?? 'utf8mb4';

            $passwordsToTry = [$pass];
            if (str_starts_with($pass, '(') && str_ends_with($pass, ')')) {
                $passwordsToTry[] = trim($pass, '()');
            } else {
                $passwordsToTry[] = "({$pass})";
            }

            $lastEx = null;
            foreach ($passwordsToTry as $testPass) {
                try {
                    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
                    $pdo = new PDO($dsn, $user, $testPass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_TIMEOUT => 2,
                    ]);

                    self::$pdo = $pdo;
                    self::$driver = 'mysql';
                    self::initMysqlSchema();
                    self::seedInitialData();
                    return self::$pdo;
                } catch (\Throwable $e) {
                    $lastEx = $e;
                }
            }

            if ($connectionType === 'mysql') {
                throw new Exception("Gagal terhubung ke MySQL InfinityFree: " . ($lastEx ? $lastEx->getMessage() : 'Unknown error'));
            }
        }

        // 2. Fallback ke SQLite lokal (Laragon / Offline)
        self::$driver = 'sqlite';
        $sqlitePath = $config['sqlite']['database'] ?? self::$dbFile;
        $dir = dirname($sqlitePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $isNew = !file_exists($sqlitePath) || filesize($sqlitePath) === 0;

        self::$pdo = new PDO('sqlite:' . $sqlitePath);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Optimization for SQLite performance and concurrent reads
        try {
            self::$pdo->exec('PRAGMA journal_mode = WAL;');
        } catch (\Exception $e) {
            try {
                self::$pdo->exec('PRAGMA journal_mode = DELETE;');
            } catch (\Exception $e) {}
        }
        try {
            self::$pdo->exec('PRAGMA synchronous = NORMAL;');
        } catch (\Exception $e) {}
        try {
            self::$pdo->exec('PRAGMA foreign_keys = ON;');
        } catch (\Exception $e) {}

        self::initSchema();

        if ($isNew) {
            self::seedInitialData();
        } else {
            self::updateProductImagesIfEmpty();
        }

        return self::$pdo;
    }

    public static function initMysqlSchema(): void {
        $db = self::$pdo;
        $db->exec("
            CREATE TABLE IF NOT EXISTS `stores` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(100) NOT NULL UNIQUE,
                `password_hash` VARCHAR(255) NOT NULL,
                `plain_password` VARCHAR(255) DEFAULT '',
                `store_name` VARCHAR(150) NOT NULL,
                `store_tagline` VARCHAR(255) DEFAULT 'Lengkap, Murah & Bersahabat',
                `store_address` TEXT,
                `store_phone` VARCHAR(50) DEFAULT '0812-3456-7890',
                `store_logo` TEXT,
                `receipt_header` VARCHAR(255) DEFAULT 'STRUK BELANJA',
                `receipt_footer` TEXT,
                `paper_size` VARCHAR(10) DEFAULT '58mm',
                `printer_type` VARCHAR(20) DEFAULT 'bluetooth',
                `auto_print` TINYINT(1) DEFAULT 1,
                `cash_drawer` TINYINT(1) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `super_admins` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(100) UNIQUE NOT NULL,
                `password_hash` VARCHAR(255) NOT NULL,
                `name` VARCHAR(150) NOT NULL DEFAULT 'Super Administrator',
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `settings` (
                `id` INT PRIMARY KEY,
                `store_name` VARCHAR(150) NOT NULL DEFAULT 'Tokomu',
                `store_tagline` VARCHAR(255) DEFAULT 'Lengkap, Murah & Bersahabat',
                `store_address` TEXT,
                `store_phone` VARCHAR(50) DEFAULT '0812-3456-7890',
                `store_logo` TEXT,
                `receipt_header` VARCHAR(255) DEFAULT 'TERIMA KASIH TELAH BERBELANJA',
                `receipt_footer` TEXT,
                `paper_size` VARCHAR(10) DEFAULT '58mm',
                `printer_type` VARCHAR(20) DEFAULT 'bluetooth',
                `auto_print` TINYINT(1) DEFAULT 1,
                `cash_drawer` TINYINT(1) DEFAULT 0,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `categories` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL UNIQUE,
                `icon` VARCHAR(50) DEFAULT 'package',
                `sort_order` INT DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `products` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `store_id` INT DEFAULT 1,
                `barcode` VARCHAR(100) NULL,
                `name` VARCHAR(255) NOT NULL,
                `category_id` INT NULL,
                `unit` VARCHAR(50) DEFAULT 'pcs',
                `buy_price` DECIMAL(15,2) DEFAULT 0.00,
                `sell_price` DECIMAL(15,2) NOT NULL,
                `stock` DECIMAL(15,2) DEFAULT 0.00,
                `min_stock` DECIMAL(15,2) DEFAULT 5.00,
                `image` TEXT,
                `is_active` TINYINT(1) DEFAULT 1,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY `idx_products_barcode` (`barcode`),
                KEY `idx_products_store` (`store_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `transactions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `store_id` INT DEFAULT 1,
                `invoice_no` VARCHAR(100) UNIQUE NOT NULL,
                `customer_name` VARCHAR(150) DEFAULT 'Umum',
                `customer_phone` VARCHAR(50) NULL,
                `payment_method` VARCHAR(50) DEFAULT 'cash',
                `subtotal` DECIMAL(15,2) NOT NULL,
                `discount_amount` DECIMAL(15,2) DEFAULT 0.00,
                `grand_total` DECIMAL(15,2) NOT NULL,
                `cash_amount` DECIMAL(15,2) DEFAULT 0.00,
                `change_amount` DECIMAL(15,2) DEFAULT 0.00,
                `status` VARCHAR(50) DEFAULT 'completed',
                `total_profit` DECIMAL(15,2) DEFAULT 0.00,
                `notes` TEXT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                KEY `idx_trx_store` (`store_id`),
                KEY `idx_trx_date` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `transaction_items` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `transaction_id` INT NOT NULL,
                `product_id` INT NULL,
                `product_name` VARCHAR(255) NOT NULL,
                `unit` VARCHAR(50) DEFAULT 'pcs',
                `buy_price` DECIMAL(15,2) DEFAULT 0.00,
                `sell_price` DECIMAL(15,2) NOT NULL,
                `qty` DECIMAL(15,2) NOT NULL,
                `subtotal` DECIMAL(15,2) NOT NULL,
                `profit` DECIMAL(15,2) DEFAULT 0.00,
                KEY `idx_ti_trx` (`transaction_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `kasbon` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `store_id` INT DEFAULT 1,
                `transaction_id` INT NULL,
                `customer_name` VARCHAR(150) NOT NULL,
                `customer_phone` VARCHAR(50) NULL,
                `total_debt` DECIMAL(15,2) NOT NULL,
                `paid_amount` DECIMAL(15,2) DEFAULT 0.00,
                `remaining_debt` DECIMAL(15,2) NOT NULL,
                `status` VARCHAR(50) DEFAULT 'unpaid',
                `due_date` DATE NULL,
                `notes` TEXT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY `idx_kasbon_store` (`store_id`),
                KEY `idx_kasbon_customer` (`customer_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `kasbon_payments` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `kasbon_id` INT NOT NULL,
                `payment_amount` DECIMAL(15,2) NOT NULL,
                `payment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `notes` TEXT NULL,
                KEY `idx_kp_kasbon` (`kasbon_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `stock_logs` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `product_id` INT NOT NULL,
                `type` VARCHAR(50) NOT NULL,
                `qty_change` DECIMAL(15,2) NOT NULL,
                `stock_before` DECIMAL(15,2) NOT NULL,
                `stock_after` DECIMAL(15,2) NOT NULL,
                `note` TEXT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                KEY `idx_sl_prod` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public static function initSchema(): void {
        $db = self::$pdo;

        $db->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY,
                store_name TEXT NOT NULL DEFAULT 'Tokomu',
                store_tagline TEXT DEFAULT 'Lengkap, Murah & Bersahabat',
                store_address TEXT DEFAULT 'Jl. Pemajatan Komp. Permata Hijau 2, Blok B No.2',
                store_phone TEXT DEFAULT '0812-3456-7890',
                receipt_header TEXT DEFAULT 'TERIMA KASIH TELAH BERBELANJA',
                receipt_footer TEXT DEFAULT 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.\nSemoga berkah & langganan terus!',
                paper_size TEXT DEFAULT '58mm',
                printer_type TEXT DEFAULT 'bluetooth',
                auto_print INTEGER DEFAULT 1,
                cash_drawer INTEGER DEFAULT 0,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                icon TEXT DEFAULT 'package',
                sort_order INTEGER DEFAULT 0
            );

            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                barcode TEXT UNIQUE,
                name TEXT NOT NULL,
                category_id INTEGER,
                unit TEXT DEFAULT 'pcs',
                buy_price REAL DEFAULT 0,
                sell_price REAL NOT NULL,
                stock REAL DEFAULT 0,
                min_stock REAL DEFAULT 5,
                image TEXT DEFAULT '',
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
            );

            CREATE TABLE IF NOT EXISTS transactions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                invoice_no TEXT UNIQUE NOT NULL,
                customer_name TEXT DEFAULT 'Umum',
                customer_phone TEXT,
                payment_method TEXT DEFAULT 'cash',
                subtotal REAL NOT NULL,
                discount_amount REAL DEFAULT 0,
                grand_total REAL NOT NULL,
                cash_amount REAL DEFAULT 0,
                change_amount REAL DEFAULT 0,
                status TEXT DEFAULT 'completed',
                total_profit REAL DEFAULT 0,
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS transaction_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                transaction_id INTEGER NOT NULL,
                product_id INTEGER,
                product_name TEXT NOT NULL,
                unit TEXT DEFAULT 'pcs',
                buy_price REAL DEFAULT 0,
                sell_price REAL NOT NULL,
                qty REAL NOT NULL,
                subtotal REAL NOT NULL,
                profit REAL DEFAULT 0,
                FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
            );

            CREATE TABLE IF NOT EXISTS kasbon (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                transaction_id INTEGER,
                customer_name TEXT NOT NULL,
                customer_phone TEXT,
                total_debt REAL NOT NULL,
                paid_amount REAL DEFAULT 0,
                remaining_debt REAL NOT NULL,
                status TEXT DEFAULT 'unpaid',
                due_date DATE,
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE SET NULL
            );

            CREATE TABLE IF NOT EXISTS kasbon_payments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                kasbon_id INTEGER NOT NULL,
                payment_amount REAL NOT NULL,
                payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                notes TEXT,
                FOREIGN KEY (kasbon_id) REFERENCES kasbon(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS stock_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                product_id INTEGER NOT NULL,
                type TEXT NOT NULL,
                qty_change REAL NOT NULL,
                stock_before REAL NOT NULL,
                stock_after REAL NOT NULL,
                note TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS stores (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                plain_password TEXT DEFAULT '',
                store_name TEXT NOT NULL,
                store_tagline TEXT DEFAULT 'Lengkap, Murah & Bersahabat',
                store_address TEXT DEFAULT '',
                store_phone TEXT DEFAULT '',
                receipt_header TEXT DEFAULT 'STRUK BELANJA',
                receipt_footer TEXT DEFAULT 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.\nSemoga berkah & langganan terus!',
                paper_size TEXT DEFAULT '58mm',
                printer_type TEXT DEFAULT 'bluetooth',
                auto_print INTEGER DEFAULT 1,
                cash_drawer INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS super_admins (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                name TEXT NOT NULL DEFAULT 'Super Administrator',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE INDEX IF NOT EXISTS idx_products_barcode ON products(barcode);
            CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
            CREATE INDEX IF NOT EXISTS idx_trx_invoice ON transactions(invoice_no);
            CREATE INDEX IF NOT EXISTS idx_trx_date ON transactions(created_at);
            CREATE INDEX IF NOT EXISTS idx_kasbon_customer ON kasbon(customer_name);
            CREATE INDEX IF NOT EXISTS idx_kasbon_status ON kasbon(status);
        ");

        // Migration: add image and store_id columns if not exist
        try {
            $db->exec("ALTER TABLE products ADD COLUMN image TEXT DEFAULT '';");
        } catch (\Exception $e) {}
        try {
            $db->exec("ALTER TABLE products ADD COLUMN store_id INTEGER DEFAULT 1;");
        } catch (\Exception $e) {}
        try {
            $db->exec("ALTER TABLE transactions ADD COLUMN store_id INTEGER DEFAULT 1;");
        } catch (\Exception $e) {}
        try {
            $db->exec("ALTER TABLE kasbon ADD COLUMN store_id INTEGER DEFAULT 1;");
        } catch (\Exception $e) {}
        try {
            $db->exec("ALTER TABLE stores ADD COLUMN plain_password TEXT DEFAULT '';");
        } catch (\Exception $e) {}
        try {
            $db->exec("ALTER TABLE stores ADD COLUMN store_logo TEXT DEFAULT '';");
        } catch (\Exception $e) {}
        try {
            $db->exec("ALTER TABLE settings ADD COLUMN store_logo TEXT DEFAULT '';");
        } catch (\Exception $e) {}
    }

    public static function getProductSeedList(): array {
        return [
            // Beras & Biji-bijian
            ['barcode' => '8991001001', 'name' => 'Beras Rojo Lele (5 kg)', 'cat' => 'Beras & Biji-bijian', 'unit' => 'sak', 'buy' => 73000, 'sell' => 78000, 'stock' => 15, 'min' => 3, 'img' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&q=80'],
            ['barcode' => '8991001002', 'name' => 'Beras Pandan Wangi (5 kg)', 'cat' => 'Beras & Biji-bijian', 'unit' => 'sak', 'buy' => 82000, 'sell' => 88000, 'stock' => 10, 'min' => 2, 'img' => 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=400&q=80'],
            ['barcode' => '8991001003', 'name' => 'Beras Eceran Medium (per Kg)', 'cat' => 'Beras & Biji-bijian', 'unit' => 'kg', 'buy' => 12500, 'sell' => 14500, 'stock' => 50, 'min' => 10, 'img' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&q=80'],
            ['barcode' => '8991001004', 'name' => 'Tepung Terigu Segitiga Biru 1kg', 'cat' => 'Beras & Biji-bijian', 'unit' => 'pcs', 'buy' => 10500, 'sell' => 12500, 'stock' => 24, 'min' => 5, 'img' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80'],
            ['barcode' => '8991001005', 'name' => 'Tepung Beras Rose Brand 500g', 'cat' => 'Beras & Biji-bijian', 'unit' => 'pcs', 'buy' => 7000, 'sell' => 8500, 'stock' => 18, 'min' => 5, 'img' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80'],
            ['barcode' => '8991001006', 'name' => 'Tepung Maizena Bola Deli 200g', 'cat' => 'Beras & Biji-bijian', 'unit' => 'pcs', 'buy' => 5000, 'sell' => 6500, 'stock' => 12, 'min' => 3, 'img' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80'],

            // Minyak & Gula
            ['barcode' => '8991002001', 'name' => 'Minyak Goreng Minyakita 1 Liter', 'cat' => 'Minyak & Gula', 'unit' => 'pcs', 'buy' => 14500, 'sell' => 16000, 'stock' => 30, 'min' => 8, 'img' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80'],
            ['barcode' => '8991002002', 'name' => 'Minyak Goreng Bimoli 2 Liter', 'cat' => 'Minyak & Gula', 'unit' => 'pcs', 'buy' => 35000, 'sell' => 39000, 'stock' => 12, 'min' => 4, 'img' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80'],
            ['barcode' => '8991002003', 'name' => 'Minyak Goreng Tropical 2 Liter', 'cat' => 'Minyak & Gula', 'unit' => 'pcs', 'buy' => 34500, 'sell' => 38500, 'stock' => 10, 'min' => 4, 'img' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80'],
            ['barcode' => '8991002004', 'name' => 'Gula Pasir Gulaku Kuning 1kg', 'cat' => 'Minyak & Gula', 'unit' => 'pcs', 'buy' => 16000, 'sell' => 18000, 'stock' => 25, 'min' => 5, 'img' => 'https://images.unsplash.com/photo-1622484212850-cab596d66e74?w=400&q=80'],
            ['barcode' => '8991002005', 'name' => 'Gula Pasir Curah / Eceran (1 kg)', 'cat' => 'Minyak & Gula', 'unit' => 'kg', 'buy' => 15000, 'sell' => 17000, 'stock' => 40, 'min' => 10, 'img' => 'https://images.unsplash.com/photo-1622484212850-cab596d66e74?w=400&q=80'],
            ['barcode' => '8991002006', 'name' => 'Gula Merah / Aren Gandu (per Kg)', 'cat' => 'Minyak & Gula', 'unit' => 'kg', 'buy' => 22000, 'sell' => 26000, 'stock' => 15, 'min' => 3, 'img' => 'https://images.unsplash.com/photo-1581441363689-1f3c3c414635?w=400&q=80'],

            // Mie & Instan
            ['barcode' => '8991003001', 'name' => 'Indomie Goreng Original', 'cat' => 'Mie & Instan', 'unit' => 'pcs', 'buy' => 2900, 'sell' => 3500, 'stock' => 80, 'min' => 20, 'img' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&q=80'],
            ['barcode' => '8991003002', 'name' => 'Indomie Kuah Soto Mie', 'cat' => 'Mie & Instan', 'unit' => 'pcs', 'buy' => 2800, 'sell' => 3300, 'stock' => 60, 'min' => 15, 'img' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&q=80'],
            ['barcode' => '8991003003', 'name' => 'Indomie Kuah Ayam Bawang', 'cat' => 'Mie & Instan', 'unit' => 'pcs', 'buy' => 2800, 'sell' => 3300, 'stock' => 45, 'min' => 15, 'img' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&q=80'],
            ['barcode' => '8991003004', 'name' => 'Mie Sedaap Goreng', 'cat' => 'Mie & Instan', 'unit' => 'pcs', 'buy' => 2850, 'sell' => 3400, 'stock' => 50, 'min' => 15, 'img' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&q=80'],
            ['barcode' => '8991003005', 'name' => 'Sarden ABC Saus Tomat 155g', 'cat' => 'Mie & Instan', 'unit' => 'kaleng', 'buy' => 9500, 'sell' => 11500, 'stock' => 16, 'min' => 4, 'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'],

            // Bumbu Dapur & Telur
            ['barcode' => '8991004001', 'name' => 'Telur Ayam Negeri (per Kg)', 'cat' => 'Bumbu Dapur & Telur', 'unit' => 'kg', 'buy' => 25500, 'sell' => 28500, 'stock' => 35, 'min' => 10, 'img' => 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=400&q=80'],
            ['barcode' => '8991004002', 'name' => 'Telur Bebek Asin Matang', 'cat' => 'Bumbu Dapur & Telur', 'unit' => 'butir', 'buy' => 3300, 'sell' => 4000, 'stock' => 25, 'min' => 5, 'img' => 'https://images.unsplash.com/photo-1516448620398-c5f44bf9f441?w=400&q=80'],
            ['barcode' => '8991004003', 'name' => 'Royco Rasa Sapi 1 Renteng (12 sachet)', 'cat' => 'Bumbu Dapur & Telur', 'unit' => 'renteng', 'buy' => 4500, 'sell' => 5500, 'stock' => 15, 'min' => 3, 'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'],
            ['barcode' => '8991004004', 'name' => 'Masako Rasa Ayam 1 Renteng (12 sachet)', 'cat' => 'Bumbu Dapur & Telur', 'unit' => 'renteng', 'buy' => 4500, 'sell' => 5500, 'stock' => 14, 'min' => 3, 'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'],
            ['barcode' => '8991004005', 'name' => 'Kecap Manis Bango 520ml Refill', 'cat' => 'Bumbu Dapur & Telur', 'unit' => 'pcs', 'buy' => 22000, 'sell' => 25500, 'stock' => 12, 'min' => 3, 'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'],
            ['barcode' => '8991004006', 'name' => 'Saus Sambal ABC 135ml', 'cat' => 'Bumbu Dapur & Telur', 'unit' => 'btl', 'buy' => 6500, 'sell' => 8000, 'stock' => 18, 'min' => 4, 'img' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80'],
            ['barcode' => '8991004007', 'name' => 'Garam Dapur Cap Kapal 250g', 'cat' => 'Bumbu Dapur & Telur', 'unit' => 'pcs', 'buy' => 2500, 'sell' => 3500, 'stock' => 30, 'min' => 5, 'img' => 'https://images.unsplash.com/photo-1518110903415-3e445d0705a6?w=400&q=80'],

            // Minuman & Susu
            ['barcode' => '8991005001', 'name' => 'Kopi Kapal Api Spesial Mix 1 Renteng', 'cat' => 'Minuman & Susu', 'unit' => 'renteng', 'buy' => 13000, 'sell' => 15000, 'stock' => 20, 'min' => 5, 'img' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400&q=80'],
            ['barcode' => '8991005002', 'name' => 'Kopi Good Day Cappuccino 1 Renteng', 'cat' => 'Minuman & Susu', 'unit' => 'renteng', 'buy' => 18500, 'sell' => 21500, 'stock' => 15, 'min' => 3, 'img' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?w=400&q=80'],
            ['barcode' => '8991005003', 'name' => 'Teh Celup Sariwangi isi 30', 'cat' => 'Minuman & Susu', 'unit' => 'kotak', 'buy' => 6500, 'sell' => 8000, 'stock' => 16, 'min' => 4, 'img' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=400&q=80'],
            ['barcode' => '8991005004', 'name' => 'Susu Kental Manis Frisian Flag Kaleng', 'cat' => 'Minuman & Susu', 'unit' => 'kaleng', 'buy' => 11000, 'sell' => 13000, 'stock' => 15, 'min' => 3, 'img' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&q=80'],
            ['barcode' => '8991005005', 'name' => 'Susu Ultra Milk Cokelat 250ml', 'cat' => 'Minuman & Susu', 'unit' => 'kotak', 'buy' => 6000, 'sell' => 7500, 'stock' => 24, 'min' => 6, 'img' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&q=80'],
            ['barcode' => '8991005006', 'name' => 'Aqua Botol Sedang 600ml', 'cat' => 'Minuman & Susu', 'unit' => 'btl', 'buy' => 3000, 'sell' => 4000, 'stock' => 36, 'min' => 10, 'img' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=400&q=80'],

            // Gas & Galon
            ['barcode' => '8991006001', 'name' => 'Gas Elpiji 3 Kg (Isi Ulang Melon)', 'cat' => 'Gas & Galon', 'unit' => 'tabung', 'buy' => 19000, 'sell' => 22000, 'stock' => 8, 'min' => 3, 'img' => 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=400&q=80'],
            ['barcode' => '8991006002', 'name' => 'Aqua Galon 19 Liter (Refill Resmi)', 'cat' => 'Gas & Galon', 'unit' => 'galon', 'buy' => 18500, 'sell' => 22000, 'stock' => 12, 'min' => 4, 'img' => 'https://images.unsplash.com/photo-1564419320461-6870880221ad?w=400&q=80'],
            ['barcode' => '8991006003', 'name' => 'Air Galon Isi Ulang RO Standar', 'cat' => 'Gas & Galon', 'unit' => 'galon', 'buy' => 4000, 'sell' => 6000, 'stock' => 20, 'min' => 5, 'img' => 'https://images.unsplash.com/photo-1564419320461-6870880221ad?w=400&q=80'],

            // Sabun & Kebersihan
            ['barcode' => '8991007001', 'name' => 'Sabun Cuci Piring Sunlight Lime 650ml', 'cat' => 'Sabun & Kebersihan', 'unit' => 'pouch', 'buy' => 12500, 'sell' => 15000, 'stock' => 18, 'min' => 4, 'img' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80'],
            ['barcode' => '8991007002', 'name' => 'Deterjen Rinso Molto Anti Noda 770g', 'cat' => 'Sabun & Kebersihan', 'unit' => 'pcs', 'buy' => 19000, 'sell' => 23000, 'stock' => 14, 'min' => 3, 'img' => 'https://images.unsplash.com/photo-1607006314167-27a3c3e80f83?w=400&q=80'],
            ['barcode' => '8991007003', 'name' => 'Sabun Mandi Lifebuoy Bar 110g', 'cat' => 'Sabun & Kebersihan', 'unit' => 'pcs', 'buy' => 3500, 'sell' => 4500, 'stock' => 28, 'min' => 6, 'img' => 'https://images.unsplash.com/photo-1607006314167-27a3c3e80f83?w=400&q=80'],
            ['barcode' => '8991007004', 'name' => 'Pasta Gigi Pepsodent 190g', 'cat' => 'Sabun & Kebersihan', 'unit' => 'pcs', 'buy' => 12500, 'sell' => 15000, 'stock' => 15, 'min' => 3, 'img' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80'],
            ['barcode' => '8991007005', 'name' => 'Shampoo Sunsilk Black Shine 1 Renteng', 'cat' => 'Sabun & Kebersihan', 'unit' => 'renteng', 'buy' => 5000, 'sell' => 6500, 'stock' => 20, 'min' => 4, 'img' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80'],

            // Rokok
            ['barcode' => '8991008001', 'name' => 'Rokok Sampoerna Mild 16', 'cat' => 'Rokok', 'unit' => 'bungkus', 'buy' => 32000, 'sell' => 35000, 'stock' => 20, 'min' => 5, 'img' => 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?w=400&q=80'],
            ['barcode' => '8991008002', 'name' => 'Rokok Djarum Super 12', 'cat' => 'Rokok', 'unit' => 'bungkus', 'buy' => 23000, 'sell' => 25500, 'stock' => 25, 'min' => 5, 'img' => 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?w=400&q=80'],
            ['barcode' => '8991008003', 'name' => 'Rokok Gudang Garam Surya 12', 'cat' => 'Rokok', 'unit' => 'bungkus', 'buy' => 24000, 'sell' => 26500, 'stock' => 20, 'min' => 5, 'img' => 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?w=400&q=80'],

            // Snack
            ['barcode' => '8991009001', 'name' => 'Kerupuk Kaleng Putih Satuan', 'cat' => 'Snack & Makanan Ringan', 'unit' => 'pcs', 'buy' => 800, 'sell' => 1000, 'stock' => 50, 'min' => 10, 'img' => 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=400&q=80'],
            ['barcode' => '8991009002', 'name' => 'Biskuit Roma Kelapa 300g', 'cat' => 'Snack & Makanan Ringan', 'unit' => 'bungkus', 'buy' => 9500, 'sell' => 11500, 'stock' => 15, 'min' => 3, 'img' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&q=80'],
        ];
    }

    public static function updateProductImagesIfEmpty(): void {
        $db = self::$pdo;
        $items = self::getProductSeedList();
        $stmt = $db->prepare("UPDATE products SET image = ? WHERE barcode = ? AND (image IS NULL OR image = '')");
        foreach ($items as $item) {
            $stmt->execute([$item['img'], $item['barcode']]);
        }
    }

    public static function seedInitialData(): void {
        $db = self::$pdo;
        $insertIgnore = self::isMysql() ? "INSERT IGNORE INTO" : "INSERT OR IGNORE INTO";

        // Seed settings
        $stmt = $db->query("SELECT COUNT(*) as cnt FROM settings");
        if ((int)$stmt->fetch()['cnt'] === 0) {
            $db->exec("
                INSERT INTO settings (id, store_name, store_tagline, store_address, store_phone, receipt_header, receipt_footer, paper_size, printer_type, auto_print)
                VALUES (1, 'Tokomu', 'Lengkap, Murah & Bersahabat', 'Jl. Pemajatan Komp. Permata Hijau 2, Blok B No.2', '0812-3456-7890', 'STRUK BELANJA', 'Terima kasih telah berbelanja di Tokomu!\nBarang yang sudah dibeli tidak dapat ditukar.', '58mm', 'bluetooth', 1)
            ");
        }

        // Seed Categories
        $categories = [
            ['name' => 'Beras & Biji-bijian', 'icon' => 'wheat', 'sort_order' => 1],
            ['name' => 'Minyak & Gula', 'icon' => 'droplet', 'sort_order' => 2],
            ['name' => 'Mie & Instan', 'icon' => 'soup', 'sort_order' => 3],
            ['name' => 'Bumbu Dapur & Telur', 'icon' => 'egg', 'sort_order' => 4],
            ['name' => 'Minuman & Susu', 'icon' => 'coffee', 'sort_order' => 5],
            ['name' => 'Gas & Galon', 'icon' => 'flame', 'sort_order' => 6],
            ['name' => 'Sabun & Kebersihan', 'icon' => 'sparkles', 'sort_order' => 7],
            ['name' => 'Rokok', 'icon' => 'cigarette', 'sort_order' => 8],
            ['name' => 'Snack & Makanan Ringan', 'icon' => 'cookie', 'sort_order' => 9],
            ['name' => 'Lain-lain', 'icon' => 'box', 'sort_order' => 10],
        ];

        $catStmt = $db->prepare("$insertIgnore categories (id, name, icon, sort_order) VALUES (?, ?, ?, ?)");
        $catIds = [];
        $i = 1;
        foreach ($categories as $cat) {
            $catStmt->execute([$i, $cat['name'], $cat['icon'], $cat['sort_order']]);
            $catIds[$cat['name']] = $i;
            $i++;
        }

        // Seed Products
        $products = self::getProductSeedList();

        $prodStmt = $db->prepare("
            $insertIgnore products (store_id, barcode, name, category_id, unit, buy_price, sell_price, stock, min_stock, image, is_active)
            VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");

        foreach ($products as $p) {
            $catId = $catIds[$p['cat']] ?? 1;
            $prodStmt->execute([
                $p['barcode'],
                $p['name'],
                $catId,
                $p['unit'],
                $p['buy'],
                $p['sell'],
                $p['stock'],
                $p['min'],
                $p['img']
            ]);
        }

        // Seed default store #1 (Tokomu / tokomu123)
        $cntStore = (int)$db->query("SELECT COUNT(*) FROM stores")->fetchColumn();
        if ($cntStore === 0) {
            $settings = self::fetchOne("SELECT * FROM settings WHERE id = 1");
            $storeName = $settings['store_name'] ?? 'Tokomu';
            $address = $settings['store_address'] ?? 'Jl. Pemajatan Komp. Permata Hijau 2, Blok B No.2';
            $phone = $settings['store_phone'] ?? '0812-3456-7890';
            $pw = password_hash('tokomu123', PASSWORD_DEFAULT);
            $db->prepare("
                $insertIgnore stores (id, username, password_hash, plain_password, store_name, store_tagline, store_address, store_phone, receipt_header, receipt_footer)
                VALUES (1, 'tokomu', ?, 'tokomu123', ?, 'Lengkap, Murah & Bersahabat', ?, ?, 'STRUK BELANJA', 'Terima kasih telah berbelanja di Tokomu!\nBarang yang sudah dibeli tidak dapat ditukar.')
            ")->execute([$pw, $storeName, $address, $phone]);
        }

        // Seed default super admin (username: superadmin, password: admin123)
        $cntAdmin = (int)$db->query("SELECT COUNT(*) FROM super_admins")->fetchColumn();
        if ($cntAdmin === 0) {
            $pwAdmin = password_hash('admin123', PASSWORD_DEFAULT);
            $db->prepare("
                $insertIgnore super_admins (id, username, password_hash, name)
                VALUES (1, 'superadmin', ?, 'Super Administrator')
            ")->execute([$pwAdmin]);
        }
    }

    public static function prepare(string $sql): \PDOStatement {
        if (self::isMysql()) {
            $sql = str_ireplace('INSERT OR IGNORE INTO', 'INSERT IGNORE INTO', $sql);
        }
        return self::get()->prepare($sql);
    }

    public static function exec(string $sql): int {
        if (self::isMysql()) {
            $sql = str_ireplace('INSERT OR IGNORE INTO', 'INSERT IGNORE INTO', $sql);
        }
        return self::get()->exec($sql);
    }

    public static function query(string $sql, array $params = []): \PDOStatement {
        if (self::isMysql()) {
            $sql = str_ireplace('INSERT OR IGNORE INTO', 'INSERT IGNORE INTO', $sql);
        }
        $stmt = self::get()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchOne(string $sql, array $params = []): ?array {
        $stmt = self::query($sql, $params);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }

    public static function lastInsertId(): string {
        return self::get()->lastInsertId();
    }
}
