<?php
// customer_dashboard.php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/User.php';
require_once 'models/Book.php';

requireLogin();

$user = new User();
$book = new Book();

// Get user's rentals
$user_rentals = $user->getUserRentals($_SESSION['user_id']);
$available_books = $book->getAvailableBooks();

// Process book rental
if ($_POST && isset($_POST['rent_book'])) {
    require_once 'models/Rental.php';
    $rental = new Rental();
    $rental->user_id = $_SESSION['user_id'];
    $rental->book_id = intval($_POST['book_id']);
    $rental->due_date = date('Y-m-d', strtotime('+' . RENTAL_PERIOD_DAYS . ' days'));

    // Check if user has reached maximum books limit
    if ($rental->getUserActiveRentalsCount($_SESSION['user_id']) >= MAX_BOOKS_PER_USER) {
        $error_message = 'You have reached the maximum limit of ' . MAX_BOOKS_PER_USER . ' books.';
    } elseif ($rental->create()) {
        $success_message = 'Book rented successfully! Due date: ' . formatDate($rental->due_date);
        // Refresh the page to update available books
        header("Location: customer_dashboard.php?rented=success");
        exit();
    } else {
        $error_message = 'Unable to rent book. Please try again.';
    }
}

if (isset($_GET['rented']) && $_GET['rented'] == 'success') {
    $success_message = 'Book rented successfully!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <h2>📚 BookMate</h2>
        </div>
        <div class="nav-menu">
            <a href="customer_dashboard.php" class="nav-link active">My Dashboard</a>
            <a href="browse_books.php" class="nav-link">Browse Books</a>
            <a href="my_profile.php" class="nav-link">My Profile</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
        <div class="nav-user">
            Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>My Dashboard</h1>
            <p>Manage your book rentals</p>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="section">
                <div class="section-header">
                    <h2>My Current Rentals</h2>
                </div>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Rental Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $has_rentals = false;
                            while ($row = $user_rentals->fetch(PDO::FETCH_ASSOC)):
                                if ($row['status'] == 'rented'):
                                    $has_rentals = true;
                                    $days_until_due = getDaysUntilDue($row['due_date']);
                                    $status_class = 'status-active';
                                    if ($days_until_due < 0) {
                                        $status_class = 'status-overdue';
                                    } elseif ($days_until_due <= 2) {
                                        $status_class = 'status-warning';
                                    }
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
                            <?php endif; endwhile; ?>

                            <?php if (!$has_rentals): ?>
                            <tr>
                                <td colspan="5" class="text-center">No active rentals</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section">
                <div class="section-header">
                    <h2>Available Books</h2>
                    <a href="browse_books.php" class="btn btn-primary">View All</a>
                </div>
                <div class="books-grid">
                    <?php
                    $count = 0;
                    while ($book_row = $available_books->fetch(PDO::FETCH_ASSOC) && $count < 6):
                        $count++;
                    ?>
                    <div class="book-card">
                        <div class="book-image">📖</div>
                        <h3><?php echo htmlspecialchars($book_row['title']); ?></h3>
                        <p class="book-author">by <?php echo htmlspecialchars($book_row['author']); ?></p>
                        <p class="book-category"><?php echo htmlspecialchars($book_row['category']); ?></p>
                        <p class="book-availability"><?php echo $book_row['available_copies']; ?> available</p>
                        <form method="POST" action="">
                            <input type="hidden" name="book_id" value="<?php echo $book_row['book_id']; ?>">
                            <button type="submit" name="rent_book" class="btn btn-primary btn-small">Rent Book</button>
                        </form>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>