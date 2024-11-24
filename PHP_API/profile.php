<?php
header('Content-Type: application/json; charset=UTF-8');

// Include the database connection
include 'conn.php';

// Check if 'id' parameter is set
if (!isset($_GET['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Missing farmer ID"]);
    exit;
}

$farmer_id = intval($_GET['id']);

// Prepare SQL query
$sql = "SELECT rsbsa_num, first_name, middle_name, last_name, ext_name, sex, contact_number, barangay_id, crop_id FROM farmers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $farmer_id);

// Execute the query
if ($stmt->execute()) {
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Fetch associative array of the row
        $row = $result->fetch_assoc();

        // Optionally, fetch barangay or crop name using another query if needed

        // Return the farmer data as a JSON response
        echo json_encode([
            "fullname" => $row['first_name'] . ' ' . ($row['middle_name'] ? $row['middle_name'] . ' ' : '') . $row['last_name'] . ($row['ext_name'] ? ' ' . $row['ext_name'] : ''),
            "rsbsa_num" => $row['rsbsa_num'],
            "sex" => $row['sex'],
            "contact_number" => $row['contact_number'],
            "barangay" => $row['barangay_id'],
            "crop" => $row['crop_id']
        ]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(["error" => "Farmer not found"]);
    }
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Query execution failed"]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
