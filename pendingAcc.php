<?php
session_start();

include 'layout/header.php';
include 'models/Models.php';
include 'models/FarmersSignUp.php';
include 'models/Farmers.php';
include 'models/Admin.php';
include 'models/Crops.php';
include 'models/Barangay.php';

if (!isset($_SESSION['user_login'])) {
    header('Location: login.php');
    exit();
}

// Initializing the models
$admin = new Admin('admins');
$farmersSignUp = new FarmersSignUp('farmers_signup');

// Fetch crops and barangays data (not used directly here, but good for consistency)
$crops = new Crops('crops');
$barangays = new Barangay('barangay');

$user = $_SESSION['user_login'];

// Fetch admin data
$admin = $admin->where(['username' => $user])->get()[0];

// Fetch only pending farmers (is_approve = 0)
$farmers = $farmersSignUp->getPendingFarmers(); // Fetch pending farmers only
?>
<link rel="stylesheet" href="assets/css/farmers.css?v=4">
<link rel="stylesheet" href="css/farmer.css">
</head>

<body>

<div class="container">
    <?php include 'layout/sidebar.php'; ?>
    <?php include 'layout/nav.php'; ?>

    <!-- ================ Farmer Details List ================= -->
    <div class="details">
        <div class="farmerlist">
            <div style="margin-bottom: 2em">
                <h1 style="text-align:center; margin-bottom: 30px;">Pending Accounts</h1>
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success_message'] ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error_message'] ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>
            </div>

            <table>
            <thead>
    <tr>
        <th>Full Name</th>
        <th>Contact Number</th>
        <th>Area</th>
        <th>Crop</th>
        <th>Barangay</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    <?php foreach ($farmers as $farmer) { ?>
    <tr>
        <td><?= $farmer['first_name'] ?> <?= $farmer['middle_name'] ?? '' ?> <?= $farmer['last_name'] ?></td>
        <td><?= $farmer['contact_number'] ?></td>
        <td><?= $farmer['area'] ?> ha</td>
        <td><?= $farmer['crop_name'] ?? 'N/A' ?></td>
        <td><?= $farmer['barangay_name'] ?? 'N/A' ?></td>
        <td>
            <!-- Approve Farmer Button -->
            <a href="scripts/approve-farmer.php?id=<?= $farmer['id'] ?>" class="btn-action edit" title="Approve Farmer"
               style="display: inline-flex; align-items: center; padding: 10px; border-radius: 5px; color: white; text-decoration: none; background-color: #4CAF50; margin: 5px;">
               <i class="las la-check" style="font-size: 20px;"></i>
            </a>

            <!-- Decline Farmer Button -->
            <a href="scripts/decline-farmer.php?id=<?= $farmer['id'] ?>" class="btn-action delete"
               title="Decline Farmer"
               onclick="return confirm('Are you sure you want to decline this farmer?');"
               style="display: inline-flex; align-items: center; padding: 10px; border-radius: 5px; color: white; text-decoration: none; background-color: #f44336; margin: 5px;">
               <i class="las la-trash" style="font-size: 20px;"></i>
            </a>
        </td>
    </tr>
    <?php } ?>
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
</script>

<?php include 'layout/footer.php'; ?>
