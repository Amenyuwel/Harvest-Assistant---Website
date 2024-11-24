<?php
session_start();
include 'models/Harvest.php';

if (isset($_GET['farmer_id'])) {
    $farmerId = $_GET['farmer_id'];
    $harvestModel = new Harvest();
    
    $currentSeverity = $harvestModel->getSeverityByFarmerId($farmerId);
    
    echo json_encode(['severity' => $currentSeverity]);
}
