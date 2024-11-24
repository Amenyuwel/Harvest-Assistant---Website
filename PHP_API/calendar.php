<?php
header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include 'conn.php';

// Ensure the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $farmer_id = isset($data['farmer_id']) ? (int)$data['farmer_id'] : null;
    $crop_id = isset($data['crop_id']) ? (int)$data['crop_id'] : null;
    $area = isset($data['area']) ? (float)$data['area'] : null;
    $plant_date = isset($data['plant_date']) ? $data['plant_date'] : null;

    // Validate inputs
    if (is_null($farmer_id) || is_null($crop_id) || is_null($area) || is_null($plant_date)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Missing required parameters."]);
        exit;
    }

    // Convert date format if needed
    $plant_date = date('Y-m-d', strtotime($plant_date));

    // Retrieve crop_min and crop_price value from the database
    $crop_query = $conn->prepare("SELECT crop_min, price FROM crop_price WHERE crop_id = ?");
    $crop_query->bind_param("i", $crop_id);
    $crop_query->execute();
    $result = $crop_query->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $crop_min = floatval($row['crop_min']); // Get the minimum produce value from the database
        $crop_price = floatval($row['price']); // Get the crop price value from the database

        // Initialize variables
        $hectares = $area;
        $estimated_produce = 0;
        $estimated_income = 0;

        // Check crop_id to differentiate between rice and corn
        if ($crop_id == 1) { 
            // Rice computation (assuming crop_id 1 is rice)
            $estimated_produce = $hectares * $crop_min; // Estimated produce for rice
            $estimated_income = ($estimated_produce * 1000) * $crop_price;
        } elseif ($crop_id == 2) { 
            // Corn computation (assuming crop_id 2 is corn)
            $estimated_produce = $hectares * $crop_min; // Estimated produce for corn
            $estimated_income = ($estimated_produce * 1000) * $crop_price;
        } else {
            // Default response for unknown crops
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Unknown crop type"]);
            exit;
        }

        // Round the produce and income to 2 decimal places
        $estimated_produce = round($estimated_produce, 2);
        $estimated_income = round($estimated_income, 2);

        // Prepare and execute SQL query, including estimated_produce and estimated_income
        $stmt = $conn->prepare("INSERT INTO harvests (farmer_id, crop_id, area, date_planted, estimated_produce, estimated_income, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("iidsdd", $farmer_id, $crop_id, $area, $plant_date, $estimated_produce, $estimated_income);

        // Execute statement and return response
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "Data successfully inserted."]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to insert data. Error: " . $stmt->error]);
        }

        // Close statement
        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid crop ID."]);
    }

    // Close crop_min query
    $crop_query->close();
} else {
    // Handle non-POST requests
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

// Close connection
$conn->close();
?>
