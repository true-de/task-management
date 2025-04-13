<?php
/**
 * Main process controller for the Task Management System
 * 
 * This file serves as the main entry point for both API requests
 * and normal page loads. It includes the appropriate components
 * based on the request type.
 */

// For API requests
if (isset($_POST['action'])) {
    // Handle API requests through the API handler
    require_once 'api_handler.php';
}
// For normal page loads
else {
    // Include config and function files
    require_once __DIR__ . '/config.php';
    require_once __DIR__ . '/function/task_functions.php';
    require_once __DIR__ . '/function/user_functions.php';


    // Get initial data for the page render
    $users = getUsers($pdo);
    $allTasks = getTasks($pdo);

    // Include the main view template
    include "index.php";
}