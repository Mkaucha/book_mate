<?php
// Start session safely and correctly
if (session_status() === PHP_SESSION_NONE) {
    session_name('customer_session'); // Customer session only
    session_start();
}
?>

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
      
      <!-- Logo -->
      <div class="logo-section">
        <a href="home.php" class="d-flex align-items-center text-decoration-none text-dark">
          <img src="img/icon.png" alt="Logo" width="30" class="me-2">
          <span class="fs-4 fw-bold">Book Mate</span>
        </a>
      </div>

      <!-- Navigation -->
      <nav class="d-flex align-items-center">
        <a href="home.php" class="me-3 text-decoration-none">Home</a>
        <a href="#" class="me-3 text-decoration-none">About Us</a>
        <a href="#" class="me-3 text-decoration-none">Contact Us</a>
        <a href="index.php" class="me-3 text-decoration-none" target="_blank">Admin Login</a>

        <?php if (!empty($_SESSION['username']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'customer'): ?>
          <!-- Logged-in CUSTOMER dropdown -->
          <div class="dropdown d-inline">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="customer_dashboard.php">My Dashboard</a></li>
              <li><a class="dropdown-item" href="search.php">Browse Books</a></li>
              <li><a class="dropdown-item" href="my_profile.php">My Profile</a></li>
              <li><a class="dropdown-item text-danger" href="customer_logout.php">Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <!-- Not logged in -->
          <a href="customer_login.php" class="btn btn-outline-primary ms-2">Member Login</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

