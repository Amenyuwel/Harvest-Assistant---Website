<?php
session_start();

if(isset($_SESSION['user_login'])){
    header('Location: index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harvest Assistant</title>
    <link rel="icon" type="img/jpg" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/login.css?v=1">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body{
            background-image: url("assets/images/bg.jpg");
        }
    </style>
</head>
<body>
    <div class="wrap">
        <form action="scripts/login.php" method="post" >
            <h1>Login</h1>
            <div class="input">
                <input type="text" placeholder="Username" name="username" required>
                <i class='bx bx-user'></i>
            </div>

            <div class="input">
                <input type="password" placeholder="Password" name="password"  required>
                <i class='bx bx-lock-alt'></i>
            </div>


            <button type="submit" class="btnlogin" >Login</button>

        </form>
    </div>

    <script>
        <?php
        if(isset($_SESSION['error_message'])) {
            echo "alert('". $_SESSION['error_message'] ."');";
            unset($_SESSION['error_message']);
        }
        ?>
    </script>

</body>
</html>