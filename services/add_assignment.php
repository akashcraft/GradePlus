<?php

$success = 0;
$invalid_course = 0;
$error = 0;
$_POST["authorize"] = "gradeplus";
if ($_POST["authorize"] == "gradeplus") {
    try {
        $conn = new mysqli("localhost", "gradeplusclient", "gradeplussql", "gradeplus");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get form data
            $course_code = $_POST['course_code'];
            $assignment_name = $_POST['assignment_name'];
            $description = $_POST['description'];
            $due_date = $_POST['due_date'];
            $instructor = $_POST['instructor'];

            // File upload handling
            if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == UPLOAD_ERR_OK) {
                // Read the file content
                $file_temp_path = $_FILES['assignment_file']['tmp_name'];
                $file_content = file_get_contents($file_temp_path);
                $file_content = mysqli_real_escape_string($conn, $file_content);
            } else {
                $file_content = NULL;
            }

            // Verify if the instructor owns the course
            $verifyInstructorSql = "SELECT * FROM courses WHERE course_code = ? AND instructor_name = ?";
            $stmt = $conn->prepare($verifyInstructorSql);
            $stmt->bind_param("ss", $course_code, $instructor);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Instructor owns the course, execute SQL query
                $stmt->close();
                $stmt = $conn->prepare("INSERT INTO assignment (course_code, assignment_name, assignment_file, description, due_date, instructor) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $course_code, $assignment_name, $file_content, $description, $due_date, $instructor);
                if ($stmt->execute()) {
                    $success = 1;
                } else {
                    $error = 1;
                }
            } else {
                $invalid_course = 1;
            }
            $stmt->close();
        }
        $conn->close();

    } catch (Exception $e) {
        $success = 0;
        $error = 1;
    }
} else {
    header("Location: illegal.php");
}

header('Content-Type: application/json');
echo json_encode(["success" => $success,"invalid_course" => $invalid_course,"error" => $error]);
?>