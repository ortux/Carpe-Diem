<?php
if (!isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();  // Always call exit after header redirection to stop further script execution
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Analytics Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Popup overlay */
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Popup content */
        .popup-content {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .popup-content input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .popup-content button {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Bottom Navigation -->


    <!-- Main Content -->
    <!--<div id="popup-overlay" class="popup-overlay">
        <div id="popup-content" class="popup-content">
            <form id="subject-form" method="GET" action="dashboard.php">
                <h2 class="text-lg font-bold mb-4">Enter Topic</h2>
                <input type="text" id="subject-input" name="subject" placeholder="Enter What You Will Be Learning Today"
                    required>
                <button id="submit-btn" class="mt-4">Submit</button>
            </form>
        </div>
    </div>-->
    <?php include('./component/sidebar.php'); ?>
    <main class="sm:ml-64 p-4">
        <h1 class="text-4xl font-bold text-center text-blue-600 mt-10">
            Hello <?= isset($_COOKIE['username']) ? htmlspecialchars($_COOKIE['username']) : 'Guest'; ?>
            <div id="distraction-info"></div>

        </h1>
        <div class="container mx-auto px-4 pb-16 pt-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Today's Stats -->
                <?php
                $subject = isset($_GET['subject']) ? $_GET['subject'] : null;
                ?>
                <div class="bg-white rounded-lg shadow p-6">
                    <?php if ($subject): ?>
                        <h2 class="text-xl font-bold mb-4">Today's Study Session of <?= $_GET['subject'] ?></h2>
                        <div class="text-3xl font-bold text-blue-600" id="todayTime">0:00:00</div>
                        <p class="text-gray-600">Total study time</p>
                        <button id="startLearning" class="mt-4 px-4 py-2 bg-yellow-500 text-white rounded">Start
                            Session</button><br>
                        <button id="stopSessionBtn" class="mt-4 px-4 py-2 bg-red-500 text-white rounded">Stop
                            Session</button><br>
                        <a href="dashboard.php" id="startSessionBtn"
                            class="mt-4 px-4 py-2 bg-green-500 text-white rounded inline-block">Cancel Session</a>
                    <?php else: ?>
                        <h1 class="text-xl font-bold mb-4">Start Learning Only After Pressing Add Topic</h1>
                        <button id="open-popup" class="mt-4 px-4 py-2 bg-yellow-500 text-white rounded">Add
                            Topic</button><br>


                    <?php endif; ?>

                </div>
                <div id="popup-overlay" class="popup-overlay">
                    <!-- Popup content -->
                    <div id="popup-content" class="popup-content">
                        <form id="subject-form" method="GET" action="redirect_handeler.php">
                            <label for="type" class="block text-lg mb-2">Choose The Type Of Lesson:</label>
                            <select name="type" id="type" class="bg-white border p-2 rounded" required>
                                <option value="reading">Reading</option>
                                <option value="writing">Writing</option>
                            </select>
                            <h2 class="text-lg font-bold mb-4">Enter Topic</h2>
                            <input type="text" id="subject-input" name="subject"
                                placeholder="Enter What You Will Be Learning Today" required>
                            <button id="submit-btn" class="mt-4">Submit</button>
                        </form>
                    </div>
                </div>


                <!-- Weekly Stats -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Weekly Progress</h2>
                    <canvas id="weeklyChart"></canvas>
                </div>


                <!-- Study Streak -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Hard Working Days</h2>
                    <div class="text-3xl font-bold text-green-600" id="streak">0 days</div>
                    <p class="text-gray-600">Keep it up!</p>
                </div>

                <div class="bg-white shadow-lg rounded-lg p-6 text-center" id="days-left-card">
                    <h1 class="text-2xl font-bold text-gray-800 mb-4">Days Left Until Your Date</h1>
                    <p class="text-lg text-gray-600" id="days-left-text">Calculating...</p>
                </div>
            </div>
            <div id="shareModal"
                class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg max-w-sm w-full">
                    <h2 class="text-xl font-bold mb-4">Share Your Activities</h2>
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <button class="share-btn" data-platform="whatsapp">
                            <img src="path/to/whatsapp-icon.png" alt="WhatsApp" class="w-12 h-12 mx-auto">
                            <span>WhatsApp</span>
                        </button>
                        <button class="share-btn" data-platform="instagram">
                            <img src="path/to/instagram-icon.png" alt="Instagram" class="w-12 h-12 mx-auto">
                            <span>Instagram</span>
                        </button>
                        <button class="share-btn" data-platform="facebook">
                            <img src="path/to/facebook-icon.png" alt="Facebook" class="w-12 h-12 mx-auto">
                            <span>Facebook</span>
                        </button>
                    </div>
                    <button id="closeModal" class="w-full bg-gray-200 text-gray-800 py-2 rounded-lg">Close</button>
                </div>
            </div>



            <!-- Status Indicator -->
            <div class="fixed top-4 right-4">
                <div id="statusIndicator" class="hidden px-4 py-2 rounded-full text-white font-bold"></div>
            </div><br>
            <div id="activities-section" class="bg-white p-6 rounded-lg shadow-md">
                <h1 class="text-3xl font-extrabold text-gray-800 leading-tight mb-6 border-b-4 border-blue-500 pb-2">
                    Your Activities
                </h1>
                <?php
                $servername = "localhost";
                $username = "vcluygjj_attendance";
                $password = "om2002lopa";
                $dbname = "vcluygjj_study_app";

                $conn = mysqli_connect($servername, $username, $password, $dbname);
                if (!$conn)
                    die("Connection failed: " . mysqli_connect_error());

                if (isset($_COOKIE['username'])) {
                    $userName = $_COOKIE['username'];
                    $query = "SELECT id, session_date, duration, subject, mode FROM study_sessions WHERE user_name = '$userName';";
                    $result = mysqli_query($conn, $query);

                    if (mysqli_num_rows($result) > 0):
                        ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th class="px-4 py-2 font-bold text-gray-600 border-b">Session Date</th>
                                        <th class="px-4 py-2 font-bold text-gray-600 border-b">Duration</th>
                                        <th class="px-4 py-2 font-bold text-gray-600 border-b">Subject</th>
                                        <th class="px-4 py-2 font-bold text-gray-600 border-b">Mode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2"><?= htmlspecialchars($row['session_date']) ?></td>
                                            <td class="px-4 py-2"><?= htmlspecialchars($row['duration']) ?></td>
                                            <td class="px-4 py-2"><?= htmlspecialchars($row['subject']) ?></td>
                                            <td class="px-4 py-2"><?= htmlspecialchars($row['mode']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    else:
                        echo "<p class='text-red-500'>No study sessions found for you start studying.</p>";
                    endif;
                } else {
                    echo "<p class='text-red-500'>No username found in cookies.</p>";
                }
                mysqli_close($conn);
                ?>
            </div>

        </div>
    </main>

    <script>

        let mediaRecorder;
        let isRecording = false;
        let startTime;
        let elapsedPausedTime = 0; // Variable to track the paused time
        let timer;
        let totalStudyTime = parseInt(getCookie('totalStudyTime') || '0');
        let weeklyData = JSON.parse(getCookie('weeklyData') || '[]');
        let streak = parseInt(getCookie('streak') || '0');

        // Initialize weekly chart
        const ctx = document.getElementById('weeklyChart').getContext('2d');
        const weeklyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Study Hours',
                    data: weeklyData,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        async function initializeAudio() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                const audioContext = new AudioContext();
                const audioSource = audioContext.createMediaStreamSource(stream);
                const analyser = audioContext.createAnalyser();

                audioSource.connect(analyser);
                analyser.fftSize = 256;

                const bufferLength = analyser.frequencyBinCount;
                const dataArray = new Uint8Array(bufferLength);

                function checkAudio() {
                    analyser.getByteFrequencyData(dataArray);
                    const average = dataArray.reduce((a, b) => a + b) / bufferLength;

                    if (average > 30) { // Threshold for voice detection
                        if (!isRecording) {
                            startRecording();
                        }
                    } else {
                        if (isRecording) {
                            stopRecording();
                        }
                    }

                    requestAnimationFrame(checkAudio);
                }

                checkAudio();
                showStatus('Listening for voice...', 'bg-blue-500');
            } catch (err) {
                console.error('Error accessing microphone:', err);
                showStatus('Microphone access denied', 'bg-red-500');
            }
        }

        function startRecording() {
            isRecording = true;
            startTime = new Date();
            showStatus('Recording...', 'bg-green-500');
            updateTimer();
        }

        function stopRecording() {
            if (!isRecording) return;

            isRecording = false;
            clearTimeout(timer);

            // Pause the timer, track the paused time
            elapsedPausedTime += (new Date() - startTime); // Add the time spent to the paused time

            const sessionDuration = Math.floor(elapsedPausedTime / 1000); // Convert to seconds
            totalStudyTime += sessionDuration;

            updateWeeklyData(sessionDuration);
            updateStreak();

            // Save to cookies
            setCookie('totalStudyTime', totalStudyTime, 365);
            setCookie('weeklyData', JSON.stringify(weeklyData), 365);
            setCookie('streak', streak, 365);

            showStatus('Paused', 'bg-yellow-500');
        }

        function updateTimer() {
            if (isRecording) {
                const elapsed = new Date() - startTime + elapsedPausedTime; // Add paused time to the current time

                const seconds = Math.floor((elapsed / 1000) % 60);
                const minutes = Math.floor((elapsed / 1000 / 60) % 60);
                const hours = Math.floor(elapsed / 1000 / 60 / 60);

                document.getElementById('todayTime').textContent =
                    `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                timer = setTimeout(updateTimer, 1000);
            }
        }

        function updateWeeklyData(sessionDuration) {
            const today = new Date().getDay();
            const adjustedDay = today === 0 ? 6 : today - 1; // Convert to Mon-Sun format

            if (!weeklyData[adjustedDay]) {
                weeklyData[adjustedDay] = 0;
            }
            weeklyData[adjustedDay] += Math.floor(sessionDuration / 3600); // Convert to hours

            weeklyChart.data.datasets[0].data = weeklyData;
            weeklyChart.update();
        }

        function updateStreak() {
            const lastStudyDate = getCookie('lastStudyDate');
            const today = new Date().toDateString();

            if (lastStudyDate !== today) {
                if (isConsecutiveDay(lastStudyDate)) {
                    streak++;
                } else {
                    streak = 1;
                }
                setCookie('lastStudyDate', today, 365);
                document.getElementById('streak').textContent = `${streak} days`;
            }
        }

        function isConsecutiveDay(lastDate) {
            if (!lastDate) return false;

            const last = new Date(lastDate);
            const today = new Date();
            const diffTime = Math.abs(today - last);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            return diffDays === 1;
        }

        function showStatus(message, className) {
            const statusIndicator = document.getElementById('statusIndicator');
            statusIndicator.textContent = message;
            statusIndicator.className = `px-4 py-2 rounded-full text-white font-bold ${className}`;
            statusIndicator.style.display = 'block';
        }

        // Cookie utilities
        function setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
            document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
        }

        function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const startLearningBtn = document.getElementById('startLearning');
            if (startLearningBtn) {
                startLearningBtn.addEventListener('click', initializeAudio);
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openPopupBtn = document.getElementById('open-popup');
            const popupOverlay = document.getElementById('popup-overlay');
            const popupContent = document.getElementById('popup-content');
            const submitBtn = document.getElementById('submit-btn');

            if (openPopupBtn) {
                openPopupBtn.addEventListener('click', () => {
                    popupOverlay.style.display = 'block';
                    popupContent.style.display = 'block';
                });
            }

            if (submitBtn) {
                submitBtn.addEventListener('click', () => {
                    const subject = document.getElementById('subject-input').value.trim();

                    if (subject) {
                        alert(`Subject Submitted: ${subject}`);
                        // Here you can send the subject to the server or process it as needed
                        popupOverlay.style.display = 'none';
                        popupContent.style.display = 'none';
                    } else {
                        alert('Please enter a topic.');
                    }
                });
            }

            // Close popup when clicking outside of the content
            if (popupOverlay) {
                popupOverlay.addEventListener('click', (e) => {
                    if (e.target === popupOverlay) {
                        popupOverlay.style.display = 'none';
                        popupContent.style.display = 'none';
                    }
                });
            }
        });
    </script>
    <script>
        // Function to get a cookie value by name
        function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        // Event listener for the "stopSessionBtn" button
        document.getElementById('stopSessionBtn').addEventListener('click', async function () {
            try {
                // Stop recording (assumed to be a function you defined elsewhere)
                stopRecording();

                // Get the total time from the element with id 'todayTime'
                const todayTimeElement = document.getElementById('todayTime');
                if (!todayTimeElement) {
                    console.error('Error: todayTime element not found.');
                    return;
                }
                const totalTime = todayTimeElement.textContent; // Get the total time in HH:MM:SS format

                // Extract subject from URL
                const subject = new URLSearchParams(window.location.search).get('subject');
                if (!subject) {
                    console.warn('Warning: Subject parameter not found in URL.');
                    // Optionally, handle this case by setting a default value
                }

                // Retrieve distraction number and time from cookies
                const distractionNo = getCookie('distraction_count');
                const distractionTime = getCookie('distraction_time');
                
                console.log("Distraction Nok:", distractionNo);

                // If distraction cookies are not found, default them to 0
                const distractionNoValue = distractionNo || '0';
                const distractionTimeValue = distractionTime || '0';

                // Prepare data to send
                const formData = new FormData();
                formData.append('totalTime', totalTime);
                formData.append('subject', subject); // Default subject if none is provided
                formData.append('mode', "reading"); // Add mode as "reading"
                formData.append('distraction_no', distractionNoValue); // Append distraction number
                formData.append('distraction_time', distractionTimeValue); // Append distraction time

                // Send the data to record.php
                const response = await fetch('record.php', {
                    method: 'POST',
                    body: formData
                });

                // Handle response
                if (response.ok) {
                    const result = await response.text();
                    console.log('Total study time and subject sent:', result);
                    // Redirect to record.php after successful submission
                    window.location.href = 'record.php';
                } else {
                    console.error('Error sending data:', response.statusText);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    </script>
    <script>
        // Replace this with your specific date (YYYY-MM-DD format)
        const targetDate = new Date('2025-2-18');

        // Get today's date
        const today = new Date();

        // Calculate the difference in time
        const timeDifference = targetDate - today;

        // Convert time difference to days
        const daysLeft = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));

        // Display the result
        const daysLeftText = document.getElementById('days-left-text');
        if (daysLeft > 0) {
            daysLeftText.textContent = `${daysLeft} days left for board!`;
        } else if (daysLeft === 0) {
            daysLeftText.textContent = "Today is the starting of board";
        } else {
            daysLeftText.textContent = `${Math.abs(daysLeft)} days have passed since your target date.`;
        }
    </script>
    <script>
        // Function to get the current value of the cookie
        function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        // Function to set a cookie with a specified name, value, and expiration time in days
        function setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000)); // Set expiration time
            document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
        }

        // Variable to track if a distraction event has been counted
        let distractionInProgress = false;

        // Function to update the distraction time and count
        function updateDistractionTime() {
            // If distraction is already in progress, do not increment again
            if (distractionInProgress) return;

            const lastDistractionTime = parseInt(getCookie('lastDistractionTime') || '0');
            const currentTime = new Date().getTime(); // Get current time in milliseconds

            if (lastDistractionTime !== 0) {
                const timeDifference = currentTime - lastDistractionTime; // Calculate the time difference
                let totalDistractionTime = parseInt(getCookie('distraction_time') || '0'); // Get current total distraction time
                totalDistractionTime += timeDifference; // Add the current distraction time to the total

                // Update the total distraction time in the cookie
                setCookie('distraction_time', totalDistractionTime, 365);

                // Update the distraction count
                let distractionCount = parseInt(getCookie('distraction_count') || '0');
                distractionCount += 1; // Increment distraction count

                // Update the distraction count in the cookie
                setCookie('distraction_count', distractionCount, 365);
            }

            // Update the last distraction time cookie with the current time
            setCookie('lastDistractionTime', currentTime, 365);

            // Mark that a distraction event has been processed
            distractionInProgress = true;
        }

        // Detect when the page visibility changes (tab focus change)
        document.addEventListener("visibilitychange", function () {
            if (document.visibilityState === "hidden") {
                // Page is being hidden (e.g., tab is switched or minimized)
                updateDistractionTime(); // Update distraction time and count when tab is hidden
            } else {
                // Page is being shown again, reset the distraction flag
                distractionInProgress = false;
            }
        });

        // Detect when the page is unloaded (tab closed or navigated away)
        window.addEventListener("beforeunload", function () {
            updateDistractionTime(); // Update distraction time and count when the page is closed
        });

        // Display the total distraction time and count (optional)
        document.addEventListener('DOMContentLoaded', function () {
            let totalDistractionTime = parseInt(getCookie('distraction_time') || '0');
            let distractionCount = parseInt(getCookie('distraction_count') || '0');

            // Convert total distraction time from milliseconds to minutes
            let totalDistractionMinutes = Math.floor(totalDistractionTime / 1000 / 60);

            // Display the distraction count and total distraction time in minutes (you can display it on the page as well)
            console.log(`Total Distractions: ${distractionCount} times`);
            console.log(`Total Distraction Time: ${totalDistractionMinutes} minutes`);

            // Optionally, display this on the page:
            const distractionInfo = document.getElementById('distraction-info');
            if (distractionInfo) {
                distractionInfo.innerHTML = `
                <p>Total Distractions: ${distractionCount}</p>
                <p>Total Distraction Time: ${totalDistractionMinutes} minutes</p>
            `;
            }
        });
    </script>


</body>

</html>