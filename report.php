<?php
session_start();

include 'layout/header.php';
include 'models/Models.php';
include 'models/Admin.php';
include 'models/Farmers.php';
include 'models/Crops.php';
include 'models/Barangay.php';
include 'models/PestReports.php'; 

// Redirect if not logged in
if (!isset($_SESSION['user_login'])) {
    header('Location: login.php');
    exit();
}

$admin = new Admin('admins');
$farmers = new Farmers('farmers');
$crops = new Crops('crops');
$barangays = new Barangay('barangay');

// Make sure to pass the correct table name to the PestReports model
$pestReports = new PestReports('pest_report');  // Updated to use 'pest_report' table

$user = $_SESSION['user_login'];
$admin = $admin->where(['username' => $user])->get()[0];

// Pagination calculation
$page = $_GET['page'] ?? 1;
$paginate = ceil(count($farmers->all()) / 10);  // Use ceil() for proper pagination
$farmers = $farmers->farmers((int)$page);  // Fetch farmers for the current page
$crops = $crops->all();
$barangays = $barangays->all();

// Fetch all pest reports
$pestReports = $pestReports->all();


?>
<link rel="stylesheet" href="assets/css/farmers.css?v=5">
<link rel="stylesheet" href="css/farmer.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<div class="container">
    <?php include 'layout/sidebar.php'; ?>
    <?php include 'layout/nav.php'; ?>

    <!-- ================ Pest Report Details List ================= -->
    <div class="details">
        <div class="farmerlist">
            <div style="margin-bottom: 2em">
                <h1 style="text-align:center; margin-bottom: 30px;">Pest Report Overview</h1>

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

                <button class="btn-main" id="downloadExcel"><i class="las la-file-excel addicon"></i> <span>Download as Excel</span></button>
            </div>

            <div style="display: flex;justify-content: space-evenly;" class="charts"><!-- Chart for Pest Report Statistics -->
                    <div style="width: 400px; height: 200px;">
                        <span style="display:flex; justify-content:center; color:#333;">Total Number of Reports Per Pest</span>
                        <canvas id="pestReportChart"></canvas>
                    </div>
                    <div style="width: 400px; height: 200px;">
                        <span style="display:flex; justify-content:center; color:#333;">Pest Report Trends by Type (Monthly)</span>
                        <canvas id="pestReportsLineChart"></canvas>
                    </div>
                    
            </div>

            <!-- Update Pest Report Popup -->
            <div id="popupUpdatePestReport" class="popup">
                <div class="popup-content">
                    <span class="close" onclick="closepopup(document.getElementById('popupUpdatePestReport'))">&times;</span>
                    <h3>Pest Report Feedback</h3>
                    <form id="updatePestReportForm" action="scripts/update-pest-report.php" method="POST">
                        <input type="hidden" id="update_report_id" name="report_id" required>
                        <label style="margin-top: 50px;" class="lbl" for="update_report_details">Severity</label>
                        <input class="inp" id="update_report_details" type="number" name="report_details" placeholder="Enter severity" required></input>
                        <button class="addfarmer" type="submit">Confirm</button>
                    </form>
                </div>
            </div>


            <!-- Add Search Bar with Icon -->
            <div style="display: flex; justify-content: end; margin-bottom: 20px;">
                <input type="text" id="searchBar" placeholder="Search Pest Reports..." onkeyup="filterReports()"
                    style="padding-left: 30px; width: 300px; height: 35px; border-radius: 5px; border: 1px solid #ccc;">
                <i class="las la-search" style="position: relative;left: -293px;font-size: 20px;color: #888;top: 8px;"></i>
            </div>

            <!-- Enhanced Table Design -->
            <table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Farmer Name</th> <!-- Changed from Farmer ID to Farmer Name -->
            <th>Image</th>
            <th>Pest Type</th>
            <th>Date Reported</th>
            <th>Address</th>
            <th>Actions</th> <!-- Added Actions Column -->
        </tr>
    </thead>
    <tbody>
        <?php
        $counter = 1; // Start the counter at 1
        foreach ($farmers as $farmer) {
            foreach ($pestReports as $report) {
                // Match pest report to the farmer by 'name' instead of 'farmer_id'
                if (isset($report['name']) && $report['name'] == ($farmer['first_name'] . ' ' . $farmer['last_name'])) {
        ?>
                    <tr>
                        <td><?= $counter ?></td> <!-- Use counter instead of report ID -->
                        <td><?= htmlspecialchars($report['name']) ?></td> 
                        <td>
                            <?php if (!empty($report['image'])): ?>
                                <img src="<?= htmlspecialchars($report['image']) ?>" alt="Pest Image" style="width: 100px; height: auto;">
                            <?php else: ?>
                                No Image Available
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($report['pest_type']) ?></td> 
                        <td><?= date('F j, Y, g:i a', strtotime($report['date_reported'])) ?></td> 
                        <td><?= htmlspecialchars($report['address']) ?></td> 
                        <td style="display:flex;align;align-content: center;justify-content: center;" >
<!-- 

                            <a href="#" class="btn-action edit" 
                                title="Edit Pest Report" 
                                onclick="openUpdatePestReport(<?= $report['id'] ?>)" 
                                style="display: inline-flex; align-items: center; padding: 10px; border-radius: 5px; color: white; text-decoration: none; background-color: #4CAF50; margin: 5px; transition: background-color 0.3s;"
                                onmouseover="this.style.backgroundColor='#45a049';" 
                                onmouseout="this.style.backgroundColor='#4CAF50';">
                                    <i class="las la-edit" style="font-size: 20px;"></i>
                            </a> -->

                            <a href="scripts/delete-pest-report.php?id=<?= $report['id'] ?>" class="btn-action delete" 
                                title="Delete Pest Report"
                                onclick="return confirm('Are you sure you want to delete this pest report?');"
                                style="display: inline-flex; align-items: center; padding: 10px; border-radius: 5px; color: white; text-decoration: none; background-color: #f44336; margin: 5px; transition: background-color 0.3s;"
                                onmouseover="this.style.backgroundColor='#e53935';" 
                                onmouseout="this.style.backgroundColor='#f44336';">
                                <i class="las la-trash" style="font-size: 20px;"></i>
                            </a>
                        </td>
                    </tr>
        <?php
                    $counter++; // Increment the counter for each row
                }
            }
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
                <a href="./report.php?page=<?= $count ?>" class="btn-main <?= ($count == $page) ? 'active' : '' ?>">
                    <?= $count ?>
                </a>
            <?php
                $count++;
            }
            ?>
        </div>
    </div>
</div>

<!-- Pang Download as Excel ------->
<script>
document.getElementById('downloadExcel').addEventListener('click', function() {
    // Select the table
    var table = document.querySelector("table");

    // Create a new workbook and worksheet
    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.table_to_sheet(table);

    // Add the worksheet to the workbook
    XLSX.utils.book_append_sheet(wb, ws, "Pest Reports");

    // Generate Excel file and download it
    XLSX.writeFile(wb, "Pest_Reports.xlsx");
});

//----------------------------Number of report---------------------------
document.addEventListener('DOMContentLoaded', function() {
    const pestReports = <?= json_encode($pestReports) ?>;
    const pestCounts = {};

    // Count the number of reports for each pest type
    pestReports.forEach(report => {
        const pestType = report.pest_type || 'Unknown'; // Default to 'Unknown' if pest_type is missing
        if (pestCounts[pestType]) {
            pestCounts[pestType]++;
        } else {
            pestCounts[pestType] = 1;
        }
    });

    // Get labels and data for the chart
    const labels = Object.keys(pestCounts);
    const data = Object.values(pestCounts);

    // Define a color mapping for different pest types
    const colorMap = {
        'Stem Borer': 'rgba(75, 192, 192, 1)',  // Teal for Stem Borer
        'Fall Armyworm': 'rgba(255, 99, 132, 1)', // Red for Fall Armyworm
        'Leaf Folder': 'rgba(54, 162, 235, 1)', // Blue for Leaf Folder
        'Rice Bug': 'rgba(255, 206, 86, 1)',    // Yellow for Rice Bug
        'Unknown': 'rgba(153, 102, 255, 1)',    // Purple for Unknown
        // Add more pest types and their corresponding colors as needed
    };

    // Create datasets for the chart with separate colors
    const datasets = labels.map(label => ({
        label: label, // Set the label for each dataset
        data: [pestCounts[label]], // Data array for each pest type
        backgroundColor: colorMap[label] || 'rgba(200, 200, 200, 1)', // Default color if not defined
        borderColor: colorMap[label] || 'rgba(200, 200, 200, 1)', // Default color for border
        borderWidth: 1
    }));

    // Get the chart context
    const ctx = document.getElementById('pestReportChart').getContext('2d');
    
    // Create the chart
    const pestReportChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets // Use the datasets array to include legends
        },
        options: {
            scales: {
                x: {
                    display: false // Set display to false to remove x-axis labels
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Reports', // Add the Y-axis label
                        font: {
                            weight: 'bold', // Make the Y-axis label bold
                            size: 14
                        }
                    },
                    ticks: {
                        stepSize: 1, // Ensure that only whole numbers are shown
                        beginAtZero: true
                    }
                }
            },
            responsive: true,
            plugins: {
                legend: {
                    position: 'top', // Keep the legend visible
                },
                title: {
                    display: false, // Remove the chart title
                },
                tooltip: {
                    callbacks: {
                        title: function() {
                            return ''; // Return an empty string for title
                        },
                        label: function(tooltipItem) {
                            return `Reports: ${tooltipItem.raw}`; // Show only the number of reports
                        }
                    }
                }
            }
        }
    });
});



function filterReports() {
    const searchInput = document.getElementById('searchBar').value.toLowerCase();
    const tableRows = document.querySelectorAll("table tbody tr");

    tableRows.forEach(row => {
        const farmerName = row.children[1].textContent.toLowerCase();
        const pestType = row.children[3].textContent.toLowerCase();
        const dateReported = row.children[4].textContent.toLowerCase();
        const address = row.children[5].textContent.toLowerCase();

        if (
            farmerName.includes(searchInput) ||
            pestType.includes(searchInput) ||
            dateReported.includes(searchInput) ||
            address.includes(searchInput)
        ) {
            row.style.display = ""; // Show row if it matches search
        } else {
            row.style.display = "none"; // Hide row if it doesn't match
        }
    });
}

 // Extract pest reports and count reports by pest type per month
 var pestReportsData = [
        <?php 
        $pestCountByType = [];

        // Loop through the pest reports
        foreach ($pestReports as $report) {
            $pestType = htmlspecialchars($report['pest_type']);
            $dateReported = date('Y-m', strtotime($report['date_reported'])); // Format the date to YYYY-MM for monthly grouping

            // Initialize the data structure if not already set
            if (!isset($pestCountByType[$pestType])) {
                $pestCountByType[$pestType] = [];
            }

            // Count the pest reports by month
            if (!isset($pestCountByType[$pestType][$dateReported])) {
                $pestCountByType[$pestType][$dateReported] = 0;
            }

            $pestCountByType[$pestType][$dateReported]++;
        }

        // Convert the data into a format suitable for Chart.js
        foreach ($pestCountByType as $pestType => $reportDates) {
            $dates = array_keys($reportDates);
            $counts = array_values($reportDates);
            echo "{
                label: '$pestType',
                data: " . json_encode($counts) . ",
                fill: false,
                borderColor: '#".substr(md5($pestType), 0, 6)."', // Random color for each pest type
                tension: 0.1,
                borderWidth: 2,
                pointRadius: 4
            },";
        }
        ?>
    ];

    // Create the line chart
    var ctx = document.getElementById('pestReportsLineChart').getContext('2d');
    var pestReportsLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_keys(current($pestCountByType))); ?>, // Use the months as labels
            datasets: pestReportsData
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Reports',
                        font: {
                            weight: 'bold',  // Make the Y-axis title bold
                            size: 14
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Month',
                        font: {
                            weight: 'bold',  // Make the X-axis title bold
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


    function popup(id) {
            id.style.display = 'block'
        }

        function closepopup(id) {
            id.style.display = 'none'
        }
    function openUpdatePestReport(id) {
        document.getElementById('update_report_id').value = id; // Populate the report ID
        popup(document.getElementById('popupUpdatePestReport')); // Show the popup
    }



</script>

<!-- <script>
    function popup(id) {
        id.style.display = 'block';
    }

    function closepopup(id) {
        id.style.display = 'none';
    }
</script> -->

<?php include 'layout/footer.php'; ?>
