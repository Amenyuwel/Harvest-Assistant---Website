<?php
header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include 'conn.php';

// Fetch planting dates from the database
$query = "SELECT farmer_id, crop_id, date_planted FROM harvest";
$result = $conn->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    'success' => true,
    'planting_dates' => $data
]);

$conn->close();
?>
