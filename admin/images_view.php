<?php
# Include the admin header
include('header_admin.php');

# Include the connection file for Oracle Database
include('../connection.php');

# SQL Query to fetch images, ordered by the creation date (most recent first)
$query = "
    SELECT idimg, 
           image, 
           sideimages1, 
           sideimages2, 
           sideimages3, 
           datecreate
    FROM images
    ORDER BY datecreate DESC
";

# Execute the query using OCI8
$stid = oci_parse($condb, $query);
oci_execute($stid);

# Check if any rows are returned
if (oci_fetch_all($stid, $rows, null, null, OCI_FETCHSTATEMENT_BY_ROW) > 0) {
    echo "<hr>
        <a href='car_img_upload.php' class='w3-button w3-round w3-hover-shadow w3-round-xlarge w3-center' style='margin-bottom:10px;width:40%;background: #FFBF00'>
        Upload Car Images <b> Click Here! </b></a>
        <br>";

    # Iterate through each row
    foreach ($rows as $rekod) {
        # Prepare data for GET request
        $data_get = [
            'numPlate' => $rekod['IDIMG'],
            'carName' => $rekod['IMAGE'],
            'carType' => $rekod['SIDEIMAGES1'],
            'color' => $rekod['SIDEIMAGES2'],
            'yearManufac' => $rekod['SIDEIMAGES3'],
            'desccar' => $rekod['DATECREATE']
        ];

        # Assign session variable
        $_SESSION['idimg'] = $rekod['IDIMG'];

        # Display the images and associated actions
        echo "
        <a href=purchase_view.php?" . http_build_query($data_get) . ">
        <div class='w3-col w3-container w3-card w3-round-large' style='margin-bottom:15px;text-align:left; width: 100%; height: auto; padding-bottom:20px'>
            <img class='mw396' data-testid='cover-image' style='height:auto;width:200px; padding-top:10px; padding-bottom:10px' 
                 src='data:image/jpg;charset=utf8;base64," . base64_encode($rekod['IMAGE']) . "' />
            <img class='mw396' data-testid='cover-image' style='height:auto;width:200px; padding-top:10px; padding-bottom:10px' 
                 src='data:image/jpg;charset=utf8;base64," . base64_encode($rekod['SIDEIMAGES1']) . "' />
            <img class='mw396' data-testid='cover-image' style='height:auto;width:200px; padding-top:10px; padding-bottom:10px' 
                 src='data:image/jpg;charset=utf8;base64," . base64_encode($rekod['SIDEIMAGES2']) . "' />
            <img class='mw396' data-testid='cover-image' style='height:auto;width:200px; padding-top:10px; padding-bottom:10px' 
                 src='data:image/jpg;charset=utf8;base64," . base64_encode($rekod['SIDEIMAGES3']) . "' />
            <h5>Images ID : " . $rekod['IDIMG'] . "</h5>
            <div style='height: 40px; padding-top:5px'>
                <a class='w3-button w3-round w3-round-xlarge' style='margin-left:5px; background: #FFBF00' href='car_info.php?idimg=" . $rekod['IDIMG'] . "'>Select<br></a>
                <a href='delete.php?jadual=images&medan_kp=idimg&kp=" . $rekod['IDIMG'] . "' onClick=\"return confirm('Confirm delete this image?')\">Delete</a>
            </div>
        </div>
        </a>";
    }
} else {
    # Display a message if no images are found
    echo "<i><br><br>The car you are looking for may not be in stock or has been sold out.<br>You can continue searching next :)<br><br></i>";
}

# Free the statement resource
oci_free_statement($stid);
?>
