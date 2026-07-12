<?php
session_start();
include('../connection.php'); // Include the database connection

// Fetch data from the KUEH table
$foodName = htmlspecialchars($_GET['search'] ?? ''); // Sanitize input
$withIngredients = isset($_GET['with']) ? array_filter(explode(',', $_GET['with'])) : [];
$withoutIngredients = isset($_GET['without']) ? array_filter(explode(',', $_GET['without'])) : [];

// Prepare base SQL statement (converted LISTAGG to GROUP_CONCAT)
$sql = "SELECT k.KUEHID, k.KUEHNAME, k.IMAGE, GROUP_CONCAT(i.NAMEITEM ORDER BY i.NAMEITEM SEPARATOR ', ') AS ITEMS
        FROM KUEH k
        JOIN ITEMS i ON k.KUEHID = i.KUEHID
        WHERE UPPER(k.KUEHNAME) LIKE UPPER(CONCAT('%', ?, '%'))
        GROUP BY k.KUEHID, k.KUEHNAME, k.IMAGE";

$stmt = mysqli_prepare($condb, $sql);
mysqli_stmt_bind_param($stmt, 's', $foodName);

// Execute the query
if (mysqli_stmt_execute($stmt)) {
    $result = mysqli_stmt_get_result($stmt);
    $recipes = [];
    $total_recipes = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        // Image is now a filename
        if (!empty($row['IMAGE']) && file_exists('../kueh_images/' . $row['IMAGE'])) {
            $row['IMAGE_DATA_URI'] = '../kueh_images/' . $row['IMAGE'];
        } else {
            $row['IMAGE_DATA_URI'] = '../sources/default-kueh.jpg'; // Default image
        }

        // Fetch the creator's name
        $sql_creator = "SELECT COALESCE(u.NAME, a.NAME) AS NAME
                        FROM KUEH k
                        LEFT JOIN USERS u ON k.USERNAME = u.USERNAME
                        LEFT JOIN ADMIN a ON k.USERNAME = a.USERNAME
                        WHERE k.KUEHID = ?";
        $stmt_creator = mysqli_prepare($condb, $sql_creator);
        mysqli_stmt_bind_param($stmt_creator, 'i', $row['KUEHID']);

        if (mysqli_stmt_execute($stmt_creator)) {
            $result_creator = mysqli_stmt_get_result($stmt_creator);
            if ($creator_row = mysqli_fetch_assoc($result_creator)) {
                $row['NAMECREATOR'] = $creator_row['NAME'];
            }
        }
        mysqli_stmt_close($stmt_creator);

        // Check if the kueh is in the user's favorites
        $username = $_SESSION['username'] ?? null;
        $isFavorite = false;
        if ($username) {
            $sql_check = "SELECT COUNT(*) AS count FROM FAVORITE WHERE KUEHID = ? AND USERNAME = ?";
            $stmt_check = mysqli_prepare($condb, $sql_check);
            mysqli_stmt_bind_param($stmt_check, 'is', $row['KUEHID'], $username);

            if (mysqli_stmt_execute($stmt_check)) {
                $result_check = mysqli_stmt_get_result($stmt_check);
                $favoriteRow = mysqli_fetch_assoc($result_check);
                $isFavorite = ($favoriteRow['count'] > 0);
            }
            mysqli_stmt_close($stmt_check);
        }
        $row['IS_FAVORITE'] = $isFavorite;

        // Fetch ITEMNAME from ITEMS table
        $itemNames = explode(', ', $row['ITEMS']);

        // Check if the row should be added based on ingredient filters
        $addRow = true;

        // Check for withIngredients
        if (!empty($withIngredients)) {
            foreach ($withIngredients as $ingredient) {
                if (!in_array($ingredient, $itemNames)) {
                    $addRow = true;
                    break;
                }
            }
        }

        // Check for withoutIngredients
        if (!empty($withoutIngredients)) {
            foreach ($withoutIngredients as $ingredient) {
                if (in_array($ingredient, $itemNames)) {
                    $addRow = false;
                    break;
                }
            }
        }

        // Add the row to recipes if it passes the filters
        if ($addRow) {
            $recipes[] = $row;
            $total_recipes++;
        }
    }

    mysqli_stmt_close($stmt);
} else {
    error_log("Database error: " . mysqli_error($condb));
    die("Database error: " . mysqli_error($condb));
}

mysqli_close($condb);

// Prepare the response
$response = [
    'total_recipes' => $total_recipes,
    'recipes' => ''
];

if (!empty($recipes)) {
    ob_start();
    foreach ($recipes as $recipe): ?>
        <div class="col-12 mb-4">
            <a href="kuehDetails.php?id=<?= htmlspecialchars($recipe['KUEHID']) ?>"
                class="text-decoration-none shadow-sm text-dark" style="height:100px">
                <div class="card card-hover-effect rounded shadow-sm" style="border: none;">
                    <div class="row g-0">
                        <div class="col-md-3 w3-display-container">
                            <div class="card-img-container">
                                <img src="<?= htmlspecialchars($recipe['IMAGE_DATA_URI']) ?>" class="img-fluid rounded-start"
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
                                    <button class="btn btn-light" onclick="toggleFavorite(<?= htmlspecialchars($recipe['KUEHID']) ?>, event)">
                                        <i class="bi <?= $recipe['IS_FAVORITE'] ? 'bi-bookmark-fill' : 'bi-bookmark' ?>"></i>
                                    </button>
                                </div>
                                <p class="card-text" style="font-size: 1.1rem;">
                                    <?= htmlspecialchars($recipe['ITEMS']) ?>
                                </p>
                                <div class="d-flex align-items-center mt-auto">
                                    <img src="../sources/header/logo.png" alt="Profile Picture"
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