<?php
/**
 * Order Model
 */

class Order {
    private $conn;
    private $table_name = "orders";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create new order
     */
    public function create($customerName, $tableNumber, $note = '') {
        $query = "INSERT INTO " . $this->table_name . "
                  (customer_name, table_number, note, status, created_at)
                  VALUES (:customer_name, :table_number, :note, 'CREATED', NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':customer_name', $customerName);
        $stmt->bindParam(':table_number', $tableNumber);
        $stmt->bindParam(':note', $note);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    /**
     * Get orders by table number
     */
    public function getByTable($tableNumber, $status = null) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE table_number = :table_number";
        
        if ($status) {
            $query .= " AND status = :status";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':table_number', $tableNumber);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get order by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update order status and sync order items
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            // Sync order items status
            $this->syncOrderItemsStatus($id, $status);
            return true;
        }
        
        return false;
    }
    
    /**
     * Sync order items status based on order status
     */
    private function syncOrderItemsStatus($orderId, $orderStatus) {
        // Map order status to order_item status
        $itemStatus = 'WAITING';
        
        if ($orderStatus == 'COOKING') {
            $itemStatus = 'COOKING';
        } else if ($orderStatus == 'DONE' || $orderStatus == 'CANCELLED') {
            $itemStatus = 'DONE';
        }
        
        // Update all items of this order
        $query = "UPDATE order_item 
                  SET status = :status 
                  WHERE order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $itemStatus);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
    }
}
?>
