<?PHP
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

if (isset($_SESSION['google_user'])) {
    $user = [
        "userName" => $_SESSION['google_user']['name'],
        "email" => $_SESSION['google_user']['email'],
        "profile_image" => $_SESSION['google_user']['picture']
    ];
} else {
    $user = [
        "userName" => $_SESSION['username'],
        "email" => $_SESSION['email'],
        "profile_image" => "https://static.vecteezy.com/system/resources/previews/024/983/914/non_2x/simple-user-default-icon-free-png.png"
    ];
    // Fetch actual uploader image from DB if exists
    $userQuery = "SELECT IMAGE FROM USERS WHERE USERNAME = ?";
    $userStmt = mysqli_prepare($condb, $userQuery);
    mysqli_stmt_bind_param($userStmt, 's', $_SESSION['username']);
    mysqli_stmt_execute($userStmt);
    $userResult = mysqli_stmt_get_result($userStmt);
    if ($userRow = mysqli_fetch_assoc($userResult)) {
        if (!empty($userRow['IMAGE'])) {
            $user['profile_image'] = $userRow['IMAGE'];
        }
    }
    mysqli_stmt_close($userStmt);
}

// Process form submission
if (isset($_POST['submit'])) {
    $kuehName = $_POST['kuehName'];
    $kuehDesc = $_POST['kuehDesc'];
    $foodTypeCode = $_POST['foodtype'];
    $methodId = $_POST['method'];
    $popularId = $_POST['popular'];
    $originId = $_POST['origin'];
    $video = $_POST['video'];
    $ingredients = $_POST['ingredients'] ?? [];
    $steps = $_POST['steps'] ?? [];

    // Validate and process uploaded image
    if (!empty($_FILES['image']['tmp_name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'jfif'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        $fileSize = $_FILES['image']['size'];
        $fileMime = $_FILES['image']['type'];
        $fileName = basename($_FILES['image']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileSize > $maxFileSize) {
            echo "<script>
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'Saiz fail melebihi had 5MB',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            </script>";
        } elseif (!in_array($fileMime, $allowedTypes) || !in_array($fileExt, $allowedExtensions)) {
            echo "<script>
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'Format fail tidak sah. Hanya JPG, PNG, GIF dibenarkan',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            </script>";
        } else {
            // Process and optimize image
            try {
                $img = Image::make($_FILES['image']['tmp_name']);

                // Resize if larger than 1200px width/height
                if ($img->width() > 1200 || $img->height() > 1200) {
                    $img->resize(1200, 1200, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upSize();
                    });
                }

                // Generate unique filename
                $newFileName = uniqid('kueh_', true) . '.' . $fileExt;
                $uploadPath = '../kueh_images/' . $newFileName;

                // Ensure upload directory exists
                if (!file_exists('../kueh_images')) {
                    mkdir('../kueh_images', 0777, true);
                }

                // Save optimized image
                $img->save($uploadPath, 85);

                // Insert into KUEH table
                $username_to_insert = $_SESSION['username'];
                $sql = "INSERT INTO KUEH (KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, POPULARID, ORIGINID, VIDEO, IMAGE, USERNAME) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = mysqli_prepare($condb, $sql);
                mysqli_stmt_bind_param($stmt, "ssiiiisss", $kuehName, $kuehDesc, $foodTypeCode, $methodId, $popularId, $originId, $video, $newFileName, $username_to_insert);
                
                if (mysqli_stmt_execute($stmt)) {
                    $newKuehId = mysqli_insert_id($condb);
                    
                    // Insert ingredients
                    $sql_item = "INSERT INTO ITEMS (KUEHID, NAMEITEM) VALUES (?, ?)";
                    $stmt_item = mysqli_prepare($condb, $sql_item);
                    foreach ($ingredients as $ingredient) {
                        $ingredient = trim($ingredient);
                        if (!empty($ingredient)) {
                            mysqli_stmt_bind_param($stmt_item, "is", $newKuehId, $ingredient);
                            mysqli_stmt_execute($stmt_item);
                        }
                    }
                    mysqli_stmt_close($stmt_item);

                    // Insert steps
                    $sql_step = "INSERT INTO STEPS (KUEHID, STEP) VALUES (?, ?)";
                    $stmt_step = mysqli_prepare($condb, $sql_step);
                    foreach ($steps as $step) {
                        $step = trim($step);
                        if (!empty($step)) {
                            mysqli_stmt_bind_param($stmt_step, "is", $newKuehId, $step);
                            mysqli_stmt_execute($stmt_step);
                        }
                    }
                    mysqli_stmt_close($stmt_step);

                    echo "<script>
                        window.location.href = 'kuehDetails.php?id=" . $newKuehId . "';
                    </script>";
                    exit;
                } else {
                    echo "<script>alert('Gagal memasukkan resipi ke pangkalan data.');</script>";
                }
                mysqli_stmt_close($stmt);
            } catch (Exception $e) {
                echo "<script>alert('Ralat semasa memproses imej: " . addslashes($e->getMessage()) . "');</script>";
            }
        }
    } else {
        echo "<script>
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'error',
                title: 'Sila masukkan gambar resipi',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        </script>";
    }
}

function getOptionsWithIdAndName($query, $idField, $nameField)
{
    global $condb;
    $stid = mysqli_prepare($condb, $query);
    mysqli_stmt_execute($stid);
    $result = mysqli_stmt_get_result($stid);
    $options = "";
    while ($row = mysqli_fetch_assoc($result)) {
        $options .= "<option value='{$row[$idField]}'>{$row[$nameField]}</option>";
    }
    mysqli_stmt_close($stid);
    return $options;
}

// Populate dropdowns
$foodtypeOptions = getOptionsWithIdAndName("SELECT FOODTYPECODE, TYPENAME FROM FOODTYPE", "FOODTYPECODE", "TYPENAME");
$methodOptions = getOptionsWithIdAndName("SELECT METHODID, METHODNAME FROM METHOD", "METHODID", "METHODNAME");
$popularOptions = getOptionsWithIdAndName("SELECT POPULARID, LEVEL FROM POPULARITY", "POPULARID", "LEVEL");
$originOptions = getOptionsWithIdAndName("SELECT ORIGINCODE, NAMESTATE FROM ORIGIN", "ORIGINCODE", "NAMESTATE");

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
            border: 2px dashed var(--color-border);
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
            background-color: var(--color-surface);
        }
        
        #previewImage {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .upload-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: var(--color-text-muted);
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
            <h2 class="font-serif fw-bold display-6 mb-2" style="font-family: var(--font-serif) !important;">Kongsi Resipi Baharu</h2>
            <p class="text-muted font-sans" style="font-size: 0.95rem;">Muat naik gambar, isi ramuan, dan kongsikan warisan masakan anda kepada komuniti.</p>
        </div>

        <form id="kuehForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            
            <!-- Title & Basic Info -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-5">
                    <!-- Image Upload Zone -->
                    <label for="imageUpload" class="upload-zone">
                        <img id="previewImage" src="" alt="Recipe Preview">
                        <div class="upload-placeholder">
                            <i class="bi bi-cloud-arrow-up text-success fs-1"></i>
                            <span class="fw-bold">Muat Naik Gambar</span>
                            <span style="font-size: 0.75rem;">Format JPG, PNG atau GIF (Max 5MB)</span>
                        </div>
                        <input type="file" name="image" id="imageUpload" accept="image/*" required>
                    </label>
                </div>
                
                <div class="col-12 col-md-7 d-flex flex-column gap-3">
                    <!-- Recipe Title -->
                    <div>
                        <label for="kuehName" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Nama Kueh</label>
                        <input type="text" name="kuehName" id="kuehName" class="form-control rounded-pill px-3 py-2.5" placeholder="Tajuk: Cth. Kuih Lapis Pandan Atok" required>
                    </div>

                    <!-- Creator info line -->
                    <div class="d-flex align-items-center gap-3 my-1">
                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>"
                             alt="Profile Picture"
                             class="rounded-circle border"
                             width="42" height="42"
                             style="object-fit: cover;"
                             onerror="this.src='https://static.vecteezy.com/system/resources/previews/024/983/914/non_2x/simple-user-default-icon-free-png.png';">
                        <div>
                            <h6 class="mb-0 fw-bold" style="font-size: 0.9rem; color: var(--color-text);"><?= htmlspecialchars($user['userName']) ?></h6>
                            <small class="text-muted" style="font-size: 0.8rem;"><?= htmlspecialchars($user['email']) ?></small>
                        </div>
                    </div>
                    
                    <!-- Recipe Story Description -->
                    <div>
                        <label for="kuehDesc" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Kisah & Deskripsi Resipi</label>
                        <textarea name="kuehDesc" id="kuehDesc" class="form-control" rows="4" style="border-radius: 16px;" placeholder="Kongsi sedikit kisah di sebalik resipi ini, tip istimewa atau cara hidangan..." required></textarea>
                    </div>
                </div>
            </div>

            <!-- Recipe Metadata Dropdowns Grid -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="foodtype" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Jenis Makanan</label>
                    <select name="foodtype" id="foodtype" class="form-select rounded-pill px-3" required>
                        <option value="" disabled selected>Pilih Jenis</option>
                        <?= $foodtypeOptions ?>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="method" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Cara Masakan</label>
                    <select name="method" id="method" class="form-select rounded-pill px-3" required>
                        <option value="" disabled selected>Pilih Cara</option>
                        <?= $methodOptions ?>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="popular" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Populariti</label>
                    <select name="popular" id="popular" class="form-select rounded-pill px-3" required>
                        <option value="" disabled selected>Pilih Populariti</option>
                        <?= $popularOptions ?>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="origin" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Negeri Asal</label>
                    <select name="origin" id="origin" class="form-select rounded-pill px-3" required>
                        <option value="" disabled selected>Pilih Negeri</option>
                        <?= $originOptions ?>
                    </select>
                </div>
                
                <div class="col-12">
                    <label for="video" class="form-label font-mono text-uppercase text-muted" style="font-size: 0.75rem;">Pautan Video Rujukan (YouTube)</label>
                    <input type="url" name="video" id="video" class="form-control rounded-pill px-3" placeholder="https://www.youtube.com/watch?v=...">
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
                            <div class="input-group-item">
                                <div class="input-group">
                                    <span class="drag-handle input-group-text bg-transparent border-0"><i class="bi bi-justify"></i></span>
                                    <input type="text" name="ingredients[]" class="form-control" placeholder="Cth: 400g Beras Pulut" required>
                                    <button type="button" class="btn btn-outline-danger border-0 delete-button"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
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
                            <div class="input-group-item">
                                <div class="input-group">
                                    <span class="drag-handle input-group-text bg-transparent border-0"><i class="bi bi-justify"></i></span>
                                    <input type="text" name="steps[]" class="form-control" placeholder="Cth: Rendam beras pulut 4 jam..." required>
                                    <button type="button" class="btn btn-outline-danger border-0 delete-button"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Submission -->
            <div class="d-flex justify-content-end gap-3 mt-5">
                <button type="submit" name="submit" class="btn btn-primary-custom px-5 py-2.5 justify-content-center">
                    <span>Terbitkan Resipi</span>
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
        const uploadPlaceholder = document.querySelector('.upload-placeholder');
        
        // Image preview listener
        if (imageUpload) {
            imageUpload.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.style.display = 'block';
                        uploadPlaceholder.style.display = 'none';
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

        // Form submit validator
        document.getElementById('kuehForm').addEventListener('submit', function(event) {
            const file = imageUpload.files;
            if (!file || file.length === 0) {
                event.preventDefault();
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: 'Sila masukkan gambar resipi',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            }
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