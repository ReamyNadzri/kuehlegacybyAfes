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

<div class="w3-container">
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="w3-row">
            <!-- Car Details Form (75%) -->
            <div class="w3-col m9 w3-padding-right">
                <div class="w3-card w3-round w3-padding w3-margin-right" style="min-height: 600px;">
                    <h3>Car Details</h3>

                    <div class="w3-row-padding">
                        <div class="w3-third">
                            <label>Plate Number</label>
                            <input type="text" name="numPlate" class="w3-input w3-border" value="<?php echo $carData ? $carData['NUMPLATE'] : ''; ?>" required>
                        </div>
                        <div class="w3-third">
                            <label>Car Name</label>
                            <input type="text" name="carName" class="w3-input w3-border" value="<?php echo $carData ? $carData['CARNAME'] : ''; ?>" required>
                        </div>
                        <div class="w3-third">
                            <label>Car Type</label>
                            <input type="text" name="carType" class="w3-input w3-border" value="<?php echo $carData ? $carData['CARTYPE'] : ''; ?>" required>
                        </div>
                    </div>

                    <div class="w3-row-padding w3-margin-top">
                        <div class="w3-third">
                            <label>Color</label>
                            <input type="text" name="color" class="w3-input w3-border" value="<?php echo $carData ? $carData['COLOR'] : ''; ?>" required>
                        </div>
                        <div class="w3-third">
                            <label>Year of Manufacture</label>
                            <select name="yearManufac" class="w3-select w3-border" required>
                                <option disabled selected value="">Select Year</option>
                                <?php
                                foreach ($years as $year) {
                                    $selected = ($yearManufac == $year) ? 'selected' : '';
                                    echo "<option value='$year' $selected>$year</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="w3-third">
                            <label>Initial Price</label>
                            <input type="number" name="initialPrice" class="w3-input w3-border" value="<?php echo $carData ? $carData['INITIALPRICE'] : ''; ?>" required>
                        </div>
                    </div>

                    <div class="w3-row-padding w3-margin-top">
                        <div class="w3-third">
                            <label>Transmission</label>
                            <input type="text" name="transmission" class="w3-input w3-border" value="<?php echo $carData ? $carData['TRANSMISSION'] : ''; ?>" required>
                        </div>
                        <div class="w3-third">
                            <label>Odometer</label>
                            <input type="number" name="odometer" class="w3-input w3-border" value="<?php echo $carData ? $carData['ODOMETER'] : ''; ?>" required>
                        </div>
                        <div class="w3-third">
                            <label>Variant</label>
                            <input type="text" name="variant" class="w3-input w3-border" value="<?php echo $carData ? $carData['VARIANT'] : ''; ?>" required>
                        </div>
                    </div>

                    <div class="w3-row-padding w3-margin-top">
                        <div class="w3-third">
                            <label>Fuel Type</label>
                            <input type="text" name="fuelType" class="w3-input w3-border" value="<?php echo $carData ? $carData['FUELTYPE'] : ''; ?>" required>
                        </div>
                        <div class="w3-third">
                            <label>Seat</label>
                            <input type="number" name="seat" class="w3-input w3-border" value="<?php echo $carData ? $carData['SEAT'] : ''; ?>" required>
                        </div>
                        <div class="w3-third">
                            <label>CC</label>
                            <input type="number" name="cc" class="w3-input w3-border" value="<?php echo $carData ? $carData['CC'] : ''; ?>" required>
                        </div>
                    </div>

                    <div class="w3-row-padding w3-margin-top">
                        <div class="w3-col m12">
                            <label>Model</label>
                            <select name="model_ID" class="w3-select w3-border" required>
                                <option disabled selected value="">Select Model</option>
                                <?php
                                $sql = "SELECT model_ID, modelName FROM model";
                                $stmt = oci_parse($condb, $sql);
                                oci_execute($stmt);
                                while ($row = oci_fetch_assoc($stmt)) {
                                    $selected = ($carData && $carData['MODEL_ID'] == $row['MODEL_ID']) ? 'selected' : '';
                                    echo "<option value='" . $row['MODEL_ID'] . "' $selected>" . $row['MODELNAME'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="w3-row-padding w3-margin-top">
                        <div class="w3-col m12">
                            <label>Description</label>
                            <textarea name="desccar" class="w3-input w3-border" rows="4" required><?php echo $carData ? $carData['DESCCAR'] : ''; ?></textarea>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Image Upload Section (25%) -->
            <div class="w3-col m3">
                <div class="w3-card w3-round w3-padding" style="min-height: 600px;">
                    <h3>Car Images<span class="w3-text-red">*</span></h3>

                    <!-- Main Image -->
                    <div class="w3-margin-bottom">
                        <label>Main Image</label>
                        <div class="w3-display-container w3-border" style="height: 150px; background-color: #f5f5f5;">
                            <?php
                            if ($carData && $carData['IMAGE']) {
                                $mainImage = base64_encode($carData['IMAGE']->load());
                                echo "<img id='mainPreview' src='data:image/jpeg;base64,$mainImage' style='width: 100%; height: 100%; object-fit: cover;'>";
                            } else {
                                echo "<img id='mainPreview' class='w3-hide' style='width: 100%; height: 100%; object-fit: cover;'>";
                                echo "<div id='mainPlaceholder' class='w3-display-middle' style='text-align: center; width: 100%;'>
                    <i class='fas fa-camera w3-jumbo w3-text-light-grey'></i>
                  </div>";
                            }
                            ?>
                        </div>
                        <input type="file" name="image" class="w3-hide" id="mainImage" accept="image/*">
                        <button type="button" onclick="document.getElementById('mainImage').click()" class="w3-button w3-block w3-blue w3-margin-top">Select Main Image</button>
                    </div>

                    <!-- Side Images -->
                    <div class="w3-row-padding">
                        <div class="w3-col s4">
                            <div class="w3-display-container w3-border" style="height: 120px; background-color: #f5f5f5;">
                                <?php
                                if ($carData && $carData['SIDEIMAGES1']) {
                                    $sideImage1 = base64_encode($carData['SIDEIMAGES1']->load());
                                    echo "<img id='side1Preview' src='data:image/jpeg;base64,$sideImage1' style='width: 100%; height: 100%; object-fit: cover;'>";
                                } else {
                                    echo "<img id='side1Preview' class='w3-hide' style='width: 100%; height: 100%; object-fit: cover;'>";
                                    echo "<div id='side1Placeholder' class='w3-display-middle' style='text-align: center; width: 100%;'>
                        <i class='fas fa-plus'></i>
                      </div>";
                                }
                                ?>
                            </div>
                            <input type="file" name="sideimage1" class="w3-hide" id="side1" accept="image/*">
                            <button type="button" onclick="document.getElementById('side1').click()" class="w3-button w3-tiny w3-blue w3-margin-top">Side 1</button>
                        </div>
                        <div class="w3-col s4">
                            <div class="w3-display-container w3-border" style="height: 120px; background-color: #f5f5f5;">
                                <?php
                                if ($carData && $carData['SIDEIMAGES2']) {
                                    $sideImage2 = base64_encode($carData['SIDEIMAGES2']->load());
                                    echo "<img id='side2Preview' src='data:image/jpeg;base64,$sideImage2' style='width: 100%; height: 100%; object-fit: cover;'>";
                                } else {
                                    echo "<img id='side2Preview' class='w3-hide' style='width: 100%; height: 100%; object-fit: cover;'>";
                                    echo "<div id='side2Placeholder' class='w3-display-middle' style='text-align: center; width: 100%;'>
                        <i class='fas fa-plus'></i>
                      </div>";
                                }
                                ?>
                            </div>
                            <input type="file" name="sideimage2" class="w3-hide" id="side2" accept="image/*">
                            <button type="button" onclick="document.getElementById('side2').click()" class="w3-button w3-tiny w3-blue w3-margin-top">Side 2</button>
                        </div>
                        <div class="w3-col s4">
                            <div class="w3-display-container w3-border" style="height: 120px; background-color: #f5f5f5;">
                                <?php
                                if ($carData && $carData['SIDEIMAGES3']) {
                                    $sideImage3 = base64_encode($carData['SIDEIMAGES3']->load());
                                    echo "<img id='side3Preview' src='data:image/jpeg;base64,$sideImage3' style='width: 100%; height: 100%; object-fit: cover;'>";
                                } else {
                                    echo "<img id='side3Preview' class='w3-hide' style='width: 100%; height: 100%; object-fit: cover;'>";
                                    echo "<div id='side3Placeholder' class='w3-display-middle' style='text-align: center; width: 100%;'>
                        <i class='fas fa-plus'></i>
                      </div>";
                                }
                                ?>
                            </div>
                            <input type="file" name="sideimage3" class="w3-hide" id="side3" accept="image/*">
                            <button type="button" onclick="document.getElementById('side3').click()" class="w3-button w3-tiny w3-blue w3-margin-top">Side 3</button>
                        </div>
                    </div>

                    <button type="submit" name="submit" class="w3-button w3-block w3-amber w3-margin-top">Update Car Details</button>

                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function previewImage(input, previewId, placeholderId, removeId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
                document.getElementById(previewId).classList.remove('w3-hide');
                document.getElementById(placeholderId).classList.add('w3-hide');
                if (removeId) document.getElementById(removeId).classList.remove('w3-hide');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Add event listeners
    document.getElementById('mainImage').addEventListener('change', function() {
        previewImage(this, 'mainPreview', 'mainPlaceholder', 'mainRemove');
    });

    document.getElementById('side1').addEventListener('change', function() {
        previewImage(this, 'side1Preview', 'side1Placeholder');
    });

    document.getElementById('side2').addEventListener('change', function() {
        previewImage(this, 'side2Preview', 'side2Placeholder');
    });

    document.getElementById('side3').addEventListener('change', function() {
        previewImage(this, 'side3Preview', 'side3Placeholder');
    });
</script>

<?PHP
// Handle form submission for update
if (isset($_POST["submit"])) {
    if (!$condb) {
        die("<script>alert('Connection to Oracle failed. Please check your connection settings.');</script>");
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

    // Prepare SQL statement to update car details
    $sql = "UPDATE CAR SET
        CARNAME = :carName,
        CARTYPE = :carType,
        COLOR = :color,
        YEARMANUFAC = :yearManufac,
        INITIALPRICE = :initialPrice,
        DESCCAR = :desccar,
        TRANSMISSION = :transmission,
        ODOMETER = :odometer,
        VARIANT = :variant,
        FUELTYPE = :fuelType,
        SEAT = :seat,
        CC = :cc,
        MODEL_ID = :model_ID
        WHERE NUMPLATE = :numPlate";

    // Prepare and bind
    $stmt = oci_parse($condb, $sql);

    // Bind the parameters
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
    oci_bind_by_name($stmt, ":numPlate", $numPlate);

    // Execute the statement
    if (oci_execute($stmt)) {
        oci_commit($condb);
        echo "<script>
            alert('Car details updated successfully!');
            window.location.href = 'car_info.php';
        </script>";
    } else {
        $e = oci_error($stmt);
        echo "<script>alert('Error updating car details: " . htmlentities($e['message']) . "');</script>";
    }

    // Free the statement
    oci_free_statement($stmt);

    // Handle image updates if new images are uploaded
    if (
        !empty($_FILES["image"]["name"]) || !empty($_FILES["sideimage1"]["name"]) ||
        !empty($_FILES["sideimage2"]["name"]) || !empty($_FILES["sideimage3"]["name"])
    ) {
        // Fetch the existing image ID
        $sql = "SELECT IDIMG FROM CAR WHERE NUMPLATE = :numPlate";
        $stmt = oci_parse($condb, $sql);
        oci_bind_by_name($stmt, ':numPlate', $numPlate);
        oci_execute($stmt);
        $row = oci_fetch_assoc($stmt);
        $idimg = $row['IDIMG'];

        // Retrieve old image paths from hidden fields
        $old_image = $_POST['old_image'];
        $old_sideimage1 = $_POST['old_sideimage1'];
        $old_sideimage2 = $_POST['old_sideimage2'];
        $old_sideimage3 = $_POST['old_sideimage3'];

        // Prepare the SQL with conditional updates
        $sql = "UPDATE IMAGES SET
            image = CASE 
                WHEN :update_image = 1 THEN EMPTY_BLOB()
                ELSE image 
            END,
            sideimages1 = CASE 
                WHEN :update_side1 = 1 THEN EMPTY_BLOB()
                ELSE sideimages1 
            END,
            sideimages2 = CASE 
                WHEN :update_side2 = 1 THEN EMPTY_BLOB()
                ELSE sideimages2 
            END,
            sideimages3 = CASE 
                WHEN :update_side3 = 1 THEN EMPTY_BLOB()
                ELSE sideimages3 
            END
            WHERE IDIMG = :idimg
            RETURNING 
                CASE WHEN :update_image = 1 THEN image END,
                CASE WHEN :update_side1 = 1 THEN sideimages1 END,
                CASE WHEN :update_side2 = 1 THEN sideimages2 END,
                CASE WHEN :update_side3 = 1 THEN sideimages3 END
            INTO :image, :sideimages1, :sideimages2, :sideimages3";

        $stmt = oci_parse($condb, $sql);

        // Create flags for which images are being updated
        $update_image = !empty($_FILES["image"]["name"]) ? 1 : 0;
        $update_side1 = !empty($_FILES["sideimage1"]["name"]) ? 1 : 0;
        $update_side2 = !empty($_FILES["sideimage2"]["name"]) ? 1 : 0;
        $update_side3 = !empty($_FILES["sideimage3"]["name"]) ? 1 : 0;

        // Bind the update flags
        oci_bind_by_name($stmt, ":update_image", $update_image);
        oci_bind_by_name($stmt, ":update_side1", $update_side1);
        oci_bind_by_name($stmt, ":update_side2", $update_side2);
        oci_bind_by_name($stmt, ":update_side3", $update_side3);

        $lob0 = oci_new_descriptor($condb, OCI_D_LOB);
        $lob1 = oci_new_descriptor($condb, OCI_D_LOB);
        $lob2 = oci_new_descriptor($condb, OCI_D_LOB);
        $lob3 = oci_new_descriptor($condb, OCI_D_LOB);

        oci_bind_by_name($stmt, ":idimg", $idimg);
        oci_bind_by_name($stmt, ":image", $lob0, -1, SQLT_BLOB);
        oci_bind_by_name($stmt, ":sideimages1", $lob1, -1, SQLT_BLOB);
        oci_bind_by_name($stmt, ":sideimages2", $lob2, -1, SQLT_BLOB);
        oci_bind_by_name($stmt, ":sideimages3", $lob3, -1, SQLT_BLOB);

        if (oci_execute($stmt, OCI_DEFAULT)) {
            if ($update_image) {
                $image0 = file_get_contents($_FILES['image']['tmp_name']);
                $lob0->save($image0);
            }
            if ($update_side1) {
                $image1 = file_get_contents($_FILES['sideimage1']['tmp_name']);
                $lob1->save($image1);
            }
            if ($update_side2) {
                $image2 = file_get_contents($_FILES['sideimage2']['tmp_name']);
                $lob2->save($image2);
            }
            if ($update_side3) {
                $image3 = file_get_contents($_FILES['sideimage3']['tmp_name']);
                $lob3->save($image3);
            }

            oci_commit($condb);
            echo "<script>alert('Images updated successfully!');</script>";
        } else {
            $e = oci_error($stmt);
            echo "<script>alert('Error updating images: " . htmlentities($e['message']) . "');</script>";
        }

        // Free resources
        $lob0->free();
        $lob1->free();
        $lob2->free();
        $lob3->free();
        oci_free_statement($stmt);
    }

    oci_close($condb);
}
?>