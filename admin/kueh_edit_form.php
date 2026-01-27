<?php
include('header_admin.php');
include('connection.php');

// Add Intervention Image configuration
use Intervention\Image\ImageManagerStatic as Image;

Image::configure(['driver' => 'gd']);

// Check if KUEHID is provided in the query string (for update mode)
if (isset($_GET['kuehId'])) {
    $kuehId = $_GET['kuehId'];

    // Fetch existing kueh data
    $sql_fetch_kueh = "SELECT * FROM KUEH WHERE KUEHID = ?";
    $stmt_fetch_kueh = mysqli_prepare($condb, $sql_fetch_kueh);
    mysqli_stmt_bind_param($stmt_fetch_kueh, "i", $kuehId);
    mysqli_stmt_execute($stmt_fetch_kueh);
    $result_fetch_kueh = mysqli_stmt_get_result($stmt_fetch_kueh);
    $kuehData = mysqli_fetch_assoc($result_fetch_kueh);
    mysqli_stmt_close($stmt_fetch_kueh);

    // Initialize existing values for dropdowns
    $existingFoodType = $kuehData['FOODTYPECODE'];
    $existingMethod = $kuehData['METHODID'];
    $existingPopularity = $kuehData['POPULARID'];
    $existingOrigin = $kuehData['ORIGINID'];
    $existingLink = $kuehData['VIDEO'];

    // Fetch ingredients
    $sql_fetch_ingredients = "SELECT NAMEITEM FROM ITEMS WHERE KUEHID = ?";
    $stmt_fetch_ingredients = mysqli_prepare($condb, $sql_fetch_ingredients);
    mysqli_stmt_bind_param($stmt_fetch_ingredients, "i", $kuehId);
    mysqli_stmt_execute($stmt_fetch_ingredients);
    $result_ingredients = mysqli_stmt_get_result($stmt_fetch_ingredients);
    $ingredients = [];
    while ($row = mysqli_fetch_assoc($result_ingredients)) {
        $ingredients[] = $row['NAMEITEM'];
    }
    mysqli_stmt_close($stmt_fetch_ingredients);

    // Fetch steps
    $sql_fetch_steps = "SELECT STEP FROM STEPS WHERE KUEHID = ?";
    $stmt_fetch_steps = mysqli_prepare($condb, $sql_fetch_steps);
    mysqli_stmt_bind_param($stmt_fetch_steps, "i", $kuehId);
    mysqli_stmt_execute($stmt_fetch_steps);
    $result_steps = mysqli_stmt_get_result($stmt_fetch_steps);
    $steps = [];
    while ($row = mysqli_fetch_assoc($result_steps)) {
        $steps[] = $row['STEP'];
    }
    mysqli_stmt_close($stmt_fetch_steps);

    // Get existing image path
    $existingImagePath = null;
    if (!empty($kuehData['IMAGE'])) {
        $existingImagePath = '../kueh_images/' . $kuehData['IMAGE'];
    }
} else {
    // Initialize variables for non-edit mode
    $existingImagePath = null;
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
        $filename = null;

        // Check if a new image is uploaded
        if (!empty($_FILES['image']['tmp_name'])) {
            // File upload validation
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'jfif'];
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB

            $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $file_mime = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];

            if (!in_array($file_ext, $allowed_extensions)) {
                die("<script>alert('Invalid file extension.');</script>");
            }
            if (!in_array($file_mime, $allowed_mimes)) {
                die("<script>alert('Invalid file type.');</script>");
            }
            if ($file_size > $max_size) {
                die("<script>alert('File size exceeds 5MB limit.');</script>");
            }

            // Verify it's actually an image
            $image_info = getimagesize($_FILES['image']['tmp_name']);
            if ($image_info === false) {
                die("<script>alert('File is not a valid image.');</script>");
            }

            // Delete old image file
            $old_image_query = "SELECT image FROM kueh WHERE kuehId = ?";
            $old_image_stmt = mysqli_prepare($condb, $old_image_query);
            mysqli_stmt_bind_param($old_image_stmt, "i", $kuehID);
            mysqli_stmt_execute($old_image_stmt);
            $old_result = mysqli_stmt_get_result($old_image_stmt);
            $old_row = mysqli_fetch_assoc($old_result);
            if ($old_row && !empty($old_row['image'])) {
                $old_path = '../kueh_images/' . $old_row['image'];
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
            mysqli_stmt_close($old_image_stmt);

            // Generate unique filename
            $filename = uniqid() . '_' . time() . '.' . $file_ext;
            $targetPath = '../kueh_images/' . $filename;

            // Optimize and save image
            $img = Image::make($_FILES['image']['tmp_name']);
            if ($img->width() > 1920) {
                $img->resize(1920, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            $img->save($targetPath, 80);

            // Update with new image
            $sql_kueh = "UPDATE KUEH SET KUEHNAME = ?, KUEHDESC = ?, FOODTYPECODE = ?, METHODID = ?, POPULARID = ?, ORIGINID = ?, VIDEO = ?, IMAGE = ? WHERE KUEHID = ?";
            $laksana_sql_kueh = mysqli_prepare($condb, $sql_kueh);
            mysqli_stmt_bind_param($laksana_sql_kueh, "sssissssi", $kuehName, $kuehDesc, $foodTypeCode, $methodId, $popularId, $originId, $video, $filename, $kuehID);
        } else {
            // No new image uploaded, retain existing
            $sql_kueh = "UPDATE KUEH SET KUEHNAME = ?, KUEHDESC = ?, FOODTYPECODE = ?, METHODID = ?, POPULARID = ?, ORIGINID = ?, VIDEO = ? WHERE KUEHID = ?";
            $laksana_sql_kueh = mysqli_prepare($condb, $sql_kueh);
            mysqli_stmt_bind_param($laksana_sql_kueh, "sssisssi", $kuehName, $kuehDesc, $foodTypeCode, $methodId, $popularId, $originId, $video, $kuehID);
        }
    }

    if (mysqli_stmt_execute($laksana_sql_kueh)) {

        // Handle ingredients and steps updates
        // Delete existing ingredients and steps for update mode
        if (isset($kuehID)) {
            $sql_delete_ingredients = "DELETE FROM ITEMS WHERE KUEHID = ?";
            $stmt_delete_ingredients = mysqli_prepare($condb, $sql_delete_ingredients);
            mysqli_stmt_bind_param($stmt_delete_ingredients, "i", $kuehID);
            mysqli_stmt_execute($stmt_delete_ingredients);
            mysqli_stmt_close($stmt_delete_ingredients);

            $sql_delete_steps = "DELETE FROM STEPS WHERE KUEHID = ?";
            $stmt_delete_steps = mysqli_prepare($condb, $sql_delete_steps);
            mysqli_stmt_bind_param($stmt_delete_steps, "i", $kuehID);
            mysqli_stmt_execute($stmt_delete_steps);
            mysqli_stmt_close($stmt_delete_steps);
        }

        // Insert new ingredients
        if (!empty($ingredients)) {
            $sql_items = "INSERT INTO ITEMS (KUEHID, NAMEITEM) VALUES (?, ?)";
            $laksana_sql_items = mysqli_prepare($condb, $sql_items);

            foreach ($ingredients as $ingredient) {
                mysqli_stmt_bind_param($laksana_sql_items, "is", $kuehID, $ingredient);
                if (!mysqli_stmt_execute($laksana_sql_items)) {
                    echo "Error inserting ingredient: " . htmlentities(mysqli_error($condb));
                }
            }
            mysqli_stmt_close($laksana_sql_items);
        }

        // Insert new steps
        if (!empty($steps)) {
            $sql_steps = "INSERT INTO STEPS (KUEHID, STEP) VALUES (?, ?)";
            $laksana_sql_steps = mysqli_prepare($condb, $sql_steps);

            foreach ($steps as $step) {
                mysqli_stmt_bind_param($laksana_sql_steps, "is", $kuehID, $step);
                if (!mysqli_stmt_execute($laksana_sql_steps)) {
                    echo "Error inserting step: " . htmlentities(mysqli_error($condb));
                }
            }
            mysqli_stmt_close($laksana_sql_steps);
        }

        echo "<script>
            window.location.href = 'kueh_info.php?msg=update_success';
        </script>";
        exit();
    } else {
        echo "<script>alert('Error saving kueh details: " . htmlentities(mysqli_error($condb)) . "');</script>";
    }

    mysqli_stmt_close($laksana_sql_kueh);
}

function getOptionsWithIdAndName($query, $idField, $nameField, $selectedValue = null)
{
    global $condb;
    $stmt = mysqli_prepare($condb, $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $options = "";
    while ($row = mysqli_fetch_assoc($result)) {
        $selected = ($row[$idField] == $selectedValue) ? 'selected' : '';
        $options .= "<option value='{$row[$idField]}' $selected>{$row[$nameField]}</option>";
    }
    mysqli_stmt_close($stmt);
    return $options;
}

// Populate dropdowns with existing values
$foodtypeOptions = getOptionsWithIdAndName("SELECT FOODTYPECODE, TYPENAME FROM FOODTYPE", "FOODTYPECODE", "TYPENAME", $existingFoodType ?? null);
$methodOptions = getOptionsWithIdAndName("SELECT METHODID, METHODNAME FROM METHOD", "METHODID", "METHODNAME", $existingMethod ?? null);
$popularOptions = getOptionsWithIdAndName("SELECT POPULARID, LEVEL FROM POPULARITY", "POPULARID", "LEVEL", $existingPopularity ?? null);
$originOptions = getOptionsWithIdAndName("SELECT ORIGINCODE, NAMESTATE FROM ORIGIN", "ORIGINCODE", "NAMESTATE", $existingOrigin ?? null);

// Fetch creator's details (user or admin who created this kueh)
$username = '';
$email = '';

if (isset($kuehData['USERNAME'])) {
    $creatorUsername = $kuehData['USERNAME'];
    
    // Try to find in USERS table first
    $sql = "SELECT USERNAME, EMAIL FROM USERS WHERE USERNAME = ?";
    $stmt = mysqli_prepare($condb, $sql);
    mysqli_stmt_bind_param($stmt, "s", $creatorUsername);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $userData = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if ($userData) {
        $username = $userData['USERNAME'];
        $email = $userData['EMAIL'] ?? '';
    } else {
        // If not found in USERS, try ADMIN table
        $sql = "SELECT USERNAME, EMAIL FROM ADMIN WHERE USERNAME = ?";
        $stmt = mysqli_prepare($condb, $sql);
        mysqli_stmt_bind_param($stmt, "s", $creatorUsername);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $adminData = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if ($adminData) {
            $username = $adminData['USERNAME'];
            $email = $adminData['EMAIL'] ?? '';
        }
    }
}

mysqli_close($condb);
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
                                echo isset($existingImagePath) && !empty($existingImagePath) && file_exists($existingImagePath)
                                    ? htmlspecialchars($existingImagePath)
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