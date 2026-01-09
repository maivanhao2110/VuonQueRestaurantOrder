<?php
/**
 * Staff Model
 */

class Staff {
    private $conn;
    private $table_name = "staff";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($includeInactive = true) {
        $query = "SELECT id, full_name, username, is_active, created_at, cccd, phone, email, address
                  FROM " . $this->table_name;

        if (!$includeInactive) {
            $query .= " WHERE is_active = 1";
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT id, full_name, username, is_active, created_at, cccd, phone, email, address
                  FROM " . $this->table_name . "
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($fullName, $username, $passwordHash, $cccd = null, $phone = null, $email = null, $address = null, $isActive = 1) {
        $query = "INSERT INTO " . $this->table_name . "
                  (full_name, username, password_hash, is_active, created_at, cccd, phone, email, address)
                  VALUES (:full_name, :username, :password_hash, :is_active, NOW(), :cccd, :phone, :email, :address)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':full_name', $fullName);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':is_active', $isActive);
        $stmt->bindParam(':cccd', $cccd);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    public function update($id, $fullName, $username, $passwordHashOrNull, $cccd = null, $phone = null, $email = null, $address = null, $isActive = 1) {
        $setPasswordSql = "";
        if ($passwordHashOrNull !== null) {
            $setPasswordSql = ", password_hash = :password_hash";
        }

        $query = "UPDATE " . $this->table_name . "
                  SET full_name = :full_name,
                      username = :username,
                      is_active = :is_active,
                      cccd = :cccd,
                      phone = :phone,
                      email = :email,
                      address = :address
                      $setPasswordSql
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':full_name', $fullName);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':is_active', $isActive);
        $stmt->bindParam(':cccd', $cccd);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':address', $address);

        if ($passwordHashOrNull !== null) {
            $stmt->bindParam(':password_hash', $passwordHashOrNull);
        }

        return $stmt->execute();
    }

    public function setActive($id, $isActive) {
        $query = "UPDATE " . $this->table_name . " SET is_active = :is_active WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':is_active', $isActive);
        return $stmt->execute();
    }
}

?>
