<?php
// db.php - Database connection setup for CASA & CO. Membership System

error_reporting(0);
ini_set('display_errors', 0);

// Customize these settings for your cPanel / MySQL environment
$db_host = 'localhost';
$db_name = 'pdadmin_membership';
$db_user = 'pdadmin_membershipadmin';
$db_pass = 'Wohenshuai123!';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // If the database connection fails, return JSON or display error
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}
