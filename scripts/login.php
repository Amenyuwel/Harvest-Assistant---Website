<?php
    include '../config/conn.php';

    session_start();

    if($_SERVER["REQUEST_METHOD"] != "POST") {
        die('This page cannot be accessed through GET and PUT Request!');
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE username = ?";

    $checkUsername = $conn->prepare($sql);
    $checkUsername->execute([$username]);

    if(!$checkUsername->fetch()){
        $_SESSION['error_message'] = 'User not found!';
        header('Location: ../login.php');
        exit();
    }

    $sql .= " AND password = ?";

    $checkPassword = $conn->prepare($sql);
    $checkPassword->execute([$username, $password]);
    $user = $checkPassword->fetch();

    if(!$user){
        $_SESSION['error_message'] = 'Invalid Password!';
        header('Location: ../login.php');
        exit();
    }

    $_SESSION['user_login'] = $user['username'];

    header('Location: ../index.php');
    exit();
