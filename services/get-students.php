<?php
// File: /services/get_students.php

// Set the content type to JSON
header('Content-Type: application/json');

require '../config.php';

// Shared secret for authorization
if (empty($_POST["authorize"]) || empty($_POST["course_code"])) {
    sendResponse(0, 1, [], "Missing or empty parameters.");
}

// Verify authorization
if (htmlspecialchars($_POST["authorize"]) !== "gradeplus") {
    header("Location: illegal.php");
    exit();
}

// Create a new MySQLi connection
$conn = new mysqli($DB_HOST, 'gradeplusclient', 'gradeplussql', 'gradeplus');

// Check for connection errors
if ($conn->connect_error) {
    sendResponse(0, 1, [], "Connection failed: " . $conn->connect_error);
}

// Bind the course_code variable
$course_code = htmlspecialchars($_POST["course_code"]);

// Check if the course exists
$checkCourseSql = "SELECT COUNT(*) FROM courses WHERE course_code = ?";
$checkCourseStmt = $conn->prepare($checkCourseSql);
if (!$checkCourseStmt) {
    sendResponse(0, 1, [], "Preparation failed for course check: " . $conn->error);
}

$checkCourseStmt->bind_param("s", $course_code);
$checkCourseStmt->execute();
$checkCourseStmt->bind_result($courseExists);
$checkCourseStmt->fetch();
$checkCourseStmt->close();

// If the course does not exist, send an error response
if ($courseExists == 0) {
    sendResponse(0, 1, [], "Course code '$course_code' does not exist.");
}

// Prepare SQL query to fetch student information
$sql = "
SELECT 
    login.profilePicture,
    login.dname,
    login.username
FROM 
    login
JOIN 
    enrollment ON login.username = enrollment.username
WHERE 
    enrollment.course_code = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    sendResponse(0, 1, [], "Preparation failed: " . $conn->error);
}

// Bind parameter and execute the query
$stmt->bind_param("s", $course_code);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    sendResponse(0, 1, [], "Query failed: " . $conn->error);
}

// Fetch data and build the response
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = [
        "profilePicture" => base64_encode($row['profilePicture']),
        "dname" => $row['dname'],
        "username" => $row['username']
    ];
}

// Close resources
$stmt->close();
$conn->close();

// Send the successful response
sendResponse(1, 0, $students, "Students retrieved successfully.");

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
?>
