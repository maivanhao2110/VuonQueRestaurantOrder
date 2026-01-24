<?php
/**
 * Database Connection
 * Singleton pattern for PDO connection
 */

class Database {
    private static $instance = null;
    private $host = "localhost";
    private $db_name = "db_vuonquerestaurant";
    private $username = "root";
    private $password = "";
    private $port = "3306";
    public $conn;

    // Private constructor to prevent direct instantiation (optional, but good for Singleton)
    // Private constructor to prevent direct instantiation
    private function __construct() {}

    /**
     * Get Singleton instance
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Get database connection
     */
    public function getConnection() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . 
                    ";port=" . $this->port .
                    ";dbname=" . $this->db_name,
                    $this->username,
                    $this->password
                );
                $this->conn->exec("set names utf8mb4");
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $exception) {
                echo "Connection error: " . $exception->getMessage();
            }
        }
        return $this->conn;
    }
}
?>
