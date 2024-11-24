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

$farmer_id = isset($data['farmer_id']) ? intval($data['farmer_id']) : 0;
$crop_id = isset($data['crop_id']) ? intval($data['crop_id']) : 0;

if ($farmer_id == 0 || $crop_id == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

// Fetch planting data from the database
$stmt = $conn->prepare("SELECT date_planted, crop, crop_variant, area, estimated_produce, estimated_income FROM harvest WHERE farmer_id = ? AND crop_id = ?");
$stmt->bind_param("ii", $farmer_id, $crop_id);
$stmt->execute();
$stmt->bind_result($date_planted, $crop, $crop_variant, $area, $estimated_produce, $estimated_income);

if ($stmt->fetch()) {
    echo json_encode([
        'success' => true,
        'planting_date' => $date_planted,
        'crop' => $crop,
        'crop_variant' => $crop_variant,
        'area' => $area,
        'estimated_produce' => $estimated_produce,
        'estimated_income' => $estimated_income
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No data found']);
}

$stmt->close();
$conn->close();
?>
