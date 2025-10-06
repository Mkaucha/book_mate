<?php
// send_notifications.php - Automated notification system
require_once 'config/settings.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// This script should be run as a cron job daily
// Example: 0 9 * * * /usr/bin/php /path/to/bookmate/send_notifications.php

$database = new Database();
$db = $database->getConnection();

$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$seven_days = date('Y-m-d', strtotime('+7 days'));

// Function to send notification email
function sendNotificationEmail($email, $full_name, $title, $due_date, $type) {
    $subject = '';
    $message = '';

    switch ($type) {
        case 'due_reminder':
            $subject = 'BookMate - Book Due Reminder';
            $message = "
            <html>
            <head><title>Book Due Reminder</title></head>
            <body>
                <h2>📚 BookMate Library System</h2>
                <p>Dear {$full_name},</p>
                <p>This is a friendly reminder that your borrowed book is due soon:</p>
                <div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #667eea; margin: 20px 0;'>
                    <strong>Book:</strong> {$title}<br>
                    <strong>Due Date:</strong> " . formatDate($due_date) . "
                </div>
                <p>Please return the book by the due date to avoid late fees.</p>
                <p>Thank you for using BookMate!</p>
                <hr>
                <small>This is an automated message from BookMate Library Management System.</small>
            </body>
            </html>";
            break;

        case 'overdue_notice':
            $subject = 'BookMate - Overdue Book Notice';
            $days_overdue = (new DateTime())->diff(new DateTime($due_date))->days;
            $fine = $days_overdue * FINE_PER_DAY;

            $message = "
            <html>
            <head><title>Overdue Book Notice</title></head>
            <body>
                <h2>⚠️ BookMate Library System - Overdue Notice</h2>
                <p>Dear {$full_name},</p>
                <p>Your borrowed book is now overdue:</p>
                <div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>
                    <strong>Book:</strong> {$title}<br>
                    <strong>Was Due:</strong> " . formatDate($due_date) . "<br>
                    <strong>Days Overdue:</strong> {$days_overdue}<br>
                    <strong>Current Fine:</strong> $" . number_format($fine, 2) . "
                </div>
                <p>Please return the book as soon as possible to avoid additional charges.</p>
                <p>Contact the library if you need assistance.</p>
                <hr>
                <small>This is an automated message from BookMate Library Management System.</small>
            </body>
            </html>";
            break;
    }

    return sendEmail($email, $subject, $message);
}

// Function to log notification
function logNotification($db, $user_id, $rental_id, $message, $type, $status) {
    $query = "INSERT INTO notifications (user_id, rental_id, message, notification_type, status) 
              VALUES (:user_id, :rental_id, :message, :type, :status)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':rental_id', $rental_id);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':status', $status);
    return $stmt->execute();
}

echo "Starting notification process...\n";

// 1. Send 7-day advance reminders
$query_7day = "
    SELECT r.rental_id, r.user_id, r.due_date, u.full_name, u.email, b.title
    FROM rentals r
    JOIN users u ON r.user_id = u.user_id
    JOIN books b ON r.book_id = b.book_id
    WHERE r.status = 'rented' 
    AND r.due_date = :seven_days
    AND NOT EXISTS (
        SELECT 1 FROM notifications n 
        WHERE n.rental_id = r.rental_id 
        AND n.notification_type = 'due_reminder' 
        AND DATE(n.sent_date) = CURDATE()
    )";

$stmt_7day = $db->prepare($query_7day);
$stmt_7day->bindParam(':seven_days', $seven_days);
$stmt_7day->execute();

$count_7day = 0;
while ($row = $stmt_7day->fetch(PDO::FETCH_ASSOC)) {
    $message = "Book '{$row['title']}' is due in 7 days";
    $status = sendNotificationEmail($row['email'], $row['full_name'], $row['title'], $row['due_date'], 'due_reminder') ? 'sent' : 'failed';
    logNotification($db, $row['user_id'], $row['rental_id'], $message, 'due_reminder', $status);
    $count_7day++;
}

echo "Sent {$count_7day} 7-day reminder(s).\n";

// 2. Send 1-day reminders
$query_1day = "
    SELECT r.rental_id, r.user_id, r.due_date, u.full_name, u.email, b.title
    FROM rentals r
    JOIN users u ON r.user_id = u.user_id
    JOIN books b ON r.book_id = b.book_id
    WHERE r.status = 'rented' 
    AND r.due_date = :tomorrow
    AND NOT EXISTS (
        SELECT 1 FROM notifications n 
        WHERE n.rental_id = r.rental_id 
        AND n.notification_type = 'due_reminder' 
        AND DATE(n.sent_date) = CURDATE()
    )";

$stmt_1day = $db->prepare($query_1day);
$stmt_1day->bindParam(':tomorrow', $tomorrow);
$stmt_1day->execute();

$count_1day = 0;
while ($row = $stmt_1day->fetch(PDO::FETCH_ASSOC)) {
    $message = "Book '{$row['title']}' is due tomorrow";
    $status = sendNotificationEmail($row['email'], $row['full_name'], $row['title'], $row['due_date'], 'due_reminder') ? 'sent' : 'failed';
    logNotification($db, $row['user_id'], $row['rental_id'], $message, 'due_reminder', $status);
    $count_1day++;
}

echo "Sent {$count_1day} 1-day reminder(s).\n";

// 3. Send overdue notices
$query_overdue = "
    SELECT r.rental_id, r.user_id, r.due_date, u.full_name, u.email, b.title
    FROM rentals r
    JOIN users u ON r.user_id = u.user_id
    JOIN books b ON r.book_id = b.book_id
    WHERE r.status = 'rented' 
    AND r.due_date < :today
    AND NOT EXISTS (
        SELECT 1 FROM notifications n 
        WHERE n.rental_id = r.rental_id 
        AND n.notification_type = 'overdue_notice' 
        AND DATE(n.sent_date) = CURDATE()
    )";

$stmt_overdue = $db->prepare($query_overdue);
$stmt_overdue->bindParam(':today', $today);
$stmt_overdue->execute();

$count_overdue = 0;
while ($row = $stmt_overdue->fetch(PDO::FETCH_ASSOC)) {
    $message = "Book '{$row['title']}' is overdue";
    $status = sendNotificationEmail($row['email'], $row['full_name'], $row['title'], $row['due_date'], 'overdue_notice') ? 'sent' : 'failed';
    logNotification($db, $row['user_id'], $row['rental_id'], $message, 'overdue_notice', $status);
    $count_overdue++;
}

echo "Sent {$count_overdue} overdue notice(s).\n";

echo "Notification process completed.\n";
echo "Total notifications sent: " . ($count_7day + $count_1day + $count_overdue) . "\n";
?>
