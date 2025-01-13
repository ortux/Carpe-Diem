<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Add custom 3D animation */
        .hover-3d {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-3d:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        .hover-3d:focus-within {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg hover-3d">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">
            Enter Your Name
        </h1>
        <form action="login_handeler.php" method="POST" class="space-y-4">
            <div>
                <label for="name" class="block text-gray-600 font-medium mb-2">
                    Your Name:
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 hover-3d"
                    placeholder="Enter your name" 
                    required 
                >
            </div>
            <button 
                type="submit" 
                class="w-full bg-blue-500 text-white font-bold py-3 rounded-lg hover:bg-blue-600 hover-3d focus:ring-4 focus:ring-blue-300"
            >
                Submit
            </button>
        </form>
    </div>
</body>
</html>
