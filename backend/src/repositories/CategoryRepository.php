<?php
require_once __DIR__ . '/BaseRepository.php';

class CategoryRepository extends BaseRepository {
    protected $table_name = "category";

    public function getAllActive() {
        $query = "SELECT id, name, description, is_active 
                  FROM " . $this->table_name . " 
                  WHERE is_active = 1 
                  ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAdmin($includeInactive = true) {
        $query = "SELECT id, name, description, is_active 
                  FROM " . $this->table_name;

        if (!$includeInactive) {
            $query .= " WHERE is_active = 1";
        }

        $query .= " ORDER BY name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name, $description = null, $isActive = 1) {
        $query = "INSERT INTO " . $this->table_name . " (name, description, is_active)
                  VALUES (:name, :description, :is_active)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':is_active', $isActive);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $name, $description = null, $isActive = 1) {
        $query = "UPDATE " . $this->table_name . "
                  SET name = :name,
                      description = :description,
                      is_active = :is_active
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':is_active', $isActive);

        return $stmt->execute();
    }

    public function setActive($id, $isActive) {
        $query = "UPDATE " . $this->table_name . " SET is_active = :is_active WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindValue(':is_active', $isActive, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
