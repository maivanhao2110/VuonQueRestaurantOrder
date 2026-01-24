<?php
/**
 * Statictis Service (typo kept for compatibility)
 */

class StatictisService {
	private $db;

	public function __construct($db) {
		$this->db = $db;
	}

	private function normalizeRange($from, $to) {
		// Defaults: last 30 days
		if (!$to) {
			$to = date('Y-m-d');
		}
		if (!$from) {
			$from = date('Y-m-d', strtotime($to . ' -29 days'));
		}

		// Use full-day boundaries
		$fromDt = $from . ' 00:00:00';
		$toDt = $to . ' 23:59:59';

		return [$from, $to, $fromDt, $toDt];
	}

	public function getOverview($from = null, $to = null) {
		[$fromDate, $toDate, $fromDt, $toDt] = $this->normalizeRange($from, $to);

		// Total orders
		$stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM orders WHERE created_at BETWEEN :from_dt AND :to_dt");
		$stmt->execute([':from_dt' => $fromDt, ':to_dt' => $toDt]);
		$totalOrders = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

		// Orders by status
		$stmt = $this->db->prepare("SELECT status, COUNT(*) AS count FROM orders WHERE created_at BETWEEN :from_dt AND :to_dt GROUP BY status");
		$stmt->execute([':from_dt' => $fromDt, ':to_dt' => $toDt]);
		$byStatusRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$ordersByStatus = [];
		foreach ($byStatusRows as $row) {
			$ordersByStatus[$row['status']] = (int)$row['count'];
		}

		// Invoices & payments
		// Invoices & payments
		$stmt = $this->db->prepare(
			"SELECT
				COUNT(id) AS invoices_total,
				COUNT(id) AS invoices_paid,
				0 AS invoices_pending,
				COALESCE(SUM(total_amount), 0) AS revenue_paid
			 FROM invoice
			 WHERE created_at BETWEEN :from_dt AND :to_dt"
		);
		$stmt->execute([':from_dt' => $fromDt, ':to_dt' => $toDt]);
		$inv = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

		// Top items by quantity
		$stmt = $this->db->prepare(
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
		$topItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return [
			'range' => [
				'from' => $fromDate,
				'to' => $toDate,
			],
			'orders' => [
				'total' => $totalOrders,
				'by_status' => $ordersByStatus,
			],
			'invoices' => [
				'total' => (int)($inv['invoices_total'] ?? 0),
				'paid' => (int)($inv['invoices_paid'] ?? 0),
				'pending' => (int)($inv['invoices_pending'] ?? 0),
			],
			'revenue' => [
				'paid_total' => (float)($inv['revenue_paid'] ?? 0),
			],
			'top_items' => $topItems,
		];
	}
}

?>
