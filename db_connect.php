<?php
// Database credentials
$servername = "localhost";
$username = "admin_master"; 
$password = "newpass123";     // Password set to 12345
$dbname = "bookstore_db";

// Create connection. The 5th parameter (3307) specifies the custom port.
$conn = new mysqli($servername, $username, $password, $dbname, 3307);

// Check connection and stop if it fails
if ($conn->connect_error) {
    // Show a more detailed error message if the connection fails again
    die("Database Connection failed (Port 3307): " . $conn->connect_error);
}
?>