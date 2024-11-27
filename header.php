<?php
if (!isset($_SESSION)) {
    session_start();
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
?>

<div class="header">
    <div class="logo">
        <h1>Contact Manager</h1>
    </div>
    <nav>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="show_contact.php">Contacts</a></li>
            <li><a href="create_contact.php">Add Contact</a></li>
            <li><a href="manage_contact.php">Manage Contacts</a></li>
        </ul>
        <div class="profile-dropdown">
            <button class="dropdown-btn">
               Howdy, <?php echo htmlspecialchars($displayName); ?>!▼
            </button>
            <div class="dropdown-content">
                <a href="edit_profile.php">Edit Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
</div>
