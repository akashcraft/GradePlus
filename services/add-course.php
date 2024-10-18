<?php

require '../config.php';

// Check if the authorization key matches the shared secret
if ($_POST["authorize"] == "gradeplus") {
    try {
        // Create a new MySQLi connection
        $conn = new mysqli($DB_HOST, "gradeplusclient", "gradeplussql", "gradeplus");

        // If the connection fails, throw an exception
        if (!$conn) {
            throw new Exception();
        }

        // Check if banner is empty, otherwise use default image
        if (empty($_FILES['banner']['name'])) {
            $upload_dir = "../img/card.jpg";
        } else {
            // Create path to banner image to store in database
            $img_dir = "../img/";
            $banner_img = basename($_FILES['banner']['name']);
            $upload_dir = $img_dir . $banner_img;
            // Upload image to /img directory
            if (!move_uploaded_file($_FILES['banner']['tmp_name'], $upload_dir)) {
                throw new Exception("Failed to upload file to img directory");
            }
        }

        // Generate a unique course invite code by hashing the course name and instructor name
        // and taking the first 10 characters of the resulting SHA-256 hash
        $course_invite_code = strtoupper(substr(hash('sha256', $_POST['coursename'] . $_POST['instructor_name']), 0, 6));

        // Prepare an SQL query to insert the new course into the 'courses' table
        $addCourseSql = $conn->prepare("
            INSERT INTO courses (course_code, course_name, course_banner, instructor_name, instructor_dname, invite_code)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        // Bind the values to the prepared statement
        $addCourseSql->bind_param("ssssss", $_POST['coursecode'], $_POST['coursename'], $upload_dir, $_POST['instructor_name'], $_POST['instructor_dname'], $course_invite_code);

        $result = $addCourseSql->execute();

        // Prepare an SQL query to add the instructor into the 'enrollment' table
        $addCourseSql = $conn->prepare("
            INSERT INTO enrollment 
            VALUES (?, ?, ?, 0, ?, ?)
        ");

        // Bind the values to the prepared statement
        $addCourseSql->bind_param("sssss", $_POST['instructor_name'], $_POST['coursecode'], $_POST['coursename'], $course_invite_code, $_POST['instructor_name']);


        // Execute the prepared SQL statement
        $result = $addCourseSql->execute();

        $addCourseSql->close();

        // Check if the execution was successful
        if (!$result) {
            $success = 0;
            $error = 1;
        } else {
            $success = 1;
            $error = 0;
        }

    } catch (Exception $e) {
        $success = 0;
        $error = 1;
    }

    // Close the MySQLi connection
    mysqli_close($conn);

    // Send a JSON response back to the client with the success and error status
    header('Content-Type: application/json');
    echo json_encode(["success" => $success, "error" => $error]);

} else {
    // If authorization fails, redirect the user to the 'illegal.php' page
    header("Location: illegal.php");
}
