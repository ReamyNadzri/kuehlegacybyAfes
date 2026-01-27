<?php
include('header_admin.php');
include('connection.php');

// SQL Queries to get the required totals
$userCountSql = "SELECT COUNT(DISTINCT USERNAME) AS total_users FROM KUEH";
$userCountStmt = mysqli_prepare($condb, $userCountSql);
mysqli_stmt_execute($userCountStmt);
$result = mysqli_stmt_get_result($userCountStmt);
$userCountRow = mysqli_fetch_assoc($result);
$totalUsers = $userCountRow['total_users'];
mysqli_stmt_close($userCountStmt);

$kuehCountSql = "SELECT COUNT(*) AS total_kueh FROM KUEH";
$kuehCountStmt = mysqli_prepare($condb, $kuehCountSql);
mysqli_stmt_execute($kuehCountStmt);
$result = mysqli_stmt_get_result($kuehCountStmt);
$kuehCountRow = mysqli_fetch_assoc($result);
$totalKueh = $kuehCountRow['total_kueh'];
mysqli_stmt_close($kuehCountStmt);

// Count the total number of kueh types
$foodTypeCountSql = "SELECT COUNT(*) AS total_food_types FROM FOODTYPE";
$foodTypeCountStmt = mysqli_prepare($condb, $foodTypeCountSql);
mysqli_stmt_execute($foodTypeCountStmt);
$result = mysqli_stmt_get_result($foodTypeCountStmt);
$foodTypeCountRow = mysqli_fetch_assoc($result);
$totalFoodTypes = $foodTypeCountRow['total_food_types'];
mysqli_stmt_close($foodTypeCountStmt);

// Count the total number of images uploaded
$imageCountSql = "SELECT COUNT(*) AS total_images FROM KUEH WHERE IMAGE IS NOT NULL";
$imageCountStmt = mysqli_prepare($condb, $imageCountSql);
mysqli_stmt_execute($imageCountStmt);
$result = mysqli_stmt_get_result($imageCountStmt);
$imageCountRow = mysqli_fetch_assoc($result);
$totalImages = $imageCountRow['total_images'];
mysqli_stmt_close($imageCountStmt);

// Count the total number of users who have not uploaded any image
$inactiveUserCountSql = "SELECT COUNT(DISTINCT USERNAME) AS inactive_users FROM KUEH WHERE IMAGE IS NULL";
$inactiveUserCountStmt = mysqli_prepare($condb, $inactiveUserCountSql);
mysqli_stmt_execute($inactiveUserCountStmt);
$result = mysqli_stmt_get_result($inactiveUserCountStmt);
$inactiveUserCountRow = mysqli_fetch_assoc($result);
$totalInactiveUsers = $inactiveUserCountRow['inactive_users'];
mysqli_stmt_close($inactiveUserCountStmt);

// Close the connection
mysqli_close($condb);
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
        <h4>Kueh Analytics</h4> <br><br>
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
                <p><strong>Total Users who have uploaded data:</strong> <?php echo $totalUsers; ?></p>
            </div>
            <div class="col-12 col-md-6">
                <p><strong>Total Kueh added:</strong> <?php echo $totalKueh; ?></p>
            </div>
            <div class="col-12 col-md-6">
                <p><strong>Total Food Types:</strong> <?php echo $totalFoodTypes; ?></p>
            </div>
            <div class="col-12 col-md-6">
                <p><strong>Total Uploaded Images:</strong> <?php echo $totalImages; ?></p>
            </div>

        </div>

        <br><br>

        <!-- Circle Chart Container with adjusted size -->
        <div class="row justify-content-center">
            <div class="col-5">
                <!-- Reduced size for the circle chart -->
                <canvas id="circleChart" width="50" height="50"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Data for the Circle Chart (Pie Chart)
        const data = {
            labels: ['Users', 'Kueh', 'Food Types', 'Images'],
            datasets: [{
                label: 'System Analytics',
                data: [<?php echo $totalUsers; ?>, <?php echo $totalKueh; ?>, <?php echo $totalFoodTypes; ?>, <?php echo $totalImages; ?>, <?php echo $totalInactiveUsers; ?>],
                backgroundColor: [
                    'rgb(54, 162, 235)',
                    'rgb(255, 99, 132)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'

                ],
                borderColor: [
                    'rgb(54, 162, 235)',
                    'rgb(255, 99, 132)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)'

                ],
                borderWidth: 1
            }]
        };

        // Chart Configuration for Pie Chart
        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        };

        // Render the Circle Chart
        const circleChart = new Chart(document.getElementById('circleChart'), config);
    </script>
</body>