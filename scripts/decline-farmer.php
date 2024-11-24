<?php
include '../config/conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    die('This page cannot be accessed through POST requests!');
}

$farmer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($farmer_id == 0) {
    $_SESSION['error_message'] = 'Invalid farmer ID!';
    header('Location: ../pendingAcc.php');
    exit();
}

// Prepare and execute the delete statement from `farmers_signup` table
$sql = "DELETE FROM farmers_signup WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt->execute([$farmer_id])) {
    $_SESSION['success_message'] = 'Farmer account declined and deleted successfully!';
} else {
    $_SESSION['error_message'] = 'Failed to decline and delete farmer account!';
}

header('Location: ../pendingAcc.php');
exit();
?>
