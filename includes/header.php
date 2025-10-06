<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Book Mate</title>
  <link rel="icon" href="img/favicon.png" type="image/png">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style2.css" rel="stylesheet">
</head>
<body>

  <!-- Header -->
  <header class="bg-light py-3 border-bottom">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="logo-section">
          <a href="home.php" class="d-flex align-items-center"><img src="img/icon.png" alt="Logo" width="30" class="me-2">
          <span class="fs-4 fw-bold">Book Mate</span></a>
      </div>
      <nav>
        <a href="home.php" class="me-3 text-decoration-none">Home</a>
        <a href="#" target="_blank" class="me-3 text-decoration-none">About Us</a>
        <a href="#" target="_blank" class="me-3 text-decoration-none">Contact Us</a>
        <a href="index.php" target="_blank" class="me-3 text-decoration-none">Admin Login</a>
         <?php if (!empty($_SESSION['username'])): ?>
        <!-- Logged-in user dropdown -->
        <div class="dropdown d-inline">
          <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="customer_dashboard.php">My Dashboard</a></li>
            <li><a class="dropdown-item" href="search.php">Browse Books</a></li>
            <li><a class="dropdown-item" href="my_profile.php">My Profile</a></li>
            <li><a class="dropdown-item" href="customer_logout.php">Logout</a></li>
          </ul>
        </div>
      <?php else: ?>
        <!-- Not logged in -->
        <a href="customer_login.php" class="me-3 text-decoration-none">Member Login</a>
      <?php endif; ?>
      </nav>
    </div>
  </header>