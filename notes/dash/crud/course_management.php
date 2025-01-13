<?php
if(!isset($_COOKIE['admin_id'])){
    header("Location: ../../auth/login.php");
    exit(); // It's a good practice to call exit after a redirect to prevent further code execution.
}
?>
<?php
require '../../database.php';

// Fetch all courses with lesson counts
$courses = $conn->query("
    SELECT notes.*, COUNT(lessons.id) as lesson_count 
    FROM notes 
    LEFT JOIN lessons ON notes.id = lessons.note_id 
    GROUP BY notes.id 
    ORDER BY notes.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses & Lessons</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 flex flex-col">
            <!-- Logo Area -->
            <div class="p-5">
                <h1 class="text-2xl font-bold">Dashboard</h1>
            </div>

            <!-- Main Navigation -->
            <nav class="flex-1">
                <ul class="space-y-2 py-4">
                    <li>
                        <a href="../dashboard.php" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="./user_management.php"
                            class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Manage Users
                        </a>
                    </li>

                    <!-- Manage Notes Dropdown -->
                    <li class="relative">
                        <div
                            class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors cursor-pointer group">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Manage Notes
                            <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <!-- Submenu -->
                        <ul class="bg-gray-900 py-2">
                            <li>
                                <a href="./course_management.php" class="flex items-center pl-14 py-2 hover:bg-gray-700 transition-colors">
                                    Manage Notes
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <!-- Secondary Navigation -->
            <div class="border-t border-gray-700">
                <ul class="space-y-2 py-4">
                    <li>
                        <a href="webmail.rntnotes.xyz" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            View Email
                        </a>
                    </li>
                    <li>
                        <a href="../manager.php" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            File Manager
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </a>
                    </li>
                    <li>
                        <a href="../../auth/logout.php"
                            class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors text-red-400">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Manage Courses & Lessons</h1>
                <a href="./pages/add_course.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    Add New Course
                </a>
            </div>

            <!-- Course Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while($course = $courses->fetch_assoc()): ?>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">
                                <?php echo $course['title']; ?>
                            </h2>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                Class
                                <?php echo $course['class_name']; ?>
                            </span>
                        </div>

                        <p class="text-gray-600 mb-4">
                            <?php echo $course['description']; ?>
                        </p>

                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <?php echo $course['lesson_count']; ?> Lessons
                        </div>

                        <div class="flex justify-between items-center">
                            <div class="space-x-2">
                                <a href="edit_course.php?id=<?php echo $course['id']; ?>"
                                    class="text-blue-600 hover:text-blue-800">Edit</a>
                                <a href="#" onclick="deleteCourse(<?php echo $course['id']; ?>)"
                                    class="text-red-600 hover:text-red-800">Delete</a>
                            </div>
                            <a href="./pages/lesson.php?course_id=<?php echo $course['id']; ?>"
                                class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                                Manage Lessons
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <script>
            function deleteCourse(id) {
                if (confirm('Are you sure you want to delete this course and all its lessons?')) {
                    fetch('delete_course.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=' + id
                    }).then(() => {
                        window.location.reload();
                    });
                }
            }
        </script>
</body>

</html>