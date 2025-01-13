<?php
if (!isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();  // Always call exit after header redirection to stop further script execution
}else{
    header("Location: dashboard.php");
    exit();
}
?>