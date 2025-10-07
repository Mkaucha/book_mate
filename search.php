<?php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/Book.php';
require_once "config/database.php";
include 'includes/header.php';

$database = new Database();
$conn = $database->getConnection();

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
        header("Location: search.php?rented=success");
        exit();
    } else {
        $error_message = 'Unable to rent book. Please try again.';
    }
} elseif (isset($_POST['rent_book'])) {
    // Guest tried to rent a book
    $error_message = 'You must be logged in to rent a book.';
}

// Show success message if redirected
if (isset($_GET['rented']) && $_GET['rented'] == 'success') {
    $success_message = 'Book rented successfully!';
}

// Fetch categories from the table
$query = "SELECT DISTINCT category FROM books ORDER BY category ASC";
$result = $conn->prepare($query);
$result->execute();
$types = $result->fetchAll(PDO::FETCH_COLUMN);

// Search keyword
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Default query
$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

// Keyword filter
if (!empty($keyword)) {
    $sql .= " AND (title LIKE :keyword OR author LIKE :keyword OR description LIKE :keyword)";
    $params[':keyword'] = '%' . $keyword . '%';
}

// Year filter
if (isset($_GET['year']) && $_GET['year'] !== 'all') {
    $sql .= " AND publication_year = :year";
    $params[':year'] = $_GET['year'];
}

// Availability filter
if (isset($_GET['availability'])) {
    $sql .= " AND available_copies > 0";
}

// Category filter
if (isset($_GET['collectionType']) && is_array($_GET['collectionType']) && count($_GET['collectionType']) > 0) {
    $placeholders = [];
    foreach ($_GET['collectionType'] as $index => $type) {
        $key = ":collectionType$index";
        $placeholders[] = $key;
        $params[$key] = $type;
    }
    $sql .= " AND category IN (" . implode(',', $placeholders) . ")";
}

// Execute query
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="search-section">
<!-- Main Content -->
<div class="container my-4">
    <div class="row">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Sidebar Filters -->
        <aside class="sidebar book_filter col-lg-3 mb-4">
            <div class="card border-0">
                <div class="card-body">
                    <h5 class="card-title">Filters</h5>
                    <form id="filtersForm" method="GET" action="">
                        <!-- Publication Year -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Publication Year</label>
                            <div>
                                <?php
                                $years = ['all' => 'All', '2023' => '2023', '2022' => '2022', '2021' => '2021'];
                                foreach ($years as $value => $label) {
                                    $checked = (isset($_GET['year']) && $_GET['year'] == $value) || (!isset($_GET['year']) && $value == 'all') ? 'checked' : '';
                                    echo "
                                        <div class='form-check'>
                                            <input class='form-check-input' type='radio' name='year' id='year$value' value='$value' $checked />
                                            <label class='form-check-label' for='year$value'>$label</label>
                                        </div>
                                    ";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Availability -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Availability</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="availability" id="availabilityOnShelf" value="onShelf"
                                <?= isset($_GET['availability']) ? 'checked' : '' ?> />
                                <label class="form-check-label" for="availabilityOnShelf">On Shelf</label>
                            </div>
                        </div>

                        <!-- Category Type -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category Type</label>
                            <?php
                            $selectedTypes = isset($_GET['collectionType']) ? (array)$_GET['collectionType'] : [];
                            foreach ($types as $type) {
                                $safeType = htmlspecialchars($type);
                                $checked = in_array($type, $selectedTypes) ? 'checked' : '';
                                echo "
                                    <div class='form-check'>
                                        <input class='form-check-input' type='checkbox' 
                                            name='collectionType[]' id='collection$safeType' 
                                            value='$safeType' $checked />
                                        <label class='form-check-label' for='collection$safeType'>" . ucfirst($safeType) . "</label>
                                    </div>
                                ";
                            }
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Area -->
        <main class="col-lg-9">
            <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 p-3">
                <?php if (count($books) === 0): ?>
                    <p>No books found.</p>
                <?php else: ?>
                    <div class="mb-2">
                        Found <strong><?= count($books) ?></strong> result<?= count($books) !== 1 ? 's' : '' ?>.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Book List -->
            <div id="resultsInfo" class="mb-3 text-muted fw-semibold">
                <?php foreach ($books as $book): ?>
                    <div class="card p-3 mb-4 bg-light">
                        <div class="row g-3">
                            <div class="col-auto">
                                <img src="img/<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-image" />
                            </div>
                            <div class="col">
                                <h5 class="card-title">
                                    <a href="book_detail.php?id=<?= $book['book_id'] ?>"><?= htmlspecialchars($book['title']) ?></a>
                                </h5>
                                <div class="mb-2"><span class="tag"><?= htmlspecialchars($book['author']) ?></span></div>
                                <p class="card-text description"><?= htmlspecialchars(mb_strimwidth($book['description'], 0, 300, "...")) ?></p>
                            </div>
                            <div class="col-auto d-flex flex-column align-items-center justify-content-between">
                                <div class="availability-box"><span>Availability</span><?= $book['available_copies'] ?></div>

                                <?php if (!empty($_SESSION['user_id'])): ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                        <?php if ($book['available_copies'] > 0): ?>
                                            <button type="submit" name="rent_book" class="btn btn-outline-primary">Rent Book</button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary px-4" disabled>Unavailable</button>
                                        <?php endif; ?>
                                    </form>
                                <?php else: ?>
                                    <a href="customer_login.php" class="btn btn-outline-primary">Login to Rent</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
</div>
</section>
<!-- Auto-submit filters form -->
<script>
document.querySelectorAll('#filtersForm input').forEach(input => {
    input.addEventListener('change', () => {
        document.getElementById('filtersForm').submit();
    });
});
</script>

<?php include 'includes/footer.php'; ?>
