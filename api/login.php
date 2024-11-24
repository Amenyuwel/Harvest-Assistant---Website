<?php
// functions
require_once('../config/conn.php');
require_once('../functions.php');

require_once('../models/Models.php');
require_once('../models/Farmers.php');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    return json([
        'message' => 'This page cannot be accessed through GET and PUT Request!'
    ], 401);
}

$rsbsa_num = $_POST['rsbsa_num'] ?? null;
$password = $_POST['password'] ?? null;

$farmer = new Farmers();
$info = $farmer->where(['rsbsa_num' => $rsbsa_num])->get();
if (!$info) {
    return json([
        'message' => 'User not found!',
        'status' => 'invalid'
    ], 403);
}

$info = $info[0];


$password = $info['password'] == $password;

if (!$password) {
    return json([
        'message' => 'Password mismatch!',
        'status' => 'invalid'
    ], 403);
}

$token = md5(rand());
$date = new DateTime();
$date = $date->format('Y-m-d H:i:s');

$user_id = $info['id'];

$checkUser = $conn->prepare('SELECT * FROM farmer_tokens WHERE farmer_id = ?');
$checkUser->execute([$user_id]);
$checkUser = $checkUser->fetch();

if (!$checkUser) {
    $stmt = $conn->prepare('INSERT INTO farmer_tokens(farmer_id, remember_token, date_login) VALUES(?,?,?)');
    $stmt->execute([$info['id'], $token, $date]);
}

$stmt = $conn->prepare('UPDATE farmer_tokens SET remember_token = ?, date_login = ? WHERE farmer_id = ?');
$stmt->execute([$token, $date, $info['id']]);

return json([
    'message' => 'Login Successfully',
    'remember_token' => $token
], 201);
