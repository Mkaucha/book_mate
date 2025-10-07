<?php
// includes/auth.php

//  Detect page context and start separate sessions for admin & customer
if (session_status() === PHP_SESSION_NONE) {
    $currentScript = basename($_SERVER['PHP_SELF']);

    if (strpos($currentScript, 'admin') !== false) {
        // Admin pages use separate session
        session_name('admin_session');
    } else {
        // Customer and general pages use customer session
        session_name('customer_session');
    }

    session_start();
}

require_once 'config/database.php';

// UNIVERSAL LOGIN FUNCTION
function login($username, $password, $allowedRole = null) {
    try {
        $database = new Database();
        $db = $database->getConnection();

        // Fetch user from database
        $query = "SELECT user_id, username, email, password, full_name, user_type, status 
                  FROM users 
                  WHERE username = :username AND status = 'active'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if (password_verify($password, $user['password'])) {

                // Restrict login if a specific role is required
                if ($allowedRole && $user['user_type'] !== $allowedRole) {
                    return false; // Not allowed to login on this page
                }

                // Use separate session names for admin and customer
                if (session_status() === PHP_SESSION_NONE) {
                    if ($user['user_type'] === 'admin') {
                        session_name('admin_session');
                    } else {
                        session_name('customer_session');
                    }
                    session_start();
                }

                // Set session variables
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id']   = $user['user_id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['full_name'] = $user['full_name'];

                // Redirect based on user type
                if ($user['user_type'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: home.php");
                }
                exit();
            }
        }

        return false; // Invalid credentials
    } catch (Exception $e) {
        echo "Login error: " . $e->getMessage();
        return false;
    }
}


//  Logout functions for both roles
function logout() {
    session_name("admin_session"); // target the admin session
    session_start();               // start that session
    session_destroy();             // destroy it
    header("Location: index.php"); // redirect to login page
    exit();
}

function customerLogout() {
    session_name("customer_session"); // target the customer session
    session_start();                  // start that session
    session_destroy();                // destroy it
    header("Location: home.php");     // redirect to home or login
    exit();
}

//  Session role checks
// function isLoggedIn() {
//     return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
// }

// function isAdmin() {
//     return isLoggedIn() && ($_SESSION['user_type'] ?? '') === 'admin';
// }

// function isCustomer() {
//     return isLoggedIn() && ($_SESSION['user_type'] ?? '') === 'customer';
// }
// Check if customer session exists
function isCustomerLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name("customer_session"); // Customer session
        session_start();
    }
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true
           && ($_SESSION['user_type'] ?? '') === 'customer';
}

// Check if admin session exists
function isAdminLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name("admin_session"); // Admin session
        session_start();
    }
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true
           && ($_SESSION['user_type'] ?? '') === 'admin';
}

//  Access restrictions
function requireLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: index.php");
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdminLoggedIn()) {
        header("Location: home.php");
        exit();
    }
}

function requireCustomer() {
    requireLogin();
    if (!isCustomerLoggedIn()) {
        header("Location: admin_dashboard.php");
        exit();
    }
}

//  Register new user (always as customer)
function registerUser($username, $email, $password, $full_name, $phone = '', $address = '') {
    try {
        $database = new Database();
        $db = $database->getConnection();

        // Duplicate check
        $query = "SELECT user_id FROM users WHERE username = :username OR email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return false;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, email, password, full_name, phone, address, user_type, status)
                  VALUES (:username, :email, :password, :full_name, :phone, :address, 'customer', 'active')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);

        return $stmt->execute();
    } catch (Exception $e) {
        echo "Registration error: " . $e->getMessage();
        return false;
    }
}
?>
