<?php
session_start();
include('connection.php'); // Include the database connection

// Fetch data from the KUEH table
$foodName = $_GET['search'] ?? ''; // Use null coalescing operator to avoid undefined index notice
$withIngredients = isset($_GET['with']) ? explode(',', $_GET['with']) : [];
$withoutIngredients = isset($_GET['without']) ? explode(',', $_GET['without']) : [];

// Validate and sanitize input
$foodName = trim($foodName);
$foodName = htmlspecialchars($foodName, ENT_QUOTES, 'UTF-8');

// Prepare SQL statement
$sql = "SELECT k.KUEHID, k.KUEHNAME, LISTAGG(i.NAMEITEM, ', ') WITHIN GROUP (ORDER BY i.NAMEITEM) AS ITEMS
        FROM KUEH k
        JOIN ITEMS i ON k.KUEHID = i.KUEHID
        WHERE UPPER(k.KUEHNAME) LIKE '%' || UPPER(:search) || '%'";

// Add "WITH" conditions
if (!empty($withIngredients)) {
    $sql .= " AND (";
    foreach ($withIngredients as $index => $ingredient) {
        if ($index > 0)
            $sql .= " OR ";
        $sql .= "UPPER(i.NAMEITEM) LIKE '%' || UPPER(:with$index) || '%'";
    }
    $sql .= ")";
}

// Add "WITHOUT" conditions
if (!empty($withoutIngredients)) {
    $sql .= " AND NOT (";
    foreach ($withoutIngredients as $index => $ingredient) {
        if ($index > 0)
            $sql .= " OR ";
        $sql .= "UPPER(i.NAMEITEM) LIKE '%' || UPPER(:without$index) || '%'";
    }
    $sql .= ")";
}

$sql .= " GROUP BY k.KUEHID, k.KUEHNAME";
$stid = oci_parse($condb, $sql);

// Bind search parameter
oci_bind_by_name($stid, ':search', $foodName);

// Bind "WITH" parameters
foreach ($withIngredients as $index => $ingredient) {
    oci_bind_by_name($stid, ":with$index", $ingredient);
}

// Bind "WITHOUT" parameters
foreach ($withoutIngredients as $index => $ingredient) {
    oci_bind_by_name($stid, ":without$index", $ingredient);
}

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

        // Check if the kueh is in the user's favorites
        $username = $_SESSION['username'] ?? null;
        $isFavorite = false;
        if ($username) {
            $sql_check = "SELECT COUNT(*) AS count FROM FAVORITE WHERE KUEHID = :kueh_id AND USERNAME = :username";
            $stid_check = oci_parse($condb, $sql_check);
            oci_bind_by_name($stid_check, ':kueh_id', $row['KUEHID']);
            oci_bind_by_name($stid_check, ':username', $username);
            oci_execute($stid_check);
            $favoriteRow = oci_fetch_array($stid_check, OCI_ASSOC);
            $isFavorite = ($favoriteRow['COUNT'] > 0);
        }

        $row['IS_FAVORITE'] = $isFavorite; // Add the favorite status to the row
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

// Prepare the response
$response = [
    'total_recipes' => $total_recipes,
    'recipes' => ''
];

if (!empty($recipes)) {
    ob_start();
    foreach ($recipes as $recipe): ?>
        <div class="col-12 mb-4">
            <a href="kuehDetails.php?id=<?= $recipe['KUEHID'] ?>"
                class="text-decoration-none shadow-sm text-dark" style="height:100px">
                <div class="card card-hover-effect rounded shadow-sm" style="border: none;">
                    <div class="row g-0">
                        <div class="col-md-3 w3-display-container">
                            <div class="card-img-container">
                                <img src="<?= $recipe['IMAGE_DATA_URI'] ?>" class="img-fluid rounded-start"
                                    alt="<?= htmlspecialchars($recipe['KUEHNAME']) ?>"
                                    style="max-width: 100%; max-height: 200px; object-fit: cover;">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <strong>
                                        <h2 class="card-title"><?= htmlspecialchars($recipe['KUEHNAME']) ?></h2>
                                    </strong>
                                    <button class="btn btn-light" onclick="toggleFavorite(<?= $recipe['KUEHID'] ?>, event)">
                                        <i class="bi <?= $recipe['IS_FAVORITE'] ? 'bi-bookmark-fill' : 'bi-bookmark' ?>"></i>
                                    </button>
                                </div>
                                <p class="card-text" style="font-size: 1.1rem;">
                                    <?= htmlspecialchars($recipe['ITEMS']) ?>
                                </p>
                                <div class="d-flex align-items-center mt-auto"> <!-- Add mt-auto here -->
                                    <img src="sources\header\logo.png" alt="Profile Picture"
                                        class="rounded-circle me-2 border" width="40" height="40">
                                    <p class="card-text" style="font-size: 1.1rem;">
                                        <?= htmlspecialchars($recipe['NAMECREATOR']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
<?php endforeach;
    $response['recipes'] = ob_get_clean();
} else {
    $response['recipes'] = '<div class="col-12"><p>No recipes found for "' . htmlspecialchars($foodName) . '".</p></div>';
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>