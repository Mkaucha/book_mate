-- BookMate Library Management System Database Schema
-- Run this SQL script in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS bookmate;
USE bookmate;

-- Users table (both admin and customers)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    user_type ENUM('admin', 'customer') DEFAULT 'customer',
    phone VARCHAR(20),
    address TEXT,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Books table
CREATE TABLE books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100) NOT NULL,
    publisher VARCHAR(100),
    category VARCHAR(50),
    publication_year INT,
    total_copies INT DEFAULT 1,
    available_copies INT DEFAULT 1,
    description TEXT,
    cover_image VARCHAR(255),
    added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Rentals table
CREATE TABLE rentals (
    rental_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    rental_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    status ENUM('rented', 'returned', 'overdue') DEFAULT 'rented',
    fine_amount DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

-- Notifications table
CREATE TABLE notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    rental_id INT,
    message TEXT NOT NULL,
    notification_type ENUM('due_reminder', 'overdue_notice', 'return_confirmation') NOT NULL,
    sent_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('sent', 'pending', 'failed') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (rental_id) REFERENCES rentals(rental_id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, user_type) 
VALUES ('admin', 'admin@bookmate.com', '$2y$10$VlbJEw3iM8tA7NqdynkFXupjnPK78dFWRITsgXyFNW0zJ5l5Sdd1i', 'System Administrator', 'admin');

-- Insert sample books with cover images
INSERT INTO books 
(isbn, title, author, publisher, category, publication_year, total_copies, available_copies, description, cover_image)
VALUES
('9780141439518', 'Pride and Prejudice', 'Jane Austen', 'Penguin Classics', 'Fiction', 1813, 3, 3, 'A romantic novel of manners written by Jane Austen in 1813.', 'pride.jpg'),
('9780061120084', 'To Kill a Mockingbird', 'Harper Lee', 'Harper Perennial Modern Classics', 'Fiction', 1960, 2, 2, 'A gripping tale of racial injustice and loss of innocence.', 'mockingbird.jpg'),
('9780544003415', 'The Lord of the Rings', 'J.R.R. Tolkien', 'Houghton Mifflin Harcourt', 'Fantasy', 1954, 4, 4, 'Epic fantasy adventure in Middle-earth.', 'lorings.jpg'),
('9780132350884', 'Clean Code', 'Robert C. Martin', 'Prentice Hall', 'Technology', 2008, 2, 2, 'A handbook of agile software craftsmanship.', 'cleancode.jpg'),
('9780596517748', 'JavaScript: The Good Parts', 'Douglas Crockford', 'O Reilly Media', 'Technology', 2008, 1, 1, 'Unearthing the excellence in JavaScript.', 'javascript.jpg');

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_books_isbn ON books(isbn);
CREATE INDEX idx_rentals_user_id ON rentals(user_id);
CREATE INDEX idx_rentals_book_id ON rentals(book_id);
CREATE INDEX idx_rentals_status ON rentals(status);
CREATE INDEX idx_notifications_user_id ON notifications(user_id);
