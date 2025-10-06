<?php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/Book.php';
require_once "config/database.php";
include 'includes/header.php';

// Handle book rental only if user is logged in
if (!empty($_SESSION['user_id']) && $_POST && isset($_POST['rent_book'])) {
    require_once 'models/Rental.php';
    $rental = new Rental();
    $rental->user_id = $_SESSION['user_id'];
    $rental->book_id = intval($_POST['book_id']);
    $rental->due_date = date('Y-m-d', strtotime('+' . RENTAL_PERIOD_DAYS . ' days'));

    if ($rental->getUserActiveRentalsCount($_SESSION['user_id']) >= MAX_BOOKS_PER_USER) {
        $error_message = 'You have reached the maximum limit of ' . MAX_BOOKS_PER_USER . ' books.';
    } elseif ($rental->create()) {
        header("Location: book_detail.php?id={$rental->book_id}&rented=success");
        exit();
    } else {
        $error_message = 'Unable to rent book. Please try again.';
    }
} elseif (isset($_POST['rent_book'])) {
    // User tried to rent without logging in
    $error_message = 'You must be logged in to rent a book.';
}

// Show success message if redirected
if (isset($_GET['rented']) && $_GET['rented'] == 'success') {
    $success_message = 'Book rented successfully!';
}

// Get book ID from URL
$bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($bookId === 0) {
    echo "<p class='text-danger text-center mt-5'>Invalid book ID.</p>";
    include 'includes/footer.php';
    exit;
}

$database = new Database();
$conn = $database->getConnection();

// Fetch book by ID
$query = "SELECT * FROM books WHERE book_id = :id LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $bookId, PDO::PARAM_INT);
$stmt->execute();
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    echo "<p class='text-danger text-center mt-5'>Book not found.</p>";
    include 'includes/footer.php';
    exit;
}
?>

<!-- Product Detail Section -->
<section class="py-5 detail" style="height: 100vh;">
    <div class="container">
        <div class="row g-4">
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Book Image -->
            <div class="col-md-3 text-center book-cover">
                <img src="img/<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="img-fluid rounded shadow" />
            </div>

            <!-- Book Info -->
            <div class="col-md-9 px-5">
                <h1 class="mb-3"><?= htmlspecialchars($book['title']) ?></h1>
                <h5 class="text-muted mb-4">by <?= htmlspecialchars($book['author']) ?></h5>
                <p><?= htmlspecialchars($book['description']) ?></p>

                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item"><strong>Publisher:</strong> <?= htmlspecialchars($book['publisher']) ?></li>
                    <li class="list-group-item"><strong>Year:</strong> <?= htmlspecialchars($book['publication_year']) ?></li>
                    <li class="list-group-item"><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn']) ?></li>
                    <li class="list-group-item"><strong>Category:</strong> <?= htmlspecialchars($book['category']) ?></li>
                    <li class="list-group-item"><strong>Availability:</strong>
                        <?= $book['available_copies'] > 0 ? "In Stock ({$book['available_copies']})" : "<span class='text-danger'>Out of Stock</span>" ?>
                </ul>

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap gap-3">
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                            <?php if ($book['available_copies'] > 0): ?>
                                <button type="submit" name="rent_book" class="btn btn-primary">Rent Book</button>
                            <?php else: ?>
                                <button class="btn btn-secondary px-4" disabled>Unavailable</button>
                            <?php endif; ?>
                        </form>
                    <?php else: ?>
                        <a href="customer_login.php" class="btn btn-primary">Login to Rent</a>
                    <?php endif; ?>

                    <a href="home.php" class="btn btn-link align-self-center px-0">← Back</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
