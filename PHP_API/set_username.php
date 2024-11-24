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
$farmerID = isset($data['farmerID']) ? trim($data['farmerID']) : '';
$username = isset($data['username']) ? trim($data['username']) : '';

// Check if any required fields are empty
if (empty($farmerID) || empty($username)) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required'
    ]);
    exit();
}

// Prepare and bind the SQL statement to prevent SQL injection
$stmt = $conn->prepare("UPDATE farmers SET username = ?, session_key = 1 WHERE id = ?");
$stmt->bind_param("si", $username, $farmerID);

// Execute the statement and check if it was successful
if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Username updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update username. Please try again.'
    ]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>