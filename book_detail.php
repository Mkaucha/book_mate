<?php
  include 'header.php';
?>
  <!-- Product Detail Section -->
  <section class="py-5 detail mt-7">
    <div class="container">
      <div class="row g-4">
        <!-- Book Image -->
        <div class="col-md-3 text-center book-cover">
          <img src="img/postgres.jpg" alt="Book Cover" class="img-fluid rounded shadow" />
        </div>

        <!-- Book Info -->
        <div class="col-md-9 px-5">
          <h1 class="mb-3">PostgreSQL: Up and Running</h1>
          <h5 class="text-muted mb-4">by Regina O. Obe & Leo S. Hsu</h5>

          <p>
            Dive into PostgreSQL with this comprehensive guide that covers everything from installation to advanced
            features. Ideal for developers, DBAs, and data enthusiasts wanting to master PostgreSQL.
          </p>

          <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item"><strong>Publisher:</strong> O'Reilly Media</li>
            <li class="list-group-item"><strong>Year:</strong> 2023</li>
            <li class="list-group-item"><strong>Pages:</strong> 350</li>
            <li class="list-group-item"><strong>ISBN:</strong> 978-1492076803</li>
            <li class="list-group-item"><strong>Language:</strong> English</li>
            <li class="list-group-item"><strong>Availability:</strong> In Stock</li>
          </ul>

          <!-- Action Buttons -->
          <div class="d-flex flex-wrap gap-3">
            <button class="btn btn-primary px-4">Reserve</button>
            <button class="btn btn-outline-secondary px-4">Add to Wishlist</button>
            <a href="home.html" class="btn btn-link align-self-center px-0">← Back to Collections</a>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php
  include 'footer.php';
?>