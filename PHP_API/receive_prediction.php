<?php
include 'conn.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve POST parameters
    $farmerID      = isset($_POST['farmer_id']) ? trim($_POST['farmer_id']) : '';
    $encoded_image = isset($_POST['image']) ? trim($_POST['image']) : '';
    $pest_type     = isset($_POST['pest_type']) ? trim($_POST['pest_type']) : '';
    $latitude      = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
    $longitude     = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
    $address       = isset($_POST['address']) ? trim($_POST['address']) : '';
    $damage        = isset($_POST['damage']) ? trim($_POST['damage']) : ''; 
    $sampling      = isset($_POST['sampling']) ? trim($_POST['sampling']) : ''; 

    // Log incoming data for debugging
    $log_data = [
        'Received farmer_id' => $farmerID,
        'Received encoded_image_length' => strlen($encoded_image),
        'Received pest_type' => $pest_type,
        'Received latitude' => $latitude,
        'Received longitude' => $longitude,
        'Received address' => $address,
        'Received damage' => $damage,
        'Received sampling' => $sampling,
    ];
    file_put_contents("php_debug_log.txt", print_r($log_data, true), FILE_APPEND);

    // Validate required fields
    if (empty($farmerID) || empty($encoded_image) || empty($pest_type) || empty($latitude) || empty($longitude) || empty($address)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        exit();
    }

    // Validate latitude and longitude as numeric values
    if (!is_numeric($latitude) || !is_numeric($longitude)) {
        echo json_encode(['success' => false, 'message' => 'Invalid latitude or longitude']);
        exit();
    }

    // Validate damage and sampling as numeric values
    if (!is_numeric($damage) || !is_numeric($sampling) || $sampling == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid damage or sampling values']);
        exit();
    }

    // Calculate severity
    $severity = round((($damage / $sampling) * 100) / ($sampling * 9) * 100, 2);

    // Retrieve the farmer's first name, last name from the farmers table
    $stmt = $conn->prepare("SELECT first_name, last_name FROM farmers WHERE id = ?");
    $stmt->bind_param("i", $farmerID);
    $stmt->execute();
    $result = $stmt->get_result();
    $farmer = $result->fetch_assoc();

    if (!$farmer) {
        echo json_encode(['success' => false, 'message' => 'Farmer not found']);
        exit();
    }

    $name = $farmer['first_name'] . ' ' . $farmer['last_name'];
    $stmt->close();

    // Handle image processing if an image is provided
    $filePath = '';
    if (!empty($encoded_image)) {
        $encoded_image = preg_replace('#^data:image/\w+;base64,#i', '', $encoded_image);
        $decoded_image = base64_decode($encoded_image);

        if ($decoded_image === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to decode image']);
            exit();
        }

        $timestamp = time();
        $image_name = $farmerID . '_' . $timestamp . '.jpg';
        $upload_dir = '/home/u766798681/domains/dermocura.net/public_html/harvest/PHP_API/predicted_images/';

        if (!is_writable($upload_dir)) {
            echo json_encode(['success' => false, 'message' => 'Upload directory is not writable.']);
            exit();
        }

        $filePath = $upload_dir . $image_name;

        if (file_put_contents($filePath, $decoded_image) === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to save image on server']);
            exit();
        }
    }

    // Insert pest report data into the pest_report table
    $stmt = $conn->prepare("INSERT INTO pest_report (name, image, pest_type, date_reported, latitude, longitude, address, damage, sampling) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?)");
    if ($stmt) {
        // Change the bind_param to include damage and sampling
        $stmt->bind_param("ssssddss", $name, $image_name, $pest_type, $latitude, $longitude, $address, $damage, $sampling);

        if ($stmt->execute()) {
            // Update the severity index in the harvests table for the corresponding farmer with is_active = 1
            $update_stmt = $conn->prepare("UPDATE harvests SET sev_index = ? WHERE farmer_id = ? AND is_active = 1");
            if ($update_stmt) {
                $update_stmt->bind_param("di", $severity, $farmerID);
                if ($update_stmt->execute()) {
                    // Check if the severity index is greater than 10
                    if ($severity > 10) {
                        // Update estimated_income and estimated_produce by subtracting severity
                        $adjust_stmt = $conn->prepare("UPDATE harvests SET estimated_income = estimated_income - (estimated_income * ? / 100), estimated_produce = estimated_produce - (estimated_produce * ? / 100) WHERE farmer_id = ? AND is_active = 1");
                        if ($adjust_stmt) {
                            $adjust_stmt->bind_param("ddi", $severity, $severity, $farmerID);
                            if ($adjust_stmt->execute()) {
                                echo json_encode(['success' => true, 'message' => 'Pest report, harvest severity index, and adjustments to estimated income and produce updated successfully']);
                            } else {
                                error_log("Database error: " . $adjust_stmt->error);
                                file_put_contents("php_debug_log.txt", "Database error: " . $adjust_stmt->error . "\n", FILE_APPEND);
                                echo json_encode(['success' => false, 'message' => 'Database error: ' . $adjust_stmt->error]);
                            }
                            $adjust_stmt->close();
                        } else {
                            error_log("Database error: Failed to prepare adjustment statement");
                            file_put_contents("php_debug_log.txt", "Database error: Failed to prepare adjustment statement\n", FILE_APPEND);
                            echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare adjustment statement']);
                        }
                    } else {
                        echo json_encode(['success' => true, 'message' => 'Pest report and harvest severity index updated successfully']);
                    }
                } else {
                    error_log("Database error: " . $update_stmt->error);
                    file_put_contents("php_debug_log.txt", "Database error: " . $update_stmt->error . "\n", FILE_APPEND);
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $update_stmt->error]);
                }
                $update_stmt->close();
            } else {
                error_log("Database error: Failed to prepare update statement");
                file_put_contents("php_debug_log.txt", "Database error: Failed to prepare update statement\n", FILE_APPEND);
                echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare update statement']);
            }
        } else {
            error_log("Database error: " . $stmt->error);
            file_put_contents("php_debug_log.txt", "Database error: " . $stmt->error . "\n", FILE_APPEND);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        error_log("Database error: Failed to prepare statement");
        file_put_contents("php_debug_log.txt", "Database error: Failed to prepare statement\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Database error: Failed to prepare statement']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
