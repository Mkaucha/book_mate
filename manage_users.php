<?php
// manage_users.php
require_once 'config/settings.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'models/User.php';

requireAdmin();

$user = new User();
$users = $user->getAllCustomers();

// Handle user status toggle
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $user_id = $_GET['toggle_status'];
    $user_data = $user->getUserById($user_id);

    if ($user_data) {
        $user->user_id = $user_id;
        $user->full_name = $user_data['full_name'];
        $user->email = $user_data['email'];
        $user->phone = $user_data['phone'];
        $user->address = $user_data['address'];
        $user->status = ($user_data['status'] == 'active') ? 'inactive' : 'active';

        if ($user->updateUser()) {
            $status = $user->status == 'active' ? 'activated' : 'deactivated';
            header("Location: manage_users.php?status_changed={$status}");
            exit();
        }
    }
}

$success_message = '';
if (isset($_GET['status_changed'])) {
    $success_message = 'User ' . $_GET['status_changed'] . ' successfully!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <a href="admin_dashboard.php" style="text-decoration: none; color: inherit;">
                <h2><img src="img/icon.png" alt="Logo" width="30" class="me-2"> BookMate Admin</h2>
            </a>
        </div>
        <div class="nav-menu">
            <a href="admin_dashboard.php" class="nav-link">Dashboard</a>
            <a href="manage_books.php" class="nav-link">Books</a>
            <a href="manage_rentals.php" class="nav-link">Rentals</a>
            <a href="manage_users.php" class="nav-link active">Users</a>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
        <div class="nav-user">
            Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Manage Users</h1>
            <p>View and manage library customers</p>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Member Since</th>
                        <th>Status</th>
                        <th>Current Rentals</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): 
                        // Get current rental count
                        require_once 'models/Rental.php';
                        $rental = new Rental();
                        $active_rentals = $rental->getUserActiveRentalsCount($row['user_id']);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo formatDate($row['created_date']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $row['status'] == 'active' ? 'status-active' : 'status-overdue'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td class="text-center"><?php echo $active_rentals; ?></td>
                        <td class="actions">
                            <a href="user_details.php?id=<?php echo $row['user_id']; ?>" 
                               class="btn btn-small btn-secondary">View</a>
                            <a href="manage_users.php?toggle_status=<?php echo $row['user_id']; ?>" 
                               class="btn btn-small <?php echo $row['status'] == 'active' ? 'btn-danger' : 'btn-primary'; ?>"
                               onclick="return confirm('Are you sure you want to <?php echo $row['status'] == 'active' ? 'deactivate' : 'activate'; ?> this user?')">
                                <?php echo $row['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>