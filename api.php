<?php
// api.php - Main router for Task Management System API

// Database connection
require_once 'config.php'; // Using config.php instead of db_connection.php
require_once 'controllers/TaskController.php';
require_once 'controllers/UserController.php';

// Error reporting configuration
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set headers for JSON API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get endpoint
$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';

// Initialize controllers with PDO connection
$taskController = new TaskController($pdo);
$userController = new UserController($pdo);

// Process request based on method and endpoint
switch ($method) {
    case 'GET':
        // Handle GET requests
        if ($endpoint === 'tasks') {
            // Get all tasks
            $taskController->getTasks();
        } elseif ($endpoint === 'task' && isset($_GET['id'])) {
            // Get specific task
            $taskController->getTask($_GET['id']);
        } elseif ($endpoint === 'users') {
            // Get all users
            $userController->getUsers();
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
        break;

    case 'POST':
        // Handle POST requests
        $data = json_decode(file_get_contents('php://input'), true);

        if ($endpoint === 'tasks') {
            // Add new task
            $taskController->addTask($data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
        break;

    case 'PUT':
        // Handle PUT requests
        $data = json_decode(file_get_contents('php://input'), true);

        if ($endpoint === 'task' && isset($_GET['id'])) {
            // Update task
            $taskController->updateTask($_GET['id'], $data);
        } elseif ($endpoint === 'task-status' && isset($_GET['id'])) {
            // Update task status only
            $taskController->updateTaskStatus($_GET['id'], $data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
        break;

    case 'DELETE':
        // Handle DELETE requests
        if ($endpoint === 'task' && isset($_GET['id'])) {
            // Delete task
            $taskController->deleteTask($_GET['id']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}