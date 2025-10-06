<?php
// browse_books.php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/Book.php';

requireLogin();

$book = new Book();
$search_term = '';
$category_filter = '';

// Handle search and filtering
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = sanitize($_GET['search']);
    $books = $book->searchBooks($search_term);
} elseif (isset($_GET['category']) && !empty($_GET['category'])) {
    $category_filter = sanitize($_GET['category']);
    $books = $book->getAllBooks(); // You might want to implement category filtering
} else {
    $books = $book->getAvailableBooks();
}

$categories = $book->getCategories();

// Handle book rental
if ($_POST && isset($_POST['rent_book'])) {
    require_once 'models/Rental.php';
    $rental = new Rental();
    $rental->user_id = $_SESSION['user_id'];
    $rental->book_id = intval($_POST['book_id']);
    $rental->due_date = date('Y-m-d', strtotime('+' . RENTAL_PERIOD_DAYS . ' days'));

    if ($rental->getUserActiveRentalsCount($_SESSION['user_id']) >= MAX_BOOKS_PER_USER) {
        $error_message = 'You have reached the maximum limit of ' . MAX_BOOKS_PER_USER . ' books.';
    } elseif ($rental->create()) {
        header("Location: browse_books.php?rented=success");
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
    <title>Browse Books - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <a href="customer_dashboard.php" style="text-decoration: none; color: inherit;">
                <h2>📚 BookMate </h2>
            </a>
        </div>
        <div class="nav-menu">
            <a href="customer_dashboard.php" class="nav-link">My Dashboard</a>
            <a href="browse_books.php" class="nav-link active">Browse Books</a>
            <a href="my_profile.php" class="nav-link">My Profile</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
        <div class="nav-user">
            Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Browse Books</h1>
            <p>Find and rent your next favorite book</p>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="search-section">
            <form method="GET" action="" class="search-form">
                <div class="search-row">
                    <div class="form-group">
                        <input type="text" name="search" placeholder="Search by title, author, or category..." 
                               value="<?php echo htmlspecialchars($search_term); ?>" class="search-input">
                    </div>
                    <div class="form-group">
                        <select name="category" class="category-select">
                            <option value="">All Categories</option>
                            <?php while ($cat = $categories->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo htmlspecialchars($cat['category']); ?>" 
                                    <?php echo ($category_filter == $cat['category']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>

        <div class="books-section">
            <?php if ($search_term): ?>
                <h2>Search Results for "<?php echo htmlspecialchars($search_term); ?>"</h2>
            <?php elseif ($category_filter): ?>
                <h2>Books in "<?php echo htmlspecialchars($category_filter); ?>"</h2>
            <?php else: ?>
                <h2>Available Books</h2>
            <?php endif; ?>

            <div class="books-grid">
                <?php
                $book_count = 0;
                while ($book_row = $books->fetch(PDO::FETCH_ASSOC)):
                    if ($book_row['available_copies'] > 0):
                        $book_count++;
                ?>
                <div class="book-card">
                    <div class="book-image">📖</div>
                    <div class="book-details">
                        <h3><?php echo htmlspecialchars($book_row['title']); ?></h3>
                        <p class="book-author">by <?php echo htmlspecialchars($book_row['author']); ?></p>
                        <p class="book-category"><?php echo htmlspecialchars($book_row['category']); ?></p>
                        <p class="book-isbn">ISBN: <?php echo htmlspecialchars($book_row['isbn']); ?></p>
                        <?php if ($book_row['description']): ?>
                        <p class="book-description"><?php echo htmlspecialchars(substr($book_row['description'], 0, 100)) . (strlen($book_row['description']) > 100 ? '...' : ''); ?></p>
                        <?php endif; ?>
                        <p class="book-availability">
                            <span class="availability-count"><?php echo $book_row['available_copies']; ?> available</span>
                        </p>
                    </div>
                    <div class="book-actions">
                        <form method="POST" action="">
                            <input type="hidden" name="book_id" value="<?php echo $book_row['book_id']; ?>">
                            <button type="submit" name="rent_book" class="btn btn-primary btn-full">Rent Book</button>
                        </form>
                    </div>
                </div>
                <?php 
                    endif;
                endwhile; 

                if ($book_count == 0):
                ?>
                <div class="no-books">
                    <h3>No books found</h3>
                    <p>Try adjusting your search criteria or browse all available books.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>