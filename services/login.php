<?php
session_start();
$success = 0;
$incorrect = 0;
$error = 0;
$empty = 0;

if (isset($_SESSION['logtime']) && isset($_SESSION['username'])) {

    if ($_SESSION['logtime'] > time()) {
        header('Location: account.php');
    } else {
        echo "Moaaz";
        unset($_SESSION['username']);
        unset($_SESSION['logtime']);
        unset($_SESSION['dname']);
    }
}

if (isset($_POST['authorize'])) {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');

    if ((empty($username) || empty($email)) && empty($password)) {
        $empty = 1;
    }

    $conn = mysqli_connect('localhost', 'gradeplusclient', 'gradeplussql', 'gradeplus');

    if ($conn->connect_error) {
        $error = 1;
    }

    $sqlCommand = $conn->prepare("SELECT username, dname FROM login WHERE (username = ? OR email = ?) AND password = ?");
    $sqlCommand->bind_param("sss", $username, $email, $password);

    if ($sqlCommand->execute()) {
        $sqlCommand->store_result();
        if ($sqlCommand->num_rows > 0) {
            $sqlCommand->bind_result($username, $dname);
            $sqlCommand->fetch();

            $_SESSION['logtime'] = time() + 1;
            $_SESSION['username'] = $username;
            $_SESSION['dname'] = $dname;
            $loggedin = 1;
            $sqlUpdate = $conn->prepare("UPDATE login SET loggedin = ? WHERE username = ?");
            $sqlUpdate->bind_param("is", $loggedin, $username);
            if ($sqlUpdate->execute() != 1) {
                echo "Error updating record: " . $sqlUpdate->error;
            }
            $success = 1;
        } else {
            $incorrect = 1;
        }
    } else {
        $error = 1;
    }

    $sqlCommand->close();
    $conn->close();
} else {
    $empty = 1;
}

?>