<?php
// config/database.php

class Database {
    private $host = "localhost";
    private $db_name = "spk_moora_trip";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("set names utf8mb4");
        } catch(PDOException $exception) {
            // If the database doesn't exist, try connecting without DB name to run migrations
            try {
                $this->conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->exec("set names utf8mb4");
            } catch(PDOException $e2) {
                die("Koneksi Database Gagal: " . $e2->getMessage());
            }
        }

        return $this->conn;
    }
}
?>
