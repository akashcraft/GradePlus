<?php

if ($_POST["authorize"] == "gradeplus") {
    try {
        // Create a new MySQLi connection
        $conn = new mysqli("localhost", "gradeplusclient", "gradeplussql", "gradeplus");

        // If the connection fails, throw an exception
        if (!$conn) {
            throw new Exception();
        }

        // Retrieve the username and invite code from the POST request
        $username = $_POST['username'];
        $invite_code = $_POST['invite_code'];

        // Prepare a query to retrieve the course details based on the invite code
        $courseQuery = $conn->prepare("
            SELECT course_code, course_name, instructor_name
            FROM courses
            WHERE invite_code = ?
        ");

        $courseQuery->bind_param("s", $invite_code);
        $courseQuery->execute();
        $courseQuery->store_result();

        // If the query returns a result, bind the results to variables
        if ($courseQuery->num_rows > 0) {
            $course = $courseQuery->bind_result($course_code, $course_name, $instructor_name);
            $courseQuery->fetch();
        } else {
            header('Content-Type: application/json');
            echo json_encode(["success" => 0, "error" => 0, "invalid" => 1, "exists" => 0]);
            return;
        }

        // Check if the user is already enrolled in the course
        $enrollmentCheckQuery = $conn->prepare("
        SELECT * FROM enrollment
        WHERE username = ? AND invite_code = ?
        ");

        $enrollmentCheckQuery->bind_param("ss", $username, $invite_code);
        $enrollmentCheckQuery->execute();
        $enrollmentCheckQuery->store_result();

        if ($enrollmentCheckQuery->num_rows > 0) {
            header('Content-Type: application/json');
            echo json_encode(["success" => 0, "error" => 0, "invalid" => 0, "exists" => 1]);
            return;
        }

        $updateSql = $conn->prepare("
            INSERT INTO enrollment VALUES (?, ?, ?, 0, ?, ?)
        ");


        $updateSql->bind_param("sssss", $username, $course_code, $course_name, $invite_code, $instructor_name);
        $result = $updateSql->execute();

        if ($result) {
            $success = 1;
            $error = 0;
        } else {
            $success = 0;
            $error = 1;
        }

    } catch (Exception $e) {
        $success = 0;
        $error = 1;
    }

    // Close the MySQLi connection
    mysqli_close($conn);

    // Send a JSON response back to the client with the success and error status
    header('Content-Type: application/json');
    echo json_encode(["success" => $success, "error" => $error, "invalid" => 0, "exists" => 0]);

} else {
    header("Location: illegal.php");
}
