<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['subject'])) {  // If this is the quiz generation request
        $subject = $_POST['subject'] ?? '';
        $chapter = $_POST['chapter'] ?? '';
        $board = $_POST['board'] ?? '';
        
        if ($subject && $chapter && $board) {
            $prompt = "Generate a quiz for {$subject}, Chapter: {$chapter}, Board: {$board}. 
                      Create 5 multiple choice questions.
                      Format each question as JSON like this:
                      {
                        'questions': [
                          {
                            'question': 'Question text here?',
                            'options': ['Option A', 'Option B', 'Option C', 'Option D'],
                            'correct': 0
                          }
                        ]
                      }
                      Also include 3 long answer questions at the end in this format.
                      ";

            $api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=AIzaSyAt_a5BOeb8gwlCtaolAvIQ3ycj2NxQKhU";
            
            $data = array(
                "contents" => array(
                    array(
                        "parts" => array(
                            array("text" => $prompt)
                        )
                    )
                )
            );
            
            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            if ($response) {
                $generatedContent = json_decode($response, true);
                $content = $generatedContent['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Extract JSON and long questions
                preg_match('/{.*}/s', $content, $jsonMatches);
                preg_match('/Long Questions:(.*?)$/s', $content, $longMatches);
                
                if (!empty($jsonMatches[0])) {
                    $mcqQuestions = json_decode($jsonMatches[0], true);
                }
                if (!empty($longMatches[1])) {
                    $longQuestions = trim($longMatches[1]);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function initializeQuiz(questions) {
            let currentScore = 0;
            let totalAnswered = 0;
            
            questions.forEach((question, index) => {
                const options = document.querySelectorAll(`[name="q${index}"]`);
                options.forEach(option => {
                    option.addEventListener('change', function() {
                        const questionCard = this.closest('.question-card');
                        const feedback = questionCard.querySelector('.feedback');
                        const correctIndex = question.correct;
                        
                        if (!this.dataset.answered) {
                            totalAnswered++;
                            if (parseInt(this.value) === correctIndex) {
                                currentScore++;
                                feedback.innerHTML = '<span class="text-green-600">Correct!</span>';
                            } else {
                                feedback.innerHTML = '<span class="text-red-600">Incorrect!</span>';
                            }
                            
                            // Disable all options for this question
                            options.forEach(opt => {
                                opt.disabled = true;
                                opt.dataset.answered = 'true';
                                if (parseInt(opt.value) === correctIndex) {
                                    opt.parentElement.classList.add('bg-green-100');
                                }
                            });
                            
                            // Update score
                            document.getElementById('score').textContent = 
                                `Score: ${currentScore}/${totalAnswered}`;
                        }
                    });
                });
            });
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <?php include('../component/sidebar.php'); ?>
    <main class="sm:ml-64 p-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Interactive Quiz
                </h1>
                <p class="mt-3 text-xl text-gray-500">
                    Test your knowledge!
                </p>
            </div>
    
            <?php if (!isset($mcqQuestions)): ?>
            <!-- Quiz Generation Form -->
            <div class="bg-white shadow rounded-lg p-6 mb-8">
                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                            <input type="text" name="subject" id="subject" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="e.g. Physics">
                        </div>
    
                        <div>
                            <label for="chapter" class="block text-sm font-medium text-gray-700">Chapter</label>
                            <input type="text" name="chapter" id="chapter" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="e.g. Kinematics">
                        </div>
    
                        <div>
                            <label for="board" class="block text-sm font-medium text-gray-700">Board/Standard</label>
                            <input type="text" name="board" id="board" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                   placeholder="e.g. CBSE Class 12">
                        </div>
                    </div>
    
                    <div class="flex justify-center">
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Generate Quiz
                        </button>
                    </div>
                </form>
            </div>
            
            <?php else: ?>
            <!-- Quiz Display -->
            <div class="space-y-8">
                <!-- Score Display -->
                <div class="bg-white shadow rounded-lg p-4 mb-6">
                    <h2 id="score" class="text-xl font-semibold text-center">Score: 0/0</h2>
                </div>
    
                <!-- Multiple Choice Questions -->
                <div class="space-y-6">
                    <?php foreach ($mcqQuestions['questions'] as $index => $question): ?>
                    <div class="bg-white shadow rounded-lg p-6 question-card">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <?php echo ($index + 1) . '. ' . htmlspecialchars($question['question']); ?>
                        </h3>
                        
                        <div class="space-y-3">
                            <?php foreach ($question['options'] as $optIndex => $option): ?>
                            <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="q<?php echo $index; ?>" value="<?php echo $optIndex; ?>"
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <span class="ml-3 text-gray-700">
                                    <?php echo htmlspecialchars($option); ?>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="feedback mt-3 text-sm font-medium"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
    
                <!-- Long Questions -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Long Answer Questions</h2>
                    <div class="space-y-4">
                        <?php 
                        if (isset($longQuestions)) {
                            echo '<div class="prose max-w-none">';
                            echo nl2br(htmlspecialchars($longQuestions));
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
    
                <!-- Reset Button -->
                <div class="flex justify-center mt-8">
                    <form method="POST" class="inline-block">
                        <button type="submit"
                                class="px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Generate New Quiz
                        </button>
                    </form>
                </div>
            </div>
    
            <script>
                initializeQuiz(<?php echo json_encode($mcqQuestions['questions']); ?>);
            </script>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>