<?php

include '../config/conn.php';
include '../functions.php';

include '../models/Models.php';

include '../models/Farmers.php';
include '../models/Crops.php';
include '../models/Barangay.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die('This page cannot be accessed through GET and PUT Request!');
}

$filename = $_FILES["csv_farmers"]["tmp_name"]; 

if($_FILES["csv_farmers"]["size"] > 0){
    $file = fopen($filename, "r");
    
    // var_dump($getData);
    $count = 0;
    while (($getData = fgetcsv($file, 10000, ",")) !== FALSE){
        if($count == 0){
            $count++;
            continue;
        }

        $rsbsa_num = $getData[0] ?? 0;
        $first_name = $getData[1] ?? 'no_fname';
        $middle_name = $getData[2] ?? '';
        $last_name = $getData[3] ?? 'no_lname';
        $crops = checkCrops($getData[4]) ?? null; 
        $area = $getData[5] ?? 0;
        $brgy = checkBrgy($getData[6]) ?? null;
        $contact_number = $getData[7] ?? 0;

        if(checkFarmer($rsbsa_num)){
            $_SESSION['error_message'] = 'Existing farmer detected! Please change your rsbsa number...';
            header('Location: ../farmers.php');
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO farmers(rsbsa_num, password, first_name, middle_name, last_name, contact_number, area, crops, barangay, role) 
                                VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$rsbsa_num, 123, $first_name, $middle_name, $last_name, $contact_number, $area, $crops, $brgy, 1]);
    }

}

$_SESSION['success_message'] = 'Successfully added farmers!';
header('Location: ../farmers.php');
exit();

function checkCrops($cropName){
    $crops = new Crops('crops');
    $crops = $crops->all();
    foreach($crops as $crop){
        if(strtolower($crop['name']) == strtolower($cropName)){
            return $crop['id'];
        }
    }
    return false;
}

function checkBrgy($brgyName){
    $brgys = new Barangay('barangay');
    $brgys = $brgys->all();

    foreach($brgys as $brgy){
        if(strtolower($brgy['barangay_name']) == strtolower($brgyName)){
            return $brgy['id'];
        }
    }
    return false;
}

function checkFarmer($farmerNum){
    $farmers = new Farmers('farmers');
    $farmers = $farmers->all();

    foreach($farmers as $farmer){
        if($farmer['rsbsa_num'] == $farmerNum){
            return true;
        }
    }

    return false;
}