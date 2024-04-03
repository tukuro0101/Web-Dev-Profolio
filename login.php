<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Sign Up</title>
</head>
<body>
    <div class="page_container">
        <header>
            <?php include 'nav.php'; // Include your navigation header ?>
        </header>
        
        <div class="form-container">
            <div id="login-form">
                <h2>Login</h2>
                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="error-message"><?php echo $_SESSION['login_error']; ?></div>
                    <?php unset($_SESSION['login_error']); // Clear the error message after displaying ?>
                <?php endif; ?>
                <form action="authenticate.php" method="post">
                    <input type="hidden" name="action" value="login">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
                </form>
                <p>Don't have an account? <button onclick="toggleForms()">Sign up here</button></p>
            </div>

            <div id="signup-form" style="display: none;">
                <h2>Sign Up</h2>
                <?php if (isset($_SESSION['signup_errors'])): ?>
                    <div class="error-message">
                        <?php 
                        // Display all signup errors
                        echo implode('<br>', $_SESSION['signup_errors']); 
                        unset($_SESSION['signup_errors']); // Clear errors after displaying
                        ?>
                    </div>
                <?php endif; ?>
                <form action="authenticate.php" method="post">
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
                loginForm.style.display = loginForm.style.display === 'none' ? 'block' : 'none';
                signupForm.style.display = signupForm.style.display === 'none' ? 'block' : 'none';
            }
        </script>

        <footer>
            <?php include 'contact.php'; // Include your footer or contact info ?>
        </footer>
    </div>
</body>
</html>