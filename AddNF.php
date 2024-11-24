
<?php
session_start();

include 'models/Models.php';
include 'models/Admin.php';
include 'models/Crops.php';
include 'models/Barangay.php';

if (!isset($_SESSION['user_login'])) {
    header('Location: login.php');
    exit();
}

$admin = new Admin('admins');
$cropsModel = new Crops('crops');
$barangaysModel = new Barangay('barangay');

$user = $_SESSION['user_login'];
$admin = $admin->where(['username' => $user])->get()[0];

$crops = $cropsModel->all();
$barangays = $barangaysModel->all();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/ANF.css?v=10">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <title>Harvest Assistant</title>
    <link rel="icon" type="jpg/pngs" href="assets/images/logo.png">
</head> 
<body> 
    <div class="container">
        <span class="close" onclick="window.location.href='farmers.php'">&times;</span>
        <header>Registration</header>

        <div class="step-row">
            <div id="progress"></div>
            <!-- <div class="step-col"><small>PART 1</small></div>
            <div class="step-col"><small>PART 2</small></div>
            <div class="step-col"><small>PART 3</small></div>
            <div class="step-col"><small>PART 4</small></div>
            <div class="step-col"><small>PART 5</small></div>
            <div class="step-col"><small>PART 6</small></div> -->
        </div>

        <form action="scripts/add-farmer.php" method="POST">
            <!------------------FORM 1----------------------------!-->
            <div class="form first" id="Form1" >
                <div class="details personal">
                    <span class="title">PART I: PERSONAL INFORMATION</span>

                    <div class="fields">

                        <div class="input-field">
                        <label for="">Surname<span style="color: red;">*</span></label>
                            <input type="text" name="last_name" id="last_name" placeholder="" required oninput="updatePreview();">
                        </div>

                        <div class="input-field">
                            <label for="">First Name<span style="color: red;">*</span></label>
                            <input type="text" name="first_name" id="first_name" placeholder="" required oninput="updatePreview();">
                        </div>

                        <div class="input-field">
                            <label for="">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" placeholder="" oninput="updatePreview();">
                        </div>

                        <div class="input-field">
                            <label for="">Extension Name</label>
                            <input type="text" name="extension_name" id="extension_name" placeholder="" oninput="updatePreview();">
                        </div>

                        <div class="input-field">
                            <label for="sex">Sex<span style="color: red;">*</span></label>
                            <div class="radio-group" required>
                                <input type="radio" name="sex" id="male" value="male">
                                <label for="male">Male</label>
                                <input type="radio" name="sex" id="female" value="female">
                                <label for="female">Female</label>
                            </div>
                        </div>

                        <div class="input-field">
                            <label for="">Mobile Number<span style="color: red;">*</span></label>
                            <input type="tel" maxlength="11" name="contact_number" id="" placeholder="" required>
                        </div>

                    </div>
                </div>

                <div class="details ID">
                    <span class="title">Address</span>
                    <div class="fields">

                        <div class="input-field">
                            <label for="">House/Lot/Blog no./Purok<span style="color: red;">*</span></label>
                            <input type="text" name="purok" id="" placeholder="" required>
                        </div>

                        <div class="input-field">
                            <label for="">Street/Sitio/Subdv.<span style="color: red;">*</span></label>
                            <input type="text" name="street" id="" placeholder="" required>
                        </div>
                    
                    

                        <div class="input-field">
                            <label for="barangay">Barangay<span style="color: red;">*</span></label>
                            <select id="barangay" name="barangay" required onchange="generateRsbsaNumber();">
                                <option style="color: #333;" value="" disabled selected>Select Barangay</option>
                                <option value="Apopong">Apopong</option>
                                <option value="Baluan">Baluan</option>
                                <option value="Buayan">Buayan</option>
                                <option value="Bula">Bula</option>
                                <option value="Heights">Heights</option>
                                <option value="Conel">Conel</option>
                                <option value="Dadiangas East">Dadiangas East</option>
                                <option value="Fatima">Fatima</option>
                                <option value="Katangawan">Katangawan</option>
                                <option value="Lagao">Lagao</option>
                                <option value="Ligaya">Ligaya</option>
                                <option value="Mabuhay">Mabuhay</option>
                                <option value="San Isidro">San Isidro</option>
                                <option value="San Jose">San Jose</option>
                                <option value="Tinagacan">Tinagacan</option>
                                <option value="Upper Labay">Upper Labay</option>
                            </select>
                        </div>


                        <div class="input-field">
                            <label for="city">Municipality/City<span style="color: red;">*</span></label>
                            <input type="text" id="city" name="city_display" value="General Santos City" readonly onfocus="updateWithoutIncrement();" required>
                            <input type="hidden" name="city" value="General Santos City">
                        </div>

                        <div class="input-field">
                            <label for="province">Province<span style="color: red;">*</span></label>
                            <input type="text" id="province" name="province_display" value="South Cotabato" readonly onfocus="updateWithoutIncrement();" required>
                            <input type="hidden" name="province" value="South Cotabato">
                        </div>

                       

                        <div class="input-field">
                            <label for="region">Region<span style="color: red;">*</span></label>
                            <input type="text" id="region" name="region_display" value="Region XII - SOCCSKSARGEN" readonly onfocus="updateWithoutIncrement();" required>
                            <input type="hidden" name="region" value="Region XII - SOCCSKSARGEN">
                        </div>


                        

                    </div>

                    <div id="Next1" class="nextBtn" >
                        <span  class="btnText">Next</span>
                        <i class="uil uil-navigator"></i>
                    </div>

                </div>
            </div>

            <!------------------FORM 2----------------------------!-->
            <div class="form second" id="Form2">
                <div class="details address">
                    <span class="title"></span>

                    <div class="fields">


                        <div class="input-field">
                            <label for="">Landline Number</label>
                            <input type="text" name="landline_number" id="" placeholder="" >
                        </div>

                        <div class="input-field">
                            <label for="">Date of Birth<span style="color: red;">*</span></label>
                            <input type="date" name="birthdate" id="" placeholder="" required>
                        </div>

                        <div class="input-field">
                            <label for="">Place of Birth<span style="color: red;">*</span></label>
                            <input type="text"  name="brithplace" id="" placeholder="" required>
                        </div>

                        <div class="input-field">
                            <label for="religion">Religion<span style="color: red;">*</span></label>
                            <input list="rreligion" id="religion" name="religion" placeholder="Choose or specify if others" required>
                            <datalist id="rreligion">
                                <option value="Christianity">
                                <option value="Islam">
                            </datalist>
                        </div>

                        <div class="input-field">
                            <label for="civil_status">Civil Status <span style="color: red;">*</span></label>
                            <select id="civil_status" name="civil_status" required>
                                <option style="color: #333;" value="" disabled selected>Select civil status</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="widowed">Widowed</option>
                                <option value="separated">Separated</option>
                            </select>
                        </div>

                        <div class="input-field">
                            <label for="">Name Of Spouse If Married</label>
                            <input type="text" name="spouse_name" id="" placeholder="" >
                        </div>

                    </div>
                </div>

                <div class="details family">
                    <span class="title"></span>

                    <div class="fields">

                        <div class="input-field">
                            <label for="">Highest Formal Education<span style="color: red;">*</span></label>
                            <select id="" name="education" required>
                                <option value="" disabled selected>Select highest formal education</option>
                                <option value="Pre-school">Pre-school</option>
                                <option value="Elementary">Elementary</option>
                                <option value="High School (non K-12)">High School (non K-12)</option>
                                <option value="Junior High School (K-12)">Junior High School (K-12)</option>
                                <option value="Senior High School (K-12)">Senior High School (K-12)</option>
                                <option value="College">College</option>
                                <option value="Vocational">Vocational</option>
                                <option value="Post-graduate">Post-graduate</option>
                                <option value="None">None</option>
                            </select>
                        </div>

                        <div class="input-field">
                            <label for="PWD">Person With Disability (PWD)<span style="color: red;">*</span></label>
                            <div class="radio-group" required>
                                <input type="radio" name="pwd" id="yes" value="yes">
                                <label for="Yes">Yes</label>
                                <input type="radio" name="pwd" id="no" value="no">
                                <label for="No">No</label>
                            </div>
                        </div>

                        <div class="input-field">
                            <label for="4P's">4P's Beneficiary?<span style="color: red;">*</span></label>
                            <div class="radio-group" required>
                                <input type="radio" name="4ps" id="4ps" value="Yes">
                                <label for="Yes">Yes</label>
                                <input type="radio" name="4ps" id="4ps" value="No">
                                <label for="No">No</label>
                            </div>
                        </div>

                        <div class="input-field">
                            <label for="indigenous">Member of an Indigenous Group?<span style="color: red;">*</span></label>
                            <input list="iindigenous" id="indigenous" name="indigenous" placeholder="If yes, specify" required>
                            <datalist id="iindigenous">
                                <option value="No">
                                <option value="Aeta">
                                <option value="Ifugao">
                                <option value="Igorot">
                                <option value="Lumad">
                                <option value="Mangyan">
                                <option value="Badjao">
                                <option value="Blaan">
                                <option value="Higaonon">
                                <option value="Ibanag">
                                <option value="Ivatan">
                                <option value="Kalinga">
                                <option value="Manobo">
                                <option value="Maranao">
                                <option value="Subanen">
                                <option value="T'boli">
                                <option value="Tingguian">
                                <option value="Yakan">
                            </datalist>
                        </div>


                        <div class="input-field">
                            <label for="GovID">With Government ID?<span style="color: red;">*</span></label>
                            <input list="GGovID" id="GovID" name="gov_id" placeholder="If yes, specify ID Type" required>
                            <datalist id="GGovID">
                                <option value="No">
                                <option value="Philippine Passport">
                                <option value="Driver's License">
                                <option value="UMID (Unified Multi-Purpose ID)">
                                <option value="SSS ID">
                                <option value="GSIS ID">
                                <option value="PhilHealth ID">
                                <option value="TIN ID">
                                <option value="Voter's ID">
                                <option value="PRC ID">
                                <option value="Postal ID">
                                <option value="Barangay ID">
                                <option value="National ID (PhilSys)">
                            </datalist>
                        </div>


                        <div class="input-field">
                            <label for="">ID Number</label>
                            <input type="text" name="id_num" id="" placeholder="Enter the ID Number" >
                        </div>

                        <div class="input-field">
                            <label for="">Member of any Farmers Association/Cooperative?<span style="color: red;">*</span></label>
                            <input list="FAC" id="" name="farmers_association" placeholder="If yes, specify" required>
                            <datalist id="FAC">
                                <option value="No">
                            </datalist>
                        </div>

                        <!-- <hr style="bottom: 160px; height:3px; width: 100%; background-color: black; color:rgb(6, 6, 6); position: absolute;"> -->
                        
                        <div class="input-field">
                            <label for="">Person to notify in case of emergency<span style="color: red;">*</span></label>
                            <input type="text" name="emergency_name" id="" placeholder="" >
                        </div>

                        <div class="input-field">
                            <label for="">Contact Number<span style="color: red;">*</span></label>
                            <input type="tel" maxlength="11" name="emergency_number" id="" placeholder="Enter person's contact number" >
                        </div>

                    </div>
                    <div class="buttons">
                        <div id="Back1" class="backBtn" >
                            <i class="uil uil-navigator"></i>
                            <span class="btnText">Back</span>   
                        </div>
                        <div id="Next2" class="nextBtn" >
                            <span class="btnText">Next</span>
                            <i class="uil uil-navigator"></i>
                        </div>
                    </div>
                </div>

            </div>
                
            <!------------------FORM 3----------------------------!-->
            <div class="form third" id="Form3">
            <div class="details additional">
                <span class="title"></span>

                <div class="fields">

                    <div class="input-field">
                        <label for="">Mother's Maiden Name<span style="color: red;">*</span></label>
                        <input type="text" name="mothers_name" id="" placeholder="" required>
                    </div>
                   

                    <div class="input-field">
                        <label for="">Household Head?<span style="color: red;">*</span></label>
                        <div class="radio-group" required>
                            <input type="radio" name="household_head" id="Yes" value="Yes">
                            <label for="Yes">Yes</label>
                            <input type="radio" name="household_head" id="No" value="No">
                            <label for="No">No</label>
                        </div>
                    </div>

                    <div class="input-field">
                        <label for="">If no, name of household head</label>
                        <input type="text" name="household_name" id="" placeholder="">
                    </div>

                    <div class="input-field">
                        <label for="">Relationship</label>
                        <input type="text" name="household_relationship" id="" placeholder="Relationship between the household head" >
                    </div>

                    <div class="input-field">
                        <label for="">No. of living household members</label>
                        <input type="number" name="household_members" id="" placeholder="" >
                    </div>

                    <div class="input-field">
                        <label for="">No. of male</label>
                        <input type="number" name="num_male" id="" placeholder="" >
                    </div>

                    <div class="input-field">
                        <label for="">No. of female</label>
                        <input type="number" name="num_female" id="" placeholder="" >
                    </div>


        
            </div>
            </div>
            <div class="details additional">
                <span class="title">PART II. FARM PROFILE</span>

                <div class="fields">

                    <div class="input-field">
                        <label for="main_livelihood">Main Livelihood<span style="color: red;">*</span></label>
                        <input type="text" id="main_livelihood" name="main_livelihood" value="Farmer" readonly required>
                    </div>
                   

                    <div class="input-field">
                        <label for="">Type of Farming Activity<span style="color: red;">*</span></label>
                        <div class="radio-group" required>
                            <input type="radio" name="farming_act" id="" value="Rice">
                            <label for="Rice">Rice</label>
                            <input type="radio" name="farming_act" id="" value="Corn">
                            <label for="Corn">Corn</label>
                        </div>
                    </div>

                    <div class="input-field">
                        <label for="">Gross Annual Income Last Year (Farming)</label>
                        <input type="number" name="gross_farming" id="" placeholder="">
                    </div>

                    <div class="input-field">
                        <label for="">Gross Annual Income Last Year (Non-farming)</label>
                        <input type="number" name="gross_non_farming" id="" placeholder="">
                    </div>

                    <!-- hantod diri -->

                    
        
            </div>
            <div  class="buttons">
                <div id="Back2" class="backBtn" >
                    <i class="uil uil-navigator"></i>
                    <span class="btnText">Back</span>   
                </div>
                <div id="Next3"class="nextBtn" >
                    <span class="btnText">Next</span>
                    <i class="uil uil-navigator"></i>
                </div>
                
                </div>
            </div>
        </div>

    <!------------------FORM 4----------------------------!-->
    <div class="form third" id="Form4">
    <small style="text-align: center; display: block; font-weight: 500; color: blue;" >(ENTER THE PARCEL YOU WANT TO ENROLL OR YOUR MAIN FARMLAND)</small>
            <div class="details additional">
                <span class="title">Farm Parcel No. 1</span>

                <div class="fields">

                    <div class="input-field">
                        <label for="">Farm Location<span style="color: red;">*</span></label>
                        <input type="text" name="farm_location" id="" placeholder="Barangay, City/Municipality" required>
                    </div>

                    <div class="input-field">
                        <label for="">Total Farm Area (in hectares)<span style="color: red;">*</span></label>
                        <input type="number" name="total_area" id="" placeholder="" required>
                    </div>

                    <div class="input-field">
                        <label for="">Ownership Document No.<span style="color: red;">*</span></label>
                        <input type="number" name="ownership_doc_num" id="" placeholder="" required>
                    </div>

                    <div class="input-field">
                        <label for="">Ownership Type<span style="color: red;">*</span></label>
                        <select id="" name="ownership_type" required>
                            <option value="" disabled selected>Ownership Type</option>
                            <option value="Registered Owner">Registered Owner</option>
                            <option value="Tenant">Tenant</option>
                            <option value="Lessee">Lessee</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <!-- walay column sa table  -->
                    <div class="input-field">
                        <label for="">If not the owner, enter Name of Land Owner</label>
                        <input type="text" name="owner_name" id="" placeholder="Enter Name of Land Owner">
                    </div>

                    <div class="input-field">
                        <label for="">Within Ancestral Domain<span style="color: red;">*</span></label>
                        <div class="radio-group" required>
                            <input type="radio" name="ancestral_domain" id="" value="Yes">
                            <label for="Yes">Yes</label>
                            <input type="radio" name="ancestral_domain" id="" value="No">
                            <label for="No">No</label>
                        </div>
                    </div>

                    <div class="input-field">
                        <label for="">Agrarian Reform Beneficiary<span style="color: red;">*</span></label>
                        <div class="radio-group" required>
                            <input type="radio" name="agrarian_benefeciary" id="Yes" value="Yes">
                            <label for="Yes">Yes</label>
                            <input type="radio" name="agrarian_benefeciary" id="No" value="No">
                            <label for="No">No</label>
                        </div>
                    </div>
            </div>
            </div>
            <div class="details ID">
                <span class="title"></span>
                <div class="fields">

                    <div class="input-field">
                        <label for="">Crop/Commodity</label>
                        <input type="text" name="crop" id="" placeholder="Rice/Corn">
                    </div>
                    <div class="input-field">
                        <label for="">Size (ha)</label>
                        <input type="number" name="area" id="" placeholder="">
                    </div>
                    <div class="input-field">
                        <label for="">Farm Type</label>
                        <input type="text" name="farm_type" id="" placeholder="">
                    </div>
                    <div class="input-field">
                        <label for="">Organic Practitioner</label>
                        <div class="radio-group">
                            <input type="radio" name="organic_practitioner" id="" value="Yes">
                            <label for="Yes">Yes</label>
                            <input type="radio" name="organic_practitioner" id="" value="No">
                            <label for="No">No</label>
                        </div>
                    </div>
                    <div class="input-field">
                        <label for="">Remarks</label>
                        <input type="text" name="remarks" id="" placeholder="">
                    </div>
                </div>

                <div  class="buttons">
                    <div id="Back3" class="backBtn" >
                        <i class="uil uil-navigator"></i>
                        <span class="btnText">Back</span>   
                    </div>
                    <div id="Next4" class="nextBtn" >
                        <span class="btnText">Next</span>
                        <i class="uil uil-navigator"></i>
                    </div>
                    
                    </div>

            </div>
        </div>
            <!------------------FORM 5----------------------------!-->
    <div class="form third" id="Form5">
        <small style="text-align: center; display: block; font-weight: 500; color: blue;" >(SKIP THIS PART IF YOU DON'T HAVE SECOND PARCEL)</small>
            <div class="details additional">
                <span class="title">Farm Parcel No. 2</span>

                <div class="fields">

                    <div class="input-field">
                        <label for="">Farm Location<span style="color: red;">*</span></label>
                        <input type="text" name="farm_location2" id="" placeholder="Barangay, City/Municipality">
                    </div>

                    <div class="input-field">
                        <label for="">Total Farm Area (in hectares)<span style="color: red;">*</span></label>
                        <input type="number" name="total_area2" id="" placeholder="">
                    </div>

                    <div class="input-field">
                        <label for="">Ownership Document No.<span style="color: red;">*</span></label>
                        <input type="number" name="ownership_doc_num2" id="" placeholder="">
                    </div>

                    <div class="input-field">
                        <label for="">Ownership Type<span style="color: red;">*</span></label>
                        <select id="" name="ownership_type2">
                            <option value="" disabled selected>Ownership Type</option>
                            <option value="Registered Owner">Registered Owner</option>
                            <option value="Tenant">Tenant</option>
                            <option value="Lessee">Lessee</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <!-- walay column sa table  -->
                    <div class="input-field">
                        <label for="">If not the owner, enter Name of Land Owner</label>
                        <input type="text" name="owner_name2" id="" placeholder="Enter Name of Land Owner">
                    </div>

                    <div class="input-field">
                        <label for="">Within Ancestral Domain<span style="color: red;">*</span></label>
                        <div class="radio-group">
                            <input type="radio" name="ancestral_domain2" id="" value="Yes">
                            <label for="Yes">Yes</label>
                            <input type="radio" name="ancestral_domain2" id="" value="No">
                            <label for="No">No</label>
                        </div>
                    </div>

                    <div class="input-field">
                        <label for="">Agrarian Reform Beneficiary<span style="color: red;">*</span></label>
                        <div class="radio-group">
                            <input type="radio" name="agrarian_benefeciary2" id="Yes" value="Yes">
                            <label for="Yes">Yes</label>
                            <input type="radio" name="agrarian_benefeciary2" id="No" value="No">
                            <label for="No">No</label>
                        </div>
                    </div>
            </div>
            </div>
            <div class="details ID">
                <span class="title"></span>
                <div class="fields">

                    <div class="input-field">
                        <label for="">Crop/Commodity</label>
                        <input type="text" name="crop2" id="" placeholder="Rice/Corn">
                    </div>
                    <div class="input-field">
                        <label for="">Size (ha)</label>
                        <input type="number" name="area2" id="" placeholder="">
                    </div>
                    <div class="input-field">
                        <label for="">Farm Type</label>
                        <input type="text" name="farm_type2" id="" placeholder="">
                    </div>
                    <div class="input-field">
                        <label for="">Organic Practitioner</label>
                        <div class="radio-group">
                            <input type="radio" name="organic_practitioner2" id="" value="Yes">
                            <label for="Yes">Yes</label>
                            <input type="radio" name="organic_practitioner2" id="" value="No">
                            <label for="No">No</label>
                        </div>
                    </div>
                    <div class="input-field">
                        <label for="">Remarks</label>
                        <input type="text" name="remarks2" id="" placeholder="">
                    </div>
                </div>

                <div  class="buttons">
                    <div id="Back4" class="backBtn" >
                        <i class="uil uil-navigator"></i>
                        <span class="btnText">Back</span>   
                    </div>
                    <div id="Next5" class="nextBtn" >
                        <span class="btnText">Next</span>
                        <i class="uil uil-navigator"></i>
                    </div>
                    
                    </div>

            </div>
        </div>
        <!------------------FORM 6----------------------------!-->
    <div class="form third" id="Form6">
        <small style="text-align: center; display: block; font-weight: 500; color: blue;" >(SKIP THIS PART IF YOU DON'T HAVE THIRD PARCEL)</small>
            <div class="details additional">
                <span class="title">Farm Parcel No. 3</span>

                <div class="fields">

                    <div class="input-field">
                        <label for="">Farm Location<span style="color: red;">*</span></label>
                        <input type="text" name="farm_location3" id="" placeholder="Barangay, City/Municipality">
                    </div>

                    <div class="input-field">
                        <label for="">Total Farm Area (in hectares)<span style="color: red;">*</span></label>
                        <input type="number" name="total_area3" id="" placeholder="">
                    </div>

                    <div class="input-field">
                        <label for="">Ownership Document No.<span style="color: red;">*</span></label>
                        <input type="number" name="ownership_doc_num3" id="" placeholder="">
                    </div>

                    <div class="input-field">
                        <label for="">Ownership Type<span style="color: red;">*</span></label>
                        <select id="" name="ownership_type3">
                            <option value="" disabled selected>Ownership Type</option>
                            <option value="Registered Owner">Registered Owner</option>
                            <option value="Tenant">Tenant</option>
                            <option value="Lessee">Lessee</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <!-- walay column sa table  -->
                    <div class="input-field">
                        <label for="">If not the owner, enter Name of Land Owner</label>
                        <input type="text" name="owner_name3" id="" placeholder="Enter Name of Land Owner">
                    </div>

                    <div class="input-field">
                        <label for="">Within Ancestral Domain<span style="color: red;">*</span></label>
                        <div class="radio-group">
                            <input type="radio" name="ancestral_domain3" id="" value="Yes">
                            <label for="Yes">Yes</label>
                            <input type="radio" name="ancestral_domain3" id="" value="No">
                            <label for="No">No</label>
                        </div>
                    </div>

                    <div class="input-field">
                        <label for="">Agrarian Reform Beneficiary<span style="color: red;">*</span></label>
                        <div class="radio-group">
                            <input type="radio" name="agrarian_benefeciary3" id="Yes" value="Yes">
                            <label for="Yes">Yes</label>
                            <input type="radio" name="agrarian_benefeciary3" id="No" value="No">
                            <label for="No">No</label>
                        </div>
                    </div>
            </div>
            </div>
            <div class="details ID">
                <span class="title"></span>
                <div class="fields">

                    <div class="input-field">
                        <label for="">Crop/Commodity</label>
                        <input type="text" name="crop3" id="" placeholder="Rice/Corn">
                    </div>
                    <div class="input-field">
                        <label for="">Size (ha)</label>
                        <input type="number" name="area3" id="" placeholder="">
                    </div>
                    <div class="input-field">
                        <label for="">Farm Type</label>
                        <input type="text" name="farm_type3" id="" placeholder="">
                    </div>
                    <div class="input-field">
                        <label for="">Organic Practitioner</label>
                        <div class="radio-group">
                            <input type="radio" name="organic_practitioner3" id="" value="Yes">
                            <label for="Yes">Yes</label>
                            <input type="radio" name="organic_practitioner3" id="" value="No">
                            <label for="No">No</label>
                        </div>
                    </div>
                    <div class="input-field">
                        <label for="">Remarks</label>
                        <input type="text" name="remarks3" id="" placeholder="">
                    </div>
                    
                    
                </div>

                <div  class="buttons">
                    <div id="Back5" class="backBtn" >
                        <i class="uil uil-navigator"></i>
                        <span class="btnText">Back</span>   
                    </div>
                    <div id="Next6" class="nextBtn" >
                        <span class="btnText">Next</span>
                        <i class="uil uil-navigator"></i>
                    </div>
                    
                    </div>

            </div>
      </div>

       <!------------------FORM 7----------------------------!-->
    <div class="form third" id="Form7">
        
            <div class="details additional">
                <span style="margin-left:100px; text-align:center;" class="title">Registry System for Basic Sectors in Agriculture (RSBSA) Enrollment Client's Copy</span>

                <div class="fields">

                    <div style="margin-left:170px; width: 100vh;text-align: center;" class="input-field">
                            <label style="font-size: 20px; margin-top: 20px;" for="rsbsa_number">RSBSA Number</label>
                            <input style="text-align:center;border: none;font-size: 50px;" type="text" id="rsbsa_number" name="rsbsa_num" readonly>
                    </div>

                    
                    <!-- <div id="preview">
                        <h3>Preview Information</h3>
                        <p><strong>Surname:</strong> <span id="preview_last_name"></span></p>
                        <p><strong>First Name:</strong> <span id="preview_first_name"></span></p>
                        <p><strong>Middle Name:</strong> <span id="preview_middle_name"></span></p>
                        <p><strong>Extension Name:</strong> <span id="preview_extension_name"></span></p>
                        <p><strong>RSBSA Number:</strong> <span id="preview_rsbsa_number"></span></p>
                    </div> -->

     

                </div>

                <div class="preview" style="display:flex;justify-content: space-evenly;align-content: stretch;flex-wrap: nowrap;flex-direction: column;align-items: stretch;margin: 2px 0;">
                        <div style="margin:10px 0;" class="input-field">
                                <label for="Surname:">Surname:</label>
                                <span style="margin: 0 10px;" id="preview_last_name"></span>
                        </div>

                        <div style="margin:10px 0;" class="input-field">
                                <label for="First Name:">First Name:</label>
                                <span style="margin: 0 10px;" id="preview_first_name"></span>
                        </div>

                        <div style="margin:10px 0;" class="input-field">
                                <label for="Middle Name">Middle Name:</label>
                                <span style="margin: 0 10px;" id="preview_middle_name"></span>
                        </div>

                        <div style="margin:10px 0;" class="input-field">
                                <label for="Extension Name">Extension Name:</label>
                                <span style="margin: 0 10px;" id="preview_extension_name"></span>
                        </div>

                        <div style="margin:10px 0;" class="input-field">
                                <label for="Extension Name">Password (Harvest Assistant - App):</label>
                                <span style="margin: 0 10px;" id="preview_extension_name">123</span>
                        </div>
                    </div>

                <div  class="buttons">
                    <div id="Back6" class="backBtn" >
                        <i class="uil uil-navigator"></i>
                        <span class="btnText">Back</span>   
                    </div>
                    <button type="submit" value="submit" id="Next7" class="nextBtn" >
                        <span class="btnText">Next</span>
                        <i class="uil uil-navigator"></i>
                    </button>
                    
                    </div>

            </div>
      </div>

        </form>
    </div>
<!-- For Preview Info -->
    <script>
function updatePreview() {
    document.getElementById('preview_last_name').innerText = document.getElementById('last_name').value;
    document.getElementById('preview_first_name').innerText = document.getElementById('first_name').value;
    document.getElementById('preview_middle_name').innerText = document.getElementById('middle_name').value;
    document.getElementById('preview_extension_name').innerText = document.getElementById('extension_name').value;
}
</script>




<!-- For Auto GEneration of rsbsa Num  -->
<script>
        let usedNumbers = {}; // To store used numbers for each barangay to avoid duplication within the session

        function generateRsbsaNumber() {
            const region = document.getElementById('region').value;
            const province = document.getElementById('province').value;
            const city = document.getElementById('city').value;
            const barangay = document.getElementById('barangay').value;

            // Mapping for region, province, and city to their corresponding numbers
            const regionNumber = region === "Region XII - SOCCSKSARGEN" ? '12' : '00';
            const provinceNumber = province === "South Cotabato" ? '63' : '00';
            const cityNumber = city === "General Santos City" ? '03' : '00';

            // Assign numbers based on the selected barangay
            const barangayNumber = barangay === "Apopong" ? '017' :
                        barangay === "Baluan" ? '003' : 
                        barangay === "Buayan" ? '004' : 
                        barangay === "Bula" ? '005' : 
                        barangay === "Heights" ? '019' : 
                        barangay === "Conel" ? '012' : 
                        barangay === "Dadiangas East" ? '002' : 
                        barangay === "Fatima" ? '022' : 
                        barangay === "Katangawan" ? '006' : 
                        barangay === "Lagao" ? '007' : 
                        barangay === "Ligaya" ? '008' : 
                        barangay === "Mabuhay" ? '018' : 
                        barangay === "San Isidro" ? '009' : 
                        barangay === "San Jose" ? '020' : 
                        barangay === "Tinagacan" ? '010' : 
                        barangay === "Upper Labay" ? '013' : 
                        '000'; // Default to '000' if no match
            // Ensure usedNumbers has an entry for the selected barangay
            if (!usedNumbers[barangay]) {
                usedNumbers[barangay] = new Set();
            }

            // Generate a unique 5-digit random number for the last batch
            let lastBatch;
            do {
                lastBatch = String(Math.floor(10000 + Math.random() * 90000)); // Generate a 5-digit number
            } while (usedNumbers[barangay].has(lastBatch)); // Ensure it's unique within this session

            // Add the generated number to the set of used numbers for the barangay
            usedNumbers[barangay].add(lastBatch);

            // Construct the RSBSA number using the format 000000-000-00000
            const rsbsaNumber = `${regionNumber}${provinceNumber}${cityNumber}-${barangayNumber}-${lastBatch}`;

            document.getElementById('rsbsa_number').value = rsbsaNumber; // Show RSBSA number
        }

        // Reset the increment (or in this case, regenerate RSBSA) when barangay changes
        function resetIncrement() {
            generateRsbsaNumber(); // Generate new RSBSA number
        }

        // Call this function for other fields to avoid regenerating the random number
        function updateWithoutIncrement() {
            generateRsbsaNumber(); // Only update the RSBSA number without changing the random part
        }



    </script>

    <!-- asdas -->
    <script>

        var Form1 = document.getElementById("Form1");
        var Form2 = document.getElementById("Form2");
        var Form3 = document.getElementById("Form3");
        var Form4 = document.getElementById("Form4");
        var Form5 = document.getElementById("Form5");
        var Form6 = document.getElementById("Form6");
        var Form7 = document.getElementById("Form7");

        var Next1 = document.getElementById("Next1");
        var Next2 = document.getElementById("Next2");
        var Next3 = document.getElementById("Next3");
        var Next4 = document.getElementById("Next4");
        var Next5 = document.getElementById("Next5");
        var Next6 = document.getElementById("Next6");
        var Next7 = document.getElementById("Next7");

        var Back1 = document.getElementById("Back1");
        var Back2 = document.getElementById("Back2");
        var Back3 = document.getElementById("Back3");
        var Back4 = document.getElementById("Back4");
        var Back5 = document.getElementById("Back5");
        var Back6 = document.getElementById("Back6");



        var progress = document.getElementById("progress");

        Next1.onclick = function(){
            Form1.style.left = "-2000px";
            Form2.style.left = "0";
            progress.style.width = "28%"
            progress.style.transition = "0.5s ease"
        }
        Back1.onclick = function(){
            Form1.style.left = "0";
            Form2.style.left = "2000px";
            progress.style.width = "14%"
            progress.style.transition = "0.5s ease"
        }
        Next2.onclick = function(){
            Form2.style.left = "-2000px";
            Form3.style.left = "0";
            progress.style.width = "42%"
            progress.style.transition = "0.5s ease"
        }
        Back2.onclick = function(){
            Form2.style.left = "0";
            Form3.style.left = "2000px";
            progress.style.width = "28%"
            progress.style.transition = "0.5s ease"
        }
        Next3.onclick = function(){
            Form3.style.left = "-2000px";
            Form4.style.left = "0";
            progress.style.width = "56%"
            progress.style.transition = "0.5s ease"
        }
        Back3.onclick = function(){
            Form3.style.left = "0";
            Form4.style.left = "2000px";
            progress.style.width = "42%"
            progress.style.transition = "0.5s ease"
        }
        Next4.onclick = function(){
            Form4.style.left = "-2000px";
            Form5.style.left = "0";
            progress.style.width = "70%"
            progress.style.transition = "0.5s ease"
        }
        Back4.onclick = function(){
            Form4.style.left = "0";
            Form5.style.left = "2000px";
            progress.style.width = "56%"
            progress.style.transition = "0.5s ease"
        }
        Next5.onclick = function(){
            Form5.style.left = "-2000px";
            Form6.style.left = "0";
            progress.style.width = "84%"
            progress.style.transition = "0.5s ease"
        }
        Back5.onclick = function(){
            Form5.style.left = "0";
            Form6.style.left = "2000px";
            progress.style.width = "70%"
            progress.style.transition = "0.5s ease"
        }
        Next6.onclick = function(){
            Form6.style.left = "-2000px";
            Form7.style.left = "0";
            progress.style.width = "100%"
            progress.style.transition = "0.5s ease"
        }
        Back6.onclick = function(){
            Form6.style.left = "0";
            Form7.style.left = "2000px";
            progress.style.width = "84%"
            progress.style.transition = "0.5s ease"
        }

        // const form = document.querySelector('form'),
        //     nextBtn = form.querySelector('.nextBtn'),
        //     backBtn = form.querySelector('.backBtn'),
        //     allInput = form.querySelectorAll('.first input');

        // nextBtn.addEventListener('click', ()=> {
        //     allInput.forEach(input => {
        //         if(input.value != ""){
        //             form.classList.add("secActive");
        //         }else{
        //             form.classList.remove("secActive");
        //         }
        //     })
        // })

        // backBtn.addEventListener('click', () => form.classList.remove('secActive'));
    </script>
</body>
</html>