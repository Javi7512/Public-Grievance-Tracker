<?php
$uploadError = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_grievance'])) {
    $category = $_POST['category'] ?? '';
    $description = $_POST['description'] ?? '';
    $location = $_POST['location'] ?? '';
    
    // Validate input
    if (empty($category) || empty($description) || empty($location)) {
        $uploadError = 'Please fill in all required fields.';
    } else {
        // Handle file upload if provided
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = uploadImage($_FILES['image']);
            if (!$imagePath) {
                // uploadError is set in the uploadImage function
            }
        }
        
        if (empty($uploadError)) {
            $result = createGrievance($user['id'], $category, $description, $location, $imagePath);
            if ($result['success']) {
                $success = true;
            } else {
                $uploadError = $result['message'];
            }
        }
    }
}
?>

<div class="bg-white rounded-lg shadow p-6">
    <?php if ($success): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">Your grievance has been submitted successfully.</span>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($uploadError)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline"><?= htmlspecialchars($uploadError) ?></span>
    </div>
    <?php endif; ?>
    
    <form method="POST" action="dashboard.php?tab=file-grievance" enctype="multipart/form-data" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label for="category" class="block text-sm font-medium text-gray-700">Grievance Category *</label>
                <select id="category" name="category" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md">
                    <option value="">Select a category</option>
                    <option value="road">Road Repair</option>
                    <option value="water">Water Supply</option>
                    <option value="garbage">Garbage Disposal</option>
                    <option value="transport">Public Transport</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="space-y-2">
                <label for="location" class="block text-sm font-medium text-gray-700">Location *</label>
                <input type="text" id="location" name="location" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Specific address or area">
            </div>
        </div>

        <div class="space-y-2">
            <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
            <textarea id="description" name="description" rows="5" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Provide detailed information about your grievance"></textarea>
        </div>

        <div class="space-y-2">
            <label for="image" class="block text-sm font-medium text-gray-700">Upload Image (optional)</label>
            <div class="border-2 border-dashed border-gray-300 rounded-md p-4 text-center">
                <input id="image" name="image" type="file" accept="image/*" class="hidden">
                <label for="image" class="cursor-pointer block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Click to upload an image</p>
                    <p class="text-xs text-gray-400">(Max file size: 5MB, Formats: JPG, PNG)</p>
                </label>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <button type="reset" class="py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </button>
            <button type="submit" name="submit_grievance" class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Submit Grievance
            </button>
        </div>
    </form>
</div>