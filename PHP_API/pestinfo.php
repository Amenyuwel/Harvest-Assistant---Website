<?php
// Set headers for JSON response and CORS
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$host = "localhost";
$username = "u766798681_adminHarvest";
$password = "EmmanBayot_69";
$dbname = "u766798681_dbHarvest";

// Try to establish a database connection using PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Log the error and return a 500 response code
    error_log("Connection failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed."]);
    exit;
}

// Query to get all pest data
$sql = "SELECT pest_name, pest_img, pest_desc, pest_reco, active_month, season FROM pest_info";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch all pest records
    $pests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if any pests are found and return the result
    if (count($pests) > 0) {
        echo json_encode($pests, JSON_PRETTY_PRINT);
    } else {
        echo json_encode(["message" => "No pests found"]);
    }
} catch (PDOException $e) {
    // Log the query error and return a 500 response code
    error_log("Query failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Query execution failed."]);
}

// Close the connection
$conn = null;
?>
