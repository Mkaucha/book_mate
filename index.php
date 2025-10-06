
<?php
require_once 'config/settings.php';
require_once 'includes/auth.php';

function sanitize($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: home.php");
    }
    exit();
}

$login_error = '';
if ($_POST) {
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (login($username, $password)) {
        if (isAdmin()) {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: home.php");
        }
        exit();
    } else {
        $login_error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <h1><img src="img/icon.png" alt="Logo" width="30" class="me-2"> BookMate</h1>
                <p>Library Management System</p>
            </div>

            <?php if ($login_error): ?>
                <div class="alert alert-error"><?php echo $login_error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Login</button>
            </form>

            <div class="login-links">
                <a href="register.php">Create New Account</a>
            </div>

            <div class="demo-accounts">
                <h3>Demo Accounts</h3>
                <p><strong>Admin:</strong> username: admin, password: admin123</p>
                <p><strong>Customer:</strong> Register a new account</p>
            </div>
        </div>
    </div>
</body>
</html>
