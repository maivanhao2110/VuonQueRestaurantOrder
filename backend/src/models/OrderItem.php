<?php
/**
 * OrderItem Model
 */

class OrderItem
{
    private $conn;
    private $table_name = "order_item";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Create order items in batch
     */
    public function createBatch($orderId, $items)
    {
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
    public function getByOrderId($orderId)
    {
        $query = "SELECT oi.*, m.name as menu_item_name 
                  FROM " . $this->table_name . " oi
                  LEFT JOIN menu_item m ON oi.menu_item_id = m.id
                  WHERE oi.order_id = :order_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update item status
     */
    public function updateStatus($id, $status)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Get item by ID
     */
    public function getById($id)
    {
        $query = "SELECT oi.*, m.name as menu_item_name 
                  FROM " . $this->table_name . " oi
                  LEFT JOIN menu_item m ON oi.menu_item_id = m.id
                  WHERE oi.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Create single item
     */
    public function create($orderId, $menuItemId, $quantity, $price)
    {
        $query = "INSERT INTO " . $this->table_name . "
                  (order_id, menu_item_id, quantity, price, status)
                  VALUES (:order_id, :menu_item_id, :quantity, :price, 'WAITING')";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':menu_item_id', $menuItemId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Update quantity
     */
    public function updateQuantity($id, $quantity)
    {
        $query = "UPDATE " . $this->table_name . " SET quantity = :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Delete item
     */
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>