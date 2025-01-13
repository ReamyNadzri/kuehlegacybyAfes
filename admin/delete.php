<?php
# menyemak kewujudan data GET
if (!empty($_GET)) {
    # Memanggil fail connection dari folder luaran
    include('../connection.php');

    # Mengambil data GET
    $jadual = $_GET['jadual'];
    $medan_kp = $_GET['medan_kp'];
    $kp = $_GET['kp'];

    # Step 1: Fetch the Image ID associated with the car
    $sql_fetch_image_id = "
        SELECT IDIMG FROM $jadual WHERE $medan_kp = :kp
    ";
    $stmt_fetch_image_id = oci_parse($condb, $sql_fetch_image_id);
    oci_bind_by_name($stmt_fetch_image_id, ':kp', $kp);
    oci_execute($stmt_fetch_image_id);

    $row = oci_fetch_assoc($stmt_fetch_image_id);
    $idimg = $row['IDIMG'];

    # Step 2: Delete the Car Record
    $arahan_sql_hapus = "
        DELETE FROM $jadual WHERE $medan_kp = :kp
    ";

    # Melaksanakan arahan SQL
    $laksana_arahan = oci_parse($condb, $arahan_sql_hapus);

    # Bind parameter untuk mengelakkan SQL Injection
    oci_bind_by_name($laksana_arahan, ':kp', $kp);

    # Laksana arahan SQL dalam syarat IF
    if (oci_execute($laksana_arahan, OCI_COMMIT_ON_SUCCESS)) {
        # Step 3: Delete the Image Record
        $sql_delete_image = "
            DELETE FROM IMAGES WHERE IDIMG = :idimg
        ";
        $stmt_delete_image = oci_parse($condb, $sql_delete_image);
        oci_bind_by_name($stmt_delete_image, ':idimg', $idimg);

        if (oci_execute($stmt_delete_image, OCI_COMMIT_ON_SUCCESS)) {
            oci_free_statement($stmt_delete_image);
            oci_free_statement($laksana_arahan);
            oci_close($condb);
            echo "<script>alert('Data and associated image deleted successfully');</script>";
            echo "<script>window.location.href='car_info.php';</script>";
        } else {
            echo "<script>alert('Image deletion failed');
            window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Delete Failure');
        window.history.back();</script>";
    }
}
?>