<?php
// database.php

// Database configuration
$host = 'localhost'; // Database host
$db_name = ''; // Database name
$username = ''; // Database username
$password = ''; // Database password

// Create a connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
