<?php
include('connection.php'); // Include the database connection

// Fetch data from the KUEH table

// Prepare SQL statement
$sql = "SELECT f.KUEHID, k.KUEHNAME, LISTAGG(i.NAMEITEM, ', ') WITHIN GROUP (ORDER BY i.NAMEITEM) AS ITEMS
        FROM FAVORITE f
        JOIN KUEH k ON f.KUEHID = k.KUEHID
        JOIN ITEMS i ON f.KUEHID = i.KUEHID
        GROUP BY f.KUEHID, k.KUEHNAME";

$stid = oci_parse($condb, $sql);

// Execute the query
if (oci_execute($stid)) {
    $recipes = [];
    $total_recipes = 0;

    while ($row = oci_fetch_assoc($stid)) {
        $blobQuery = "SELECT IMAGE FROM KUEH WHERE KUEHID = :kuehID";
        $blobStmt = oci_parse($condb, $blobQuery);
        oci_bind_by_name($blobStmt, ':kuehID', $row['KUEHID']);
        oci_execute($blobStmt);

        if ($blobRow = oci_fetch_assoc($blobStmt)) {
            $blobData = $blobRow['IMAGE']->load(); // Fetch BLOB data
            $row['IMAGE_DATA_URI'] = 'data:image/jpeg;base64,' . base64_encode($blobData);
        }

        oci_free_statement($blobStmt);

        $blobQuery = "SELECT COALESCE(u.NAME, a.NAME) AS NAME
              FROM KUEH k
              LEFT JOIN USERS u ON k.USERNAME = u.USERNAME
              LEFT JOIN ADMIN a ON k.USERNAME = a.USERNAME
              WHERE k.KUEHID = :kuehID";
        $blobStmt = oci_parse($condb, $blobQuery);
        oci_bind_by_name($blobStmt, ':kuehID', $row['KUEHID']);
        oci_execute($blobStmt);

        if ($blobRow = oci_fetch_assoc($blobStmt)) {
            $row['NAMECREATOR'] = $blobRow['NAME'];
        }

        oci_free_statement($blobStmt);
        $recipes[] = $row;
        $total_recipes++;
    }
} else {
    // Handle query execution error
    $error = oci_error($stid);
    $error_message = "Database error: " . $error['message'];
}

oci_free_statement($stid);
oci_close($condb);



// Related Searches (Example data, can be fetched from the database)
$relatedSearches = [
    "kek batik ovaltine",
    "kek batik indulgence",
    "kek batik cheese",
    "kek batik simple",
    "kek batik ganache"
];

// Filter Options (Example data, can be fetched from the database)
$filterOptions = [
    "with" => ["coklat", "keju", "kacang", "gula"],
    "without" => ["gula", "tepung", "telur"]
];
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
        /* Custom Styles */
        .sticky-sidebar {
            position: sticky;
            top: 80px;
            height: calc(100vh - 70px);
            overflow-y: auto;
            z-index: 999;
        }

        .card-hover-effect:hover {
            background-color: beige;
            transition: background-color 0.3s ease;
        }

        .related-search-btn {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 20px;
            padding: 5px 15px;
            margin: 5px;
            transition: all 0.3s ease;
        }

        .related-search-btn:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
        }

        .filter-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .filter-card .card-body {
            padding: 20px;
        }

        .filter-card .form-select {
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }

        .filter-card .btn-primary {
            background-color: #0d6efd;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .filter-card .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
        }

        .filter-card .btn-outline-secondary {
            border-radius: 10px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .filter-card .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }

        .sticky-sidebar .card-body {
            border-top: 1px solid gray;
            /* Add a bold top border with your desired color */
            padding-top: 15px;
            /* Add spacing to avoid overlap with content */
            margin-top: 15px;
            /* Create space between stacked cards */
        }

        .sticky-sidebar .card-body:first-child {
            border-top: none;
            /* Remove the top border from the first card-body for a cleaner look */
            margin-top: 0;
            /* Ensure no extra space at the top */
        }

        .card-img-container {
            width: 100%;
            /* Fixed width for the image container */
            height: 200px;
            /* Fixed height for the image container */
            overflow: hidden;
            align-items: center;
            justify-content: center;
        }

        .card-img-container img {
            width: 100%;
            /* Make the image fill the container */
            height: 100%;
            /* Make the image fill the container */
            object-fit: cover;
            /* Ensure the image covers the container without distortion */
        }

        .card {
            height: 175px;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>
    <div class="container py-4">
        <h2 id="recipeCountHeading" class="mb-4">Terdapat <?= $total_recipes ?> resipi yang disimpan</h2><hr>

        <div class="row">
            <!-- Main Content Column -->
            <div class="col-md-8">
                <!-- Recipe Cards -->
                <div id="recipeContainer" class="row">
                    <?php if (!empty($recipes)): ?>
                        <?php foreach ($recipes as $recipe): ?>
                            <div class="col-12 mb-4">
                                <a href="kuehDetails.php?id=<?= $recipe['KUEHID'] ?>"
                                    class="text-decoration-none shadow-sm text-dark">
                                    <div class="card card-hover-effect rounded shadow-sm  border-0">
                                        <div class="row g-0">
                                            <div class="col-md-3">
                                                <div class="card-img-container">
                                                    <img src="<?= $recipe['IMAGE_DATA_URI'] ?? 'path/to/default/image.jpg' ?>"
                                                        class="img-fluid rounded-start"
                                                        alt="<?= htmlspecialchars($recipe['KUEHNAME']) ?>"
                                                        style="max-width: 100%; max-height: 175px; object-fit: cover;">
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <strong>
                                                            <h2 class="card-title"><?= htmlspecialchars($recipe['KUEHNAME']) ?>
                                                            </h2>
                                                        </strong>
                                                        <button type="button"
                                                        class="btn me-2 fw-bold"
                                                        id="saveRecipeButton"
                                                        onclick="toggleFavorite(<?= $recipe['KUEHID'] ?>)">
                                                        <i class="far fa-trash-can"></i></button>
                                                    </div>
                                                    <p class="card-text" style="font-size: 1.1rem;">
                                                        <?= htmlspecialchars($recipe['ITEMS']) ?>
                                                    </p>

                                                    <div class="d-flex align-items-center">
                                                        <img src="sources\header\logo.png" alt="Profile Picture"
                                                            class="rounded-circle me-2 border" width="40" height="40">
                                                        <p class="card-text w3-text-grey" style="font-size: 1.1rem;"><i>
                                                            oleh <?= htmlspecialchars($recipe['NAMECREATOR']) ?></i>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p>Tiada kueh dalam kegemaran.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar Column -->
            <div class="col-md-4">
                <div class="sticky-sidebar w-100" style="max-width: 300px;">
                    <!-- Filter -->

                    <div class="card-body">
                        
                        <h5 class="card-title mb-0">Berikan Maklum Balas:</h5>
                        <form action="submit_feedback.php" method="POST">
                            <textarea class="form-control" name="feedback" placeholder="Sila tulis maklum balas di sini.."></textarea>
                            <br>
                            <button class="btn btn-outline-secondary" type="submit">Hantar</button>
                        </form><br>
                        <h4 class="form-text text-muted">Sila jangan masukkan sebarang maklumat peribadi (data peribadi) dalam borang maklum balas ini, termasuk
                            nama dan butiran peribadi anda.

                            Kami akan gunakan maklumat ini untuk membantu kami memperbaiki perkhidmatan kami. Dengan menghantar
                            maklum balas ini, anda bersetuju untuk membenarkan maklumat anda diproses sejajar dengan Dasar Privasi
                            dan Terma Perkhidmatan kami. </h4>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    
</body>

</html>