<?php
$servername = "localhost";
$username = "u766798681_adminHarvest";
$password = "EmmanBayot_69";
$dbname = "u766798681_dbHarvest";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
