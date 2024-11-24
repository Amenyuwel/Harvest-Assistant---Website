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

// Validate input
if ($farmer_id == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input data'
    ]);
    exit();
}

// Prepare the SQL query to DELETE the row where `is_active = 1` for the specific farmer
$stmt = $conn->prepare("
    DELETE FROM harvests 
    WHERE farmer_id = ? AND is_active = 1
");
$stmt->bind_param("i", $farmer_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Active harvest record deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No matching active record found'
        ]);
    }
} else {
    error_log("Database Error: " . $stmt->error);
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting harvest record'
    ]);
}

$stmt->close();
$conn->close();
exit();
?>
