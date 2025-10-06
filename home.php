<?php
  require_once "config/database.php";
  require_once 'config/settings.php';
  require_once 'includes/auth.php';
  require_once 'includes/functions.php';
  require_once 'models/User.php';
  require_once 'models/Book.php';
  include 'includes/header.php';

// requireLogin();

  // Create DB instance and get connection
  $database = new Database();
  $conn = $database->getConnection();
  
  $query1 = "SELECT * FROM books WHERE available_copies > 0 LIMIT 10";
  $stmt = $conn->prepare($query1);
  $stmt->execute();

  $popularBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Fetch the most recently added books
  $query2 = "SELECT * FROM books ORDER BY added_date DESC LIMIT 10";
  $stmt = $conn->prepare($query2);
  $stmt->execute();

  $newBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $query3 = "SELECT DISTINCT category FROM books WHERE category IS NOT NULL AND category != ''";
  $stmt = $conn->prepare($query3);
  $stmt->execute();
  $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

  <!-- Hero Search -->
<section class="py-5 text-white text-center search">
  <div class="container">
    <form action="search.php" method="GET" class="input-group w-75 mx-auto">
      <input 
        type="text" 
        class="form-control" 
        placeholder="Search by Title, Author..." 
        name="keyword"
        required
      >
      <button class="btn btn-primary" type="submit">Search</button>
    </form>
  </div>
</section>

  <!-- Topics -->
  <section class="py-5 collections">
    <div class="container">
      <h2 class="mb-4">Select the topic you are interested in</h2>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
          <div class="col">
            <div class="p-3 bg-light border rounded text-center cards">
              <img src="img/literature.png" alt="">
              <p>Literature</p>
            </div>
          </div>
      
        <div class="col">
          <div class="p-3 bg-light border rounded text-center cards">
            <img src="img/social-science.png" alt="">
            <p>Social Sciences</p>
          </div>
        </div>
        <div class="col">
          <div class="p-3 bg-light border rounded text-center cards">
            <img src="img/science.png" alt="">
            <p>Applied Sciences</p>
          </div>
        </div>
        <div class="col">
          <div class="p-3 bg-light border rounded text-center cards">
            <img src="img/prototype.png" alt="">
            <p>Art & Recreation</p>
          </div>
        </div>
        <div class="col">
          <div class="p-3 bg-light border rounded text-center cards">
            <img src="img/application.png" alt="">
            <p>See More</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Popular Collections -->
  <section class="py-5 bg-light collections">
    <div class="container">
      <h2 class="mb-3">Popular among our collections</h2>
      <div class="mb-3">
        <?php foreach ($categories as $cat): ?>
          <span class="badge bg-secondary me-2"><?= htmlspecialchars($cat['category']) ?></span>
        <?php endforeach; ?>
      </div>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
        <?php foreach($popularBooks as $book): ?>
        <div class="col">
          <a href="book_detail.php?id=<?= $book['book_id'] ?>">
            <div class="p-3 bg-white border rounded text-center cards">
              <img src="img/<?= htmlspecialchars($book['cover_image']) ?>" alt="" class="mw-100">
              <p><?= htmlspecialchars($book['description']) ?></p>
            </div>
          </a>
          </div>
          <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- New Collections -->
  <section class="py-5 collections">
    <div class="container">
      <h2 class="mb-3">New collections + updated</h2>
      <div class="mb-3">
        <?php foreach ($categories as $cat): ?>
            <span class="badge bg-secondary me-2"><?= htmlspecialchars($cat['category']) ?></span>
        <?php endforeach; ?>
      </div>
     <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
        <?php foreach($newBooks as $book): ?>
        <div class="col">
          <a href="book_detail.php?id=<?= $book['book_id'] ?>">
            <div class="p-3 bg-white border rounded text-center cards">
              <img src="img/<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="mw-100">
              <p><?= htmlspecialchars($book['description']) ?></p>
            </div>
          </a>
        </div>
        <?php endforeach ?>
      </div>
    </div>
  </section>


<?php
  include 'includes/footer.php';
?>
