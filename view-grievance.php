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

// Get grievance ID from URL
$grievanceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($grievanceId <= 0) {
    header('Location: dashboard.php');
    exit();
}

// Get grievance details
$grievance = getGrievanceById($grievanceId, $user['id'], $isAdmin);

if (!$grievance) {
    header('Location: dashboard.php');
    exit();
}

// Get responses for this grievance
$responses = getResponses($grievanceId);

// Handle admin response submission
$responseMessage = '';
$responseError = '';

if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $_POST['comment'] ?? '';
    $status = $_POST['status'] ?? $grievance['status'];
    $department = $_POST['department'] ?? $grievance['department'];
    
    if (empty($comment)) {
        $responseError = 'Response comment is required';
    } else {
        $result = addResponse($grievanceId, $user['id'], $comment, $status, $department);
        
        if ($result['success']) {
            // Refresh the data
            $grievance = getGrievanceById($grievanceId, $user['id'], $isAdmin);
            $responses = getResponses($grievanceId);
            $responseMessage = 'Response added successfully';
        } else {
            $responseError = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_TITLE ?> - Grievance Details</title>
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
                    <a href="dashboard.php" class="text-white/80 hover:text-white hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">
                        Back to Dashboard
                    </a>
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
                </div>
            </div>
        </div>
    </header>
      
    <main class="flex-grow p-4 max-w-3xl mx-auto w-full">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-700">Grievance #<?= $grievance['id'] ?></h3>
                    <p class="text-gray-500 text-sm">
                        Filed on: <?= date('M d, Y H:i', strtotime($grievance['dateFiled'])) ?>
                    </p>
                </div>
                <div>
                    <?php 
                    $statusInfo = getStatusInfo($grievance['status']);
                    ?>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusInfo['bgColor'] ?> <?= $statusInfo['textColor'] ?>">
                        <?= $statusInfo['label'] ?>
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Category</p>
                    <p class="text-gray-700"><?= getCategoryLabel($grievance['category']) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Location</p>
                    <p class="text-gray-700"><?= htmlspecialchars($grievance['location']) ?></p>
                </div>
                <?php if (!empty($grievance['department'])): ?>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Department</p>
                    <p class="text-gray-700"><?= getDepartmentLabel($grievance['department']) ?></p>
                </div>
                <?php endif; ?>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Submitted By</p>
                    <p class="text-gray-700"><?= htmlspecialchars($grievance['userName']) ?></p>
                </div>
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-500 font-medium">Description</p>
                <p class="text-gray-700"><?= nl2br(htmlspecialchars($grievance['description'])) ?></p>
            </div>
            
            <?php if (!empty($grievance['imagePath']) && file_exists($grievance['imagePath'])): ?>
            <div class="mb-4">
                <p class="text-sm text-gray-500 font-medium">Uploaded Image</p>
                <img src="<?= $grievance['imagePath'] ?>" alt="Grievance evidence" class="mt-2 rounded-md max-h-60 object-cover">
            </div>
            <?php endif; ?>
            
            <!-- Response History -->
            <div class="mt-8 mb-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-4">Response History</h4>
                
                <?php if (empty($responses)): ?>
                <div class="bg-gray-100 rounded-md p-4 text-center">
                    <p class="text-gray-500">No responses yet.</p>
                </div>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($responses as $response): ?>
                    <div class="bg-gray-100 rounded-md p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 005 10a6 6 0 0012 0c0-.352-.035-.696-.1-1.028A5 5 0 0010 11z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium text-gray-700">
                                    <?= htmlspecialchars($response['adminName']) ?> (<?= getDepartmentLabel($response['department']) ?>)
                                </span>
                            </div>
                            <span class="text-sm text-gray-500"><?= date('M d, Y H:i', strtotime($response['timestamp'])) ?></span>
                        </div>
                        <p class="text-gray-700"><?= nl2br(htmlspecialchars($response['comment'])) ?></p>
                        <div class="mt-2 text-sm">
                            <span class="text-gray-500">Status updated to: </span>
                            <?php 
                            $respStatusInfo = getStatusInfo($response['statusUpdated']);
                            ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $respStatusInfo['bgColor'] ?> <?= $respStatusInfo['textColor'] ?>">
                                <?= $respStatusInfo['label'] ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Admin Response Form -->
            <?php if ($isAdmin): ?>
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-4">Add Response</h4>
                
                <?php if (!empty($responseMessage)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?= htmlspecialchars($responseMessage) ?></span>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($responseError)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?= htmlspecialchars($responseError) ?></span>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="view-grievance.php?id=<?= $grievanceId ?>" class="space-y-4">
                    <div>
                        <label for="comment" class="block text-sm font-medium text-gray-700">Response</label>
                        <textarea id="comment" name="comment" rows="3" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter your response to this grievance"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Update Status</label>
                            <select id="status" name="status" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md">
                                <option value="pending" <?= $grievance['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="in-progress" <?= $grievance['status'] === 'in-progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="resolved" <?= $grievance['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700">Assign Department</label>
                            <select id="department" name="department" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md">
                                <option value="public-works" <?= ($grievance['department'] ?? '') === 'public-works' ? 'selected' : '' ?>>Public Works Department</option>
                                <option value="water-authority" <?= ($grievance['department'] ?? '') === 'water-authority' ? 'selected' : '' ?>>Water Authority</option>
                                <option value="sanitation" <?= ($grievance['department'] ?? '') === 'sanitation' ? 'selected' : '' ?>>Sanitation Department</option>
                                <option value="transport-authority" <?= ($grievance['department'] ?? '') === 'transport-authority' ? 'selected' : '' ?>>Transport Authority</option>
                                <option value="municipal-office" <?= ($grievance['department'] ?? '') === 'municipal-office' ? 'selected' : '' ?>>Municipal Office</option>
                                <option value="other" <?= ($grievance['department'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit Response
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <a href="dashboard.php?tab=my-grievances" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Grievances
                </a>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 mt-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-center">
                <p class="text-gray-500 text-sm">© <?= date('Y') ?> <?= SITE_TITLE ?>. All rights reserved.</p>
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
    </script>
    <script src="assets/js/script.js"></script>
</body>
</html>