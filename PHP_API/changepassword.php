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

// Extract and validate data
$farmer_id = isset($data['farmer_id']) ? trim($data['farmer_id']) : '';
$newPassword = isset($data['newPassword']) ? trim($data['newPassword']) : '';

// Check if any required fields are empty
if (empty($farmer_id) || empty($newPassword)) {
    echo json_encode([
        'success' => false,
        'message' => 'Farmer ID and new password are required'
    ]);
    exit();
}

// Prepare the SQL statement
$sql = "UPDATE farmers SET password = ? WHERE id = ?"; // Use 'id' as the farmer_id
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $newPassword, $farmer_id);  // "si" means string (password), integer (farmer_id)

// Execute the statement
if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating password: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
