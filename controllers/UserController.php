<?php
// controllers/UserController.php - Controller for user-related API endpoints

class UserController
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Get all users
    public function getUsers()
    {
        $query = "SELECT user_id, full_name FROM users ORDER BY full_name";
        $result = $this->conn->query($query);

        if ($result) {
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode(['users' => $users]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch users: ' . $this->conn->error]);
        }
    }

    // Add methods for user management as needed

    // Get specific user
    public function getUser($id)
    {
        $id = $this->conn->real_escape_string($id);
        $query = "SELECT user_id, full_name, username FROM users WHERE user_id = '$id'";

        $result = $this->conn->query($query);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo json_encode(['user' => $user]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
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
        $username = $this->conn->real_escape_string($data['username']);
        $checkQuery = "SELECT COUNT(*) as count FROM users WHERE username = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            http_response_code(409); // Conflict
            echo json_encode(['error' => 'Username already exists']);
            $checkStmt->close();
            return;
        }
        $checkStmt->close();

        // Prepare data for insertion
        $fullName = $this->conn->real_escape_string($data['full_name']);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Create query
        $query = "INSERT INTO users (full_name, username, password) VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $fullName, $username, $hashedPassword);

        if ($stmt->execute()) {
            $user_id = $this->conn->insert_id;
            echo json_encode([
                'success' => true,
                'message' => 'User added successfully',
                'user' => [
                    'user_id' => $user_id,
                    'full_name' => $fullName,
                    'username' => $username
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add user: ' . $stmt->error]);
        }

        $stmt->close();
    }

    // Delete user
    public function deleteUser($id)
    {
        $id = intval($id);

        // Check if user has any assigned tasks
        $checkQuery = "SELECT COUNT(*) as task_count FROM tasks WHERE assignee_id = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();
        $checkStmt->close();

        if ($row['task_count'] > 0) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Cannot delete user. User has ' . $row['task_count'] . ' assigned tasks.']);
            return;
        }

        // Delete user
        $query = "DELETE FROM users WHERE user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete user: ' . $stmt->error]);
        }

        $stmt->close();
    }
}