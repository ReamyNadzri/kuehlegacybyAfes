<?php
include('../includes/header.php');
include('../connection.php');

// Fetch kueh details by kueh_id
function fetchKuehDetails($conn, $kueh_id)
{
    $sql = "SELECT KUEHID, KUEHNAME, KUEHDESC, TAGKUEH, FOODTYPECODE, METHODID, VIDEO, IMAGE FROM KUEH WHERE KUEHID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $kueh_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $kuehDetails = mysqli_fetch_assoc($result);

    // Convert image filename to file path
    if ($kuehDetails && !empty($kuehDetails['IMAGE'])) {
        $imagePath = '../kueh_images/' . $kuehDetails['IMAGE'];
        if (file_exists($imagePath)) {
            $kuehDetails['KUEH_IMAGE_PATH'] = $imagePath;
        } else {
            $kuehDetails['KUEH_IMAGE_PATH'] = '../sources/default-kueh.jpg';
        }
    } else {
        $kuehDetails['KUEH_IMAGE_PATH'] = '../sources/default-kueh.jpg';
    }

    mysqli_stmt_close($stmt);
    return $kuehDetails;
}

// Fetch ingredients by kueh_id
function fetchIngredients($conn, $kueh_id)
{
    $sql = "SELECT NAMEITEM FROM ITEMS WHERE KUEHID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $kueh_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $ingredients = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $ingredients[] = $row['NAMEITEM'];
    }

    mysqli_stmt_close($stmt);
    return $ingredients;
}

// Fetch steps by kueh_id
function fetchSteps($conn, $kueh_id)
{
    $sql = "SELECT STEP FROM steps WHERE KUEHID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $kueh_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $steps = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $steps[] = $row['STEP'];
    }

    mysqli_stmt_close($stmt);
    return $steps;
}

// Function to check if the kueh is in the user's favorites
function isKuehInFavorites($conn, $kueh_id, $username)
{
    $sql = "SELECT COUNT(*) AS count FROM FAVORITE WHERE KUEHID = ? AND USERNAME = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'is', $kueh_id, $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    return ($row['count'] > 0);
}

// Get the username from the session
$username = $_SESSION['username'] ?? null;

// Get kueh_id from query parameter
$kueh_id = $_GET['id'] ?? null;

// Check if the kueh is in the user's favorites
$isFavorite = false;
if ($username && $kueh_id) {
    $isFavorite = isKuehInFavorites($condb, $kueh_id, $username);
}

if ($kueh_id) {
    $kuehDetails = fetchKuehDetails($condb, $kueh_id);
    $ingredients = fetchIngredients($condb, $kueh_id);
    $steps = fetchSteps($condb, $kueh_id);
} else {
    die("Kueh ID is missing.");
}

/* GET DETAIL OF THE KUEH'S CREATOR */
$blobQuery = "SELECT COALESCE(u.USERNAME, a.USERNAME) AS USERNAME, COALESCE(u.NAME, a.NAME) AS NAME, u.IMAGE AS IMAGE
                    FROM KUEH k
                    LEFT JOIN USERS u ON k.USERNAME = u.USERNAME
                    LEFT JOIN ADMIN a ON k.USERNAME = a.USERNAME
                    WHERE k.KUEHID = ?
                    ORDER BY k.KUEHID DESC";

$blobStmt = mysqli_prepare($condb, $blobQuery);
mysqli_stmt_bind_param($blobStmt, 'i', $kueh_id);
mysqli_stmt_execute($blobStmt);

$blobResult = mysqli_stmt_get_result($blobStmt);
$creator = [
    'USERNAMECREATOR' => '',
    'NAMECREATOR' => 'Penyumbang Resipi',
    'CREATORIMAGE' => ''
];
if ($blobRow = mysqli_fetch_assoc($blobResult)) {
    $creator['USERNAMECREATOR'] = $blobRow['USERNAME'];
    $creator['NAMECREATOR'] = $blobRow['NAME'];
    $creator['CREATORIMAGE'] = $blobRow['IMAGE'];
}

mysqli_stmt_close($blobStmt);
mysqli_close($condb);
?>

<head>
    <style>
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            overflow: hidden;
            height: 0;
            max-width: 100%;
            border-radius: 16px;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .ingredients-card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 20px;
            padding: 2rem;
        }
        
        .steps-card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 20px;
            padding: 2rem;
        }

        .step-number {
            font-family: var(--font-mono);
            background-color: var(--color-accent-light);
            color: var(--color-accent);
            font-weight: 600;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<div class="row g-4 my-2">
    <!-- Left Column (Food Image) -->
    <div class="col-12 col-md-5 col-lg-4">
        <div class="double-bezel-outer" style="position: sticky; top: 100px;">
            <div class="double-bezel-inner" style="padding: 0;">
                <img src="<?php echo htmlspecialchars($kuehDetails['KUEH_IMAGE_PATH']); ?>"
                     class="img-fluid w-100"
                     alt="<?php echo htmlspecialchars($kuehDetails['KUEHNAME']); ?>"
                     style="aspect-ratio: 1/1; object-fit: cover; display: block;">
            </div>
        </div>
    </div>
    
    <!-- Right Column (Details & Info) -->
    <div class="col-12 col-md-7 col-lg-8 d-flex flex-column gap-4">
        <div>
            <!-- Breadcrumbs / Tag -->
            <span class="badge" style="background-color: var(--color-accent-light); color: var(--color-accent); font-family: var(--font-mono); text-transform: uppercase; letter-spacing: 0.05em; padding: 6px 12px; border-radius: 100px; font-weight: 600; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($kuehDetails['TAGKUEH'] ?? 'Resipi'); ?>
            </span>
            
            <!-- Recipe Title -->
            <h1 class="display-5 fw-bold font-serif mb-3" style="font-family: var(--font-serif) !important;">
                <?php echo htmlspecialchars($kuehDetails['KUEHNAME']); ?>
            </h1>
            
            <!-- Creator Profile -->
            <div class="d-flex align-items-center gap-3 py-2">
                <?php
                $defaultProfileImage = 'https://static.vecteezy.com/system/resources/previews/024/983/914/non_2x/simple-user-default-icon-free-png.png';
                $profileImage = !empty($creator['CREATORIMAGE']) ? $creator['CREATORIMAGE'] : $defaultProfileImage;
                ?>
                <img src="<?php echo htmlspecialchars($profileImage); ?>"
                     alt="Profile Picture"
                     class="rounded-circle border"
                     width="48" height="48"
                     style="object-fit: cover;"
                     onerror="this.src='<?php echo $defaultProfileImage; ?>';">

                <div>
                    <h6 class="mb-0 fw-bold" style="color: var(--color-text);"><?php echo htmlspecialchars($creator['NAMECREATOR']); ?></h6>
                    <small class="text-muted">Penyumbang Resipi</small>
                </div>
            </div>
        </div>
        
        <!-- Recipe Description -->
        <p class="lead font-sans" style="font-size: 1.1rem; color: var(--color-text-muted); line-height: 1.7; max-width: 65ch;">
            <?php echo htmlspecialchars($kuehDetails['KUEHDESC']); ?>
        </p>
        
        <!-- Action Buttons -->
        <div class="d-flex flex-wrap gap-2 pt-2">
            <?php if (isset($_SESSION['username']) && $creator['USERNAMECREATOR'] == $_SESSION['username']): ?>
                <a href="editKueh.php?kuehId=<?php echo $kueh_id; ?>" class="btn btn-outline-success rounded-pill px-4 py-2">
                    <i class="bi bi-pencil-square me-2"></i> Sunting
                </a>
                <a href="deleteKueh.php?jadual=KUEH&medan_kp=KUEHID&kp=<?php echo $kueh_id; ?>" class="btn btn-outline-danger rounded-pill px-4 py-2" onclick="return confirm('Adakah anda pasti mahu memadam resipi ini?')">
                    <i class="bi bi-trash me-2"></i> Padam
                </a>
            <?php else: ?>
                <button type="button"
                        class="btn <?php echo $isFavorite ? 'btn-warning' : 'btn-outline-warning'; ?> rounded-pill px-4 py-2 fw-semibold"
                        id="saveRecipeButton"
                        onclick="toggleFavorite(<?php echo $kueh_id; ?>)">
                    <i class="bi <?php echo $isFavorite ? 'bi-bookmark-fill' : 'bi-bookmark'; ?> me-2"></i> Simpan Resipi
                </button>
            <?php endif; ?>
            
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2" onclick="copyToClipboard()">
                <i class="bi bi-link-45deg me-2"></i> Salin Pautan
            </button>
            <button type="button" class="btn btn-success rounded-pill px-4 py-2" onclick="copyToWhatsapp()">
                <i class="bi bi-whatsapp me-2"></i> Whatsapp
            </button>
        </div>
    </div>
</div>

<div class="row g-4 mt-4">
    <!-- Ingredients Panel -->
    <div class="col-12 col-md-5 col-lg-4">
        <div class="ingredients-card">
            <h3 class="font-serif mb-4" style="font-size: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 28px; height: 28px; color: var(--color-accent);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 18V9a6 6 0 0 1 12 0v9"></path>
                    <path d="M3 18h18a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-2a1 1 0 0 1 1-1Z"></path>
                    <path d="M12 6V3"></path>
                    <path d="M9 7v-2"></path>
                    <path d="M15 7v-2"></path>
                </svg>
                Ramuan
            </h3>
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <tbody>
                        <?php foreach ($ingredients as $index => $ingredient): ?>
                            <tr style="border-bottom: 1px solid var(--color-border);">
                                <td class="fw-bold font-mono py-3" style="width: 40px; color: var(--color-text-muted); font-size: 0.9rem;"><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></td>
                                <td class="py-3" style="color: var(--color-text); font-weight: 500;"><?php echo htmlspecialchars($ingredient); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Steps Panel -->
    <div class="col-12 col-md-7 col-lg-8">
        <div class="steps-card">
            <h3 class="font-serif mb-4" style="font-size: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 28px; height: 28px; color: var(--color-accent);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 8h18"></path>
                    <path d="M5 8v10a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8"></path>
                    <path d="M9 3v5"></path>
                    <path d="M15 3v5"></path>
                    <path d="M12 3h.01"></path>
                    <path d="M2 12h2"></path>
                    <path d="M20 12h2"></path>
                </svg>
                Langkah Penyediaan
            </h3>
            <div class="d-flex flex-column gap-4">
                <?php foreach ($steps as $index => $step): ?>
                    <div class="d-flex gap-3">
                        <div>
                            <span class="step-number"><?php echo $index + 1; ?></span>
                        </div>
                        <div class="pt-1">
                            <p class="mb-0 font-sans" style="color: var(--color-text); font-size: 1.05rem; line-height: 1.6;"><?php echo htmlspecialchars($step); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($kuehDetails['VIDEO'])): ?>
    <!-- Video Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="double-bezel-outer">
                <div class="double-bezel-inner" style="padding: 0;">
                    <div class="video-container">
                        <iframe id="youtube-embed" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Page Closing Tags (Backwards-compatibility for 5-div open layout) -->
</main> <!-- content-body -->
</div> <!-- content-wrapper-compat-4 -->
</div> <!-- content-wrapper-compat-3 -->
</div> <!-- main-layout -->
</div> <!-- app-container -->

<?php include('../includes/footer.php'); ?>

<script>
    // Your YouTube URL
    const youtubeUrl = "<?php echo $kuehDetails['VIDEO']; ?>";

    // Function to extract video ID from URL
    function getVideoId(url) {
        if (!url) return null;
        if (url.includes("v=")) {
            const videoId = url.split("v=")[1];
            const ampersandPosition = videoId.indexOf("&");
            return ampersandPosition !== -1 ? videoId.substring(0, ampersandPosition) : videoId;
        } else if (url.includes("youtu.be")) {
            const videoId = url.split("/").pop();
            const questionMarkPosition = videoId.indexOf("?");
            return questionMarkPosition !== -1 ? videoId.substring(0, questionMarkPosition) : videoId;
        }
        return null;
    }

    // Set the iframe src dynamically
    const videoId = getVideoId(youtubeUrl);
    if (videoId) {
        const iframe = document.getElementById("youtube-embed");
        if (iframe) {
            iframe.setAttribute("src", `https://www.youtube.com/embed/${videoId}`);
            iframe.setAttribute("title", "YouTube video player");
            iframe.setAttribute("allow", "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture");
        }
    }

    function copyToClipboard() {
        const currentUrl = window.location.href;
        navigator.clipboard.writeText(currentUrl)
            .then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'success',
                    title: 'Pautan telah disalin ke papan klip!',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            })
            .catch((error) => {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'Gagal menyalin pautan!',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                console.error('Failed to copy link: ', error);
            });
    }

    function copyToWhatsapp() {
        const currentUrl = window.location.href;
        const message = "Mari cuba resipi " + "<?php echo addslashes($kuehDetails['KUEHNAME']); ?>" + " ini di KuehLegacy: " + currentUrl;
        const encodedMessage = encodeURIComponent(message);
        const url = `https://wa.me/?text=${encodedMessage}`;
        window.open(url, '_blank');
    }

    function toggleFavorite(kueh_id) {
        fetch('../favorites/toggleFavorite.php', {
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
                    const saveRecipeButton = document.getElementById('saveRecipeButton');
                    if (data.isFavorite) {
                        saveRecipeButton.classList.remove('btn-outline-warning');
                        saveRecipeButton.classList.add('btn-warning');
                        saveRecipeButton.innerHTML = '<i class="bi bi-bookmark-fill me-2"></i> Simpan Resipi';

                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'success',
                            title: 'Ditambah ke kegemaran!',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        saveRecipeButton.classList.remove('btn-warning');
                        saveRecipeButton.classList.add('btn-outline-warning');
                        saveRecipeButton.innerHTML = '<i class="bi bi-bookmark me-2"></i> Simpan Resipi';

                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'success',
                            title: 'Dikeluarkan dari kegemaran!',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                } else {
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: 'error',
                        title: 'Operasi gagal: ' + data.message,
                        showConfirmButton: false,
                        timer: 3000,
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
                    timer: 3000,
                    timerProgressBar: true
                });
            });
    }
</script>
</body>
</html>