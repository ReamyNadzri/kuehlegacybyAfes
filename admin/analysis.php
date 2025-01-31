<?php
include('header_admin.php');
include('connection.php');

// SQL Query to count total users who uploaded data
$userCountSql = "SELECT COUNT(DISTINCT USERNAME) AS total_users
                 FROM KUEH";
$userCountStmt = oci_parse($condb, $userCountSql);
oci_execute($userCountStmt);
$userCountRow = oci_fetch_assoc($userCountStmt);
$totalUsers = $userCountRow['TOTAL_USERS'];

// SQL Query to count total kueh added
$kuehCountSql = "SELECT COUNT(*) AS total_kueh
                 FROM KUEH";
$kuehCountStmt = oci_parse($condb, $kuehCountSql);
oci_execute($kuehCountStmt);
$kuehCountRow = oci_fetch_assoc($kuehCountStmt);
$totalKueh = $kuehCountRow['TOTAL_KUEH'];

// Close the SQL statements and connection
oci_free_statement($userCountStmt);
oci_free_statement($kuehCountStmt);
oci_close($condb);
?>

<body style="background-color: #FFFAF0;">
    <link rel="stylesheet" href="style.css">
    <title>System Analytics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js for visualizing analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="container mt-4">
        <h4>System Analytics</h4>
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <p><strong>Total Users who have uploaded data:</strong> <?php echo $totalUsers; ?></p>
            </div>
            <div class="col-12 col-md-6">
                <p><strong>Total Kueh added:</strong> <?php echo $totalKueh; ?></p>
            </div>
        </div>

        <!-- Chart Container -->
        <div class="row">
            <div class="col-12 col-md-6">
                <canvas id="userChart"></canvas>
            </div>
            <div class="col-12 col-md-6">
                <canvas id="kuehChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Data for Total Users Chart
        const userData = {
            labels: ['Total Users'],
            datasets: [{
                label: 'Users Count',
                data: [<?php echo $totalUsers; ?>],
                backgroundColor: 'rgb(54, 162, 235)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        };

        // Data for Total Kueh Chart
        const kuehData = {
            labels: ['Total Kueh'],
            datasets: [{
                label: 'Kueh Count',
                data: [<?php echo $totalKueh; ?>],
                backgroundColor: 'rgb(255, 99, 132)',
                borderColor: 'rgb(255, 99, 132)',
                borderWidth: 1
            }]
        };

        // User Chart Configuration
        const userConfig = {
            type: 'bar',
            data: userData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,  // Set to stepSize of 1 to make sure only integers are shown on Y-axis
                            precision: 0  // No decimals, integers only
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.raw + ' users';
                            }
                        }
                    }
                }
            }
        };

        // Kueh Chart Configuration
        const kuehConfig = {
            type: 'bar',
            data: kuehData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,  // Set to stepSize of 1 to make sure only integers are shown on Y-axis
                            precision: 0  // No decimals, integers only
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.raw + ' kueh';
                            }
                        }
                    }
                }
            }
        };

        // Render Charts
        const userChart = new Chart(document.getElementById('userChart'), userConfig);
        const kuehChart = new Chart(document.getElementById('kuehChart'), kuehConfig);
    </script>
</body>
