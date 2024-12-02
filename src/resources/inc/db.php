<?php

/*
class Database {
    private $host = "localhost";
    private $db_name = "motell_booking";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            die("Database connection failed. Please try again later.");
        }

        return $this->conn;
    }
}




class Database {
    private $host = "localhost";
    private $db_name = "motell_booking";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $exception) {
                error_log("Connection error: " . $exception->getMessage());
                die("Database connection failed. Please try again later.");
            }
        }
        return $this->conn;
    }
}

*/
class Database {
    private $host = "localhost";
    private $db_name = "motell_booking";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $exception) {
                error_log("Database connection error: " . $exception->getMessage());
                die("Database connection failed: " . $exception->getMessage());
            }
        }
        return $this->conn;
    }
}




