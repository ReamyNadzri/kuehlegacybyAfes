<?php
include('header_admin.php');
include('connection.php');

// Check if KUEHID is provided in the query string (for update mode)
if (isset($_GET['kuehId'])) {
    $kuehId = $_GET['kuehId'];

    // Fetch existing kueh data
    $sql_fetch_kueh = "SELECT * FROM KUEH WHERE KUEHID = :kuehId";
    $stmt_fetch_kueh = oci_parse($condb, $sql_fetch_kueh);
    oci_bind_by_name($stmt_fetch_kueh, ":kuehId", $kuehId);
    oci_execute($stmt_fetch_kueh);
    $kuehData = oci_fetch_assoc($stmt_fetch_kueh);

    // Initialize existing values for dropdowns
    $existingFoodType = $kuehData['FOODTYPECODE'];
    $existingMethod = $kuehData['METHODID'];
    $existingPopularity = $kuehData['POPULARID'];
    $existingOrigin = $kuehData['ORIGINID'];
    $existingLink = $kuehData['VIDEO'];

    // Fetch ingredients
    $sql_fetch_ingredients = "SELECT NAMEITEM FROM ITEMS WHERE KUEHID = :kuehId";
    $stmt_fetch_ingredients = oci_parse($condb, $sql_fetch_ingredients);
    oci_bind_by_name($stmt_fetch_ingredients, ":kuehId", $kuehId);
    oci_execute($stmt_fetch_ingredients);
    $ingredients = [];
    while ($row = oci_fetch_assoc($stmt_fetch_ingredients)) {
        $ingredients[] = $row['NAMEITEM'];
    }

    // Fetch steps
    $sql_fetch_steps = "SELECT STEP FROM STEPS WHERE KUEHID = :kuehId";
    $stmt_fetch_steps = oci_parse($condb, $sql_fetch_steps);
    oci_bind_by_name($stmt_fetch_steps, ":kuehId", $kuehId);
    oci_execute($stmt_fetch_steps);
    $steps = [];
    while ($row = oci_fetch_assoc($stmt_fetch_steps)) {
        $steps[] = $row['STEP'];
    }

    // Check if IMAGE BLOB is not empty before loading
    $existingImage = null;
    if (!empty($kuehData['IMAGE']) && $kuehData['IMAGE']->size() > 0) {
        $existingImage = $kuehData['IMAGE']->load();
    }
}

if (isset($_POST['submit'])) {
    $kuehID = $_POST['kuehId'];
    $kuehName = $_POST['kuehName'];
    $kuehDesc = $_POST['kuehDesc'];
    $foodTypeCode = $_POST['foodtype'];
    $methodId = $_POST['method'];
    $popularId = $_POST['popular'];
    $originId = $_POST['origin'];
    $video = $_POST['video'];
    $ingredients = $_POST['ingredients'] ?? [];
    $steps = $_POST['steps'] ?? [];

    // Check if KUEHID is provided (update mode)
    if ($kuehID != null) {

        // Check if a new image is uploaded
        if (!empty($_FILES['image']['tmp_name'])) {
            // New image is uploaded
            $imageData = file_get_contents($_FILES['image']['tmp_name']);
            $sql_kueh = "UPDATE KUEH SET KUEHNAME = :kuehName, KUEHDESC = :kuehDesc, FOODTYPECODE = :foodTypeCode, METHODID = :methodId, POPULARID = :popularId, ORIGINID = :originId, VIDEO = :video, IMAGE = EMPTY_BLOB() WHERE KUEHID = :kuehId RETURNING IMAGE INTO :image";
            $lob = oci_new_descriptor($condb, OCI_D_LOB);
        } else {
            // No new image is uploaded, retain the existing image
            $sql_kueh = "UPDATE KUEH SET KUEHNAME = :kuehName, KUEHDESC = :kuehDesc, FOODTYPECODE = :foodTypeCode, METHODID = :methodId, POPULARID = :popularId, ORIGINID = :originId, VIDEO = :video WHERE KUEHID = :kuehId";
        }

        $laksana_sql_kueh = oci_parse($condb, $sql_kueh);
        oci_bind_by_name($laksana_sql_kueh, ":kuehId", $kuehID);
    }
    // Bind parameters
    oci_bind_by_name($laksana_sql_kueh, ":kuehName", $kuehName);
    oci_bind_by_name($laksana_sql_kueh, ":kuehDesc", $kuehDesc);
    oci_bind_by_name($laksana_sql_kueh, ":foodTypeCode", $foodTypeCode);
    oci_bind_by_name($laksana_sql_kueh, ":methodId", $methodId);
    oci_bind_by_name($laksana_sql_kueh, ":popularId", $popularId);
    oci_bind_by_name($laksana_sql_kueh, ":originId", $originId);
    oci_bind_by_name($laksana_sql_kueh, ":video", $video);

    // Bind the BLOB descriptor
    if (isset($lob)) {
        oci_bind_by_name($laksana_sql_kueh, ":image", $lob, -1, SQLT_BLOB);
    }

    if (oci_execute($laksana_sql_kueh, OCI_DEFAULT)) {
        if (isset($lob)) {
            // Save the new image data into the BLOB
            $lob->save($imageData);
            oci_commit($condb);
            $lob->free();
        }

        // Handle ingredients and steps updates
        // Delete existing ingredients and steps for update mode
        if (isset($kuehID)) {
            $sql_delete_ingredients = "DELETE FROM ITEMS WHERE KUEHID = :kuehId";
            $stmt_delete_ingredients = oci_parse($condb, $sql_delete_ingredients);
            oci_bind_by_name($stmt_delete_ingredients, ":kuehId", $kuehID);
            oci_execute($stmt_delete_ingredients);

            $sql_delete_steps = "DELETE FROM STEPS WHERE KUEHID = :kuehId";
            $stmt_delete_steps = oci_parse($condb, $sql_delete_steps);
            oci_bind_by_name($stmt_delete_steps, ":kuehId", $kuehID);
            oci_execute($stmt_delete_steps);
        }

        // Insert new ingredients
        if (!empty($ingredients)) {
            $sql_items = "INSERT INTO ITEMS (KUEHID, NAMEITEM) VALUES (:kuehId, :nameitem)";
            $laksana_sql_items = oci_parse($condb, $sql_items);

            foreach ($ingredients as $ingredient) {
                oci_bind_by_name($laksana_sql_items, ":kuehId", $kuehID);
                oci_bind_by_name($laksana_sql_items, ":nameitem", $ingredient);
                if (!oci_execute($laksana_sql_items)) {
                    $e = oci_error($laksana_sql_items);
                    echo "Error inserting ingredient: " . htmlentities($e['message']);
                }
            }
            oci_free_statement($laksana_sql_items);
        }

        // Insert new steps
        if (!empty($steps)) {
            $sql_steps = "INSERT INTO STEPS (KUEHID, STEP) VALUES (:kuehId, :step)";
            $laksana_sql_steps = oci_parse($condb, $sql_steps);

            foreach ($steps as $step) {
                oci_bind_by_name($laksana_sql_steps, ":kuehId", $kuehID);
                oci_bind_by_name($laksana_sql_steps, ":step", $step);
                if (!oci_execute($laksana_sql_steps)) {
                    $e = oci_error($laksana_sql_steps);
                    echo "Error inserting step: " . htmlentities($e['message']);
                }
            }
            oci_free_statement($laksana_sql_steps);
        }

        oci_commit($condb);
        echo "<script>
            window.location.href = 'kueh_info.php?msg=update_success';
        </script>";
        exit();
    } else {
        $e = oci_error($laksana_sql_kueh);
        echo "<script>alert('Error saving kueh details: " . htmlentities($e['message']) . "');</script>";
    }

    oci_free_statement($laksana_sql_kueh);
}

function getOptionsWithIdAndName($query, $idField, $nameField, $selectedValue = null)
{
    global $condb;
    $stid = oci_parse($condb, $query);
    oci_execute($stid);
    $options = "";
    while ($row = oci_fetch_assoc($stid)) {
        $selected = ($row[$idField] == $selectedValue) ? 'selected' : '';
        $options .= "<option value='{$row[$idField]}' $selected>{$row[$nameField]}</option>";
    }
    oci_free_statement($stid);
    return $options;
}

// Populate dropdowns with existing values
$foodtypeOptions = getOptionsWithIdAndName("SELECT FOODTYPECODE, TYPENAME FROM FOODTYPE", "FOODTYPECODE", "TYPENAME", $existingFoodType ?? null);
$methodOptions = getOptionsWithIdAndName("SELECT METHODID, METHODNAME FROM METHOD", "METHODID", "METHODNAME", $existingMethod ?? null);
$popularOptions = getOptionsWithIdAndName("SELECT POPULARID, LEVELSTAR FROM POPULARITY", "POPULARID", "LEVELSTAR", $existingPopularity ?? null);
$originOptions = getOptionsWithIdAndName("SELECT ORIGINCODE, NAMESTATE FROM ORIGIN", "ORIGINCODE", "NAMESTATE", $existingOrigin ?? null);


if (isset($_SESSION['adminid'])) {
    $adminId = $_SESSION['adminid'];
    $sql = "SELECT USERNAME, EMAIL, IMAGE FROM admin WHERE USERNAME = :adminid";
    $stmt = oci_parse($condb, $sql);
    oci_bind_by_name($stmt, ":adminid", $adminId);
    oci_execute($stmt);
    $adminData = oci_fetch_assoc($stmt);

    if ($adminData) {
        $username = $adminData['USERNAME'];
        $email = $adminData['EMAIL'];
    }
}

oci_close($condb);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legacy Kueh System</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/product-sans" rel="stylesheet">
    <style>
        input::placeholder {
            font-size: 1.5rem;
            color: #6c757d;
            opacity: 1;
        }

        #imageContainer {
            width: 300px;
            height: 300px;
            cursor: pointer;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #previewImage {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #imageUpload {
            display: none;
        }
    </style>
</head>

<body class="" style="background-color: #FFFAF0;">
    <div class="container w-75">
        <form id="kuehForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" name="submit" class="btn btn-primary mb-4">Simpan</button>
            </div>
            <!-- Existing image and title section -->
            <div class="row">
                <div class="col-12 col-md-4 my-4" id="imageContainer">
                    <!-- Image Preview -->
                    <img id="previewImage"
                        src="<?php
                                echo isset($existingImage) && !empty($existingImage)
                                    ? 'data:image/jpeg;base64,' . base64_encode($existingImage)
                                    : 'sources/kueh_default.png';
                                ?>"
                        class="img-fluid text-center rounded-3"
                        alt="Uploaded Image Preview">
                    <!-- Hidden File Input -->
                    <input type="file" name="image" id="imageUpload" accept="image/*">
                </div>
                <div class="col-12 col-md gy-4">
                    <div class="col-12 bg-primary">
                        <input class="w-100 p-1 border-0 shadow-none fw-bolder fs-2" style="background-color: #FFFAF0;" type="text" name="kuehName" placeholder="Tajuk: Kuih Lapis Atok" value="<?php echo isset($kuehData['KUEHNAME']) ? htmlspecialchars($kuehData['KUEHNAME']) : ''; ?>">
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center my-2">
                            <!-- Avatar -->
                            <img src="sources/profile-icon.png" alt="Profile Picture" class="rounded-circle border" width="50" height="50">
                            <!-- Text -->
                            <div class="ms-3">
                                <h6 class="mb-0"><?php echo htmlspecialchars($username); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($email); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <textarea class="w-100 p-1 border-0 shadow-none" name="kuehDesc" rows="6" style="background-color: #FFFAF0;" placeholder="Share kisah resepi anda"><?php echo isset($kuehData['KUEHDESC']) ? htmlspecialchars($kuehData['KUEHDESC']) : ''; ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Dropdown Section -->
            <div class="row mb-4">
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="foodtype" class="form-label fw-bold">Jenis makanan</label>
                    <select name="foodtype" id="foodtype" class="form-select" required>
                        <option value="" disabled>Pilih Jenis</option>
                        <?= $foodtypeOptions ?>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="method" class="form-label fw-bold">Cara Masakan</label>
                    <select name="method" id="method" class="form-select" required>
                        <option value="" disabled>Pilih Cara Masakan</option>
                        <?= $methodOptions ?>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="popular" class="form-label fw-bold">Populariti</label>
                    <select name="popular" id="popular" class="form-select" required>
                        <option value="" disabled>Pilih Populariti</option>
                        <?= $popularOptions ?>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="origin" class="form-label fw-bold">Negeri Asal</label>
                    <select name="origin" id="origin" class="form-select" required>
                        <option value="" disabled>Pilih Negeri Asal</option>
                        <?= $originOptions ?>
                    </select>
                </div>
                <div class="col-12">
                    <label for="origin" class="form-label fw-bold">Link Video Rujukan</label><br>
                    <input type="url" name="video" class="form-control" value="<?= $existingLink ?>" required>
                </div>
            </div>

            <!-- Ingredients Section -->
            <div class="row mt-5">
                <div class="col-12 col-lg-3 col-md-6 py-3">
                    <div class="row mb-2 align-items-center">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h1 class="fw-bolder m-0">Ramuan</h1>
                            <button type="button" class="btn btn-primary ms-3" id="addIngredientButton"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                    <div id="ingredientContainer" style="height: 250px; overflow-y: auto; padding: 10px;">
                        <?php if (isset($ingredients)): ?>
                            <?php foreach ($ingredients as $ingredient): ?>
                                <div class="input-group mb-2">
                                    <button type="button" class="btn btn-outline-secondary border-0"><i class="bi bi-justify"></i></button>
                                    <input type="text" name="ingredients[]" class="form-control" value="<?php echo htmlspecialchars($ingredient); ?>" required>
                                    <button type="button" class="btn btn-outline-danger delete-button"><i class="bi bi-x"></i></button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Steps Section -->
                <div class="col-12 col-lg col-md-6 py-3">
                    <div class="row mb-2 align-items-center">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h1 class="fw-bolder m-0">Cara Memasak</h1>
                            <button type="button" class="btn btn-primary ms-3" id="addStepButton"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                    <div id="stepContainer" style="height: 250px; overflow-y: auto; padding: 10px;">
                        <?php if (isset($steps)): ?>
                            <?php foreach ($steps as $step): ?>
                                <div class="input-group mb-2">
                                    <button type="button" class="btn btn-outline-secondary border-0"><i class="bi bi-justify"></i></button>
                                    <input type="text" name="steps[]" class="form-control" value="<?php echo htmlspecialchars($step); ?>" required>
                                    <button type="button" class="btn btn-outline-danger delete-button"><i class="bi bi-x"></i></button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <input type="hidden" name="kuehId" value="<?php echo isset($kuehId) ? $kuehId : ''; ?>">

        </form>
    </div><br><br>

    <script>
        // Function to handle the deletion of input fields
        function addDeleteListener(deleteButton) {
            deleteButton.addEventListener('click', function() {
                // Remove the parent div of the delete button
                this.closest('.input-group').remove();
            });
        }

        // Add ingredient input field
        document.getElementById('addIngredientButton').addEventListener('click', function() {
            const container = document.getElementById('ingredientContainer');
            const newInput = document.createElement('div');
            newInput.className = 'input-group mb-2';
            newInput.innerHTML = `
        <button type="button" class="btn btn-outline-secondary border-0"><i class="bi bi-justify"></i></button>
        <input type="text" name="ingredients[]" class="form-control" required>
        <button type="button" class="btn btn-outline-danger delete-button"><i class="bi bi-x"></i></button>
    `;
            container.appendChild(newInput);

            // Add delete listener to the new delete button
            const deleteButton = newInput.querySelector('.delete-button');
            addDeleteListener(deleteButton);
        });

        // Add step input field
        document.getElementById('addStepButton').addEventListener('click', function() {
            const container = document.getElementById('stepContainer');
            const newInput = document.createElement('div');
            newInput.className = 'input-group mb-2';
            newInput.innerHTML = `
        <button type="button" class="btn btn-outline-secondary border-0"><i class="bi bi-justify"></i></button>
        <input type="text" name="steps[]" class="form-control" required>
        <button type="button" class="btn btn-outline-danger delete-button"><i class="bi bi-x"></i></button>
    `;
            container.appendChild(newInput);

            // Add delete listener to the new delete button
            const deleteButton = newInput.querySelector('.delete-button');
            addDeleteListener(deleteButton);
        });

        // Add delete listeners to existing delete buttons (for pre-populated fields)
        document.querySelectorAll('.delete-button').forEach(function(deleteButton) {
            addDeleteListener(deleteButton);
        });

        // Image upload functionality
        document.getElementById('imageUpload').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Trigger file input when clicking on the image container
        document.getElementById('imageContainer').addEventListener('click', function() {
            document.getElementById('imageUpload').click();
        });

        // Form submission validation
        document.getElementById('kuehForm').addEventListener('submit', function(event) {
            const imageInput = document.getElementById('imageUpload');
            if (!imageInput.files || imageInput.files.length === 0 && !<?php echo isset($existingImage) ? 'true' : 'false'; ?>) {
                event.preventDefault();
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'Please Insert Image',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            }
        });
    </script>
</body>

</html>