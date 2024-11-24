<?php
session_start();
include '../config/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmer_id = intval($_POST['farmer_id']);
    $severity = floatval($_POST['severity']); // Severity as a percentage (e.g., 10 for 10%)

    try {
        // Fetch the current estimated produce and income from the harvests table
        $sql = "SELECT estimated_produce, estimated_income FROM harvests WHERE farmer_id = ? AND is_active = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$farmer_id]);
        $harvest = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($harvest) {
            // Calculate the deductions
            $produce_deduction = ($harvest['estimated_produce'] * $severity) / 100;
            $income_deduction = ($harvest['estimated_income'] * $severity) / 100;

            // Update the estimated produce and income in the harvests table
            $sql_update = "UPDATE harvests 
                           SET estimated_produce = estimated_produce - ?, 
                               estimated_income = estimated_income - ?, 
                               severity = ? 
                           WHERE farmer_id = ? AND is_active = 1";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->execute([$produce_deduction, $income_deduction, $severity, $farmer_id]);

            $_SESSION['feedback'] = [
                'message' => 'Severity applied successfully, income and harvest adjusted.',
                'type' => 'success'
            ];
        } else {
            $_SESSION['feedback'] = [
                'message' => 'No active harvest record found for this farmer.',
                'type' => 'error'
            ];
        }
    } catch (PDOException $e) {
        $_SESSION['feedback'] = [
            'message' => 'Error: ' . $e->getMessage(),
            'type' => 'error'
        ];
    }

    // Redirect back to the severity submission page
    header('Location: ../farmers.php');
    exit();
} else {
    $_SESSION['feedback'] = [
        'message' => 'Invalid request method.',
        'type' => 'error'
    ];
    header('Location: ../farmers.php');
    exit();
}
