<?php
header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
include 'conn.php';

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Get farmer_id from POST request
$farmer_id = isset($_POST['farmer_id']) ? (int)$_POST['farmer_id'] : null;

if ($farmer_id === null) {
    echo json_encode(["success" => false, "message" => "Farmer ID is required."]);
    exit;
}

// Prepare and execute the SQL query to fetch data for the specific farmer with is_active = 1
$sql = "SELECT date_planted, crop_id, area, estimated_produce, estimated_income, sev_index FROM harvests WHERE farmer_id = ? AND is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if any records are found
if ($result->num_rows > 0) {
    $harvests = [];
    while ($row = $result->fetch_assoc()) {
        $harvests[] = [
            "date_planted" => $row["date_planted"],
            "crop_id" => $row["crop_id"],
            "area" => $row["area"],
            "estimated_produce" => $row["estimated_produce"],
            "estimated_income" => $row["estimated_income"],
            "sev_index" => $row["sev_index"]
        ];
    }

    // Return the data in JSON format
    echo json_encode(["success" => true, "data" => $harvests]);
} else {
    // No records found
    echo json_encode(["success" => false, "message" => "No active harvests found for the specified farmer."]);
}

// Close the connection
$stmt->close();
$conn->close();

?>
