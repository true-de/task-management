<?php
// controllers/TaskController.php - Controller for task-related API endpoints

class TaskController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
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

        try {
            $stmt = $this->pdo->query($query);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['tasks' => $tasks]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch tasks: ' . $e->getMessage()]);
        }
    }

    // Get specific task
    public function getTask($id)
    {
        $query = "SELECT t.*, u.full_name as assignee_name 
                FROM tasks t 
                LEFT JOIN users u ON t.assignee_id = u.user_id 
                WHERE t.task_id = :id";

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $task = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($task) {
                echo json_encode(['task' => $task]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Task not found']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch task: ' . $e->getMessage()]);
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
        $title = $data['title'];
        $description = isset($data['description']) ? $data['description'] : '';
        $deadline = isset($data['deadline']) && !empty($data['deadline']) ? $data['deadline'] : NULL;
        $priority = isset($data['priority']) ? $data['priority'] : 'medium';
        $assignee_id = isset($data['assignee_id']) && !empty($data['assignee_id']) ? $data['assignee_id'] : NULL;

        // Create query
        $query = "INSERT INTO tasks (title, description, deadline, priority, assignee_id) VALUES (:title, :description, :deadline, :priority, :assignee_id)";

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':deadline', $deadline, PDO::PARAM_STR);
            $stmt->bindParam(':priority', $priority, PDO::PARAM_STR);
            $stmt->bindParam(':assignee_id', $assignee_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $task_id = $this->pdo->lastInsertId();

                // Get the created task
                $query = "SELECT t.*, u.full_name as assignee_name 
                        FROM tasks t 
                        LEFT JOIN users u ON t.assignee_id = u.user_id 
                        WHERE t.task_id = :task_id";

                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':task_id', $task_id, PDO::PARAM_INT);
                $stmt->execute();
                $task = $stmt->fetch(PDO::FETCH_ASSOC);

                echo json_encode(['success' => true, 'message' => 'Task added successfully', 'task' => $task]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add task: ' . $e->getMessage()]);
        }
    }

    // Update task
    public function updateTask($id, $data)
    {
        // Validate required fields
        if (!isset($data['title']) || empty($data['title'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Task title is required']);
            return;
        }

        // Prepare data for update
        $title = $data['title'];
        $description = isset($data['description']) ? $data['description'] : '';
        $deadline = isset($data['deadline']) && !empty($data['deadline']) ? $data['deadline'] : NULL;
        $priority = isset($data['priority']) ? $data['priority'] : 'medium';
        $completed = isset($data['completed']) ? intval($data['completed']) : 0;
        $assignee_id = isset($data['assignee_id']) && !empty($data['assignee_id']) ? $data['assignee_id'] : NULL;

        // Create query
        $query = "UPDATE tasks SET 
                title = :title, 
                description = :description, 
                deadline = :deadline, 
                priority = :priority, 
                completed = :completed, 
                assignee_id = :assignee_id 
                WHERE task_id = :id";

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':deadline', $deadline, PDO::PARAM_STR);
            $stmt->bindParam(':priority', $priority, PDO::PARAM_STR);
            $stmt->bindParam(':completed', $completed, PDO::PARAM_INT);
            $stmt->bindParam(':assignee_id', $assignee_id, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Get the updated task
                $query = "SELECT t.*, u.full_name as assignee_name 
                        FROM tasks t 
                        LEFT JOIN users u ON t.assignee_id = u.user_id 
                        WHERE t.task_id = :id";

                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $task = $stmt->fetch(PDO::FETCH_ASSOC);

                echo json_encode(['success' => true, 'message' => 'Task updated successfully', 'task' => $task]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update task: ' . $e->getMessage()]);
        }
    }

    // Update task status only
    public function updateTaskStatus($id, $data)
    {
        // Validate required fields
        if (!isset($data['completed'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Task completed status is required']);
            return;
        }

        $completed = intval($data['completed']);

        // Create query
        $query = "UPDATE tasks SET completed = :completed WHERE task_id = :id";

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':completed', $completed, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Task status updated successfully']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update task status: ' . $e->getMessage()]);
        }
    }

    // Delete task
    public function deleteTask($id)
    {
        // Create query
        $query = "DELETE FROM tasks WHERE task_id = :id";

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete task: ' . $e->getMessage()]);
        }
    }
}