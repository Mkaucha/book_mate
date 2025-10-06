<?php
// my_profile.php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/User.php';
include 'includes/header.php';

// requireLogin();

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

// Helper to safely echo data
function safe($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<section style='background: #f5f5f5;' class="py-5">
    <div class="container">
        <div class="page-header text-center mb-4">
            <h1 class="fw-bold">My Profile</h1>
            <p class="text-muted">Update your account information</p>
        </div>

        <!-- Alert Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo safe($success_message); ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo safe($error_message); ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Profile Information -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100 p-4">
                    <div class="card-body">
                        <h4 class="card-title border-start border-primary ps-2 mb-3">Profile Information</h4>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" id="username" name="username" 
                                    class="form-control" value="<?php echo safe($user_data['username'] ?? ''); ?>" disabled>
                                <small class="text-muted">Username cannot be changed</small>
                            </div>

                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" id="full_name" name="full_name" required
                                    class="form-control" value="<?php echo safe($user_data['full_name'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" required
                                    class="form-control" value="<?php echo safe($user_data['email'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" id="phone" name="phone"
                                    class="form-control" value="<?php echo safe($user_data['phone'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea id="address" name="address" rows="3" class="form-control"><?php echo safe($user_data['address'] ?? ''); ?></textarea>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Account Information -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100 p-4">
                    <div class="card-body">
                        <h4 class="card-title border-start border-primary ps-2 mb-3">Account Information</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Member Since:</strong>
                                <span><?php echo safe(isset($user_data['created_date']) ? formatDate($user_data['created_date']) : ''); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Account Status:</strong>
                                <span class="badge rounded-pill 
                                    <?php echo isset($user_data['status']) && $user_data['status'] == 'active' ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo safe(isset($user_data['status']) ? ucfirst($user_data['status']) : ''); ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>User Type:</strong>
                                <span><?php echo safe(isset($user_data['user_type']) ? ucfirst($user_data['user_type']) : ''); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php' ?>
