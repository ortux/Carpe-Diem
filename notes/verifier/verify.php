<?php
session_start();
require '../database.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify the token and update the user's status
    $stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id);
        $stmt->fetch();

        // Update user to set is_verified to 1
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo "Your email has been verified!";
    } else {
        echo "Invalid token!";
    }
    $stmt->close();
}
?>
