<?php
include "db_connection.php";

// check for error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Fetch all tasks from database
$allTasks = [];
$query = "SELECT t.*, u.full_name as assignee_name
          FROM tasks t
          LEFT JOIN users u ON t.assignee_id = u.user_id
          ORDER BY t.deadline ASC";

$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $allTasks[] = $row;
    }
}

// Fetch all users for assignment dropdown - UPDATED to include username
$users = [];
$userQuery = "SELECT user_id, full_name, username FROM users ORDER BY full_name";
$userResult = $conn->query($userQuery);
if ($userResult) {
    while ($row = $userResult->fetch_assoc()) {
        $users[] = $row;
    }
}


// <!-- register nad delete user -->
function delete_user($conn, $user_id)
{
    // Validate user_id
    if (!is_numeric($user_id)) {
        return "Invalid user ID.";
    }

    // First check if user has any assigned tasks
    $checkQuery = "SELECT COUNT(*) as task_count FROM tasks WHERE assignee_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['task_count'] > 0) {
        return "Cannot delete user. User has " . $row['task_count'] . " assigned tasks.";
    }

    // Prepare and execute the delete query
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            return "User deleted successfully.";
        } else {
            return "User not found.";
        }
    } else {
        return "Failed to delete user: " . $conn->error;
    }
}

$message = "";
if (isset($_GET['delete_user_id'])) {
    $user_id = intval($_GET['delete_user_id']);
    $message = delete_user($conn, $user_id);
}
// Call register_user when form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register_user"])) {
    $full_name = trim($_POST["full_name"]);
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // Validate inputs
    if (!empty($full_name) && !empty($username) && !empty($password)) {
        // Check if username already exists
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            $message = "Username already exists. Please choose a different username.";
        } else {
            // Save user to database
            $stmt = $conn->prepare("INSERT INTO users (full_name, username, password) VALUES (?, ?, ?)");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("sss", $full_name, $username, $hashed_password);

            if ($stmt->execute()) {
                $message = "User registered successfully.";
                // Refresh the page to update the user list
                header("Location: " . $_SERVER['PHP_SELF'] . "?registration_success=1");
                exit;
            } else {
                $message = "Registration failed: " . $conn->error;
            }
        }
    } else {
        $message = "Please fill in all fields.";
    }
}

if (isset($_GET['registration_success'])) {
    $message = "User registered successfully.";
}

// Include the HTML components
include "html/header.php";
include "html/navigation.php";
include "html/all_tasks_tab.php";
include "html/add_task_tab.php";
include "html/user_management_tab.php";
echo "</div>"; // Close the tab-content div
include "html/modals.php";
include "html/footer.php";
?>