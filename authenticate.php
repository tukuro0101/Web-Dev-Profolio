<?php
include 'connection.php'; // Make sure this file exists and contains the PDO connection


// Signup
if (isset($_POST['signup'])) {
    // Validate input data
    $errors = [];

    // Check username length
    if (strlen($_POST['new_username']) < 6) {
        $errors[] = "Username must be at least 6 characters long";
    }

    // Check password length
    if (strlen($_POST['new_password']) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }

    // Check if password matches username
    if ($_POST['new_password'] === $_POST['new_username']) {
        $errors[] = "Password cannot be the same as username";
    }

    // Check if confirm password matches password
    if ($_POST['new_password'] !== $_POST['confirm_password']) {
        $errors[] = "Passwords do not match";
    }

    // Check if email is empty and valid
    if (empty($_POST['email'])) {
        $errors[] = "Email is required";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // If there are validation errors, redirect back to signup page with error messages
    if (!empty($errors)) {
        $_SESSION['signup_errors'] = $errors;
        header("Location: login.php"); // Redirect back to login page
        exit;
    }

    // No validation errors, proceed with signup
    $new_username = $_POST['new_username'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Hashing the password
    $type = 'normal'; // Default user type to normal
    $email = $_POST['email'];

    // Insert user into the database
    $stmt = $pdo->prepare("INSERT INTO `users` (`username`, `password`, `type`, `email`) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$new_username, $new_password, $type, $email])) {
        $_SESSION['signup_success'] = "Signup successful! Please login.";
        header('Location: login.php'); // Redirect to login page after successful signup
        exit;
    } else {
        $_SESSION['signup_error'] = "Signup failed! Please try again later.";
        header("Location: login.php"); // Redirect back to login page
        exit;
    }
}

// Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve user from the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Verify password
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
        $_SESSION['login_error'] = "Login failed! Please check your username and password.";
        header("Location: login.php"); // Redirect back to login page
        exit;
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
