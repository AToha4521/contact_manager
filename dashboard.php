<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'contact_db');

// Ensure database connection is successful
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch the display name (name) using the session's username
$username = $_SESSION['username'];
$sql = "SELECT name FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($displayName);
$stmt->fetch();
$stmt->close();

// Fallback if the name is not set in the database
$displayName = $displayName ?: $username;

include 'header.php';
?>

            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Dashboard</title>
                <link rel="stylesheet" href="style.css">
            </head>

        <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #ffffff;
            background-color: #121212;
            margin: 0;
            padding: 0;
            padding-top: 70px; 
        }

        .container {
            width: 592px;
            max-width: 1000px;
            margin: 53px auto;
            text-align: center;
            padding: 8px ;
        }

        .header {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            height: 70px; 
            background-color: #1e1e1e;
            color: #ffffff;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            box-sizing: border-box; 
        }

        .header .logo h1 {
            font-size: 1.5rem;
        }

        .header .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .header .nav-links li {
            display: inline-block;
            white-space: nowrap; /* Prevents links from breaking into multiple lines */
        }

        .header .nav-links li a {
            color: #ffffff;
            text-decoration: none;
            font-size: 1rem;
            padding: 10px;
            transition: color 0.3s ease;
        }

        .header .nav-links li a:hover {
            color: #007bff;
        }
        </style>

<body>
    <div class="container">
        <h2 class="login-title">Welcome, <?php echo htmlspecialchars($displayName); ?>!</h2>
        <div class="card-container">
            <div class="card">
                <h3>Create Contact</h3>
                <p>Add a new contact to your list.</p>
                <a href="create_contact.php" class="btn">Create</a>
            </div>
            <div class="card">
                <h3>Show Contacts</h3>
                <p>View your saved contacts.</p>
                <a href="show_contact.php" class="btn">View</a>
            </div>
            <div class="card">
                <h3>Manage Contacts</h3>
                <p>Edit or delete contacts.</p>
                <a href="manage_contact.php" class="btn">Manage</a>
            </div>
            <div class="card">
                <h3>Logout</h3>
                <p>Log out of your account.</p>
                <a href="logout.php" class="btn">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
