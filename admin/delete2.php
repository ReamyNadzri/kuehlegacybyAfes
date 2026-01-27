<?php
include('header_admin.php');
include('../connection.php');
//
# Check if necessary parameters are passed
if (isset($_GET['jadual']) && isset($_GET['medan_kp']) && isset($_GET['kp'])) {
    $jadual = $_GET['jadual'];  // Table name (ADMIN)
    $medan_kp = $_GET['medan_kp'];  // Field to match (USERNAME)
    $kp = $_GET['kp'];  // Value to delete (the admin's username)

    # SQL query to delete the record
    $arahan_sql_hapus = "DELETE FROM $jadual WHERE $medan_kp = ?";

    # Prepare the statement
    $stmt = mysqli_prepare($condb, $arahan_sql_hapus);
    mysqli_stmt_bind_param($stmt, 's', $kp);

    # Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Admin deleted successfully');
        window.location.href='admin_info.php';</script>";
    } else {
        echo "<script>alert('Failed to delete admin');
        window.history.back();</script>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<script>alert('Missing parameters');
    window.history.back();</script>";
}
