<?php

if ($_POST["authorize"] == "gradeplus") {
    try {
        // Create a new MySQLi connection
        $conn = new mysqli("localhost", "gradeplusclient", "gradeplussql", "gradeplus");

        // If the connection fails, throw an exception
        if (!$conn) {
            throw new Exception();
        }

        $username = $_POST['username'];


        $retrieveSql = $conn->prepare("
        SELECT 
        c.course_code,
        c.course_name, 
        c.invite_code,
        c.course_banner,
        e.pinned,
        c.instructor_name
        FROM courses c
        JOIN enrollment e
        ON c.invite_code = e.invite_code
        WHERE e.username = ?
    ");

        $retrieveSql->bind_param("s", $username);

        $result = $retrieveSql->execute();

        if ($result) {
            $retrieveSql->store_result();
            $retrieveSql->bind_result($course_code, $course_name, $invite_code, $course_banner, $pinned, $instructor_name);
            $courses = [];
            while ($retrieveSql->fetch()) {
                $courses[] = [
                    "course_code" => $course_code,
                    "course_name" => $course_name,
                    "invite_code" => $invite_code,
                    "course_banner" => $course_banner,
                    "pinned" => $pinned,
                    "instructor_name" => $instructor_name
                ];
            }
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

    // Sort courses based on the pinned attribute
    usort($courses, function ($a, $b) {
        return $b['pinned'] - $a['pinned'];
    });

    // Send a JSON response back to the client with the success and error status
    header('Content-Type: application/json');
    echo json_encode(["success" => $success, "error" => $error, "courses" => $courses]);
} else {
    // Redirect the user to the illegal access page
    header("Location: illegal.php");
}
