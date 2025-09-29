<?php
// add_book.php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/Book.php';

requireAdmin();

$error_message = '';
$success_message = '';

if ($_POST) {
    $book = new Book();
    $book->isbn = sanitize($_POST['isbn'] ?? '');
    $book->title = sanitize($_POST['title'] ?? '');
    $book->author = sanitize($_POST['author'] ?? '');
    $book->publisher = sanitize($_POST['publisher'] ?? '');
    $book->category = sanitize($_POST['category'] ?? '');
    $book->publication_year = intval($_POST['publication_year'] ?? 0);
    $book->total_copies = intval($_POST['total_copies'] ?? 1);
    $book->available_copies = $book->total_copies;
    $book->description = sanitize($_POST['description'] ?? '');

    if ($book->create()) {
        header("Location: manage_books.php?added=success");
        exit();
    } else {
        $error_message = 'Failed to add book. ISBN might already exist.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <h2>📚 BookMate Admin</h2>
        </div>
        <div class="nav-menu">
            <a href="admin_dashboard.php" class="nav-link">Dashboard</a>
            <a href="manage_books.php" class="nav-link active">Books</a>
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
            <h1>Add New Book</h1>
            <a href="manage_books.php" class="btn btn-secondary">Back to Books</a>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="form">
            <div class="form-row">
                <div class="form-group">
                    <label for="isbn">ISBN:</label>
                    <input type="text" id="isbn" name="isbn" required 
                           value="<?php echo isset($_POST['isbn']) ? htmlspecialchars($_POST['isbn']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Fiction">Fiction</option>
                        <option value="Non-Fiction">Non-Fiction</option>
                        <option value="Science">Science</option>
                        <option value="Technology">Technology</option>
                        <option value="History">History</option>
                        <option value="Biography">Biography</option>
                        <option value="Fantasy">Fantasy</option>
                        <option value="Romance">Romance</option>
                        <option value="Mystery">Mystery</option>
                        <option value="Educational">Educational</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required 
                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="author">Author:</label>
                    <input type="text" id="author" name="author" required 
                           value="<?php echo isset($_POST['author']) ? htmlspecialchars($_POST['author']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="publisher">Publisher:</label>
                    <input type="text" id="publisher" name="publisher" 
                           value="<?php echo isset($_POST['publisher']) ? htmlspecialchars($_POST['publisher']) : ''; ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="publication_year">Publication Year:</label>
                    <input type="number" id="publication_year" name="publication_year" min="1000" max="<?php echo date('Y'); ?>"
                           value="<?php echo isset($_POST['publication_year']) ? $_POST['publication_year'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="total_copies">Total Copies:</label>
                    <input type="number" id="total_copies" name="total_copies" min="1" max="100" required
                           value="<?php echo isset($_POST['total_copies']) ? $_POST['total_copies'] : '1'; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Book</button>
                <a href="manage_books.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>