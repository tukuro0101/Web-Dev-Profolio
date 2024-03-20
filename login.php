<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'style.php'; ?>
    <title>Login & Sign Up</title>
</head>
<body>
    <div class="page_container">
        <header><?php include 'nav.php'; ?></header>
        
        <div class="form-container">
            <div id="login-form">
                <h2>Login</h2>
                <form action="authenticate.php" method="post">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
                </form>
                <p>Don't have an account? <button onclick="toggleForms()">Sign up here</button></p>
            </div>

            <div id="signup-form" style="display: none;">
                <h2>Sign Up</h2>
                <form action="authenticate.php" method="post">
                    <input type="text" name="new_username" placeholder="Username" required>
                    <input type="password" name="new_password" placeholder="Password" required>
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

        <footer> <?php include 'contact.php'; ?></footer>
    </div>

  
</body>
</html>
