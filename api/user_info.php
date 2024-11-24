<?php

require_once('../functions.php');

require_once('../models/Models.php');
require_once('../models/Farmers.php');
require_once('../models/Tokens.php');

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    return json([
        'message' => 'This page cannot be accessed through POST and PUT Request!'
    ], 401);
}

$token = bearer();

if(strlen($token) == 0){
    return json([
        'message' => 'No token found! Please Login...',
        'status' => 'failed',
    ]);
}

$checkToken = new Tokens();
$checkToken = $checkToken->token($token);

if(!$checkToken){
    return json([
        'message' => 'Invalid Token! Please Login...',
        'status' => 'failed',
    ]);
}

$user_id = $checkToken['rsbsa_num'];

$farmer = new Farmers();
$info = $farmer->farmer($user_id);

return json($info, 200);
