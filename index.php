<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" width="device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./login.css">
    <title>Login | Hospital Management</title>
</head>
<body>
    <div class="login-page">
        <div class="login-box">
            <div class="logo-area">
                <img src="./logo.png" alt="Hospital Logo" class="logo">
                <h2>Login</h2>
            </div>

            <form class="login-form" action="./php/login.php" method="POST">
                <div class="form-group">
                <label for="email">Email ID</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn-login">Login</button>

                <div class="extra-links">
                    Don't have an account? <a href="./register.html"> Sign Up</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
