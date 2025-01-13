<?php
session_start();
require '../dbconfig.php';

// Check if a note ID is provided
$note_id = $_GET['note_id'] ?? null;
if (!$note_id) {
    die("No note selected.");
}

// Handle file download request
if (isset($_GET['download'])) {
    // Check if the user is logged in
    if (!isset($_COOKIE['user_id'])) {
        // If not logged in, redirect to the login page with an error message
        header("Location: ../auth/login.php?error=You must be logged in to download files.");
        exit;
    }

    // Decode the file path
    $file_path = base64_decode($_GET['download']);

    // Make sure the file exists
    if ($file_path && file_exists($file_path)) {
        // Set headers to download the file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        // Clear output buffer before downloading
        ob_clean();
        flush();
        readfile($file_path);
        exit;
    } else {
        die("File not found.");
    }
}

// Fetch note information
$stmt = $pdo->prepare("SELECT * FROM notes WHERE id = ?");
$stmt->execute([$note_id]);
$note = $stmt->fetch();

if (!$note) {
    die("Note not found.");
}

// Fetch lessons related to the note
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE note_id = ? ORDER BY created_at DESC");
$stmt->execute([$note_id]);
$lessons = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lessons for <?= htmlspecialchars($note['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes fadeUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-up {
            animation: fadeUp 0.6s ease-out forwards;
        }

        .lesson-card {
            opacity: 0;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-900 dark:to-indigo-900">
    <?php include ('../../component/sidebar.php');?>
    <!-- Hero Section -->
    <main class="sm:ml-64 p-4">
        <div class="relative overflow-hidden bg-white/30 backdrop-blur-lg shadow-lg mb-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="text-center animate-fade-up">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                        <?= htmlspecialchars($note['title']) ?>
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        <?= htmlspecialchars($note['description']) ?>
                    </p>
                </div>
            </div>
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white/10 pointer-events-none"></div>
        </div>

        <div class="container mx-auto px-4 pb-12">
            <?php if (count($lessons) > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($lessons as $index => $lesson): ?>
                        <div class="lesson-card bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300"
                            style="animation-delay: <?= $index * 0.1 ?>s">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div
                                        class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="ml-4 text-xl font-bold text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($lesson['lesson_title']) ?>
                                    </h3>
                                </div>

                                <a href="../uploads/lessons/index.php?path=<?= base64_encode(htmlspecialchars($lesson['lesson_file_path'])) ?>"
                                    target="_blank"
                                    class="group flex items-center justify-center w-full px-6 py-3 mt-4 text-white bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300">
                                    <span>View Lesson</span>
                                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform duration-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                                <a href="?note_id=<?= $note_id ?>&download=<?= base64_encode($lesson['lesson_file_path']) ?>"
                                    class="group flex items-center justify-center w-full px-6 py-3 mt-4 text-white bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300">
                                    <span>Download Lesson</span>
                                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform duration-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12 animate-fade-up">
                    <div class="text-gray-500 dark:text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 12h.01M12 12h.01"></path>
                        </svg>
                        <p class="text-xl">No lessons found for this note.</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Enhanced Back Button -->
            <div class="mt-12 text-center animate-fade-up" style="animation-delay: 0.5s">
                <a href="view_notes.php"
                    class="inline-flex items-center px-6 py-3 bg-gray-800 dark:bg-gray-700 text-white rounded-lg hover:bg-gray-700 dark:hover:bg-gray-600 transition-all duration-300 transform hover:-translate-y-1">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m4 14h10" />
                    </svg>
                    <span>Back to Notes</span>
                </a>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.lesson-card');
            cards.forEach(card => {
                card.classList.add('animate-fade-up');
            });
        });
    </script>
</body>

</html>