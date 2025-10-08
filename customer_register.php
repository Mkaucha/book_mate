<?php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php'; // sanitize.
include 'includes/header.php';

// Redirect if already logged in
if (isCustomerLoggedIn()) {
    header("Location: home.php");
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
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registration_error = 'Invalid email format.';
    } elseif (strlen($password) < 6) {
        $registration_error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $registration_error = 'Passwords do not match.';
    } elseif (registerUser($username, $email, $password, $full_name, $phone, $address)) {
        $registration_success = true;
        
    } else {
        $registration_error = 'Email or username already exists.';
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-7 col-md-12">
            <h2 class="text-center text-dark mt-5">Member Register Form</h2>
            <div class="card o-hidden my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="p-5">
                                <?php if ($registration_error): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($registration_error); ?></div>
                                <?php endif; ?>
                                <?php if ($registration_success): ?>
                                    <div class="alert alert-success">
                                        Account created successfully! <a href="customer_login.php">Login here</a>
                                    </div>
                                <?php else: ?>
                                    <form class="user" method="POST" action="">
                                        <div class="form-group row mb-3">
                                                <label><b>User Name</b></label>
                                                <input type="text" name="username" class="form-control form-control-user" placeholder="User Name" required
                                                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label><b>Email Address</b></label>
                                            <input type="email" name="email" class="form-control form-control-user" placeholder="Email Address" required
                                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="full_name">Full Name:</label>
                                            <input type="text" id="full_name" class="form-control form-control-user" name="full_name" placeholder="Full Name" required 
                                                value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label><b>Phone</b></label>
                                            <input type="tel" name="phone" class="form-control form-control-user" placeholder="Phone" required
                                                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label><b>Address</b></label>
                                            <textarea name="address" class="form-control form-control-user" placeholder="Address" required ><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <div class="col-sm-6">
                                                <label><b>Password</b></label>
                                                <input type="password" name="password" class="form-control form-control-user" placeholder="Password" required>
                                            </div>
                                            <div class="col-sm-6">
                                                <label><b>Repeat Password</b></label>
                                                <input type="password" name="confirm_password" class="form-control form-control-user" placeholder="Repeat Password" required>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100 mb-3">Register Account</button>
                                    </form>
                                <?php endif; ?>

                                <hr>
                                <div class="text-center">
                                    <a href="customer_login.php" class="text-dark fw-bold">Already have an account? Login!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
