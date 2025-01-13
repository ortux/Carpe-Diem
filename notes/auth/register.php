<?php
session_start();
require '../database.php'; // Include your database connection file

// Function to send verification email
function sendVerificationEmail($to, $verification_link) {
    $subject = "Verify your email address";
    $message = "
    <html>
    <head>
        <title>Email Verification</title>
    </head>
    <body>
        <h2>Verify Your Email</h2>
        <p>Please click the link below to verify your email address:</p>
        <a href='$verification_link'>$verification_link</a>
    </body>
    </html>
    ";

    // Set content-type header for sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    
    // Additional headers
    $headers .= "From: noreply@rntnotes.xyz" . "\r\n"; // Replace with your sender email address

    // Send the email
    return mail($to, $subject, $message, $headers);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $class = $_POST['class'];
    $verification_token = bin2hex(random_bytes(16));

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<p class='text-red-500'>Email already exists. Please use a different email.</p>";
    } else {
        // Prepare and execute the insert statement
        $stmt = $conn->prepare("INSERT INTO users (email, password, class, verification_token) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $password, $class, $verification_token);

        if ($stmt->execute()) {
            // Send verification email
            $verification_link = "http://rntnotes.xyz/new/verify.php?token=$verification_token"; // Update with your domain
            if (sendVerificationEmail($email, $verification_link)) {
                echo "<p class='text-green-500'>Registration successful! Please check your email to verify your account.</p>";
            } else {
                echo "<p class='text-red-500'>Registration successful, but failed to send verification email.</p>";
            }
        } else {
            echo "<p class='text-red-500'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded shadow-md w-80">
        <h2 class="mb-4 text-lg font-bold">Register</h2>
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Class</label>
                <select name="class" class="w-full p-2 border rounded" required>
                    <option value="">Select your class</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Register</button>
        </form>
    </div>
</body>
</html>
