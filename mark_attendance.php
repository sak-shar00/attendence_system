<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_system";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get roll number and name from the URL and trim any leading/trailing spaces
$rollNo = isset($_GET['roll_no']) ? trim($_GET['roll_no']) : '';
$name = isset($_GET['name']) ? trim($_GET['name']) : '';

if (empty($rollNo) || empty($name)) {
    echo "Roll number or name cannot be empty.";
    exit();
}

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM students WHERE roll_number = ? AND name = ?");
$stmt->bind_param("ss", $rollNo, $name);

// Execute and check the result
if ($stmt->execute()) {
    $result = $stmt->get_result();
    
    // Check if the roll number and name exist
    if ($result && $result->num_rows > 0) {
        // If the roll number exists, mark attendance
        $sql_insert = "INSERT INTO attendance (roll_number, attendance_time, status) 
                       VALUES (?, CURRENT_TIMESTAMP, 'Present') 
                       ON DUPLICATE KEY UPDATE attendance_time = CURRENT_TIMESTAMP, status = 'Present'";
        
        // Prepare the insert statement
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("s", $rollNo);
        
        if ($stmt_insert->execute()) {
            echo "Attendance marked for Roll Number: $rollNo at " . date("Y-m-d H:i:s");
        } else {
            echo "Error marking attendance: " . $stmt_insert->error;
        }
        
        $stmt_insert->close();
    } else {
        echo "Invalid Roll Number or Name. Attendance not marked.";
    }
} else {
    echo "Error executing query: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
