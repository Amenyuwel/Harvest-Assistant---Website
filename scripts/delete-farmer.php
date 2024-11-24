<?php
session_start();
include '../config/conn.php';

if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = 'Invalid farmer ID.';
    header('Location: ../farmers.php');
    exit();
}

$farmer_id = intval($_GET['id']);

try {
    // First, delete all related records in the 'harvests' table for this farmer
    $sql = "DELETE FROM harvests WHERE farmer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$farmer_id]);

    // Now, delete the farmer from the 'farmers' table
    $sql = "DELETE FROM farmers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$farmer_id])) {
        $_SESSION['success_message'] = 'Farmer and related harvest records deleted successfully.';
    } else {
        $_SESSION['error_message'] = 'Failed to delete farmer.';
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Error: ' . $e->getMessage();
}

header('Location: ../farmers.php');
exit();