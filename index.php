<?php
session_start();

include_once('layout/header.php');

include 'models/Models.php';

include 'models/Admin.php';
include 'models/FarmersSignUp.php';
include 'models/Farmers.php';
include 'models/Harvest.php';
include 'models/PestReports.php';
include 'models/PriceHistory.php';
include 'functions.php';

if (!isset($_SESSION['user_login'])) {
    header('Location: login.php');
    exit();
}

$priceHistoryModel = new PriceHistory();
$admin = new Admin();
$farmers = new Farmers();
$harvests = new Harvest();
$pests = new PestReports();
$farmersSignUp = new FarmersSignUp();

$user = $_SESSION['user_login'];

$admin = $admin->where(['username' => $user])->get()[0];
$farmers = $farmers->all();
$pests = $pests->all();
$pendingFarmers = $farmersSignUp->all();
$harvestsData = $harvests->harvests();

$priceHistoryData = $priceHistoryModel->getAllPriceHistory();

?>
<link rel="stylesheet" href="css/dashboard.css?v=13">
</head>

<body>

    <div class="container">

        <?php include 'layout/sidebar.php' ?>
        <?php include 'layout/nav.php' ?>

        <!-- ======================= Cards ================== -->
        <div class="cards">
            <!-- Card for Total Farmers -->
            <div class="card">
                <a href="farmers.php">
                    <div>
                        <div class="number"><?= count($farmers); ?></div>
                        <div class="cardname">Total Farmers</div>
                    </div>
                </a>
                <div class="card-icon">
                    <i class="las la-users"></i>
                </div>
            </div>
            <!-- Card for Recent Harvest -->
            <div class="card">
                <div>
                    <div class="number"><?= count($harvests->harvests()) ?></div>
                    <div class="cardname">Recent Harvest</div>
                </div>

                <div class="card-icon">
                    <i class="las la-seedling"></i>
                </div>
            </div>
            <!-- Card for Pest Report -->
            <div class="card">
                <a href="report.php">
                    <div>
                        <div class="number"><?= count($pests) ?></div>
                        <div class="cardname">Pest Report</div>
                    </div>
                </a>

                <div class="card-icon">
                    <i class="las la-bug"></i>
                </div>
            </div>
            <!-- Card for Updating Crop Price -->
            <div class="card" onclick="popup(popupprice)" >
                    <div>
                        <div class="number">Update</div>
                        <div class="cardname">Crop Price</div>
                    </div>
                <div class="card-icon">
                    <i class="las la-tag"></i>
                </div>
            </div>
            <!-- Modal for Updating Prices -->
            <div id="popupprice" class="popup">
                <div class="popup-content">
                    <span class="close" onclick="closepopup(popupprice)">&times;</span>
                    <h3>Update Crop Prices</h3>
                    <?php
                    include 'config/conn.php'; 

                    try {
                        // Query to get the current Corn and Rice prices
                        $getCornPrice = "SELECT price FROM crop_price WHERE crop_id = 1"; 
                        $getRicePrice = "SELECT price FROM crop_price WHERE crop_id = 2"; 

                        // Execute the queries for Corn
                        $stmtCorn = $conn->prepare($getCornPrice);
                        $stmtCorn->execute();
                        $currentCornPrice = $stmtCorn->fetchColumn();

                        // Execute the queries for Rice
                        $stmtRice = $conn->prepare($getRicePrice);
                        $stmtRice->execute();
                        $currentRicePrice = $stmtRice->fetchColumn();

                    } catch (PDOException $e) {
                        echo "<script>
                            alert('Error fetching current prices: " . $e->getMessage() . "');
                            window.location.href = 'index.php';
                            </script>";
                        exit();
                    }
                    ?>
                    <form action="scripts/update-price.php" method="POST">
                        <label class="lbl" for="cornPrice">Corn Price (per kg):</label>
                        <input class="inp" type="number" id="cornPrice" name="cornPrice" value="<?= htmlspecialchars($currentCornPrice); ?>" step="0.01" required><br><br>

                        <label class="lbl" for="ricePrice">Rice Price (per kg):</label>
                        <input class="inp" type="number" id="ricePrice" name="ricePrice" value="<?= htmlspecialchars($currentRicePrice); ?>" step="0.01" required><br><br>

                        <button class="addfarmer" type="submit">Update Prices</button>
                    </form>
                </div>
            </div>


        </div>

        <!-- ======================= Charts ================== -->
        <div class="charts" >

            <!-- Comparison Chart -->
            <div class="chart" style="grid-column: 1 / 3;">

                <h2>Harvest Comparison</h2>
                <div class="sort" style="display: flex; align-items:center; justify-content:center;">
                    <label for="fromDate" style="font-family: Arial, sans-serif; font-size: 14px; margin-right: 10px;">From:</label>
                    <input type="date" id="fromDate" name="fromDate" style="padding: 8px; font-size: 14px; margin-right: 20px; border: 1px solid #ccc; border-radius: 4px;">
                    <label for="toDate" style="font-family: Arial, sans-serif; font-size: 14px; margin-right: 10px;">To:</label>
                    <input type="date" id="toDate" name="toDate" style="padding: 8px; font-size: 14px; margin-right: 20px; border: 1px solid #ccc; border-radius: 4px;">
                    <button onclick="updateChart()" style="padding: 8px 16px; font-size: 14px; background-color: #007BFF; color: white; border: none; border-radius: 4px; cursor: pointer;">Update Chart</button>
                </div>
                <canvas id="harvestChart" height="100px"></canvas>
            </div>


            <div class="chart">
            <h2>Price Trends for Rice and Corn</h2>
            <canvas id="priceChangeLineChart" width="400" height="200"></canvas>
            </div>

            <!-- Crop Charts -->
            <div class="chart">
                <h2>Crop Harvested</h2>
                <canvas id="cropPieChart" ></canvas>
            </div>
        </div>

        <!-- ================ Recent Harvest ================= -->
                <div class="details">
                    <div class="recentHarvest">
                        <div class="cardHeader">
                            <h2>Harvest Information</h2>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <td>RSBSA Number</td>
                                    <td>Name</td>
                                    <td>Crop</td>
                                    <td>Barangay</td>
                                    <td>Date Harvested</td>
                                    <td>Estimated Produce (kg)</td>
                                    <td>Estimated Income</td>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($harvestsData as $harvest) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($harvest['rsbsa_number'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($harvest['first_name'] . ' ' . $harvest['last_name'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($harvest['crop_name'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($harvest['barangay'] ?? 'N/A'); ?></td> 
                                        <td><?= htmlspecialchars($harvest['date_harvested'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($harvest['estimated_produce'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($harvest['estimated_income'] ?? 'N/A'); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                 <!-- ================= Pending Accounts ================ -->
                    <div class="PendingFarmer">
                        <div class="cardHeader" style=" margin-bottom: 20px;  " >
                            <h2>Price History</h2>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <td>Crop</td>
                                    <td>Old Price</td>
                                    <td>New Price</td>
                                    <td>Change Date</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($priceHistoryData as $priceHistory) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($priceHistory['crop_name']); ?></td>
                                        <td><?= htmlspecialchars($priceHistory['old_price']); ?></td>
                                        <td><?= htmlspecialchars($priceHistory['new_price']); ?></td>
                                        <td><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($priceHistory['change_date']))); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>



                </div>
            </div>                      
    </div>
    </div>

    <script>
        <?php
        if (isset($_GET['login']) && $_GET['login'] == "success") {
            echo "alert('Successfully logged in');";
        }
        ?>
    </script>


    <script>
        function updateChart() {
    // Get selected "From" and "To" dates
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;

    // Ensure valid date range is selected
    if (!fromDate || !toDate) {
        alert("Please select both 'From' and 'To' dates.");
        return;
    }

    // Convert the input values into Date objects
    const from = new Date(fromDate);
    const to = new Date(toDate);

    // Ensure the 'from' date is before the 'to' date
    if (from > to) {
        alert("The 'From' date cannot be after the 'To' date.");
        return;
    }

    // Filter the data based on the selected date range
    const filteredLastYear = filterByDateRange(lastYear, from, to);
    const filteredThisYear = filterByDateRange(thisYear, from, to);

    // Update chart with filtered data
    harvestChart.data.datasets[0].data = filteredLastYear;
    harvestChart.data.datasets[1].data = filteredThisYear;

    // Update chart labels to reflect the selected date range
    const labels = generateLabels(from, to);
    harvestChart.data.labels = labels;

    // Re-render the chart
    harvestChart.update();
}

// Helper function to filter data by the selected date range
function filterByDateRange(data, from, to) {
    const filteredData = [];
    for (let month in data) {
        const monthDate = new Date(Date.parse(month + " 1, 2024")); // Adjust year if needed
        if (monthDate >= from && monthDate <= to) {
            filteredData.push(data[month]);
        }
    }
    return filteredData;
}

// Helper function to generate labels based on the selected date range
function generateLabels(from, to) {
    const labels = [];
    const currentDate = new Date(from);

    while (currentDate <= to) {
        labels.push(monthNames[currentDate.getMonth()]);
        currentDate.setMonth(currentDate.getMonth() + 1);
    }

    return labels;
}



        <?php

        $date = $harvests->all();


        $lastYear = array_filter($date, function ($data) {
            $getLastYear = new DateTime();
            $getYear = new DateTime($data['date_harvested']);
            $getLastYear = $getLastYear->modify('-1 year');
            return $getYear->format('Y') == $getLastYear->format('Y');
        });

        $thisYear = array_filter($date, function ($data) {
            $getThisYear = new DateTime();
            $getYear = new DateTime($data['date_harvested']);
            return $getYear->format('Y') == $getThisYear->format('Y');
        });


        $crops = pluck($harvests->harvests(), 'crop_name');
        $corn = array_filter($crops, function ($crop) {
            return $crop == 'Corn';
        });

        $rice = array_filter($crops, function ($crop) {
            return $crop == 'Rice';
        });

        ?>

        var lastYear = [<?php echo json_encode($lastYear); ?>]
        lastYear = Object.values(...lastYear)
        var thisYear = [<?php echo json_encode($thisYear); ?>]
        thisYear = Object.values(...thisYear)

        var monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        let lastJan = lastYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 0
        })
        let lastFeb = lastYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 1
        })
        let lastMar = lastYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 2
        })
        let lastApr = lastYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 4
        })
        let lastMay = lastYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 5
        })
        let lastJun = lastYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 6
        })
        let lastJul = lastYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 7
        })
        let lastAug = lastYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 8
        })
        let lastSep = lastYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 9
        })
        let lastOct = lastYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 9
        })
        let lastNov = lastYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 10
        })
        let lastDec = lastYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 11
        })

        let thisJan = thisYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 0
        })
        let thisFeb = thisYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 1
        })
        let thisMar = thisYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 2
        })
        let thisApr = thisYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 4
        })
        let thisMay = thisYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 5
        })
        let thisJun = thisYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 6
        })
        let thisJul = thisYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 7
        })
        let thisAug = thisYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 8
        })
        let thisSep = thisYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 9
        })
        let thisOct = thisYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 9
        })
        let thisNov = thisYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 10
        })
        let thisDec = thisYear.filter(data => {
            date = new Date(data.date_harvested)
            date = date.getMonth()
            return date == 11
        })

        console.log()

        lastYear = {
            Jan: lastJan ? lastJan.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Feb: lastFeb ? lastFeb.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Mar: lastMar ? lastMar.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Apr: lastApr ? lastApr.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            May: lastMay ? lastMay.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Jun: lastJun ? lastJun.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Jul: lastJul ? lastJul.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Aug: lastAug ? lastAug.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Sep: lastSep ? lastSep.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Oct: lastOct ? lastOct.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Nov: lastNov ? lastNov.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Dec: lastDec ? lastDec.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
        }

        thisYear = {
            Jan: thisJan ? thisJan.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Feb: thisFeb ? thisFeb.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Mar: thisMar ? thisMar.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Apr: thisApr ? thisApr.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            May: thisMay ? thisMay.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Jun: thisJun ? thisJun.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Jul: thisJul ? thisJul.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Aug: thisAug ? thisAug.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Sep: thisSep ? thisSep.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Oct: thisOct ? thisOct.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Nov: thisNov ? thisNov.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
            Dec: thisDec ? thisDec.reduce(function(sum, obj) {
                return obj.estimated_produce + sum
            }, 0) : [],
        }

        var ctx = document.getElementById('harvestChart').getContext('2d');
        var harvestChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],  // Labels for X-axis
                datasets: [{
                    label: 'Harvested Last Year',
                    data: [
                        lastYear.Jan,
                        lastYear.Feb,
                        lastYear.Mar,
                        lastYear.Apr,
                        lastYear.May,
                        lastYear.Jun,
                        lastYear.Jul,
                        lastYear.Aug,
                        lastYear.Sep,
                        lastYear.Oct,
                        lastYear.Nov,
                        lastYear.Dec,
                    ],
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }, {
                    label: 'Harvested This Year',
                    data: [
                        thisYear.Jan,
                        thisYear.Feb,
                        thisYear.Mar,
                        thisYear.Apr,
                        thisYear.May,
                        thisYear.Jun,
                        thisYear.Jul,
                        thisYear.Aug,
                        thisYear.Sep,
                        thisYear.Oct,
                        thisYear.Nov,
                        thisYear.Dec,
                    ],
                    fill: false,
                    borderColor: 'rgb(255, 0, 0)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date (Month)',  // Label for X-axis
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Harvested Produce (Estimated)',  // Label for Y-axis
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        beginAtZero: true  // Ensure Y-axis starts from zero
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            boxWidth: 20
                        }
                    }
                }
            }
        });


        rice = [<?php echo json_encode($rice) ?>];
        corn = [<?php echo json_encode($corn) ?>];

        var ctx = document.getElementById('cropPieChart').getContext('2d');
        var cropPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Rice', 'Corn'], 
                datasets: [{
                    data: [Object.values(rice[0]).length, Object.values(corn[0]).length],
                    backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgba(255, 159, 64, 0.5)'],
                    borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 159, 64, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true, 
                        position: 'top', 
                        labels: {
                            font: {
                                size: 14, 
                                weight: 'bold' 
                            },
                            boxWidth: 20 
                        }
                    },
                    
                }
            }
        });

    </script>


        <?php
// Sort the price history data by change_date in ascending order
usort($priceHistoryData, function($a, $b) {
    return strtotime($a['change_date']) - strtotime($b['change_date']);
});
?>

<script>
    // Extracting the sorted price history data
    var priceHistoryData = [
        <?php foreach ($priceHistoryData as $priceHistory) { ?>
            {
                crop: "<?php echo htmlspecialchars($priceHistory['crop_name']); ?>",
                oldPrice: <?php echo htmlspecialchars($priceHistory['old_price']); ?>,
                newPrice: <?php echo htmlspecialchars($priceHistory['new_price']); ?>,
                changeDate: "<?php echo htmlspecialchars(date('Y-m-d', strtotime($priceHistory['change_date']))); ?>"
            },
        <?php } ?>
    ];

    // Sort the data by date
    priceHistoryData.sort(function(a, b) {
        return new Date(a.changeDate) - new Date(b.changeDate);
    });

    // Extracting the necessary data
    var changeDates = priceHistoryData.map(function(data) { return data.changeDate; });
    var cropsData = {};

    priceHistoryData.forEach(function(data) {
        if (!cropsData[data.crop]) {
            cropsData[data.crop] = { newPrices: [], dates: [] };
        }

        // Only add new price if it's for rice or corn
        if (data.crop === 'Rice' || data.crop === 'Corn') {
            cropsData[data.crop].newPrices.push(data.newPrice);
        } else {
            // Otherwise, add both old and new prices
            cropsData[data.crop].newPrices.push(data.newPrice);
        }

        cropsData[data.crop].dates.push(data.changeDate);
    });

    // Define colors for Rice and Corn
    var colors = {
        Rice: 'rgb(255, 159, 64)', // Orange color for Rice
        Corn: 'rgb(54, 162, 235)', // Blue color for Corn
        Default: 'rgb(75, 192, 192)' // Default color for other crops
    };

    // Create the chart
    var ctx = document.getElementById('priceChangeLineChart').getContext('2d');
    var priceChangeLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: changeDates, // Dates as labels
            datasets: Object.keys(cropsData).map(function(cropName) {
                // Set specific colors for Rice and Corn, default for others
                var color = colors[cropName] || colors.Default;

                return {
                    label: cropName + ' - Price',
                    data: cropsData[cropName].newPrices,
                    fill: false,
                    borderColor: color,
                    tension: 0.1,
                    borderWidth: 2,
                    pointRadius: 4
                };
            })
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Price (in Peso)',
                        font: {
                            weight: 'bold',  // Make Y-axis title bold
                            size: 14
                        }
                    },
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date of Price Change',
                        font: {
                            weight: 'bold',  // Make X-axis title bold
                            size: 14
                        }
                    },
                    ticks: {
                        autoSkip: true,
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });
</script>







        




        <script>
        function popup(id) {
            id.style.display = 'block'
        }

        function closepopup(id) {
            id.style.display = 'none'
        }

        // window.onclick = function(event) {
        //     if (event.target == modal) {
        //         modal.style.display = "none";
        //     }
        // }
    </script>


    <?php
    
    include_once 'layout/footer.php';
    ?>