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
        $invite_code = $_POST['invitecode'];
        $pinned = $_POST['pinned'];

        $updateSql = $conn->prepare("
            UPDATE enrollment
            SET pinned = ?
            WHERE invite_code = ? AND username = ?
        ");

        $updateSql->bind_param("iss", $pinned, $invite_code, $username);

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
        echo $e;
    }

    // Close the MySQLi connection
    mysqli_close($conn);

    // Send a JSON response back to the client with the success and error status
    header('Content-Type: application/json');
    echo json_encode(["success" => $success, "error" => $error]);

} else {
    header("Location: illegal.php");
}
