<?php
if(!isset($_COOKIE['admin_id'])){
    header("Location: ../../../auth/login.php");
    exit(); // It's a good practice to call exit after a redirect to prevent further code execution.
}
?>
<?php
require '../../../database.php';
$db = $conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note_id = (int)$_POST['note_id'];
    $lesson_title = $db->real_escape_string($_POST['lesson_title']);
    
    // Handle file upload
    if (isset($_FILES['lesson_file']) && $_FILES['lesson_file']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['lesson_file']['name'];
        $file_tmp = $_FILES['lesson_file']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Generate unique filename
        $new_filename = $lesson_title . '.' . $file_ext;
        $upload_path = '../../../uploads/lessons/';
        $db_file_path = 'uploads/lessons/' . $new_filename;
        
        // Move file to upload directory
        if (move_uploaded_file($file_tmp, $upload_path . $new_filename)) {
            // Insert into database
            $query = "INSERT INTO lessons (note_id, lesson_title, lesson_file_path) 
                     VALUES ($note_id, '$lesson_title', '$db_file_path')";
            
            if ($db->query($query)) {
                header("Location: ./lesson.php?course_id=" . $note_id);
                exit();
            }
        }
    }
}

// Redirect back if something fails
header("Location: lesson.php?course_id=" . $note_id . "&error=1");
exit();
?>