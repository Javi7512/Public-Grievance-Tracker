<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Redirect to dashboard if user is logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?> - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="min-h-screen bg-gray-100 flex flex-col">
    <header class="bg-blue-600 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex-shrink-0">
                    <h1 class="text-2xl font-bold text-white"><?= SITE_TITLE ?></h1>
                </div>
                <div class="flex items-center">
                    <a href="login.php" class="bg-white/20 text-white hover:bg-white/30 px-4 py-2 rounded-md text-sm font-medium">
                        Login / Register
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold text-gray-700 sm:text-5xl">
                    Empowering Citizens, Improving Communities
                </h2>
                <p class="mt-4 text-xl text-gray-500 max-w-3xl mx-auto">
                    A platform to report and track public grievances for a better tomorrow.
                </p>
                <div class="mt-8 flex justify-center">
                    <a href="register.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-md text-lg">
                        Get Started
                    </a>
                </div>
            </div>

            <!-- Features Section -->
            <div class="py-12">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 inline-block mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Voice Your Concerns</h3>
                        <p class="text-gray-500">
                            Submit grievances related to road repair, water supply, garbage disposal, public transport, and more.
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 inline-block mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Track Progress</h3>
                        <p class="text-gray-500">
                            Monitor the status of your grievances from submission to resolution with complete transparency.
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 inline-block mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Connect with Authorities</h3>
                        <p class="text-gray-500">
                            Direct communication with relevant departments for faster resolution of your issues.
                        </p>
                    </div>
                </div>
            </div>

            <!-- How It Works Section -->
            <div class="py-12">
                <h2 class="text-3xl font-bold text-gray-700 mb-8 text-center">How It Works</h2>
                <div class="flex flex-col md:flex-row md:items-start md:space-x-8">
                    <div class="flex-1 mb-8 md:mb-0 text-center">
                        <div class="rounded-full bg-blue-600 h-12 w-12 flex items-center justify-center text-white text-xl font-bold mx-auto mb-4">1</div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Register an Account</h3>
                        <p class="text-gray-500">Create an account to get started with tracking your grievances.</p>
                    </div>
                    <div class="flex-1 mb-8 md:mb-0 text-center">
                        <div class="rounded-full bg-blue-600 h-12 w-12 flex items-center justify-center text-white text-xl font-bold mx-auto mb-4">2</div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Submit a Grievance</h3>
                        <p class="text-gray-500">Provide details about your issue including location and evidence.</p>
                    </div>
                    <div class="flex-1 mb-8 md:mb-0 text-center">
                        <div class="rounded-full bg-blue-600 h-12 w-12 flex items-center justify-center text-white text-xl font-bold mx-auto mb-4">3</div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Track Resolution</h3>
                        <p class="text-gray-500">Follow the progress as authorities address your grievance.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="text-center md:text-left">
                    <p class="text-gray-500 text-sm">© <?= date('Y') ?> <?= SITE_TITLE ?>. All rights reserved.</p>
                </div>
                <div class="flex justify-center md:justify-end mt-4 md:mt-0 space-x-6">
                    <a href="#" class="text-gray-500 hover:text-gray-600 text-sm">Privacy Policy</a>
                    <a href="#" class="text-gray-500 hover:text-gray-600 text-sm">Terms of Service</a>
                    <a href="#" class="text-gray-500 hover:text-gray-600 text-sm">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>