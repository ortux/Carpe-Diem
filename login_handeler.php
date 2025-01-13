<?php
// Include the database configuration
require 'db_config.php';

// Check if the form is submitted and if the name is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']); // Sanitize the name input
    $code = rand(1000, 9999);

    // Validate name
    if (empty($name)) {
        echo "Name cannot be empty.";
        exit;
    }

    // Connect to the database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the name already exists in the database
    $query = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $query->bind_param("s", $name);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $usercode = $row['code'];
        // User exists; set a permanent cookie
        setcookie("username", $name, time() + (10 * 365 * 24 * 60 * 60), "/"); // 10 years
        setcookie("usercode", $usercode, time() + (10 * 365 * 24 * 60 * 60), "/");
        header("Location: dashboard.php");
    } else {
        // Insert the name into the database
        $insert = $conn->prepare("INSERT INTO users (username, code) VALUES (?, ?)");
        $insert->bind_param("si", $name, $code);
        if ($insert->execute()) {
            // Set a permanent cookie
            setcookie("username", $name, time() + (10 * 365 * 24 * 60 * 60), "/"); // 10 years
            setcookie("usercode", $code, time() + (10 * 365 * 24 * 60 * 60), "/"); // 10 years
            header("Location: dashboard.php");
        } else {
            echo "Error: " . $conn->error;
        }
        $insert->close();
    }

    // Close the connection
    $query->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
