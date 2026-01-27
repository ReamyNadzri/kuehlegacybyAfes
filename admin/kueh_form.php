<?PHP
include('header_admin.php');
include('connection.php');

// Add Intervention Image configuration
use Intervention\Image\ImageManagerStatic as Image;

Image::configure(['driver' => 'gd']);

// Process form submission
if (isset($_POST['submit'])) {
    // Check if an image is uploaded
    if (empty($_FILES['image']['tmp_name'])) {
        // Show SweetAlert2 toast if no image is uploaded
        echo "<script>
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'error',
                title: 'Please Insert Image',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        </script>";
    } else {
        // Process form data if image is uploaded
        $kuehName = $_POST['kuehName'];
        $kuehDesc = $_POST['kuehDesc'];
        $foodTypeCode = $_POST['foodtype'];
        $methodId = $_POST['method'];
        $popularId = $_POST['popular'];
        $originId = $_POST['origin'];
        $video = $_POST['video'];
        $ingredients = $_POST['ingredients'] ?? [];
        $steps = $_POST['steps'] ?? [];

        // File upload validation
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'jfif'];
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $file_mime = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];

        if (!in_array($file_ext, $allowed_extensions)) {
            die("<script>alert('Invalid file extension. Allowed: jpg, jpeg, png, gif, jfif');</script>");
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

        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $file_ext;
        $targetDir = '../kueh_images/';
        $targetPath = $targetDir . $filename;

        // Optimize and save image using Intervention Image
        $img = Image::make($_FILES['image']['tmp_name']);
        if ($img->width() > 1920) {
            $img->resize(1920, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        $img->save($targetPath, 80);

        // Insert into KUEH table
        $sql_kueh = "INSERT INTO KUEH (KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, VIDEO, POPULARID, ORIGINID, IMAGE, USERNAME) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $laksana_sql_kueh = mysqli_prepare($condb, $sql_kueh);

        // Bind parameters
        mysqli_stmt_bind_param($laksana_sql_kueh, "sssississ", $kuehName, $kuehDesc, $foodTypeCode, $methodId, $video, $popularId, $originId, $filename, $_SESSION['adminid']);

        // Execute the query
        if (mysqli_stmt_execute($laksana_sql_kueh)) {
            // Get the last inserted KUEHID
            $kuehId = mysqli_insert_id($condb);

            // Insert ingredients into ITEMS table
            if (!empty($ingredients)) {
                $sql_items = "INSERT INTO ITEMS (KUEHID, NAMEITEM) VALUES (?, ?)";
                $laksana_sql_items = mysqli_prepare($condb, $sql_items);

                foreach ($ingredients as $ingredient) {
                    mysqli_stmt_bind_param($laksana_sql_items, "is", $kuehId, $ingredient);
                    if (!mysqli_stmt_execute($laksana_sql_items)) {
                        echo "Error inserting ingredient: " . htmlentities(mysqli_error($condb));
                    }
                }
                mysqli_stmt_close($laksana_sql_items);
            }

            // Insert steps into STEPS table
            if (!empty($steps)) {
                $sql_steps = "INSERT INTO STEPS (KUEHID, STEP) VALUES (?, ?)";
                $laksana_sql_steps = mysqli_prepare($condb, $sql_steps);

                foreach ($steps as $step) {
                    mysqli_stmt_bind_param($laksana_sql_steps, "is", $kuehId, $step);
                    if (!mysqli_stmt_execute($laksana_sql_steps)) {
                        echo "Error inserting step: " . htmlentities(mysqli_error($condb));
                    }
                }
                mysqli_stmt_close($laksana_sql_steps);
            }

            echo "<script>
                window.location.href = 'kueh_info.php?msg=add_success';
            </script>";
            exit();
        } else {
            echo "<script>alert('Error saving kueh details: " . htmlentities(mysqli_error($condb)) . "');</script>";
        }

        // Free resources
        mysqli_stmt_close($laksana_sql_kueh);
    }
}




function getOptionsWithIdAndName($query, $idField, $nameField)
{
    global $condb;
    $stmt = mysqli_prepare($condb, $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $options = "";
    while ($row = mysqli_fetch_assoc($result)) {
        $options .= "<option value='{$row[$idField]}'>{$row[$nameField]}</option>";
    }
    mysqli_stmt_close($stmt);
    return $options;
}

// Populate dropdowns
$foodtypeOptions = getOptionsWithIdAndName("SELECT FOODTYPECODE, TYPENAME FROM FOODTYPE", "FOODTYPECODE", "TYPENAME");
$methodOptions = getOptionsWithIdAndName("SELECT METHODID, METHODNAME FROM METHOD", "METHODID", "METHODNAME");
$popularOptions = getOptionsWithIdAndName("SELECT POPULARID, LEVEL FROM POPULARITY", "POPULARID", "LEVEL");
$originOptions = getOptionsWithIdAndName("SELECT ORIGINCODE, NAMESTATE FROM ORIGIN", "ORIGINCODE", "NAMESTATE");

if (isset($_SESSION['adminid'])) {
    $adminId = $_SESSION['adminid'];
    $sql = "SELECT USERNAME, EMAIL, IMAGE FROM admin WHERE USERNAME = ?";
    $stmt = mysqli_prepare($condb, $sql);
    mysqli_stmt_bind_param($stmt, "s", $adminId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $adminData = mysqli_fetch_assoc($result);

    if ($adminData) {
        $username = $adminData['USERNAME'];
        $email = $adminData['EMAIL'];
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($condb);
?>

<body class="" style="background-color: #FFFAF0;">

    <link rel="stylesheet" href="style.css">
    <title>Legacy Kueh System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
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

        /* Fixed size for the image container */
        #imageContainer {
            width: 300px;
            /* Fixed width */
            height: 300px;
            /* Fixed height */
            cursor: pointer;
            overflow: hidden;
            /* Optional: Add a border for better visibility */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Fixed size for the image */
        #previewImage {
            width: 100%;
            /* Make the image fill the container */
            height: 100%;
            /* Make the image fill the container */
            object-fit: cover;
            /* Ensure the image covers the container without distortion */
        }

        /* Hide the file input */
        #imageUpload {
            display: none;
        }

        /* Add a pointer cursor to the image container */
        #imageContainer {
            cursor: pointer;
        }
    </style>


    <!--CONTENT START HERE-->
    <div class="container w-75">
        <form id="kuehForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" name="submit" class="btn btn-primary mb-4">Terbitkan</button>
            </div>
            <!-- Existing image and title section -->
            <div class="row">
                <div class="col-12 col-md-4 my-4" id="imageContainer">
                    <!-- Image Preview -->
                    <img id="previewImage"
                        src="sources/kueh_default.png"
                        class="img-fluid text-center rounded-3"
                        alt="Uploaded Image Preview">

                    <!-- Hidden File Input -->
                    <input type="file"
                        name="image"
                        id="imageUpload"
                        accept="image/*">
                </div>
                <div class="col-12 col-md gy-4">
                    <div class="col-12 bg-primary">
                        <input class="w-100 p-1 border-0 shadow-none fw-bolder fs-2" style="background-color: #FFFAF0;" type="text" name="kuehName" placeholder="Tajuk: Kuih Lapis Atok" required>
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
                        <textarea class="w-100 p-1 border-0 shadow-none" name="kuehDesc" style="background-color: #FFFAF0;" placeholder="Share kisah resepi anda" rows="6" required></textarea>
                    </div>
                </div>
            </div>

            <!-- Dropdown Section -->
            <div class="row mb-4 gy-2">
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="foodtype" class="form-label fw-bold">Jenis makanan</label>
                    <select name="foodtype" id="foodtype" class="form-select" required>
                        <option value="" disabled selected>Pilih Jenis</option>
                        <?= $foodtypeOptions ?>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="method" class="form-label fw-bold">Cara Masakan</label>
                    <select name="method" id="method" class="form-select" required>
                        <option value="" disabled selected>Pilih Cara Masakan</option>
                        <?= $methodOptions ?>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="popular" class="form-label fw-bold">Populariti</label>
                    <select name="popular" id="popular" class="form-select" required>
                        <option value="" disabled selected>Pilih Populariti</option>
                        <?= $popularOptions ?>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="origin" class="form-label fw-bold">Negeri Asal</label>
                    <select name="origin" id="origin" class="form-select" required>
                        <option value="" disabled selected>Pilih Negeri Asal</option>
                        <?= $originOptions ?>
                    </select>
                </div>
                <div class="col-12">
                    <label for="origin" class="form-label fw-bold">Link Video Rujukan</label><br>
                    <input type="url" name="video" class="form-control" required>
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
                        <div class="input-group mb-2">
                            <button type="button" class="btn btn-outline-secondary border-0"><i class="bi bi-justify"></i></button>
                            <input type="text" name="ingredients[]" class="form-control" required>
                        </div>
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
                        <div class="input-group mb-2">
                            <button type="button" class="btn btn-outline-secondary border-0"><i class="bi bi-justify"></i></button>
                            <input type="text" name="steps[]" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div><br><br>

    <script>
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

            newInput.querySelector('.delete-button').addEventListener('click', function() {
                newInput.remove();
            });
        });

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

            newInput.querySelector('.delete-button').addEventListener('click', function() {
                newInput.remove();
            });
        });

        // Add event listeners to existing delete buttons
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                button.closest('.input-group').remove();
            });
        });

        document.getElementById('imageUpload').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                };
                reader.readAsDataURL(file); // Convert image to base64 for preview
            }
        });

        // Trigger the file input when the image container is clicked
        document.getElementById('imageContainer').addEventListener('click', function() {
            document.getElementById('imageUpload').click();
        });

        // Update the image preview when a file is selected
        document.getElementById('imageUpload').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                };
                reader.readAsDataURL(file); // Convert image to base64 for preview
            }
        });

        document.getElementById('kuehForm').addEventListener('submit', function(event) {

            // Check if an image is uploaded
            const imageInput = document.getElementById('imageUpload');
            if (!imageInput.files || imageInput.files.length === 0) {
                // Prevent the form from submitting if no image is uploaded
                event.preventDefault();

                // Show SweetAlert2 toast if no image is uploaded
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
            } else {
                // If an image is uploaded, submit the form
                this.submit();
            }
        });
    </script>
</body>