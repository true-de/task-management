<?php
// Functions for task management

// Function to fetch all tasks
function getTasks($pdo)
{
    $stmt = $pdo->query("SELECT t.*, u.full_name as assignee_name 
                         FROM tasks t 
                         LEFT JOIN users u ON t.assignee_id = u.user_id 
                         ORDER BY t.deadline ASC, t.priority DESC");
    return $stmt->fetchAll();
}

// Function to fetch a single task
function getTask($pdo, $taskId)
{
    $stmt = $pdo->prepare("SELECT t.*, u.full_name as assignee_name 
                          FROM tasks t 
                          LEFT JOIN users u ON t.assignee_id = u.user_id 
                          WHERE t.task_id = ?");
    $stmt->execute([$taskId]);
    return $stmt->fetch();
}

// Function to add a new task
function addTask($pdo, $taskData)
{
    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, deadline, priority, assignee_id, created_at) 
                           VALUES (?, ?, ?, ?, ?, NOW())");
    return $stmt->execute([
        $taskData['title'],
        $taskData['description'],
        $taskData['deadline'],
        $taskData['priority'],
        $taskData['assignee_id'] ? $taskData['assignee_id'] : null
    ]);
}

// Function to update a task
function updateTask($pdo, $taskId, $taskData)
{
    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, deadline = ?, 
                           priority = ?, assignee_id = ?, completed = ?, updated_at = NOW()
                           WHERE task_id = ?");
    return $stmt->execute([
        $taskData['title'],
        $taskData['description'],
        $taskData['deadline'],
        $taskData['priority'],
        $taskData['assignee_id'] ? $taskData['assignee_id'] : null,
        $taskData['completed'] ? 1 : 0,
        $taskId
    ]);
}

// Function to delete a task
function deleteTask($pdo, $taskId)
{
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE task_id = ?");
    return $stmt->execute([$taskId]);
}

// Function to toggle task complete status
function toggleTaskComplete($pdo, $taskId)
{
    // First get the current status
    $stmt = $pdo->prepare("SELECT completed FROM tasks WHERE task_id = ?");
    $stmt->execute([$taskId]);
    $task = $stmt->fetch();

    if (!$task) {
        return false;
    }

    // Toggle the status
    $newStatus = $task['completed'] ? 0 : 1;

    // Update the status
    $stmt = $pdo->prepare("UPDATE tasks SET completed = ?, updated_at = NOW() WHERE task_id = ?");
    $result = $stmt->execute([$newStatus, $taskId]);

    return [
        'success' => $result,
        'new_status' => $newStatus
    ];
}