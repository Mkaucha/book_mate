<?php
// edit_book.php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/Book.php';

requireAdmin();

$error_message = $success_message = '';

// Validate and fetch book
if (!isset($_GET['book_id']) || !is_numeric($_GET['book_id'])) {
    die('Invalid book ID.');
}
$book_id = intval($_GET['book_id']);

$book_model = new Book();
$book_data = $book_model->getBookById($book_id);
if (!$book_data) {
    die('Book not found.');
}

if ($_POST) {
    $updated = [
        'isbn' => sanitize($_POST['isbn'] ?? ''),
        'title' => sanitize($_POST['title'] ?? ''),
        'author' => sanitize($_POST['author'] ?? ''),
        'publisher' => sanitize($_POST['publisher'] ?? ''),
        'category' => sanitize($_POST['category'] ?? ''),
        'publication_year' => intval($_POST['publication_year'] ?? 0),
        'total_copies' => intval($_POST['total_copies'] ?? 1),
        'available_copies' => intval($_POST['available_copies'] ?? 1),
        'description' => sanitize($_POST['description'] ?? '')
    ];

    if ($book_model->update($book_id, $updated)) {
        $success_message = "Book updated successfully.";
        // Reload new data
        $book_data = $book_model->getBookById($book_id);
    } else {
        $error_message = "Failed to update book. ISBN might already exist.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - <?php echo SITE_NAME; ?></title>
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
            <a href="manage_books.php" class="nav-link active">Books</a>
            <a href="manage_rentals.php" class="nav-link">Rentals</a>
            <a href="manage_users.php" class="nav-link">Users</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
        <div class="nav-user">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
    </nav>
    <div class="container">
        <div class="page-header">
            <h1>Edit Book</h1>
            <a href="manage_books.php" class="btn btn-secondary">Back to Books</a>
        </div>
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="form">
            <div class="form-row">
                <div class="form-group">
                    <label for="isbn">ISBN:</label>
                    <input type="text" id="isbn" name="isbn" required
                           value="<?php echo htmlspecialchars($book_data['isbn']); ?>">
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <?php
                        $categories = ['Fiction','Non-Fiction','Science','Technology','History','Biography','Fantasy','Romance','Mystery','Educational'];
                        foreach ($categories as $cat):
                        ?>
                        <option value="<?php echo $cat; ?>" <?php if($book_data['category'] == $cat) echo 'selected'; ?>>
                            <?php echo $cat; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required 
                       value="<?php echo htmlspecialchars($book_data['title']); ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="author">Author:</label>
                    <input type="text" id="author" name="author" required
                           value="<?php echo htmlspecialchars($book_data['author']); ?>">
                </div>
                <div class="form-group">
                    <label for="publisher">Publisher:</label>
                    <input type="text" id="publisher" name="publisher"
                           value="<?php echo htmlspecialchars($book_data['publisher']); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="publication_year">Publication Year:</label>
                    <input type="number" id="publication_year" name="publication_year" min="1000" max="<?php echo date('Y'); ?>"
                           value="<?php echo htmlspecialchars($book_data['publication_year']); ?>">
                </div>
                <div class="form-group">
                    <label for="total_copies">Total Copies:</label>
                    <input type="number" id="total_copies" name="total_copies" min="1" max="100" required
                           value="<?php echo htmlspecialchars($book_data['total_copies']); ?>">
                </div>
                <div class="form-group">
                    <label for="available_copies">Available Copies:</label>
                    <input type="number" id="available_copies" name="available_copies" min="0" max="100"
                           value="<?php echo htmlspecialchars($book_data['available_copies']); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($book_data['description']); ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Book</button>
                <a href="manage_books.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
