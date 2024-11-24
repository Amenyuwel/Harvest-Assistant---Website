<?php

include '../config/conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die('This page cannot be accessed through GET or PUT requests!');
}


$pestName = $_POST['pest_name'];
$pestDesc = $_POST['pest_desc'];
$pestReco = $_POST['pest_reco'];
$activeMonth = $_POST['active_month'];
$season = $_POST['season'];

try {

    $sql = "SELECT * FROM `pest_info` WHERE pest_name = :pestName";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':pestName', $pestName, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetch()) {
 
        $_SESSION['feedback'] = [
            'message' => 'Pest information already exists!',
            'type' => 'error'
        ];
        header('Location: ../PestI.php');
        exit();
    }


    $insertPestInfo = "INSERT INTO `pest_info` (pest_name, pest_desc, pest_reco, active_month, season) 
                        VALUES (:pestName, :pestDesc, :pestReco, :activeMonth, :season)";
    
    $stmt = $conn->prepare($insertPestInfo);
    $stmt->bindValue(':pestName', $pestName, PDO::PARAM_STR);
    $stmt->bindValue(':pestDesc', $pestDesc, PDO::PARAM_STR);
    $stmt->bindValue(':pestReco', $pestReco, PDO::PARAM_STR);
    $stmt->bindValue(':activeMonth', $activeMonth, PDO::PARAM_STR);
    $stmt->bindValue(':season', $season, PDO::PARAM_STR);

    if ($stmt->execute()) {

        $_SESSION['feedback'] = [
            'message' => 'New Pest information added successfully!',
            'type' => 'success'
        ];
    } else {

        $_SESSION['feedback'] = [
            'message' => 'Failed to add new pest information.',
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
