<?php
session_start();

include 'layout/header.php';
include 'models/Models.php';
include 'models/Admin.php';
include 'models/Farmers.php';
include 'models/Crops.php';
include 'models/Barangay.php';
include 'models/Harvest.php';

// Redirect if not logged in
if (!isset($_SESSION['user_login'])) {
    header('Location: login.php');
    exit();
}

$admin = new Admin('admins');
$farmers = new Farmers('farmers');
$crops = new Crops('crops');
$barangays = new Barangay('barangay');
$harvestModel = new Harvest();

$user = $_SESSION['user_login'];
$admin = $admin->where(['username' => $user])->get()[0];

// Pagination calculation
$page = $_GET['page'] ?? 1;
$paginate = ceil(count($farmers->all()) / 10);  // Use ceil() for proper pagination
$farmers = $farmers->farmers((int)$page);  // Fetch farmers for the current page
$crops = $crops->all();
$barangays = $barangays->all();

// Count the number of corn and rice farmers
$cornFarmersCount = 0;
$riceFarmersCount = 0;

foreach ($farmers as $farmer) {
    if ($farmer['crop_name'] === 'Corn') {
        $cornFarmersCount++;
    } elseif ($farmer['crop_name'] === 'Rice') {
        $riceFarmersCount++;
    }
}


?>
<link rel="stylesheet" href="assets/css/farmers.css?v=5">
<link rel="stylesheet" href="css/farmer.css?v=6">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
</head>

<body>

<div class="container">
    <?php include 'layout/sidebar.php'; ?>
    <?php include 'layout/nav.php'; ?>

    <!-- ================ Farmers list ================= -->
    <div class="details">
        <div class="farmerlist">
            <div style="margin-bottom: 2em">
                <h1 style="text-align:center; margin-bottom: 30px;">Farmers' Information</h1>

                <?php
                // Success and error message display
                if (isset($_SESSION['success_message'])) {
                    echo '<div class="alert-success">' . $_SESSION['success_message'] . '</div>';
                    unset($_SESSION['success_message']);
                }

                if (isset($_SESSION['error_message'])) {
                    echo '<div class="alert-error">' . $_SESSION['error_message'] . '</div>';
                    unset($_SESSION['error_message']);
                }
                ?>

                <button class="btn-main" onclick="window.location.href='AddNF.php'"><i class="las la-user-plus addicon"></i> <span>Add Farmer</span></button>
                <button class="btn-main" onclick="popup(popupcsv)"><i class="las la-file-excel addicon"></i> <span>Batch Upload Farmer</span></button>
                <button class="btn-main" onclick="popup(popupbarangay)"><i class="las la-map-pin addicon"></i> <span>Add Barangay</span></button>
                <button class="btn-main" onclick="downloadTable()"><i class="las la-file-excel addicon"></i> <span>Download as Excel</span></button>
                
            
        
                



            </div>

            <?php
                if (isset($_SESSION['feedback'])) {
                    $feedback = $_SESSION['feedback'];
                    echo "<div class='feedback {$feedback['type']}'>{$feedback['message']}</div>";
                    unset($_SESSION['feedback']); // Remove feedback after displaying
                }
                ?>

                    <!-- Update Farmer Popup -->
                <div id="popupUpdateFarmer" class="popup">
                    <div class="popup-content">
                        <span class="close" onclick="closepopup(popupUpdateFarmer)">&times;</span>
                        <h3>Update Farmer Information</h3>
                        <form id="updateFarmerForm" action="scripts/update-farmer.php" method="POST">
                            <input type="hidden" id="update_farmer_id" name="farmer_id" required>
                            <label class="lbl" for="update_rsbsa_num">RSBSA Number</label>
                            <input class="inp" type="text" id="update_rsbsa_num" name="rsbsa_num" required>

                            <label class="lbl" for="update_fname">First Name</label>
                            <input class="inp" type="text" id="update_fname" name="fname" required>

                            <label class="lbl" for="update_mname">Middle Name</label>
                            <input class="inp" type="text" id="update_mname" name="mname">

                            <label class="lbl" for="update_lname">Last Name</label>
                            <input class="inp" type="text" id="update_lname" name="lname" required>

                            <label class="lbl" for="update_crop">Select Crop</label>
                            <select class="inp" id="update_crop" name="crop" required>
                                <?php foreach ($crops as $crop) { ?>
                                    <option value="<?= $crop['id'] ?>"><?= $crop['crop_name'] ?></option>
                                <?php } ?>
                            </select>

                            <label class="lbl" for="update_area">Area</label>
                            <input class="inp" type="number" step="0.01" id="update_area" name="area" required>

                            <label class="lbl" for="update_barangay">Select Barangay</label>
                            <select class="inp" id="update_barangay" name="barangay" required>
                                <?php foreach ($barangays as $barangay) { ?>
                                    <option value="<?= $barangay['id'] ?>"><?= $barangay['barangay_name'] ?></option>
                                <?php } ?>
                            </select>

                            <label class="lbl" for="update_contact">Contact</label>
                            <input class="inp" type="number" id="update_contact" name="contact" required>

                            <button class="addfarmer" type="submit">Save Changes</button>
                        </form>
                    </div>
                </div> 

                    <!-- Severity Popup -->
                <div id="popopSeverity" class="popup">
                    <div class="popup-content">
                        <span class="close" onclick="closepopup(popopSeverity)">&times;</span>
                        <h3>Information preview for severity submission</h3>
                        <form id="severityForm" action="scripts/submit-severity.php" method="POST">
                            <input type="hidden" id="severity_farmer_id" name="farmer_id" required>
                            
                            <label class="lbl" for="severity_rsbsa_num">RSBSA Number</label>
                            <input class="inp" type="text" id="severity_rsbsa_num" name="rsbsa_num" readonly>

                            <label class="lbl" for="severity_crop">Crop</label>
                            <select class="inp" id="severity_crop" name="crop" disabled required>
                                <?php foreach ($crops as $crop) { ?>
                                    <option value="<?= $crop['id'] ?>"><?= $crop['crop_name'] ?></option>
                                <?php } ?>
                            </select>

                            <label class="lbl" for="severity_area">Area</label>
                            <input class="inp" type="number" step="0.01" id="severity_area" name="area" readonly required>

                            <label class="lbl" for="severity_barangay">Barangay</label>
                            <select class="inp" id="severity_barangay" name="barangay" disabled required>
                                <?php foreach ($barangays as $barangay) { ?>
                                    <option value="<?= $barangay['id'] ?>"><?= $barangay['barangay_name'] ?></option>
                                <?php } ?>
                            </select>

                            <label class="lbl" for="severity">Severity (%)</label>
                            <input class="inp" type="number" id="severity" name="severity" required>

                            <button class="addfarmer" type="submit">Submit Severity</button>
                        </form>
                    </div>
                </div>


              <!-- Add Barangay -->
              <div id="popupbarangay" class="popup">
                    <div class="popup-content">
                    <span class="close" onclick="closepopup(popupbarangay)">&times;</span>
                        <h3>Add New Barangay</h3>
                        <form action="scripts/add-barangay.php" method="POST">
                            <label class="lbl" for="barangay_name">Barangay Name</label>
                            <input class="inp" type="text" id="barangay_name" name="barangay_name" required>
                            <button class="addfarmer" type="submit">Add Barangay</button>
                        </form>
                    </div>
                </div>

                <!-- Upload File -->
                <div id="popupcsv" class="popup">
                    <div class="popup-content">
                        <span class="close" onclick="closepopup(popupcsv)">&times;</span>
                        <h3>Batch Upload</h3>
                        <form action="scripts/batch-upload.php" method="POST" name="upload_file" enctype="multipart/form-data">
                            <input class="file-input" type="file" name="csv_farmers" accept=".csv" required>
                            <button class="addfarmer" type="submit">Upload File</button>
                        </form>
                    </div>
                </div>

            <!-- For Search -->

            <div style="display: flex; justify-content: end; margin-bottom: 20px;">
            
                <input type="text" id="searchBar" placeholder="Search Farmers..." onkeyup="filterFarmers()" style="padding-left: 30px; width: 300px; height: 35px; border-radius: 5px; border: 1px solid #ccc;">
                <i class="las la-search" style="position: relative; left: -293px; font-size: 20px; color: #888; top: 8px;"></i>


                  <!-- Sorting Dropdown -->
                <label for="sortOptions" style="margin-right: 10px; font-weight: bold; align-self: center;">Sort by:</label>
                <select id="sortOptions" onchange="sortTable()" style="width: 200px; height: 35px; border-radius: 5px; padding: 5px; border: 1px solid #ccc; font-size: 14px; background-color: #f9f9f9; color: #555;">
                    <option value="0" disabled>Select</option>
                    <option value="1">Full Name (A-Z)</option>
                    <option value="2">Crop (A-Z)</option>
                    <option value="3">Area (Smallest to Largest)</option>
                    <option value="4">Barangay (A-Z)</option>
                </select>

                <div style="margin-left:20px;">
                    <h4>Total Corn Farmers: <span style="color: blue;"><?= $cornFarmersCount ?></span></h4>
                    <h4>Total Rice Farmers: <span style="color: blue;"><?= $riceFarmersCount ?></span></h4>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>RSBSA Number</th>
                        <th>Full Name</th>
                        <th>Crop</th>
                        <th>Area</th>
                        <th>Barangay</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody  id="farmerTable">
                    <?php
                    foreach ($farmers as $farmer) {
                    ?>
                        <tr>
                            <td style="text-align:center;" class="row-number"></td>
                            <td><?= $farmer['rsbsa_num'] ?></td>
                            <td><?= $farmer['first_name'] ?>
                                <?= $farmer['middle_name'] ?? '' ?>
                                <?= $farmer['last_name'] ?>
                            </td>
                            <td><?= $farmer['crop_name'] ?></td>
                            <td><?= $farmer['area'] ?> ha</td>
                            <td><?= $farmer['barangay_name'] ?></td>
                            <td><?= $farmer['contact_number'] ?></td>
                            <td style="display:flex;align;align-content: center;justify-content: center;">
                                <!-- Edit Action Button -->
                                <a href="#" class="btn-action edit" 
                                    title="Edit Farmer" 
                                    onclick="openUpdateFarmer(<?= $farmer['id'] ?>, '<?= $farmer['rsbsa_num'] ?>', '<?= $farmer['first_name'] ?>', '<?= $farmer['middle_name'] ?? '' ?>', '<?= $farmer['last_name'] ?>', <?= $farmer['crop_id'] ?>, <?= $farmer['area'] ?>, <?= $farmer['barangay_id'] ?>, '<?= $farmer['contact_number'] ?>')"
                                    style="display: inline-flex; align-items: center; padding: 10px; border-radius: 5px; color: white; text-decoration: none; background-color: #4CAF50; margin: 5px; transition: background-color 0.3s;"
                                    onmouseover="this.style.backgroundColor='#45a049';" 
                                    onmouseout="this.style.backgroundColor='#4CAF50';">
                                    <i class="las la-edit" style="font-size: 20px;"></i>
                                </a>

                                <!-- Delete Action Button -->
                                <a href="scripts/delete-farmer.php?id=<?= $farmer['id'] ?>" class="btn-action delete" 
                                    title="Delete Farmer" 
                                    onclick="return confirm('Are you sure you want to delete this farmer?');"
                                    style="display: inline-flex; align-items: center; padding: 10px; border-radius: 5px; color: white; text-decoration: none; background-color: #f44336; margin: 5px; transition: background-color 0.3s;"
                                    onmouseover="this.style.backgroundColor='#e53935';" 
                                    onmouseout="this.style.backgroundColor='#f44336';">
                                    <i class="las la-trash" style="font-size: 20px;"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>

        </div>

        <!-- Pagination -->
        <div style="display:flex; justify-content:flex-end; margin:10px; gap: 5px">
            <?php
            $count = 1;
            while ($count <= $paginate) {
            ?>
                <a href="./farmers.php?page=<?= $count ?>" class="btn-main <?= ($count == $page) ? 'active' : '' ?>">
                    <?= $count ?>
                </a>
            <?php
                $count++;
            }
            ?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script>
// Function to download the table as an Excel file
function downloadTable() {
    const table = document.getElementById("farmerTable");
    
    // Create a workbook and add the table data to it
    const wb = XLSX.utils.table_to_book(table, { sheet: "Farmers" });
    
    // Generate the Excel file and prompt the user to download
    XLSX.writeFile(wb, "Farmers_Data.xlsx");
}
</script>


<script>
function sortTable() {
    const table = document.getElementById("farmerTable");
    const rows = Array.from(table.rows);
    const sortOption = document.getElementById("sortOptions").value;

    rows.sort((a, b) => {
        let valA, valB;

        switch (sortOption) {
            case "1": // Full Name (A-Z)
                valA = a.cells[2].textContent.trim();
                valB = b.cells[2].textContent.trim();
                return valA.localeCompare(valB);
            case "2": // Crop (A-Z)
                valA = a.cells[3].textContent.trim();
                valB = b.cells[3].textContent.trim();
                return valA.localeCompare(valB);
            case "3": // Area (Smallest to Largest)
                valA = parseFloat(a.cells[4].textContent) || 0;
                valB = parseFloat(b.cells[4].textContent) || 0;
                return valA - valB;
            case "4": // Barangay (A-Z)
                valA = a.cells[5].textContent.trim();
                valB = b.cells[5].textContent.trim();
                return valA.localeCompare(valB);
            default:
                return 0;
        }
    });

    // Re-insert rows in the correct order
    table.innerHTML = "";
    rows.forEach(row => table.appendChild(row));

    // Update the row numbers after sorting
    updateRowNumbers();
}

// Function to update row numbers dynamically
function updateRowNumbers() {
    const table = document.getElementById("farmerTable");
    const rows = table.getElementsByTagName("tr");

    Array.from(rows).forEach((row, index) => {
        const rowNumberCell = row.querySelector('.row-number');
        if (rowNumberCell) {
            rowNumberCell.textContent = index + 1;
        }
    });
}

// Call updateRowNumbers on page load to ensure correct initial numbering
document.addEventListener("DOMContentLoaded", updateRowNumbers);

    function popup(id) {
        id.style.display = 'block';
    }

    function closepopup(id) {
        id.style.display = 'none';
    }

    function openUpdateFarmer(id, rsbsaNum, firstName, middleName, lastName, cropId, area, barangayId, contact) {
            // Populate form fields
            document.getElementById('update_farmer_id').value = id;
            document.getElementById('update_rsbsa_num').value = rsbsaNum;
            document.getElementById('update_fname').value = firstName;
            document.getElementById('update_mname').value = middleName;
            document.getElementById('update_lname').value = lastName;
            document.getElementById('update_crop').value = cropId;
            document.getElementById('update_area').value = area;
            document.getElementById('update_barangay').value = barangayId;
            document.getElementById('update_contact').value = contact;

            popup(popupUpdateFarmer); // Show the popup
    }

    function openUpdateSeverity(id, rsbsaNum, cropId, area, barangayId, severity) {
    // Populate form fields with unique IDs

    
    document.getElementById('severity_farmer_id').value = id;
    document.getElementById('severity_rsbsa_num').value = rsbsaNum;
    document.getElementById('severity_crop').value = cropId;
    document.getElementById('severity_area').value = area;
    document.getElementById('severity_barangay').value = barangayId;
    document.getElementById('severity').value = severity;

    popup(popopSeverity); // Show the popup
}

function filterFarmers() {
        const searchInput = document.getElementById('searchBar').value.toLowerCase();
        const tableRows = document.querySelectorAll("table tbody tr");

        tableRows.forEach(row => {
            const rsbsaNum = row.children[1].textContent.toLowerCase();
            const fullName = row.children[2].textContent.toLowerCase();
            const crop = row.children[3].textContent.toLowerCase();
            const barangay = row.children[5].textContent.toLowerCase();
            const contact = row.children[6].textContent.toLowerCase();

            // Check if any of the row's cells match the search input
            if (
                rsbsaNum.includes(searchInput) ||
                fullName.includes(searchInput) ||
                crop.includes(searchInput) ||
                barangay.includes(searchInput) ||
                contact.includes(searchInput)
            ) {
                row.style.display = ""; // Show row if it matches search
            } else {
                row.style.display = "none"; // Hide row if it doesn't match
            }
        });
    }




</script>

<?php include 'layout/footer.php'; ?>
