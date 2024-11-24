<?php
include 'conn.php';
if(isset($_GET['deleteid'])){
    $id=$_GET['deleteid'];

    // JavaScript confirmation dialog
    echo "<script>
            if(confirm('Are you sure you want to delete this item?')) {
                window.location.href = 'delete.php?confirm_deleteid=$id';
            } else {
                window.location.href = 'farmers.php'; // Redirect back to the farmers page
            }
          </script>";
} 
elseif(isset($_GET['confirm_deleteid'])) {
    $id=$_GET['confirm_deleteid'];

    $sql="DELETE FROM `farmertbl` WHERE id=$id";
    $result=mysqli_query($conn,$sql);
    if($result){
        echo "<script>alert('Item deleted successfully!');</script>";
        header('location: farmers.php');
    }else{
        die("Connection failed: " . $conn->connect_error);
    }
}
