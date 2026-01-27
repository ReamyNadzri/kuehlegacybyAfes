<?php
include('../connection.php');

if (isset($_GET['adminName'])) {
    $adminName = $_GET['adminName'];

    # SQL to delete the admin --
    $sql_delete = "DELETE FROM ADMIN WHERE USERNAME = ?";
    $stmt = mysqli_prepare($condb, $sql_delete);
    mysqli_stmt_bind_param($stmt, 's', $adminName);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Admin deleted successfully!');
        window.location.href='admin_info.php';</script>";
    } else {
        echo "<script>alert('Failed to delete admin');
        window.history.back();</script>";
    }
    mysqli_stmt_close($stmt);
}
