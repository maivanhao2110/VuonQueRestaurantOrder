<?php
require_once __DIR__ . '/BaseRepository.php';

class MenuItemRepository extends BaseRepository {
    protected $table_name = "menu_item";

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

    public function getAllAdmin($categoryId = null, $includeUnavailable = true) {
        $query = "SELECT m.*, c.name as category_name 
                  FROM " . $this->table_name . " m
                  LEFT JOIN category c ON m.category_id = c.id
                  WHERE 1=1";

        if (!$includeUnavailable) {
            $query .= " AND m.is_available = 1";
        }

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
    
    // Override getById to include category name
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

    public function create($categoryId, $name, $price, $imageUrl = null, $description = null, $isAvailable = 1) {
        $query = "INSERT INTO " . $this->table_name . "
                  (category_id, name, price, image_url, description, is_available, created_at)
                  VALUES (:category_id, :name, :price, :image_url, :description, :is_available, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image_url', $imageUrl);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':is_available', $isAvailable);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $categoryId, $name, $price, $imageUrl = null, $description = null, $isAvailable = 1) {
        $query = "UPDATE " . $this->table_name . "
                  SET category_id = :category_id,
                      name = :name,
                      price = :price,
                      image_url = :image_url,
                      description = :description,
                      is_available = :is_available
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image_url', $imageUrl);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':is_available', $isAvailable);

        return $stmt->execute();
    }

    public function setAvailable($id, $isAvailable) {
        $query = "UPDATE " . $this->table_name . " SET is_available = :is_available WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':is_available', $isAvailable);
        return $stmt->execute();
    }
}
?>
