<?php
// my_profile.php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/User.php';

requireLogin();

$user = new User();
$user_data = $user->getUserById($_SESSION['user_id']);

$success_message = '';
$error_message = '';

if ($_POST) {
    $user->user_id = $_SESSION['user_id'];
    $user->full_name = sanitize($_POST['full_name'] ?? '');
    $user->email = sanitize($_POST['email'] ?? '');
    $user->phone = sanitize($_POST['phone'] ?? '');
    $user->address = sanitize($_POST['address'] ?? '');
    $user->status = $user_data['status']; // Keep current status

    if ($user->updateUser()) {
        $_SESSION['full_name'] = $user->full_name; // Update session
        $success_message = 'Profile updated successfully!';
        $user_data = $user->getUserById($_SESSION['user_id']); // Refresh data
    } else {
        $error_message = 'Failed to update profile.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <h2>📚 BookMate</h2>
        </div>
        <div class="nav-menu">
            <a href="customer_dashboard.php" class="nav-link">My Dashboard</a>
            <a href="browse_books.php" class="nav-link">Browse Books</a>
            <a href="my_profile.php" class="nav-link active">My Profile</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
        <div class="nav-user">
            Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>My Profile</h1>
            <p>Update your account information</p>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="section">
                <div class="section-header">
                    <h2>Profile Information</h2>
                </div>

                <form method="POST" action="" class="form">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo htmlspecialchars($user_data['username']); ?>" disabled>
                        <small>Username cannot be changed</small>
                    </div>

                    <div class="form-group">
                        <label for="full_name">Full Name:</label>
                        <input type="text" id="full_name" name="full_name" required
                               value="<?php echo htmlspecialchars($user_data['full_name']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required
                               value="<?php echo htmlspecialchars($user_data['email']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="tel" id="phone" name="phone"
                               value="<?php echo htmlspecialchars($user_data['phone']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="address">Address:</label>
                        <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user_data['address']); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>

            <div class="section">
                <div class="section-header">
                    <h2>Account Information</h2>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <strong>Member Since:</strong>
                        <span><?php echo formatDate($user_data['created_date']); ?></span>
                    </div>

                    <div class="info-item">
                        <strong>Account Status:</strong>
                        <span class="status-badge <?php echo $user_data['status'] == 'active' ? 'status-active' : 'status-overdue'; ?>">
                            <?php echo ucfirst($user_data['status']); ?>
                        </span>
                    </div>

                    <div class="info-item">
                        <strong>User Type:</strong>
                        <span><?php echo ucfirst($user_data['user_type']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .info-grid {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .info-item:last-child {
        border-bottom: none;
    }
    </style>
</body>
</html>