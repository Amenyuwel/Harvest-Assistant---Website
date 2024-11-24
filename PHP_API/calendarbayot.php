<?php
header('Content-Type: application/json');

// Enable error logging for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include 'conn.php';

// Ensure the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Received POST request.");

    // Collect and decode JSON input data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Extract variables from the input data
    $farmer_id = isset($data['farmer_id']) ? (int)$data['farmer_id'] : null;
    $crop_id = isset($data['crop_id']) ? (int)$data['crop_id'] : null;
    $area = isset($data['area']) ? (float)$data['area'] : null;  // Ensure it's treated as a float
    $plant_date = isset($data['plant_date']) ? $data['plant_date'] : null;
    $area_unit = isset($data['area_unit']) ? strtolower($data['area_unit']) : null;

    // Validate required inputs
    if (is_null($farmer_id) || is_null($crop_id) || is_null($area)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Missing required parameters."]);
        exit;
    }

    if ($farmer_id <= 0 || $crop_id <= 0 || $area <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Farmer ID, Crop ID, and Area must be positive values."]);
        exit;
    }

    // Validate date format if plant_date is provided
    if (!is_null($plant_date) && !preg_match("/^\d{4}-\d{2}-\d{2}$/", $plant_date)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid date format. Expected format: YYYY-MM-DD."]);
        exit;
    }

    // Convert area from square meters to hectares if necessary
    if (!is_null($area_unit)) {
        if ($area_unit === 'square meter' || $area_unit === 'square meters') {
            $area = $area / 10000.0; // Convert square meters to hectares
            error_log("Converted area from square meters to hectares: $area");
        } else if ($area_unit === 'hectare' || $area_unit === 'hectares') {
            // No conversion needed
            error_log("Area unit is in hectares: $area");
        } else {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid area unit. Must be 'square meter' or 'hectare'."]);
            exit;
        }
    }

    // Ensure the area is properly formatted as a float with a reasonable precision
    $area = round($area, 4); // Round to 4 decimal places to avoid precision issues

    // Retrieve crop_min and price from the database
    $crop_query = $conn->prepare("SELECT crop_min, price FROM crop_price WHERE crop_id = ?");
    if (!$crop_query) {
        error_log("Failed to prepare crop query.");
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to prepare SQL query."]);
        exit;
    }

    $crop_query->bind_param("i", $crop_id);
    if (!$crop_query->execute()) {
        error_log("Failed to execute crop query: " . $crop_query->error);
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Database query failed."]);
        exit;
    }

    $result = $crop_query->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $crop_min = floatval($row['crop_min']);
        $crop_price = floatval($row['price']);

        // Compute estimated produce and income
        $estimated_produce = round($area * $crop_min, 2);
        $estimated_income = round(($estimated_produce * 1000) * $crop_price, 2); // Adjust calculation if needed

        // Insert data into harvests table
        $stmt = $conn->prepare("INSERT INTO harvests (farmer_id, crop_id, area, date_planted, date_harvested, estimated_produce, estimated_income, is_active) 
                                VALUES (?, ?, ?, ?, NULL, ?, ?, 1)");

        if ($plant_date === null) {
            $stmt->bind_param("iidd", $farmer_id, $crop_id, $area, $estimated_produce, $estimated_income);
        } else {
            $stmt->bind_param("iissdd", $farmer_id, $crop_id, $area, $plant_date, $estimated_produce, $estimated_income);
        }

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "Data successfully inserted."]);
        } else {
            error_log("Failed to insert data: " . $stmt->error);
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Failed to insert data. SQL Error: " . $stmt->error]);
        }

        // Close statement
        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid crop ID."]);
    }

    $crop_query->close();
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
