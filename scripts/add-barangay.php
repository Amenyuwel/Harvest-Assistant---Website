<?php

include '../config/conn.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die('This page cannot be accessed through GET and PUT Request!');
}

$barangay_name = $_POST['barangay_name'];

// Check if the barangay already exists
$sql = "SELECT * FROM `barangay` WHERE barangay_name = ?";
$checkBarangay = $conn->prepare($sql);
$checkBarangay->execute([$barangay_name]);

if ($checkBarangay->fetch()) {
    $_SESSION['error_message'] = 'Barangay already exists!';
    header('Location: ../farmers.php');
    exit();
}

// Insert the new barangay into the database
$sql = "INSERT INTO `barangay` (barangay_name) VALUES (?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$barangay_name]);

$_SESSION['success_message'] = 'Successfully added new barangay!';
header('Location: ../farmers.php');
exit();
?>