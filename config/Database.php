<?php
class Database {
    private $host = "localhost";    // MAMP default host
    private $port = "8889";         // MAMP default MySQL port
    private $db_name = "blood_donor_db";
    private $username = "root";     // MAMP default username
    private $password = "root";     // MAMP default password
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Updated DSN to include port
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            // Enhanced error logging
            error_log("Database Connection Error: " . $e->getMessage());
            return null;
        }
    }
}