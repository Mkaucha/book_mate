<?php
// models/Rental.php
require_once 'config/database.php';

class Rental {
    private $conn;
    private $table_name = "rentals";

    public $rental_id;
    public $user_id;
    public $book_id;
    public $rental_date;
    public $due_date;
    public $return_date;
    public $status;
    public $fine_amount;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, book_id, due_date) 
                  VALUES (:user_id, :book_id, :due_date)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':book_id', $this->book_id);
        $stmt->bindParam(':due_date', $this->due_date);

        if ($stmt->execute()) {
            // Update book availability
            $update_query = "UPDATE books SET available_copies = available_copies - 1 WHERE book_id = :book_id";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':book_id', $this->book_id);
            return $update_stmt->execute();
        }

        return false;
    }

    public function getAllRentals() {
        $query = "SELECT r.rental_id, r.rental_date, r.due_date, r.return_date, r.status, r.fine_amount,
                         u.full_name, u.email,
                         b.title, b.author, b.isbn
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.user_id
                  JOIN books b ON r.book_id = b.book_id
                  ORDER BY r.rental_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getActiveRentals() {
        $query = "SELECT r.rental_id, r.rental_date, r.due_date, r.fine_amount,
                         u.full_name, u.email,
                         b.title, b.author, b.isbn
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.user_id
                  JOIN books b ON r.book_id = b.book_id
                  WHERE r.status = 'rented'
                  ORDER BY r.due_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getOverdueRentals() {
        $today = date('Y-m-d');
        $query = "SELECT r.rental_id, r.rental_date, r.due_date, r.fine_amount,
                         u.full_name, u.email, u.user_id,
                         b.title, b.author, b.isbn
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.user_id
                  JOIN books b ON r.book_id = b.book_id
                  WHERE r.status = 'rented' AND r.due_date < :today
                  ORDER BY r.due_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        return $stmt;
    }

    public function returnBook() {
        $this->return_date = date('Y-m-d');
        $this->status = 'returned';

        $query = "UPDATE " . $this->table_name . " 
                  SET return_date = :return_date, status = :status, fine_amount = :fine_amount 
                  WHERE rental_id = :rental_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':return_date', $this->return_date);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':fine_amount', $this->fine_amount);
        $stmt->bindParam(':rental_id', $this->rental_id);

        if ($stmt->execute()) {
            // Update book availability
            $update_query = "UPDATE books SET available_copies = available_copies + 1 
                           WHERE book_id = (SELECT book_id FROM " . $this->table_name . " WHERE rental_id = :rental_id)";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(':rental_id', $this->rental_id);
            return $update_stmt->execute();
        }

        return false;
    }

    public function getRentalById($id) {
        $query = "SELECT r.*, u.full_name, u.email, b.title, b.author
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.user_id
                  JOIN books b ON r.book_id = b.book_id
                  WHERE r.rental_id = :rental_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':rental_id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserActiveRentalsCount($user_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE user_id = :user_id AND status = 'rented'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    // Add this method below
    public function getUserRentals($user_id) {
        $query = "SELECT b.title, b.author, r.rental_date, r.due_date 
                  FROM " . $this->table_name . " r
                  JOIN books b ON b.book_id = r.book_id
                  WHERE r.user_id = :user_id AND r.status = 'rented'
                  ORDER BY r.due_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}
?>
