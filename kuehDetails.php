<?php
include('header.php');
include('connection.php');

// Fetch kueh details by kueh_id
function fetchKuehDetails($conn, $kueh_id)
{
    $sql = "SELECT KUEHID, KUEHNAME, KUEHDESC, TAGKUEH, FOODTYPECODE, METHODID, VIDEO, IMAGE FROM KUEH WHERE KUEHID = :kueh_id";
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ':kueh_id', $kueh_id);
    oci_execute($stid);

    $kuehDetails = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS);

    // Convert BLOB image to Base64
    if ($kuehDetails && isset($kuehDetails['IMAGE'])) {
        $blobData = $kuehDetails['IMAGE']->load(); // Load BLOB data
        $base64Image = base64_encode($blobData); // Convert to Base64
        $kuehDetails['KUEH_IMAGE_BASE64'] = 'data:image/jpeg;base64,' . $base64Image; // Add Base64 string to the array
    }

    return $kuehDetails;
}

// Fetch ingredients by kueh_id
function fetchIngredients($conn, $kueh_id)
{
    $sql = "SELECT NAMEITEM FROM ITEMS WHERE KUEHID = :kueh_id"; // Adjust the query as per your table structure
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ':kueh_id', $kueh_id);
    oci_execute($stid);

    $ingredients = [];
    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        $ingredients[] = $row['NAMEITEM']; // Adjust the column name as per your table
    }
    return $ingredients;
}

// Fetch steps by kueh_id
function fetchSteps($conn, $kueh_id)
{
    $sql = "SELECT STEP FROM steps WHERE KUEHID = :kueh_id"; // Adjust the query as per your table structure
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ':kueh_id', $kueh_id);
    oci_execute($stid);

    $steps = [];
    while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        $steps[] = $row['STEP']; // Adjust the column name as per your table
    }
    return $steps;
}

// Function to check if the kueh is in the user's favorites
function isKuehInFavorites($conn, $kueh_id, $username)
{
    $sql = "SELECT COUNT(*) AS count FROM FAVORITE WHERE KUEHID = :kueh_id AND USERNAME = :username";
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ':kueh_id', $kueh_id);
    oci_bind_by_name($stid, ':username', $username);
    oci_execute($stid);

    $row = oci_fetch_array($stid, OCI_ASSOC);
    return ($row['COUNT'] > 0); // Returns true if the kueh is in favorites
}

//START============================================================================================================================

// Get the username from the session (assuming the user is logged in)
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
    oci_close($condb);
} else {
    // Handle case where kueh_id is not provided
    die("Kueh ID is missing.");
}

/* GET DETAIL OF THE KUEH'S CREATOR */
$blobQuery = "SELECT COALESCE(u.USERNAME, a.USERNAME) AS USERNAME, COALESCE(u.NAME, a.NAME) AS NAME, u.IMAGE AS IMAGE
                    FROM KUEH k
                    LEFT JOIN USERS u ON k.USERNAME = u.USERNAME
                    LEFT JOIN ADMIN a ON k.USERNAME = a.USERNAME
                    WHERE k.KUEHID = :kuehID
                    ORDER BY k.KUEHID DESC";

$blobStmt = oci_parse($condb, $blobQuery);
oci_bind_by_name($blobStmt, ':kuehID', $kueh_id);
oci_execute($blobStmt);

if ($blobRow = oci_fetch_assoc($blobStmt)) {
    $creator['USERNAMECREATOR'] = $blobRow['USERNAME'];
    $creator['NAMECREATOR'] = $blobRow['NAME'];
    $creator['CREATORIMAGE'] = $blobRow['IMAGE'];
}

oci_free_statement($blobStmt);
?>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Legacy Kueh System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/product-sans" rel="stylesheet">
    <style>
        .video-container {
      position: relative;
      padding-bottom: 56.25%; /* 16:9 aspect ratio */
      overflow: hidden;
      height: 0;
      max-width: 100%;
    }
    .video-container iframe {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
    }
    </style>
</head>

<div class="w3-round-large" style="height: 92%; margin-top: 4%">
    <!--CONTENT START HERE-->
    <div class="container w-75">
        <div class="row">
            <div class="col-12 col-md-4 my-4">
                <?php if (isset($kuehDetails['KUEH_IMAGE_BASE64'])): ?>
                    <!-- Fixed size image container -->
                    <div style="width: 300px; height: 300px; overflow: hidden; border-radius: 10px;">
                        <img src="<?php echo $kuehDetails['KUEH_IMAGE_BASE64']; ?>"
                            class="img-fluid text-center rounded-3"
                            alt="<?php echo $kuehDetails['KUEHNAME']; ?>"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                <?php else: ?>
                    <p>No image available.</p>
                <?php endif; ?>
            </div>
            <div class="col-12 col-md my-4 d-flex flex-column">
                <div class="col-12">
                    <h2 class="w-100 p-1 border-0 shadow-none fw-bolder fs-2"><?php echo $kuehDetails['KUEHNAME']; ?></h2>
                </div>
                <div class="col-12">
                    <div class="d-flex align-items-center my-2">
                    <?PHP
                                                        if ($creator['CREATORIMAGE'] != null) {
                                                            ?><img src="<?= $creator['CREATORIMAGE'] ?>"
                                                                alt="Profile Picture" class="rounded-circle me-2 border" width="40"
                                                                height="40"><?PHP
                                                        } else {
                                                            ?>
                                                            <img src="sources/header/logo.png" alt="Profile Picture"
                                                                class="rounded-circle me-2 border" width="40" height="40"><?PHP
                                                        }
                                                        ?>
                        
                        <div class="ms-3">
                            <h6 class="mb-0"><?php echo $creator['NAMECREATOR'] ?></h6> <!--NAMA ORANG SHARE KUIH -->
                            <!-- <small class="text-muted">@Uchu • Pahang, Malaysia</small> -->
                        </div>
                    </div>
                </div>
                <p class="mb-2"><?php echo $kuehDetails['KUEHDESC']; ?></p>
                <p class="mb-4">
                    <?php echo $kuehDetails['TAGKUEH']; ?>
                </p>
                <div class="mt-auto">
                    <?php
                    if($creator['USERNAMECREATOR']==($_SESSION['username'])){
                        echo '<a href="editKueh.php?kuehId='.$kueh_id.'" type="button" class="btn btn-outline-primary me-2 fw-bold"><i class="bi bi-pencil-square"></i> Sunting</a>';
                        echo '<a href="deleteKueh.php?jadual=KUEH&medan_kp=KUEHID&kp='.$kueh_id.'" type="button" class="btn btn-outline-danger me-2 fw-bold" onClick=\"return confirm("Confirm to delete data?")\"><i class="bi bi-trash"></i> Padam</a>';

                    
                    }else{ ?>
                        <button type="button"
                        class="btn <?php echo $isFavorite ? 'btn-warning' : 'btn-outline-warning'; ?> me-2 fw-bold"
                        id="saveRecipeButton"
                        onclick="toggleFavorite(<?php echo $kueh_id; ?>)">
                        <i class="bi <?php echo $isFavorite ? 'bi-bookmark-fill' : 'bi-bookmark'; ?>"></i> Simpan Resipi
                    </button>
                    <?php }
                    ?>
                    <button type="button" class="btn btn-outline-secondary me-2 fw-bold"><i class="bi bi-folder-plus"></i> Tambah ke folder</button>
                    <button type="button" class="btn btn-outline-secondary me-2 fw-bold" onclick="copyToClipboard()">
                        <i class="bi bi-upload"></i> Kongsi
                    </button>
                    <button type="button" class="btn btn-success me-2 fw-bold" onclick="copyToWhatsapp()">
                        <i class="bi bi-whatsapp"></i> Kongsi ke Whatsapp
                    </button>
                    
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <?php if($kuehDetails['VIDEO'] != null) { ?>
        <h1 class="fw-bolder">Video Rujukan</h1>
        <div class="video-container w3-margin"><iframe id="youtube-embed" frameborder="0" allowfullscreen></iframe></div>
        <?php } ?>

            <div class="col-12 col-lg-3 col-md-6 py-3">
                <h1 class="fw-bolder">Ramuan</h1>
                <table class="table">
                    <tbody>
                        <?php foreach ($ingredients as $index => $ingredient): ?>
                            <tr>
                                <td scope="row"><?php echo $index + 1; ?></td>
                                <td><?php echo $ingredient; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-12 col-lg col-md-6 py-3 ms-3">
                <h1 class="fw-bolder">Langkah</h1>
                <div class="row gy-3">
                    <div class="col-12">
                        <?php foreach ($steps as $index => $step): ?>
                            <div class="d-flex mb-3">
                                <div class="me-3">
                                    <span class="badge bg-secondary rounded-pill"><?php echo $index + 1; ?></span>
                                </div>
                                <div>
                                    <p class="mb-0"><?php echo $step; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Your YouTube URL
    const youtubeUrl = "<?php echo $kuehDetails['VIDEO']; ?>";

    // Function to extract video ID from URL
    function getVideoId(url) {
  if (url.includes("v=")) {
    // For full URLs (https://www.youtube.com/watch?v=...)
    const videoId = url.split("v=")[1];
    const ampersandPosition = videoId.indexOf("&");
    return ampersandPosition !== -1 ? videoId.substring(0, ampersandPosition) : videoId;
  } else if (url.includes("youtu.be")) {
    // For shortened URLs (https://youtu.be/...)
    const videoId = url.split("/").pop(); // Get the last part of the URL
    const questionMarkPosition = videoId.indexOf("?");
    return questionMarkPosition !== -1 ? videoId.substring(0, questionMarkPosition) : videoId;
  }
  return null; // Return null if the URL is invalid
}

    // Set the iframe src dynamically
    const videoId = getVideoId(youtubeUrl);
    const iframe = document.getElementById("youtube-embed");
    iframe.setAttribute("src", `https://www.youtube.com/embed/${videoId}`);
    iframe.setAttribute("title", "YouTube video player");
    iframe.setAttribute("allow", "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture");

    function copyToClipboard() {
        // Get the current URL
        const currentUrl = window.location.href;

        // Copy the URL to the clipboard
        navigator.clipboard.writeText(currentUrl)
            .then(() => {
                // Show a SweetAlert2 success notification
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'success',
                    title: 'Link has been copied to clipboard!',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            })
            .catch((error) => {
                // Show a SweetAlert2 error notification if copying fails
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'Failed to copy link to clipboard!',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
                console.error('Failed to copy link: ', error);
            });
    }
    function copyToWhatsapp() {
        // Get the current URL
        const currentUrl = window.location.href;

        // Copy the URL to the clipboard
        const message = "Check out this kuih I found on Kueh Legacy! "+" "+currentUrl;
        const encodedMessage = encodeURIComponent(message);
        const url = `https://wa.me/?text=${encodedMessage}`;
        
        // Open WhatsApp in a new tab
        window.open(url, '_blank')

            .then(() => {
                // Show a SweetAlert2 success notification
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'success',
                    title: 'Link has been copied to clipboard!',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            })
            .catch((error) => {
                // Show a SweetAlert2 error notification if copying fails
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'Failed to copy link to clipboard!',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
                console.error('Failed to copy link: ', error);
            });
    }

    function toggleFavorite(kueh_id) {
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
                    const saveRecipeButton = document.getElementById('saveRecipeButton');
                    if (data.isFavorite) {
                        saveRecipeButton.classList.remove('btn-outline-warning');
                        saveRecipeButton.classList.add('btn-warning');
                        saveRecipeButton.innerHTML = '<i class="bi bi-bookmark-fill"></i> Simpan Resipi';

                        // Show success toast for adding to favorites
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'success',
                            title: 'Added to favorites!',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer);
                                toast.addEventListener('mouseleave', Swal.resumeTimer);
                            }
                        });
                    } else {
                        saveRecipeButton.classList.remove('btn-warning');
                        saveRecipeButton.classList.add('btn-outline-warning');
                        saveRecipeButton.innerHTML = '<i class="bi bi-bookmark"></i> Simpan Resipi';

                        // Show success toast for removing from favorites
                        Swal.fire({
                            toast: true,
                            position: 'top',
                            icon: 'success',
                            title: 'Removed from favorites!',
                            showConfirmButton: false,
                            timer: 3000,
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
                        timer: 3000,
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
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            });
    }
</script>


<?php
include('footer.php');
//include('popup.php');
?>