<?php
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

$kuehName = ucfirst(strtolower($foodName)); // Convert first character to uppercase

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
            /* Hide any overflow */
            display: flex;
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
    </style>
</head>

<body>
    <?php include('header.php'); ?>
    <div class="container py-4">
        <h1 id="recipeCountHeading" class="mb-4">(<?= $total_recipes ?>) resipi <?= htmlspecialchars($kuehName) ?></h1>

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
                                    <div class="card card-hover-effect rounded shadow-sm  border-0" style="height: 200px">
                                        <div class="row g-0">
                                            <div class="col-md-3">
                                                <div class="card-img-container">
                                                    <img src="<?= $recipe['IMAGE_DATA_URI'] ?? 'path/to/default/image.jpg' ?>"
                                                        class="img-fluid rounded-start"
                                                        alt="<?= htmlspecialchars($recipe['KUEHNAME']) ?>">
                                                </div>
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
                                                        <?= htmlspecialchars($recipe['ITEMS']) ?>
                                                    </p>

                                                    <div class="d-flex align-items-center">
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
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p>No recipes found for "<?= htmlspecialchars($kuehName) ?>".</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar Column -->
            <div class="col-md-4">
                <div class="sticky-sidebar w-100" style="max-width: 300px;">
                    <!-- Related Searches -->

                    <div class="card-body" style="   border-top: 1px solid gray;">
                        <h5 class="card-title mb-4">Carian Berkaitan</h5>
                        <div class="d-flex flex-wrap">
                            <?php foreach ($relatedSearches as $related): ?>
                                <a href="?search=<?= urlencode($related) ?>" class="btn related-search-btn">
                                    <?= htmlspecialchars($related) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>


                    <!-- Filter -->

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Filter</h5>
                            <a href="?" class="btn btn-outline-secondary">Set Semula</a>
                        </div>

                        <!-- Input for "With" options -->
                        <p class="mb-2">Lihat resipi-resipi dengan:</p>
                        <div class="mb-3">
                            <input type="text" id="withInput" class="form-control"
                                placeholder="Masukkan bahan (cth: coklat)">
                            <small class="form-text text-muted">Tekan Enter untuk menambah bahan.</small>
                        </div>
                        <div id="withTags" class="d-flex flex-wrap mb-4"></div>
                        <input type="hidden" name="with" id="withHiddenInput">

                        <!-- Input for "Without" options -->
                        <p class="mb-2">Lihat resipi-resipi tanpa:</p>
                        <div class="mb-3">
                            <input type="text" id="withoutInput" class="form-control"
                                placeholder="Masukkan bahan (cth: gula)">
                            <small class="form-text text-muted">Tekan Enter untuk menambah bahan.</small>
                        </div>
                        <div id="withoutTags" class="d-flex flex-wrap mb-4"></div>
                        <input type="hidden" name="without" id="withoutHiddenInput">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const withInput = document.getElementById('withInput');
            const withoutInput = document.getElementById('withoutInput');
            const withTagsContainer = document.getElementById('withTags');
            const withoutTagsContainer = document.getElementById('withoutTags');
            const recipeContainer = document.getElementById('recipeContainer'); // Container for recipes

            let withTags = [];
            let withoutTags = [];

            // Function to create a tag
            function createTag(value, container, tagArray) {
                const tag = document.createElement('div');
                tag.className = 'related-search-btn me-2 mb-2';
                tag.textContent = value;

                // Add a remove button
                const removeButton = document.createElement('span');
                removeButton.className = 'ms-2';
                removeButton.innerHTML = '&times;';
                removeButton.style.cursor = 'pointer';
                removeButton.addEventListener('click', () => {
                    container.removeChild(tag);
                    const index = tagArray.indexOf(value);
                    if (index !== -1) {
                        tagArray.splice(index, 1);
                        updateRecipes(); // Update recipes when a tag is removed
                    }
                });

                tag.appendChild(removeButton);
                container.appendChild(tag);

                // Add to the tag array
                tagArray.push(value);
            }

            // Function to update recipes dynamically
            function updateRecipes() {
                const params = new URLSearchParams();
                params.append('search', '<?= $foodName ?>'); // Pass the search term
                if (withTags.length > 0) params.append('with', withTags.join(','));
                if (withoutTags.length > 0) params.append('without', withoutTags.join(','));

                fetch(`fetchrecipes.php?${params.toString()}`)
                    .then(response => response.json()) // Expect JSON response
                    .then(data => {
                        // Update the recipe listing
                        recipeContainer.innerHTML = data.recipes;

                        // Update the recipe count in the heading
                        const recipeCountHeading = document.getElementById('recipeCountHeading');
                        recipeCountHeading.textContent = `(${data.total_recipes}) resipi <?= htmlspecialchars($kuehName) ?>`;
                    })
                    .catch(error => console.error('Error fetching recipes:', error));
            }

            // Handle "With" input
            withInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent form submission
                    const value = withInput.value.trim();
                    if (value && !withTags.includes(value)) {
                        createTag(value, withTagsContainer, withTags);
                        withInput.value = ''; // Clear the input field
                        updateRecipes(); // Update recipes when a new tag is added
                    }
                }
            });

            // Handle "Without" input
            withoutInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent form submission
                    const value = withoutInput.value.trim();
                    if (value && !withoutTags.includes(value)) {
                        createTag(value, withoutTagsContainer, withoutTags);
                        withoutInput.value = ''; // Clear the input field
                        updateRecipes(); // Update recipes when a new tag is added
                    }
                }
            });

            // Initial load of recipes
            updateRecipes();
        });
    </script>
</body>

</html>