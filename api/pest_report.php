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

if (!isset($_POST['pestName'])) {
    return json([
        "message" => "Please add pest name!",
        "status" => "invalid"
    ]);
}

$pestName = $_POST['pestName'];

if (!isset($_FILES['uploadPests'])) {
    return json([
        "message" => "Please upload image!",
        "status" => "invalid"
    ]);
}

$base_dir = "../pest_images/{$info['rsbsa_num']}/";

if (!is_dir($base_dir)) {
    mkdir($base_dir, 0755, true);
}

$original_filename = $_FILES["uploadPests"]["name"];
$file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
$new_filename = md5(random_bytes(20)) . '.' . $file_extension;

$target_file = $base_dir . $new_filename;

$file_path = explode('../', $target_file)[1];

move_uploaded_file($_FILES["uploadPests"]["tmp_name"], $target_file);

$date = new DateTime();
$date = $date->format("Y-m-d H:i:s");
$stmt = $conn->prepare('INSERT INTO pests_reports(farmer_id, img_path, pest_name, date_reported) VALUES(?,?,?,?)');
$stmt->execute([$info['id'], $file_path, $pestName, $date]);

return json([
    "message" => "Successfully uploaded image",
    "status" => "success"
], 200);
