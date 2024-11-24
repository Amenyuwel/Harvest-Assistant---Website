<?php
include '../config/conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die('This page cannot be accessed through GET or PUT requests!');
}

// Get the form inputs
$pestId = $_POST['pest_id'];
$pestName = $_POST['pest_name'];
$pestDesc = $_POST['pest_desc'];
$pestReco = $_POST['pest_reco'];
$activeMonth = $_POST['active_month'];
$season = $_POST['season'];

try {
    // Prepare SQL statement to update pest information
    $updatePestInfo = "UPDATE pest_info SET 
                            pest_name = :pestName, 
                            pest_desc = :pestDesc, 
                            pest_reco = :pestReco, 
                            active_month = :activeMonth, 
                            season = :season 
                        WHERE id = :pestId";

    // Prepare the statement and bind parameters
    $stmt = $conn->prepare($updatePestInfo);
    $stmt->bindValue(':pestId', $pestId, PDO::PARAM_INT);
    $stmt->bindValue(':pestName', $pestName, PDO::PARAM_STR);
    $stmt->bindValue(':pestDesc', $pestDesc, PDO::PARAM_STR);
    $stmt->bindValue(':pestReco', $pestReco, PDO::PARAM_STR);
    $stmt->bindValue(':activeMonth', $activeMonth, PDO::PARAM_STR);
    $stmt->bindValue(':season', $season, PDO::PARAM_STR);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['feedback'] = [
            'message' => 'Pest information updated successfully!',
            'type' => 'success'
        ];
    } else {
        $_SESSION['feedback'] = [
            'message' => 'Failed to update pest information.',
            'type' => 'error'
        ];
    }
    header("Location: ../PestI.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['feedback'] = [
        'message' => 'Error: ' . $e->getMessage(),
        'type' => 'error'
    ];
    header("Location: ../PestI.php");
    exit();
}
?>
