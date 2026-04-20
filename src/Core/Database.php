<?php

class Database {
    private $host = DB_HOST;
    private $port = DB_PORT;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $db_name = DB_NAME;

    // private $host = 'localhost';
    // private $port = '3307';
    // private $user = 'root';
    // private $pass = '';
    // private $db_name = 'auth';

    private $dbh;
    private $stmt;
    private $error;

    // Add this property to track transaction value
    private $inTransaction = false;

    public function __construct() {
        $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name .";charset=utf8mb4";
        // $dsn = "mysql:host=mysql; port=3307; dbname=auth; charset=utfmb4";

        $option = [
            // Jangan stringify blob, dan gunakan native prepares
            PDO::ATTR_STRINGIFY_FETCHES => false, // ⚠️ PENTING: Jangan stringify BLOB
            PDO::ATTR_EMULATE_PREPARES => false, // ⚠️ PENTING: Gunakan native prepares
            // Yg General
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // This is important for Transactions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
    
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $option);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() : PDO {
        return $this->dbh;
    }
}
