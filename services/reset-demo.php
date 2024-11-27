<?php

require '../config.php';

// Service to initialize/reset demo database. Handles creating MySQL user "gradeplusclient", creating "gradeplus" database, creating and filling "login" table.
//if ($_POST["authorize"] == "gradeplus") {
try {
    // Initialize/Reset Demo Database
    // Connect to MySQL as admin
    $conn = mysqli_connect('127.0.0.1', 'root', '');
    if (!$conn) {
        error_log("Connection to MySQL as admin failed: " . mysqli_connect_error());
    }

    // Check if gradeplusclient user exists
    $checkUserSql = "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = 'gradeplusclient')";
    $result = mysqli_query($conn, $checkUserSql);
    $row = mysqli_fetch_array($result);
    // Create user and give privileges if it does not exist
    if ($row[0] == 0) {
        $createUserSql = "CREATE USER 'gradeplusclient'@$DB_HOST IDENTIFIED BY 'gradeplussql'";
        $result = mysqli_query($conn, $createUserSql);
        if (!$result) {
            error_log("Create user query failed: " . mysqli_error($conn));
        }

        $grantPrivilegesSql = "GRANT ALL PRIVILEGES ON gradeplus.* TO 'gradeplusclient'@$DB_HOST;";
        $result = mysqli_query($conn, $grantPrivilegesSql);
        if (!$result) {
            error_log("Grant privileges query failed: " . mysqli_error($conn));
        }
        $result = mysqli_query($conn, "FLUSH PRIVILEGES");
        if (!$result) {
            error_log("Flush privileges query failed: " . mysqli_error($conn));
        }
    }

    //Close admin connection
    mysqli_close($conn);

    // Create gradeplusclient connection
    $conn = mysqli_connect($DB_HOST, 'gradeplusclient', 'gradeplussql');
    if (!$conn) {
        error_log("Connection to MySQL as gradeplusclient failed: " . mysqli_connect_error());
    }

    // Create database if it does not exist
    $createDbSql = "CREATE DATABASE IF NOT EXISTS gradeplus";
    $result = mysqli_query($conn, $createDbSql);
    mysqli_select_db($conn, 'gradeplus');
    if (!$result) {
        error_log("Create database query failed: " . mysqli_error($conn));
    }

    // Drop table if it exists
    $resetTableSql = "DROP TABLE IF EXISTS login;";
    $result = mysqli_query($conn, $resetTableSql);
    if (!$result) {
        error_log("Drop table query failed: " . mysqli_error($conn));
    }


    // Create table
    $createTableSql = "
        CREATE TABLE login (
            username VARCHAR(50) PRIMARY KEY,
            email VARCHAR(50),
            password VARCHAR(50),
            dname VARCHAR(50),
            loggedin INT,
            profile_picture VARCHAR(255),
            usertype VARCHAR(20) NOT NULL DEFAULT 'Student'
        );";
    $result = mysqli_query($conn, $createTableSql);
    if (!$result) {
        error_log("Create table query failed: " . mysqli_error($conn));
    }

    // Insert dummy data
    $insertDataSql = "
        INSERT INTO login (username, email, password, dname, loggedin, usertype) VALUES
        ('demo', 'demo@gradeplus.com', 'demo@', 'Demo', 0, 'Student'),
        ('admin', 'admin@gradeplus.com', 'admin@', 'Administrator', 0, 'Admin'),
        ('asamanta', 'asamanta@gradeplus.com', 'asamanta', 'Akash Samanta', 0, 'Student'),
        ('mafewer', 'mafewer@gradeplus.com', 'mafewer', 'Matthew Fewer', 0, 'Student'),
        ('jfbrown03', 'jfbrown03@gradeplus.com', 'jfbrown03', 'Jordan Brown', 0, 'Student'),
        ('duplic8e', 'duplic8e@gradeplus.com', 'duplic8e', 'Abdulrahman', 0, 'Student'),
        ('shanehillier', 'shanehillier@gradeplus.com', 'shanehillier', 'Shane Hiller', 0, 'Student'),
        ('moaaz', 'moaaz@gradeplus.com', 'moaaz', 'Moaaz Elshabasy', 0, 'Student'),
        ('ddolomount', 'ddolomount@gradeplus.com', 'ddolomount', 'Daniel Dolomount', 0, 'Student'),
        ('brjthomas', 'brjthomas@gradeplus.com', 'brjthomas', 'Ben Thomas', 0, 'Student'),
        ('rabbas', 'rabbas@gradeplus.com', 'rabbas', 'Raja Abbas', 0, 'Instructor'),
        ('hnasiri', 'hnasiri@gradeplus.com', 'hnasiri', 'Hamed Nasiri', 0, 'Instructor'),
        ('weimin', 'weimin@gradeplus.com', 'weimin', 'Weimin Huang', 0, 'Instructor'),
        ('thumeerawa', 'thumeerawa@gradeplus.com', 'thumeerawa', 'Thumeera Wanasinghe', 0, 'Instructor');
        ";
    $result = mysqli_query($conn, $insertDataSql);
    if (!$result) {
        error_log("Insert dummy data query failed: " . mysqli_error($conn));
    }

    // Drop enrollment table if it exists
    $resetTableSqlEnrollment = "DROP TABLE IF EXISTS enrollment;";
    $result = mysqli_query($conn, $resetTableSqlEnrollment);
    if (!$result) {
        error_log("Drop table query failed: " . mysqli_error($conn));
    }

    $createTableSqlEnrollment = "
        CREATE TABLE enrollment (
            username VARCHAR(50),
            course_code VARCHAR(50),
            course_name VARCHAR(50),
            pinned INT,
            invite_code VARCHAR(50),
            instructor VARCHAR(50)
        );";
    $result = mysqli_query($conn, $createTableSqlEnrollment);
    if (!$result) {
        error_log("Create table query failed: " . mysqli_error($conn));
    }

    // Insert dummy data
    $insertDataSqlEnrollment = "
        INSERT INTO enrollment VALUES
        ('asamanta', 'ECE 6400', 'Software Development', 1 , 'ABCDEF', 'rabbas'),
        ('mafewer', 'ECE 6400', 'Software Development', 1 , 'ABCDEF', 'rabbas'),
        ('jfbrown03', 'ECE 6400', 'Software Development', 1 , 'ABCDEF', 'rabbas'),
        ('duplic8e', 'ECE 6400', 'Software Development', 1 , 'ABCDEF', 'rabbas'),
        ('shanehillier', 'ECE 6400', 'Software Development', 1 , 'ABCDEF', 'rabbas'),
        ('moaaz', 'ECE 6400', 'Software Development', 1 , 'ABCDEF', 'rabbas'),
        ('ddolomount', 'ECE 6400', 'Software Development', 1 , 'ABCDEF', 'rabbas'),
        ('brjthomas', 'ECE 6400', 'Software Development', 1 , 'ABCDEF', 'rabbas'),
        ('rabbas', 'ECE 6400', 'Software Development', 1 , 'ABCDEF', 'rabbas'),
        ('hnasiri', 'ECE 6500', 'Computer Architecture', 1 , 'GHIJK', 'hnasiri'),
        ('asamanta', 'ECE 6500', 'Computer Architecture', 1 , 'GHIJK', 'hnasiri'),
        ('mafewer', 'ECE 6500', 'Computer Architecture', 1 , 'GHIJK', 'hnasiri'),
        ('jfbrown03', 'ECE 6500', 'Computer Architecture', 1 , 'GHIJK', 'hnasiri'),
        ('duplic8e', 'ECE 6500', 'Computer Architecture', 1 , 'GHIJK', 'hnasiri'),
        ('shanehillier', 'ECE 6500', 'Computer Architecture', 1 , 'GHIJK', 'hnasiri'),
        ('moaaz', 'ECE 6500', 'Computer Architecture', 1 , 'GHIJK', 'hnasiri'),
        ('ddolomount', 'ECE 6500', 'Computer Architecture', 1 , 'GHIJK', 'hnasiri'),
        ('brjthomas', 'ECE 6500', 'Computer Architecture', 1 , 'GHIJK', 'hnasiri'),
        ('weimin', 'ECE 6600', 'Communication Principles', 1 , 'LMNOP', 'weimin'),
        ('asamanta', 'ECE 6600', 'Communication Principles', 1 , 'LMNOP', 'weimin'),
        ('mafewer', 'ECE 6600', 'Communication Principles', 1 , 'LMNOP', 'weimin'),
        ('jfbrown03', 'ECE 6600', 'Communication Principles', 1 , 'LMNOP', 'weimin'),
        ('duplic8e', 'ECE 6600', 'Communication Principles', 1 , 'LMNOP', 'weimin'),
        ('shanehillier', 'ECE 6600', 'Communication Principles', 1 , 'LMNOP', 'weimin'),
        ('moaaz', 'ECE 6600', 'Communication Principles', 1 , 'LMNOP', 'weimin'),
        ('ddolomount', 'ECE 6600', 'Communication Principles', 1 , 'LMNOP', 'weimin'),
        ('brjthomas', 'ECE 6600', 'Communication Principles', 1 , 'LMNOP', 'weimin'),
        ('thumeerawa', 'ECE 6610', 'Communication Networks', 1 , 'QRSTU', 'thumeerawa'),
        ('asamanta', 'ECE 6610', 'Communication Networks', 1 , 'QRSTU', 'thumeerawa'),
        ('mafewer', 'ECE 6610', 'Communication Networks', 1 , 'QRSTU', 'thumeerawa'),
        ('jfbrown03', 'ECE 6610', 'Communication Networks', 1 , 'QRSTU', 'thumeerawa'),
        ('duplic8e', 'ECE 6610', 'Communication Networks', 1 , 'QRSTU', 'thumeerawa'),
        ('shanehillier', 'ECE 6610', 'Communication Networks', 1 , 'QRSTU', 'thumeerawa'),
        ('moaaz', 'ECE 6610', 'Communication Networks', 1 , 'QRSTU', 'thumeerawa'),
        ('ddolomount', 'ECE 6610', 'Communication Networks', 1 , 'QRSTU', 'thumeerawa'),
        ('brjthomas', 'ECE 6610', 'Communication Networks', 1 , 'QRSTU', 'thumeerawa');
        ";
    $result = mysqli_query($conn, $insertDataSqlEnrollment);
    if (!$result) {
        error_log("Insert dummy data query failed: " . mysqli_error($conn));
    }

    // Drop assignment table if it exists
    $resetTableSqlAssignment = "DROP TABLE IF EXISTS assignment;";
    $result = mysqli_query($conn, $resetTableSqlAssignment);
    if (!$result) {
        error_log("Drop table query failed: " . mysqli_error($conn));
    }

    $createTableSqlAssignment = "
        CREATE TABLE assignment (
            course_code VARCHAR(50),
            invite_code VARCHAR(10),
            assignment_name VARCHAR(50),
            assignment_file VARCHAR(255),
            description VARCHAR(50),
            due_date Date,
            instructor VARCHAR(50),
            assignment_id INT PRIMARY KEY
        );";
    $result = mysqli_query($conn, $createTableSqlAssignment);
    if (!$result) {
        error_log("Create table query failed: " . mysqli_error($conn));
    }

    // Insert dummy data
    $insertDataSqlAssignment = "
        INSERT INTO assignment (course_code, invite_code, assignment_name, assignment_file, description, due_date, instructor, assignment_id) VALUES
        ('ECE 6400', 'ABCDEF', 'Final Report', './assignments/Project Presentations.pdf' , 'Project Presentation and Report' , '2024-11-28', 'rabbas', 0);
        ";
    $result = mysqli_query($conn, $insertDataSqlAssignment);
    if (!$result) {
        error_log("Insert dummy data query failed: " . mysqli_error($conn));
    }

    // Drop table if exists
    $resetTableSql = "DROP TABLE IF EXISTS reviews;";
    $result = mysqli_query($conn, $resetTableSql);
    if (!$result) {
        error_log("Drop table query failed: " . mysqli_error($conn));
        exit;  // Exit if table drop fails
    }

    $createTableSql = "
        CREATE TABLE reviews (
            assignment_id INT,
            assignment_name VARCHAR(50),
            invite_code VARCHAR(10),
            student VARCHAR(50),
            review VARCHAR(50) DEFAULT NULL
        );
    ";
    $result = mysqli_query($conn, $createTableSql);
    if (!$result) {
        error_log("Create table query failed: " . mysqli_error($conn));
        exit;  // Exit if table creation fails
    }

    // Insert dummy data with NULL for the review field
    $insertDataSql = "
        INSERT INTO reviews (assignment_id, assignment_name, invite_code, student, review) VALUES
        (0, 'Final Report', 'ABCDEF', 'asamanta', 'Excellent'),
        (0, 'Final Report', 'ABCDEF','mafewer', 'Good Work');
    ";
    $result = mysqli_query($conn, $insertDataSql);
    if (!$result) {
        error_log("Insert dummy data query failed: " . mysqli_error($conn));
    }

    // Drop courses table if it exists
    $resetTableSql = "DROP TABLE IF EXISTS courses;";
    $result = mysqli_query($conn, $resetTableSql);
    if (!$result) {
        error_log("Drop courses table query failed: " . mysqli_error($conn));
    }

    // Create courses table
    $createTableSql = "
        CREATE TABLE courses (
            course_code VARCHAR(255) NOT NULL,
            course_name VARCHAR(255) NOT NULL,
            course_banner VARCHAR(255),
            instructor_name VARCHAR(255) NOT NULL,
            instructor_dname VARCHAR(255) NOT NULL,
            invite_code VARCHAR(10) PRIMARY KEY
        );";

    $result = mysqli_query($conn, $createTableSql);
    if (!$result) {
        error_log("Failed to create courses table: " . mysqli_error($conn));
    }

    // Insert dummy data
    $insertDataSqlCourses = "
                INSERT INTO courses VALUES
                ('ECE 6400', 'Software Development', '../img/card1.jpg', 'rabbas', 'Raja Abbas', 'ABCDEF'),
                ('ECE 6500', 'Computer Architecture', '../img/card2.jpg', 'hnasiri', 'Hamed Nasiri', 'GHIJK'),
                ('ECE 6600', 'Communication Principles', '../img/card3.jpg', 'weimin', 'Weimin Huang', 'LMNOP'),
                ('ECE 6610', 'Communication Networks', '../img/card4.jpg', 'thumeerawa', 'Thumeera Wanasinghe', 'QRSTU');
                ";
    $result = mysqli_query($conn, $insertDataSqlCourses);
    if (!$result) {
        error_log("Insert dummy data query failed: " . mysqli_error($conn));
    }

    $resetTableSql = "DROP TABLE IF EXISTS announcements;";
    $result = mysqli_query($conn, $resetTableSql);
    if (!$result) {
        error_log("Drop table query failed: " . mysqli_error($conn));
    }

    // Create announcements table
    $createTableSql = "
        CREATE TABLE announcements (
            announcement_id VARCHAR(10) PRIMARY KEY,
            invite_code VARCHAR(10),
            header VARCHAR(255),
            text VARCHAR(255),
            date VARCHAR(255)
        );";

    $result = mysqli_query($conn, $createTableSql);
    if (!$result) {
        error_log("Failed to create announcements table: " . mysqli_error($conn));
    }

    // Insert dummy data
    $insertDataSqlAnnouncements = "
        INSERT INTO announcements VALUES
        ('1', 'ABCDEF', 'Final Submissions', 'Final Report and Presentations due soon', '2024-11-23');
        ";
    $result = mysqli_query($conn, $insertDataSqlAnnouncements);
    if (!$result) {
        error_log("Insert dummy data query failed: " . mysqli_error($conn));
    }

    $resetTableSql = "DROP TABLE IF EXISTS grades;";
    $result = mysqli_query($conn, $resetTableSql);
    if (!$result) {
        error_log("Drop table query failed: " . mysqli_error($conn));
    }

    // Create table
    $createTableSql = "
        CREATE TABLE grades (
            assignment_id INT,
            course_code VARCHAR(50),
            invite_code VARCHAR(10),
            assignment_name VARCHAR(50),
            username VARCHAR(50),
            grade INT,
            max_grade INT,
            feedback VARCHAR(50),
            submitted_pdf VARCHAR(255),
            submitted_flag INT,
            submitted_date Date
        );";
    $result = mysqli_query($conn, $createTableSql);
    if (!$result) {
        error_log("Create table query failed: " . mysqli_error($conn));
    }

    // Insert dummy data
    $insertDataSql = "
        INSERT INTO grades (assignment_id, course_code, invite_code, assignment_name, username, grade, max_grade,feedback,submitted_pdf,submitted_flag,submitted_date) VALUE
        (0, 'ECE 6400', 'ABCDEF', 'Final Report', 'asamanta', 10,10, 'Excellent', '../submissions/demo.pdf', 1, '2024-11-27'),
        (0, 'ECE 6400', 'ABCDEF', 'Final Report', 'mafewer', 8,10, 'OK', '../submissions/demo.pdf', 1, '2024-11-27');
        ";
    $result = mysqli_query($conn, $insertDataSql);
    if (!$result) {
        error_log("Insert dummy data query failed: " . mysqli_error($conn));
    }

    $success = 1;
    $error = 0;
} catch (Exception $e) {
    // SQL error
    $success = 0;
    $error = 1;
}

mysqli_close($conn);
header('Content-Type: application/json');
echo json_encode(["success" => $success,"error" => $error,"illegal" => 0]);
