<?php
// admin_dashboard.php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/Book.php';
require_once 'models/User.php';
require_once 'models/Rental.php';

requireAdmin();

// Get statistics
$book = new Book();
$user = new User();
$rental = new Rental();

$total_books_result = $book->getAllBooks();
$total_books = $total_books_result->rowCount();

$total_customers_result = $user->getAllCustomers();
$total_customers = $total_customers_result->rowCount();

$active_rentals_result = $rental->getActiveRentals();
$active_rentals = $active_rentals_result->rowCount();

$overdue_rentals_result = $rental->getOverdueRentals();
$overdue_rentals = $overdue_rentals_result->rowCount();

// Helper function to safely escape values
function safe_html($val) {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}

// Safely format dates
function safe_formatDate($date) {
    if (empty($date)) return '';
    try {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    } catch (Exception $e) {
        return '';
    }
}

// Safely get days until due
function safe_getDaysUntilDue($due_date) {
    if (empty($due_date)) return 0;
    $now = new DateTime();
    try {
        $due = new DateTime($due_date);
        $interval = $now->diff($due);
        $days = (int)$interval->format('%r%a');
        return $days;
    } catch (Exception $e) {
        return 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo safe_html(SITE_NAME); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <a href="admin_dashboard.php" style="text-decoration: none; color: inherit;">
                <h2> <img src="img/icon.png" alt="Logo" width="30" class="me-2"> BookMate Admin</h2>
            </a>
        </div>
        <div class="nav-menu">
            <a href="admin_dashboard.php" class="nav-link active">Dashboard</a>
            <a href="manage_books.php" class="nav-link">Books</a>
            <a href="manage_rentals.php" class="nav-link">Rentals</a>
            <a href="manage_users.php" class="nav-link">Users</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
        <div class="nav-user">
            Welcome, <?php echo safe_html($_SESSION['full_name'] ?? ''); ?>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Admin Dashboard</h1>
            <p>Manage your library system</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-content">
                    <h3><?php echo $total_books; ?></h3>
                    <p>Total Books</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <h3><?php echo $total_customers; ?></h3>
                    <p>Total Customers</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📖</div>
                <div class="stat-content">
                    <h3><?php echo $active_rentals; ?></h3>
                    <p>Active Rentals</p>
                </div>
            </div>
            <div class="stat-card stat-alert">
                <div class="stat-icon">⚠️</div>
                <div class="stat-content">
                    <h3><?php echo $overdue_rentals; ?></h3>
                    <p>Overdue Books</p>
                </div>
            </div>
        </div>

        <div class="dashboard-sections">
            <div class="section">
                <div class="section-header">
                    <h2>Recent Rentals</h2>
                    <a href="manage_rentals.php" class="btn btn-primary">View All</a>
                </div>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Book</th>
                                <th>Rental Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_rentals = $rental->getAllRentals();
                            $count = 0;
                            while (($row = $recent_rentals->fetch(PDO::FETCH_ASSOC)) && $count < 5):
                                if (!is_array($row)) continue;
                                $days_until_due = safe_getDaysUntilDue($row['due_date'] ?? null);
                                $status_class = '';
                                if (isset($row['status']) && ($row['status'] == 'overdue' || $days_until_due < 0)) {
                                    $status_class = 'status-overdue';
                                } elseif ($days_until_due <= 2) {
                                    $status_class = 'status-warning';
                                } else {
                                    $status_class = 'status-active';
                                }
                                $count++;
                            ?>
                            <tr>
                                <td><?php echo safe_html($row['full_name'] ?? ''); ?></td>
                                <td><?php echo safe_html($row['title'] ?? ''); ?></td>
                                <td><?php echo safe_formatDate($row['rental_date'] ?? ''); ?></td>
                                <td><?php echo safe_formatDate($row['due_date'] ?? ''); ?></td>
                                <td><span class="status-badge <?php echo $status_class; ?>"><?php echo isset($row['status']) ? ucfirst($row['status']) : ''; ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section">
                <div class="section-header">
                    <h2>Quick Actions</h2>
                </div>
                <div class="quick-actions">
                    <a href="add_book.php" class="action-card">
                        <div class="action-icon">➕</div>
                        <h3>Add New Book</h3>
                        <p>Add books to library catalog</p>
                    </a>
                    <a href="manage_rentals.php?filter=overdue" class="action-card action-alert">
                        <div class="action-icon">⚠️</div>
                        <h3>View Overdue</h3>
                        <p>Check overdue rentals</p>
                    </a>
                    <a href="send_notifications_manual.php" class="action-card">
                        <div class="action-icon">📧</div>
                        <h3>Send Reminders</h3>
                        <p>Send due date notifications</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
