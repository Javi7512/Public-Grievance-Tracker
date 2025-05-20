<?php
$isAdminView = $isAdminView ?? $isAdmin;

// Filter variables
$statusFilter = $_GET['status'] ?? 'all';
$categoryFilter = $_GET['category'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';

// Apply filters
$filteredGrievances = $grievances;

if ($statusFilter !== 'all') {
    $filteredGrievances = array_filter($filteredGrievances, function($g) use ($statusFilter) {
        return $g['status'] === $statusFilter;
    });
}

if ($categoryFilter !== 'all') {
    $filteredGrievances = array_filter($filteredGrievances, function($g) use ($categoryFilter) {
        return $g['category'] === $categoryFilter;
    });
}

if (!empty($searchQuery)) {
    $filteredGrievances = array_filter($filteredGrievances, function($g) use ($searchQuery) {
        $searchLower = strtolower($searchQuery);
        return strpos(strtolower($g['location']), $searchLower) !== false ||
               strpos(strtolower($g['description']), $searchLower) !== false ||
               strpos((string)$g['id'], $searchLower) !== false;
    });
}
?>

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <div class="w-full md:w-40">
                    <label for="status-filter" class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                    <select id="status-filter" name="status" class="w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md">
                        <option value="all">All</option>
                        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="in-progress" <?= $statusFilter === 'in-progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="resolved" <?= $statusFilter === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                    </select>
                </div>
                
                <div class="w-full md:w-48">
                    <label for="category-filter" class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                    <select id="category-filter" name="category" class="w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md">
                        <option value="all">All Categories</option>
                        <option value="road" <?= $categoryFilter === 'road' ? 'selected' : '' ?>>Road Repair</option>
                        <option value="water" <?= $categoryFilter === 'water' ? 'selected' : '' ?>>Water Supply</option>
                        <option value="garbage" <?= $categoryFilter === 'garbage' ? 'selected' : '' ?>>Garbage Disposal</option>
                        <option value="transport" <?= $categoryFilter === 'transport' ? 'selected' : '' ?>>Public Transport</option>
                        <option value="other" <?= $categoryFilter === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
            </div>
            
            <div class="relative flex-1">
                <input type="text" id="search-grievances" name="search" value="<?= htmlspecialchars($searchQuery) ?>" class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Search grievances...">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <?php if (empty($filteredGrievances)): ?>
        <div class="text-center py-8">
            <p class="text-gray-500">No grievances found matching your filters.</p>
        </div>
        <?php else: ?>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <?php if ($isAdminView): ?>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Citizen</th>
                    <?php endif; ?>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Filed</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($filteredGrievances as $grievance): 
                    $statusInfo = getStatusInfo($grievance['status']);
                ?>
                <tr class="hover:bg-gray-100">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">#<?= $grievance['id'] ?></td>
                    <?php if ($isAdminView): ?>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= isset($grievance['userName']) ? htmlspecialchars($grievance['userName']) : 'User #' . $grievance['userId'] ?>
                    </td>
                    <?php endif; ?>
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="view-grievance.php?id=<?= $grievance['id'] ?>" class="text-blue-600 hover:text-blue-900">
                            <?= $isAdminView ? 'Manage' : 'View' ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<script>
    // Filter functionality
    document.getElementById('status-filter').addEventListener('change', applyFilters);
    document.getElementById('category-filter').addEventListener('change', applyFilters);
    
    // Search with delay
    let searchTimeout;
    document.getElementById('search-grievances').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 300);
    });
    
    function applyFilters() {
        const status = document.getElementById('status-filter').value;
        const category = document.getElementById('category-filter').value;
        const search = document.getElementById('search-grievances').value;
        
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('tab', '<?= $activeTab ?>');
        
        if (status !== 'all') {
            currentUrl.searchParams.set('status', status);
        } else {
            currentUrl.searchParams.delete('status');
        }
        
        if (category !== 'all') {
            currentUrl.searchParams.set('category', category);
        } else {
            currentUrl.searchParams.delete('category');
        }
        
        if (search) {
            currentUrl.searchParams.set('search', search);
        } else {
            currentUrl.searchParams.delete('search');
        }
        
        window.location.href = currentUrl.toString();
    }
</script>