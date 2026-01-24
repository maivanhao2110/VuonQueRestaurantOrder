<?php
require_once __DIR__ . '/BaseRepository.php';

class StatisticsRepository extends BaseRepository {
    // Override __construct if needed, but BaseRepository handles db connection
    
    // No specific table_name needed as it aggregates data
    protected $table_name = "orders"; // Dummy

    public function getTotalOrders($fromDt, $toDt) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE created_at BETWEEN :from_dt AND :to_dt");
        $stmt->execute([':from_dt' => $fromDt, ':to_dt' => $toDt]);
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function getOrdersByStatus($fromDt, $toDt) {
        $stmt = $this->conn->prepare("SELECT status, COUNT(*) AS count FROM orders WHERE created_at BETWEEN :from_dt AND :to_dt GROUP BY status");
        $stmt->execute([':from_dt' => $fromDt, ':to_dt' => $toDt]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInvoiceStats($fromDt, $toDt) {
        $stmt = $this->conn->prepare(
            "SELECT
                COUNT(id) AS invoices_total,
                COUNT(id) AS invoices_paid,
                0 AS invoices_pending,
                COALESCE(SUM(total_amount), 0) AS revenue_paid
             FROM invoice
             WHERE created_at BETWEEN :from_dt AND :to_dt"
        );
        $stmt->execute([':from_dt' => $fromDt, ':to_dt' => $toDt]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getTopItems($fromDt, $toDt) {
        $stmt = $this->conn->prepare(
            "SELECT
                oi.menu_item_id,
                m.name AS menu_item_name,
                SUM(oi.quantity) AS total_quantity,
                SUM(oi.quantity * oi.price) AS total_amount
             FROM order_item oi
             INNER JOIN orders o ON o.id = oi.order_id
             LEFT JOIN menu_item m ON m.id = oi.menu_item_id
             WHERE o.created_at BETWEEN :from_dt AND :to_dt
             GROUP BY oi.menu_item_id, m.name
             ORDER BY total_quantity DESC
             LIMIT 5"
        );
        $stmt->execute([':from_dt' => $fromDt, ':to_dt' => $toDt]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
