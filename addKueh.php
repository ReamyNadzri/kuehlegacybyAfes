<?PHP
include('header.php');
include('connection.php');


                    if (empty($_SESSION['username'])) {
                    
                        echo"<script>
                            alert('Sila log masuk untuk mengakses laman ini');
                            window.location.href = 'userLogin.php';
                        </script>";}

                        if(isset($_SESSION['google_user'])){
                            $user = [
                            "userName" => $_SESSION['google_user']['name'],
                            "email" => $_SESSION['google_user']['email'],
                            "profile_image" => $_SESSION['google_user']['picture'] // Avatar Profile
                            ];
                        }else{
                            $user['userName'] = $_SESSION['username'];
                            $user['email'] = $_SESSION['email'];
                            $user['phoneNum'] = $_SESSION['phoneNum'];
                            $user['profile_image'] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAACXBIWXMAAAsTAAALEwEAmpwYAAAFdUlEQVR4nO1aS4hcRRR9UaPJxs9CVBT8oBmDEsVPFE0iGJxEMbixySKSTt+qe9+7t6dHRlQQlMadGozfGH8ouHAnoi5cmLjwFxJJ1CgiMaMjRpB8FxLJZybK7e6Zed0zk3RVve6ZhHegYJjX737Ou1V1696Kohw5cuTIkaOjIOq7FogTg7IBSDYZlEFAOQAkR2uj9jfv0mdA/JpBiSFJeqJTGdbKjUC8zqDsNiT/+Qwg/tOgPF+K+26IThHMMsQrgGSzr9NTDuRvrC3frzqimQgTxwsN8reZO94aFShbgMq3RDMFhYGBuUD8CiCPdNr5cRJ4BIhfKhaLc6bVeUiSHiD5oVuOT7JGfLcmjq+ZFudLJLcZ5H3T5XxqShwokSzqrvOY3GuQ/51u58cjQQ4ZkuXdcZ5kUbjz/Bcgvw8kTwMKAMlSnU7W9l3JzBfoUF0G5RcXEhD5jo46D0nSY4gPejmtOwRKnyZF7eozyM85RsL+jq0Jhdpq77Pg8Q79wt7R5ky0bO/I7gAor3oY83aIMYVC4UxDvMcj4l7MPMkB532e38wiazPE73oQMLyGyjdll96iW4anU6VSqZyThfJSqXIhED8CyIcdbdicSdpsiFe4fgFry8uijAHEFVc7dLvOQLG4HWxQdnbiwEJEs3X7dIyCr4OPtMaRdSB5NuoQgPgt5/XA8IIQheucwz+WB9IySjGvNsRDhvg9Q9IPxL3Wlufp3NZ1QncJY/ovMobn21geNCjPAMnPBmWg1R5DXPJYENd6E2B8ihmG5zeFrWdBRHcda/nmJnsML/CIyD8Csj5xNpyIzhszOO6700dGioRP0zYR0SU+cryyQ9Aanh8Bs8cIQH4ikICR1XF86ag8nTI+ciwxORNgUDb4KGuSQfJhCAH1UbYtMn3krHcngOTzcAJ4WygBgPJOMAHIG30i4PdQAoD472ACSL4IJ0AGPQhgr2pPi4zwognK7lACgGSvOwEkR0JDzZAcDyaA5EgLqRudCUA+7EwAOB4+Ws8AYGVVBs43Bj+UInV5dwgg2RuSA2gCkiEBQ6Nyi8WHz/d4f48zAQZl0FXRqkrl3BAC25nDSrL7+/yrTwRscjY2jheOEUjyVIYEPDkq19rkdg8Zn3WlBAbEr2eRTTbJtIItdr3hLAflZWcCDErsYfBwugBarVbPAOLfvAlAGVQZ44TKUtURSmJbsLY8zzNcD2nXaPyL8aMBBIwdiVVmowHiLsfI1c4EhGSDhuSj8VW7OEcvP3g4vzNdVwTiT3yjKPKFQX7Bk4Dj6QWxhHwXIB9ziKKj6X5fY+HzSqq0qONNACTJ9d7hS7yt6QsiS7vkpY+vtSMwynZfO6yV67wJUGhh0Z+E5mOozukT9Rcaz/qb3iFZ76s/uCg61gn2J0CNWBKlYGK5T1vak/xuf2uH19pkcYjuTMriCkD+yj8EJ/YItAucXtQA5eNiHF8x8XflZQFT8Mso0/s/5L7/6kDkW3316rteUYd8LPN7RIZkrcdXGCqKXDwqQ3sGWticSoc+S/cV9N16SX0G9CYKAwNzDfL37RnAu0oxm0KhcHZaRv2AxAeB5LF0sVP/NsiPN541FS9UhspqN5fQe0Nqa+YEKADk8hO2q1EGdQurVqtnRS3QlLbNaTScTn+b3+dCrWkypX7eV2K+KuokgGTJxJSUdxjilZMZ7nOO199OJadBxMq6zgkpeHcuS1mbLAbkf7QNrq0vvchwsnf0y7RLgO4SDrdSt6rz1iZ3R92EtZXLXLrA2uZqn4DmlthJMKthy8wGEPe2vZLH5Xui0w1A3NvOgai+h3NvdDqiqPt67TwgHwDxT7XOcX38qP/TZ+m8IUeOHDly5MiRI0fUOfwPWUs8T7LFLncAAAAASUVORK5CYII=";
                        
                        }

                        
// Process form submission
if (isset($_POST['submit'])) {
    // Process form data
    $kuehName = $_POST['kuehName'];
    $kuehDesc = $_POST['kuehDesc'];
    $foodTypeCode = $_POST['foodtype'];
    $methodId = $_POST['method'];
    $popularId = $_POST['popular'];
    $originId = $_POST['origin'];
    $video = $_POST['video'];
    $ingredients = $_POST['ingredients'] ?? [];
    $steps = $_POST['steps'] ?? [];

    // Validate uploaded image
    if (!empty($_FILES['image']['tmp_name'])) {
        // Initialize BLOB descriptor
        $lob = oci_new_descriptor($condb, OCI_D_LOB);

        // Read the image file
        $imageData = file_get_contents($_FILES['image']['tmp_name']);

        // Insert into KUEH table
        $sql_kueh = "INSERT INTO KUEH (KUEHNAME, KUEHDESC, FOODTYPECODE, METHODID, VIDEO, POPULARID, ORIGINID, IMAGE, USERNAME) 
                     VALUES (:kuehName, :kuehDesc, :foodTypeCode, :methodId, :video, :popularId, :originId, EMPTY_BLOB(), :username)
                     RETURNING IMAGE INTO :image";
        $laksana_sql_kueh = oci_parse($condb, $sql_kueh);

        // Bind parameters
        oci_bind_by_name($laksana_sql_kueh, ":kuehName", $kuehName);
        oci_bind_by_name($laksana_sql_kueh, ":kuehDesc", $kuehDesc);
        oci_bind_by_name($laksana_sql_kueh, ":foodTypeCode", $foodTypeCode);
        oci_bind_by_name($laksana_sql_kueh, ":methodId", $methodId);
        oci_bind_by_name($laksana_sql_kueh, ":video", $video);
        oci_bind_by_name($laksana_sql_kueh, ":popularId", $popularId);
        oci_bind_by_name($laksana_sql_kueh, ":originId", $originId);
        oci_bind_by_name($laksana_sql_kueh, ":image", $lob, -1, SQLT_BLOB);
        oci_bind_by_name($laksana_sql_kueh, ":username", $user['userName']);

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
                alert('Kueh details saved successfully!');
                window.location.href = 'index.php';
            </script>";
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
                        src="sources/kueh_default.png"
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
                            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Picture" class="rounded-circle border" width="50" height="50" style="">
                            <!-- Text -->
                            <div class="ms-3">
                                <h6 class="mb-0"><?=$user['userName']?></h6>
                                <small class="text-muted"><?=$user['email']?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <textarea class="w-100 p-1 border-0 shadow-none" name="kuehDesc" style="background-color: #FFFAF0;" rows="6" placeholder="Share kisah resepi anda"></textarea>
                    </div>
                </div>
            </div>

            <!-- Dropdown Section -->
            <div class="row mb-4">
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

<?php
include('footer.php');
?>