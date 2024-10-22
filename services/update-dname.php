<?php

require '../config.php';

session_start();

ini_set('display_errors', 0);
// Service to update account dname
if ($_POST["authorize"] == "gradeplus") {
    if (!isset($_SESSION['username']) || $_SESSION['username'] == 'admin') {
        header('Location: ../login.php');
    }
    if (isset($_POST['newdname'])) {
        try {
            $newDname = $_POST['newdname'];
            $currentName = $_SESSION['username'];
            $conn = mysqli_connect($DB_HOST, "gradeplusclient", "gradeplussql", "gradeplus");
            if (!$conn) {
                error_log("SQL connection failed: " . mysqli_connect_error());
            }

            // Update dname
            $updateNameSql = sprintf("UPDATE login SET dname = '%s' WHERE username = '%s'", $newDname, $currentName);
            $result = mysqli_query($conn, $updateNameSql);
            if ($result) {
                //echo("Dname update successful!");
                $success = 1;
                $_SESSION['dname'] = $newDname;
            } else {
                error_log("Update Dname failed: " . mysqli_error($conn));
                $error = 1;
            }
        } catch (Exception $e) {
            // SQL error
            $success = 0;
            $error = 1;
        }
    }
    mysqli_close($conn);
    header('Content-Type: application/json');
    echo json_encode(["success" => $success,"error" => $error]);
} else {
    // User is not authorized
    header("Location: illegal.php");
}
