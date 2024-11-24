<?php
header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include 'conn.php';

// Helper function to send a JSON response
function sendResponse($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit();
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Extract the farmer ID from the decoded JSON
$farmer_id = isset($data['farmer_id']) ? intval($data['farmer_id']) : 0;

// Validate inputs
if ($farmer_id <= 0) {
    sendResponse(false, 'Invalid input data: Farmer ID is missing or invalid');
}

// Log inputs for debugging
error_log("Fetching latest data for Farmer ID: " . $farmer_id);

try {
    // Fetch the latest row where is_active is 1 for the given farmer_id
    $stmt = $conn->prepare("
        SELECT h.area, h.date_planted, h.estimated_produce, h.estimated_income, c.crop_name, h.sev_index
        FROM harvests h 
        JOIN crops c ON h.crop_id = c.id 
        WHERE h.farmer_id = ? AND h.is_active = 1
        ORDER BY h.id DESC 
        LIMIT 1
    ");

    if (!$stmt) {
        error_log("Query preparation failed for Farmer ID: " . $farmer_id . " Error: " . $conn->error);
        sendResponse(false, 'An error occurred while fetching data');
    }

    $stmt->bind_param("i", $farmer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        
        // Log successful data fetch
        error_log("Latest row fetched successfully for Farmer ID: " . $farmer_id);
        
        sendResponse(true, 'Data fetched successfully', $data);
    } else {
        sendResponse(false, 'No active data found for Farmer ID: ' . $farmer_id);
    }
} catch (Exception $e) {
    error_log("Exception caught for Farmer ID: " . $farmer_id . " Error: " . $e->getMessage());
    sendResponse(false, 'An unexpected error occurred while fetching data');
} finally {
    // Close connections
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>
