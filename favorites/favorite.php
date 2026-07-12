<?php
include(__DIR__ . '/../includes/header.php');
include(__DIR__ . '/../connection.php'); // Include the database connection

$username = $_SESSION['username'] ?? null;

// Prepare SQL statement
$sql = "SELECT f.KUEHID, k.KUEHNAME, k.IMAGE, GROUP_CONCAT(i.NAMEITEM ORDER BY i.NAMEITEM SEPARATOR ', ') AS ITEMS
        FROM FAVORITE f
        JOIN KUEH k ON f.KUEHID = k.KUEHID
        JOIN ITEMS i ON f.KUEHID = i.KUEHID
        WHERE f.USERNAME = ? 
        GROUP BY f.KUEHID, k.KUEHNAME, k.IMAGE";

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

        // Get creator details
        $creatorQuery = "SELECT COALESCE(u.NAME, a.NAME) AS NAME
              FROM KUEH k
              LEFT JOIN USERS u ON k.USERNAME = u.USERNAME
              LEFT JOIN ADMIN a ON k.USERNAME = a.USERNAME
              WHERE k.KUEHID = ?";
        $creatorStmt = mysqli_prepare($condb, $creatorQuery);
        mysqli_stmt_bind_param($creatorStmt, 'i', $row['KUEHID']);
        mysqli_stmt_execute($creatorStmt);
        $creatorResult = mysqli_stmt_get_result($creatorStmt);

        if ($creatorRow = mysqli_fetch_assoc($creatorResult)) {
            $row['NAMECREATOR'] = $creatorRow['NAME'];
        }
        mysqli_stmt_close($creatorStmt);

        $row['IS_FAVORITE'] = true;
        $recipes[] = $row;
        $total_recipes++;
    }
}
mysqli_stmt_close($stmt);
mysqli_close($condb);
?>

<head>
    <style>
        .sticky-sidebar {
            position: sticky;
            top: 100px;
            z-index: 100;
        }

        .feedback-panel {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 20px;
            padding: 1.75rem;
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

<div class="row g-4 my-2">
    <!-- Main Content Column (Left) -->
    <div class="col-12 col-lg-8">
        <h2 id="recipeCountHeading" class="font-serif fw-bold mb-1" style="font-family: var(--font-serif) !important;">
            Terdapat <?= $total_recipes ?> resipi yang disimpan
        </h2>
        <p class="text-muted font-mono text-uppercase tracking-wider mb-4" style="font-size: 0.75rem;">Senarai Kegemaran Anda</p>
        
        <hr class="custom-divider my-3">
        
        <div id="recipeContainer" class="row g-4">
            <?php if (!empty($recipes)):
                $animate = 0.0; ?>
                <?php foreach ($recipes as $recipe): ?>
                    <div class="col-12" style="animation-delay: <?= $animate ?>s;">
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
                                    <div class="col-12 col-md-9 d-flex flex-column gap-2" style="position: relative;">
                                        
                                        <!-- Title & Bookmark Button -->
                                        <div class="d-flex justify-content-between align-items-start w-100">
                                            <h3 class="font-serif mb-0" style="font-family: var(--font-serif) !important; font-size: 1.4rem;">
                                                <?= htmlspecialchars($recipe['KUEHNAME']) ?>
                                            </h3>
                                            <button class="btn btn-sm btn-light rounded-circle shadow-sm" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; z-index: 10;"
                                                    onclick="toggleFavorite(<?= $recipe['KUEHID'] ?>, event)">
                                                <i class="bi bi-bookmark-fill text-warning"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Ingredients Snippet -->
                                        <p class="mb-3 text-muted font-sans" style="font-size: 0.95rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
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
                                        
                                        <!-- Creator Info -->
                                        <div class="d-flex align-items-center gap-2 mt-auto">
                                            <span class="font-mono text-uppercase text-muted" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                                                Oleh <?= htmlspecialchars($recipe['NAMECREATOR']) ?>
                                            </span>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php
                    $animate += 0.05;
                endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-bookmark-dash text-muted display-4"></i>
                    <p class="mt-3 text-muted">Tiada resipi disimpan. Mulakan carian resipi kegemaran anda!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Sidebar Column (Right - Feedback panel) -->
    <div class="col-12 col-lg-4">
        <div class="sticky-sidebar">
            <div class="feedback-panel">
                <h4 class="font-serif mb-3" style="font-family: var(--font-serif) !important;">Maklum Balas</h4>
                
                <hr class="sidebar-divider my-2">
                
                <form action="submit_feedback.php" method="POST" class="mt-3">
                    <div class="mb-3">
                        <textarea class="form-control" name="feedback" rows="4" placeholder="Sila tulis maklum balas di sini.." style="border-radius: 12px; font-size: 0.9rem; border: 1px solid var(--color-border);"></textarea>
                    </div>
                    <button class="btn btn-outline-secondary rounded-pill px-4 w-100" type="submit">Hantar</button>
                </form>
                
                <p class="form-text text-muted mt-3" style="font-size: 0.75rem; line-height: 1.4;">
                    Sila jangan masukkan sebarang maklumat peribadi (data peribadi) dalam borang maklum balas ini. 
                    Kami menggunakan maklum balas ini untuk menambah baik perkhidmatan kami.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Page Closing Tags (Backwards-compatibility for 5-div open layout) -->
</main> <!-- content-body -->
</div> <!-- content-wrapper-compat-4 -->
</div> <!-- content-wrapper-compat-3 -->
</div> <!-- main-layout -->
</div> <!-- app-container -->

<?php include(__DIR__ . '/../includes/footer.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleFavorite(kueh_id, event) {
        event.preventDefault();
        event.stopPropagation();

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
                    const button = event.target.closest('button');
                    const card = button.closest('.col-12');

                    // Since this is the favorite listing, removing it should drop it from list
                    if (!data.isFavorite) {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            card.remove();
                            
                            // Update count heading
                            const heading = document.getElementById('recipeCountHeading');
                            let currentCount = parseInt(heading.textContent.match(/\d+/)[0]);
                            heading.textContent = `Terdapat ${currentCount - 1} resipi yang disimpan`;
                            
                            if (currentCount - 1 === 0) {
                                location.reload();
                            }
                        }, 400);

                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'success',
                            title: 'Dikeluarkan dari kegemaran!',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    }
                } else {
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'error',
                        title: 'Gagal menukar status: ' + data.message,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'Ralat berlaku semasa menukar status kegemaran.',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            });
    }
</script>
</body>
</html>