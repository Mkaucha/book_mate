<?php
require_once 'config/settings.php';
require_once 'includes/auth.php';
include 'includes/header.php';

function sanitize($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

// If user is already logged in, redirect to home
if (isCustomerLoggedIn()) {
    header("Location: home.php");
    exit();
}

$login_error = '';

if ($_POST) {
    $username = isset($_POST['username']) ? sanitize($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (!login($username, $password, 'customer')) {
        $login_error = 'Invalid credentials or not a customer.';
    }
}
?>

<div class="container" style="height: 90vh;">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h2 class="text-center text-dark mt-5">Member Login Form</h2>
            <div class="card my-5">
                <?php if (!empty($login_error)): ?>
                    <div class="alert alert-danger text-center"><?php echo $login_error; ?></div>
                <?php endif; ?>

                <form class="card-body cardbody-color p-lg-5" method="POST" action="">
                    <div class="mb-3">
                        <label for="id"><b>User Name</b></label>
                        <input type="text" class="form-control" id="Username" name="username" required placeholder="User Name" 
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password"><b>Password</b></label>
                        <input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5 mb-5 w-100">Login</button>
                    </div>
                    <div id="emailHelp" class="form-text text-center mb-5 text-dark">
                        Not Registered? <a href="member_register.php" class="text-dark fw-bold">Create an Account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
