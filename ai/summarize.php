<?php

// Define a variable to store the response (if there is one)
$response = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['webpage_url'])) {
    // Get the webpage URL from the form submission
    $webpage_url = $_POST['webpage_url'];

    // The API URL and API Key
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=";

    // The data to send in the POST request
    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => "summarize this webpage $webpage_url"]
                ]
            ]
        ]
    ];

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);               // Set the URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     // Return the response as a string
    curl_setopt($ch, CURLOPT_POST, true);               // Set the request method to POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  // Attach the data as JSON
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'                // Set the content type header
    ]);

    // Execute the cURL request
    $responseJson = curl_exec($ch);

    // Check for errors in the request
    if (curl_errno($ch)) {
        $response = 'Error:' . curl_error($ch);
    } else {
        // Decode the JSON response
        $decodedResponse = json_decode($responseJson, true);

        // Extract the relevant content (summary text)
        if (isset($decodedResponse['candidates'][0]['content']['parts'][0]['text'])) {
            $response = $decodedResponse['candidates'][0]['content']['parts'][0]['text'];
        } else {
            $response = 'No summary available.';
        }
    }

    // Close cURL session
    curl_close($ch);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Based Summarize Webpage</title>
    <!-- Link to Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Link to jsPDF CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-gray-50 font-sans">
    <?php include('../component/sidebar.php'); ?>
    <main class="sm:ml-64 p-4">

        <!-- Main Container -->
        <div class="max-w-4xl mx-auto p-8">
            
            <!-- Header Section -->
            <h1 class="text-3xl font-semibold text-center text-indigo-600 mb-6">Summarize Webpage with AI</h1>
            
            <!-- Form Section -->
            <form action="" method="POST" class="bg-white p-6 rounded-lg shadow-lg space-y-6">
                <div>
                    <label for="webpage_url" class="block text-gray-700 font-medium">Enter Webpage URL:</label>
                    <input type="text" id="webpage_url" name="webpage_url" required placeholder="https://example.com" 
                           class="mt-2 w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <div class="text-center">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Summarize
                    </button>
                </div>
            </form>
    
            <!-- Response Section -->
            <?php if ($response): ?>
            <div class="mt-8 bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-semibold text-gray-700">Summary:</h2>
                <p id="summary-text" class="mt-4 text-gray-600"><?php echo nl2br(htmlspecialchars($response)); ?></p>
    
                <!-- Button to save as PDF -->
                <div class="mt-6 text-center">
                    <button id="save-pdf" class="px-6 py-3 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Save as PDF
                    </button>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
    </main>

    <script>
        // JavaScript to handle PDF generation
        document.getElementById('save-pdf').addEventListener('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Get the summary text from the page
            const summaryText = document.getElementById('summary-text').innerText;

            // Add the summary to the PDF
            const margin = 10;
            const pageHeight = doc.internal.pageSize.height;

            // Use doc.text to add text, allowing it to wrap if needed
            doc.text(summaryText, margin, margin, { maxWidth: 180 });  // 180 is the max width for text wrapping

            // If the text exceeds one page, add another page
            const totalHeight = doc.getTextDimensions(summaryText).h;
            if (totalHeight > pageHeight - 20) {
                doc.addPage();
                doc.text(summaryText, margin, margin);
            }

            // Save the PDF with the filename "summary.pdf"
            doc.save('summary.pdf');
        });
    </script>

</body>
</html>
