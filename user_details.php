<?php
// user_details.php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/User.php';
require_once 'models/Rental.php';

requireAdmin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid user ID.');
}
$user_id = intval($_GET['id']);

$user_model = new User();
$user = $user_model->getUserById($user_id);

if (!$user) {
    die('User not found.');
}

$rental_model = new Rental();
$current_rentals_stmt = $rental_model->getUserRentals($user_id); // <-- Fixed: assuming this method is now defined in Rental.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details - <?php echo SITE_NAME; ?></title>
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
            <a href="manage_users.php" class="nav-link active">Users</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
        <div class="nav-user">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>User Details</h1>
            <a href="manage_users.php" class="btn btn-secondary">Back to Users</a>
        </div>
        <div class="user-info-card">
            <h2><?php echo htmlspecialchars($user['full_name']); ?> <span class="status-badge <?php echo $user['status'] == 'active' ? 'status-active' : 'status-overdue'; ?>"><?php echo ucfirst($user['status']); ?></span></h2>
            <ul>
                <li><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></li>
                <li><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></li>
                <li><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></li>
                <li><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></li>
                <li><strong>Member Since:</strong> <?php echo formatDate($user['created_date']); ?></li>
            </ul>
        </div>
        <div class="section">
            <div class="section-header"><h2>Current Rentals</h2></div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Rented On</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $has_rentals = false;
                        while ($row = $current_rentals_stmt->fetch(PDO::FETCH_ASSOC)) :
                            $has_rentals = true;
                            $days_until_due = getDaysUntilDue($row['due_date']);
                            $status_class = 'status-active';
                            if ($days_until_due < 0) $status_class = 'status-overdue';
                            elseif ($days_until_due <= 2) $status_class = 'status-warning';
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td><?php echo formatDate($row['rental_date']); ?></td>
                            <td><?php echo formatDate($row['due_date']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php
                                    if ($days_until_due < 0) {
                                        echo 'Overdue (' . abs($days_until_due) . ' days)';
                                    } elseif ($days_until_due == 0) {
                                        echo 'Due Today';
                                    } else {
                                        echo 'Due in ' . $days_until_due . ' days';
                                    }
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if (!$has_rentals): ?>
                        <tr>
                            <td colspan="5" class="text-center">No current rentals</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
