<?php
/**
 * OrderItem Entity
 */

class OrderItem {
    public $id;
    public $order_id;
    public $menu_item_id;
    public $quantity;
    public $price;
    public $status;
    public $created_at;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->order_id = $data['order_id'] ?? null;
            $this->menu_item_id = $data['menu_item_id'] ?? null;
            $this->quantity = $data['quantity'] ?? null;
            $this->price = $data['price'] ?? null;
            $this->status = $data['status'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
        }
    }
}
?>