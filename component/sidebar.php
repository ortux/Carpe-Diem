<?php
$code = $_COOKIE['usercode'];
?>
<aside id="default-sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-screen bg-gradient-to-b from-blue-500 to-blue-800 text-white shadow-lg sm:translate-x-0 transition-transform"
        :class="{ '-translate-x-full': !sidebarOpen }" aria-label="Sidebar">
        <div class="h-full px-4 py-6 overflow-y-auto">
            <!-- Logo Section -->
            <div class="flex flex-col items-center justify-center mb-8">
                <img src="https://rntnotes.xyz/website/images/61f4bc01-a502-491d-9454-d0364bd5af54.png" alt="Logo"
                    class="h-14 w-auto mb-3 rounded-lg shadow-md">
                <h1 class="text-2xl font-bold">AI TUTOR</h1>
                <h5 class="text-sm text-gray-200 mt-1">Hello,
                    <?= isset($_COOKIE['username']) ? htmlspecialchars($_COOKIE['username']) : 'Guest'; ?>!</h5>
            </div>

            <!-- Main Links Section -->
            <ul class="space-y-4">
                <p class="text-sm text-gray-300 uppercase font-semibold mb-3">Main Links</p>
                <li>
                    <a href="dashboard.php"
                        class="flex items-center p-3 bg-blue-600 rounded-lg hover:bg-blue-700 transition-all shadow-sm">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                            <path
                                d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                            <path
                                d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                        </svg>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="./notes/viewer/view_notes.php?class="
                        class="flex items-center p-3 text-gray-200 rounded-lg hover:bg-blue-600 hover:text-white transition-all">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                            <path
                                d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                            <path
                                d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                        </svg>
                        <span class="ml-3">Study Material</span>
                    </a>
                </li>
                
                <div class="mt-8">
                    <p class="text-sm text-gray-300 uppercase font-semibold mb-3">Artificial Intelligence</p>
                    <ul class="space-y-4">
                        <li>
                            <a href="./ai/aiquestion.php"
                                class="flex items-center p-3 text-gray-200 rounded-lg hover:bg-blue-600 hover:text-white transition-all">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 22 21">
                                    <path
                                        d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                    <path
                                        d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                                </svg>
                                <span class=" ml-3">AI BASED PRACTICE QUESTION (ADVANCED)</span>
                            </a>
                        </li>
                        <li>
                            <a href="./ai/diagramunderstander.php"
                                class="flex items-center p-3 text-gray-200 rounded-lg hover:bg-blue-600 hover:text-white transition-all">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 22 21">
                                    <path
                                        d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                    <path
                                        d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                                </svg>
                                <span class="ml-3">Schematic Genretor</span>
                            </a>
                        </li>
                        <li>
                            <a href="./ai/summarize.php"
                                class="flex items-center p-3 text-gray-200 rounded-lg hover:bg-blue-600 hover:text-white transition-all">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 22 21">
                                    <path
                                        d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                    <path
                                        d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                                </svg>
                                <span class="ml-3">Webpage Sumarizer</span>
                            </a>
                        </li>
                        <li>
                            <a href="./ai/pdfsummarizer.php"
                                class="flex items-center p-3 text-gray-200 rounded-lg hover:bg-blue-600 hover:text-white transition-all">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 22 21">
                                    <path
                                        d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                    <path
                                        d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                                </svg>
                                <span class="ml-3">PDF summarizer</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Settings Section -->
                <div class="mt-8">
                    <p class="text-sm text-gray-300 uppercase font-semibold mb-3">Parent Connect</p>
                    <ul class="space-y-4">
                        <li>
                            <a href="#"
                                class="flex items-center p-3 text-gray-200 rounded-lg hover:bg-blue-600 hover:text-white transition-all">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 22 21">
                                    <path
                                        d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                    <path
                                        d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                                </svg>
                                <span class=" ml-3">USER CODE : <?= $code ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../tester/parent/index.php"
                                class="flex items-center p-3 text-gray-200 rounded-lg hover:bg-blue-600 hover:text-white transition-all">
                                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 22 21">
                                    <path
                                        d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                    <path
                                        d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                                </svg>
                                <span class="ml-3">Parent Link</span>
                            </a>
                        </li>
                    </ul>
                </div>
        </div>
    </aside>