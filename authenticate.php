<?php
include 'connection.php'; // Make sure this file exists and contains the PDO connection

function redirect_to_login() {
    header('Location: login.php');
    exit;
}

// Sign Up
if (isset($_POST['signup'])) {
    $new_username = $_POST['new_username'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Hashing the password
    $type = 'normal'; // Default user type to normal

    // Correcting the column names as per your database structure
    $stmt = $pdo->prepare("INSERT INTO users (username, password, type) VALUES (?, ?, ?)");
    if ($stmt->execute([$new_username, $new_password, $type])) {
        echo "Signup successful!";
        $_SESSION['user_type'] = $type;
        $_SESSION['username'] = $new_username;
        redirect_to_login();
    } else {
        echo "Signup failed!";
    }
}

// Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Using prepared statements to prevent SQL injection
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Verify the password against the hashed password in the database
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_type'] = $user['type'];
        $_SESSION['username'] = $user['username'];

        // Redirect based on user type
        if (strtolower($user['type']) == 'admin') {
            header('Location: admin_panel.php');
            exit;
        } else {
            header('Location: main.php'); // Assuming there's a user_panel.php for normal users
            exit;
        }
    } else {
        echo "Login failed!";
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
