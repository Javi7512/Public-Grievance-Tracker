<?php
require_once 'db.php';

// Get all grievances (admin) or user's grievances (regular user)
function getGrievances($userId = null, $isAdmin = false) {
    $conn = getDbConnection();
    
    if ($isAdmin) {
        $stmt = $conn->prepare("SELECT g.*, u.name as userName FROM grievances g JOIN users u ON g.userId = u.id ORDER BY g.dateFiled DESC");
    } else {
        $stmt = $conn->prepare("SELECT * FROM grievances WHERE userId = ? ORDER BY dateFiled DESC");
        $stmt->bind_param("i", $userId);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $grievances = [];
    while ($row = $result->fetch_assoc()) {
        $grievances[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    return $grievances;
}

// Get a single grievance by ID
function getGrievanceById($grievanceId, $userId = null, $isAdmin = false) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT g.*, u.name as userName FROM grievances g JOIN users u ON g.userId = u.id WHERE g.id = ?");
    $stmt->bind_param("i", $grievanceId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $grievance = $result->fetch_assoc();
        
        // Check if user has access to this grievance
        if (!$isAdmin && $grievance['userId'] != $userId) {
            $stmt->close();
            $conn->close();
            return null;
        }
        
        $stmt->close();
        $conn->close();
        return $grievance;
    }
    
    $stmt->close();
    $conn->close();
    return null;
}

// Create a new grievance
function createGrievance($userId, $category, $description, $location, $imagePath = null) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("INSERT INTO grievances (userId, category, description, location, imagePath) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $userId, $category, $description, $location, $imagePath);
    
    if ($stmt->execute()) {
        $grievanceId = $conn->insert_id;
        $stmt->close();
        $conn->close();
        return ["success" => true, "grievance_id" => $grievanceId];
    }
    
    $stmt->close();
    $conn->close();
    return ["success" => false, "message" => "Failed to create grievance"];
}

// Update a grievance
function updateGrievance($grievanceId, $status, $department) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("UPDATE grievances SET status = ?, department = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $department, $grievanceId);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return ["success" => true];
    }
    
    $stmt->close();
    $conn->close();
    return ["success" => false, "message" => "Failed to update grievance"];
}

// Get responses for a grievance
function getResponses($grievanceId) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT r.*, u.name as adminName FROM responses r JOIN users u ON r.adminId = u.id WHERE r.grievanceId = ? ORDER BY r.timestamp ASC");
    $stmt->bind_param("i", $grievanceId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $responses = [];
    while ($row = $result->fetch_assoc()) {
        $responses[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    return $responses;
}

// Add a response to a grievance
function addResponse($grievanceId, $adminId, $comment, $statusUpdated, $department) {
    $conn = getDbConnection();
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Add response
        $stmt = $conn->prepare("INSERT INTO responses (grievanceId, adminId, comment, statusUpdated, department) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $grievanceId, $adminId, $comment, $statusUpdated, $department);
        $stmt->execute();
        
        // Update grievance status and department
        $updateStmt = $conn->prepare("UPDATE grievances SET status = ?, department = ? WHERE id = ?");
        $updateStmt->bind_param("ssi", $statusUpdated, $department, $grievanceId);
        $updateStmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        $responseId = $conn->insert_id;
        $stmt->close();
        $updateStmt->close();
        $conn->close();
        
        return ["success" => true, "response_id" => $responseId];
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $conn->close();
        return ["success" => false, "message" => "Failed to add response: " . $e->getMessage()];
    }
}

// Get statistics for dashboard
function getStatistics($userId = null, $isAdmin = false) {
    $conn = getDbConnection();
    
    if ($isAdmin) {
        $totalQuery = "SELECT COUNT(*) AS total FROM grievances";
        $pendingQuery = "SELECT COUNT(*) AS pending FROM grievances WHERE status = 'pending'";
        $inProgressQuery = "SELECT COUNT(*) AS inProgress FROM grievances WHERE status = 'in-progress'";
        $resolvedQuery = "SELECT COUNT(*) AS resolved FROM grievances WHERE status = 'resolved'";
    } else {
        $totalQuery = "SELECT COUNT(*) AS total FROM grievances WHERE userId = $userId";
        $pendingQuery = "SELECT COUNT(*) AS pending FROM grievances WHERE userId = $userId AND status = 'pending'";
        $inProgressQuery = "SELECT COUNT(*) AS inProgress FROM grievances WHERE userId = $userId AND status = 'in-progress'";
        $resolvedQuery = "SELECT COUNT(*) AS resolved FROM grievances WHERE userId = $userId AND status = 'resolved'";
    }
    
    $total = $conn->query($totalQuery)->fetch_assoc()['total'];
    $pending = $conn->query($pendingQuery)->fetch_assoc()['pending'];
    $inProgress = $conn->query($inProgressQuery)->fetch_assoc()['inProgress'];
    $resolved = $conn->query($resolvedQuery)->fetch_assoc()['resolved'];
    
    $conn->close();
    
    return [
        'total' => $total,
        'pending' => $pending,
        'inProgress' => $inProgress,
        'resolved' => $resolved
    ];
}

// Upload image for grievance
function uploadImage($file) {
    global $uploadError;
    
    // Check if upload directory exists, create if not
    if (!file_exists(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }
    
    // Check file size
    if ($file['size'] > 5 * 1024 * 1024) {
        $uploadError = "File is too large. Maximum size is 5MB.";
        return false;
    }
    
    // Check file type
    $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $uploadError = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        return false;
    }
    
    // Generate a unique filename
    $fileName = "image-" . time() . "-" . mt_rand(1000000, 9999999) . "." . $fileType;
    $targetFile = UPLOAD_DIR . $fileName;
    
    // Upload the file
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return $targetFile;
    } else {
        $uploadError = "Failed to upload file.";
        return false;
    }
}

// Get category label
function getCategoryLabel($category) {
    $categories = [
        'road' => 'Road Repair',
        'water' => 'Water Supply',
        'garbage' => 'Garbage Disposal',
        'transport' => 'Public Transport',
        'other' => 'Other'
    ];
    
    return isset($categories[$category]) ? $categories[$category] : $category;
}

// Get department label
function getDepartmentLabel($department) {
    $departments = [
        'public-works' => 'Public Works Department',
        'water-authority' => 'Water Authority',
        'sanitation' => 'Sanitation Department',
        'transport-authority' => 'Transport Authority',
        'municipal-office' => 'Municipal Office',
        'other' => 'Other'
    ];
    
    return isset($departments[$department]) ? $departments[$department] : $department;
}

// Get status label and class
function getStatusInfo($status) {
    switch ($status) {
        case 'pending':
            return [
                'label' => 'Pending',
                'bgColor' => 'bg-red-100',
                'textColor' => 'text-red-800'
            ];
        case 'in-progress':
            return [
                'label' => 'In Progress',
                'bgColor' => 'bg-yellow-100',
                'textColor' => 'text-yellow-800'
            ];
        case 'resolved':
            return [
                'label' => 'Resolved',
                'bgColor' => 'bg-green-100',
                'textColor' => 'text-green-800'
            ];
        default:
            return [
                'label' => ucfirst($status),
                'bgColor' => 'bg-gray-100',
                'textColor' => 'text-gray-800'
            ];
    }
}
?>