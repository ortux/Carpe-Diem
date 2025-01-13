<?php
if(!isset($_COOKIE['admin_id'])){
    header("Location: ../../../auth/login.php");
    exit(); // It's a good practice to call exit after a redirect to prevent further code execution.
}
?>
<?php
require '../../../database.php';
$db = $conn;

if(isset($_POST['id'])) {
    $lesson_id = (int)$_POST['id'];
    
    // Get file path before deletion
    $lesson = $db->query("SELECT lesson_file_path FROM lessons WHERE id = $lesson_id")->fetch_assoc();
    $file_path = '../../' . $lesson['lesson_file_path'];
    
    // Delete file if it exists
    if(file_exists($file_path)) {
        unlink($file_path);
    }
    
    // Delete lesson from database
    $db->query("DELETE FROM lessons WHERE id = $lesson_id");
    
    // Return success response
    echo json_encode(['success' => true]);
}