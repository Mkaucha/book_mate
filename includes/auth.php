<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Add better error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

function login($username, $password) {
    try {
        $database = new Database();
        $db = $database->getConnection();

        if (!$db) {
            echo "Database connection failed";
            return false;
        }

        $query = "SELECT user_id, username, email, password, full_name, user_type, status 
                  FROM users WHERE username = :username AND status = 'active'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Debugging output: uncomment these lines if login fails
            // echo "Entered username: " . $username . "<br>";
            // echo "Database username: " . $user['username'] . "<br>";
            // echo "Password verify result: " . (password_verify($password, $user['password']) ? 'true' : 'false') . "<br>";

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['logged_in'] = true;
                return true;
            }
        }
        return false;
    } catch (Exception $e) {
        echo "Login error: " . $e->getMessage();
        return false;
    }
}

function logout() {
    session_destroy();
    header("Location: index.php");
    exit();
}

function customerLogout(){
    session_destroy();
    header("Location: home.php");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: home.php");
        exit();
    }
}

function registerUser($username, $email, $password, $full_name, $phone = '', $address = '') {
    try {
        $database = new Database();
        $db = $database->getConnection();

        // Check if username or email already exists
        $query = "SELECT user_id FROM users WHERE username = :username OR email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return false; // User already exists
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, email, password, full_name, phone, address) 
                  VALUES (:username, :email, :password, :full_name, :phone, :address)";
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
