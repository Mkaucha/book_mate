<?php
// manage_rentals.php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/Rental.php';

requireAdmin();

$rental = new Rental();
$filter = $_GET['filter'] ?? 'all';

switch ($filter) {
    case 'active':
        $rentals = $rental->getActiveRentals();
        break;
    case 'overdue':
        $rentals = $rental->getOverdueRentals();
        break;
    default:
        $rentals = $rental->getAllRentals();
        break;
}

// Handle return book
if (isset($_POST['return_book'])) {
    $rental_id = intval($_POST['rental_id']);
    $rental_data = $rental->getRentalById($rental_id);

    if ($rental_data) {
        $fine = calculateFine($rental_data['due_date']);
        $rental->rental_id = $rental_id;
        $rental->fine_amount = $fine;

        if ($rental->returnBook()) {
            header("Location: manage_rentals.php?returned=success");
            exit();
        }
    }
}

if (isset($_GET['returned']) && $_GET['returned'] == 'success') {
    $success_message = 'Book returned successfully!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rentals - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <a href="admin_dashboard.php" style="text-decoration: none; color: inherit;">
                <h2><img src="img/icon.png" alt="Logo" width="30" class="me-2"> BookMate Admin</h2>
            </a>
        </div>
        <div class="nav-menu">
            <a href="admin_dashboard.php" class="nav-link">Dashboard</a>
            <a href="manage_books.php" class="nav-link">Books</a>
            <a href="manage_rentals.php" class="nav-link active">Rentals</a>
            <a href="manage_users.php" class="nav-link">Users</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
        <div class="nav-user">
            Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Manage Rentals</h1>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="filter-section" style="margin-bottom: 2rem;">
            <div class="quick-actions">
                <a href="manage_rentals.php" class="action-card <?php echo $filter == 'all' ? 'active' : ''; ?>">
                    <div class="action-icon">📚</div>
                    <div>
                        <h3>All Rentals</h3>
                        <p>View all rental records</p>
                    </div>
                </a>
                <a href="manage_rentals.php?filter=active" class="action-card <?php echo $filter == 'active' ? 'active' : ''; ?>">
                    <div class="action-icon">📖</div>
                    <div>
                        <h3>Active Rentals</h3>
                        <p>Currently rented books</p>
                    </div>
                </a>
                <a href="manage_rentals.php?filter=overdue" class="action-card action-alert <?php echo $filter == 'overdue' ? 'active' : ''; ?>">
                    <div class="action-icon">⚠️</div>
                    <div>
                        <h3>Overdue Books</h3>
                        <p>Books past due date</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Book</th>
                        <th>Author</th>
                        <th>Rental Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Fine</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $rentals->fetch(PDO::FETCH_ASSOC)): 
                        $days_until_due = getDaysUntilDue($row['due_date']);
                        $status_class = '';
                        $current_status = isset($row['status']) ? $row['status'] : 'unknown';

                        if ($current_status == 'rented' && $days_until_due < 0) {
                            $current_status = 'overdue';
                            $status_class = 'status-overdue';
                        } elseif ($current_status == 'rented' && $days_until_due <= 2) {
                            $status_class = 'status-warning';
                        } elseif ($current_status == 'rented') {
                            $status_class = 'status-active';
                        } else {
                            $status_class = 'status-active';
                        }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['full_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['title'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['author'] ?? ''); ?></td>
                        <td><?php echo formatDate($row['rental_date'] ?? ''); ?></td>
                        <td><?php echo formatDate($row['due_date'] ?? ''); ?></td>
                        <td><?php echo isset($row['return_date']) && $row['return_date'] ? formatDate($row['return_date']) : '-'; ?></td>
                        <td>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo ucfirst($current_status); ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            if ($current_status == 'returned' && isset($row['fine_amount']) && $row['fine_amount'] > 0) {
                                echo '$' . number_format($row['fine_amount'], 2);
                            } elseif ($current_status == 'rented' && $days_until_due < 0) {
                                $calculated_fine = calculateFine($row['due_date']);
                                echo '$' . number_format($calculated_fine, 2) . ' (pending)';
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td class="actions">
                            <?php if ($current_status == 'rented'): ?>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="rental_id" value="<?php echo $row['rental_id']; ?>">
                                    <button type="submit" name="return_book" class="btn btn-small btn-primary"
                                            onclick="return confirm('Mark this book as returned?')">
                                        Return Book
                                    </button>
                                </form>
                            <?php endif; ?>
                            <!-- If not rented, cell is blank -->
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
