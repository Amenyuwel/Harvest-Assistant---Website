
<?php
include '../config/conn.php';

session_start();

// Ensure this page is accessed via POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die('This page cannot be accessed through GET and PUT Request!');
}

// Retrieve the form data
$rsbsa_num = $_POST['rsbsa_num'];
$fname = $_POST['fname'];
$mname = $_POST['mname'] ?? '';
$lname = $_POST['lname'];
$crops = $_POST['crop'];
$area = $_POST['area'];
$barangay = $_POST['barangay'];
$contact = $_POST['contact'];

// Prepare the update query
$sql = "UPDATE `farmers` SET first_name = ?, middle_name = ?, last_name = ?, contact_number = ?, area = ?, crops = ?, barangay = ? WHERE rsbsa_num = ?";
$stmt = $conn->prepare($sql);

// Execute the query with the form data
$success = $stmt->execute([$fname, $mname, $lname, $contact, $area, $crops, $barangay, $rsbsa_num]);

// Check if the update was successful
if ($success) {
    $_SESSION['success_message'] = 'Farmer information updated successfully!';
} else {
    $_SESSION['error_message'] = 'Failed to update farmer information!';
}

// Redirect back to the farmers list page
header('Location: ../farmers.php');
exit();
?>