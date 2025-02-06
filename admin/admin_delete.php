<?php
include('../connection.php');

if (isset($_GET['adminName'])) {
    $adminName = $_GET['adminName'];

    # SQL to delete the admin
    $sql_delete = "DELETE FROM ADMIN WHERE USERNAME = :USERNAME";
    $stmt = oci_parse($condb, $sql_delete);
    oci_bind_by_name($stmt, ':USERNAME', $adminName);

    if (oci_execute($stmt)) {
        echo "<script>alert('Admin deleted successfully!');
        window.location.href='admin_info.php';</script>";
    } else {
        echo "<script>alert('Failed to delete admin');
        window.history.back();</script>";
    }
}
?>
