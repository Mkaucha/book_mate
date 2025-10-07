<?php
// customer_dashboard.php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/User.php';
require_once 'models/Book.php';
include 'includes/header.php';


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
<section style='background: #f5f5f5; height:100vh;' class="py-5">

    <div class="container">
    <div class="text-center mb-5">
        <h1 class="fw-bold">My Dashboard</h1>
        <p class="text-muted">Manage your book rentals</p>
    </div>

    <div class="row">
        <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-book"></i> My Current Rentals</h4>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-striped table-hover">
                <thead class="table-primary">
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
                        if (is_array($row) && isset($row['status']) && $row['status'] == 'rented'):
                            $has_rentals = true;
                            $title = isset($row['title']) ? htmlspecialchars($row['title']) : 'Unknown';
                            $author = isset($row['author']) ? htmlspecialchars($row['author']) : 'Unknown';
                            $rental_date = isset($row['rental_date']) ? formatDate($row['rental_date']) : '';
                            $due_date = isset($row['due_date']) ? formatDate($row['due_date']) : '';
                            $days_until_due = isset($row['due_date']) ? getDaysUntilDue($row['due_date']) : null;

                            // Status class
                            $status_class = 'bg-success';
                            if ($days_until_due !== null) {
                                if ($days_until_due < 0) {
                                    $status_class = 'bg-danger';
                                } elseif ($days_until_due <= 2) {
                                    $status_class = 'bg-warning text-dark';
                                }
                            }
                    ?>
                    <tr>
                    <td><?= $title ?></td>
                    <td><?= $author ?></td>
                    <td><?= $rental_date ?></td>
                    <td><?= $due_date ?></td>
                    <td>
                        <span class="badge <?= $status_class ?> px-3 py-2">
                        <?php 
                            if ($days_until_due === null) {
                                echo 'Unknown';
                            } elseif ($days_until_due < 0) {
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
                    <td colspan="5" class="text-center text-muted py-4">
                        <em>No active rentals found</em>
                    </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                </table>
            </div>
            </div>
        </div>
        </div>
    </div>
    </div>
</section>
            <!-- <div class="section">
                <div class="section-header">
                    <h2>Available Books</h2>
                    <a href="browse_books.php" class="btn btn-primary">View All</a>
                </div>
                <div class="books-grid">
                    <?php
                    $count = 0;
                    while (($book_row = $available_books->fetch(PDO::FETCH_ASSOC)) && $count < 6):
                        if (!is_array($book_row)) continue;
                        $count++;
                        $book_title = isset($book_row['title']) ? htmlspecialchars($book_row['title']) : '';
                        $book_author = isset($book_row['author']) ? htmlspecialchars($book_row['author']) : '';
                        $book_category = isset($book_row['category']) ? htmlspecialchars($book_row['category']) : '';
                        $book_availability = isset($book_row['available_copies']) ? $book_row['available_copies'] : '0';
                        $book_id = isset($book_row['book_id']) ? $book_row['book_id'] : '';
                    ?>
                    <div class="book-card">
                        <div class="book-image">📖</div>
                        <h3><?php echo $book_title; ?></h3>
                        <p class="book-author">by <?php echo $book_author; ?></p>
                        <p class="book-category"><?php echo $book_category; ?></p>
                        <p class="book-availability"><?php echo $book_availability; ?> available</p>
                        <form method="POST" action="">
                            <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                            <button type="submit" name="rent_book" class="btn btn-primary btn-small">Rent Book</button>
                        </form>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div> -->
<?php include 'includes/footer.php'; ?>
