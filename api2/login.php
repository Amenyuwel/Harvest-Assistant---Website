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

// Extract the data
$rsbsa_num = isset($data['rsbsa_num']) ? trim($data['rsbsa_num']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

// Check if any required fields are empty
if (empty($rsbsa_num) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required'
    ]);
    exit();
}

// Prepare and bind (ensure your column names are correct)
$stmt = $conn->prepare("SELECT * FROM farmers WHERE rsbsa_num = ? AND password = ?");
$stmt->bind_param("ss", $rsbsa_num, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'userData' => [
            'rsbsa_num' => $user['rsbsa_num'],
            'farmerID' => $user['id'],  // Adjust according to your farmer ID column
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'contactNumber' => $user['contact_number'],
            'area' => $user['area']
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid RSBSA number or password'
    ]);
}

$stmt->close();
$conn->close();
?>