<?php
/**
 * Invoice Entity
 */

class Invoice {
    public $id;
    public $order_id;
    public $total_amount;
    public $type_payment;
    public $created_at;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->order_id = $data['order_id'] ?? null;
            $this->total_amount = $data['total_amount'] ?? null;
            $this->type_payment = $data['type_payment'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
        }
    }
}
?>