<?php
// Assuming you have already established a database connection using mysqli
include 'db_config.php';

// Get the student code from the request (POST or GET)
$student_code = $_POST['student_code'] ?? null;

// Check if student code is provided
if ($student_code) {
    // Prepare SQL query to fetch user by student code
    $query = $conn->prepare("SELECT * FROM users WHERE code = ?");
    $query->bind_param("i", $student_code);  // Use 'i' for integer binding
    $query->execute();
    $result = $query->get_result();
    
    // Check if any user was found
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $student_name = $user['username']; // Get the student's name
        
        // Set a cookie with the student's name (expires in 1 hour)
        setcookie("student_name", $student_name, time() + 3600, "/");  // 3600 seconds = 1 hour

        // You can also set additional cookies for session or code if needed
        setcookie("student_code", $student_code, time() + 3600, "/");

        // Redirect or display a success message
        header("Location: dashboard.php");
    } else {
        // If no user found with the given code, show an error
        echo "Invalid student code.";
    }
} else {
    echo "Student code is required.";
}
?>