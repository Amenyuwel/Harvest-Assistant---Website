<?php

include '../config/conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die('This page cannot be accessed through GET and PUT requests!');
}

// Get form data with default values for optional fields
$rsbsa_num = $_POST['rsbsa_num'];
$firstName = $_POST['first_name'];
$middleName = $_POST['middle_name'] ?? '';
$lastName = $_POST['last_name'];
$extensionName = $_POST['extension_name'] ?? '';
$sex = $_POST['sex'];
$contactNumber = $_POST['contact_number'];
$area = $_POST['area'];
$purok = $_POST['purok'];
$street = $_POST['street'];
$city = $_POST['city'];
$province = $_POST['province'];
$region = $_POST['region'];
$birthdate = $_POST['birthdate'];
$birthplace = $_POST['brithplace'] ?? ''; // Default to empty if not set
$crop_name = $_POST['crop'] ?? null; // Default to null if not set
$barangay_name = $_POST['barangay']; // Barangay name from form

// MAO NIIIII  Get barangay_id based on barangay name
$sql = "SELECT id FROM barangay WHERE barangay_name = ?";
$barangayStmt = $conn->prepare($sql);
$barangayStmt->execute([$barangay_name]);
$barangayRow = $barangayStmt->fetch();

if (!$barangayRow) {
    $_SESSION['error_message'] = 'Invalid barangay selected!';
    header('Location: ../PestI.php');
    exit();
}

// // DIRI PUD PERO CROP  Get crop_id  based on crop name
$sql = "SELECT id FROM crops WHERE crop_name = ?";
$cropStmt = $conn->prepare($sql);
$cropStmt->execute([$crop_name]);
$cropRow = $cropStmt->fetch();

if (!$cropRow) {
    $_SESSION['error_message'] = 'Invalid crop selected!';
    header('Location: ../PestI.php');
    exit();
}

$barangay_id = $barangayRow['id'];
$crop_id = $cropRow['id'];

// Check if the farmer already exists based on rsbsa_num
$sql = "SELECT * FROM farmers WHERE rsbsa_num = ?";
$checkFarmer = $conn->prepare($sql);
$checkFarmer->execute([$rsbsa_num]);

if ($checkFarmer->fetch()) {
    $_SESSION['error_message'] = 'Farmer with this RSBSA number already exists!';
    header('Location: ../PestI.php');
    exit();
}

// Hash the default password
$defaultPassword = '123';

// Insert the new farmer into the database
$sql = "INSERT INTO farmers (
            rsbsa_num, first_name, middle_name, last_name, 
            ext_name, sex, contact_number, area, purok, street, city, province, 
            region, birthdate, birthplace, crop_id, barangay_id, role_id, password
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt->execute([
    $rsbsa_num, $firstName, $middleName, $lastName, 
    $extensionName, $sex, $contactNumber, $area, $purok, $street, 
    $city, $province, $region, $birthdate, $birthplace, $crop_id, 
    $barangay_id, 1, // Default role_id
    $defaultPassword // Hashed default password
])) {
    $_SESSION['success_message'] = 'Successfully added new farmer!';
} else {
    $_SESSION['error_message'] = 'Failed to add new farmer.';
}

// Get Form 1 data
$lastName = $_POST['last_name'];
$firstName = $_POST['first_name'];
$middleName = $_POST['middle_name'] ?? '';  // Optional
$extensionName = $_POST['extension_name'] ?? '';  // Optional
$sex = $_POST['sex'];
$contactNumber = $_POST['contact_number'];
$purok = $_POST['purok'];
$street = $_POST['street'];
$barangay = $_POST['barangay'];
$city = $_POST['city'];
$province = $_POST['province'];
$region = $_POST['region'];


// Get Form 2 data
$landlineNumber = $_POST['landline_number'] ?? '';  // Optional
$birthdate = $_POST['birthdate'];
$birthplace = $_POST['brithplace'];
$religion = $_POST['religion'];
$civilStatus = $_POST['civil_status'];
$spouseName = $_POST['spouse_name'] ?? '';  // Optional
$education = $_POST['education'];
$pwd = $_POST['pwd'];
$fourPs = $_POST['4ps'];
$indigenous = $_POST['indigenous'];
$govId = $_POST['gov_id'] ?? '';  // Optional
$idNum = $_POST['id_num'] ?? '';  // Optional
$farmersAssociation = $_POST['farmers_association'];
$emergencyName = $_POST['emergency_name'] ?? '';  // Optional
$emergencyNumber = $_POST['emergency_number'] ?? '';  // Optional

// Get Form 3 data
$mothersName = $_POST['mothers_name'];
$householdHead = $_POST['household_head'];
$householdName = $_POST['household_name'] ?? '';  // Optional if household_head is Yes
$householdRelationship = $_POST['household_relationship'] ?? '';  // Optional
$householdMembers = $_POST['household_members'] ?? 0;
$numMale = $_POST['num_male'] ?? 0;
$numFemale = $_POST['num_female'] ?? 0;
$mainLivelihood = $_POST['main_livelihood'];
$farmingActivity = $_POST['farming_act'];
$grossFarmingIncome = $_POST['gross_farming'] ?? 0;
$grossNonFarmingIncome = $_POST['gross_non_farming'] ?? 0;

$sql = "SELECT * FROM farmers_info WHERE rsbsa_num = ?";
$checkFarmer = $conn->prepare($sql);
$checkFarmer->execute([$rsbsa_num]);

if ($checkFarmer->fetch()) {
    $_SESSION['error_message'] = 'Farmer with this RSBSA number already exists!';
    header('Location: ../PestI.php');
    exit();
}

// Insert the new farmer into the database
$sql = "INSERT INTO farmers_info (
            last_name, first_name, middle_name, extension_name, sex, contact_number, purok, 
            street, barangay, city, province, region, landline_number, birthdate, brithplace, 
            religion, civil_status, spouse_name, education, pwd, 4ps, indigenous, gov_id, 
            id_num, farmers_association, emergency_name, emergency_number, mothers_name, 
            household_head, household_name, household_relationship, household_members, num_male, 
            num_female, main_livelihood, farming_act, gross_farming, gross_non_farming
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt->execute([
    $lastName, $firstName, $middleName, $extensionName, $sex, $contactNumber, $purok, $street, 
    $barangay, $city, $province, $region, $landlineNumber, $birthdate, $birthplace, $religion, 
    $civilStatus, $spouseName, $education, $pwd, $fourPs, $indigenous, $govId, $idNum, 
    $farmersAssociation, $emergencyName, $emergencyNumber, $mothersName, $householdHead, 
    $householdName, $householdRelationship, $householdMembers, $numMale, $numFemale, 
    $mainLivelihood, $farmingActivity, $grossFarmingIncome, $grossNonFarmingIncome
])) {
    $_SESSION['success_message'] = '';
} else {
    $_SESSION['error_message'] = 'Failed to add new farmer.';
}

// Insert Form 4/PARCEL 1 data into farm_land_description
$farmLocation = $_POST['farm_location'];
$totalArea = $_POST['total_area'];
$ownershipDocNum = $_POST['ownership_doc_num'];
$ownershipType = $_POST['ownership_type'];
$ownerName = $_POST['owner_name'];
$ancestralDomain = $_POST['ancestral_domain'];
$agrarianBeneficiary = $_POST['agrarian_benefeciary'] ?? 0;
$crop = $_POST['crop'] ?? '';  // Optional
$area = $_POST['area'] ?? 0;  // Optional
$farmType = $_POST['farm_type'] ?? '';  // Optional
$organicPractitioner = $_POST['organic_practitioner'];
$remarks = $_POST['remarks'] ?? '';

// Insert Form 5/PARCEL 2 data into farm_land_description
$farmLocation2 = $_POST['farm_location2'] ?? '';
$totalArea2 = $_POST['total_area2'] ?? 0;
$ownershipDocNum2 = $_POST['ownership_doc_num2'] ?? '';
$ownershipType2 = $_POST['ownership_type2'] ?? '';
$ownerName2 = $_POST['owner_name2'] ?? '';
$ancestralDomain2 = $_POST['ancestral_domain2'] ?? '';
$agrarianBeneficiary2 = $_POST['agrarian_benefeciary2'] ?? 0;
$crop2 = $_POST['crop2'] ?? '';  // Optional
$area2 = $_POST['area2'] ?? 0;  // Optional
$farmType2 = $_POST['farm_type2'] ?? '';  // Optional
$organicPractitioner2 = $_POST['organic_practitioner2'] ?? '';
$remarks2 = $_POST['remarks2'] ?? '';

// Insert Form 6/PARCEL 3 data into farm_land_description
$farmLocation3 = $_POST['farm_location3'] ?? '';
$totalArea3 = $_POST['total_area3'] ?? '';
$ownershipDocNum3 = $_POST['ownership_doc_num3'] ?? '';
$ownershipType3 = $_POST['ownership_type3'] ?? '';
$ownerName3 = $_POST['owner_name3'] ?? '';
$ancestralDomain3 = $_POST['ancestral_domain3'] ?? '';
$agrarianBeneficiary3 = $_POST['agrarian_benefeciary3'] ?? 0;
$crop3 = $_POST['crop3'] ?? '';  // Optional
$area3 = $_POST['area3'] ?? 0;  // Optional
$farmType3 = $_POST['farm_type3'] ?? '';  // Optional
$organicPractitioner3 = $_POST['organic_practitioner3'] ?? '';
$remarks3 = $_POST['remarks3'] ?? '';

// Insert the farm land description data into the 'farm_land_description' table
$sql = "INSERT INTO farm_land_description (
            farm_location, total_area, ownership_doc_num, ownership_type, owner_name, 
            ancestral_domain, agrarian_benefeciary, crop, area, farm_type, 
            organic_practitioner, remarks, farm_location2, total_area2, ownership_doc_num2, ownership_type2, owner_name2, 
            ancestral_domain2, agrarian_benefeciary2, crop2, area2, farm_type2, 
            organic_practitioner2, remarks2, farm_location3, total_area3, ownership_doc_num3, ownership_type3, owner_name3, 
            ancestral_domain3, agrarian_benefeciary3, crop3, area3, farm_type3, 
            organic_practitioner3, remarks3
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt->execute([
    $farmLocation, $totalArea, $ownershipDocNum, $ownershipType, $ownerName, 
    $ancestralDomain, $agrarianBeneficiary, $crop, $area, $farmType, 
    $organicPractitioner, $remarks, $farmLocation2, $totalArea2, $ownershipDocNum2, $ownershipType2, $ownerName2, 
    $ancestralDomain2, $agrarianBeneficiary2, $crop2, $area2, $farmType2, 
    $organicPractitioner2, $remarks2, $farmLocation3, $totalArea3, $ownershipDocNum3, $ownershipType3, $ownerName3, 
    $ancestralDomain3, $agrarianBeneficiary3, $crop3, $area3, $farmType3, 
    $organicPractitioner3, $remarks3
])) {
    $_SESSION['success_message'] .= ' ';
} else {
    $_SESSION['error_message'] = 'Failed to add farm land description.';
}

$rsbsa_num = $_POST['rsbsa_num'];
// Insert the new farmer into the database
$sql = "INSERT INTO farmers_info (rsbsa_num) VALUES (?)";

$stmt = $conn->prepare($sql);

if ($stmt->execute([$rsbsa_num])) {
$_SESSION['success_message'] = 'Successfully added new farmer';
} else {
$_SESSION['error_message'] = 'Failed to add rsbsa num.';
}



header('Location: ../farmers.php');
exit();

?>
