<?php
// Database connection setup and configuration
include "db_connection.php";

// Error reporting configuration
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set JSON content type for API responses
header('Content-Type: application/json');

// Establish PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode([
        'success' => false,
        'message' => "Database Connection Failed: " . $e->getMessage()
    ]));
}