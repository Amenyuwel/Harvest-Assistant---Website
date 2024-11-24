<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conn.php'; // Assuming conn.php handles the DB connection

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Extract and validate data
$farmer_id = isset($data['farmer_id']) ? intval($data['farmer_id']) : 0;
$date_harvested = isset($data['date_harvested']) ? $data['date_harvested'] : '';

// Validate input
if ($farmer_id == 0 || empty($date_harvested)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input data'
    ]);
    exit();
}

// Convert date format if needed (to ensure the correct format for MySQL)
$date_harvested = date('Y-m-d', strtotime($date_harvested));

// Prepare the SQL query to update the `date_harvested` and set `is_active = 0`
$stmt = $conn->prepare("
    UPDATE harvests 
    SET date_harvested = ?, is_active = 0 
    WHERE farmer_id = ? AND is_active = 1
");
$stmt->bind_param("si", $date_harvested, $farmer_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Harvest date updated and record set to inactive'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No matching record found or no change made'
        ]);
    }
} else {
    error_log("Database Error: " . $stmt->error);
    echo json_encode([
        'success' => false,
        'message' => 'Error updating harvest date'
    ]);
}

$stmt->close();
$conn->close();
exit();
?>