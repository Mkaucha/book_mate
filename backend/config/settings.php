<?php
// config/settings.php
define('SITE_URL', 'http://localhost/bookmate/');
define('SITE_NAME', 'BookMate Library Management System');
define('ADMIN_EMAIL', 'admin@bookmate.com');
define('RENTAL_PERIOD_DAYS', 14);
define('FINE_PER_DAY', 1.00);
define('MAX_BOOKS_PER_USER', 5);

// Email settings for notifications
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com');
define('SMTP_PASSWORD', 'your_app_password');
define('FROM_EMAIL', 'noreply@bookmate.com');
define('FROM_NAME', 'BookMate Library');

// Session settings
ini_set('session.cookie_lifetime', 3600);
ini_set('session.gc_maxlifetime', 3600);
session_start();
?>