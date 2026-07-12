<?php
include(__DIR__ . '/../includes/header.php');
include(__DIR__ . '/../connection.php'); // Include the database connection

$foodName = $_GET['search'] ?? ''; 
$foodName = trim($foodName);
$foodName = htmlspecialchars($foodName, ENT_QUOTES, 'UTF-8');

// Prepare SQL statement
$sql = "SELECT k.KUEHID, k.KUEHNAME, k.IMAGE, GROUP_CONCAT(i.NAMEITEM ORDER BY i.NAMEITEM SEPARATOR ', ') AS ITEMS
    FROM KUEH k
    JOIN ITEMS i ON k.KUEHID = i.KUEHID
    JOIN ORIGIN o ON k.ORIGINID = o.ORIGINCODE
    WHERE UPPER(k.KUEHNAME) LIKE UPPER(CONCAT('%', ?, '%')) 
    OR UPPER(o.NAMESTATE) LIKE UPPER(CONCAT('%', ?, '%'))
    GROUP BY k.KUEHID, k.KUEHNAME, k.IMAGE ORDER BY k.KUEHID DESC";

$stmt = mysqli_prepare($condb, $sql);
mysqli_stmt_bind_param($stmt, 'ss', $foodName, $foodName);

// Execute the query
if (mysqli_stmt_execute($stmt)) {
    $recipes = [];
    $total_recipes = 0;
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
        $creatorQuery = "SELECT COALESCE(u.NAME, a.NAME) AS NAME, u.IMAGE AS IMAGE
                    FROM KUEH k
                    LEFT JOIN USERS u ON k.USERNAME = u.USERNAME
                    LEFT JOIN ADMIN a ON k.USERNAME = a.USERNAME
                    WHERE k.KUEHID = ?
                    ORDER BY k.KUEHID DESC";

        $creatorStmt = mysqli_prepare($condb, $creatorQuery);
        mysqli_stmt_bind_param($creatorStmt, 'i', $row['KUEHID']);
        mysqli_stmt_execute($creatorStmt);
        $creatorResult = mysqli_stmt_get_result($creatorStmt);

        if ($creatorRow = mysqli_fetch_assoc($creatorResult)) {
            $row['NAMECREATOR'] = $creatorRow['NAME'];
            $row['CREATORIMAGE'] = $creatorRow['IMAGE'];
        }

        mysqli_stmt_close($creatorStmt);

        // Check if the kueh is in the user's favorites
        $username = $_SESSION['username'] ?? null;
        $isFavorite = false;
        if ($username) {
            $sql_check = "SELECT COUNT(*) AS count FROM FAVORITE WHERE KUEHID = ? AND USERNAME = ?";
            $stmt_check = mysqli_prepare($condb, $sql_check);
            mysqli_stmt_bind_param($stmt_check, 'is', $row['KUEHID'], $username);
            mysqli_stmt_execute($stmt_check);
            $favoriteResult = mysqli_stmt_get_result($stmt_check);
            $favoriteRow = mysqli_fetch_assoc($favoriteResult);
            $isFavorite = ($favoriteRow['count'] > 0);
            mysqli_stmt_close($stmt_check);
        }

        $row['IS_FAVORITE'] = $isFavorite; 
        $recipes[] = $row;
        $total_recipes++;
    }
} else {
    $error_message = "Database error: " . mysqli_error($condb);
}

mysqli_stmt_close($stmt);
mysqli_close($condb);

$kuehName = ucfirst(strtolower($foodName));
?>

<head>
    <style>
        .sticky-sidebar {
            position: sticky;
            top: 100px;
            z-index: 100;
        }

        .filter-panel {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 20px;
            padding: 1.75rem;
        }

        .tag-pill-custom {
            background-color: var(--color-accent-light);
            color: var(--color-accent);
            border-radius: 100px;
            padding: 6px 14px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            border: 1px solid rgba(28, 63, 36, 0.1);
        }

        .tag-pill-custom.without {
            background-color: #FDF2F2;
            color: #9C1C1C;
            border-color: rgba(156, 28, 28, 0.1);
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
            Terdapat <?= $total_recipes ?> resipi <?= htmlspecialchars($kuehName) ?>
        </h2>
        <p class="text-muted font-mono text-uppercase tracking-wider mb-4" style="font-size: 0.75rem;">Carian Resipi & Warisan Rasa</p>
        
        <hr class="custom-divider my-3">
        
        <div id="recipeContainer" class="row g-4">
            <?php if (!empty($recipes)):
                $animate = 0.0; ?>
                <?php foreach ($recipes as $recipe): ?>
                    <div class="col-12" style="animation-delay: <?= $animate ?>s;">
                        <a href="kuehDetails.php?id=<?= $recipe['KUEHID'] ?>" class="text-decoration-none d-block double-bezel-outer recipe-listing-card-outer">
                            <div class="double-bezel-inner">
                                <!-- Embossed Monogram Watermark -->
                                <svg class="recipe-card-watermark" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M 28 20 L 36 20 L 36 23 L 33 23 L 33 77 L 36 77 L 36 80 L 28 80 L 28 77 L 31 77 L 31 23 L 28 23 Z" fill="currentColor"/>
                                    <path d="M 33 50 C 37 43 47 31 73 24 C 71 29 67 34 62 37 C 65 35 69 33 72 30 C 69 35 64 40 56 44 C 60 42 63 40 66 37 C 61 44 54 49 45 52 C 48 50 51 48 53 45 C 47 51 40 55 33 56 Z" fill="currentColor"/>
                                    <path d="M 33 53 C 41 59 50 67 58 75 C 60 77 63 78 66 78 C 66 76 64 74 62 72 C 54 65 45 57 37 49 Z" fill="currentColor"/>
                                </svg>
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
                                                <i class="bi <?= $recipe['IS_FAVORITE'] ? 'bi-bookmark-fill text-warning' : 'bi-bookmark' ?>"></i>
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
                                            <?php
                                            $defaultProfileImage = 'https://static.vecteezy.com/system/resources/previews/024/983/914/non_2x/simple-user-default-icon-free-png.png';
                                            $profileImage = !empty($recipe['CREATORIMAGE']) ? $recipe['CREATORIMAGE'] : $defaultProfileImage;
                                            ?>
                                            <img src="<?= htmlspecialchars($profileImage) ?>"
                                                 alt="Penyumbang"
                                                 class="rounded-circle border"
                                                 width="28" height="28"
                                                 style="object-fit: cover;"
                                                 onerror="this.src='<?= $defaultProfileImage ?>';">
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
                    <i class="bi bi-search text-muted display-4"></i>
                    <p class="mt-3 text-muted">Tiada resipi ditemui untuk "<?= htmlspecialchars($kuehName) ?>". Cuba carian yang lain.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Sidebar Column (Right - Filter panel) -->
    <div class="col-12 col-lg-4">
        <div class="sticky-sidebar">
            <div class="filter-panel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="font-serif mb-0" style="font-family: var(--font-serif) !important;">Penapis</h4>
                    <a href="?" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Set Semula</a>
                </div>
                
                <hr class="sidebar-divider my-3">
                
                <!-- Input for "With" options -->
                <div class="mb-4">
                    <label for="withInput" class="form-label font-mono text-uppercase text-success fw-bold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Resipi Dengan Bahan:</label>
                    <input type="text" id="withInput" class="form-control rounded-pill px-3" placeholder="Contoh: kelapa, pandan...">
                    <small class="form-text text-muted" style="font-size: 0.75rem; padding-left: 0.5rem;">Tekan Enter untuk menambah bahan.</small>
                    <div id="withTags" class="d-flex flex-wrap mt-3"></div>
                    <input type="hidden" name="with" id="withHiddenInput">
                </div>
                
                <!-- Input for "Without" options -->
                <div class="mb-3">
                    <label for="withoutInput" class="form-label font-mono text-uppercase text-danger fw-bold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Resipi Tanpa Bahan:</label>
                    <input type="text" id="withoutInput" class="form-control rounded-pill px-3" placeholder="Contoh: tepung, telur...">
                    <small class="form-text text-muted" style="font-size: 0.75rem; padding-left: 0.5rem;">Tekan Enter untuk mengecualikan bahan.</small>
                    <div id="withoutTags" class="d-flex flex-wrap mt-3"></div>
                    <input type="hidden" name="without" id="withoutHiddenInput">
                </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        const withInput = document.getElementById('withInput');
        const withoutInput = document.getElementById('withoutInput');
        const withTagsContainer = document.getElementById('withTags');
        const withoutTagsContainer = document.getElementById('withoutTags');

        let withTags = [];
        let withoutTags = [];

        function createTag(value, container, tagArray, isExclude = false) {
            const tag = document.createElement('div');
            tag.className = 'tag-pill-custom ' + (isExclude ? 'without' : '');
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

        function filterRecipes(withTags, withoutTags) {
            const recipeCards = document.querySelectorAll('#recipeContainer .col-12');
            let visibleCount = 0;

            recipeCards.forEach(card => {
                const recipeItems = card.querySelector('.font-sans').textContent.toLowerCase();
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

        function updateRecipes() {
            const visibleCount = filterRecipes(withTags, withoutTags);
            document.getElementById('recipeCountHeading').textContent = `Terdapat ${visibleCount} resipi <?= htmlspecialchars($kuehName) ?>`;
        }

        withInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = this.value.trim().toLowerCase();
                if (value && !withTags.includes(value)) {
                    createTag(value, withTagsContainer, withTags, false);
                    this.value = '';
                    updateRecipes();
                }
            }
        });

        withoutInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = this.value.trim().toLowerCase();
                if (value && !withoutTags.includes(value)) {
                    createTag(value, withoutTagsContainer, withoutTags, true);
                    this.value = '';
                    updateRecipes();
                }
            }
        });
    });

    function toggleFavorite(kueh_id, event) {
        event.preventDefault();
        event.stopPropagation();

        fetch('<?php echo $root_path; ?>favorites/toggleFavorite.php', {
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
                    const icon = button.querySelector('i');

                    if (data.isFavorite) {
                        icon.classList.remove('bi-bookmark');
                        icon.classList.add('bi-bookmark-fill');
                        icon.classList.add('text-warning');

                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'success',
                            title: 'Ditambah ke kegemaran!',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    } else {
                        icon.classList.remove('bi-bookmark-fill');
                        icon.classList.remove('text-warning');
                        icon.classList.add('bi-bookmark');

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
                        title: 'Gagal menukar status kegemaran: ' + data.message,
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