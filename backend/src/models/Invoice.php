<?php
/**
 * Invoice Model
 */

class Invoice {
    private $conn;
    private $table_name = "invoice";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT i.*, o.table_number 
                  FROM " . $this->table_name . " i
                  LEFT JOIN orders o ON i.order_id = o.id
                  ORDER BY i.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT i.*, o.table_number 
                  FROM " . $this->table_name . " i
                  LEFT JOIN orders o ON i.order_id = o.id
                  WHERE i.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($invoice) {
            $query = "SELECT oi.*, m.name as menu_item_name 
                      FROM order_item oi
                      LEFT JOIN menu_item m ON oi.menu_item_id = m.id
                      WHERE oi.order_id = :order_id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':order_id' => $invoice['order_id']]);
            $invoice['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $invoice;
    }
}
?>
