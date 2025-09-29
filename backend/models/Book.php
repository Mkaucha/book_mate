<?php
// models/Book.php
require_once 'config/database.php';

class Book {
    private $conn;
    private $table_name = "books";

    public $book_id;
    public $isbn;
    public $title;
    public $author;
    public $publisher;
    public $category;
    public $publication_year;
    public $total_copies;
    public $available_copies;
    public $description;
    public $cover_image;
    public $added_date;
    public $status;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (isbn, title, author, publisher, category, publication_year, total_copies, available_copies, description) 
                  VALUES (:isbn, :title, :author, :publisher, :category, :publication_year, :total_copies, :available_copies, :description)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':publisher', $this->publisher);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':publication_year', $this->publication_year);
        $stmt->bindParam(':total_copies', $this->total_copies);
        $stmt->bindParam(':available_copies', $this->available_copies);
        $stmt->bindParam(':description', $this->description);

        return $stmt->execute();
    }

    public function getAllBooks() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'active' ORDER BY added_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getAvailableBooks() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'active' AND available_copies > 0 ORDER BY title";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getBookById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE book_id = :book_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':book_id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBook() {
        $query = "UPDATE " . $this->table_name . " 
                  SET isbn = :isbn, title = :title, author = :author, publisher = :publisher, 
                      category = :category, publication_year = :publication_year, 
                      total_copies = :total_copies, available_copies = :available_copies, 
                      description = :description 
                  WHERE book_id = :book_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':author', $this->author);
        $stmt->bindParam(':publisher', $this->publisher);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':publication_year', $this->publication_year);
        $stmt->bindParam(':total_copies', $this->total_copies);
        $stmt->bindParam(':available_copies', $this->available_copies);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':book_id', $this->book_id);

        return $stmt->execute();
    }

    public function deleteBook() {
        $query = "UPDATE " . $this->table_name . " SET status = 'inactive' WHERE book_id = :book_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':book_id', $this->book_id);
        return $stmt->execute();
    }

    public function searchBooks($search_term) {
        $search_term = "%{$search_term}%";
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE status = 'active' AND (title LIKE :search OR author LIKE :search2 OR category LIKE :search3) 
                  ORDER BY title";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':search', $search_term);
        $stmt->bindParam(':search2', $search_term);
        $stmt->bindParam(':search3', $search_term);
        $stmt->execute();
        return $stmt;
    }

    public function getCategories() {
        $query = "SELECT DISTINCT category FROM " . $this->table_name . " WHERE status = 'active' ORDER BY category";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>