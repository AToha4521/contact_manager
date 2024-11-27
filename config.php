<?php
// Database configuration
$servername = "localhost"; //  server name from XAMPP
$username = "root"; // XAMPP MySQL username
$password = ""; //  XAMPP MySQL password is empty
$dbname = "contact_db"; // my database name from phpmyadmin

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
