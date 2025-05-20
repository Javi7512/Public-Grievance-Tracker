<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser();
$isAdmin = isAdmin();
$activeTab = $_GET['tab'] ?? 'overview';

// Get grievances
$grievances = getGrievances($user['id'], $isAdmin);

// Get statistics
$stats = getStatistics($user['id'], $isAdmin);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?> - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="min-h-screen flex flex-col bg-gray-100">
    <header class="bg-blue-600 shadow z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="dashboard.php" class="text-white text-xl font-bold cursor-pointer">
                            <?= SITE_TITLE ?>
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="dashboard.php" class="<?= $activeTab === 'overview' ? 'text-white' : 'text-white/80 hover:text-white' ?> hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">Home</a>
                            <a href="dashboard.php?tab=my-grievances" class="<?= $activeTab === 'my-grievances' ? 'text-white' : 'text-white/80 hover:text-white' ?> hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">My Grievances</a>
                            <a href="dashboard.php?tab=file-grievance" class="<?= $activeTab === 'file-grievance' ? 'text-white' : 'text-white/80 hover:text-white' ?> hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">File New</a>
                            <a href="index.php" class="text-white/80 hover:text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">About</a>
                        </div>
                    </div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <div class="relative inline-block text-left">
                            <button type="button" id="user-menu-button" class="relative max-w-xs bg-white/20 flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-white p-2">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 bg-white/20 text-white rounded-full flex items-center justify-center">
                                    <?= substr($user['name'], 0, 1) ?>
                                </div>
                                <span class="text-white ml-2 hidden md:block">
                                    <?= htmlspecialchars($user['name']) ?>
                                </span>
                            </button>
                            <div id="user-dropdown" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                <div class="py-1">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                                    <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="-mr-2 flex md:hidden">
                        <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-white">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden bg-blue-700 md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="dashboard.php" class="<?= $activeTab === 'overview' ? 'text-white' : 'text-white/80 hover:text-white' ?> block px-3 py-2 rounded-md text-base font-medium">Home</a>
                <a href="dashboard.php?tab=my-grievances" class="<?= $activeTab === 'my-grievances' ? 'text-white' : 'text-white/80 hover:text-white' ?> block px-3 py-2 rounded-md text-base font-medium">My Grievances</a>
                <a href="dashboard.php?tab=file-grievance" class="<?= $activeTab === 'file-grievance' ? 'text-white' : 'text-white/80 hover:text-white' ?> block px-3 py-2 rounded-md text-base font-medium">File New</a>
                <a href="index.php" class="text-white/80 hover:text-white block px-3 py-2 rounded-md text-base font-medium">About</a>
            </div>
            <div class="pt-4 pb-3 border-t border-white/10">
                <div class="flex items-center px-5">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-white/20 text-white rounded-full flex items-center justify-center">
                            <?= substr($user['name'], 0, 1) ?>
                        </div>
                    </div>
                    <div class="ml-3">
                        <div class="text-base font-medium text-white">
                            <?= htmlspecialchars($user['name']) ?>
                        </div>
                        <div class="text-sm font-medium text-white/60">
                            <?= htmlspecialchars($user['email']) ?>
                        </div>
                    </div>
                </div>
                <div class="mt-3 px-2 space-y-1">
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-white/80 hover:text-white">Your Profile</a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-white/80 hover:text-white">Settings</a>
                    <a href="logout.php" class="block px-3 py-2 rounded-md text-base font-medium text-white/80 hover:text-white">Sign out</a>
                </div>
            </div>
        </div>
    </header>
      
    <main class="flex-grow p-4 max-w-7xl mx-auto w-full">
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex">
                    <a href="dashboard.php" class="py-4 px-6 <?= $activeTab === 'overview' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        Overview
                    </a>
                    <a href="dashboard.php?tab=my-grievances" class="py-4 px-6 <?= $activeTab === 'my-grievances' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        My Grievances
                    </a>
                    <a href="dashboard.php?tab=file-grievance" class="py-4 px-6 <?= $activeTab === 'file-grievance' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        File Grievance
                    </a>
                    <?php if ($isAdmin): ?>
                    <a href="dashboard.php?tab=admin" class="py-4 px-6 <?= $activeTab === 'admin' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        Admin Dashboard
                    </a>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="p-6">
                <?php if ($activeTab === 'overview'): ?>
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-700 mb-2">Welcome to the Public Grievance Tracker</h1>
                    <p class="text-gray-500">Track and manage your grievances with local government authorities.</p>
                </div>
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 font-medium">Total Grievances</p>
                                <p class="text-xl font-semibold text-gray-700"><?= $stats['total'] ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 font-medium">In Progress</p>
                                <p class="text-xl font-semibold text-gray-700"><?= $stats['inProgress'] ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 font-medium">Resolved</p>
                                <p class="text-xl font-semibold text-gray-700"><?= $stats['resolved'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Recent Grievances</h2>
                    
                    <?php if (empty($grievances)): ?>
                    <div class="text-center py-8">
                        <p class="text-gray-500">No grievances found. File a new grievance to get started.</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Filed</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                $recentGrievances = array_slice($grievances, 0, 5);
                                foreach ($recentGrievances as $grievance): 
                                    $statusInfo = getStatusInfo($grievance['status']);
                                ?>
                                <tr class="hover:bg-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">#<?= $grievance['id'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= getCategoryLabel($grievance['category']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($grievance['location']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M d, Y', strtotime($grievance['dateFiled'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusInfo['bgColor'] ?> <?= $statusInfo['textColor'] ?>">
                                            <?= $statusInfo['label'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (count($grievances) > 5): ?>
                    <div class="mt-4 text-right">
                        <a href="dashboard.php?tab=my-grievances" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block ml-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($activeTab === 'my-grievances'): ?>
                <!-- My Grievances Tab -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-700 mb-2">My Grievances</h1>
                    <p class="text-gray-500">View and track all your submitted grievances.</p>
                </div>
                
                <?php include 'includes/grievance-list.php'; ?>
                <?php endif; ?>

                <?php if ($activeTab === 'file-grievance'): ?>
                <!-- File Grievance Tab -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-700 mb-2">File a New Grievance</h1>
                    <p class="text-gray-500">Submit your grievance to the relevant department for resolution.</p>
                </div>
                
                <?php include 'includes/grievance-form.php'; ?>
                <?php endif; ?>

                <?php if ($isAdmin && $activeTab === 'admin'): ?>
                <!-- Admin Dashboard Tab -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-700 mb-2">Admin Dashboard</h1>
                    <p class="text-gray-500">Manage and respond to citizen grievances.</p>
                </div>
                
                <!-- Statistics Cards (Admin) -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 font-medium">Total Grievances</p>
                                <p class="text-xl font-semibold text-gray-700"><?= $stats['total'] ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 font-medium">Pending</p>
                                <p class="text-xl font-semibold text-gray-700"><?= $stats['pending'] ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 font-medium">In Progress</p>
                                <p class="text-xl font-semibold text-gray-700"><?= $stats['inProgress'] ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 font-medium">Resolved</p>
                                <p class="text-xl font-semibold text-gray-700"><?= $stats['resolved'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php 
                // Use the same grievance list but set isAdmin flag
                $isAdminView = true;
                include 'includes/grievance-list.php'; 
                ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 mt-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex justify-center md:justify-start">
                    <p class="text-gray-500 text-sm">© <?= date('Y') ?> <?= SITE_TITLE ?>. All rights reserved.</p>
                </div>
                <div class="flex justify-center md:justify-end mt-4 md:mt-0 space-x-4">
                    <a href="#" class="text-gray-500 hover:text-gray-600 text-sm">Privacy Policy</a>
                    <a href="#" class="text-gray-500 hover:text-gray-600 text-sm">Terms of Service</a>
                    <a href="#" class="text-gray-500 hover:text-gray-600 text-sm">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // User dropdown toggle
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');
        
        userMenuButton.addEventListener('click', () => {
            userDropdown.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (event) => {
            if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.add('hidden');
            }
        });
        
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
    <script src="assets/js/script.js"></script>
</body>
</html>