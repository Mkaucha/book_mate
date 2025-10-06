<?php
// config/database.php
class Database {
    
    // // For Mac (MAMP)
    // private $host = "localhost";
    // private $port = "8889"; // MAMP default MySQL port on Mac
    // private $db_name = "bookmate"; // Your database name
    // private $username = "root";
    // private $password = "root"; // MAMP default password
    // private $conn;
    
    
    // For Windows (WAMP)
    private $host = "localhost";
    private $port = "3306";
    private $db_name = "bookmate";
    private $username = "root";  // Change as needed
    private $password = "";      // Change as needed
    private $conn;
   
    public function getConnection() {
        $this->conn = null;
        
        try {
            // Use port in DSN for MAMP
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
            die();
        }
        
        return $this->conn;
    }
}
?>