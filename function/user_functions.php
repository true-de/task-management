<?php
// Functions for user management

// Function to fetch all users
function getUsers($pdo)
{
    $stmt = $pdo->query("SELECT user_id, username, full_name FROM users ORDER BY full_name");
    return $stmt->fetchAll();
}

// Function to register a new user
function registerUser($pdo, $userData)
{
    // Validate required fields
    if (empty($userData['full_name']) || empty($userData['username']) || empty($userData['password'])) {
        return [
            'success' => false,
            'message' => 'All fields are required.'
        ];
    }

    // Check if username already exists
    $checkStmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
    $checkStmt->execute([$userData['username']]);
    $result = $checkStmt->fetch();

    if ($result['count'] > 0) {
        return [
            'success' => false,
            'message' => 'Username already exists. Please choose a different username.'
        ];
    }

    // Hash password
    $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (full_name, username, password) VALUES (?, ?, ?)");
    $result = $stmt->execute([
        $userData['full_name'],
        $userData['username'],
        $hashedPassword
    ]);

    return [
        'success' => $result,
        'message' => $result ? 'User registered successfully' : 'Error registering user'
    ];
}

// Function to delete a user
function deleteUser($pdo, $userId)
{
    // Validate user ID
    if (empty($userId)) {
        return [
            'success' => false,
            'message' => 'User ID is required.'
        ];
    }

    // Check if user has assigned tasks
    $checkStmt = $pdo->prepare("SELECT COUNT(*) as task_count FROM tasks WHERE assignee_id = ?");
    $checkStmt->execute([$userId]);
    $result = $checkStmt->fetch();

    if ($result['task_count'] > 0) {
        return [
            'success' => false,
            'message' => "Cannot delete user. User has {$result['task_count']} assigned tasks."
        ];
    }

    // Delete the user
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $result = $stmt->execute([$userId]);

    return [
        'success' => $result,
        'message' => $result ? 'User deleted successfully' : 'Error deleting user'
    ];
}