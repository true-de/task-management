<?php
// API request handler

// Include necessary files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/function/task_functions.php';
require_once __DIR__ . '/function/user_functions.php';

// Handle AJAX requests
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $response = [];

    switch ($action) {
        case 'create_task':
        case 'add_task':
            $result = addTask($pdo, [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'deadline' => $_POST['deadline'],
                'priority' => $_POST['priority'],
                'assignee_id' => $_POST['assignee_id'] ?? $_POST['assignee'] ?? null
            ]);
            $response = [
                'success' => $result,
                'message' => $result ? 'Task created successfully' : 'Error creating task'
            ];
            break;

        case 'update_task':
            $result = updateTask($pdo, $_POST['task_id'], [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'deadline' => $_POST['deadline'],
                'priority' => $_POST['priority'],
                'assignee_id' => $_POST['assignee_id'] ?? $_POST['assignee'] ?? null,
                'completed' => isset($_POST['completed']) ? $_POST['completed'] : 0
            ]);
            $response = [
                'success' => $result,
                'message' => $result ? 'Task updated successfully' : 'Error updating task'
            ];
            break;

        case 'delete_task':
            $result = deleteTask($pdo, $_POST['task_id']);
            $response = [
                'success' => $result,
                'message' => $result ? 'Task deleted successfully' : 'Error deleting task'
            ];
            break;

        case 'toggle_complete':
            $result = toggleTaskComplete($pdo, $_POST['task_id']);
            $response = [
                'success' => $result['success'],
                'message' => $result['success'] ? 'Task status updated successfully' : 'Error updating task status',
                'new_status' => $result['success'] ? $result['new_status'] : null
            ];
            break;

        case 'get_tasks':
            $tasks = getTasks($pdo);
            $response = [
                'success' => true,
                'tasks' => $tasks
            ];
            break;

        case 'get_task':
            $task = getTask($pdo, $_POST['task_id']);
            $response = [
                'success' => true,
                'task' => $task
            ];
            break;

        case 'get_users':
            $users = getUsers($pdo);
            $response = [
                'success' => true,
                'users' => $users
            ];
            break;

        case 'register_user':
            $userData = [
                'full_name' => $_POST['full_name'] ?? '',
                'username' => $_POST['username'] ?? '',
                'password' => $_POST['password'] ?? ''
            ];
            $response = registerUser($pdo, $userData);
            break;

        case 'delete_user':
            $userId = $_POST['user_id'] ?? null;
            $response = deleteUser($pdo, $userId);
            break;

        default:
            $response = [
                'success' => false,
                'message' => 'Invalid action specified'
            ];
            break;
    }

    // Return JSON response and exit
    echo json_encode($response);
    exit;
}