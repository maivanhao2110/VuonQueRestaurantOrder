<?php
require_once __DIR__ . '/../../services/StatictisService.php';
require_once __DIR__ . '/../../utils/Response.php';

class AdminStatisticsController {
    private $statService;

    public function __construct($container) {
        $this->statService = $container->get('statService');
    }

    public function getStatistics() {
        try {
            $from = $_GET['from'] ?? null;
            $to = $_GET['to'] ?? null;
            $data = $this->statService->getOverview($from, $to);
            Response::success('Lấy thống kê thành công', $data);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }
}
?>
