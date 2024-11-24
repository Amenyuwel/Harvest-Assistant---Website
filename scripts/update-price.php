<?php
include '../config/conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die('This page cannot be accessed through GET and PUT Request!');
}

// Get the form inputs
$cornPrice = $_POST['cornPrice'];
$ricePrice = $_POST['ricePrice'];

try {
    // Start a transaction
    $conn->beginTransaction();

    // Prepare SQL statements to get current prices
    $getCornPrice = "SELECT price FROM crop_price WHERE crop_id = 1"; // Corn
    $getRicePrice = "SELECT price FROM crop_price WHERE crop_id = 2"; // Rice

    // Execute queries to fetch current prices
    $stmtGetCornPrice = $conn->prepare($getCornPrice);
    $stmtGetCornPrice->execute();
    $currentCornPrice = $stmtGetCornPrice->fetchColumn();

    $stmtGetRicePrice = $conn->prepare($getRicePrice);
    $stmtGetRicePrice->execute();
    $currentRicePrice = $stmtGetRicePrice->fetchColumn();

    // Prepare SQL statements to update prices
    $updateCornPrice = "UPDATE crop_price SET price = :cornPrice WHERE crop_id = 1";
    $updateRicePrice = "UPDATE crop_price SET price = :ricePrice WHERE crop_id = 2";

    // Prepare and bind statements using PDO
    $stmt1 = $conn->prepare($updateCornPrice);
    $stmt1->bindValue(':cornPrice', $cornPrice, PDO::PARAM_STR);

    $stmt2 = $conn->prepare($updateRicePrice);
    $stmt2->bindValue(':ricePrice', $ricePrice, PDO::PARAM_STR);

    // Execute the update statements
    if ($stmt1->execute() && $stmt2->execute()) {
        // Insert records into price_history
        $insertPriceHistoryCorn = "INSERT INTO price_history (crop_id, old_price, new_price) VALUES (1, :oldCornPrice, :newCornPrice)";
        $insertPriceHistoryRice = "INSERT INTO price_history (crop_id, old_price, new_price) VALUES (2, :oldRicePrice, :newRicePrice)";

        // Prepare and bind statements for history logging
        $stmtLogCorn = $conn->prepare($insertPriceHistoryCorn);
        $stmtLogCorn->bindValue(':oldCornPrice', $currentCornPrice, PDO::PARAM_STR);
        $stmtLogCorn->bindValue(':newCornPrice', $cornPrice, PDO::PARAM_STR);

        $stmtLogRice = $conn->prepare($insertPriceHistoryRice);
        $stmtLogRice->bindValue(':oldRicePrice', $currentRicePrice, PDO::PARAM_STR);
        $stmtLogRice->bindValue(':newRicePrice', $ricePrice, PDO::PARAM_STR);

        // Execute the insert statements
        $stmtLogCorn->execute();
        $stmtLogRice->execute();

        // Commit the transaction
        $conn->commit();

        // Successful update - use JavaScript to show an alert and redirect
        echo "<script>
            alert('Price Updated Successfully!');
            window.location.href = '../index.php';
            </script>";
        exit();
    } else {
        // Failed update - rollback transaction
        $conn->rollBack();
        echo "<script>
            alert('Failed to update the price!');
            window.location.href = '../index.php';
            </script>";
        exit();
    }

} catch (PDOException $e) {
    // In case of an error, rollback transaction and display error
    $conn->rollBack();
    echo "<script>
        alert('Error: " . $e->getMessage() . "');
        window.location.href = '../index.php';
        </script>";
}
?>
