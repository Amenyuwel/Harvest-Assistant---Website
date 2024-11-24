<?php
include 'conn.php';

// Set the response content type to JSON
header('Content-Type: application/json');

// Disable error reporting to prevent unwanted output in JSON response
error_reporting(0);
ini_set('display_errors', 0);

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve POST parameters
    $password       = isset($_POST['password']) ? trim($_POST['password']) : '';
    $first_name     = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $middle_name    = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : '';
    $last_name      = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
    $area           = isset($_POST['area']) ? trim($_POST['area']) : '';
    $crop_id        = isset($_POST['crop_id']) ? trim($_POST['crop_id']) : '';
    $barangay_id    = isset($_POST['barangay_id']) ? trim($_POST['barangay_id']) : '';
    $role_id        = 1; // Assuming role_id is always 1 for farmers

    // Validate required fields (middle_name can be optional)
    if (empty($password) || empty($first_name) || empty($last_name) ||
        empty($contact_number) || empty($area) || empty($crop_id) || empty($barangay_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'All required fields must be filled'
        ]);
        exit();
    }


    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("INSERT INTO farmers_signup 
        (password, first_name, middle_name, last_name, contact_number, area, crop_id, barangay_id, role_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        // Bind parameters to the SQL statement
        $stmt->bind_param(
            "ssssssiii",
            $password,
            $first_name,
            $middle_name,
            $last_name,
            $contact_number,
            $area,
            $crop_id,
            $barangay_id,
            $role_id
        );

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Farmer registered successfully']);
        } else {
            // Log the error for debugging (optional)
            error_log("Database error: " . $stmt->error);

            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }

        // Close the statement
        $stmt->close();
    } else {
        // Log the error for debugging (optional)
        error_log("Database error: Failed to prepare statement");

        echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare statement']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

// Close the database connection
$conn->close();
?>
