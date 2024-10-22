<?php

require '../config.php';

session_start();
ini_set('display_errors', 0);   //Ben Thomas: This is to prevent the error messages from being displayed on the webpage.

// Service to delete user account
if ($_POST["authorize"] == "gradeplus") {
    if (!isset($_SESSION['username']) || $_SESSION['username'] == 'admin') {
        header('Location: ../login.php');
    }
    try {
        $currentName = $_SESSION['username'];
        $conn = mysqli_connect($DB_HOST, "gradeplusclient", "gradeplussql", "gradeplus");
        if (!$conn) {
            error_log("SQL connection failed: " . mysqli_connect_error());
        }

        // Check if user exists
        $checkUserSql = sprintf("SELECT 1 FROM login WHERE username = '%s'", $currentName);
        $result = mysqli_query($conn, $checkUserSql);
        $row = mysqli_fetch_array($result);

        if (!$result) {
            error_log("User exists check failed: " . mysqli_error($conn));
        }

        if ($row[0] != 0) {
            // Delete user
            $deleteUserSql = sprintf("DELETE FROM login WHERE username = '%s'", $currentName);
            $result = mysqli_query($conn, $deleteUserSql);
            if ($result) {
                //echo("User deleted successfully!");
                $success = 1;
                session_unset();
            } else {
                error_log("Delete user failed: " . mysqli_error($conn));
                $error = 1;
            }
        } else {
            //echo("User is not found!");
        }
    } catch (Exception $e) {
        // SQL error
        $success = 0;
        $error = 1;
    }
    mysqli_close($conn);
    header('Content-Type: application/json');
    echo json_encode(["success" => $success,"error" => $error]);
} else {
    // User is not authorized
    header("Location: illegal.php");
}
