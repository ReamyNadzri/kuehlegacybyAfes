<?php
include('connection.php'); // Include the database connection

// Fetch data from the KUEH table


$foodName = $_GET['search']; // Example variable
$sql = "SELECT * FROM KUEH WHERE UPPER(KUEHNAME) LIKE '%' || UPPER(:search) || '%'";
$stid = oci_parse($condb, $sql);
oci_bind_by_name($stid, ':search', $foodName);
oci_execute($stid);

$kuehName = ucfirst(strtolower($foodName)); // Convert first character to uppercase
$recipes = [];
$total_recipes = 0;

while ($row = oci_fetch_assoc($stid)) {

    $recipes[] = $row;
    $total_recipes++;
}

oci_free_statement($stid);
oci_close($condb);
?>

<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resipi Kek Batik</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sticky-sidebar {
            position: sticky;
            top: 20px;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }


        /* Add hover effect to the card */
            .card-hover-effect:hover {
                background-color: beige; /* Change background to beige on hover */
                transition: background-color 0.3s ease; /* Smooth transition */
            }
        
            
    </style>
</head>

<body>
    <?php include('header.php'); ?>
    <div class="container py-4">
    <h1 class="mb-4">(<?= $total_recipes ?>) resipi <?= htmlspecialchars($kuehName) ?></h1>



        <div class="row">
            <!-- Main Content Column -->
            <div class="col-md-8">
                <!-- Recipe Cards -->
                <div class="row" >
                    <?php foreach ($recipes as $recipe):
                        $blobData = $recipe['IMAGE']->load(); // Load the BLOB data into a string
                        $base64Image = base64_encode($blobData); // Encode the BLOB data to base64
                        $recipe['IMAGE_DATA_URI'] = 'data:image/jpeg;base64,' . $base64Image;
                        ?>
                        <div class="col-12 mb-4" >
                        <a href="kuehDetails.php?id=<?= $recipe['KUEHID'] ?>" class="text-decoration-none shadow-sm text-dark " >
                        <div class="card card-hover-effect rounded shadow-sm" style="border :none">
                                    <div class="row g-0">
                                        <div class="col-md-3">
                                            <img src="<?= $recipe['IMAGE_DATA_URI'] ?>" class="img-fluid rounded-start"
                                                alt="<?= htmlspecialchars($recipe['KUEHNAME']) ?>"
                                                style="max-width: 100%; height: 100%;">
                                        </div>
                                        <div class="col-md-9">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <strong>
                                                        <h2 class="card-title"><?= htmlspecialchars($recipe['KUEHNAME']) ?>
                                                        </h2>
                                                    </strong>
                                                    <button class="btn btn-light"><i class="far fa-bookmark"></i></button>
                                                </div>
                                                <p class="card-text" style="font-size: 1.1rem;">
                                                    <?= htmlspecialchars($recipe['KUEHDESC']) ?>
                                                </p>
                                                <p class="card-text">
                                                    <i class="fas fa-users"></i> Food Type Code:
                                                    <?= htmlspecialchars($recipe['FOODTYPECODE']) ?>
                                                </p>
                                                <div class="d-flex align-items-center">
                                                    <img src="sources\header\logo.png" alt="Profile Picture"
                                                        class="rounded-circle me-2 border" width="40" height="40">
                                                        <p class="card-text" style="font-size: 1.1rem;">
                                                    Anaqi
                                                </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Sidebar Column -->
            <div class="col-md-4">
                <div class="sticky-sidebar">
                    <!-- Related Searches -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Carian berkaitan</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-light btn-sm">kek batik ovaltine</button>
                                <button class="btn btn-light btn-sm">kek batik indulgence</button>
                                <button class="btn btn-light btn-sm">kek batik cheese</button>
                                <button class="btn btn-light btn-sm">kek batik simple</button>
                                <button class="btn btn-light btn-sm">kek batik ganache</button>
                            </div>
                        </div>
                    </div>

                    <!-- Filter -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Filter</h5>
                            <p>Lihat resipi-resipi dengan:</p>
                            <input type="search" class="form-control mb-3" placeholder="Taip bahan-bahan...">
                            <p>Lihat resipi-resipi tanpa:</p>
                            <input type="search" class="form-control" placeholder="Taip bahan-bahan...">
                            <button class="btn btn-outline-secondary mt-3">Set semula</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>