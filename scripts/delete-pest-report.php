<?php
session_start();
include '../config/conn.php';

// Check if the pest report ID is provided in the URL
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = 'Invalid pest report ID.';
    header('Location: ../report.php');
    exit();
}

$pest_report_id = intval($_GET['id']); // Get the pest report ID from URL and ensure it's an integer

try {
    // Delete the pest report from the 'pest_reports' table
    $sql = "DELETE FROM pest_report WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$pest_report_id])) {
        $_SESSION['success_message'] = 'Pest report deleted successfully.';
    } else {
        $_SESSION['error_message'] = 'Failed to delete pest report.';
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Error: ' . $e->getMessage();
}

// Redirect back to the pest reports page
header('Location: ../report.php');
exit();
