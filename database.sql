-- ========================================================
-- Tokomu POS - MySQL Database Schema & Initial Data
-- InfinityFree / MySQL Compatible
-- Database: if0_39237979_Tokomu
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- --------------------------------------------------------
-- 1. Table structure for `stores`
-- --------------------------------------------------------
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

-- --------------------------------------------------------
-- 2. Table structure for `super_admins`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `super_admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `name` VARCHAR(150) NOT NULL DEFAULT 'Super Administrator',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Table structure for `settings`
-- --------------------------------------------------------
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

-- --------------------------------------------------------
-- 4. Table structure for `categories`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(50) DEFAULT 'package',
    `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Table structure for `products`
-- --------------------------------------------------------
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
    KEY `idx_products_store` (`store_id`),
    KEY `idx_products_cat` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. Table structure for `transactions`
-- --------------------------------------------------------
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

-- --------------------------------------------------------
-- 7. Table structure for `transaction_items`
-- --------------------------------------------------------
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
    KEY `idx_ti_trx` (`transaction_id`),
    KEY `idx_ti_prod` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 8. Table structure for `kasbon`
-- --------------------------------------------------------
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
    KEY `idx_kasbon_customer` (`customer_name`),
    KEY `idx_kasbon_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 9. Table structure for `kasbon_payments`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kasbon_payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kasbon_id` INT NOT NULL,
    `payment_amount` DECIMAL(15,2) NOT NULL,
    `payment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `notes` TEXT NULL,
    KEY `idx_kp_kasbon` (`kasbon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 10. Table structure for `stock_logs`
-- --------------------------------------------------------
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

-- ========================================================
-- INITIAL SEED DATA
-- ========================================================

-- Store #1 (Tokomu / tokomu123)
INSERT INTO `stores` (`id`, `username`, `password_hash`, `plain_password`, `store_name`, `store_tagline`, `store_address`, `store_phone`, `receipt_header`, `receipt_footer`, `paper_size`, `printer_type`, `auto_print`)
VALUES (1, 'tokomu', '$2y$10$f6zJc.T5rD6pQ1bIqN6q1.kE54wN92XJ72E8E6sV10XwX96xR9ZgS', 'tokomu123', 'Tokomu', 'Lengkap, Murah & Bersahabat', 'Jl. Pemajatan Komp. Permata Hijau 2, Blok B No.2', '0812-3456-7890', 'STRUK BELANJA', 'Terima kasih telah berbelanja di Tokomu!\nBarang yang sudah dibeli tidak dapat ditukar.', '58mm', 'bluetooth', 1)
ON DUPLICATE KEY UPDATE `store_name`=VALUES(`store_name`);

-- Super Administrator (superadmin / admin123)
INSERT INTO `super_admins` (`id`, `username`, `password_hash`, `name`)
VALUES (1, 'superadmin', '$2y$10$k1pWwL.0mC5W4sJqX9QW5e6K54wN92XJ72E8E6sV10XwX96xR9ZgS', 'Super Administrator')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Settings Record
INSERT INTO `settings` (`id`, `store_name`, `store_tagline`, `store_address`, `store_phone`, `receipt_header`, `receipt_footer`, `paper_size`, `printer_type`, `auto_print`)
VALUES (1, 'Tokomu', 'Lengkap, Murah & Bersahabat', 'Jl. Pemajatan Komp. Permata Hijau 2, Blok B No.2', '0812-3456-7890', 'STRUK BELANJA', 'Terima kasih telah berbelanja di Tokomu!\nBarang yang sudah dibeli tidak dapat ditukar.', '58mm', 'bluetooth', 1)
ON DUPLICATE KEY UPDATE `store_name`=VALUES(`store_name`);

-- Categories
INSERT INTO `categories` (`id`, `name`, `icon`, `sort_order`) VALUES
(1, 'Beras & Biji-bijian', 'wheat', 1),
(2, 'Minyak & Gula', 'droplet', 2),
(3, 'Mie & Instan', 'soup', 3),
(4, 'Bumbu Dapur & Telur', 'egg', 4),
(5, 'Minuman & Susu', 'coffee', 5),
(6, 'Gas & Galon', 'flame', 6),
(7, 'Sabun & Kebersihan', 'sparkles', 7),
(8, 'Rokok', 'cigarette', 8),
(9, 'Snack & Makanan Ringan', 'cookie', 9),
(10, 'Lain-lain', 'box', 10)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 40+ Default Sembako Products
INSERT INTO `products` (`store_id`, `barcode`, `name`, `category_id`, `unit`, `buy_price`, `sell_price`, `stock`, `min_stock`, `image`, `is_active`) VALUES
(1, '8991001001', 'Beras Rojo Lele (5 kg)', 1, 'sak', 73000.00, 78000.00, 15.00, 3.00, 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&q=80', 1),
(1, '8991001002', 'Beras Pandan Wangi (5 kg)', 1, 'sak', 82000.00, 88000.00, 10.00, 2.00, 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=400&q=80', 1),
(1, '8991001003', 'Beras Eceran Medium (per Kg)', 1, 'kg', 12500.00, 14500.00, 50.00, 10.00, 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&q=80', 1),
(1, '8991001004', 'Tepung Terigu Segitiga Biru 1kg', 1, 'pcs', 10500.00, 12500.00, 24.00, 5.00, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80', 1),
(1, '8991001005', 'Tepung Beras Rose Brand 500g', 1, 'pcs', 7000.00, 8500.00, 18.00, 5.00, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80', 1),
(1, '8991001006', 'Tepung Maizena Bola Deli 200g', 1, 'pcs', 5000.00, 6500.00, 12.00, 3.00, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80', 1),
(1, '8991002001', 'Minyak Goreng Minyakita 1 Liter', 2, 'pcs', 14500.00, 16000.00, 30.00, 8.00, 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80', 1),
(1, '8991002002', 'Minyak Goreng Bimoli 2 Liter', 2, 'pcs', 35000.00, 39000.00, 12.00, 4.00, 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80', 1),
(1, '8991002003', 'Minyak Goreng Tropical 2 Liter', 2, 'pcs', 34500.00, 38500.00, 10.00, 4.00, 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80', 1),
(1, '8991002004', 'Gula Pasir Gulaku Kuning 1kg', 2, 'pcs', 16000.00, 18000.00, 25.00, 5.00, 'https://images.unsplash.com/photo-1622484212850-cab596d66e74?w=400&q=80', 1),
(1, '8991002005', 'Gula Pasir Curah / Eceran (1 kg)', 2, 'kg', 15000.00, 17000.00, 40.00, 10.00, 'https://images.unsplash.com/photo-1622484212850-cab596d66e74?w=400&q=80', 1),
(1, '8991002006', 'Gula Merah / Aren Gandu (per Kg)', 2, 'kg', 22000.00, 26000.00, 15.00, 3.00, 'https://images.unsplash.com/photo-1581441363689-1f3c3c414635?w=400&q=80', 1),
(1, '8991003001', 'Indomie Goreng Original', 3, 'pcs', 2900.00, 3500.00, 80.00, 20.00, 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&q=80', 1),
(1, '8991003002', 'Indomie Kuah Soto Mie', 3, 'pcs', 2800.00, 3300.00, 60.00, 15.00, 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&q=80', 1),
(1, '8991003003', 'Indomie Kuah Ayam Bawang', 3, 'pcs', 2800.00, 3300.00, 45.00, 15.00, 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&q=80', 1),
(1, '8991003004', 'Mie Sedaap Goreng', 3, 'pcs', 2850.00, 3400.00, 50.00, 15.00, 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&q=80', 1),
(1, '8991003005', 'Sarden ABC Saus Tomat 155g', 3, 'kaleng', 9500.00, 11500.00, 16.00, 4.00, 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80', 1),
(1, '8991004001', 'Telur Ayam Negeri (per Kg)', 4, 'kg', 25500.00, 28500.00, 35.00, 10.00, 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=400&q=80', 1),
(1, '8991004002', 'Telur Bebek Asin Matang', 4, 'butir', 3300.00, 4000.00, 25.00, 5.00, 'https://images.unsplash.com/photo-1516448620398-c5f44bf9f441?w=400&q=80', 1),
(1, '8991004003', 'Royco Rasa Sapi 1 Renteng (12 sachet)', 4, 'renteng', 4500.00, 5500.00, 15.00, 3.00, 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80', 1),
(1, '8991004004', 'Masako Rasa Ayam 1 Renteng (12 sachet)', 4, 'renteng', 4500.00, 5500.00, 14.00, 3.00, 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80', 1),
(1, '8991004005', 'Kecap Manis Bango 520ml Refill', 4, 'pcs', 22000.00, 25500.00, 12.00, 3.00, 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80', 1),
(1, '8991004006', 'Saus Sambal ABC 135ml', 4, 'btl', 6500.00, 8000.00, 18.00, 4.00, 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80', 1),
(1, '8991004007', 'Garam Dapur Cap Kapal 250g', 4, 'pcs', 2500.00, 3500.00, 30.00, 5.00, 'https://images.unsplash.com/photo-1518110903415-3e445d0705a6?w=400&q=80', 1),
(1, '8991005001', 'Kopi Kapal Api Spesial Mix 1 Renteng', 5, 'renteng', 13000.00, 15000.00, 20.00, 5.00, 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400&q=80', 1),
(1, '8991005002', 'Kopi Good Day Cappuccino 1 Renteng', 5, 'renteng', 18500.00, 21500.00, 15.00, 3.00, 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?w=400&q=80', 1),
(1, '8991005003', 'Teh Celup Sariwangi isi 30', 5, 'kotak', 6500.00, 8000.00, 16.00, 4.00, 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=400&q=80', 1),
(1, '8991005004', 'Susu Kental Manis Frisian Flag Kaleng', 5, 'kaleng', 11000.00, 13000.00, 15.00, 3.00, 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&q=80', 1),
(1, '8991005005', 'Susu Ultra Milk Cokelat 250ml', 5, 'kotak', 6000.00, 7500.00, 24.00, 6.00, 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&q=80', 1),
(1, '8991005006', 'Aqua Botol Sedang 600ml', 5, 'btl', 3000.00, 4000.00, 36.00, 10.00, 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=400&q=80', 1),
(1, '8991006001', 'Gas Elpiji 3 Kg (Isi Ulang Melon)', 6, 'tabung', 19000.00, 22000.00, 8.00, 3.00, 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=400&q=80', 1),
(1, '8991006002', 'Aqua Galon 19 Liter (Refill Resmi)', 6, 'galon', 18500.00, 22000.00, 12.00, 4.00, 'https://images.unsplash.com/photo-1564419320461-6870880221ad?w=400&q=80', 1),
(1, '8991006003', 'Air Galon Isi Ulang RO Standar', 6, 'galon', 4000.00, 6000.00, 20.00, 5.00, 'https://images.unsplash.com/photo-1564419320461-6870880221ad?w=400&q=80', 1),
(1, '8991007001', 'Sabun Cuci Piring Sunlight Lime 650ml', 7, 'pouch', 12500.00, 15000.00, 18.00, 4.00, 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80', 1),
(1, '8991007002', 'Deterjen Rinso Molto Anti Noda 770g', 7, 'pcs', 19000.00, 23000.00, 14.00, 3.00, 'https://images.unsplash.com/photo-1607006314167-27a3c3e80f83?w=400&q=80', 1),
(1, '8991007003', 'Sabun Mandi Lifebuoy Bar 110g', 7, 'pcs', 3500.00, 4500.00, 28.00, 6.00, 'https://images.unsplash.com/photo-1607006314167-27a3c3e80f83?w=400&q=80', 1),
(1, '8991007004', 'Pasta Gigi Pepsodent 190g', 7, 'pcs', 12500.00, 15000.00, 15.00, 3.00, 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80', 1),
(1, '8991007005', 'Shampoo Sunsilk Black Shine 1 Renteng', 7, 'renteng', 5000.00, 6500.00, 20.00, 4.00, 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&q=80', 1),
(1, '8991008001', 'Rokok Sampoerna Mild 16', 8, 'bungkus', 32000.00, 35000.00, 20.00, 5.00, 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?w=400&q=80', 1),
(1, '8991008002', 'Rokok Djarum Super 12', 8, 'bungkus', 23000.00, 25500.00, 25.00, 5.00, 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?w=400&q=80', 1),
(1, '8991008003', 'Rokok Gudang Garam Surya 12', 8, 'bungkus', 24000.00, 26500.00, 20.00, 5.00, 'https://images.unsplash.com/photo-1527061011665-3652c757a4d4?w=400&q=80', 1),
(1, '8991009001', 'Kerupuk Kaleng Putih Satuan', 9, 'pcs', 800.00, 1000.00, 50.00, 10.00, 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=400&q=80', 1),
(1, '8991009002', 'Biskuit Roma Kelapa 300g', 9, 'bungkus', 9500.00, 11500.00, 15.00, 3.00, 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&q=80', 1)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

SET FOREIGN_KEY_CHECKS = 1;
