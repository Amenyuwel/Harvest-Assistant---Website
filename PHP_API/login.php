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
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}

// Get the POST data
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Extract username/rsbsa_num and password from request
$loginValue = $data['username'] ?? $data['rsbsa_num'];
$password = $data['password'];

// SQL query to check for the farmer, considering both username and rsbsa_num
$sql = "SELECT id, session_key, password, rsbsa_num, username FROM farmers WHERE (rsbsa_num = ? OR username = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $loginValue, $loginValue);  // Bind the same value for rsbsa_num and username
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    // Check if session_key matches the login method
    if (($row['session_key'] == 0 && $row['rsbsa_num'] === $loginValue) || ($row['session_key'] == 1 && $row['username'] === $loginValue)) {
        // Verify the password
        if ($row['password'] === $password) {
            // Password is correct, prepare response
            $response = [
                'success' => true,
                'message' => 'Login successful',
                'userData' => [
                    'farmerID' => $row['id'],
                    'session_key' => $row['session_key']
                ]
            ];
            echo json_encode($response);
        } else {
            // Invalid password
            echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
        }
    } else {
        // Invalid login method (session_key and loginValue don't match)
        echo json_encode(['success' => false, 'message' => 'Invalid login method.']);
    }
} else {
    // Farmer not found
    echo json_encode(['success' => false, 'message' => 'User not found.']);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
