<?php
include 'conn.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from the request
$rsbsa_num = $_POST['rsbsa_num'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];

// Check if the current password matches
$sql = "SELECT password FROM farmers WHERE rsbsa_num='$rsbsa_num'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['password'] === $current_password) {
        // Update to the new password
        $update_sql = "UPDATE your_table_name SET password='$new_password' WHERE rsbsa_num='$rsbsa_num'";
        if ($conn->query($update_sql) === TRUE) {
            echo "Password changed successfully";
            // Log the password change
            $log_sql = "INSERT INTO password_change_log (rsbsa_num, old_password, new_password, change_date) VALUES ('$rsbsa_num', '$current_password', '$new_password', NOW())";
            $conn->query($log_sql);
        } else {
            echo "Error updating password: " . $conn->error;
        }
    } else {
        echo "Current password is incorrect";
    }
} else {
    echo "User not found";
}

$conn->close();
?>
