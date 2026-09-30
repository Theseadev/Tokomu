<?php

namespace App\Controllers;

use Flight;
use App\Database;
use App\Auth;
use Exception;

class SettingController {

    public static function get(): void {
        $storeId = Auth::id() ?? 1;
        $store = Database::fetchOne("SELECT * FROM stores WHERE id = ?", [$storeId]);
        if (!$store) {
            $store = Database::fetchOne("SELECT * FROM stores WHERE id = 1");
        }

        Flight::json([
            'success' => true,
            'settings' => $store
        ]);
    }

    public static function update(): void {
        $storeId = Auth::id() ?? 1;
        $data = \App\Helpers\RequestHelper::getJsonBody();

        $storeName = trim($data['store_name'] ?? '');
        if (empty($storeName)) {
            Flight::json(['success' => false, 'message' => 'Nama toko wajib diisi'], 400);
            return;
        }

        Database::query("
            UPDATE stores SET
                store_name = ?,
                store_tagline = ?,
                store_address = ?,
                store_phone = ?,
                receipt_header = ?,
                receipt_footer = ?,
                paper_size = ?,
                printer_type = ?,
                auto_print = ?,
                cash_drawer = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ", [
            $storeName,
            trim($data['store_tagline'] ?? ''),
            trim($data['store_address'] ?? ''),
            trim($data['store_phone'] ?? ''),
            trim($data['receipt_header'] ?? ''),
            trim($data['receipt_footer'] ?? ''),
            $data['paper_size'] ?? '58mm',
            $data['printer_type'] ?? 'bluetooth',
            !empty($data['auto_print']) ? 1 : 0,
            !empty($data['cash_drawer']) ? 1 : 0,
            $storeId
        ]);

        if ($storeId === 1) {
            Database::query("
                UPDATE settings SET
                    store_name = ?,
                    store_tagline = ?,
                    store_address = ?,
                    store_phone = ?,
                    receipt_header = ?,
                    receipt_footer = ?,
                    paper_size = ?,
                    printer_type = ?,
                    auto_print = ?,
                    cash_drawer = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = 1
            ", [
                $storeName,
                trim($data['store_tagline'] ?? ''),
                trim($data['store_address'] ?? ''),
                trim($data['store_phone'] ?? ''),
                trim($data['receipt_header'] ?? ''),
                trim($data['receipt_footer'] ?? ''),
                $data['paper_size'] ?? '58mm',
                $data['printer_type'] ?? 'bluetooth',
                !empty($data['auto_print']) ? 1 : 0,
                !empty($data['cash_drawer']) ? 1 : 0
            ]);
        }

        // Refresh session
        $updatedStore = Database::fetchOne("SELECT * FROM stores WHERE id = ?", [$storeId]);
        if ($updatedStore) {
            Auth::login($updatedStore);
        }

        Flight::json(['success' => true, 'message' => 'Pengaturan toko berhasil disimpan']);
    }

    public static function uploadLogo(): void {
        $storeId = Auth::id() ?? 1;

        if (!isset($_FILES['logo_file']) || $_FILES['logo_file']['error'] !== UPLOAD_ERR_OK) {
            Flight::json(['success' => false, 'message' => 'File foto tidak ditemukan atau gagal diunggah'], 400);
            return;
        }

        $file = $_FILES['logo_file'];
        $tmpName = $file['tmp_name'];
        $fileSize = $file['size'];

        // Maksimal 5 MB
        if ($fileSize > 5 * 1024 * 1024) {
            Flight::json(['success' => false, 'message' => 'Ukuran foto maksimal 5 MB'], 400);
            return;
        }

        // Validasi mime gambar
        $imageInfo = @getimagesize($tmpName);
        if (!$imageInfo) {
            Flight::json(['success' => false, 'message' => 'File harus berupa gambar valid (JPG, PNG, WEBP, GIF)'], 400);
            return;
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($imageInfo['mime'], $allowedMimes)) {
            Flight::json(['success' => false, 'message' => 'Format gambar tidak didukung. Gunakan JPG, PNG, atau WebP'], 400);
            return;
        }

        $extMap = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif'
        ];
        $ext = $extMap[$imageInfo['mime']] ?? 'jpg';

        $uploadDir = __DIR__ . '/../../public/uploads/logos';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = 'logo_' . $storeId . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            Flight::json(['success' => false, 'message' => 'Gagal menyimpan foto ke server'], 500);
            return;
        }

        // Relative URL from web root
        $rawBase = Flight::request()->base;
        $baseUrl = ($rawBase === '/' || empty($rawBase)) ? '' : rtrim($rawBase, '/');
        $logoUrl = $baseUrl . '/public/uploads/logos/' . $filename;

        // Update database stores and settings (if default store)
        Database::query("UPDATE stores SET store_logo = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?", [$logoUrl, $storeId]);

        if ($storeId === 1) {
            Database::query("UPDATE settings SET store_logo = ?, updated_at = CURRENT_TIMESTAMP WHERE id = 1", [$logoUrl]);
        }

        // Refresh session
        $updatedStore = Database::fetchOne("SELECT * FROM stores WHERE id = ?", [$storeId]);
        if ($updatedStore) {
            Auth::login($updatedStore);
        }

        Flight::json([
            'success' => true,
            'message' => 'Foto profil toko berhasil diperbarui!',
            'logo_url' => $logoUrl
        ]);
    }

    public static function removeLogo(): void {
        $storeId = Auth::id() ?? 1;

        Database::query("UPDATE stores SET store_logo = '', updated_at = CURRENT_TIMESTAMP WHERE id = ?", [$storeId]);

        if ($storeId === 1) {
            Database::query("UPDATE settings SET store_logo = '', updated_at = CURRENT_TIMESTAMP WHERE id = 1");
        }

        $updatedStore = Database::fetchOne("SELECT * FROM stores WHERE id = ?", [$storeId]);
        if ($updatedStore) {
            Auth::login($updatedStore);
        }

        Flight::json([
            'success' => true,
            'message' => 'Foto profil toko berhasil dihapus'
        ]);
    }

    public static function backup(): void {
        $storeId = Auth::id() ?? 1;
        $store = Auth::store() ?? Database::fetchOne("SELECT * FROM stores WHERE id = ?", [$storeId]);
        $backup = [
            'app' => 'Tokomu',
            'version' => '2.0',
            'exported_at' => date('Y-m-d H:i:s'),
            'store' => $store,
            'tables' => [
                'categories' => Database::fetchAll("SELECT * FROM categories"),
                'products' => Database::fetchAll("SELECT * FROM products WHERE store_id = ?", [$storeId]),
                'transactions' => Database::fetchAll("SELECT * FROM transactions WHERE store_id = ?", [$storeId]),
                'transaction_items' => Database::fetchAll("
                    SELECT ti.* FROM transaction_items ti 
                    JOIN transactions t ON ti.transaction_id = t.id 
                    WHERE t.store_id = ?
                ", [$storeId]),
                'kasbon' => Database::fetchAll("SELECT * FROM kasbon WHERE store_id = ?", [$storeId]),
                'kasbon_payments' => Database::fetchAll("
                    SELECT kp.* FROM kasbon_payments kp 
                    JOIN kasbon k ON kp.kasbon_id = k.id 
                    WHERE k.store_id = ?
                ", [$storeId]),
            ]
        ];

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="Backup_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $store['store_name'] ?? 'Tokomu') . '_' . date('Ymd_His') . '.json"');
        echo json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function restore(): void {
        if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
            Flight::json(['success' => false, 'message' => 'File backup tidak valid atau gagal diunggah'], 400);
            return;
        }

        $content = file_get_contents($_FILES['backup_file']['tmp_name']);
        $json = json_decode($content, true);

        if (!$json || !isset($json['tables'])) {
            Flight::json(['success' => false, 'message' => 'Format file backup tidak sesuai'], 400);
            return;
        }

        $db = Database::get();

        try {
            $db->beginTransaction();

            foreach ($json['tables'] as $tableName => $rows) {
                // Sanitize table name against known list
                if (!in_array($tableName, ['settings', 'categories', 'products', 'transactions', 'transaction_items', 'kasbon', 'kasbon_payments', 'stock_logs'])) {
                    continue;
                }

                $db->exec("DELETE FROM $tableName");

                if (!empty($rows)) {
                    $firstRow = $rows[0];
                    $columns = array_keys($firstRow);
                    $colsStr = implode(',', $columns);
                    $placeholders = implode(',', array_fill(0, count($columns), '?'));

                    $stmt = $db->prepare("INSERT INTO $tableName ($colsStr) VALUES ($placeholders)");

                    foreach ($rows as $row) {
                        $stmt->execute(array_values($row));
                    }
                }
            }

            $db->commit();
            Flight::json(['success' => true, 'message' => 'Data berhasil dipulihkan dari file backup']);
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            Flight::json(['success' => false, 'message' => 'Gagal restore data: ' . $e->getMessage()], 500);
        }
    }

    public static function resetDemo(): void {
        $db = Database::get();
        $db->exec("
            DELETE FROM transaction_items;
            DELETE FROM transactions;
            DELETE FROM kasbon_payments;
            DELETE FROM kasbon;
            DELETE FROM stock_logs;
            DELETE FROM products;
            DELETE FROM categories;
        ");
        Database::seedInitialData();

        Flight::json(['success' => true, 'message' => 'Katalog sembako berhasil di-reset ke data bawaan']);
    }
}
