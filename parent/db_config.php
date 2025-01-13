<?php
// Database configuration
$servername = "localhost"; // Typically localhost for local development
$username = ""; // Your database username
$password = ""; // Your database password
$dbname = ""; // Name of the database

// Create connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // If connection fails, stop execution and output the error
    die("Connection failed: " . $conn->connect_error);
}

// If connection is successful, you can proceed with your queries

?>
