<?php
include('connection.php'); // Include the database connection

// Fetch user session data
$username = $_SESSION['username'];

// Prepare SQL statement
$sql = "SELECT k.KUEHID, k.KUEHNAME, LISTAGG(i.NAMEITEM, ', ') WITHIN GROUP (ORDER BY i.NAMEITEM) AS ITEMS
        FROM KUEH k 
        JOIN ITEMS i ON k.KUEHID = i.KUEHID
        WHERE k.USERNAME = :username
        GROUP BY k.KUEHID, k.KUEHNAME";

// Prepare the statement
$stid = oci_parse($condb, $sql);
oci_bind_by_name($stid, ':username', $username);
oci_execute($stid);

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

        $blobQuery = "SELECT COALESCE(u.USERNAME, k.USERNAME) AS NAME
              FROM KUEH k
              LEFT JOIN USERS u ON k.USERNAME = u.USERNAME
              WHERE k.KUEHID = :kuehID";
        $blobStmt = oci_parse($condb, $blobQuery);
        oci_bind_by_name($blobStmt, ':kuehID', $row['KUEHID']);
        oci_execute($blobStmt);

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


?>



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
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
        
        /* Cooking Activity Container */
        .cooking-activity-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            width: 100%;
            margin: 20px auto;
        }

        .listing-container {
            display: flex;
            background-color: #f9f9f9;
            width: 80%;
            margin: 20px auto;
        }

        .cooking-activity-i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .cooking-activity-p{
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .cooking-activity-h4 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .cooking-activity-container .start-button {
            padding: 10px 20px;
            font-size: 1rem;
            background-color: #ff7043;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .cooking-activity-container .start-button:hover {
            background-color: #e65c36;
        }

    </style>
</head>



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

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="cooking-activity-container">
                            <p class="cooking-activity-p"><i class="fa fa-utensils cooking-activity-p"></i><br> Belum ada aktiviti membuat kueh</p>
                            <h4 class="cooking-activity-h4"> Kongsi resipe idaman anda!</h4>
                            <a href="addKueh.php" class="start-button">Mulakan!</a>     
                        </div>
                    <?php endif; ?>
                </div>