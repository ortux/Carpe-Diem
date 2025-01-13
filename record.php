<?php
// Connect to MySQL Database (adjust credentials as needed)
$servername = "localhost";
$username = "";
$password = ""; // Enter your password
$dbname = "";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if POST request contains required data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['totalTime']) && isset($_POST['subject'])) {
    // Get the total time in HH:MM:SS format and the subject
    $totalTime = $_POST['totalTime'];
    $subject = $_POST['subject'];
    $mode = $_POST['mode'];
    $distractionNo = isset($_POST['distraction_no']) ? $_POST['distraction_no'] : 0;  // Number of distractions
    $distractionTime = isset($_POST['distraction_time']) ? $_POST['distraction_time'] : 0;

    // Get the current user's name (this could be dynamic, e.g., from a session or request)
    $user_name = htmlspecialchars($_COOKIE['username']); // You can adjust this if you want dynamic user data, e.g., from a session

    // Insert new study session into the study_sessions table
    $session_date = date('Y-m-d H:i:s'); // Current date and time for the session
    $stmt = $conn->prepare("INSERT INTO study_sessions (user_name, session_date, duration, subject, mode, distraction_no, distraction_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssii", $user_name, $session_date, $totalTime, $subject, $mode, $distractionNo, $distractionTime);
    $stmt->execute();
    setcookie('distraction_time', '', time() - 3600, '/'); // Expire the 'distraction_time' cookie
    setcookie('distraction_count', '', time() - 3600, '/');
    unset($_COOKIE['distraction_time']);
    unset($_COOKIE['distraction_count']);



    // Update the user's streak in the user_streaks table
    // Check if the user already has a streak record
    $streakStmt = $conn->prepare("SELECT streak, last_study_date FROM user_streaks WHERE user_name = ?");
    $streakStmt->bind_param("s", $user_name);
    $streakStmt->execute();
    $streakStmt->store_result();

    if ($streakStmt->num_rows > 0) {
        // User has a streak record, so update it
        $streakStmt->bind_result($streak, $last_study_date);
        $streakStmt->fetch();

        $last_study_date = date('Y-m-d'); // Set the current date as last study date

        // If the user studied the previous day, increment the streak, otherwise reset to 1
        if ($last_study_date === date('Y-m-d', strtotime("-1 day"))) {
            $newStreak = $streak + 1;
        } else {
            $newStreak = 1; // If the user missed a day, reset streak to 1
        }

        // Update the user's streak and last study date
        $updateStreakStmt = $conn->prepare("UPDATE user_streaks SET streak = ?, last_study_date = ? WHERE user_name = ?");
        $updateStreakStmt->bind_param("iss", $newStreak, $last_study_date, $user_name);
        $updateStreakStmt->execute();
    } else {
        // No streak record found, insert a new record for the user
        $initialStreak = 1; // Start with a streak of 1
        $lastStudyDate = date('Y-m-d');
        $insertStreakStmt = $conn->prepare("INSERT INTO user_streaks (user_name, streak, last_study_date) VALUES (?, ?, ?)");
        $insertStreakStmt->bind_param("sis", $user_name, $initialStreak, $lastStudyDate);
        $insertStreakStmt->execute();
    }

    // Close all prepared statements
    $stmt->close();
    $streakStmt->close();
    $conn->close();

    header("Location: dashboard.php");
} else {
    header("Location: dashboard.php");
}
?>
