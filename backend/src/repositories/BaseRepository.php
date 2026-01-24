<?php
/**
 * Base Repository Interface
 */
interface BaseRepositoryInterface {
    public function getAll();
    public function getById($id);
    // create, update, delete signatures might vary too much, so we keep them specific or use flexible args.
    // simpler to keep common read methods here.
}

abstract class BaseRepository implements BaseRepositoryInterface {
    protected $conn;
    protected $table_name;

    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
