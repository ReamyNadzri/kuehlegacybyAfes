<?php
// Include the database connection
include('connection.php');

// Check if the required parameters are passed
if (isset($_GET['jadual']) && isset($_GET['medan_kp']) && isset($_GET['kp'])) {
    $jadual = htmlspecialchars($_GET['jadual']); // Table name
    $medan_kp = htmlspecialchars($_GET['medan_kp']); // Primary key column name
    $kp = htmlspecialchars($_GET['kp']); // Primary key value

    // Get image filename before deletion for cleanup
    if ($jadual === 'kueh') {
        $check_sql = "SELECT image FROM kueh WHERE kuehId = ?";
        $check_stmt = mysqli_prepare($condb, $check_sql);
        mysqli_stmt_bind_param($check_stmt, 's', $kp);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $image_row = mysqli_fetch_assoc($check_result);

        // Delete the image file if it exists
        if ($image_row && !empty($image_row['image'])) {
            $image_path = __DIR__ . '/kueh_images/' . $image_row['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        mysqli_stmt_close($check_stmt);
    }

    // Prepare the SQL DELETE query
    $sql_delete = "DELETE FROM $jadual WHERE $medan_kp = ?";

    $stmt = mysqli_prepare($condb, $sql_delete);
    mysqli_stmt_bind_param($stmt, 's', $kp);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
        	window.location.href='userProfile.php?msg=delete_success';
    	</script>";
    } else {
        echo "<script>alert('Error deleting: " . htmlentities(mysqli_error($condb)) . "');</script>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Invalid request.";
}

mysqli_close($condb);
