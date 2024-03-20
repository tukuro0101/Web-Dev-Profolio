<?php
session_start();
include 'connection.php'; // Make sure this file exists and contains the PDO connection

function redirect_to_login() {
    header('Location: login.php');
    exit;
}

// Sign Up
if (isset($_POST['signup'])) {
    $new_username = $_POST['new_username'];
    // If you don't want hashed passwords, simply remove the password_hash function
    $new_password = $_POST['new_password']; 
    $type = 'normal'; // Default user type to normal

    // The column names should not be in single quotes
    $stmt = $pdo->prepare("INSERT INTO user (Username, Password, Type) VALUES (?, ?, ?)");
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

    // Since you're not using hashed passwords, change this to a direct comparison
    $stmt = $pdo->prepare("SELECT * FROM user WHERE Username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $user['Password'] === $password) {
        $_SESSION['user_id'] = $user['User_ID'];
        $_SESSION['user_type'] = $user['Type'];
        $_SESSION['username'] = $user['Username'];

        if (strtolower($user['Type']) === 'admin') {
            header('Location: admin_panel.php');
            exit;
        } else {
            header('Location: main.php');
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
