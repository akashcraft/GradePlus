<?php

require '../config.php';

session_start();
ini_set('display_errors', 0);
// Service to update account password
if ($_POST["authorize"] == "gradeplus") {
    if (!isset($_SESSION['username']) || $_SESSION['username'] == 'admin') {
        header('Location: ../login.php');
    }
    if (isset($_POST['newpassword'])) {
        try {
            $newPassword = $_POST['newpassword'];
            $currentName = $_SESSION['username'];
            $conn = mysqli_connect($DB_HOST, "gradeplusclient", "gradeplussql", "gradeplus");
            if (!$conn) {
                error_log("SQL connection failed: " . mysqli_connect_error());
            }

            // Update password
            $updatePassSql = sprintf("UPDATE login SET password = '%s' WHERE username = '%s'", $newPassword, $currentName);
            $result = mysqli_query($conn, $updatePassSql);
            if ($result) {
                //echo("Password update successful!");
                $success = 1;
            } else {
                error_log("Update password failed: " . mysqli_error($conn));
                $error = 1;
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
