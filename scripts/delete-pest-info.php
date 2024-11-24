<?php
session_start();
include '../config/conn.php';

// Check if the pest ID is provided in the URL
if (!isset($_GET['id'])) {
    $_SESSION['feedback'] = [
        'message' => 'Invalid pest ID.',
        'type' => 'error'
    ];
    header('Location: ../PestI.php');
    exit();
}

$pest_id = intval($_GET['id']); // Get the pest ID from URL and ensure it's an integer

try {
    // Delete the pest information from the 'pest_info' table
    $sql = "DELETE FROM pest_info WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$pest_id])) {
        $_SESSION['feedback'] = [
            'message' => 'Pest information deleted successfully.',
            'type' => 'success'
        ];
    } else {
        $_SESSION['feedback'] = [
            'message' => 'Failed to delete pest information.',
            'type' => 'error'
        ];
    }
} catch (PDOException $e) {
    $_SESSION['feedback'] = [
        'message' => 'Error: ' . $e->getMessage(),
        'type' => 'error'
    ];
}

// Redirect back to the pest information page
header('Location: ../PestI.php');
exit();
