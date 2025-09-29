<?php
// models/User.php
require_once 'config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public $user_id;
    public $username;
    public $email;
    public $password;
    public $full_name;
    public $user_type;
    public $phone;
    public $address;
    public $created_date;
    public $status;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllCustomers() {
        $query = "SELECT user_id, username, email, full_name, phone, created_date, status 
                  FROM " . $this->table_name . " WHERE user_type = 'customer' ORDER BY created_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser() {
        $query = "UPDATE " . $this->table_name . " 
                  SET full_name = :full_name, email = :email, phone = :phone, address = :address, status = :status 
                  WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    public function deleteUser() {
        $query = "UPDATE " . $this->table_name . " SET status = 'inactive' WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        return $stmt->execute();
    }

    public function getUserRentals($user_id) {
        $query = "SELECT r.rental_id, r.rental_date, r.due_date, r.return_date, r.status, r.fine_amount,
                         b.title, b.author, b.isbn
                  FROM rentals r
                  JOIN books b ON r.book_id = b.book_id
                  WHERE r.user_id = :user_id
                  ORDER BY r.rental_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt;
    }
}
?>