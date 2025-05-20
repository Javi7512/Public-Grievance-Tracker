<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Redirect to dashboard if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $result = loginUser($username, $password);
        
        if ($result['success']) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?> - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="min-h-screen bg-gray-100 flex flex-col">
    <header class="bg-blue-600 shadow">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-white"><?= SITE_TITLE ?></h1>
        </div>
    </header>

    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="flex flex-col justify-center">
                    <h2 class="text-3xl font-bold text-gray-700 mb-6">Empower Your Voice</h2>
                    <p class="text-lg text-gray-500 mb-4">
                        The Public Grievance Tracker provides a transparent platform for citizens to submit and track complaints about local infrastructure and services.
                    </p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Easy submission of community issues
                        </li>
                        <li class="flex items-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Real-time tracking of grievance status
                        </li>
                        <li class="flex items-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Direct communication with authorities
                        </li>
                    </ul>
                </div>

                <div>
                    <div class="bg-white p-8 rounded-lg shadow-md">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-700 mb-2">Login</h2>
                            <p class="text-gray-500">Sign in to your account to access your dashboard</p>
                        </div>
                        
                        <?php if (!empty($error)): ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                                <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="login.php" class="space-y-4">
                            <div class="space-y-2">
                                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                <input id="username" name="username" type="text" required 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                    placeholder="Enter your username">
                            </div>
                            
                            <div class="space-y-2">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input id="password" name="password" type="password" required 
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                    placeholder="Enter your password">
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember-me" name="remember-me" type="checkbox" 
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
                                </div>
                                <a href="#" class="text-sm text-blue-600 hover:text-blue-500">Forgot password?</a>
                            </div>

                            <div>
                                <button type="submit" 
                                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Sign in
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Don't have an account? 
                                <a href="register.php" class="font-medium text-blue-600 hover:text-blue-500">
                                    Register now
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                © <?= date('Y') ?> <?= SITE_TITLE ?>. All rights reserved.
            </p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>