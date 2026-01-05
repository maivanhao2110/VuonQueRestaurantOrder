<?php
/**
 * MenuItem Model
 */

class MenuItem {
    private $conn;
    private $table_name = "menu_item";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all available menu items
     */
    public function getAll($categoryId = null) {
        $query = "SELECT m.*, c.name as category_name 
                  FROM " . $this->table_name . " m
                  LEFT JOIN category c ON m.category_id = c.id
                  WHERE m.is_available = 1";
        
        if ($categoryId) {
            $query .= " AND m.category_id = :category_id";
        }
        
        $query .= " ORDER BY m.category_id, m.name ASC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($categoryId) {
            $stmt->bindParam(':category_id', $categoryId);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get menu item by ID
     */
    public function getById($id) {
        $query = "SELECT m.*, c.name as category_name 
                  FROM " . $this->table_name . " m
                  LEFT JOIN category c ON m.category_id = c.id
                  WHERE m.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
