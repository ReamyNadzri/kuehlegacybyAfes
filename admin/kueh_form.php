<?PHP
include('header_admin.php');
include('connection.php');
// Process form submission
if (isset($_POST['submit'])) {
    // Process form data
    $kuehName = $_POST['kuehName'];
    $kuehDesc = $_POST['kuehDesc'];
    $foodTypeCode = $_POST['foodtype'];
    $methodId = $_POST['method'];
    $popularId = $_POST['popular'];
    $originId = $_POST['origin'];
    $ingredients = $_POST['ingredients'] ?? [];
    $steps = $_POST['steps'] ?? [];

    // Validate uploaded image
    if (!empty($_FILES['image']['tmp_name'])) {
        // Initialize BLOB descriptor
        $lob = oci_new_descriptor($condb, OCI_D_LOB);

        // Read the image file
        $imageData = file_get_contents($_FILES['image']['tmp_name']);

        // Insert into KUEH table
        $sql_kueh = "INSERT INTO KUEH (KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, POPULARID, ORIGINID, IMAGE, USERNAME) 
                     VALUES (:kuehName, :kuehDesc, :foodTypeCode, :methodId, :popularId, :originId, EMPTY_BLOB(), :username)
                     RETURNING IMAGE INTO :image";
        $laksana_sql_kueh = oci_parse($condb, $sql_kueh);

        // Bind parameters
        oci_bind_by_name($laksana_sql_kueh, ":kuehName", $kuehName);
        oci_bind_by_name($laksana_sql_kueh, ":kuehDesc", $kuehDesc);
        oci_bind_by_name($laksana_sql_kueh, ":foodTypeCode", $foodTypeCode);
        oci_bind_by_name($laksana_sql_kueh, ":methodId", $methodId);
        oci_bind_by_name($laksana_sql_kueh, ":popularId", $popularId);
        oci_bind_by_name($laksana_sql_kueh, ":originId", $originId);
        oci_bind_by_name($laksana_sql_kueh, ":image", $lob, -1, SQLT_BLOB);
        oci_bind_by_name($laksana_sql_kueh, ":username", $_SESSION['adminid']);

        // Execute the query
        if (oci_execute($laksana_sql_kueh, OCI_DEFAULT)) {
            // Save the image data into the BLOB
            $lob->save($imageData);

            // Commit the transaction
            oci_commit($condb);

            // Get the last inserted KUEHID
            $sql_get_kuehid = "SELECT MAX(KUEHID) AS KUEHID FROM KUEH";
            $laksana_sql_get_kuehid = oci_parse($condb, $sql_get_kuehid);
            oci_execute($laksana_sql_get_kuehid);
            $kuehIdResult = oci_fetch_assoc($laksana_sql_get_kuehid);
            $kuehId = $kuehIdResult['KUEHID'];


            // Insert ingredients into ITEMS table
            if (!empty($ingredients)) {
                $sql_items = "INSERT INTO ITEMS (KUEHID, NAMEITEM) VALUES (:kuehId, :nameitem)";
                $laksana_sql_items = oci_parse($condb, $sql_items);

                foreach ($ingredients as $ingredient) {
                    oci_bind_by_name($laksana_sql_items, ":kuehId", $kuehId);
                    oci_bind_by_name($laksana_sql_items, ":nameitem", $ingredient);
                    if (!oci_execute($laksana_sql_items)) {
                        $e = oci_error($laksana_sql_items);
                        echo "Error inserting ingredient: " . htmlentities($e['message']);
                    }
                }
                oci_free_statement($laksana_sql_items);
            }

            // Insert steps into STEPS table
            if (!empty($steps)) {
                $sql_steps = "INSERT INTO STEPS (KUEHID, STEP) VALUES (:kuehId, :step)";
                $laksana_sql_steps = oci_parse($condb, $sql_steps);

                foreach ($steps as $step) {
                    oci_bind_by_name($laksana_sql_steps, ":kuehId", $kuehId);
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
                window.location.href = 'kueh_info.php?msg=add_success';
            </script>";
            exit();
        } else {
            $e = oci_error($laksana_sql_kueh);
            echo "<script>alert('Error saving kueh details: " . htmlentities($e['message']) . "');</script>";
        }

        // Free the BLOB descriptor
        $lob->free();

        // Free resources
        oci_free_statement($laksana_sql_kueh);
        if (isset($laksana_sql_get_kuehid)) {
            oci_free_statement($laksana_sql_get_kuehid);
        }
    } else {
        echo "<script>alert('Please Upload Image');</script>";
    }
}



function getOptionsWithIdAndName($query, $idField, $nameField)
{
    global $condb;
    $stid = oci_parse($condb, $query);
    oci_execute($stid);
    $options = "";
    while ($row = oci_fetch_assoc($stid)) {
        $options .= "<option value='{$row[$idField]}'>{$row[$nameField]}</option>";
    }
    oci_free_statement($stid);
    return $options;
}

// Populate dropdowns
$foodtypeOptions = getOptionsWithIdAndName("SELECT FOODTYPECODE, TYPENAME FROM FOODTYPE", "FOODTYPECODE", "TYPENAME");
$methodOptions = getOptionsWithIdAndName("SELECT METHODID, METHODNAME FROM METHOD", "METHODID", "METHODNAME");
$popularOptions = getOptionsWithIdAndName("SELECT POPULARID, LEVELSTAR FROM POPULARITY", "POPULARID", "LEVELSTAR");
$originOptions = getOptionsWithIdAndName("SELECT ORIGINCODE, NAMESTATE FROM ORIGIN", "ORIGINCODE", "NAMESTATE");

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
        $imageBlob = $adminData['IMAGE']; // This is the BLOB data

        // Convert BLOB to base64
        if ($imageBlob) {
            $imageData = base64_encode($imageBlob->load()); // Load the BLOB and encode it
            $imageSrc = 'data:image/jpeg;base64,' . $imageData; // Create a data URL
        } else {
            $imageSrc = 'sources/header/logo.png'; // Default image if no BLOB is found
        }
    } else {
        $username = "Unknown";
        $email = "unknown@example.com";
        $imageSrc = 'sources/header/logo.png'; // Default image if no data is found
    }
} else {
    $username = "Unknown";
    $email = "unknown@example.com";
    $imageSrc = 'sources/header/logo.png'; // Default image if no session is found
}

oci_close($condb);
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
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <!-- Existing image and title section -->
            <div class="row">
                <div class="col-12 col-md-4 my-4" id="imageContainer">
                    <!-- Image Preview -->
                    <img id="previewImage"
                        src="sources/uploadimage.jpg"
                        class="img-fluid text-center rounded-3"
                        alt="Uploaded Image Preview">

                    <!-- Hidden File Input -->
                    <input type="file"
                        name="image"
                        id="imageUpload"
                        accept="image/*"
                        required>
                </div>
                <div class="col-12 col-md gy-4">
                    <div class="col-12 bg-primary">
                        <input class="w-100 p-1 border-0 shadow-none fw-bolder fs-2" style="background-color: #FFFAF0;" type="text" name="kuehName" placeholder="Tajuk: Kuih Lapis Atok">
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center my-2">
                            <!-- Avatar -->
                            <img src="<?php echo $image; ?>" alt="Profile Picture" class="rounded-circle border" width="50" height="50">
                            <!-- Text -->
                            <div class="ms-3">
                                <h6 class="mb-0"><?php echo htmlspecialchars($username); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($email); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <textarea class="w-100 p-1 border-0 shadow-none" name="kuehDesc" style="background-color: #FFFAF0;" placeholder="Share kisah resepi anda"></textarea>
                    </div>
                </div>
            </div>

            <!-- Dropdown Section -->
            <div class="row mb-4">
                <div class="col-12 col-md-6 col-lg-3">
                    <label for="foodtype" class="form-label fw-bold">Food Type</label>
                    <select name="foodtype" id="foodtype" class="form-select" required>
                        <option value="" disabled selected>Select Food Type</option>
                        <?= $foodtypeOptions ?>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="method" class="form-label fw-bold">Cooking Method</label>
                    <select name="method" id="method" class="form-select" required>
                        <option value="" disabled selected>Select Cooking Method</option>
                        <?= $methodOptions ?>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="popular" class="form-label fw-bold">Popularity</label>
                    <select name="popular" id="popular" class="form-select" required>
                        <option value="" disabled selected>Select Popularity</option>
                        <?= $popularOptions ?>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label for="origin" class="form-label fw-bold">Origin</label>
                    <select name="origin" id="origin" class="form-select" required>
                        <option value="" disabled selected>Select Origin</option>
                        <?= $originOptions ?>
                    </select>
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
                    <div id="stepContainer">
                        <div class="input-group mb-2">
                            <button type="button" class="btn btn-outline-secondary border-0"><i class="bi bi-justify"></i></button>
                            <input type="text" name="steps[]" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" name="submit" class="btn btn-primary mb-4">Save Recipe</button>
                </div>
            </div>

        </form>
    </div>

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
    </script>
</body>