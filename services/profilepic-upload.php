<?php
$success = 0;
$error = 0;
$missingFeild = 0;
try {
    $conn = mysqli_connect('localhost', 'gradeplusclient', 'gradeplussql', 'gradeplus');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $username = $_POST['username'];
        

        if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
            $profilePicture = $_FILES['profilePicture']['tmp_name'];


            $stmt = $conn->prepare("SELECT * FROM login WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows != 0) {
                $stmt->close();

                $imageData = file_get_contents($profilePicture);

                $stmt = $conn->prepare("SELECT * FROM login WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows == 0) {

                    $stmt->close();
                    $stmt = $conn->prepare("INSERT INTO login (username, profilePicture) VALUES (?, ?)");
                    $stmt->bind_param("sb", $username, $imageData);
                    $stmt->send_long_data(1, $imageData); 

                    if ($stmt->execute()) {
                        echo "Profile picture uploaded successfully!";
                    } else {
                        echo "Error uploading profile picture: " . $stmt->error;
                    }
                } else {

                    $stmt->close();
                    $stmt = $conn->prepare("UPDATE login SET profilePicture = ? WHERE username = ?");
                    $stmt->bind_param("bs", $imageData, $username);
                    $stmt->send_long_data(0, $imageData); 

                    if ($stmt->execute()) {
                        $success = 1;
                    } else {
                        $error = 1;
                    }
                }
            }

            $stmt->close();
        } else {
            $missingFeild = 0;
        }
    }

    $conn->close();
} catch (exception $e) {
    $error = 1;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Profile Picture</title>
</head>
<body>
    <h2>Upload Your Profile Picture</h2>
    <form action="profilePicUpload.php" method="post" enctype="multipart/form-data">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br><br>
        <label for="profilePicture">Select Profile Picture:</label>
        <input type="file" name="profilePicture" accept="image/*" required><br><br>
        <button type="submit">Upload</button>
    </form>
</body>
</html>

