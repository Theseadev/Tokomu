<?php

namespace App\Controllers;

use Flight;
use App\Database;
use App\Auth;
use Exception;

class ProductController {

    public static function getAll(): void {
        $storeId = Auth::id() ?? 1;
        $q = Flight::request()->query['q'] ?? '';
        $category = Flight::request()->query['category_id'] ?? '';
        $lowStock = Flight::request()->query['low_stock'] ?? '';

        $sql = "
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.store_id = ?
        ";
        $params = [$storeId];

        if (!empty($q)) {
            $sql .= " AND (p.name LIKE ? OR p.barcode LIKE ?)";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }

        if (!empty($category)) {
            $sql .= " AND p.category_id = ?";
            $params[] = (int)$category;
        }

        if ($lowStock === '1' || $lowStock === 'true') {
            $sql .= " AND p.stock <= p.min_stock";
        }

        $sql .= " ORDER BY p.stock <= p.min_stock DESC, p.name ASC";

        $products = Database::fetchAll($sql, $params);
        $categories = Database::fetchAll("SELECT * FROM categories ORDER BY sort_order ASC, name ASC");

        Flight::json([
            'success' => true,
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public static function getOne(string $id): void {
        $storeId = Auth::id() ?? 1;
        $product = Database::fetchOne("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ? AND p.store_id = ?
        ", [(int)$id, $storeId]);

        if ($product) {
            Flight::json(['success' => true, 'product' => $product]);
        } else {
            Flight::json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }
    }

    public static function save(): void {
        $storeId = Auth::id() ?? 1;
        $data = \App\Helpers\RequestHelper::getJsonBody();
        $id = !empty($data['id']) ? (int)$data['id'] : null;

        $name = trim($data['name'] ?? '');
        $barcode = trim($data['barcode'] ?? '');
        $categoryId = !empty($data['category_id']) ? (int)$data['category_id'] : null;
        $unit = trim($data['unit'] ?? 'pcs') ?: 'pcs';
        $buyPrice = (float)($data['buy_price'] ?? 0);
        $sellPrice = (float)($data['sell_price'] ?? 0);
        $stock = (float)($data['stock'] ?? 0);
        $minStock = (float)($data['min_stock'] ?? 5);
        $image = trim($data['image'] ?? '');

        if (empty($name)) {
            Flight::json(['success' => false, 'message' => 'Nama produk wajib diisi'], 400);
            return;
        }

        if ($sellPrice <= 0) {
            Flight::json(['success' => false, 'message' => 'Harga jual harus lebih dari 0'], 400);
            return;
        }

        // Auto generate barcode if empty
        if (empty($barcode)) {
            $barcode = '899' . date('ymd') . rand(100, 999);
        } else {
            // Check unique barcode within store
            $checkSql = "SELECT id FROM products WHERE barcode = ? AND store_id = ? AND is_active = 1";
            $checkParams = [$barcode, $storeId];
            if ($id) {
                $checkSql .= " AND id != ?";
                $checkParams[] = $id;
            }
            $exists = Database::fetchOne($checkSql, $checkParams);
            if ($exists) {
                Flight::json(['success' => false, 'message' => 'Barcode sudah digunakan oleh produk lain di tokomu'], 400);
                return;
            }
        }

        if ($id) {
            // Update
            $old = Database::fetchOne("SELECT stock FROM products WHERE id = ? AND store_id = ?", [$id, $storeId]);
            if (!$old) {
                Flight::json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
                return;
            }
            $oldStock = (float)$old['stock'];

            Database::query("
                UPDATE products SET
                    barcode = ?, name = ?, category_id = ?, unit = ?,
                    buy_price = ?, sell_price = ?, stock = ?, min_stock = ?,
                    image = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND store_id = ?
            ", [$barcode, $name, $categoryId, $unit, $buyPrice, $sellPrice, $stock, $minStock, $image, $id, $storeId]);

            if ($stock !== $oldStock) {
                Database::query("
                    INSERT INTO stock_logs (product_id, type, qty_change, stock_before, stock_after, note)
                    VALUES (?, 'adjustment', ?, ?, ?, 'Koreksi stok manual')
                ", [$id, $stock - $oldStock, $oldStock, $stock]);
            }

            Flight::json(['success' => true, 'message' => 'Produk berhasil diperbarui']);
        } else {
            // Insert
            Database::query("
                INSERT INTO products (
                    store_id, barcode, name, category_id, unit, buy_price, sell_price, stock, min_stock, image, is_active
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
            ", [$storeId, $barcode, $name, $categoryId, $unit, $buyPrice, $sellPrice, $stock, $minStock, $image]);

            $newId = (int)Database::lastInsertId();
            Database::query("
                INSERT INTO stock_logs (product_id, type, qty_change, stock_before, stock_after, note)
                VALUES (?, 'initial', ?, 0, ?, 'Stok awal produk baru')
            ", [$newId, $stock, $stock]);

            Flight::json(['success' => true, 'message' => 'Produk baru berhasil ditambahkan', 'id' => $newId]);
        }
    }

    public static function restock(): void {
        $storeId = Auth::id() ?? 1;
        $data = \App\Helpers\RequestHelper::getJsonBody();
        $id = !empty($data['id']) ? (int)$data['id'] : null;
        $addQty = (float)($data['add_qty'] ?? 0);
        $newBuyPrice = !empty($data['buy_price']) ? (float)$data['buy_price'] : null;
        $note = trim($data['note'] ?? 'Kulakan / Restock');

        if (!$id || $addQty <= 0) {
            Flight::json(['success' => false, 'message' => 'Jumlah barang masuk harus lebih dari 0'], 400);
            return;
        }

        $prod = Database::fetchOne("SELECT * FROM products WHERE id = ? AND store_id = ?", [$id, $storeId]);
        if (!$prod) {
            Flight::json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
            return;
        }

        $oldStock = (float)$prod['stock'];
        $newStock = $oldStock + $addQty;

        if ($newBuyPrice !== null && $newBuyPrice > 0) {
            Database::query("
                UPDATE products SET stock = ?, buy_price = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND store_id = ?
            ", [$newStock, $newBuyPrice, $id, $storeId]);
        } else {
            Database::query("
                UPDATE products SET stock = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND store_id = ?
            ", [$newStock, $id, $storeId]);
        }

        Database::query("
            INSERT INTO stock_logs (product_id, type, qty_change, stock_before, stock_after, note)
            VALUES (?, 'restock', ?, ?, ?, ?)
        ", [$id, $addQty, $oldStock, $newStock, $note]);

        Flight::json([
            'success' => true,
            'message' => "Stok berhasil ditambahkan. Stok saat ini: $newStock {$prod['unit']}"
        ]);
    }

    public static function delete(string $id): void {
        $storeId = Auth::id() ?? 1;
        Database::query("UPDATE products SET is_active = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND store_id = ?", [(int)$id, $storeId]);
        Flight::json(['success' => true, 'message' => 'Produk berhasil dihapus']);
    }

    public static function masterLookup(): void {
        $storeId = Auth::id() ?? 1;
        $barcode = Flight::request()->query['barcode'] ?? '';
        $result = \App\Services\MasterCatalogService::lookup($barcode, $storeId);
        Flight::json(array_merge(['success' => true], $result));
    }

    public static function masterSearch(): void {
        $q = Flight::request()->query['q'] ?? '';
        $results = \App\Services\MasterCatalogService::searchByName($q);
        Flight::json(['success' => true, 'results' => $results]);
    }
}
