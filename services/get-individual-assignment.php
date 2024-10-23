<?php

require '../config.php';

header('Content-Type: application/json');

function sendResponse($success, $error, $data = [], $message = "") {
    echo json_encode([
        "success" => $success,
        "error" => $error,
        "illegal" => 0,
        "data" => $data,
        "message" => $message
    ]);
    exit();
}

// Validate input parameters
if (empty($_POST["authorize"]) || empty($_POST["assignment_id"])) {
    sendResponse(0, 1, [], "Missing or empty parameters.");
}

// Verify authorization
if (htmlspecialchars($_POST["authorize"]) !== "gradeplus") {
    header("Location: illegal.php");
    exit();
}

// Establish a new MySQLi connection
$conn = new mysqli($DB_HOST, 'gradeplusclient', 'gradeplussql', 'gradeplus');

// Check for connection errors
if ($conn->connect_error) {
    sendResponse(0, 1, [], "Connection failed: " . $conn->connect_error);
}

// Sanitize the assignment ID
$assignment_id = intval($_POST["assignment_id"]);

// Prepare SQL query to retrieve the assignment by ID
$sql = "
SELECT 
    assignment_name,
    assignment_file,
    description,
    due_date,
    instructor
FROM 
    assignment
WHERE 
    assignment_id = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    sendResponse(0, 1, [], "Preparation failed: " . $conn->error);
}

// Bind assignment ID parameter and execute the query
$stmt->bind_param("i", $assignment_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    sendResponse(0, 1, [], "No assignment found with ID: $assignment_id");
}

// Fetch the assignment data
$row = $result->fetch_assoc();
$assignment = [
    "assignment_name" => $row['assignment_name'],
    "assignment_file" => $row['assignment_file'] ? base64_encode($row['assignment_file']) : null,
    "description" => $row['description'],
    "due_date" => $row['due_date'],
    "instructor" => $row['instructor']
];

// Close resources
$stmt->close();
$conn->close();

// Send the successful response
sendResponse(1, 0, $assignment, "Assignment retrieved successfully.");

?>