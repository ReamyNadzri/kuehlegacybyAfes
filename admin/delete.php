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
    $sql_fetch_image_id = "SELECT IDIMG FROM $jadual WHERE $medan_kp = ?";
    $stmt_fetch_image_id = mysqli_prepare($condb, $sql_fetch_image_id);
    mysqli_stmt_bind_param($stmt_fetch_image_id, 's', $kp);
    mysqli_stmt_execute($stmt_fetch_image_id);
    $result = mysqli_stmt_get_result($stmt_fetch_image_id);
    $row = mysqli_fetch_assoc($result);
    $idimg = $row['IDIMG'];
    mysqli_stmt_close($stmt_fetch_image_id);

    # Step 2: Delete the Car Record
    $arahan_sql_hapus = "DELETE FROM $jadual WHERE $medan_kp = ?";

    # Melaksanakan arahan SQL
    $laksana_arahan = mysqli_prepare($condb, $arahan_sql_hapus);
    mysqli_stmt_bind_param($laksana_arahan, 's', $kp);

    # Laksana arahan SQL dalam syarat IF
    if (mysqli_stmt_execute($laksana_arahan)) {
        # Step 3: Delete the Image Record
        $sql_delete_image = "DELETE FROM IMAGES WHERE IDIMG = ?";
        $stmt_delete_image = mysqli_prepare($condb, $sql_delete_image);
        mysqli_stmt_bind_param($stmt_delete_image, 's', $idimg);

        if (mysqli_stmt_execute($stmt_delete_image)) {
            mysqli_stmt_close($stmt_delete_image);
            mysqli_stmt_close($laksana_arahan);
            mysqli_close($condb);
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
