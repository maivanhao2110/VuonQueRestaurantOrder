<?php
/**
 * Category Model
 */

class Category {
    private $conn;
    private $table_name = "category";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all active categories
     */
    public function getAll() {
        $query = "SELECT id, name, description, is_active 
                  FROM " . $this->table_name . " 
                  WHERE is_active = 1 
                  ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
