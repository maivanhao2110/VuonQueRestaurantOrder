<?php
require_once __DIR__ . '/../../services/InvoiceService.php';
require_once __DIR__ . '/../../utils/Response.php';

class AdminInvoiceController {
    private $invoiceService;

    public function __construct($container) {
        $this->invoiceService = $container->get('invoiceService');
    }

    public function listInvoices() {
        try {
            $data = $this->invoiceService->getAll();
            Response::success('Lấy danh sách hóa đơn thành công', $data);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function getInvoice($id) {
        try {
            $data = $this->invoiceService->getById($id);
            if (!$data) Response::error('Không tìm thấy hóa đơn');
            Response::success('Lấy chi tiết hóa đơn thành công', $data);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }
}
?>
