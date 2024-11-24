<?php
header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include 'conn.php';

// Get the post data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Extract the data from the input
$rsbsa_num = isset($data['rsbsa_num']) ? trim($data['rsbsa_num']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';
$first_name = isset($data['first_name']) ? trim($data['first_name']) : '';
$middle_name = isset($data['middle_name']) ? trim($data['middle_name']) : NULL;
$last_name = isset($data['last_name']) ? trim($data['last_name']) : '';
$crop_id = isset($data['crop_id']) ? trim($data['crop_id']) : '';
$area = isset($data['area']) ? trim($data['area']) : '';
$contact_number = isset($data['contact_number']) ? trim($data['contact_number']) : '';
$barangay_id = isset($data['barangay_id']) ? trim($data['barangay_id']) : '';

// Check if any required fields are empty
if (empty($rsbsa_num) || empty($password) || empty($first_name) || empty($last_name) || empty($crop_id) || empty($area) || empty($contact_number) || empty($barangay_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields except middle name are required'
    ]);
    exit();
}

// Check if the rsbsa_num already exists
$checkStmt = $conn->prepare("SELECT * FROM farmers WHERE rsbsa_num = ?");
$checkStmt->bind_param("s", $rsbsa_num);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'RSBSA number already exists'
    ]);
    exit();
}

// Prepare and bind the insert query
$stmt = $conn->prepare("INSERT INTO farmers (rsbsa_num, password, first_name, middle_name, last_name, crop_id, area, contact_number, barangay_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssidis", $rsbsa_num, $password, $first_name, $middle_name, $last_name, $crop_id, $area, $contact_number, $barangay_id);

// Execute the query
if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Farmer registered successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to register farmer'
    ]);
}

$stmt->close();
$conn->close();
?>