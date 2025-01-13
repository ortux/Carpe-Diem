<?php
session_start(); // Starting session to manage session if needed

require '../database.php'; // Include your database connection file
require '../services/mail_service.php'; // Include your mail service

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, password, class, is_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $class, $is_verified);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Check if email is verified
            if ($is_verified) {
                // Store user ID and class in cookies
                setcookie('user_id', $id, time() + (31536000), "/"); // 1 year
                setcookie('class', $class, time() + (31536000), "/"); // 1 year

                // Redirect to a protected page
                header("Location: index.php");
                exit;
            } else {
                echo "<script>alert('Your email is not verified. Please check your email for the verification link.');</script>";
                echo "<p>Please check your email for the verification link. If you didn't receive it, you can <a href='?resend=true'>click here</a> to resend the verification email.</p>";
            }
        } else {
            echo "<p>Invalid password.</p>";
        }
    } else {
        // Check in admin table if the user is not found in the users table
        $stmt = $conn->prepare("SELECT id, password FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            // Verify admin password
            if (password_verify($password, $hashed_password)) {
                // Store admin ID in cookies
                setcookie('admin_id', $id, time() + (31536000), "/"); // 1 year

                // Redirect to admin dashboard
                header("Location: ../dash/dashboard.php");
                exit;
            } else {
                echo "<p>Invalid password.</p>";
            }
        } else {
            echo "<p>No user or admin found with that email address.</p>";
        }
    }

    $stmt->close();
}

// Handle resending verification email
if (isset($_GET['resend']) && $_GET['resend'] == 'true') {
    // Resend the verification email logic
    $verification_token = bin2hex(random_bytes(16)); // Generate a new token
    $stmt = $conn->prepare("UPDATE users SET verification_token = ? WHERE email = ?");
    $stmt->bind_param("ss", $verification_token, $email);

    if ($stmt->execute()) {
        // Send the verification email
        $verification_link = "http://rntnotes.xyz/new/verifier/verify.php?token=$verification_token"; // Update with your domain
        if (sendVerificationEmail($email, $verification_link)) {
            echo "<p>A new verification email has been sent. Please check your inbox.</p>";
        } else {
            echo "<p>Failed to send verification email.</p>";
        }
    } else {
        echo "<p>Error updating verification token: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded shadow-md w-80">
        <h2 class="mb-4 text-lg font-bold">Login</h2>
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full p-2 border rounded" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Login</button>
        </form>
        <div class="mt-4 text-center">
            <a href="register.php" class="text-blue-500">Don't have an account? Register here.</a>
        </div>
    </div>
</body>
</html>
