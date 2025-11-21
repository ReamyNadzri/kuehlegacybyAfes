<?php
// Database connection details
$db_user = "kuehlegacy";
$db_password = "kuehlegacy";
$db_connection_string = "localhost:1521/xe"; // e.g., "localhost/XE"

// Connect to the Oracle database
$conn = oci_connect($db_user, $db_password, $db_connection_string);
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Directory containing the images
$image_directory = "../sources/images/";

// Get all image files from the directory
$image_files = glob($image_directory . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);

// Loop through each image file and update the corresponding record in the database
foreach ($image_files as $image_file) {
    // Read the image file as binary data
    $image_data = file_get_contents($image_file);

    // Get the file name (used to identify the record to update)
    $file_name = basename($image_file);

    // Prepare the SQL statement to update the BLOB
    $sql = "UPDATE KUEH
            SET IMAGE = EMPTY_BLOB() 
            WHERE file_name = :file_name 
            RETURNING image_data INTO :image_data";
    $stmt = oci_parse($conn, $sql);

    // Bind the parameters
    $blob = oci_new_descriptor($conn, OCI_D_LOB); // Create a BLOB descriptor
    oci_bind_by_name($stmt, ":file_name", $file_name);
    oci_bind_by_name($stmt, ":image_data", $blob, -1, OCI_B_BLOB);

    // Execute the statement
    if (oci_execute($stmt, OCI_DEFAULT)) {
        // Save the binary data to the BLOB
        if ($blob->save($image_data)) {
            oci_commit($conn); // Commit the transaction
            echo "Image '$file_name' updated successfully.<br>";
        } else {
            oci_rollback($conn); // Rollback on failure
            echo "Failed to save BLOB for '$file_name'.<br>";
        }
    } else {
        $e = oci_error($stmt);
        echo "Error updating '$file_name': " . $e['message'] . "<br>";
    }

    // Free resources
    $blob->free();
    oci_free_statement($stmt);
}

// Close the database connection
oci_close($conn);
?>