<?php
// send_notifications_manual.php - Web interface for sending notifications
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/Rental.php';

requireAdmin();

$success_message = '';
$error_message = '';

if ($_POST && isset($_POST['send_notifications'])) {
    // Include the notification script
    ob_start();
    include 'cron/send_notifications.php';
    $output = ob_get_clean();

    $success_message = "Notifications sent successfully! Output: " . nl2br(htmlspecialchars($output));
}

$rental = new Rental();
$overdue_rentals = $rental->getOverdueRentals();
$overdue_count = 0;
while ($overdue_rentals->fetch(PDO::FETCH_ASSOC)) {
    $overdue_count++;
}
$overdue_rentals = $rental->getOverdueRentals(); // Reset for display

// Get upcoming due dates
$database = new Database();
$db = $database->getConnection();
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$seven_days = date('Y-m-d', strtotime('+7 days'));

$query_upcoming = "SELECT COUNT(*) as count FROM rentals WHERE status = 'rented' AND due_date BETWEEN :tomorrow AND :seven_days";
$stmt_upcoming = $db->prepare($query_upcoming);
$stmt_upcoming->bindParam(':tomorrow', $tomorrow);
$stmt_upcoming->bindParam(':seven_days', $seven_days);
$stmt_upcoming->execute();
$upcoming_count = $stmt_upcoming->fetch(PDO::FETCH_ASSOC)['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notifications - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
         <div class="nav-brand">
            <a href="admin_dashboard.php" style="text-decoration: none; color: inherit;">
                <h2>📚 BookMate Admin</h2>
            </a>
        </div>
        <div class="nav-menu">
            <a href="admin_dashboard.php" class="nav-link">Dashboard</a>
            <a href="manage_books.php" class="nav-link">Books</a>
            <a href="manage_rentals.php" class="nav-link">Rentals</a>
            <a href="manage_users.php" class="nav-link">Users</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
        <div class="nav-user">
            Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Send Notifications</h1>
            <p>Send due date reminders and overdue notices</p>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <h3><?php echo $upcoming_count; ?></h3>
                    <p>Due Soon (1-7 days)</p>
                </div>
            </div>
            <div class="stat-card stat-alert">
                <div class="stat-icon">⚠️</div>
                <div class="stat-content">
                    <h3><?php echo $overdue_count; ?></h3>
                    <p>Overdue Books</p>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h2>Manual Notification Sender</h2>
            </div>

            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                <h3>Automated Notifications</h3>
                <p>The system automatically sends:</p>
                <ul style="margin: 1rem 0; padding-left: 2rem;">
                    <li><strong>7-day reminders:</strong> Books due in 7 days</li>
                    <li><strong>1-day reminders:</strong> Books due tomorrow</li>
                    <li><strong>Overdue notices:</strong> Books past their due date</li>
                </ul>
                <p><strong>Note:</strong> For production use, set up a cron job to run send_notifications.php daily.</p>
            </div>

            <form method="POST" action="">
                <div style="text-align: center; padding: 2rem;">
                    <button type="submit" name="send_notifications" class="btn btn-primary" 
                            onclick="return confirm('Send notifications now? This will email all customers with due/overdue books.')">
                        📧 Send Notifications Now
                    </button>
                </div>
            </form>
        </div>

        <?php if ($overdue_count > 0): ?>
        <div class="section">
            <div class="section-header">
                <h2>Overdue Books Requiring Attention</h2>
            </div>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Book</th>
                            <th>Due Date</th>
                            <th>Days Overdue</th>
                            <th>Fine</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $overdue_rentals->fetch(PDO::FETCH_ASSOC)): 
                            $days_overdue = (new DateTime())->diff(new DateTime($row['due_date']))->days;
                            $fine = $days_overdue * FINE_PER_DAY;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo formatDate($row['due_date']); ?></td>
                            <td class="text-center"><?php echo $days_overdue; ?></td>
                            <td>$<?php echo number_format($fine, 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>