<?php

namespace App\Controllers;

use Flight;
use App\Database;
use App\Auth;
use Exception;

class ReportController {

    public static function getSummary(): void {
        $storeId = Auth::id() ?? 1;
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        // Today's metrics
        $todayStats = Database::fetchOne("
            SELECT 
                COUNT(*) as trx_count,
                COALESCE(SUM(grand_total), 0) as total_sales,
                COALESCE(SUM(total_profit), 0) as total_profit
            FROM transactions
            WHERE date(created_at) = ? AND status != 'cancelled' AND store_id = ?
        ", [$today, $storeId]);

        // Month metrics
        $startOfMonth = $thisMonth . '-01 00:00:00';
        $endOfMonth = date('Y-m-t 23:59:59', strtotime($startOfMonth));
        $monthStats = Database::fetchOne("
            SELECT 
                COUNT(*) as trx_count,
                COALESCE(SUM(grand_total), 0) as total_sales,
                COALESCE(SUM(total_profit), 0) as total_profit
            FROM transactions
            WHERE created_at >= ? AND created_at <= ? AND status != 'cancelled' AND store_id = ?
        ", [$startOfMonth, $endOfMonth, $storeId]);

        // Kasbon pending
        $kasbonPending = Database::fetchOne("
            SELECT 
                COUNT(*) as count_unpaid,
                COALESCE(SUM(remaining_debt), 0) as total_unpaid
            FROM kasbon
            WHERE status != 'paid' AND store_id = ?
        ", [$storeId]);

        // Low stock products count
        $lowStockCount = Database::fetchOne("
            SELECT COUNT(*) as cnt FROM products WHERE stock <= min_stock AND is_active = 1 AND store_id = ?
        ", [$storeId])['cnt'] ?? 0;

        // Low stock products list (top 6 critical)
        $lowStockList = Database::fetchAll("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.stock <= p.min_stock AND p.is_active = 1 AND p.store_id = ?
            ORDER BY (p.stock - p.min_stock) ASC
            LIMIT 6
        ", [$storeId]);

        // Last 7 days chart data
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-$i days"));
            $dayName = date('d M', strtotime($day));

            $dayStat = Database::fetchOne("
                SELECT 
                    COALESCE(SUM(grand_total), 0) as sales,
                    COALESCE(SUM(total_profit), 0) as profit,
                    COUNT(*) as count
                FROM transactions
                WHERE date(created_at) = ? AND status != 'cancelled' AND store_id = ?
            ", [$day, $storeId]);

            $chartData[] = [
                'date' => $day,
                'label' => $dayName,
                'sales' => (float)($dayStat['sales'] ?? 0),
                'profit' => (float)($dayStat['profit'] ?? 0),
                'count' => (int)($dayStat['count'] ?? 0)
            ];
        }

        // Top 8 selling products of all time
        $topProducts = Database::fetchAll("
            SELECT 
                ti.product_name,
                ti.unit,
                SUM(ti.qty) as total_qty,
                SUM(ti.subtotal) as total_revenue,
                SUM(ti.profit) as total_profit
            FROM transaction_items ti
            JOIN transactions t ON ti.transaction_id = t.id
            WHERE t.status != 'cancelled' AND t.store_id = ?
            GROUP BY ti.product_name, ti.unit
            ORDER BY total_qty DESC
            LIMIT 8
        ", [$storeId]);

        Flight::json([
            'success' => true,
            'summary' => [
                'today_sales' => (float)($todayStats['total_sales'] ?? 0),
                'today_profit' => (float)($todayStats['total_profit'] ?? 0),
                'today_trx' => (int)($todayStats['trx_count'] ?? 0),
                'month_sales' => (float)($monthStats['total_sales'] ?? 0),
                'month_profit' => (float)($monthStats['total_profit'] ?? 0),
                'month_trx' => (int)($monthStats['trx_count'] ?? 0),
                'kasbon_unpaid' => (float)($kasbonPending['total_unpaid'] ?? 0),
                'kasbon_count' => (int)($kasbonPending['count_unpaid'] ?? 0),
                'low_stock_count' => (int)$lowStockCount,
                'low_stock_list' => $lowStockList,
                'chart_data' => $chartData,
                'top_products' => $topProducts
            ]
        ]);
    }

    public static function getTransactions(): void {
        $storeId = Auth::id() ?? 1;
        $startDate = Flight::request()->query['start_date'] ?? date('Y-m-01');
        $endDate = Flight::request()->query['end_date'] ?? date('Y-m-d');
        $paymentMethod = Flight::request()->query['payment_method'] ?? '';
        $q = Flight::request()->query['q'] ?? '';

        $sql = "
            SELECT t.*, 
                   (SELECT COUNT(*) FROM transaction_items WHERE transaction_id = t.id) as item_count
            FROM transactions t
            WHERE date(t.created_at) >= ? AND date(t.created_at) <= ? AND t.store_id = ?
        ";
        $params = [$startDate, $endDate, $storeId];

        if (!empty($paymentMethod)) {
            $sql .= " AND t.payment_method = ?";
            $params[] = $paymentMethod;
        }

        if (!empty($q)) {
            $sql .= " AND (t.invoice_no LIKE ? OR t.customer_name LIKE ?)";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }

        $sql .= " ORDER BY t.created_at DESC LIMIT 100";

        $transactions = Database::fetchAll($sql, $params);

        Flight::json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }

    public static function exportCsv(): void {
        $storeId = Auth::id() ?? 1;
        $startDate = Flight::request()->query['start_date'] ?? date('Y-m-01');
        $endDate = Flight::request()->query['end_date'] ?? date('Y-m-d');

        $transactions = Database::fetchAll("
            SELECT t.* 
            FROM transactions t
            WHERE date(t.created_at) >= ? AND date(t.created_at) <= ? AND t.store_id = ?
            ORDER BY t.created_at ASC
        ", [$startDate, $endDate, $storeId]);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Laporan_Penjualan_' . $startDate . '_sd_' . $endDate . '.csv');

        $output = fopen('php://output', 'w');
        // Add BOM for Indonesian Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['No Invoice', 'Tanggal & Jam', 'Pelanggan', 'Metode Bayar', 'Subtotal', 'Diskon', 'Grand Total', 'Laba Kotor', 'Status']);

        foreach ($transactions as $t) {
            fputcsv($output, [
                $t['invoice_no'],
                $t['created_at'],
                $t['customer_name'],
                strtoupper($t['payment_method']),
                $t['subtotal'],
                $t['discount_amount'],
                $t['grand_total'],
                $t['total_profit'],
                strtoupper($t['status'])
            ]);
        }

        fclose($output);
        exit;
    }
}
