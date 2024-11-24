<?php
// Database connection
include 'conn.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from the request
$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'];
$last_name = $_POST['last_name'];
$contact_number = $_POST['contact_number'];
$area = $_POST['area'];
$crop_id = $_POST['crop_id'];
$barangay_id = $_POST['barangay_id'];

// Insert or update the user's profile
$sql = "INSERT INTO your_table_name (first_name, middle_name, last_name, contact_number, area, crop_id, barangay_id)
VALUES ('$first_name', '$middle_name', '$last_name', '$contact_number', '$area', '$crop_id', '$barangay_id')
ON DUPLICATE KEY UPDATE 
middle_name='$middle_name', last_name='$last_name', contact_number='$contact_number', area='$area', crop_id='$crop_id', barangay_id='$barangay_id'";

if ($conn->query($sql) === TRUE) {
    echo "Profile updated successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>