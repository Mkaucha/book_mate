<?php
  include 'header.php';
?>

  <!-- Hero Search -->
  <section class="py-5 bg-primary text-white text-center search">
    <div class="container">
      <div class="input-group w-75 mx-auto">
        <input type="text" class="form-control" placeholder="Enter keyword to search collection...">
        <button class="btn btn-dark"><a href="search.php">Search</a></button>
      </div>
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
        <span class="badge bg-secondary me-2">Programming</span>
        <span class="badge bg-secondary me-2">Website</span>
        <span class="badge bg-secondary me-2">Operating System</span>
        <span class="badge bg-secondary me-2">Linux</span>
        <span class="badge bg-secondary">Computer</span>
      </div>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
        <div class="col">
          <a href="book_detail.php">
            <div class="p-3 bg-white border rounded text-center cards">
              <img src="img/postgres.jpg" alt="">
              <p>Dive into PostgreSQL with this comprehensive guide that covers everything from installation to advanced
                features. Ideal for developers, DBAs, and data enthusiasts wanting to master PostgreSQL.</p>
            </div>
          </a>
          </div>
          <div class="col">
            <a href="book_detail.php">
              <div class="p-3 bg-white border rounded text-center cards">
                <img src="img/ajax.jpg" alt="">
                <p>"Discover the essentials of AJAX—Asynchronous JavaScript and XML—a powerful technique that enables dynamic, fast, and seamless web page updates without full reloads. Perfect for building modern interactive web applications."</p>
              </div>
            </a>
          </div>
          <div class="col">
            <a href="book_detail.php">
              <div class="p-3 bg-white border rounded text-center cards">
                <img src="img/web.jpg" alt="">
                <p>"Explore the foundations of modern web architecture, including client-server models, APIs, microservices, and scalable design patterns. This guide helps developers build robust, efficient, and maintainable web applications."</p>
              </div>
            </a>
          </div>
        <div class="col">
          <a href="book_detail.php">
            <div class="p-3 bg-white border rounded text-center cards">
              <img src="img/producing.jpg" alt="">
              <p>A practical guide to successfully leading and managing open source projects. This book covers collaboration, licensing, community building, infrastructure, and the social dynamics behind sustainable open source development.</p>
            </div>
          </a>
        </div>
        <div class="col">
          <a href="book_detail.php">
            <div class="p-3 bg-white border rounded text-center cards">
              <img src="img/information.jpg" alt="">
              <p>Learn how to structure and organize digital content for clarity, usability, and efficiency. This book introduces core principles of information architecture, helping designers and developers create intuitive websites and apps that users can navigate with ease</p>
            </div>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- New Collections -->
  <section class="py-5 collections">
    <div class="container">
      <h2 class="mb-3">New collections + updated</h2>
      <div class="mb-3">
        <span class="badge bg-secondary me-2">Programming</span>
        <span class="badge bg-secondary me-2">Website</span>
        <span class="badge bg-secondary me-2">Operating System</span>
        <span class="badge bg-secondary me-2">Linux</span>
        <span class="badge bg-secondary">Computer</span>
      </div>
     <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
        <div class="col">
          <a href="book_detail.php">
            <div class="p-3 bg-white border rounded text-center cards">
              <img src="img/postgres.jpg" alt="">
              <p>Dive into PostgreSQL with this comprehensive guide that covers everything from installation to advanced
                features. Ideal for developers, DBAs, and data enthusiasts wanting to master PostgreSQL.</p>
            </div>
          </a>
          </div>
          <div class="col">
            <a href="book_detail.php">
              <div class="p-3 bg-white border rounded text-center cards">
                <img src="img/ajax.jpg" alt="">
                <p>"Discover the essentials of AJAX—Asynchronous JavaScript and XML—a powerful technique that enables dynamic, fast, and seamless web page updates without full reloads. Perfect for building modern interactive web applications."</p>
              </div>
            </a>
          </div>
          <div class="col">
            <a href="book_detail.php">
              <div class="p-3 bg-white border rounded text-center cards">
                <img src="img/web.jpg" alt="">
                <p>"Explore the foundations of modern web architecture, including client-server models, APIs, microservices, and scalable design patterns. This guide helps developers build robust, efficient, and maintainable web applications."</p>
              </div>
            </a>
          </div>
        <div class="col">
          <a href="book_detail.php">
            <div class="p-3 bg-white border rounded text-center cards">
              <img src="img/producing.jpg" alt="">
              <p>A practical guide to successfully leading and managing open source projects. This book covers collaboration, licensing, community building, infrastructure, and the social dynamics behind sustainable open source development.</p>
            </div>
          </a>
        </div>
        <div class="col">
          <a href="book_detail.php">
            <div class="p-3 bg-white border rounded text-center cards">
              <img src="img/information.jpg" alt="">
              <p>Learn how to structure and organize digital content for clarity, usability, and efficiency. This book introduces core principles of information architecture, helping designers and developers create intuitive websites and apps that users can navigate with ease</p>
            </div>
          </a>
        </div>
      </div>
    </div>
  </section>


<?php
  include 'footer.php';
?>
