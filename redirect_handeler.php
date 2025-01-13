<?php
// Check if the form has been submitted via GET and the required fields are set
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type']) && isset($_GET['subject'])) {
    // Retrieve the type and subject from the query parameters
    $lessonType = $_GET['type'];
    $subject = $_GET['subject'];

    // Perform any necessary validation or sanitation
    $lessonType = htmlspecialchars($lessonType);  // Sanitize lesson type
    $subject = htmlspecialchars($subject);        // Sanitize subject

    // Example logic to redirect based on lesson type
    if ($lessonType == 'reading') {
        // If the lesson type is 'Physics' (Reading), redirect to a reading page
        header("Location: dashboard.php?subject=" . urlencode($subject));
        exit();
    } elseif ($lessonType == 'writing') {
        // If the lesson type is 'Chemistry' (Writing), redirect to a writing page
        header("Location: ok.php?subject=" . urlencode($subject));
        exit();
    } else {
        // If neither 'Physics' nor 'Chemistry', handle the error
        echo "<p>Invalid lesson type selected.</p>";
    }
} else {
    // Handle cases where the required parameters are not set
    echo "<p>Error: Missing parameters.</p>";
}
?>
