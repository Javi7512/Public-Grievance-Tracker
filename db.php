<?php
require_once 'config.php';

function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Create the database tables if they don't exist
function setupDatabase() {
    $conn = getDbConnection();
    
    // Create users table
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user'
    )");
    
    // Create grievances table
    $conn->query("CREATE TABLE IF NOT EXISTS grievances (
        id INT AUTO_INCREMENT PRIMARY KEY,
        userId INT NOT NULL,
        category VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        location VARCHAR(255) NOT NULL,
        dateFiled DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'in-progress', 'resolved') DEFAULT 'pending',
        department VARCHAR(100),
        imagePath VARCHAR(255),
        FOREIGN KEY (userId) REFERENCES users(id)
    )");
    
    // Create responses table
    $conn->query("CREATE TABLE IF NOT EXISTS responses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        grievanceId INT NOT NULL,
        adminId INT NOT NULL,
        comment TEXT NOT NULL,
        statusUpdated ENUM('pending', 'in-progress', 'resolved') NOT NULL,
        department VARCHAR(100) NOT NULL,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (grievanceId) REFERENCES grievances(id),
        FOREIGN KEY (adminId) REFERENCES users(id)
    )");
    
    // Create admin user if not exists
    $result = $conn->query("SELECT * FROM users WHERE username = 'admin'");
    if ($result->num_rows == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (username, name, email, password, role) 
                     VALUES ('admin', 'Admin User', 'admin@example.com', '$hashedPassword', 'admin')");
    }
    
    $conn->close();
}

// Call setup on include
setupDatabase();
?>