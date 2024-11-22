<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the roll number and name from the request
$rollNo = isset($_POST['roll_no']) ? trim($_POST['roll_no']) : '';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM students WHERE roll_number = ? AND name = ?");
$stmt->bind_param("ss", $rollNo, $name);

// Execute and check the result
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        echo 'valid'; // Student exists
    } else {
        echo 'invalid'; // Student does not exist
    }
} else {
    echo 'error'; // Error executing query
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
