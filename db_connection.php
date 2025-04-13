<?php
// Database configuration
$host = 'localhost'; // or your database host
$dbname = 'task_management'; // your database name
$username = 'root'; // your database username
$password = ''; // your database password

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>