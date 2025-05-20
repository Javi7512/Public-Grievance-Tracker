<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'grievance_track');

// Application settings
define('SITE_TITLE', 'Public Grievance Tracker');
define('UPLOAD_DIR', 'uploads/');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>