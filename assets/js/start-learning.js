let isReading = false;
let startTime = null;
let stopTime = null;
let timerInterval = null;
let totalReadingTime = 0;

const startLearningBtn = document.getElementById('startLearningBtn');
const totalReadingTimeElement = document.getElementById('totalReadingTime');

// This function listens to the microphone and detects when the user starts/stops reading
async function startListening() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const analyser = audioContext.createAnalyser();
        const microphone = audioContext.createMediaStreamSource(stream);
        microphone.connect(analyser);
        
        const dataArray = new Uint8Array(analyser.frequencyBinCount);

        function detectReading() {
            analyser.getByteFrequencyData(dataArray);
            const average = dataArray.reduce((sum, value) => sum + value, 0) / dataArray.length;

            // If average volume exceeds a certain threshold, assume reading is happening
            if (average > 50) { // Adjust the threshold based on testing
                if (!isReading) {
                    startReading();
                }
            } else {
                if (isReading) {
                    stopReading();
                }
            }
        }

        setInterval(detectReading, 100);
    } catch (error) {
        console.error('Error accessing microphone:', error);
    }
}

// Function to start the reading timer
function startReading() {
    isReading = true;
    startTime = new Date();
    startLearningBtn.textContent = "Stop Learning"; // Change button text
    startLearningBtn.classList.add("bg-red-500", "hover:bg-red-700");
    startLearningBtn.classList.remove("bg-blue-500", "hover:bg-blue-700");

    if (timerInterval) clearInterval(timerInterval);
    timerInterval = setInterval(updateReadingTime, 1000);
}

// Function to stop the reading timer
function stopReading() {
    isReading = false;
    stopTime = new Date();
    totalReadingTime += Math.round((stopTime - startTime) / 1000);
    clearInterval(timerInterval);

    // Update the backend with the total time spent reading
    fetch('save-reading-time.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'reading_time=' + totalReadingTime
    });

    // Update the UI
    totalReadingTimeElement.textContent = gmdate("H:i:s", totalReadingTime);

    startLearningBtn.textContent = "Start Learning"; // Reset button text
    startLearningBtn.classList.remove("bg-red-500", "hover:bg-red-700");
    startLearningBtn.classList.add("bg-blue-500", "hover:bg-blue-700");
}

// Update the reading timer on the front-end
function updateReadingTime() {
    const elapsedTime = Math.round((new Date() - startTime) / 1000);
    console.log("Reading Time: " + elapsedTime + " seconds");
}

// Initialize the mic listener when the "Start Learning" button is clicked
startLearningBtn.addEventListener('click', function () {
    if (!isReading) {
        startListening();
    } else {
        stopReading();
    }
});

// Helper function to format seconds into HH:MM:SS format
function gmdate(format, timestamp) {
    let date = new Date(timestamp * 1000);
    let hours = date.getUTCHours().toString().padStart(2, '0');
    let minutes = date.getUTCMinutes().toString().padStart(2, '0');
    let seconds = date.getUTCSeconds().toString().padStart(2, '0');
    return `${hours}:${minutes}:${seconds}`;
}
