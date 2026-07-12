<?php
include('../includes/header.php');
include('../connection.php');

// Configure Intervention Image
use Intervention\Image\ImageManagerStatic as Image;

Image::configure(['driver' => 'gd']);

if (empty($_SESSION['username'])) {
    echo "<script>
            alert('Sila log masuk untuk mengakses laman ini');
            window.location.href = '../auth/userLogin.php';
          </script>";
    exit;
}

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

    if (!$kuehData) {
        die("Resipi tidak ditemui.");
    }

    // Initialize existing values for dropdowns
    $existingFoodType = $kuehData['FOODTYPECODE'];
    $existingMethod = $kuehData['METHODID'];
    $existingPopularity = $kuehData['POPULARID'];
    $existingOrigin = $kuehData['ORIGINID'];
    $existingLink = $kuehData['VIDEO'];
    $existingImage = $kuehData['IMAGE']; 

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

    if ($kuehID != null) {
        $imageUpdated = false;
        $newFileName = null;

        // Check if a new image is uploaded
        if (!empty($_FILES['image']['tmp_name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'jfif'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB

            $fileSize = $_FILES['image']['size'];
            $fileMime = $_FILES['image']['type'];
            $fileName = basename($_FILES['image']['name']);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (
                $fileSize <= $maxFileSize &&
                in_array($fileMime, $allowedTypes) &&
                in_array($fileExt, $allowedExtensions) &&
                getimagesize($_FILES['image']['tmp_name']) !== false
            ) {
                try {
                    $img = Image::make($_FILES['image']['tmp_name']);
                    
                    if ($img->width() > 1200 || $img->height() > 1200) {
                        $img->resize(1200, 1200, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upSize();
                        });
                    }

                    $newFileName = uniqid('kueh_', true) . '.' . $fileExt;
                    $uploadPath = '../kueh_images/' . $newFileName;

                    if (!file_exists('../kueh_images')) {
                        mkdir('../kueh_images', 0777, true);
                    }

                    $img->save($uploadPath, 85);
                    $imageUpdated = true;

                    // Delete old image file if exists
                    if (!empty($_POST['existingImage'])) {
                        $oldImagePath = '../kueh_images/' . $_POST['existingImage'];
                        if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                } catch (Exception $e) {
                    echo "<script>alert('Ralat semasa memproses imej: " . addslashes($e->getMessage()) . "');</script>";
                }
            }
        }

        // Update KUEH table
        if ($imageUpdated) {
            $sql_update = "UPDATE KUEH SET KUEHNAME = ?, KUEHDESC = ?, FOODTYPECODE = ?, METHODID = ?, POPULARID = ?, ORIGINID = ?, VIDEO = ?, IMAGE = ? WHERE KUEHID = ?";
            $stmt_update = mysqli_prepare($condb, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "ssiiiissi", $kuehName, $kuehDesc, $foodTypeCode, $methodId, $popularId, $originId, $video, $newFileName, $kuehID);
        } else {
            $sql_update = "UPDATE KUEH SET KUEHNAME = ?, KUEHDESC = ?, FOODTYPECODE = ?, METHODID = ?, POPULARID = ?, ORIGINID = ?, VIDEO = ? WHERE KUEHID = ?";
            $stmt_update = mysqli_prepare($condb, $sql_update);
            mysqli_stmt_bind_param($stmt_update, "ssiiiisi", $kuehName, $kuehDesc, $foodTypeCode, $methodId, $popularId, $originId, $video, $kuehID);
        }

        if (mysqli_stmt_execute($stmt_update)) {
            // Delete old ingredients
            $sql_del_items = "DELETE FROM ITEMS WHERE KUEHID = ?";
            $stmt_del_items = mysqli_prepare($condb, $sql_del_items);
            mysqli_stmt_bind_param($stmt_del_items, "i", $kuehID);
            mysqli_stmt_execute($stmt_del_items);
            mysqli_stmt_close($stmt_del_items);

            // Insert new ingredients
            $sql_ins_items = "INSERT INTO ITEMS (KUEHID, NAMEITEM) VALUES (?, ?)";
            $stmt_ins_items = mysqli_prepare($condb, $sql_ins_items);
            foreach ($ingredients as $ingredient) {
                $ingredient = trim($ingredient);
                if (!empty($ingredient)) {
                    mysqli_stmt_bind_param($stmt_ins_items, "is", $kuehID, $ingredient);
                    mysqli_stmt_execute($stmt_ins_items);
                }
            }
            mysqli_stmt_close($stmt_ins_items);

            // Delete old steps
            $sql_del_steps = "DELETE FROM STEPS WHERE KUEHID = ?";
            $stmt_del_steps = mysqli_prepare($condb, $sql_del_steps);
            mysqli_stmt_bind_param($stmt_del_steps, "i", $kuehID);
            mysqli_stmt_execute($stmt_del_steps);
            mysqli_stmt_close($stmt_del_steps);

            // Insert new steps
            $sql_ins_steps = "INSERT INTO STEPS (KUEHID, STEP) VALUES (?, ?)";
            $stmt_ins_steps = mysqli_prepare($condb, $sql_ins_steps);
            foreach ($steps as $step) {
                $step = trim($step);
                if (!empty($step)) {
                    mysqli_stmt_bind_param($stmt_ins_steps, "is", $kuehID, $step);
                    mysqli_stmt_execute($stmt_ins_steps);
                }
            }
            mysqli_stmt_close($stmt_ins_steps);

            echo "<script>
                window.location.href = 'kuehDetails.php?id=" . $kuehID . "';
            </script>";
            exit;
        } else {
            echo "<script>alert('Gagal mengemas kini resipi.');</script>";
        }
        mysqli_stmt_close($stmt_update);
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
    $profile_image = $_SESSION['google_user']['picture'];
} else {
    $username = $_SESSION['username'];
    $email = $_SESSION['email'];
    $profile_image = "https://static.vecteezy.com/system/resources/previews/024/983/914/non_2x/simple-user-default-icon-free-png.png";
}

mysqli_close($condb);
?>

<head>
    <style>
        .publish-card-outer {
            max-width: 1000px;
            width: 100%;
            margin: 2rem auto;
        }
        
        .upload-zone {
            width: 100%;
            height: 320px;
            background-color: var(--color-sidebar-bg);
            border: 1px solid var(--color-border);
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
            position: relative;
            transition: all 0.3s var(--ease-custom);
        }
        .upload-zone:hover {
            border-color: var(--color-accent);
        }
        
        #previewImage {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #imageUpload {
            display: none;
        }

        .dynamic-list-card {
            background-color: var(--color-sidebar-bg);
            border: 1px solid var(--color-border);
            border-radius: 16px;
            padding: 1.5rem;
        }

        /* Draggable Input Items */
        .input-group-item {
            transition: transform 0.2s var(--ease-custom);
        }
        
        .input-group-item.dragging {
            opacity: 0.4;
            transform: scale(0.98);
        }
        
        .drag-handle {
            cursor: grab;
            user-select: none;
        }
        .drag-handle:active {
            cursor: grabbing;
        }

        /* Dynamic Delete Button Animations */
        .delete-button {
            transition: all 0.3s var(--ease-custom);
            transform: scale(1);
            opacity: 1;
            width: 42px;
            padding: 6px 12px;
            overflow: hidden;
        }

        #ingredientContainer .input-group-item:only-child .delete-button,
        #stepContainer .input-group-item:only-child .delete-button {
            transform: scale(0);
            opacity: 0;
            width: 0;
            padding: 0;
            margin: 0;
            border: none;
            pointer-events: none;
        }
    </style>
</head>

<div class="publish-card-outer double-bezel-outer">
    <div class="double-bezel-inner" style="padding: 2.5rem;">
        
        <div class="mb-4 text-center text-md-start">
            <h2 class="font-serif fw-bold display-6 mb-2" style="font-family: var(--font-serif) !important;">Sunting Resipi</h2>
            <p class="text-muted font-sans" style="font-size: 0.95rem;">Kemaskini maklumat resipi warisan anda secara terus.</p>
        </div>

        <form id="kuehForm" method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '?kuehId=' . $kuehId; ?>" enctype="multipart/form-data">
            <input type="hidden" name="kuehId" value="<?php echo htmlspecialchars($kuehId); ?>">
            <input type="hidden" name="existingImage" value="<?php echo htmlspecialchars($existingImage); ?>">
            
            <!-- Title & Basic Info -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-5">
                    <!-- Image Upload Zone -->
                    <label for="imageUpload" class="upload-zone">
                        <?php
                        $imageSrc = '../sources/default-kueh.jpg';
                        if (!empty($existingImage)) {
                            $imagePath = '../kueh_images/' . $existingImage;
                            if (file_exists($imagePath)) {
                                $imageSrc = $imagePath;
                            }
                        }
                        ?>
                        <img id="previewImage" src="<?php echo htmlspecialchars($imageSrc); ?>" alt="Recipe Preview">
                        <input type="file" name="image" id="imageUpload" accept="image/*">
                    </label>
                </div>
                
                <div class="col-12 col-md-7 d-flex flex-column gap-3">
                    <!-- Recipe Title -->
                    <div>
                        <label for="kuehName" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Nama Kueh</label>
                        <input type="text" name="kuehName" id="kuehName" class="form-control rounded-pill px-3 py-2.5" placeholder="Tajuk: Cth. Kuih Lapis Pandan Atok" value="<?php echo isset($kuehData['KUEHNAME']) ? htmlspecialchars($kuehData['KUEHNAME']) : ''; ?>" required>
                    </div>

                    <!-- Creator info line -->
                    <div class="d-flex align-items-center gap-3 my-1">
                        <img src="<?php echo htmlspecialchars($profile_image); ?>"
                             alt="Profile Picture"
                             class="rounded-circle border"
                             width="42" height="42"
                             style="object-fit: cover;"
                             onerror="this.src='https://static.vecteezy.com/system/resources/previews/024/983/914/non_2x/simple-user-default-icon-free-png.png';">
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-size: 0.9rem; color: var(--color-text);"><?= htmlspecialchars($username) ?></h6>
                            <small class="text-muted" style="font-size: 0.8rem;"><?= htmlspecialchars($email) ?></small>
                        </div>
                    </div>
                    
                    <!-- Recipe Story Description -->
                    <div>
                        <label for="kuehDesc" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Kisah & Deskripsi Resipi</label>
                        <textarea name="kuehDesc" id="kuehDesc" class="form-control" rows="4" style="border-radius: 16px;" placeholder="Kongsi kisah resepi warisan ini..." required><?php echo isset($kuehData['KUEHDESC']) ? htmlspecialchars($kuehData['KUEHDESC']) : ''; ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Recipe Metadata Dropdowns Grid -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="foodtype" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Jenis Makanan</label>
                    <select name="foodtype" id="foodtype" class="form-select rounded-pill px-3" required>
                        <option value="" disabled>Pilih Jenis</option>
                        <?= $foodtypeOptions ?>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="method" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Cara Masakan</label>
                    <select name="method" id="method" class="form-select rounded-pill px-3" required>
                        <option value="" disabled>Pilih Cara</option>
                        <?= $methodOptions ?>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="popular" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Populariti</label>
                    <select name="popular" id="popular" class="form-select rounded-pill px-3" required>
                        <option value="" disabled>Pilih Populariti</option>
                        <?= $popularOptions ?>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="origin" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Negeri Asal</label>
                    <select name="origin" id="origin" class="form-select rounded-pill px-3" required>
                        <option value="" disabled>Pilih Negeri</option>
                        <?= $originOptions ?>
                    </select>
                </div>
                
                <div class="col-12">
                    <label for="video" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Pautan Video Rujukan (YouTube)</label>
                    <input type="url" name="video" id="video" class="form-control rounded-pill px-3" placeholder="https://www.youtube.com/watch?v=..." value="<?php echo isset($kuehData['VIDEO']) ? htmlspecialchars($kuehData['VIDEO']) : ''; ?>">
                </div>
            </div>

            <!-- Dynamic Lists: Ingredients and Steps -->
            <div class="row g-4">
                <!-- Ingredients Panel -->
                <div class="col-12 col-md-5 col-lg-4">
                    <div class="dynamic-list-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="font-serif fw-bold mb-0" style="font-size: 1.25rem;">Ramuan</h4>
                            <button type="button" class="btn btn-sm btn-success rounded-circle shadow-sm" id="addIngredientButton" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                        <div id="ingredientContainer" class="d-flex flex-column gap-2">
                            <?php foreach ($ingredients as $ingredient): ?>
                                <div class="input-group-item">
                                    <div class="input-group mb-2">
                                        <span class="drag-handle input-group-text bg-transparent border-0"><i class="bi bi-justify"></i></span>
                                        <input type="text" name="ingredients[]" class="form-control" placeholder="Cth: Bahan..." value="<?php echo htmlspecialchars($ingredient); ?>" required>
                                        <button type="button" class="btn btn-outline-danger border-0 delete-button"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($ingredients)): ?>
                                <div class="input-group-item">
                                    <div class="input-group mb-2">
                                        <span class="drag-handle input-group-text bg-transparent border-0"><i class="bi bi-justify"></i></span>
                                        <input type="text" name="ingredients[]" class="form-control" placeholder="Cth: 500g Tepung Beras" required>
                                        <button type="button" class="btn btn-outline-danger border-0 delete-button"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Steps Panel -->
                <div class="col-12 col-md-7 col-lg-8">
                    <div class="dynamic-list-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="font-serif fw-bold mb-0" style="font-size: 1.25rem;">Langkah Penyediaan</h4>
                            <button type="button" class="btn btn-sm btn-success rounded-circle shadow-sm" id="addStepButton" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                        <div id="stepContainer" class="d-flex flex-column gap-2">
                            <?php foreach ($steps as $step): ?>
                                <div class="input-group-item">
                                    <div class="input-group mb-2">
                                        <span class="drag-handle input-group-text bg-transparent border-0"><i class="bi bi-justify"></i></span>
                                        <input type="text" name="steps[]" class="form-control" placeholder="Cth: Langkah..." value="<?php echo htmlspecialchars($step); ?>" required>
                                        <button type="button" class="btn btn-outline-danger border-0 delete-button"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($steps)): ?>
                                <div class="input-group-item">
                                    <div class="input-group mb-2">
                                        <span class="drag-handle input-group-text bg-transparent border-0"><i class="bi bi-hash"></i></span>
                                        <input type="text" name="steps[]" class="form-control" placeholder="Cth: Langkah pertama..." required>
                                        <button type="button" class="btn btn-outline-danger border-0 delete-button"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Submission -->
            <div class="d-flex justify-content-end gap-3 mt-5">
                <button type="submit" name="submit" class="btn btn-primary-custom px-5 py-2.5 justify-content-center">
                    <span>Simpan Perubahan</span>
                    <div class="icon-wrapper">
                        <i class="bi bi-check-lg"></i>
                    </div>
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Page Closing Tags (Backwards-compatibility for 5-div open layout) -->
</main> <!-- content-body -->
</div> <!-- content-wrapper-compat-4 -->
</div> <!-- content-wrapper-compat-3 -->
</div> <!-- main-layout -->
</div> <!-- app-container -->

<?php include('../includes/footer.php'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageUpload = document.getElementById('imageUpload');
        const previewImage = document.getElementById('previewImage');
        
        // Image preview listener
        if (imageUpload) {
            imageUpload.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Use event delegation for delete button clicks
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.delete-button');
            if (btn) {
                btn.closest('.input-group-item').remove();
            }
        });

        // Add ingredient row
        document.getElementById('addIngredientButton').addEventListener('click', function() {
            const container = document.getElementById('ingredientContainer');
            const newInput = document.createElement('div');
            newInput.className = 'input-group-item';
            newInput.innerHTML = `
                <div class="input-group mb-2">
                    <span class="drag-handle input-group-text bg-transparent border-0"><i class="bi bi-justify"></i></span>
                    <input type="text" name="ingredients[]" class="form-control" placeholder="Cth: Bahan tambahan..." required>
                    <button type="button" class="btn btn-outline-danger border-0 delete-button"><i class="bi bi-trash"></i></button>
                </div>
            `;
            container.appendChild(newInput);
        });

        // Add step row
        document.getElementById('addStepButton').addEventListener('click', function() {
            const container = document.getElementById('stepContainer');
            const newInput = document.createElement('div');
            newInput.className = 'input-group-item';
            newInput.innerHTML = `
                <div class="input-group mb-2">
                    <span class="drag-handle input-group-text bg-transparent border-0"><i class="bi bi-justify"></i></span>
                    <input type="text" name="steps[]" class="form-control" placeholder="Cth: Langkah seterusnya..." required>
                    <button type="button" class="btn btn-outline-danger border-0 delete-button"><i class="bi bi-trash"></i></button>
                </div>
            `;
            container.appendChild(newInput);
        });

        // Sortable lists implementation
        function makeSortable(containerId) {
            const container = document.getElementById(containerId);
            let dragSource = null;

            // Enable dragging only on handle mousedown
            container.addEventListener('mousedown', function(e) {
                const handle = e.target.closest('.drag-handle');
                if (handle) {
                    const row = handle.closest('.input-group-item');
                    if (row) {
                        row.setAttribute('draggable', 'true');
                    }
                }
            });

            container.addEventListener('mouseup', function(e) {
                const row = e.target.closest('.input-group-item');
                if (row) {
                    row.removeAttribute('draggable');
                }
            });

            container.addEventListener('dragstart', function(e) {
                const target = e.target.closest('.input-group-item');
                if (!target || target.getAttribute('draggable') !== 'true') {
                    e.preventDefault();
                    return;
                }
                dragSource = target;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', target.innerHTML);
                target.classList.add('dragging');
            });

            container.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                const target = e.target.closest('.input-group-item');
                if (target && target !== dragSource) {
                    const bounding = target.getBoundingClientRect();
                    const offset = e.clientY - bounding.top - (bounding.height / 2);
                    if (offset > 0) {
                        target.after(dragSource);
                    } else {
                        target.before(dragSource);
                    }
                }
            });

            container.addEventListener('dragend', function(e) {
                container.querySelectorAll('.input-group-item').forEach(item => {
                    item.classList.remove('dragging');
                    item.removeAttribute('draggable');
                });
                dragSource = null;
            });
        }

        makeSortable('ingredientContainer');
        makeSortable('stepContainer');
    });
</script>
</body>
</html>