<?php
include('header.php');
include('connection.php');

// Configure Intervention Image
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
    $result_kueh = mysqli_stmt_get_result($stmt_fetch_kueh);
    $kuehData = mysqli_fetch_assoc($result_kueh);

    // Initialize existing values for dropdowns
    $existingFoodType = $kuehData['FOODTYPECODE'];
    $existingMethod = $kuehData['METHODID'];
    $existingPopularity = $kuehData['POPULARID'];
    $existingOrigin = $kuehData['ORIGINID'];
    $existingLink = $kuehData['VIDEO'];
    $existingImage = $kuehData['IMAGE']; // Now stores filename

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
    mysqli_stmt_close($stmt_fetch_kueh);
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
        $imageUpdated = false;
        $newFileName = null;

        // Check if a new image is uploaded
        if (!empty($_FILES['image']['tmp_name'])) {
            // File validation
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'jfif'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            $fileSize = $_FILES['image']['size'];
            $fileMime = $_FILES['image']['type'];
            $fileName = basename($_FILES['image']['name']);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validate file
            if (
                $fileSize <= $maxFileSize &&
                in_array($fileMime, $allowedTypes) &&
                in_array($fileExt, $allowedExtensions) &&
                getimagesize($_FILES['image']['tmp_name']) !== false
            ) {

                // Process and optimize image
                try {
                    $img = Image::make($_FILES['image']['tmp_name']);

                    // Resize if larger than 1920px width
                    if ($img->width() > 1920) {
                        $img->resize(1920, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }

                    // Generate unique filename
                    $newFileName = $kuehID . '_' . time() . '.jpg';
                    $uploadPath = __DIR__ . '/kueh_images/' . $newFileName;

                    // Save optimized image
                    $img->save($uploadPath, 80);
                    $imageUpdated = true;
                } catch (Exception $e) {
                    // Fallback to standard upload
                    $newFileName = $kuehID . '_' . time() . '.' . $fileExt;
                    $uploadPath = __DIR__ . '/kueh_images/' . $newFileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                        $imageUpdated = true;
                    }
                }
            }
        }

        // Update kueh details
        if ($imageUpdated) {
            $sql_kueh = "UPDATE KUEH SET KUEHNAME = ?, KUEHDESC = ?, FOODTYPECODE = ?, METHODID = ?, POPULARID = ?, ORIGINID = ?, VIDEO = ?, IMAGE = ? WHERE KUEHID = ?";
            $stmt_kueh = mysqli_prepare($condb, $sql_kueh);
            mysqli_stmt_bind_param(
                $stmt_kueh,
                "ssiiiissi",
                $kuehName,
                $kuehDesc,
                $foodTypeCode,
                $methodId,
                $popularId,
                $originId,
                $video,
                $newFileName,
                $kuehID
            );
        } else {
            $sql_kueh = "UPDATE KUEH SET KUEHNAME = ?, KUEHDESC = ?, FOODTYPECODE = ?, METHODID = ?, POPULARID = ?, ORIGINID = ?, VIDEO = ? WHERE KUEHID = ?";
            $stmt_kueh = mysqli_prepare($condb, $sql_kueh);
            mysqli_stmt_bind_param(
                $stmt_kueh,
                "ssiiiisi",
                $kuehName,
                $kuehDesc,
                $foodTypeCode,
                $methodId,
                $popularId,
                $originId,
                $video,
                $kuehID
            );
        }

        if (mysqli_stmt_execute($stmt_kueh)) {
            // Delete existing ingredients and steps
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

            // Insert new ingredients
            if (!empty($ingredients)) {
                $sql_items = "INSERT INTO ITEMS (KUEHID, NAMEITEM) VALUES (?, ?)";
                $stmt_items = mysqli_prepare($condb, $sql_items);

                foreach ($ingredients as $ingredient) {
                    if (!empty(trim($ingredient))) {
                        mysqli_stmt_bind_param($stmt_items, "is", $kuehID, $ingredient);
                        mysqli_stmt_execute($stmt_items);
                    }
                }
                mysqli_stmt_close($stmt_items);
            }

            // Insert new steps
            if (!empty($steps)) {
                $sql_steps = "INSERT INTO STEPS (KUEHID, STEP) VALUES (?, ?)";
                $stmt_steps = mysqli_prepare($condb, $sql_steps);

                foreach ($steps as $step) {
                    if (!empty(trim($step))) {
                        mysqli_stmt_bind_param($stmt_steps, "is", $kuehID, $step);
                        mysqli_stmt_execute($stmt_steps);
                    }
                }
                mysqli_stmt_close($stmt_steps);
            }

            mysqli_commit($condb);
            mysqli_stmt_close($stmt_kueh);

            echo "<script>
                window.location.href = 'userProfile.php?msg=update_success';
            </script>";
        } else {
            echo "<script>alert('Error updating kueh details: " . mysqli_error($condb) . "');</script>";
            mysqli_stmt_close($stmt_kueh);
        }
    }
}

function getOptionsWithIdAndName($query, $idField, $nameField, $selectedValue = null)
{
    global $condb;
    $stid = mysqli_prepare($condb, $query);
    mysqli_stmt_execute($stid);
    $result = mysqli_stmt_get_result($stid);
    $options = "";
    while ($row = mysqli_fetch_assoc($result)) {
        $selected = ($row[$idField] == $selectedValue) ? 'selected' : '';
        $options .= "<option value='{$row[$idField]}' $selected>{$row[$nameField]}</option>";
    }
    mysqli_stmt_close($stid);
    return $options;
}

// Populate dropdowns with existing values
$foodtypeOptions = getOptionsWithIdAndName("SELECT FOODTYPECODE, TYPENAME FROM FOODTYPE", "FOODTYPECODE", "TYPENAME", $existingFoodType ?? null);
$methodOptions = getOptionsWithIdAndName("SELECT METHODID, METHODNAME FROM METHOD", "METHODID", "METHODNAME", $existingMethod ?? null);
$popularOptions = getOptionsWithIdAndName("SELECT POPULARID, LEVEL FROM POPULARITY", "POPULARID", "LEVEL", $existingPopularity ?? null);
$originOptions = getOptionsWithIdAndName("SELECT ORIGINCODE, NAMESTATE FROM ORIGIN", "ORIGINCODE", "NAMESTATE", $existingOrigin ?? null);



if (isset($_SESSION['google_user'])) {
    $username = $_SESSION['google_user']['name'];
    $email = $_SESSION['google_user']['email'];
} else {
    $username = $_SESSION['username'];
    $email = $_SESSION['email'];
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
<BR><BR>
<div class="container w-75">
    <form id="kuehForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
        <!-- Existing image and title section -->
        <div class="row">
            <div class="col-12 col-md-4 my-4" id="imageContainer">
                <!-- Image Preview -->
                <?php
                $imageSrc = 'sources/uploadimage.jpg';
                if (!empty($existingImage)) {
                    $imagePath = 'kueh_images/' . $existingImage;
                    if (file_exists($imagePath)) {
                        $imageSrc = $imagePath;
                    }
                }
                ?>
                <img id="previewImage" src="<?php echo $imageSrc; ?>" class="img-fluid text-center rounded-3" alt="Uploaded Image Preview">
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
                        <?php if (!isset($_SESSION['google_user'])): ?>
                            <img src="sources/profile-icon.png" alt="Profile Picture" class="rounded-circle border" width="50" height="50">
                        <?php else: ?>
                            <img src="<?= $_SESSION['google_user']['picture'] ?>" alt="Profile Picture" class="rounded-circle border" width="50" height="50">
                        <?php endif; ?>
                        <!-- Text -->
                        <div class="ms-3">
                            <h6 class="mb-0"><?php echo htmlspecialchars($username); ?></h6>
                            <small class="text-muted"><?php echo htmlspecialchars($email); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <textarea class="w-100 p-1 border-0 shadow-none" name="kuehDesc" style="background-color: #FFFAF0;" placeholder="Share kisah resepi anda" rows="6"><?php echo isset($kuehData['KUEHDESC']) ? htmlspecialchars($kuehData['KUEHDESC']) : ''; ?></textarea>
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
            <div class="col-12"><br>
                <label for="origin" class="form-label fw-bold">Link Video Rujukan</label><br>
                <input type="url" name="video" class="form-control" value="<?= $existingLink ?>">
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
                <div id="ingredientContainer">
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
                <div id="stepContainer">
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
        <input type="hidden" name="kuehId" value="<?= $_GET['kuehId'] ?>">
        <div class="row mt-3">
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" name="submit" class="btn btn-primary mb-4">Save Recipe</button>
            </div>
        </div>
    </form>
</div>

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

<?php
include('footer.php');
?>