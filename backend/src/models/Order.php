<?php
/**
 * Order Entity
 */

class Order {
    public $id;
    public $customer_name;
    public $table_number;
    public $staff_id;
    public $status;
    public $note;
    public $created_at;
    public $end_at;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->customer_name = $data['customer_name'] ?? null;
            $this->table_number = $data['table_number'] ?? null;
            $this->staff_id = $data['staff_id'] ?? null;
            $this->status = $data['status'] ?? null;
            $this->note = $data['note'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
            $this->end_at = $data['end_at'] ?? null;
        }
    }
}
?>