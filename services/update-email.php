<?php

require '../config.php';

session_start();

ini_set('display_errors', 0);
// Service to update account email
if ($_POST["authorize"] == "gradeplus") {
    if (!isset($_SESSION['username']) || $_SESSION['username'] == 'admin') {
        header('Location: ../login.php');
    }
    if (isset($_POST['newemail'])) {
        try {
            $newEmail = $_POST['newemail'];
            $currentName = $_SESSION['username'];
            $conn = mysqli_connect($DB_HOST, "gradeplusclient", "gradeplussql", "gradeplus");
            if (!$conn) {
                error_log("SQL connection failed: " . mysqli_connect_error());
            }

            // Check if new email is already taken
            $checkEmailTakenSql = sprintf("SELECT 1 FROM login WHERE email = '%s'", $newEmail);
            $result = mysqli_query($conn, $checkEmailTakenSql);
            $row = mysqli_fetch_array($result);

            if (!$result) {
                error_log("Email taken check failed: " . mysqli_error($conn));
            }

            if ($row[0] != 0) {
                $taken = 1;
            } else {
                // Update email
                $updateEmailSql = sprintf("UPDATE login SET email = '%s' WHERE username = '%s'", $newEmail, $currentName);
                $result = mysqli_query($conn, $updateEmailSql);
                if ($result) {
                    //echo("Email update successful!");
                    $_SESSION['email'] = $newEmail;
                    $success = 1;
                } else {
                    error_log("Update email failed: " . mysqli_error($conn));
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
