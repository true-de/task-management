<?php
// controllers/UserController.php - Controller for user-related API endpoints

class UserController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Get all users
    public function getUsers()
    {
        $query = "SELECT user_id, full_name FROM users ORDER BY full_name";
        try {
            $stmt = $this->pdo->query($query);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['users' => $users]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch users: ' . $e->getMessage()]);
        }
    }

    // Get specific user
    public function getUser($id)
    {
        $query = "SELECT user_id, full_name, username FROM users WHERE user_id = :id";

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                echo json_encode(['user' => $user]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch user: ' . $e->getMessage()]);
        }
    }

    // Add new user
    public function addUser($data)
    {
        // Validate required fields
        if (
            !isset($data['full_name']) || empty($data['full_name']) ||
            !isset($data['username']) || empty($data['username']) ||
            !isset($data['password']) || empty($data['password'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Full name, username and password are required']);
            return;
        }

        // Check if username already exists
        $username = $data['username'];
        $checkQuery = "SELECT COUNT(*) as count FROM users WHERE username = :username";

        try {
            $checkStmt = $this->pdo->prepare($checkQuery);
            $checkStmt->bindParam(':username', $username, PDO::PARAM_STR);
            $checkStmt->execute();
            $row = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($row['count'] > 0) {
                http_response_code(409); // Conflict
                echo json_encode(['error' => 'Username already exists']);
                return;
            }

            // Prepare data for insertion
            $fullName = $data['full_name'];
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Create query
            $query = "INSERT INTO users (full_name, username, password) VALUES (:full_name, :username, :password)";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':full_name', $fullName, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $user_id = $this->pdo->lastInsertId();
                echo json_encode([
                    'success' => true,
                    'message' => 'User added successfully',
                    'user' => [
                        'user_id' => $user_id,
                        'full_name' => $fullName,
                        'username' => $username
                    ]
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add user: ' . $e->getMessage()]);
        }
    }

    // Delete user
    public function deleteUser($id)
    {
        // Check if user has any assigned tasks
        $checkQuery = "SELECT COUNT(*) as task_count FROM tasks WHERE assignee_id = :id";

        try {
            $checkStmt = $this->pdo->prepare($checkQuery);
            $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $checkStmt->execute();
            $row = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($row['task_count'] > 0) {
                http_response_code(400); // Bad Request
                echo json_encode(['error' => 'Cannot delete user. User has ' . $row['task_count'] . ' assigned tasks.']);
                return;
            }

            // Delete user
            $query = "DELETE FROM users WHERE user_id = :id";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'User not found']);
                }
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete user: ' . $e->getMessage()]);
        }
    }
}