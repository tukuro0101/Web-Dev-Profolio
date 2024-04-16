<?php
include 'connection.php';
session_start();
// Check if a message is set (indicating a login attempt)
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    echo "<div class='message'>$message</div>";
    unset($_SESSION['message']); // Clear the message after displaying

    // Add a delay before redirecting
    echo "<script>setTimeout(function() { window.location.href = 'login.php'; }, 3000);</script>";
}

// Determine the default form mode
$formMode = isset($_SESSION['form_mode']) ? $_SESSION['form_mode'] : 'login';

// Signup
if (isset($_POST['signup'])) {
    // Validate input data
    $errors = [];
    // Set form mode to signup
    $_SESSION['form_mode'] = 'signup';

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
    try {
        if ($stmt->execute([$new_username, $new_password, $type, $email])) {
            $_SESSION['message'] = "Signup successful! Please login.";
            header('Location: login.php'); // Redirect to login page after successful signup
            exit;
        }
    } catch (PDOException $e) {
        // Check if the error is due to a duplicate email
        if ($e->getCode() == 23000) {
            $_SESSION['signup_errors'] = ["The email is already used"];
            header("Location: login.php"); // Redirect back to login page with error message
            exit;
        } else {
            // For other database errors
            $_SESSION['message'] = "Signup failed! Please try again later.";
            header("Location: login.php"); // Redirect back to login page
            exit;
        }
    }
}

// Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Set form mode to login
    $_SESSION['form_mode'] = 'login';

    // Retrieve user from the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_type'] = $user['type'];
        $_SESSION['username'] = $user['username'];

        // Set message for successful login
        $_SESSION['message'] = "Login successful!";
        if (strtolower($user['type']) == 'admin') {
            header('Location: admin_panel.php');
            exit;
        } else {
            header('Location: login.php'); // Redirect to login page after successful login
            exit;
        }
    } else {
        // Set message for failed login
        $_SESSION['message'] = "Login failed! Please check your username and password.";
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Sign Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <header>
            <?php include 'nav.php'; // Include your navigation header ?>
        </header>
        
        <div class="form-container">
            <div id="login-form" style="<?php echo ($formMode === 'login') ? 'display: block;' : 'display: none;'; ?>">
                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="error-message"><?php echo $_SESSION['login_error']; ?></div>
                    <?php unset($_SESSION['login_error']); // Clear the error message after displaying ?>
                <?php endif; ?>
                <h2>Login</h2>
                <form action="login.php" method="post">
                    <input type="hidden" name="action" value="login">
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="message"><?php echo $_SESSION['message']; ?></div>
                        <?php unset($_SESSION['message']); // Clear the message after displaying ?>
                    <?php endif; ?>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
                </form>
                <p>Don't have an account? <button onclick="toggleForms()">Sign up here</button></p>
            </div>

            <div id="signup-form" style="<?php echo ($formMode === 'signup') ? 'display: block;' : 'display: none;'; ?>">
                <?php if (isset($_SESSION['signup_errors']) && !empty($_SESSION['signup_errors'])): ?>
                    <div class="error-message">
                        <?php 
                        // Display all signup errors
                        foreach ($_SESSION['signup_errors'] as $error) {
                            echo $error . "<br>";
                        }
                        unset($_SESSION['signup_errors']); // Clear errors after displaying
                        ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="message"><?php echo $_SESSION['message']; ?></div>
                    <?php unset($_SESSION['message']); // Clear the message after displaying ?>
                <?php endif; ?>
                <h2>Sign Up</h2>
                <form action="login.php" method="post">
                    <input type="hidden" name="action" value="signup">
                    <input type="text" name="new_username" placeholder="Username" required>
                    <input type="password" name="new_password" placeholder="Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <button type="submit" name="signup">Sign Up</button>
                </form>
                <p>Already have an account? <button onclick="toggleForms()">Login here</button></p>
            </div>
        </div>

        <script>
            function toggleForms() {
                var loginForm = document.getElementById('login-form');
                var signupForm = document.getElementById('signup-form');
                if (loginForm.style.display === 'none') {
                    loginForm.style.display = 'block';
                    signupForm.style.display = 'none';
                } else {
                    loginForm.style.display = 'none';
                    signupForm.style.display = 'block';
                }
            }
        </script>

        <footer>
            <?php include 'contact.php'; // Include your footer or contact info ?>
        </footer>
    </div>
</body>

<style>
    body{background: rgb(70,70,70);
background: linear-gradient(90deg, rgba(70,70,70,1) 0%, rgba(25,25,25,1) 20%, rgba(71,71,71,1) 40%, rgba(0,0,0,1) 60%, rgba(38,38,45,1) 80%, rgba(14,21,23,1) 100%);}
.container{background: whitesmoke;}
</style>
</html>
