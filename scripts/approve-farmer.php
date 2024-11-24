<?php
// approve-farmer.php

session_start();

include '../config/conn.php';
include '../models/Models.php';
include '../models/FarmersSignUp.php';

$farmersSignUp = new FarmersSignUp('farmers_signup');

// Check if farmer ID is passed via GET request
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $farmer_id = intval($_GET['id']); // Sanitize input to prevent SQL injection

    // Update the is_approve field to 1 for the given farmer ID
    $update_query = "UPDATE farmers_signup SET is_approve = 1 WHERE id = :id";
    $update_stmt = $conn->prepare($update_query);
    if ($update_stmt) {
        $update_stmt->bindParam(':id', $farmer_id, PDO::PARAM_INT); // Use bindParam for PDO

        // Execute the update query
        if ($update_stmt->execute()) {
            // Set success message
            $_SESSION['success_message'] = "Farmer approved successfully!";

            // Call the method to move approved farmers from farmers_signup to farmers table
            $farmersSignUp->moveApprovedFarmersToFarmersTable();

            // Redirect to pendingAcc.php after successful approval
            header('Location: ../pendingAcc.php');
            exit(); // Make sure to exit to stop further script execution
        } else {
            // Set error message
            $_SESSION['error_message'] = "Error approving farmer.";
            echo json_encode(['status' => 'error', 'message' => $_SESSION['error_message']]);
        }
    } else {
        // Prepare statement failed
        $_SESSION['error_message'] = "Error preparing the query.";
        echo json_encode(['status' => 'error', 'message' => $_SESSION['error_message']]);
    }
} else {
    $_SESSION['error_message'] = "No farmer ID provided.";
    echo json_encode(['status' => 'error', 'message' => $_SESSION['error_message']]);
}
