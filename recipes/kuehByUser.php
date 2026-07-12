<?php
include(__DIR__ . '/../connection.php'); // Include the database connection

// Fetch user session data
$username = $_SESSION['username'];

// Prepare SQL statement
$sql = "SELECT k.KUEHID, k.KUEHNAME, k.IMAGE, GROUP_CONCAT(i.NAMEITEM ORDER BY i.NAMEITEM SEPARATOR ', ') AS ITEMS
        FROM KUEH k 
        JOIN ITEMS i ON k.KUEHID = i.KUEHID
        WHERE k.USERNAME = ?
        GROUP BY k.KUEHID, k.KUEHNAME, k.IMAGE";

// Prepare the statement
$stmt = mysqli_prepare($condb, $sql);
mysqli_stmt_bind_param($stmt, 's', $username);

// Execute the query
$recipes = [];
$total_recipes = 0;
if (mysqli_stmt_execute($stmt)) {
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        // Convert image filename to file path
        if (!empty($row['IMAGE'])) {
            $imagePath = __DIR__ . '/../kueh_images/' . $row['IMAGE'];
            $row['IMAGE_DATA_URI'] = file_exists($imagePath) ? $root_path . 'kueh_images/' . $row['IMAGE'] : $root_path . 'sources/default-kueh.jpg';
        } else {
            $row['IMAGE_DATA_URI'] = $root_path . 'sources/default-kueh.jpg';
        }
        $recipes[] = $row;
        $total_recipes++;
    }
}
mysqli_stmt_close($stmt);
mysqli_close($condb);
?>

<head>
    <style>
        .profile-activity-card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            width: 100%;
            margin-top: 1rem;
        }

        .recipe-listing-card-outer {
            transition: all 0.4s var(--ease-custom);
            height: 100%;
        }
        .recipe-listing-card-outer:hover {
            transform: translateY(-3px);
            border-color: var(--color-border-hover);
        }

        .listing-card-img-container {
            width: 100%;
            height: 180px;
            overflow: hidden;
            border-radius: 12px;
        }

        .listing-card-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: scale 0.6s var(--ease-custom);
        }
        .recipe-listing-card-outer:hover img {
            scale: 1.04;
        }
    </style>
</head>

<div class="row g-4 mt-2 justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
        <h3 class="font-serif fw-bold mb-4" style="font-family: var(--font-serif) !important;">Resipi Anda (<?= $total_recipes ?>)</h3>
        
        <?php if (!empty($recipes)): ?>
            <div class="d-flex flex-column gap-4">
                <?php foreach ($recipes as $recipe): ?>
                    <a href="<?php echo $root_path; ?>recipes/kuehDetails.php?id=<?= $recipe['KUEHID'] ?>" class="text-decoration-none d-block double-bezel-outer recipe-listing-card-outer">
                        <div class="double-bezel-inner">
                            <div class="row g-4 align-items-center">
                                <div class="col-12 col-md-3">
                                    <div class="listing-card-img-container">
                                        <img src="<?= htmlspecialchars($recipe['IMAGE_DATA_URI']) ?>"
                                             alt="<?= htmlspecialchars($recipe['KUEHNAME']) ?>"
                                             onerror="this.src='../sources/default-kueh.jpg';">
                                    </div>
                                </div>
                                <div class="col-12 col-md-9 d-flex flex-column gap-2">
                                    
                                    <!-- Title & Delete Button -->
                                    <div class="d-flex justify-content-between align-items-start w-100">
                                        <h4 class="font-serif mb-0" style="font-family: var(--font-serif) !important; font-size: 1.3rem; color: var(--color-text);">
                                            <?= htmlspecialchars($recipe['KUEHNAME']) ?>
                                        </h4>
                                        <button class="btn btn-sm btn-outline-danger rounded-circle shadow-sm" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; z-index: 10;"
                                                onclick="handleDelete(event, '<?= $recipe['KUEHID'] ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Ingredients -->
                                    <p class="mb-0 text-muted font-sans" style="font-size: 0.9rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        <strong>Bahan:</strong> 
                                        <?php
                                        $items = explode(', ', $recipe['ITEMS']);
                                        if (count($items) > 10) {
                                            echo htmlspecialchars(implode(', ', array_slice($items, 0, 10))) . '...';
                                        } else {
                                            echo htmlspecialchars($recipe['ITEMS']);
                                        }
                                        ?>
                                    </p>
                                    
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="profile-activity-card">
                <i class="bi bi-egg-fried text-success mb-3" style="font-size: 2.5rem; display: block;"></i>
                <h4 class="font-serif fw-bold mb-2" style="font-family: var(--font-serif) !important;">Kongsi resipi idaman anda!</h4>
                <p class="text-muted mb-4">Anda belum memuat naik sebarang resipi kueh tradisional lagi.</p>
                <a href="<?php echo $root_path; ?>recipes/addKueh.php" class="btn-primary-custom text-decoration-none">
                    <span>Mulakan Kongsi</span>
                    <div class="icon-wrapper">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function handleDelete(event, kuehId) {
        event.preventDefault();
        event.stopPropagation();

        Swal.fire({
            title: 'Adakah anda pasti?',
            text: "Resipi ini akan dipadamkan sepenuhnya!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1C3F24',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, padam!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?php echo $root_path; ?>recipes/deleteKueh.php?jadual=KUEH&medan_kp=KUEHID&kp=${kuehId}`;
            }
        });
    }
</script>