<?php
// Include the database configuration file
include 'db_config.php';
function convertMillisecondsToHHMMSS($milliseconds) {
    // Convert milliseconds to seconds
    $seconds = floor($milliseconds / 1000);
    
    // Calculate hours, minutes, and remaining seconds
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;

    // Return formatted time as HH:MM:SS
    return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}

// Check if the cookie exists for student name
if (isset($_COOKIE['student_name'])) {
    $student_name = $_COOKIE['student_name']; // Get student name from the cookie

    // Prepare a query to get study sessions for the student
    $query = $conn->prepare("SELECT * FROM study_sessions WHERE user_name = ?");
    $query->bind_param("s", $student_name); // Bind student name to the query
    $query->execute();

    $result = $query->get_result();
} else {
    echo "Student name cookie not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Sessions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 min-h-screen text-white">

    <div class="flex">
        <!-- Side Navbar -->
        <?php include('../component/sidebar_parent.php') ?>

        <!-- Main Content Area -->
        <main class="sm:ml-64 p-4">
            <div class="flex-1 p-8">
                <h1 class="text-4xl font-semibold mb-6 text-gray-800">Study Sessions for:
                    <?= htmlspecialchars($student_name) ?></h1>

                <!-- Table for study sessions -->
                <div class="overflow-x-auto bg-white p-6 rounded-lg shadow-lg">
                    <table class="min-w-full text-gray-800">
                        <thead class="bg-gray-700 text-white">
                            <tr>
                                <th class="py-2 px-4 border-b">Session ID</th>
                                <th class="py-2 px-4 border-b">User Name</th>
                                <th class="py-2 px-4 border-b">Session Date</th>
                                <th class="py-2 px-4 border-b">Duration</th>
                                <th class="py-2 px-4 border-b">Subject</th>
                                <th class="py-2 px-4 border-b">Mode</th>
                                <th class="py-2 px-4 border-b">Distraction No</th>
                                <th class="py-2 px-4 border-b">Distraction Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr class="hover:bg-gray-100">
                                        <td class="py-2 px-4 border-b"><?= $row['id'] ?></td>
                                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['user_name']) ?></td>
                                        <td class="py-2 px-4 border-b"><?= $row['session_date'] ?></td>
                                        <td class="py-2 px-4 border-b"><?= $row['duration'] ?></td>
                                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['subject']) ?></td>
                                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['mode']) ?></td>
                                        <td class="py-2 px-4 border-b"><?= $row['distraction_no'] ?></td>
                                        <td class="py-2 px-4 border-b"><?= convertMillisecondsToHHMMSS($row['distraction_time']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="py-2 px-4 text-center border-b">No study sessions found for the
                                        student.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
    </main>

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>