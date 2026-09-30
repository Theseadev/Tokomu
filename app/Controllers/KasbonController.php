<?php

namespace App\Controllers;

use Flight;
use App\Database;
use App\Auth;
use Exception;

class KasbonController {

    public static function list(): void {
        $storeId = Auth::id() ?? 1;
        $status = Flight::request()->query['status'] ?? ''; // unpaid, partial, paid
        $q = Flight::request()->query['q'] ?? '';

        $sql = "
            SELECT k.*, t.invoice_no, t.created_at as transaction_date
            FROM kasbon k
            LEFT JOIN transactions t ON k.transaction_id = t.id
            WHERE k.store_id = ?
        ";
        $params = [$storeId];

        if (!empty($status)) {
            $sql .= " AND k.status = ?";
            $params[] = $status;
        }

        if (!empty($q)) {
            $sql .= " AND (k.customer_name LIKE ? OR k.customer_phone LIKE ?)";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }

        $sql .= " ORDER BY CASE WHEN k.status != 'paid' THEN 0 ELSE 1 END, k.created_at DESC";

        $kasbonList = Database::fetchAll($sql, $params);

        // Calculate summary for this store
        $totalUnpaid = Database::fetchOne("
            SELECT SUM(remaining_debt) as total FROM kasbon WHERE status != 'paid' AND store_id = ?
        ", [$storeId])['total'] ?? 0;

        Flight::json([
            'success' => true,
            'kasbon' => $kasbonList,
            'total_unpaid' => (float)$totalUnpaid
        ]);
    }

    public static function pay(): void {
        $storeId = Auth::id() ?? 1;
        $data = \App\Helpers\RequestHelper::getJsonBody();
        $id = !empty($data['id']) ? (int)$data['id'] : null;
        $amount = (float)($data['amount'] ?? 0);
        $notes = trim($data['notes'] ?? 'Pembayaran kasbon');

        if (!$id || $amount <= 0) {
            Flight::json(['success' => false, 'message' => 'Nominal pembayaran tidak valid'], 400);
            return;
        }

        $kasbon = Database::fetchOne("SELECT * FROM kasbon WHERE id = ? AND store_id = ?", [$id, $storeId]);
        if (!$kasbon) {
            Flight::json(['success' => false, 'message' => 'Data kasbon tidak ditemukan'], 404);
            return;
        }

        if ($kasbon['status'] === 'paid') {
            Flight::json(['success' => false, 'message' => 'Kasbon ini sudah lunas'], 400);
            return;
        }

        $remaining = (float)$kasbon['remaining_debt'];
        $actualPay = min($amount, $remaining);
        $newPaid = (float)$kasbon['paid_amount'] + $actualPay;
        $newRemaining = max(0, $remaining - $actualPay);
        $newStatus = ($newRemaining <= 0) ? 'paid' : 'partial';

        Database::query("
            INSERT INTO kasbon_payments (kasbon_id, payment_amount, notes, payment_date)
            VALUES (?, ?, ?, CURRENT_TIMESTAMP)
        ", [$id, $actualPay, $notes]);

        Database::query("
            UPDATE kasbon SET
                paid_amount = ?, remaining_debt = ?, status = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ? AND store_id = ?
        ", [$newPaid, $newRemaining, $newStatus, $id, $storeId]);

        // If fully paid and linked to transaction, update transaction status
        if ($newStatus === 'paid' && !empty($kasbon['transaction_id'])) {
            Database::query("
                UPDATE transactions SET status = 'completed', change_amount = 0 WHERE id = ? AND store_id = ?
            ", [$kasbon['transaction_id'], $storeId]);
        }

        Flight::json([
            'success' => true,
            'message' => ($newStatus === 'paid') ? 'Kasbon LUNAS!' : "Pembayaran Rp " . number_format($actualPay, 0, ',', '.') . " dicatat. Sisa: Rp " . number_format($newRemaining, 0, ',', '.'),
            'new_status' => $newStatus,
            'remaining' => $newRemaining
        ]);
    }

    public static function getReceipt(string $id): void {
        $storeId = Auth::id() ?? 1;
        $kasbon = Database::fetchOne("
            SELECT k.*, t.invoice_no, t.created_at as transaction_date
            FROM kasbon k
            LEFT JOIN transactions t ON k.transaction_id = t.id
            WHERE k.id = ? AND k.store_id = ?
        ", [(int)$id, $storeId]);

        if (!$kasbon) {
            Flight::json(['success' => false, 'message' => 'Data kasbon tidak ditemukan'], 404);
            return;
        }

        $payments = Database::fetchAll("
            SELECT * FROM kasbon_payments WHERE kasbon_id = ? ORDER BY payment_date ASC
        ", [(int)$id]);

        $store = Auth::store() ?? Database::fetchOne("SELECT * FROM stores WHERE id = ?", [$storeId]);
        $settings = Database::fetchOne("SELECT * FROM settings WHERE id = 1") ?: [];

        Flight::json([
            'success' => true,
            'receipt' => [
                'type' => 'kasbon',
                'store' => [
                    'name' => $store['store_name'] ?? $settings['store_name'] ?? 'Tokomu',
                    'tagline' => $store['store_tagline'] ?? $settings['store_tagline'] ?? '',
                    'address' => $store['store_address'] ?? $settings['store_address'] ?? 'Jl. Pemajatan Komp. Permata Hijau 2, Blok B No.2',
                    'phone' => $store['store_phone'] ?? $settings['store_phone'] ?? '',
                    'paper_size' => $store['paper_size'] ?? $settings['paper_size'] ?? '58mm',
                ],
                'kasbon' => [
                    'id' => $kasbon['id'],
                    'invoice_no' => $kasbon['invoice_no'] ?: ('KB-' . str_pad($kasbon['id'], 4, '0', STR_PAD_LEFT)),
                    'customer_name' => $kasbon['customer_name'],
                    'customer_phone' => $kasbon['customer_phone'],
                    'total_debt' => (float)$kasbon['total_debt'],
                    'paid_amount' => (float)$kasbon['paid_amount'],
                    'remaining_debt' => (float)$kasbon['remaining_debt'],
                    'status' => $kasbon['status'],
                    'created_at' => date('d/m/Y H:i', strtotime($kasbon['created_at'])),
                    'due_date' => $kasbon['due_date'] ? date('d/m/Y', strtotime($kasbon['due_date'])) : '-',
                    'notes' => $kasbon['notes']
                ],
                'payments' => array_map(function($p) {
                    return [
                        'amount' => (float)$p['payment_amount'],
                        'date' => date('d/m/Y H:i', strtotime($p['payment_date'])),
                        'notes' => $p['notes']
                    ];
                }, $payments)
            ]
        ]);
    }
}
