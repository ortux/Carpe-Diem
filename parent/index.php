<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen bg-gradient-to-r from-blue-500 via-teal-400 to-indigo-600 flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-semibold text-center mb-6 text-gray-700">Student Code Login</h2>
        <form action="login-handler.php" method="POST">
            <div class="mb-4">
                <label for="student_code" class="block text-sm font-medium text-gray-600">Enter Student Code</label>
                <input type="text" id="student_code" name="student_code" class="mt-2 p-2 w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required />
            </div>
            <button type="submit" class="w-full py-2 px-4 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Login</button>
        </form>
    </div>
</body>
</html>
