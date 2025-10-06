<?php
// register.php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php'; // <-- make sure this is present

if (isLoggedIn()) {
    header("Location: customer_dashboard.php");
    exit();
}

$registration_error = '';
$registration_success = false;

if ($_POST) {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');

    // Validation
    if (strlen($password) < 6) {
        $registration_error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $registration_error = 'Passwords do not match';
    } elseif (registerUser($username, $email, $password, $full_name, $phone, $address)) {
        $registration_success = true;
    } else {
        $registration_error = 'Username or email already exists';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box register-box">
            <div class="logo">
                <h1>📚 BookMate</h1>
                <p>Create Your Account</p>
            </div>

            <?php if ($registration_error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($registration_error); ?></div>
            <?php endif; ?>

            <?php if ($registration_success): ?>
                <div class="alert alert-success">
                    Account created successfully! <a href="index.php">Login here</a>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" required 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="full_name">Full Name:</label>
                        <input type="text" id="full_name" name="full_name" required 
                               value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="address">Address:</label>
                        <textarea id="address" name="address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">Register</button>
                </form>
            <?php endif; ?>

            <div class="login-links">
                <a href="index.php">Already have an account? Login</a>
            </div>
        </div>
    </div>
</body>
</html>
