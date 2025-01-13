<?php
include('connection.php'); // Include the database connection

// Fetch data from the KUEH table
$sql = "SELECT KUEHID, KUEHNAME, KUEHDESC, VIDEO, FOODTYPECODE FROM KUEH";
$stid = oci_parse($condb, $sql);
oci_execute($stid);

$recipes = [];
while ($row = oci_fetch_assoc($stid)) {
    $recipes[] = $row;
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
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <div class="container py-4">
        <h1 class="mb-4">(161) resipi Kek batik</h1>
        
        <div class="alert alert-warning">
            <i class="fas fa-bookmark"></i> Resipi 'kek batik' terbukti
        </div>

        <div class="row">
            <!-- Main Content Column -->
            <div class="col-md-8">
                <!-- Recipe Cards -->
                <div class="row">
                    <?php foreach ($recipes as $recipe): ?>
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="row g-0">
                                    <div class="col-md-3">
                                        <img src="/api/placeholder/200/200" class="img-fluid rounded-start" alt="<?= htmlspecialchars($recipe['KUEHNAME']) ?>">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="card-title"><?= htmlspecialchars($recipe['KUEHNAME']) ?></h5>
                                                <button class="btn btn-light"><i class="far fa-bookmark"></i></button>
                                            </div>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($recipe['KUEHDESC']) ?>
                                                </small>
                                            </p>
                                            <p class="card-text">
                                                <i class="fas fa-users"></i> Food Type Code: <?= htmlspecialchars($recipe['FOODTYPECODE']) ?>
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <img src="/api/placeholder/30/30" class="rounded-circle me-2" alt="Profile">
                                                <span>Video: <a href="<?= htmlspecialchars($recipe['VIDEO']) ?>" target="_blank">Watch Video</a></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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