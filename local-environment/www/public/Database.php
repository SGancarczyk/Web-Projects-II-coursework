<?php
class Database {
    private $host = "mysql";
    private $db_name = "project_db";
    private $user = "pw2user";
    private $pass = "pw2pass";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            error_log("DB Error: " . $e->getMessage());
        }
        return $this->conn;
    }
}