<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harvest Assistant</title>
    <link rel="icon" type="jpg/pngs" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/update.css?v=2">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel= "stylesheet" href= "https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css" >
</head>
<body>
    
    <div class="container">

        <div class="sidebar">
            <ul>
                <li>
                    <a href="">
                        <span class="icon" ><ion-icon name="leaf-outline"></ion-icon></span>
                        <span class="title" >Harvest Assistant</span>
                    </a>
                </li>

                <li>
                    <a href="index.php">
                        <span class="icon" ><ion-icon name="home-outline" ></ion-icon></span>
                        <span class="title" >Dashboard</span>
                    </a>
                    <span class="htitle" >Dashboard</span>
                </li>

                <li>
                    <a href="farmers.php">
                        <span class="icon" ><ion-icon name="people-outline"></ion-icon></span>
                        <span class="title" >Farmers</span>
                    </a>
                    <span class="htitle" >Farmers</span>
                </li>
                
                <li>
                    <a href="analytics.php">
                        <span class="icon" ><ion-icon name="bar-chart-outline"></ion-icon></span>
                        <span class="title" >Analytics</span>
                    </a>
                    <span class="htitle" >Analytics</span>
                </li>

                <li>
                    <a href="report.php">
                        <span class="icon" ><ion-icon name="alert-circle-outline"></ion-icon></span>
                        <span class="title" >Report</span>
                    </a>
                    <span class="htitle" >Report</span>
                </li>

                <li>
                    <a href="messages.php">
                        <span class="icon" ><ion-icon name="chatbubble-outline"></ion-icon></span>
                        <span class="title" >Messages</span>
                    </a>
                    <span class="htitle" >Messages</span>
                </li>

                <li>
                    <a href="logout.php">
                        <span class="icon" ><ion-icon name="log-out-outline"></ion-icon></span>
                        <span class="title" >Sign Out</span>
                    </a>
                    <span class="htitle" >Sign Out</span>
                </li>

            </ul>
        </div>
        

        <div class="main">
            <div class="topbar">
                <div class="toggle"><ion-icon name="menu-outline"></ion-icon></div>
                
                <div class="search">
                    <label>
                        <input type="text" id="searchinput" placeholder="Search" >
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>       

                <div class="user">

                    <div class="topicon">
                        <span><ion-icon name="chatbubbles-outline"></ion-icon></span>
                        <span><ion-icon name="notifications-outline"></ion-icon></span>
                    </div>

                    <img src="assets/images/pfp.jpg" alt="">
                        <div class="userdes">
                            <p>Emerson Alvarado</p>
                            <small>Admin</small>
                        </div>
                </div>
            </div>
            <h2>Report</h2>

            
         

        </div>
    </div>





    <script src="assets/js/main.js" ></script>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>
</html>