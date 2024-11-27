<?php
if (isset($_POST['register'])) {
    $conn = new mysqli('localhost', 'root', '', 'contact_db');

    // Collect data from the form
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Encrypt the password
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];

    // Insert the data into the database
    $conn->query("INSERT INTO users (username, password, name, email, dob) VALUES ('$username', '$password', '$name', '$email', '$dob')");
    
    // Redirect to the login page after successful registration
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script defer src="script.js"></script>
</head>
<body>
    <div class="container">
        <h2 class="login-title">No account? Create new!</h2>
        <form method="POST" action="" class="form-container">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" required>
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter a username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter a password" required>
            </div>
            <button type="submit" name="register" class="btn-submit">Register</button>
        </form>
        <div class="create-account-box">
            <p>Already have an account? <a href="index.php">Log in</a></p>
        </div>
    </div>
</body>
</html>
