<?php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php'; // sanitize, etc.
include 'includes/header.php';

// Redirect if already logged in
if (isCustomerLoggedIn()) {
    header("Location: home.php");
    exit();
}

$registration_error = '';
$registration_success = false;

if ($_POST) {
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $full_name = trim($first_name . ' ' . $last_name);
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registration_error = 'Invalid email format.';
    } elseif (strlen($password) < 6) {
        $registration_error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $registration_error = 'Passwords do not match.';
    } elseif (registerUser($full_name, $email, $password, $phone, $address)) {
        $registration_success = true;
        // Redirect to login page after successful registration
        header("Location: customer_login.php?registered=1");
        exit();
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
                                            <div class="col-sm-6">
                                                <label><b>First Name</b></label>
                                                <input type="text" name="first_name" class="form-control form-control-user" placeholder="First Name" required
                                                    value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <label><b>Last Name</b></label>
                                                <input type="text" name="last_name" class="form-control form-control-user" placeholder="Last Name" required
                                                    value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label><b>Email Address</b></label>
                                            <input type="email" name="email" class="form-control form-control-user" placeholder="Email Address" required
                                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label><b>Phone</b></label>
                                            <input type="tel" name="phone" class="form-control form-control-user" placeholder="Phone"
                                                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label><b>Address</b></label>
                                            <textarea name="address" class="form-control form-control-user" placeholder="Address"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
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
