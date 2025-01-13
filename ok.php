<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Session</title>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/hand-pose-detection"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slide-up {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-fade-in {
            animation: fade-in 0.5s ease-out;
        }

        .animate-slide-up {
            animation: slide-up 0.7s ease-out;
        }
    </style>
</head>

<body class="bg-gray-100">
    <?php include ('./component/sidebar.php');?>
    <main class="sm:ml-64 p-4">

        <div class="container mx-auto my-8 px-4">
            <!-- Header Section with Animation -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 animate-fade-in">
                

                <!-- Floating Timer -->
                <div class="fixed top-4 right-4 z-50 bg-white rounded-lg shadow-lg p-4
                    transform transition-all duration-300 hover:scale-105">
                    <div class="flex flex-col items-center">
                        <span id="timer" class="text-3xl font-bold text-indigo-600">00:00</span>
                        <span id="status" class="text-sm text-gray-600 mt-1">Status: Waiting</span>
                    </div>
                </div>
            </div>

            <!-- Video Feed Container with Animation -->
            <div class="relative rounded-xl overflow-hidden shadow-2xl 
                transform transition-all duration-500 hover:shadow-3xl
                animate-slide-up">
                <video id="video" class="w-full max-w-4xl mx-auto rounded-xl" height="480" autoplay></video>
                <canvas id="canvas" class="absolute top-0 left-0 w-full h-full 
                       bg-transparent pointer-events-none"></canvas>

                <!-- Overlay Status Indicator -->
                <div class="absolute top-4 left-4 bg-black/50 text-white px-3 py-1 rounded-full
                    transform transition-all duration-300">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 rounded-full animate-pulse" id="statusDot"></div>
                        <span class="text-sm" id="recordingStatus">Ready</span>
                    </div>
                </div>
            </div>

            <!-- Controls Section -->
            <div class="mt-8 flex justify-center space-x-4 animate-fade-in">
                <button id="stopSessionBtn" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-3 
                       rounded-lg transform transition-all duration-300 hover:scale-105
                       hover:shadow-lg flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>End Session</span>
                </button>
            </div>
            
            <div class="mt-8 flex justify-center space-x-4 animate-fade-in">
                <a id="stopSessionBtn" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-3 
                       rounded-lg transform transition-all duration-300 hover:scale-105
                       hover:shadow-lg flex items-center space-x-2" href="dashboard.php">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Cancel Session</span>
                </a>
            </div>

            <!-- Alert Box -->
            <div id="soundAlert" class="hidden fixed bottom-4 right-4 bg-yellow-100 border-l-4 border-yellow-500 
                p-4 rounded-lg shadow-lg transform transition-all duration-300 
                animate-bounce">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="font-semibold text-yellow-800">Don't waste your time!</p>
                </div>
            </div>

            <!-- Hidden Form -->
            <form id="sessionForm" action="record.php" method="POST" class="hidden">
                <input type="hidden" id="totalTimeInput" name="totalTime">
                <input type="hidden" id="subjectInput" name="subject">
                <input type="hidden" name="mode" value="writing">
                <button type="submit" id="submitBtn"></button>
            </form>
        </div>
    </main>

    <script>
        const videoElement = document.getElementById("video");
        const timerElement = document.getElementById("timer");
        const statusElement = document.getElementById("status");
        const soundAlert = document.getElementById("soundAlert");
        const canvasElement = document.getElementById("canvas");
        const canvasContext = canvasElement.getContext("2d");

        // Correct reference for stopSessionBtn
        const stopSessionBtn = document.getElementById('stopSessionBtn');
        const sessionForm = document.getElementById('sessionForm');
        const totalTimeInput = document.getElementById('totalTimeInput');
        const subjectInput = document.getElementById('subjectInput');

        let timer = 0;
        let lastActivityTime = Date.now();
        let writing = false;
        let handDetector;
        let detectionInterval;
        let timerInterval;

        // Function to request camera permission and setup the camera stream
        async function setupCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                videoElement.srcObject = stream;
                setupHandDetector();  // Once camera is ready, setup hand detector
            } catch (err) {
                alert("Camera permission denied or camera not available.");
            }
        }

        // Setup hand pose detector
        async function setupHandDetector() {
            handDetector = await handPoseDetection.createDetector(handPoseDetection.SupportedModels.MediaPipeHands, {
                runtime: 'tfjs',
            });
            startWritingMode();
        }

        // Start writing mode (hand pose detection)
        async function startWritingMode() {
            detectionInterval = setInterval(async () => {
                const poses = await handDetector.estimateHands(videoElement);
                const now = Date.now();

                // Draw detection results on the canvas
                drawDetectionResults(poses);

                if (poses.length > 0) {
                    // If we detect hands, treat as writing activity
                    lastActivityTime = now;
                    if (!writing) {
                        writing = true;
                        statusElement.textContent = "Status: Writing";  // Update status to Writing
                        resumeTimer();  // Start or resume the timer when writing starts
                    }
                } else {
                    // If no hands detected, check for inactivity
                    if (writing && now - lastActivityTime > 1000) {  // 1 second inactivity
                        writing = false;
                        statusElement.textContent = "Status: Not Writing";  // Update status to Not Writing
                        soundAlert.classList.remove("hidden");
                        playSoundAlert();
                        pauseTimer();  // Pause the timer when not writing
                    }
                }
            }, 1000);  // Run detection every 1 second
        }

        // Draw detection results on the canvas
        function drawDetectionResults(poses) {
            canvasContext.clearRect(0, 0, canvasElement.width, canvasElement.height);  // Clear previous drawing

            if (poses.length > 0) {
                poses.forEach((pose) => {
                    pose.keypoints.forEach((keypoint) => {
                        if (keypoint.score > 0.5) {  // Only draw if the keypoint is detected with a good score
                            canvasContext.beginPath();
                            canvasContext.arc(keypoint.x, keypoint.y, 5, 0, 2 * Math.PI);
                            canvasContext.fillStyle = "red";
                            canvasContext.fill();
                        }
                    });
                });
            }
        }

        // Timer update function
        function updateTimer() {
            const currentTime = Date.now();
            timer += (currentTime - lastActivityTime) / 1000;  // Increment timer
            const minutes = Math.floor(timer / 60);
            const seconds = Math.floor(timer % 60);
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        // Start or resume the timer
        function resumeTimer() {
            if (!timerInterval) {
                timerInterval = setInterval(updateTimer, 1000);  // Update every second
            }
        }

        // Pause the timer
        function pauseTimer() {
            clearInterval(timerInterval);  // Stop the timer interval
            timerInterval = null;  // Reset the interval
        }

        // Play sound alert when time is wasted
        function playSoundAlert() {
            const audio = new Audio('https://rntnotes.xyz/tester/alert.mp3');  // Replace with the correct path to sound file
            audio.play();
            soundAlert.classList.remove("hidden");
            setTimeout(() => soundAlert.classList.add("hidden"), 3000);  // Hide alert after 3 seconds
        }

        // Stop session and submit the form
        stopSessionBtn.addEventListener('click', function () {
            // Get the current timer as total time
            const totalTime = timerElement.textContent;  // Assuming timerElement holds the current time in HH:MM format

            // Retrieve the dynamic subject from PHP and inject it into the JavaScript
            const subject = <?php echo json_encode($_GET['subject']); ?>;  // Output the subject as a JavaScript variable

            // Set the values of the hidden inputs with the current data
            totalTimeInput.value = totalTime;
            subjectInput.value = subject;

            // Submit the form
            sessionForm.submit();
        });

        // Start the camera when the page loads
        setupCamera();
    </script>

</body>

</html>