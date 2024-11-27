<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'config.php'; // Database connection

$message = "";

// Fetch user profile data
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT name, email, dob, username FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $new_username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the new password if provided
    $password_hash = !empty($password) ? md5($password) : null;

    // Update query
    if ($password_hash) {
        $update_query = "UPDATE users SET name = ?, email = ?, dob = ?, username = ?, password = ? WHERE username = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssssss", $name, $email, $dob, $new_username, $password_hash, $username);
    } else {
        $update_query = "UPDATE users SET name = ?, email = ?, dob = ?, username = ? WHERE username = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssss", $name, $email, $dob, $new_username, $username);
    }

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
        $_SESSION['username'] = $new_username; // Update session username if changed
    } else {
        $message = "Error updating profile: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            padding-right: 40px!important;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            font-size: 1rem;
            color: #ddd;
        }

        input[type="text"], input[type="email"], input[type="date"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            background-color: #333;
            color: #ffffff;
            border: 1px solid #444;
            border-radius: 5px;
            font-size: 1rem;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin: 20px 0;
            font-size: 1rem;
            color: #4caf50;
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" value="<?php echo htmlspecialchars($user_data['name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user_data['dob'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter new username" value="<?php echo htmlspecialchars($user_data['username'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter new password (optional)">
            </div>
            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
