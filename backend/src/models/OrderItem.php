<?php
/**
 * OrderItem Model
 */

class OrderItem {
    private $conn;
    private $table_name = "order_item";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create order items in batch
     */
    public function createBatch($orderId, $items) {
        $query = "INSERT INTO " . $this->table_name . "
                  (order_id, menu_item_id, quantity, price)
                  VALUES (:order_id, :menu_item_id, :quantity, :price)";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($items as $item) {
            $stmt->bindParam(':order_id', $orderId);
            $stmt->bindParam(':menu_item_id', $item['menu_item_id']);
            $stmt->bindParam(':quantity', $item['quantity']);
            $stmt->bindParam(':price', $item['price']);
            
            if (!$stmt->execute()) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get items for an order
     */
    public function getByOrderId($orderId) {
        $query = "SELECT oi.*, m.name as menu_item_name 
                  FROM " . $this->table_name . " oi
                  LEFT JOIN menu_item m ON oi.menu_item_id = m.id
                  WHERE oi.order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
