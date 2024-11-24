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
    $rsbsa_num      = isset($_POST['rsbsa_num']) ? trim($_POST['rsbsa_num']) : '';
    $password       = isset($_POST['password']) ? trim($_POST['password']) : '';
    $first_name     = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $middle_name    = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : '';
    $last_name      = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
    $area           = isset($_POST['area']) ? trim($_POST['area']) : '';
    $crop_id        = isset($_POST['crop_id']) ? trim($_POST['crop_id']) : '';
    $barangay_id    = isset($_POST['barangay_id']) ? trim($_POST['barangay_id']) : '';
    $role_id        = 1; // Assuming role_id is always 1 for farmers
    $encoded_image  = isset($_POST['image']) ? trim($_POST['image']) : '';

    // Validate required fields (middle_name can be optional)
    if (empty($rsbsa_num) || empty($password) || empty($first_name) || empty($last_name) ||
        empty($contact_number) || empty($area) || empty($crop_id) || empty($barangay_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'All required fields must be filled'
        ]);
        exit();
    }

    $filePath = '';
    if (!empty($encoded_image)) {
        // Decode the Base64 string
        $encoded_image = preg_replace('#^data:image/\w+;base64,#i', '', $encoded_image); // Remove base64 header if exists
        $decoded_image = base64_decode($encoded_image);
        
        if ($decoded_image === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to decode image']);
            exit();
        }

        // Create a unique name for the image: rsbsa_num + current timestamp
        $timestamp = time(); // Current timestamp
        $image_name = $rsbsa_num . '_' . $timestamp . '.jpg'; // Combine rsbsa_num and timestamp
      
        $upload_dir = '../register_upload/'; // Directory where the image will be saved

        // Check if the directory exists, create if not
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
                exit();
            }
        }

        // Full path to save the image
        $filePath = $upload_dir . $image_name;

        // Save the image to the server
        if (file_put_contents($filePath, $decoded_image) === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to save image on server']);
            exit();
        }

        // Optionally, set file permissions (depending on your server's needs)
        chmod($filePath, 0644);
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO farmers_signup 
        (rsbsa_num, password, first_name, middle_name, last_name, contact_number, area, crop_id, barangay_id, role_id, image_path) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param(
            "sssssssiiis",
            $rsbsa_num,
            $password, 
            $first_name,
            $middle_name,
            $last_name,
            $contact_number,
            $area,
            $crop_id,
            $barangay_id,
            $role_id,
            $image_name // Store only the image name in the database
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Farmer registered successfully']);
        } else {
            error_log("Database error: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        error_log("Database error: Failed to prepare statement");
        echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare statement']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
