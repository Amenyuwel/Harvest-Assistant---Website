                <canvas id="harvestChart" width="900" height="400" ></canvas>
                <?php

                $conn = new mysqli('localhost', 'root', '', 'harvest_data');


                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

    
                $query = "SELECT date, recent_harvest, last_year_harvest FROM harvest ORDER BY date";
                $result = $conn->query($query);

 
                $labels = [];
                $recentHarvest = [];
                $lastYearHarvest = [];

                while ($row = $result->fetch_assoc()) {
                    $labels[] = date('M', strtotime($row['date']));
                    $recentHarvest[] = $row['recent_harvest'];
                    $lastYearHarvest[] = $row['last_year_harvest'];
                }

  
                $conn->close();
                ?>

                <script>
                var ctx = document.getElementById('harvestChart').getContext('2d');
                var harvestChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($labels); ?>,
                        datasets: [{
                            label: 'Recent Harvest',
                            data: <?php echo json_encode($recentHarvest); ?>,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Last Year Harvest',
                            data: <?php echo json_encode($lastYearHarvest); ?>,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
                </script>
                
