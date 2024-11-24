<?php
session_start();

include 'layout/header.php';
include 'models/Models.php';
include 'models/Admin.php';
include 'models/PestInfo.php'; 


if (!isset($_SESSION['user_login'])) {
    header('Location: login.php');
    exit();
}


$admin = new Admin('admins');
$user = $_SESSION['user_login'];
$admin = $admin->where(['username' => $user])->get()[0];

// Load pest info data
$pestInfo = new PestInfo('pest_info');  // Load data from 'pest_info' table
$pestData = $pestInfo->all();  // Fetch all records from pest_info table
?>
<link rel="stylesheet" href="assets/css/farmers.css?v=6">
<link rel="stylesheet" href="css/farmer.css?v=4">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
</head>

<body>

<div class="container">
    <?php include 'layout/sidebar.php'; ?>
    <?php include 'layout/nav.php'; ?>

    
    <div class="details">
        <!-- <h1 style="text-align:center; margin-bottom: 15px;">Pest Information</h1> -->
        <div class="farmerlist">
            <div style="margin-bottom: 2em">
                    <h1 style="text-align:center; margin-bottom: 30px; ">Pest Information</h1>
        
                    <button class="btn-main" onclick="popup(popupAddPest)"><i class="las la-plus addicon"></i><span>Add New Pest</span></button>

                </div>

                <?php
                if (isset($_SESSION['feedback'])) {
                    $feedback = $_SESSION['feedback'];
                    echo "<div class='feedback {$feedback['type']}'>{$feedback['message']}</div>";
                    unset($_SESSION['feedback']); // Remove feedback after displaying
                }
                ?>

                <!-- ================ Modal for Add Pest New Pest ================= -->
                    <div id="popupAddPest" class="popup">
                        <div class="popup-content">
                            <span class="close" onclick="closepopup(popupAddPest)">&times;</span>
                            <h3>Add New Pest Information</h3>
                            <form id="addPestForm" action="scripts/add-new-pest.php" method="POST">
                                <label class="lbl" for="pest_name">Pest Name</label>
                                <input class="inp" type="text" id="pest_name" name="pest_name" required>

                                <label class="lbl" for="pest_desc">Description</label>
                                <textarea id="pest_desc" name="pest_desc" required></textarea>

                                <label class="lbl" for="pest_reco">Recommendations</label>
                                <textarea id="pest_reco" name="pest_reco" required></textarea>

                                <label class="lbl" for="active_month">Active Month</label>
                                <input class="inp" type="text" id="active_month" name="active_month" required>

                                <label class="lbl" for="season">Season</label>
                                <input class="inp" type="text" id="season" name="season" required>

                                <button class="addfarmer" type="submit">Add Pest</button>
                            </form>
                        </div>
                    </div>


                    <!-- ================ Modal for update pest info ================= -->
                    <div id="popupUpdatePest" class="popup">
                    <div class="popup-content">
                        <span class="close" onclick="closepopup(popupUpdatePest)">&times;</span>
                        <h3>Update Pest Information</h3>
                        <form id="updateFarmerForm" action="scripts/update-pest-info.php" method="POST">
                            <input type="hidden" id="update_pest_id" name="pest_id" required>
                            <label class="lbl" for="update_pest_name">Pest Name</label>
                            <input class="inp" type="text" id="update_pest_name" name="pest_name" required>

                            <label class="lbl" for="update_pest_desc">Description</label>
                            <textarea id="update_pest_desc" name="pest_desc" required></textarea>
                            <!-- <input class="inp" type="text" id="update_fname" name="fname" required> -->

                            <label class="lbl" for="update_pest_reco">Recommendations</label>
                            <textarea id="update_pest_reco" name="pest_reco" required></textarea>
                            <!-- <input class="inp" type="text" id="update_mname" name="mname"> -->

                            <label class="lbl" for="update_active_month">Active Month</label>
                            <input class="inp" type="text" id="update_active_month" name="active_month" required>

                            <label class="lbl" for="update_season">Season</label>
                            <input class="inp" type="text" id="update_season" name="season" required>

                            <button class="addfarmer" type="submit">Save Changes</button>
                        </form>
                    </div>
                </div>
            
            <!-- ================ Pest Information Table ================= -->
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Pest Name</th>
                        <th>Description</th>
                        <th>Recommendations</th>
                        <th>Active Month</th>
                        <th>Season</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1; 
                    foreach ($pestData as $pest) {
                    ?>
                        <tr>
                            <td><?= $counter ?></td> 
                            <td><?= htmlspecialchars($pest['pest_name']) ?></td> 
                            <td><?= htmlspecialchars($pest['pest_desc']) ?></td> 
                            <td><?= htmlspecialchars($pest['pest_reco']) ?></td> 
                            <td><?= htmlspecialchars($pest['active_month']) ?></td>
                            <td><?= htmlspecialchars($pest['season']) ?></td> 
                            <td style="">
                               
                                <a href="#" class="btn-action edit" 
                                   title="Edit Pest Information" 
                                   onclick="openUpdatePestInfo(<?= $pest['id'] ?>, '<?= $pest['pest_name'] ?>', '<?= $pest['pest_desc'] ?>', '<?= $pest['pest_reco'] ?>', '<?= $pest['active_month'] ?>', '<?= $pest['season'] ?>')" 
                                   style="display: inline; position: relative; align-items: center; padding: 10px; border-radius: 5px; color: white; text-decoration: none; background-color: #4CAF50; margin: 5px; transition: background-color 0.3s;"
                                   onmouseover="this.style.backgroundColor='#45a049';" 
                                   onmouseout="this.style.backgroundColor='#4CAF50';">
                                    <i class="las la-edit" style="font-size: 20px;"></i>
                                </a>

                                
                                <a href="scripts/delete-pest-info.php?id=<?= $pest['id'] ?>" class="btn-action delete" 
                                   title="Delete Pest Information"
                                   onclick="return confirm('Are you sure you want to delete this pest information?');"
                                   style="display: ruby; position: relative; align-items: center; padding: 10px; border-radius: 5px; color: white; text-decoration: none; background-color: #f44336; margin: 5px; transition: background-color 0.3s;"
                                   onmouseover="this.style.backgroundColor='#e53935';" 
                                   onmouseout="this.style.backgroundColor='#f44336';">
                                    <i class="las la-trash" style="font-size: 20px;"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                        $counter++;  
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
        function popup(id) {
            id.style.display = 'block'
        }

        function closepopup(id) {
            id.style.display = 'none'
        }

        function openUpdatePestInfo(id, pest_name, pest_desc, pest_reco, active_month, season) {
            // Populate form fields
            document.getElementById('update_pest_id').value = id;
            document.getElementById('update_pest_name').value = pest_name;
            document.getElementById('update_pest_desc').value = pest_desc;
            document.getElementById('update_pest_reco').value = pest_reco;
            document.getElementById('update_active_month').value = active_month;
            document.getElementById('update_season').value = season;

            popup(popupUpdatePest); // Show the popup
        }
    </script>



<?php include 'layout/footer.php'; ?>
