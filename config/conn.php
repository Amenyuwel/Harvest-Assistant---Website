<?php
    $host = "localhost";
    $username = "u766798681_adminHarvest";
    $password = "EmmanBayot_69";
    $dbname = "u766798681_dbHarvest";

    try{
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    }catch(Exception $e){
        die("Error Message: " .  $e->getMessage());
    }