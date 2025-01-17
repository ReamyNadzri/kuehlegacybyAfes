<?php
include('header.php');
include('connection.php');

// Fetch kueh details by kueh_id
function fetchKuehDetails($conn, $kueh_id)
{
    $sql = "SELECT KUEHID, KUEHNAME, KUEHDESC, TAGKUEH, FOODTYPECODE, METHODID, IMAGE FROM KUEH WHERE KUEHID = :kueh_id";
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
</head>

<div class="w3-round-large" style="height: 92%; margin-top: 4%">
    <!--CONTENT START HERE-->
    <div class="container w-75">
        <div class="row">
            <div class="col-12 col-md-4 my-4">
                <?php if (isset($kuehDetails['KUEH_IMAGE_BASE64'])): ?>
                    <img src="<?php echo $kuehDetails['KUEH_IMAGE_BASE64']; ?>" class="img-fluid text-center rounded-3" alt="<?php echo $kuehDetails['KUEHNAME']; ?>" style="min-height:380px; object-fit: cover;">
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
                        <img src="sources\header\logo.png" alt="Profile Picture" class="rounded-circle border" width="50" height="50">
                        <div class="ms-3">
                            <h6 class="mb-0">Haziq Akram</h6> <!--NAMA ORANG SHARE KUIH -->
                            <small class="text-muted">@Uchu • Pahang, Malaysia</small>
                        </div>
                    </div>
                </div>
                <p class="mb-2"><?php echo $kuehDetails['FOODTYPECODE']; ?><br><?php echo $kuehDetails['KUEHDESC']; ?></p>
                <p class="mb-4">
                    <?php echo $kuehDetails['TAGKUEH']; ?>
                </p>
                <div class="mt-auto">
                    <button type="button"
                        class="btn <?php echo $isFavorite ? 'btn-warning' : 'btn-outline-warning'; ?> me-2 fw-bold"
                        id="saveRecipeButton"
                        onclick="toggleFavorite(<?php echo $kueh_id; ?>)">
                        <i class="bi <?php echo $isFavorite ? 'bi-bookmark-fill' : 'bi-bookmark'; ?>"></i> Simpan Resipi
                    </button>
                    <button type="button" class="btn btn-outline-secondary me-2 fw-bold"><i class="bi bi-folder-plus"></i> Tambah ke folder</button>
                    <button type="button" class="btn btn-outline-secondary me-2 fw-bold" onclick="copyToClipboard()"><i class="bi bi-upload"></i> Kongsi</button>
                    <button type="button" class="btn btn-outline-secondary fw-bold"><i class="bi bi-printer"></i> Cetak</button>
                    <button type="button" class="btn btn-outline-secondary fw-bold border-0"><i class="bi bi-three-dots"></i></button>
                </div>
            </div>
        </div>
        <div class="row mt-5">
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
    function copyToClipboard() {
        // Get the current URL
        const currentUrl = window.location.href;

        // Copy the URL to the clipboard
        navigator.clipboard.writeText(currentUrl)
            .then(() => {
                // Show a notification that the link has been copied
                alert('Link has been copied to clipboard!');
            })
            .catch((error) => {
                // Handle any errors
                console.error('Failed to copy link: ', error);
                alert('Failed to copy link. Please try again.');
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
                    } else {
                        saveRecipeButton.classList.remove('btn-warning');
                        saveRecipeButton.classList.add('btn-outline-warning');
                        saveRecipeButton.innerHTML = '<i class="bi bi-bookmark"></i> Simpan Resipi';
                    }
                } else {
                    alert('Failed to toggle favorite status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while toggling favorite status.');
            });
    }
</script>


<?php
include('footer.php');
//include('popup.php');
?>