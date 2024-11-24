<?php
include '../config/conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die('This page cannot be accessed through GET or PUT requests!');
}

// Collect POST data and sanitize inputs
$farmer_id = intval($_POST['farmer_id']);
$rsbsa_num = trim($_POST['rsbsa_num']);
$fname = trim($_POST['fname']);
$mname = trim($_POST['mname']);
$lname = trim($_POST['lname']);
$crop_id = intval($_POST['crop']);
$area = floatval($_POST['area']);
$barangay_id = intval($_POST['barangay']);
$contact = trim($_POST['contact']);

// Validate inputs
if ($farmer_id <= 0 || empty($rsbsa_num) || empty($fname) || empty($lname) || $crop_id <= 0 || $area <= 0 || $barangay_id <= 0 || empty($contact)) {
    $_SESSION['feedback'] = [
        'message' => 'Invalid input data',
        'type' => 'error'
    ];
    header('Location: ../farmers.php');
    exit();
}

// Prepare SQL statement
$sql = "UPDATE farmers SET 
            rsbsa_num = :rsbsa_num, 
            first_name = :first_name, 
            middle_name = :middle_name, 
            last_name = :last_name, 
            crop_id = :crop_id, 
            area = :area, 
            barangay_id = :barangay_id, 
            contact_number = :contact_number 
        WHERE id = :id";

$stmt = $conn->prepare($sql);

if ($stmt) {
    // Bind parameters
    $stmt->bindValue(':rsbsa_num', $rsbsa_num, PDO::PARAM_STR);
    $stmt->bindValue(':first_name', $fname, PDO::PARAM_STR);
    $stmt->bindValue(':middle_name', $mname, PDO::PARAM_STR);
    $stmt->bindValue(':last_name', $lname, PDO::PARAM_STR);
    $stmt->bindValue(':crop_id', $crop_id, PDO::PARAM_INT);
    $stmt->bindValue(':area', $area, PDO::PARAM_STR);
    $stmt->bindValue(':barangay_id', $barangay_id, PDO::PARAM_INT);
    $stmt->bindValue(':contact_number', $contact, PDO::PARAM_STR);
    $stmt->bindValue(':id', $farmer_id, PDO::PARAM_INT);
    
    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['feedback'] = [
            'message' => 'Farmers information updated successfully!',
            'type' => 'success'
        ];
    } else {
        $_SESSION['feedback'] = [
            'message' => 'Failed to update farmer information.',
            'type' => 'error'
        ];
    }
} else {
    // Log error for debugging purposes
    error_log('Failed to prepare SQL statement: ' . $conn->error);
    $_SESSION['feedback'] = [
        'message' => 'Failed to prepare SQL statement.',
        'type' => 'error'
    ];
}

// Redirect to farmers.php
header('Location: ../farmers.php');
exit();
?>
