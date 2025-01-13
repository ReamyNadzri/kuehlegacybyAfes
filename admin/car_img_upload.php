<?PHP
# Memanggil fail header_admin.php
include('header_admin.php');
# Memanggil fail connection dari folder luaran
include('../connection.php');

?>
<style>
    .pad {
        padding-left: 30px;
        padding-top: 30px;
    }
</style>

<form action="car_img_upload.php" method="post" enctype="multipart/form-data" style="margin: 0 auto;width:75%">
    <h3>Select Main Image File:</h3>
    <div class="w3-hover-shadow w3-card w3-round-xlarge" style="height:100px">
        <input class="pad" type="file" name="image">
    </div><br>

    <h3>Select Side Image 1 File:</h3>
    <div class="w3-hover-shadow w3-card w3-round-xlarge" style="height:100px">
        <input class="pad" type="file" name="sideimage1">
    </div><br>

    <h3>Select Side Image 2 File:</h3>
    <div class="w3-hover-shadow w3-card w3-round-xlarge" style="height:100px">
        <input class="pad" type="file" name="sideimage2">
    </div><br>

    <h3>Select Side Image 3 File:</h3>
    <div class="w3-hover-shadow w3-card w3-round-xlarge" style="height:100px">
        <input class="pad" type="file" name="sideimage3">

    </div><br><br>
    <input class="w3-center w3-button w3-round-xlarge" size="15" style="background: #FFBF00; " type="submit"
        name="submit" value="Upload Files"><br><br><br>
</form>

<?php

// If file upload form is submitted 
$status = $statusMsg = "Please upload only ( '.jpg', '.png', '.jpeg', '.gif' ) FORMAT ONLY!. Click OK to continue.";
if (isset($_POST["submit"])) {
    $status = 'error';
    if (!empty($_FILES["image"]["name"]) && !empty($_FILES["sideimage1"]["name"]) && !empty($_FILES["sideimage2"]["name"]) && !empty($_FILES["sideimage3"]["name"])) {

        // Get file info main images
        $fileName0 = basename($_FILES["image"]["name"]);
        $fileType0 = pathinfo($fileName0, PATHINFO_EXTENSION);

        // Get file info sideimage 1
        $fileName1 = basename($_FILES["sideimage1"]["name"]);
        $fileType1 = pathinfo($fileName1, PATHINFO_EXTENSION);

        // Get file info sideimage 2
        $fileName2 = basename($_FILES["sideimage2"]["name"]);
        $fileType2 = pathinfo($fileName2, PATHINFO_EXTENSION);

        // Get file info sideimage 3
        $fileName3 = basename($_FILES["sideimage3"]["name"]);
        $fileType3 = pathinfo($fileName3, PATHINFO_EXTENSION);

        // Allow certain file formats 
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

        if (in_array($fileType0, $allowTypes) && in_array($fileType1, $allowTypes) && in_array($fileType2, $allowTypes) && in_array($fileType3, $allowTypes)) {

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

            // SQL for Oracle BLOB insert
            $arahan_sql_simpan = "
    INSERT INTO IMAGES (image, sideimages1, sideimages2, sideimages3, datecreate)
    VALUES (EMPTY_BLOB(), EMPTY_BLOB(), EMPTY_BLOB(), EMPTY_BLOB(), SYSDATE)
    RETURNING image, sideimages1, sideimages2, sideimages3 INTO :image, :sideimages1, :sideimages2, :sideimages3
";

            // Parse and bind BLOBs
            $stmt = oci_parse($condb, $arahan_sql_simpan);
            oci_bind_by_name($stmt, ":image", $lob0, -1, SQLT_BLOB);
            oci_bind_by_name($stmt, ":sideimages1", $lob1, -1, SQLT_BLOB);
            oci_bind_by_name($stmt, ":sideimages2", $lob2, -1, SQLT_BLOB);
            oci_bind_by_name($stmt, ":sideimages3", $lob3, -1, SQLT_BLOB);

            if (oci_execute($stmt, OCI_DEFAULT)) {
                // Write BLOB data
                $lob0->save($image0);
                $lob1->save($image1);
                $lob2->save($image2);
                $lob3->save($image3);

                // Commit transaction
                oci_commit($condb);

                // Free resources
                $lob0->free();
                $lob1->free();
                $lob2->free();
                $lob3->free();
                oci_free_statement($stmt);
                oci_close($condb);

                $status = 'success';
                $statusMsg = "File uploaded successfully.";
            } else {
                $e = oci_error($stmt);
                $status = 'error';
                $statusMsg = "Upload failed: " . htmlentities($e['message']);
            }
        } else {
            $statusMsg = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.';
        }
    } else {
        $statusMsg = 'Please select an image file to upload.';
    }
}

// Display status message 
echo "<script>alert('" . $statusMsg . "');</script>";
?>