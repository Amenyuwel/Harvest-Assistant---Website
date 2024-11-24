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

if (!isset($_POST['datePlanted'])) {
    return json([
        "message" => "Please add date!",
        "status" => "invalid"
    ]);
}

$datePlanted = $_POST['datePlanted'];
$variance_id = $_POST['varianceID'];
$date = new DateTime($datePlanted);
$date = $date->format("Y-m-d");

$stmt = $conn->prepare('INSERT INTO farmer_planted(farmer_id, variance_id, date_planted, isHarvested) VALUES(?,?,?,?)');
$stmt->execute([$farmer_id, $variance_id, $date, 0]);

return json([
    "message" => "Planted successfully",
    "status" => "success"
], 200);