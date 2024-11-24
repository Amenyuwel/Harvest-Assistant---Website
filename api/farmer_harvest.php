<?php

// functions
require_once('../config/conn.php');
require_once('../functions.php');

require_once('../models/Models.php');
require_once('../models/Farmers.php');
require_once('../models/Tokens.php');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    return json([
        'message' => 'This page cannot be accessed through GET and PUT Request!'
    ], 401);
}

$token = bearer();

if (strlen($token) == 0) {
    return json([
        'message' => 'No token found! Please Login...',
        'status' => 'failed',
    ]);
}

$checkToken = new Tokens();
$checkToken = $checkToken->token($token);

if (!$checkToken) {
    return json([
        'message' => 'Invalid Token! Please Login...',
        'status' => 'failed',
    ]);
}

$username = $checkToken['rsbsa_num'];

$farmer = new Farmers();
$info = $farmer->farmer($username);

$farmer_id = $info['id'];
$crop_id = $info['crop_id'];

if (!isset($_POST['plantedID'])) {
    return json([
        "message" => "Please add the planted ID of the crops!",
        "status" => "invalid"
    ]);
}

$plantedID = $_POST['plantedID'];
$checkHarvest = $conn->prepare('SELECT isHarvested FROM farmer_planted WHERE id = ?');
$checkHarvest->execute([$plantedID]);
$isHarvested = $checkHarvest->fetch(PDO::FETCH_ASSOC);

if($isHarvested['isHarvested']){
    return json([
        "message" => "Already harvested this crop.",
        "status" => "invalid"
    ], 400);
}

$estimated_produce = $info['area'] * 5000;
$estimated_income = ($estimated_produce / 50) * 1800;

$date = new DateTime();
$date = $date->format("Y-m-d H:i:s");

$updateHarvest = $conn->prepare('UPDATE farmer_planted SET isHarvested = ? WHERE id = ?');
$updateHarvest->execute([1, $plantedID]);

$stmt = $conn->prepare('INSERT INTO harvests(farmer_id, crop_id, date_harvested, estimated_produce, estimated_income) VALUES(?,?,?,?,?)');
$stmt->execute([$farmer_id, $crop_id, $date, $estimated_produce, $estimated_income]);

return json([
    "message" => "Harvested successfully",
    "status" => "success"
], 200);