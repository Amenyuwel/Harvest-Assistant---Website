<?php

function pluck($array, $column) {
    return array_map(function($item) use ($column) {
        return $item[$column];
    }, $array);
}

function json($array = [], $response = 200){
    $jsonData = json_encode($array);

    http_response_code($response);
    header('Content-Type: application/json');

    echo $jsonData;
}

function bearer(){
    $token = substr($_SERVER['HTTP_AUTHORIZATION'] ?? 0, 7);
    return $token;
}