<?php
// Include the database connection
include('connection.php');

// Check if the required parameters are passed
if (isset($_GET['jadual']) && isset($_GET['medan_kp']) && isset($_GET['kp'])) {
    $jadual = htmlspecialchars($_GET['jadual']); // Table name
    $medan_kp = htmlspecialchars($_GET['medan_kp']); // Primary key column name
    $kp = htmlspecialchars($_GET['kp']); // Primary key value

    // Prepare the SQL DELETE query
    $sql_delete = "DELETE FROM $jadual WHERE $medan_kp = :kp";

    // Parse the query
    $stmt = oci_parse($condb, $sql_delete);

    // Bind the primary key value
    oci_bind_by_name($stmt, ":kp", $kp);

    // Execute the query
    if (oci_execute($stmt)) {
        oci_commit($condb);
        echo "<script>
        	window.location.href='kueh_info.php?msg=delete_success';
    	</script>";
    } else {
        $e = oci_error($stmt);
        oci_rollback($condb);
        echo "<script>alert('Error deleting: " . htmlentities($e['message']) . "');</script>";
    }

    // Free the statement resource
    oci_free_statement($stmt);
} else {
    echo "Invalid request.";
}

// Close the database connection
oci_close($condb);
