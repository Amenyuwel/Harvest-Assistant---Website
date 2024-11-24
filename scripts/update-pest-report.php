<?php
include '../config/conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die('This page cannot be accessed through GET or PUT requests!');
}

// Get the form inputs
$reportId = $_POST['report_id'];
$newSeverity = $_POST['report_details'];

try {
    // Step 1: Retrieve current harvest information based on the farmer associated with the report
    // Assuming `farmer_id` in `pest_report` table corresponds to `farmer_id` in `harvests`
    $pestReport = $conn->prepare("SELECT farmer_id FROM pest_report WHERE id = :reportId");
    $pestReport->bindParam(':reportId', $reportId, PDO::PARAM_INT);
    $pestReport->execute();
    $reportData = $pestReport->fetch(PDO::FETCH_ASSOC);

    if ($reportData) {
        $farmerId = $reportData['farmer_id'];

        // Step 2: Fetch the current harvest entry for this farmer
        $harvestStmt = $conn->prepare("SELECT * FROM harvests WHERE farmer_id = :farmerId AND is_active = 1");
        $harvestStmt->bindParam(':farmerId', $farmerId, PDO::PARAM_INT);
        $harvestStmt->execute();
        $harvestData = $harvestStmt->fetch(PDO::FETCH_ASSOC);

        if ($harvestData) {
            $currentSeverity = $harvestData['severity'];
            $severityDifference = ($newSeverity - $currentSeverity) / 100;

            // Step 3: Calculate new values for estimated produce and income
            $newEstimatedProduce = $harvestData['estimated_produce'] * (1 - $severityDifference);
            $newEstimatedIncome = $harvestData['estimated_income'] * (1 - $severityDifference);

            // Step 4: Update the harvest record with new severity, estimated produce, and estimated income
            $updateHarvest = $conn->prepare("
                UPDATE harvests 
                SET severity = :newSeverity, 
                    estimated_produce = :newEstimatedProduce, 
                    estimated_income = :newEstimatedIncome 
                WHERE id = :harvestId
            ");
            $updateHarvest->bindParam(':newSeverity', $newSeverity, PDO::PARAM_STR);
            $updateHarvest->bindParam(':newEstimatedProduce', $newEstimatedProduce, PDO::PARAM_STR);
            $updateHarvest->bindParam(':newEstimatedIncome', $newEstimatedIncome, PDO::PARAM_STR);
            $updateHarvest->bindParam(':harvestId', $harvestData['id'], PDO::PARAM_INT);

            if ($updateHarvest->execute()) {
                $_SESSION['success_message'] = 'Pest report and harvest data updated successfully.';
            } else {
                $_SESSION['error_message'] = 'Failed to update harvest data.';
            }
        } else {
            $_SESSION['error_message'] = 'No active harvest found for this farmer.';
        }
    } else {
        $_SESSION['error_message'] = 'Pest report not found.';
    }

    header("Location: ../report.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Error: ' . $e->getMessage();
    header("Location: ../report.php");
    exit();
}
?>
