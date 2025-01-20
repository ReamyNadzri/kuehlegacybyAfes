<?php
include('header.php');
include('connection.php'); // Include the database connection

$foodName = $_GET['search'] ?? ''; // Use null coalescing operator to avoid undefined index notice
// Validate and sanitize input
$foodName = trim($foodName);
$foodName = htmlspecialchars($foodName, ENT_QUOTES, 'UTF-8');

// Prepare SQL statement
$sql = "SELECT k.KUEHID, k.KUEHNAME, LISTAGG(i.NAMEITEM, ', ') WITHIN GROUP (ORDER BY i.NAMEITEM) AS ITEMS
        FROM KUEH k
        JOIN ITEMS i ON k.KUEHID = i.KUEHID
        WHERE UPPER(k.KUEHNAME) LIKE '%' || UPPER(:search) || '%'";

$sql .= " GROUP BY k.KUEHID, k.KUEHNAME";
$stid = oci_parse($condb, $sql);

// Bind search parameter
oci_bind_by_name($stid, ':search', $foodName);

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

$kuehName = ucfirst(strtolower($foodName)); // Convert first character to uppercase
?>

<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resipi Kek Batik</title>
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
            padding-top: 15px;
            margin-top: 15px;
        }

        .sticky-sidebar .card-body:first-child {
            border-top: none;
            margin-top: 0;
        }

        .recipe-page .card-img-container img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .recipe-page .card-img-container {
            width: 200px;
            height: 200px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: 10px;

        }


        .recipe-page .card {
            height: auto;
            display: flex;
            align-items: center;
        }


        .recipe-page .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .profile-section {
            margin-top: 60px;
        }
    </style>
</head>

<body>

    <div class="container py-4">
        <h2 id="recipeCountHeading" class="mb-4">Terdapat <?= $total_recipes ?> resipi <?= htmlspecialchars($kuehName) ?></h2>
        <hr>

        <div class="row">
            <!-- Main Content Column -->
            <div class="col-md-8">
                <!-- Recipe Cards -->
                <div id="recipeContainer" class="row">
                    <?php if (!empty($recipes)):
                        $animate = 0.0; ?>
                        <?php foreach ($recipes as $recipe): ?>
                            <div class="col-12 mb-4 w3-animate-left" style="animation-delay: <?= $animate ?>s;">
                                <a href="kuehDetails.php?id=<?= $recipe['KUEHID'] ?>"
                                    class="text-decoration-none shadow-sm text-dark">
                                    <div class="card card-hover-effect rounded shadow-sm  border-0">
                                        <div class="row g-0">
                                            <div class="col-md-3">
                                                <div class="recipe-page">
                                                    <div class=" card-img-container">
                                                        <img src="<?= $recipe['IMAGE_DATA_URI'] ?? 'path/to/default/image.jpg' ?>"
                                                            class="img-fluid rounded-start"
                                                            alt="<?= htmlspecialchars($recipe['KUEHNAME']) ?>"
                                                            style="max-width: 100%; max-height: 200px; object-fit: cover;">
                                                    </div>
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
                                                    <div class="d-flex align-items-center profile-section">
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
                        <?php
                            $animate += 0.04;
                        endforeach; ?>
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

            let withTags = [];
            let withoutTags = [];

            // Function to create a tag
            function createTag(value, container, tagArray) {
                const tag = document.createElement('div');
                tag.className = 'related-search-btn me-2 mb-2';
                tag.textContent = value;

                const removeButton = document.createElement('span');
                removeButton.className = 'ms-2';
                removeButton.innerHTML = '&times;';
                removeButton.style.cursor = 'pointer';
                removeButton.onclick = function() {
                    container.removeChild(tag);
                    const index = tagArray.indexOf(value);
                    if (index !== -1) {
                        tagArray.splice(index, 1);
                    }
                    updateRecipes();
                };

                tag.appendChild(removeButton);
                container.appendChild(tag);
                tagArray.push(value);
            }

            // Function to filter recipes based on tags
            function filterRecipes(withTags, withoutTags) {
                const recipeCards = document.querySelectorAll('#recipeContainer .col-12');
                let visibleCount = 0;

                recipeCards.forEach(card => {
                    const recipeItems = card.querySelector('.card-text').textContent.toLowerCase();
                    const shouldShow =
                        withTags.every(tag => recipeItems.includes(tag)) &&
                        withoutTags.every(tag => !recipeItems.includes(tag));

                    if (shouldShow) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                return visibleCount;
            }

            // Function to update recipes with loading animation
            function updateRecipes() {
                const visibleCount = filterRecipes(withTags, withoutTags);
                document.getElementById('recipeCountHeading').textContent = `Terdapat ${visibleCount} resipi <?= htmlspecialchars($kuehName) ?>`;
            }

            // Event listener for "With" input
            withInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const value = this.value.trim().toLowerCase();
                    if (value && !withTags.includes(value)) {
                        createTag(value, withTagsContainer, withTags);
                        this.value = '';
                        updateRecipes();
                    }
                }
            });

            // Event listener for "Without" input
            withoutInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const value = this.value.trim().toLowerCase();
                    if (value && !withoutTags.includes(value)) {
                        createTag(value, withoutTagsContainer, withoutTags);
                        this.value = '';
                        updateRecipes();
                    }
                }
            });
        });

        // Existing favorite toggle function (unchanged)
        function toggleFavorite(kueh_id, event) {
            event.preventDefault(); // Prevent the default action of the button
            event.stopPropagation(); // Stop the event from bubbling up

            // Send an AJAX request to toggle the favorite status
            fetch('toggleFavorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        kueh_id: kueh_id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Toggle the button's appearance
                        const button = event.target.closest('button');
                        const icon = button.querySelector('i'); // Get the icon element

                        if (data.isFavorite) {
                            icon.classList.remove('bi-bookmark'); // Remove the outline class
                            icon.classList.add('bi-bookmark-fill'); // Add the filled class

                            // Show success toast for adding to favorites
                            Swal.fire({
                                toast: true,
                                position: 'top',
                                icon: 'success',
                                title: 'Added to favorites!',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer);
                                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                                }
                            });
                        } else {
                            icon.classList.remove('bi-bookmark-fill'); // Remove the filled class
                            icon.classList.add('bi-bookmark'); // Add the outline class

                            // Show success toast for removing from favorites
                            Swal.fire({
                                toast: true,
                                position: 'top',
                                icon: 'success',
                                title: 'Removed from favorites!',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer);
                                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                                }
                            });
                        }
                    } else {
                        // Show error toast if the operation failed
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'error',
                            title: 'Failed to toggle favorite status: ' + data.message,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer);
                                toast.addEventListener('mouseleave', Swal.resumeTimer);
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);

                    // Show error toast for unexpected errors
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'error',
                        title: 'An error occurred while toggling favorite status.',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                });
        }
    </script>
</body>

</html>