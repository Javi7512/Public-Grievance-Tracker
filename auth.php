<?php
require_once 'db.php';

// Register a new user
function registerUser($username, $name, $email, $password) {
    $conn = getDbConnection();
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return ["success" => false, "message" => "Username already exists"];
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return ["success" => false, "message" => "Email already in use"];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $name, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        $userId = $conn->insert_id;
        
        // Set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'user';
        
        $stmt->close();
        $conn->close();
        return ["success" => true, "user_id" => $userId];
    } else {
        $stmt->close();
        $conn->close();
        return ["success" => false, "message" => "Registration failed"];
    }
}

// Login a user
function loginUser($username, $password) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            $stmt->close();
            $conn->close();
            return ["success" => true, "user" => $user];
        }
    }
    
    $stmt->close();
    $conn->close();
    return ["success" => false, "message" => "Invalid username or password"];
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Logout user
function logoutUser() {
    session_unset();
    session_destroy();
    return true;
}

// Get current user data
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT id, username, name, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user;
    }
    
    $stmt->close();
    $conn->close();
    return null;
}
?>