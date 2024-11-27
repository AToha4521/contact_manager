<?php
session_start();
if (isset($_POST['login'])) {
    // XAMPP MySQL connection setup
    $conn = new mysqli('localhost', 'root', '', 'contact_db');
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Password encryption

    $result = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password'");
    if ($result->num_rows > 0) {
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
    } else {
        echo "Invalid login! Either The account was not Created or incorrect Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2 class="login-title">Welcome User!</h2>
        <form action="login.php" method="post" class="form-container">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn-submit">Login</button>
        </form>

        <!-- New 'Create Account' Link Box Under Login Form -->
        <div class="create-account-box">
            <p>Don't have an account? <a href="register.php">Create Account</a></p>
        </div>
    </div>
    <div id="popup" class="popup hidden">Welcome Back!</div>
</body>
</html>