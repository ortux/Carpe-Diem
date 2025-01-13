<?php
require '../dbconfig.php';

$classesStmt = $pdo->query("SELECT DISTINCT class_name FROM notes");
$classes = $classesStmt->fetchAll(PDO::FETCH_COLUMN);
$selected_class = $_GET['class'] ?? '';

if ($selected_class) {
    $stmt = $pdo->prepare("SELECT id, title, description FROM notes WHERE class_name = ? ORDER BY created_at DESC");
    $stmt->execute([$selected_class]);
} else {
    $stmt = $pdo->query("SELECT id, title, description FROM notes ORDER BY created_at DESC");
}
$notes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <title>Notes and Lessons</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }
    </style>
</head>

<body
    class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 transition-all duration-300">
    <!-- Enhanced Navbar with Gradient -->
    <?php include ('../../component/sidebar.php');?>

    <main class="sm:ml-64 p-4">
        <h1
            class="text-4xl font-bold text-center mb-12 text-white bg-gradient-to-r from-blue-500 to-indigo-600 dark:from-blue-700 dark:to-indigo-800 rounded-lg p-4 fade-in">
            Notes and Lessons
        </h1>

        <!-- Enhanced Filter -->
        <form method="GET" class="max-w-xl mx-auto mb-12 fade-in" style="animation-delay: 0.2s">
            <div class="relative">
                <select name="class" id="class"
                    class="w-full px-4 py-3 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent appearance-none transition-all duration-300">
                    <option value="">All Classes</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= htmlspecialchars($class) ?>" <?= ($class == $selected_class) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($class) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit"
                    class="absolute right-0 top-0 h-full px-4 bg-blue-500 text-white rounded-r-lg hover:bg-blue-600 transition-colors duration-300">
                    Filter
                </button>
            </div>
        </form>

        <!-- Enhanced Notes Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if ($notes): ?>
                <?php foreach ($notes as $index => $note): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300 overflow-hidden fade-in"
                        style="animation-delay: <?= 0.1 * ($index + 1) ?>s">
                        <div class="p-6">
                            <h2 class="text-xl font-bold mb-3 text-gray-800 dark:text-white">
                                <?= htmlspecialchars($note['title']) ?>
                            </h2>
                            <p class="text-gray-600 dark:text-gray-300 mb-6 line-clamp-3">
                                <?= nl2br(htmlspecialchars($note['description'])) ?>
                            </p>
                            <a href="./view_lessons.php?note_id=<?= $note['id'] ?>"
                                class="inline-block w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-3 rounded-lg transform hover:scale-105 transition-all duration-300">
                                View Lessons
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center text-gray-600 dark:text-gray-300 fade-in">
                    No notes available for this class.
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;

        themeToggle.addEventListener('click', () => {
            html.classList.toggle('dark');
            document.body.classList.toggle('dark');
        });
    </script>
    <script>
        // Theme toggle functionality
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        const themeToggleBtn = document.getElementById('theme-toggle');
        const mobileMenuBtn = document.querySelector('[aria-controls="navbar-menu"]');
        const mobileMenu = document.getElementById('navbar-menu');

        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
            document.documentElement.classList.add('dark');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
            document.documentElement.classList.remove('dark');
        }

        themeToggleBtn.addEventListener('click', function () {
            // Toggle icons
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // If is set in localStorage
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });

        // Mobile menu toggle
        mobileMenuBtn.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>

</html>