<?php
// controllers/TaskController.php - Controller for task-related API endpoints

class TaskController
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Get all tasks
    public function getTasks()
    {
        // Check if filter is applied
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

        // Base query
        $query = "SELECT t.*, u.full_name as assignee_name 
                FROM tasks t 
                LEFT JOIN users u ON t.assignee_id = u.user_id";

        // Apply filter
        switch ($filter) {
            case 'completed':
                $query .= " WHERE t.completed = 1";
                break;
            case 'pending':
                $query .= " WHERE t.completed = 0";
                break;
            case 'high':
                $query .= " WHERE t.priority = 'high'";
                break;
            case 'medium':
                $query .= " WHERE t.priority = 'medium'";
                break;
            case 'low':
                $query .= " WHERE t.priority = 'low'";
                break;
            default:
                // No filter or 'all' - no additional WHERE clause
                break;
        }

        // Order by deadline
        $query .= " ORDER BY t.deadline ASC";

        $result = $this->conn->query($query);

        if ($result) {
            $tasks = [];
            while ($row = $result->fetch_assoc()) {
                $tasks[] = $row;
            }
            echo json_encode(['tasks' => $tasks]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch tasks: ' . $this->conn->error]);
        }
    }

    // Get specific task
    public function getTask($id)
    {
        $id = $this->conn->real_escape_string($id);
        $query = "SELECT t.*, u.full_name as assignee_name 
                FROM tasks t 
                LEFT JOIN users u ON t.assignee_id = u.user_id 
                WHERE t.task_id = '$id'";

        $result = $this->conn->query($query);

        if ($result && $result->num_rows > 0) {
            $task = $result->fetch_assoc();
            echo json_encode(['task' => $task]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Task not found']);
        }
    }

    // Add new task
    public function addTask($data)
    {
        // Validate required fields
        if (!isset($data['title']) || empty($data['title'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Task title is required']);
            return;
        }

        // Prepare data for insertion
        $title = $this->conn->real_escape_string($data['title']);
        $description = isset($data['description']) ? $this->conn->real_escape_string($data['description']) : '';
        $deadline = isset($data['deadline']) && !empty($data['deadline']) ? $this->conn->real_escape_string($data['deadline']) : NULL;
        $priority = isset($data['priority']) ? $this->conn->real_escape_string($data['priority']) : 'medium';
        $assignee_id = isset($data['assignee_id']) && !empty($data['assignee_id']) ? intval($data['assignee_id']) : NULL;

        // Create query
        $query = "INSERT INTO tasks (title, description, deadline, priority, assignee_id) VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssi", $title, $description, $deadline, $priority, $assignee_id);

        if ($stmt->execute()) {
            $task_id = $this->conn->insert_id;

            // Get the created task
            $query = "SELECT t.*, u.full_name as assignee_name 
                    FROM tasks t 
                    LEFT JOIN users u ON t.assignee_id = u.user_id 
                    WHERE t.task_id = $task_id";

            $result = $this->conn->query($query);
            $task = $result->fetch_assoc();

            echo json_encode(['success' => true, 'message' => 'Task added successfully', 'task' => $task]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add task: ' . $stmt->error]);
        }

        $stmt->close();
    }

    // Update task
    public function updateTask($id, $data)
    {
        $id = intval($id);

        // Validate required fields
        if (!isset($data['title']) || empty($data['title'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Task title is required']);
            return;
        }

        // Prepare data for update
        $title = $this->conn->real_escape_string($data['title']);
        $description = isset($data['description']) ? $this->conn->real_escape_string($data['description']) : '';
        $deadline = isset($data['deadline']) && !empty($data['deadline']) ? $this->conn->real_escape_string($data['deadline']) : NULL;
        $priority = isset($data['priority']) ? $this->conn->real_escape_string($data['priority']) : 'medium';
        $completed = isset($data['completed']) ? intval($data['completed']) : 0;
        $assignee_id = isset($data['assignee_id']) && !empty($data['assignee_id']) ? intval($data['assignee_id']) : NULL;

        // Create query
        $query = "UPDATE tasks SET 
                title = ?, 
                description = ?, 
                deadline = ?, 
                priority = ?, 
                completed = ?, 
                assignee_id = ? 
                WHERE task_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssiis", $title, $description, $deadline, $priority, $completed, $assignee_id, $id);

        if ($stmt->execute()) {
            // Get the updated task
            $query = "SELECT t.*, u.full_name as assignee_name 
                    FROM tasks t 
                    LEFT JOIN users u ON t.assignee_id = u.user_id 
                    WHERE t.task_id = $id";

            $result = $this->conn->query($query);
            $task = $result->fetch_assoc();

            echo json_encode(['success' => true, 'message' => 'Task updated successfully', 'task' => $task]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update task: ' . $stmt->error]);
        }

        $stmt->close();
    }

    // Update task status only
    public function updateTaskStatus($id, $data)
    {
        $id = intval($id);

        // Validate required fields
        if (!isset($data['completed'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Task completed status is required']);
            return;
        }

        $completed = intval($data['completed']);

        // Create query
        $query = "UPDATE tasks SET completed = ? WHERE task_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $completed, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Task status updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update task status: ' . $stmt->error]);
        }

        $stmt->close();
    }

    // Delete task
    public function deleteTask($id)
    {
        $id = intval($id);

        // Create query
        $query = "DELETE FROM tasks WHERE task_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete task: ' . $stmt->error]);
        }

        $stmt->close();
    }
}