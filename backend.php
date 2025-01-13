<?php
// Set up database connection
$host = 'localhost';
$dbname = 'vcluygjj_study_app';
$username = 'vcluygjj_attendance';  // Change to your MySQL username
$password = 'om2002lopa';      // Change to your MySQL password
$dsn = "mysql:host=$host;dbname=$dbname;charset=UTF8";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to check if user exists
function userExists($username) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to insert a new user
function addUser($username) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO users (username) VALUES (?)");
    $stmt->execute([$username]);
}

// Function to insert or update study session
function saveStudySession($username, $duration) {
    global $pdo;

    // Check if the user has a session today
    $stmt = $pdo->prepare("SELECT * FROM study_sessions WHERE user_name = ? AND DATE(session_date) = CURDATE()");
    $stmt->execute([$username]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($session) {
        // Update the session if it exists today
        $stmt = $pdo->prepare("UPDATE study_sessions SET duration = duration + ? WHERE id = ?");
        $stmt->execute([$duration, $session['id']]);
    } else {
        // Insert a new session if none exists for today
        $stmt = $pdo->prepare("INSERT INTO study_sessions (user_name, session_date, duration) VALUES (?, NOW(), ?)");
        $stmt->execute([$username, $duration]);
    }
}

// Function to calculate the streak
function getUserStreak($username) {
    global $pdo;

    // Get the last study date for the user
    $stmt = $pdo->prepare("SELECT session_date FROM study_sessions WHERE user_name = ? ORDER BY session_date DESC LIMIT 1");
    $stmt->execute([$username]);
    $lastSession = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lastSession) {
        return 0;
    }

    // Get the last streak record
    $stmt = $pdo->prepare("SELECT streak, last_study_date FROM user_streaks WHERE user_name = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$username]);
    $streakData = $stmt->fetch(PDO::FETCH_ASSOC);

    $today = new DateTime();
    $lastStudyDate = new DateTime($lastSession['session_date']);
    $diff = $lastStudyDate->diff($today)->days;

    // Calculate streak
    if ($streakData) {
        if ($diff == 1) {
            $streak = $streakData['streak'] + 1;  // Increase streak by 1 if it's a consecutive day
        } else if ($diff > 1) {
            $streak = 1;  // Reset streak if it's not consecutive
        } else {
            $streak = $streakData['streak'];  // If no new day passed, keep the streak the same
        }
    } else {
        $streak = 1;
    }

    // Update or insert streak record
    if ($streakData) {
        $stmt = $pdo->prepare("UPDATE user_streaks SET streak = ?, last_study_date = CURDATE() WHERE user_name = ?");
        $stmt->execute([$streak, $username]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO user_streaks (user_name, streak, last_study_date) VALUES (?, ?, CURDATE())");
        $stmt->execute([$username, $streak]);
    }

    return $streak;
}

// Fetch weekly study data (last 7 days)
function getWeeklyData($username) {
    global $pdo;
    $weeklyData = array_fill(0, 7, 0);  // Initialize array for weekly data (7 days)

    $stmt = $pdo->prepare("SELECT session_date, duration FROM study_sessions WHERE user_name = ? AND session_date >= CURDATE() - INTERVAL 7 DAY");
    $stmt->execute([$username]);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($sessions as $session) {
        $dayOfWeek = (new DateTime($session['session_date']))->format('w');  // Get day of week (0 = Sunday, 6 = Saturday)
        $weeklyData[$dayOfWeek] += floor($session['duration'] / 3600);  // Convert seconds to hours
    }

    return $weeklyData;
}

// Handling AJAX requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'])) {
        $username = $_POST['username'];

        // Check if user exists
        if (!userExists($username)) {
            addUser($username);  // Add new user if not exists
        }

        $streak = getUserStreak($username);
        $weeklyData = getWeeklyData($username);

        // Respond with user streak and weekly data
        echo json_encode([
            'streak' => $streak,
            'weeklyData' => $weeklyData,
        ]);
    } elseif (isset($_POST['duration']) && isset($_POST['username'])) {
        $duration = (int)$_POST['duration'];  // Duration in seconds
        $username = $_POST['username'];

        // Save study session and update streak
        saveStudySession($username, $duration);
        $streak = getUserStreak($username);
        $weeklyData = getWeeklyData($username);

        // Respond with updated streak and weekly data
        echo json_encode([
            'streak' => $streak,
            'weeklyData' => $weeklyData,
        ]);
    }
}
