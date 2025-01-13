<?php
if(!isset($_COOKIE['admin_id'])){
    header("Location: ../../../auth/login.php");
    exit(); // It's a good practice to call exit after a redirect to prevent further code execution.
}
?>
<?php
require '../../../database.php';

$course_id = $_GET['course_id'];
$course = $conn->query("SELECT * FROM notes WHERE id = $course_id")->fetch_assoc();

// Pagination setup
$items_per_page = 8;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Get total lessons count
$total_lessons = $conn->query("SELECT COUNT(*) as count FROM lessons WHERE note_id = $course_id")->fetch_assoc()['count'];
$total_pages = ceil($total_lessons / $items_per_page);

// Get lessons for current page
$lessons = $conn->query("SELECT * FROM lessons WHERE note_id = $course_id ORDER BY created_at DESC LIMIT $offset, $items_per_page");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lessons</title>
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
                        <a href="../../dashboard.php"
                            class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="./crud/user_management.php"
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
                                <a href="../course_management.php"
                                    class="flex items-center pl-14 py-2 hover:bg-gray-700 transition-colors">
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
                        <a href="webmail.rntnotes.xyz"
                            class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            View Email
                        </a>
                    </li>
                    <li>
                        <a href="../../../manager.php"
                            class="flex items-center px-6 py-3 hover:bg-gray-700 transition-colors">
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
                        <a href="../../../auth/logout.php"
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
            <div class="mb-6">
                <a href="../course_management.php" class="text-blue-600 hover:text-blue-800">← Back to Courses</a>
            </div>

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        <?php echo $course['title']; ?>
                    </h1>
                    <p class="text-gray-600">Manage Lessons</p>
                </div>
                <button onclick="showAddLessonModal()"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    Add New Lesson
                </button>
            </div>

            <!-- Lessons List -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Title
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                File
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date Added
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while($lesson = $lessons->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo $lesson['lesson_title']; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="../../../<?php echo $lesson['lesson_file_path']; ?>"
                                    class="text-blue-600 hover:text-blue-900" target="_blank">
                                    View File
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($lesson['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="edit_leasson.php?id=<?php echo $lesson['id']; ?>"
                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                    Edit
                                </a>
                                <button onclick="deleteLesson(<?php echo $lesson['id']; ?>)"
                                    class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="px-6 py-4 bg-white border-t">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Showing
                            <?php echo $offset + 1; ?> to
                            <?php echo min($offset + $items_per_page, $total_lessons); ?>
                            of
                            <?php echo $total_lessons; ?> lessons
                        </div>

                        <div class="flex space-x-2">
                            <?php if($total_pages > 1): ?>
                            <?php if($page > 1): ?>
                            <a href="?course_id=<?php echo $course_id; ?>&page=<?php echo ($page - 1); ?>"
                                class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                Previous
                            </a>
                            <?php endif; ?>

                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?course_id=<?php echo $course_id; ?>&page=<?php echo $i; ?>"
                                class="px-3 py-1 <?php echo $i === $page ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700'; ?> rounded-md hover:bg-blue-600 hover:text-white">
                                <?php echo $i; ?>
                            </a>
                            <?php endfor; ?>

                            <?php if($page < $total_pages): ?>
                            <a href="?course_id=<?php echo $course_id; ?>&page=<?php echo ($page + 1); ?>"
                                class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                Next
                            </a>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Lesson Modal -->
        <div id="addLessonModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Add New Lesson</h3>
                    <form action="add_lesson.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="note_id" value="<?php echo $course_id; ?>">

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Lesson Title
                            </label>
                            <input type="text" name="lesson_title" required
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Lesson File
                            </label>
                            <input type="file" name="lesson_file" required
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="closeAddLessonModal()"
                                class="px-4 py-2 text-gray-500 hover:text-gray-700">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                Add Lesson
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function showAddLessonModal() {
                document.getElementById('addLessonModal').classList.remove('hidden');
            }

            function closeAddLessonModal() {
                document.getElementById('addLessonModal').classList.add('hidden');
            }

            function deleteLesson(id) {
                if (confirm('Are you sure you want to delete this lesson?')) {
                    fetch('delete_lesson.php', {
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