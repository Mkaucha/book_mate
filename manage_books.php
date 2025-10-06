<?php
// manage_books.php (Admin)
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/Book.php';

requireAdmin();

$book = new Book();
$all_books = $book->getAllBooks();

$success_message = $error_message = '';

// Handle book delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_book'])) {
    $book_id = intval($_POST['book_id']);
    if ($book->delete($book_id)) {
        header("Location: manage_books.php?deleted=success");
        exit();
    } else {
        $error_message = 'Failed to delete book.';
    }
}

if (isset($_GET['deleted']) && $_GET['deleted'] === 'success') {
    $success_message = 'Book deleted successfully.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - <?php echo SITE_NAME; ?></title>
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
            <a href="manage_books.php" class="nav-link active">Books</a>
            <a href="manage_rentals.php" class="nav-link">Rentals</a>
            <a href="manage_users.php" class="nav-link">Users</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
        <div class="nav-user">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
    </nav>
    <div class="container">
        <div class="page-header">
            <h1>Manage Books</h1>
            <a href="add_book.php" class="btn btn-primary">Add New Book</a>
        </div>
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ISBN</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Publisher</th>
                        <th>Category</th>
                        <th>Year</th>
                        <th>Total</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $all_books->fetch(PDO::FETCH_ASSOC)) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['isbn'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['title'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['author'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['publisher'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['category'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['publication_year'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['total_copies'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['available_copies'] ?? ''); ?></td>
                            <td>
                                <a href="edit_books.php?book_id=<?php echo $row['book_id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                                <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                    <input type="hidden" name="book_id" value="<?php echo $row['book_id']; ?>">
                                    <button type="submit" name="delete_book" class="btn btn-small btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
