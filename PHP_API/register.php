<?php
ob_start();  // Start output buffering
header('Content-Type: application/json');

// Enable error logging (for debugging purposes)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include 'conn.php';

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Capture input data
$first_name = $_POST['first_name'] ?? '';
$middle_name = $_POST['middle_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$ext_name = $_POST['ext_name'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';
$area = $_POST['area'] ?? '';
$purok = $_POST['purok'] ?? '';
$street = $_POST['street'] ?? '';
$city = $_POST['city'] ?? '';
$province = $_POST['province'] ?? '';
$region = $_POST['region'] ?? '';
$birthdate = $_POST['birthdate'] ?? '';
$birthplace = $_POST['birthplace'] ?? '';
$religion = $_POST['religion'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$crop_id = $_POST['crop_id'] ?? '';
$barangay_id = $_POST['barangay_id'] ?? '';
$sex = $_POST['sex'] ?? '';
$role_id = $_POST['role_id'] ?? '';

// Validate required fields
if (empty($first_name) || empty($last_name) || empty($contact_number) || empty($username) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Please fill all required fields"]);
    exit;
}

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO farmers_signup (first_name, middle_name, last_name, ext_name, contact_number, area, purok, street, city, province, region, birthdate, birthplace, religion, username, password, crop_id, barangay_id, sex, role_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssssssssssssssi", $first_name, $middle_name, $last_name, $ext_name, $contact_number, $area, $purok, $street, $city, $province, $region, $birthdate, $birthplace, $religion, $username, $hashed_password, $crop_id, $barangay_id, $sex, $role_id);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registration successful"]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
}

// Close connections
$stmt->close();
$conn->close();
?>