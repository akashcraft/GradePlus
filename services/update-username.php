<?php

require '../config.php';

session_start();
// Service to update account username
if ($_POST["authorize"] == "gradeplus") {
    if (!isset($_SESSION['username']) || $_SESSION['username'] == 'admin') {
        header('Location: ../login.php');
    }
    if (isset($_POST['newname'])) {
        try {
            $newName = $_POST['newname'];
            $currentName = $_SESSION['username'];
            $conn = mysqli_connect($DB_HOST, "gradeplusclient", "gradeplussql", "gradeplus");
            if (!$conn) {
                error_log("SQL connection failed: " . mysqli_connect_error());
            }

            // Check if new username is already taken
            $checkNameTakenSql = sprintf("SELECT 1 FROM login WHERE username = '%s'", $newName);
            $result = mysqli_query($conn, $checkNameTakenSql);
            $row = mysqli_fetch_array($result);

            if ($row == null) {
                $row = [0];
            }

            if (!$result) {
                error_log("Username taken check failed: " . mysqli_error($conn));
            }

            if ($row[0] != 0) {
                $taken = 1;
                $success = 0;
                $error = 0;
            } else {
                // Update username
                $updateNameSql = sprintf("UPDATE login SET username = '%s' WHERE username = '%s'", $newName, $currentName);
                $result = mysqli_query($conn, $updateNameSql);
                if ($result) {
                    $success = 1;
                    $error = 0;
                    $taken = 0;
                } else {
                    error_log("Update username failed: " . mysqli_error($conn));
                    $error = 1;
                }
            }
        } catch (Exception $e) {
            // SQL error
            $success = 0;
            $error = 1;
            $taken = 0;
        }
    }
    mysqli_close($conn);
    header('Content-Type: application/json');
    echo json_encode(["success" => $success,"error" => $error,"taken" => $taken]);
} else {
    // User is not authorized
    header("Location: illegal.php");
}
