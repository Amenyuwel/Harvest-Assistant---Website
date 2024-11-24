<?php


if(isset($_POST['username']) && isset($_POST['password'])){
    require_once "db_config.php";
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM admintbl WHERE username='$username' and password='$password'";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        echo "Login Succesfully";
    }else{
        echo"Ivalid";
    }
}
