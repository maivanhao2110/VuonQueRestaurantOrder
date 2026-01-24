<?php
/**
 * Statictis Service (typo kept for compatibility)
 */

require_once __DIR__ . '/../repositories/StatisticsRepository.php';

class StatictisService {
    private $statsRepo;

    public function __construct($db) {
        $this->statsRepo = new StatisticsRepository($db);
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

        // Usage of Repository methods
        $totalOrders = $this->statsRepo->getTotalOrders($fromDt, $toDt);
        
        $byStatusRows = $this->statsRepo->getOrdersByStatus($fromDt, $toDt);
        $ordersByStatus = [];
        foreach ($byStatusRows as $row) {
            $ordersByStatus[$row['status']] = (int)$row['count'];
        }

        $inv = $this->statsRepo->getInvoiceStats($fromDt, $toDt);
        
        $topItems = $this->statsRepo->getTopItems($fromDt, $toDt);

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
