<?php
// database.php

// Database configuration
$host = 'localhost'; // Database host
$db_name = 'vcluygjj_new_website'; // Database name
$username = 'vcluygjj_attendance'; // Database username
$password = 'om2002lopa'; // Database password

// Create a connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
