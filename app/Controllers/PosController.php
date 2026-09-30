<?php

namespace App\Controllers;

use Flight;
use App\Database;
use App\Auth;
use PDO;
use Exception;

class PosController {

    public static function getPosCatalog(): void {
        $storeId = Auth::id() ?? 1;
        $q = Flight::request()->query['q'] ?? '';
        $categoryId = Flight::request()->query['category_id'] ?? '';

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

        if (!empty($categoryId) && is_numeric($categoryId)) {
            $sql .= " AND p.category_id = ?";
            $params[] = (int)$categoryId;
        }

        $sql .= " ORDER BY p.name ASC";

        $products = Database::fetchAll($sql, $params);
        $categories = Database::fetchAll("SELECT * FROM categories ORDER BY sort_order ASC, name ASC");

        Flight::json([
            'success' => true,
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public static function scanBarcode(): void {
        $storeId = Auth::id() ?? 1;
        $barcode = Flight::request()->query['barcode'] ?? '';
        if (empty($barcode)) {
            Flight::json(['success' => false, 'message' => 'Barcode kosong'], 400);
            return;
        }

        $product = Database::fetchOne("
            SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.store_id = ? AND p.barcode = ?
        ", [$storeId, $barcode]);

        if ($product) {
            Flight::json(['success' => true, 'product' => $product]);
        } else {
            Flight::json(['success' => false, 'message' => 'Produk dengan barcode tersebut tidak ditemukan di tokomu'], 404);
        }
    }

    public static function checkout(): void {
        $storeId = Auth::id() ?? 1;
        $data = \App\Helpers\RequestHelper::getJsonBody();
        $items = $data['items'] ?? [];

        if (empty($items) || !is_array($items)) {
            Flight::json(['success' => false, 'message' => 'Keranjang belanja masih kosong'], 400);
            return;
        }

        $customerName = trim($data['customer_name'] ?? 'Pelanggan Umum') ?: 'Pelanggan Umum';
        $customerPhone = trim($data['customer_phone'] ?? '');
        $paymentMethod = $data['payment_method'] ?? 'cash'; // cash, qris, transfer, kasbon
        $discountAmount = (float)($data['discount_amount'] ?? 0);
        $cashAmount = (float)($data['cash_amount'] ?? 0);
        $notes = trim($data['notes'] ?? '');
        $dueDate = !empty($data['due_date']) ? $data['due_date'] : date('Y-m-d', strtotime('+7 days'));

        $db = Database::get();

        try {
            $db->beginTransaction();

            // Calculate totals and verify items
            $subtotal = 0;
            $totalProfit = 0;
            $processedItems = [];

            foreach ($items as $item) {
                $qty = (float)($item['qty'] ?? 1);
                if ($qty <= 0) continue;

                $productId = !empty($item['id']) && is_numeric($item['id']) ? (int)$item['id'] : null;
                $productName = trim($item['name'] ?? 'Item');
                $sellPrice = (float)($item['sell_price'] ?? 0);
                $buyPrice = (float)($item['buy_price'] ?? 0);
                $unit = trim($item['unit'] ?? 'pcs');

                // If existing product in DB, fetch actual details & check stock
                if ($productId) {
                    $prod = Database::fetchOne("SELECT * FROM products WHERE id = ? AND store_id = ?", [$productId, $storeId]);
                    if ($prod) {
                        $productName = $prod['name'];
                        $unit = $prod['unit'];
                        $buyPrice = (float)$prod['buy_price'];
                        // Use latest sell price from product unless explicitly customized
                        if (empty($item['sell_price'])) {
                            $sellPrice = (float)$prod['sell_price'];
                        }

                        // Deduct stock
                        $newStock = $prod['stock'] - $qty;
                        Database::query("UPDATE products SET stock = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND store_id = ?", [$newStock, $productId, $storeId]);

                        // Log stock movement
                        Database::query("
                            INSERT INTO stock_logs (product_id, type, qty_change, stock_before, stock_after, note)
                            VALUES (?, 'sale', ?, ?, ?, ?)
                        ", [$productId, -$qty, $prod['stock'], $newStock, 'Penjualan Kasir']);
                    }
                }

                $itemSubtotal = $sellPrice * $qty;
                $itemProfit = ($sellPrice - $buyPrice) * $qty;

                $subtotal += $itemSubtotal;
                $totalProfit += $itemProfit;

                $processedItems[] = [
                    'product_id' => $productId,
                    'product_name' => $productName,
                    'unit' => $unit,
                    'buy_price' => $buyPrice,
                    'sell_price' => $sellPrice,
                    'qty' => $qty,
                    'subtotal' => $itemSubtotal,
                    'profit' => $itemProfit
                ];
            }

            if (empty($processedItems)) {
                $db->rollBack();
                Flight::json(['success' => false, 'message' => 'Tidak ada item yang valid'], 400);
                return;
            }

            $grandTotal = max(0, $subtotal - $discountAmount);
            $totalProfit = max(0, $totalProfit - $discountAmount);

            // Invoice number format: TRX-YYYYMMDD-XXXX (scoped with store_id to avoid multi-store collision)
            $datePrefix = date('Ymd');
            $countToday = Database::fetchOne("
                SELECT COUNT(*) as cnt FROM transactions 
                WHERE store_id = ? AND date(created_at) = date('now')
            ", [$storeId])['cnt'] ?? 0;
            $sequence = str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
            $invoiceNo = ($storeId > 1) ? "TRX-{$datePrefix}-S{$storeId}-{$sequence}" : "TRX-{$datePrefix}-{$sequence}";

            // Fail-safe against invoice_no collisions
            while (Database::fetchOne("SELECT id FROM transactions WHERE invoice_no = ?", [$invoiceNo])) {
                $countToday++;
                $sequence = str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
                $invoiceNo = "TRX-{$datePrefix}-S{$storeId}-{$sequence}";
            }

            // Payment calculations
            $changeAmount = 0;
            $trxStatus = 'completed';

            if ($paymentMethod === 'cash') {
                if ($cashAmount < $grandTotal) {
                    $db->rollBack();
                    Flight::json(['success' => false, 'message' => 'Nominal uang tunai kurang dari total belanja'], 400);
                    return;
                }
                $changeAmount = max(0, $cashAmount - $grandTotal);
            } elseif ($paymentMethod === 'kasbon') {
                $trxStatus = 'kasbon';
                $cashAmount = 0;
                $changeAmount = 0;
            } else {
                // QRIS / Transfer
                $cashAmount = $grandTotal;
                $changeAmount = 0;
            }

            // Insert transaction
            Database::query("
                INSERT INTO transactions (
                    store_id, invoice_no, customer_name, customer_phone, payment_method,
                    subtotal, discount_amount, grand_total, cash_amount, change_amount,
                    status, total_profit, notes, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
            ", [
                $storeId, $invoiceNo, $customerName, $customerPhone, $paymentMethod,
                $subtotal, $discountAmount, $grandTotal, $cashAmount, $changeAmount,
                $trxStatus, $totalProfit, $notes
            ]);

            $transactionId = (int)Database::lastInsertId();

            // Insert transaction items
            $itemStmt = $db->prepare("
                INSERT INTO transaction_items (
                    transaction_id, product_id, product_name, unit,
                    buy_price, sell_price, qty, subtotal, profit
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($processedItems as $it) {
                $itemStmt->execute([
                    $transactionId,
                    $it['product_id'],
                    $it['product_name'],
                    $it['unit'],
                    $it['buy_price'],
                    $it['sell_price'],
                    $it['qty'],
                    $it['subtotal'],
                    $it['profit']
                ]);
            }

            // If Kasbon, insert record into kasbon table
            $kasbonId = null;
            if ($paymentMethod === 'kasbon') {
                Database::query("
                    INSERT INTO kasbon (
                        store_id, transaction_id, customer_name, customer_phone,
                        total_debt, paid_amount, remaining_debt, status, due_date, notes
                    ) VALUES (?, ?, ?, ?, ?, 0, ?, 'unpaid', ?, ?)
                ", [
                    $storeId, $transactionId, $customerName, $customerPhone,
                    $grandTotal, $grandTotal, $dueDate, $notes
                ]);
                $kasbonId = (int)Database::lastInsertId();
            }

            $db->commit();

            // Build Receipt payload for immediate printing / popup
            $receipt = self::buildReceiptPayload($transactionId);

            Flight::json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses',
                'transaction_id' => $transactionId,
                'invoice_no' => $invoiceNo,
                'grand_total' => $grandTotal,
                'change_amount' => $changeAmount,
                'kasbon_id' => $kasbonId,
                'receipt' => $receipt
            ]);

        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            Flight::json(['success' => false, 'message' => 'Gagal memproses transaksi: ' . $e->getMessage()], 500);
        }
    }

    public static function getReceipt(string $id): void {
        $receipt = self::buildReceiptPayload((int)$id);
        if ($receipt) {
            Flight::json(['success' => true, 'receipt' => $receipt]);
        } else {
            Flight::json(['success' => false, 'message' => 'Struk tidak ditemukan'], 404);
        }
    }

    public static function buildReceiptPayload(int $transactionId): ?array {
        $trx = Database::fetchOne("SELECT * FROM transactions WHERE id = ?", [$transactionId]);
        if (!$trx) return null;

        $storeId = (int)($trx['store_id'] ?? 1);
        $items = Database::fetchAll("SELECT * FROM transaction_items WHERE transaction_id = ? ORDER BY id ASC", [$transactionId]);
        $store = Database::fetchOne("SELECT * FROM stores WHERE id = ?", [$storeId])
              ?: Database::fetchOne("SELECT * FROM settings WHERE id = 1")
              ?: [];

        return [
            'store' => [
                'name' => $store['store_name'] ?? 'Tokomu',
                'tagline' => $store['store_tagline'] ?? '',
                'address' => $store['store_address'] ?? '',
                'phone' => $store['store_phone'] ?? '',
                'receipt_header' => $store['receipt_header'] ?? '',
                'receipt_footer' => $store['receipt_footer'] ?? 'Terima Kasih',
                'paper_size' => $store['paper_size'] ?? '58mm',
                'auto_print' => (int)($store['auto_print'] ?? 1),
            ],
            'transaction' => [
                'id' => $trx['id'],
                'invoice_no' => $trx['invoice_no'],
                'date' => date('d/m/Y H:i', strtotime($trx['created_at'])),
                'customer_name' => $trx['customer_name'],
                'customer_phone' => $trx['customer_phone'],
                'payment_method' => strtoupper($trx['payment_method']),
                'subtotal' => (float)$trx['subtotal'],
                'discount_amount' => (float)$trx['discount_amount'],
                'grand_total' => (float)$trx['grand_total'],
                'cash_amount' => (float)$trx['cash_amount'],
                'change_amount' => (float)$trx['change_amount'],
                'status' => $trx['status'],
                'notes' => $trx['notes']
            ],
            'items' => array_map(function($it) {
                return [
                    'name' => $it['product_name'],
                    'unit' => $it['unit'],
                    'qty' => (float)$it['qty'],
                    'price' => (float)$it['sell_price'],
                    'subtotal' => (float)$it['subtotal']
                ];
            }, $items)
        ];
    }
}
