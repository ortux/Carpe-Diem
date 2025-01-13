<?php
session_start(); // Start the session

// Check if the user is logged in by checking the cookie
if (isset($_COOKIE['user_id'])) {
    // Clear the cookie by setting its expiration time in the past
    setcookie('user_id', '', time() - 3600, '/'); // Expire the cookie
    setcookie('class', '', time() - 3600, '/');
    // Clear class cookie if needed
}
else{
    setcookie('admin_id', '', time() - 3600, '/');
}

// Optionally, if you are using sessions, you can also unset session variables
$_SESSION = array(); // Unset all session variables

// If you want to destroy the session completely
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}
session_destroy(); // Destroy the session

// Redirect the user to the homepage or login page
header("Location: ../index.php");
exit();
?>
