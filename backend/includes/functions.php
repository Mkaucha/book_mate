<?php
// includes/functions.php

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function calculateFine($due_date, $return_date = null) {
    $return_date = $return_date ? $return_date : date('Y-m-d');
    $due = new DateTime($due_date);
    $returned = new DateTime($return_date);

    if ($returned <= $due) {
        return 0;
    }

    $interval = $due->diff($returned);
    $overdue_days = $interval->days;

    return $overdue_days * FINE_PER_DAY;
}

function generateBookCode($title, $author) {
    $code = strtoupper(substr($title, 0, 3) . substr($author, 0, 3));
    return preg_replace('/[^A-Z0-9]/', '', $code);
}

function sendEmail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . FROM_NAME . " <" . FROM_EMAIL . ">" . "\r\n";

    return mail($to, $subject, $message, $headers);
}

function getDaysUntilDue($due_date) {
    $due = new DateTime($due_date);
    $today = new DateTime();
    $interval = $today->diff($due);

    if ($due < $today) {
        return -$interval->days; // Overdue (negative days)
    }

    return $interval->days;
}

function getBookAvailability($book_id) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT available_copies FROM books WHERE book_id = :book_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();

    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    return $book ? $book['available_copies'] : 0;
    // //  if ($stmt->execute([':book_id' => $book_id])) {
    // //     $book = $stmt->fetch(PDO::FETCH_ASSOC);

    // //     if ($book && isset($book['available_copies'])) {
    // //         return $book['available_copies'];
    // //     }
    // // }

    // // Default return if query fails or no record found
    // return 0;
}

function updateBookAvailability($book_id, $change) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "UPDATE books SET available_copies = available_copies + :change 
              WHERE book_id = :book_id AND (available_copies + :change) >= 0";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':change', $change);
    $stmt->bindParam(':book_id', $book_id);

    return $stmt->execute();
}
?>