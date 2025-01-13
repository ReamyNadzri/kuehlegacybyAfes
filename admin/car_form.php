<?php
include('header_admin.php');
include('../connection.php');

// Initialize variables to hold car data
$carData = null;

// Check if numPlate is provided in the query string
if (isset($_GET['numPlate'])) {
    $numPlate = $_GET['numPlate'];

    // Fetch car data and associated images from the database
    $sql = "
        SELECT c.*, i.image, i.sideimages1, i.sideimages2, i.sideimages3
        FROM car c
        LEFT JOIN images i ON c.idimg = i.idimg
        WHERE c.numPlate = :numPlate
    ";
    $stmt = oci_parse($condb, $sql);
    oci_bind_by_name($stmt, ':numPlate', $numPlate);
    oci_execute($stmt);

    // Fetch the result
    $carData = oci_fetch_assoc($stmt);

    // Free the statement
    oci_free_statement($stmt);
}

$currentYear = date("Y");
$years = range(1900, $currentYear + 5);

// Fetch the year from the database
$yearManufac = $carData ? $carData['YEARMANUFAC'] : '';
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


    <!--CONTENT START HERE-->
    <div class="container w-75">
        <div class="row">
            <div class="col-12 col-md-4 my-4">
                <img src="sources\kuehDetails\cekodokpisang.jpeg" class="img-fluid text-center rounded-3" alt="test image" style="min-height:380px; object-fit: cover;">
            </div>
            <div class="col-12 col-md gy-4">
                <div class="col-12 bg-primary">
                    <input class="w-100 p-1 border-0 shadow-none fw-bolder fs-2" style="background-color: #FFFAF0;" type="text" name="kuehName" placeholder="Tajuk: Kuih Lapis Atok">
                </div>
                <div class="col-12">
                    <div class="d-flex align-items-center my-2">
                        <!-- Avatar -->
                        <img src="sources\header\logo.png" alt="Profile Picture" class="rounded-circle border" width="50" height="50" style="">
                        <!-- Text -->
                        <div class="ms-3">
                            <h6 class="mb-0">Haziq Akram</h6>
                            <small class="text-muted">@cook_111408822</small>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <textarea class="w-100 p-1 border-0 shadow-none" name="kuehDesc" style="background-color: #FFFAF0;" placeholder="Share kisah resepi anda"></textarea>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-12 col-lg-3 col-md-6 py-3">
                <h1 class="fw-bolder">Ramuan</h1>
                <div class="row gy-3">
                    <div class="col">Sajian</div>
                    <div class="col">
                        <input type="text" class="rounded" placeholder="2 Orang">
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <button class="btn btn-outline-secondary border-0" type="button" id="button-addon1"><i
                                    class="bi bi-justify"></i></button>
                            <input type="text" class="form-control" placeholder=""
                                aria-label="Example text with button addon" aria-describedby="button-addon1">
                            <button class="btn btn-outline-secondary" type="button" id="button-addon2"><i
                                    class="bi bi-three-dots"></i></button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <button class="btn btn-outline-secondary border-0" type="button" id="button-addon1"><i
                                    class="bi bi-justify"></i></button>
                            <input type="text" class="form-control" placeholder=""
                                aria-label="Example text with button addon" aria-describedby="button-addon1">
                            <button class="btn btn-outline-secondary" type="button" id="button-addon2"><i
                                    class="bi bi-three-dots"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg col-md-6 py-3 ms-3">
                <h1 class="fw-bolder">Cara Memasak</h1>
                <div class="row gy-3">
                    <div class="col">Tempoh Masak</div>
                    <div class="col text-start">
                        <input type="text" class="rounded" placeholder="1 jam 30 minit">
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <button class="btn btn-outline-secondary border-0" type="button" id="button-addon1"><i
                                    class="bi bi-justify"></i></button>
                            <input type="text" class="form-control" placeholder=""
                                aria-label="Example text with button addon" aria-describedby="button-addon1">
                            <button class="btn btn-outline-secondary" type="button" id="button-addon2"><i
                                    class="bi bi-three-dots"></i></button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <button class="btn btn-outline-secondary border-0" type="button" id="button-addon1"><i
                                    class="bi bi-justify"></i></button>
                            <input type="text" class="form-control" placeholder=""
                                aria-label="Example text with button addon" aria-describedby="button-addon1">
                            <button class="btn btn-outline-secondary" type="button" id="button-addon2"><i
                                    class="bi bi-three-dots"></i></button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <button class="btn btn-outline-secondary border-0" type="button" id="button-addon1"><i
                                    class="bi bi-justify"></i></button>
                            <input type="text" class="form-control" placeholder=""
                                aria-label="Example text with button addon" aria-describedby="button-addon1">
                            <button class="btn btn-outline-secondary" type="button" id="button-addon2"><i
                                    class="bi bi-three-dots"></i></button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <button class="btn btn-outline-secondary border-0" type="button" id="button-addon1"><i
                                    class="bi bi-justify"></i></button>
                            <input type="text" class="form-control" placeholder=""
                                aria-label="Example text with button addon" aria-describedby="button-addon1">
                            <button class="btn btn-outline-secondary" type="button" id="button-addon2"><i
                                    class="bi bi-three-dots"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php
    include('footer.php');
    ?>



    </div> <!--must include in next page-->
    </div>
    </div>
    </div>
    </div>
</body><!--until here-->

<?PHP
// Handle image upload
if (isset($_POST["submit"])) {
    if (!$condb) {
        die("<script>alert('Connection to Oracle failed. Please check your connection settings.');</script>");
    }

    if (
        !empty($_FILES["image"]["name"]) && !empty($_FILES["sideimage1"]["name"]) &&
        !empty($_FILES["sideimage2"]["name"]) && !empty($_FILES["sideimage3"]["name"])
    ) {

        // Get file info
        $fileName0 = basename($_FILES["image"]["name"]);
        $fileName1 = basename($_FILES["sideimage1"]["name"]);
        $fileName2 = basename($_FILES["sideimage2"]["name"]);
        $fileName3 = basename($_FILES["sideimage3"]["name"]);

        $fileType0 = pathinfo($fileName0, PATHINFO_EXTENSION);
        $fileType1 = pathinfo($fileName1, PATHINFO_EXTENSION);
        $fileType2 = pathinfo($fileName2, PATHINFO_EXTENSION);
        $fileType3 = pathinfo($fileName3, PATHINFO_EXTENSION);

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (
            in_array($fileType0, $allowTypes) && in_array($fileType1, $allowTypes) &&
            in_array($fileType2, $allowTypes) && in_array($fileType3, $allowTypes)
        ) {

            // Initialize BLOB descriptors
            $lob0 = oci_new_descriptor($condb, OCI_D_LOB);
            $lob1 = oci_new_descriptor($condb, OCI_D_LOB);
            $lob2 = oci_new_descriptor($condb, OCI_D_LOB);
            $lob3 = oci_new_descriptor($condb, OCI_D_LOB);

            // Read image files
            $image0 = file_get_contents($_FILES['image']['tmp_name']);
            $image1 = file_get_contents($_FILES['sideimage1']['tmp_name']);
            $image2 = file_get_contents($_FILES['sideimage2']['tmp_name']);
            $image3 = file_get_contents($_FILES['sideimage3']['tmp_name']);

            $sql = "INSERT INTO IMAGES (image, sideimages1, sideimages2, sideimages3, datecreate)
                   VALUES (EMPTY_BLOB(), EMPTY_BLOB(), EMPTY_BLOB(), EMPTY_BLOB(), SYSDATE)
                   RETURNING image, sideimages1, sideimages2, sideimages3 INTO :image, :sideimages1, :sideimages2, :sideimages3";

            $stmt = oci_parse($condb, $sql);

            oci_bind_by_name($stmt, ":image", $lob0, -1, SQLT_BLOB);
            oci_bind_by_name($stmt, ":sideimages1", $lob1, -1, SQLT_BLOB);
            oci_bind_by_name($stmt, ":sideimages2", $lob2, -1, SQLT_BLOB);
            oci_bind_by_name($stmt, ":sideimages3", $lob3, -1, SQLT_BLOB);

            if (oci_execute($stmt, OCI_DEFAULT)) {
                $lob0->save($image0);
                $lob1->save($image1);
                $lob2->save($image2);
                $lob3->save($image3);

                oci_commit($condb);

                // Get the last inserted IDIMG
                $sql_last_id = "SELECT MAX(IDIMG) as LAST_ID FROM IMAGES";
                $stmt_last_id = oci_parse($condb, $sql_last_id);
                oci_execute($stmt_last_id);
                $row = oci_fetch_assoc($stmt_last_id);
                $last_id = $row['LAST_ID'];

                $status = 'success';
                $statusMsg = "Images uploaded successfully. Please complete the car details.";
            } else {
                $e = oci_error($stmt);
                $statusMsg = "Upload failed: " . htmlentities($e['message']);
            }

            // Free resources
            $lob0->free();
            $lob1->free();
            $lob2->free();
            $lob3->free();
            oci_free_statement($stmt);
        } else {
            $statusMsg = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.';
        }
    }

    $numPlate = $_POST['numPlate'];
    $carName = $_POST['carName'];
    $carType = $_POST['carType'];
    $color = $_POST['color'];
    $yearManufac = $_POST['yearManufac'];
    $initialPrice = number_format((float)$_POST['initialPrice'], 2, '.', ''); // Ensure proper formatting
    $desccar = $_POST['desccar'];
    $transmission = $_POST['transmission'];
    $odometer = $_POST['odometer'];
    $variant = $_POST['variant'];
    $fuelType = $_POST['fuelType'];
    $seat = $_POST['seat'];
    $cc = $_POST['cc'];
    $model_ID = $_POST['model_ID'];

    // Prepare SQL statement
    $sql = "INSERT INTO CAR (
        NUMPLATE, CARNAME, CARTYPE, COLOR, YEARMANUFAC, 
        INITIALPRICE, DESCCAR, TRANSMISSION, ODOMETER, 
        VARIANT, FUELTYPE, SEAT, CC, MODEL_ID, IDIMG
    ) VALUES (
        :numPlate, :carName, :carType, :color, :yearManufac,
        :initialPrice, :desccar, :transmission, :odometer,
        :variant, :fuelType, :seat, :cc, :model_ID, :idimg
    )";

    // Prepare and bind
    $stmt = oci_parse($condb, $sql);

    // Bind the parameters
    oci_bind_by_name($stmt, ":numPlate", $numPlate);
    oci_bind_by_name($stmt, ":carName", $carName);
    oci_bind_by_name($stmt, ":carType", $carType);
    oci_bind_by_name($stmt, ":color", $color);
    oci_bind_by_name($stmt, ":yearManufac", $yearManufac);
    oci_bind_by_name($stmt, ":initialPrice", $initialPrice);
    oci_bind_by_name($stmt, ":desccar", $desccar);
    oci_bind_by_name($stmt, ":transmission", $transmission);
    oci_bind_by_name($stmt, ":odometer", $odometer);
    oci_bind_by_name($stmt, ":variant", $variant);
    oci_bind_by_name($stmt, ":fuelType", $fuelType);
    oci_bind_by_name($stmt, ":seat", $seat);
    oci_bind_by_name($stmt, ":cc", $cc);
    oci_bind_by_name($stmt, ":model_ID", $model_ID);
    oci_bind_by_name($stmt, ":idimg", $last_id);

    // Execute the statement
    if (oci_execute($stmt)) {
        oci_commit($condb);
        echo "<script>
            alert('Car details saved successfully!');
            window.location.href = 'car_info.php';
        </script>";
    } else {
        $e = oci_error($stmt);
        echo "<script>alert('Error saving car details: " . htmlentities($e['message']) . "');</script>";
    }

    // Free the statement
    oci_free_statement($stmt);
    oci_close($condb);
}
