<?php
include('header.php'); // Include the database connection
include('connection.php'); // Include the database connection


$username = $_SESSION['username'] ?? null;

// Prepare SQL statement
$sql = "SELECT f.KUEHID, k.KUEHNAME, LISTAGG(i.NAMEITEM, ', ') WITHIN GROUP (ORDER BY i.NAMEITEM) AS ITEMS
        FROM FAVORITE f
        JOIN KUEH k ON f.KUEHID = k.KUEHID
        JOIN ITEMS i ON f.KUEHID = i.KUEHID
        WHERE f.USERNAME = :username 
        GROUP BY f.KUEHID, k.KUEHNAME";

$stid = oci_parse($condb, $sql);
oci_bind_by_name($stid, ':username', $username);

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
            $row['CREATORIMAGE'] = $blobRow['IMAGE'] ?? NULL;
        }


        oci_free_statement($blobStmt);

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
?>


<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
            height: 200px;
        }

        /* Card body layout */
        .recipe-page .card-body {
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 1rem;
        }

        .card-content {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .card-header-section {
            margin-bottom: 0.5rem;
        }

        /* Ingredients text */
        .ingredients-list {
            flex: 1;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            margin: 0;
            font-size: 1.1rem;
            line-height: 1.5;
        }

        /* Profile section at bottom */
        .profile-section {
            position: absolute;
            top: 140px;
            padding-top: 0.5rem;
            align-items: center;
        }
    </style>
</head>


<div class="container py-4">
    <h2 id="recipeCountHeading" class="mb-4">Terdapat <?= $total_recipes ?> resipi yang disimpan</h2>
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
                                                        <h2 class="card-title"><?= htmlspecialchars($recipe['KUEHNAME']) ?>
                                                        </h2>
                                                    </strong>
                                                    <button class="btn btn-light"
                                                        onclick="toggleFavorite(<?= $recipe['KUEHID'] ?>, event)">
                                                        <i class="bi <?= $recipe['IS_FAVORITE'] ? 'bi-bookmark-fill' : 'bi-bookmark' ?>"></i>
                                                    </button>
                                                </div>
                                                <p class="card-text ingredients-list"
                                                    style="font-size: 1.1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; position: relative; max-height: 3.3em; line-height: 1.65em;">
                                                    <?php
                                                    $items = explode(', ', $recipe['ITEMS']);
                                                    if (count($items) > 10) {
                                                        echo htmlspecialchars(implode(', ', array_slice($items, 0, 10))) . '...';
                                                    } else {
                                                        echo htmlspecialchars($recipe['ITEMS']);
                                                    }
                                                    ?>
                                                </p>
                                                <div class="d-flex align-items-center profile-section">
                                                    <?PHP
                                                    if ($recipe['CREATORIMAGE'] != null) {
                                                    ?><img src="<?= $recipe['CREATORIMAGE'] ?>"
                                                            alt="Profile Picture" class="rounded-circle me-2 border" width="40"
                                                            height="40"><?PHP
                                                                    } else {
                                                                        ?>
                                                        <img src="sources/header/logo.png" alt="Profile Picture"
                                                            class="rounded-circle me-2 border" width="40" height="40"><?PHP
                                                                                                                    }
                                                                                                                        ?>
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
                        $animate += 0.10;
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

<script>


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
                    const card = button.closest('.col-12'); // The card element containing the favorite item

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

                        // Remove the card from the listing
                        card.remove();

                        // Update the recipe count dynamically
                        const recipeCountHeading = document.getElementById('recipeCountHeading');
                        let currentCount = parseInt(recipeCountHeading.textContent.match(/\d+/)[0]);
                        recipeCountHeading.textContent = `Terdapat ${currentCount - 1} resipi yang disimpan`;

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


</html>