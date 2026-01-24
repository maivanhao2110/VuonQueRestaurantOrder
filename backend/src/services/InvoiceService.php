<?php
require_once __DIR__ . '/../repositories/InvoiceRepository.php';

class InvoiceService {
    private $invoiceRepo;

    public function __construct($db) {
        $this->invoiceRepo = new InvoiceRepository($db);
    }

    public function getAll() {
        return $this->invoiceRepo->getAll();
    }

    public function getById($id) {
        return $this->invoiceRepo->getById($id);
    }
}
?>
