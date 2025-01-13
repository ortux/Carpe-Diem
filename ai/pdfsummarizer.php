<?php
// Enable error reporting for debugging

// process.php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["pdfFile"])) {
    $target_dir = "uploads/";
    
    // Check if directory exists, if not create it with proper permissions
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            $error = "Failed to create uploads directory. Please check server permissions.";
        }
        chmod($target_dir, 0777);
    }
    
    // Verify directory is writable
    if (!is_writable($target_dir)) {
        $error = "Upload directory is not writable. Current permissions: " . substr(sprintf('%o', fileperms($target_dir)), -4);
    }
    
    if (!isset($error)) {
        $target_file = $target_dir . basename($_FILES["pdfFile"]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        // Detailed upload error checking
        if ($_FILES["pdfFile"]["error"] !== UPLOAD_ERR_OK) {
            switch ($_FILES["pdfFile"]["error"]) {
                case UPLOAD_ERR_INI_SIZE:
                    $error = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $error = "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error = "The uploaded file was only partially uploaded";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error = "No file was uploaded";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error = "Missing a temporary folder";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error = "Failed to write file to disk";
                    break;
                default:
                    $error = "Unknown upload error";
            }
            $uploadOk = 0;
        }
        
        // Check file type
        if($fileType != "pdf") {
            $error = "Only PDF files are allowed.";
            $uploadOk = 0;
        }
        
        // Check file size (optional)
        if ($_FILES["pdfFile"]["size"] > 5000000) {
            $error = "Sorry, your file is too large. Maximum size is 5MB.";
            $uploadOk = 0;
        }
        
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["pdfFile"]["tmp_name"], $target_file)) {
                $uploaded_file = true;
                $file_url = 'http://' . $_SERVER['HTTP_HOST'] . 
                           dirname($_SERVER['REQUEST_URI']) . '/' . $target_file;
                
                // Call Gemini API
                $api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=";
                
                $data = array(
                    "contents" => array(
                        array(
                            "parts" => array(
                                array(
                                    "text" => "summarize this webpage pdf file at " . $file_url
                                )
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
                
                if (curl_errno($ch)) {
                    $error = "API Error: " . curl_error($ch);
                }
                
                curl_close($ch);
                
                if ($response) {
                    $summary = json_decode($response, true);
                }
            } else {
                $error = "Sorry, there was an error uploading your file. Error details: " . error_get_last()['message'];
            }
        }
    }
}

// Get server configuration info for debugging
$upload_max_filesize = ini_get('upload_max_filesize');
$post_max_size = ini_get('post_max_size');
$max_execution_time = ini_get('max_execution_time');
$memory_limit = ini_get('memory_limit');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Summarizer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function updateFileName(input) {
            const fileName = input.files[0]?.name;
            if (fileName) {
                document.getElementById('file-name').textContent = fileName;
                document.getElementById('file-name-container').classList.remove('hidden');
                document.getElementById('upload-prompt').classList.add('hidden');
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <?php include('../component/sidebar.php'); ?>
    <main class="sm:ml-64 p-4">
        <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl p-6">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">PDF Summarizer</h1>
                <p class="mt-2 text-gray-600">Upload a PDF file to get its summary</p>
            </div>
    
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error: </strong>
                    <span class="block sm:inline"><?php echo $error; ?></span>
                    <div class="mt-2 text-sm">
                        <p>Server Configuration:</p>
                        <ul class="list-disc pl-5">
                            <li>Upload max filesize: <?php echo $upload_max_filesize; ?></li>
                            <li>Post max size: <?php echo $post_max_size; ?></li>
                            <li>Max execution time: <?php echo $max_execution_time; ?></li>
                            <li>Memory limit: <?php echo $memory_limit; ?></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
    
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col w-full h-32 border-4 border-dashed hover:bg-gray-100 hover:border-gray-300">
                        <div class="flex flex-col items-center justify-center pt-7">
                            <div id="upload-prompt">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 group-hover:text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                </svg>
                                <p class="pt-1 text-sm tracking-wider text-gray-400 group-hover:text-gray-600">
                                    Select PDF file (max <?php echo $upload_max_filesize; ?>)
                                </p>
                            </div>
                            <div id="file-name-container" class="hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p id="file-name" class="pt-1 text-sm font-medium text-gray-700"></p>
                                <p class="text-xs text-gray-500 mt-1">(Click to change file)</p>
                            </div>
                        </div>
                        <input type="file" name="pdfFile" class="opacity-0" accept=".pdf" required onchange="updateFileName(this)" />
                    </label>
                </div>
                
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Upload and Summarize
                    </button>
                </div>
            </form>
    
            <?php if (isset($summary)): ?>
                <div class="mt-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Summary</h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700"><?php echo $summary['candidates'][0]['content']['parts'][0]['text'] ?? 'Unable to generate summary.'; ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
