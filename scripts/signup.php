<?php
    include '../config/conn.php';

    session_start();

    if($_SERVER["REQUEST_METHOD"] != "POST") {
        die('This page cannot be accessed through GET and PUT Request!');
    }

    $fname = $_POST['fname'];
    $mname = $_POST['mname'] ?? '';
    $lname = $_POST['lname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if($password != $confirm_password){
        $_SESSION['error_message'] = 'Password mismatched!';
        header('Location: ../signup.php');
        exit();
    }

    $sql = "SELECT * FROM admins WHERE username = ?";

    $checkUsername = $conn->prepare($sql);
    $checkUsername->execute([$username]);

    if($checkUsername->fetch()){
        $_SESSION['error_message'] = 'User exists!';
        header('Location: ../signup.php');
        exit();
    }

    $sql = 'INSERT INTO admins(username, password, first_name, middle_name, last_name, email, role_id)
            VALUES(?,?,?,?,?,?,?)';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $password, $fname, $mname, $lname, $email, '2']);

    $_SESSION['user_login'] = $username;

    header('Location: ../index.php');
    exit();
