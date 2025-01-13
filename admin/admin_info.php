<?php
include('header_admin.php');
include('../connection.php');

# Menyemak kewujudan data GET
if (!empty($_GET)) {
    $adminName = $_GET['adminName'];
    $admin_ID = $_GET['admin_ID'];
    $adminPhone = $_GET['adminPhone'];
    $adminPass = $_GET['adminPass'];

    if (empty($adminName) or empty($adminPhone) or empty($admin_ID) or empty($adminPass)) {
        die("<script>alert('Please insert all the data');
        window.history.back();</script>");
    }

    if (strlen($admin_ID) != 12 or !is_numeric($admin_ID)) {
        die("<script>alert('Wrong Admin ID');
        window.history.back();</script>");
    }

    $arahan_sql_simpan = "INSERT INTO admin 
        (ADMINNAME, ADMIN_ID, ADMINPHONE, ADMINPASS) 
        VALUES (:ADMINNAME, :ADMIN_ID, :ADMINPHONE, :ADMINPASS)";

    $stmt = oci_parse($condb, $arahan_sql_simpan);
    oci_bind_by_name($stmt, ':ADMINNAME', $adminName);
    oci_bind_by_name($stmt, ':ADMIN_ID', $admin_ID);
    oci_bind_by_name($stmt, ':ADMINPHONE', $adminPhone);
    oci_bind_by_name($stmt, ':ADMINPASS', $adminPass);

    if (oci_execute($stmt)) {
        echo "<script>alert('Registration Success');
        window.location.href='admin_info.php';</script>";
    } else {
        echo "<script>alert('Registration Failure');
        window.history.back();</script>";
    }
}

$arahan_sql_cari = "SELECT * FROM admin";
$laksana_sql_cari = oci_parse($condb, $arahan_sql_cari);
oci_execute($laksana_sql_cari);
# bahagian 1 : memaparkan data dalam bentuk jadual
# arahan SQL mencari kereta yang masih belum dijual
$arahan_sql_cari = "SELECT * FROM admin";
# melaksanakan arahan sql cari tersebut
$laksana_sql_cari = oci_parse($condb, $arahan_sql_cari);
oci_execute($laksana_sql_cari);
?>



<!-- menyediakan header bagi jadual -->
<!-- selepas header akan diselitkan dengan borang untuk mendaftar kereta baru -->

<h4>List of administrator</h4>
<table class="w3-table-all" id='saiz' border='1'>
    <tr class="w3-light-blue">
        <td>Bil</td>
        <td>Admin Name</td>
        <td>Admin ID</td>
        <td>Phone Number</td>
        <td>Password</td>
        <td></td>
    </tr>
    <tr>
        <form action='' method='GET'>
            <td>#</td>
            <td><input type='text' name='adminName'></td>
            <td><input type='text' name='admin_ID'></td>
            <td><input type='text' name='adminPhone'></td>
            <td><input type='password' name='adminPass'></td>
            <td><input type='submit' value='Save'></td>
        </form>
    </tr>
    <?PHP
    $bil = 0;
    while ($rekod = oci_fetch_array($laksana_sql_cari, OCI_ASSOC + OCI_RETURN_NULLS)) {
        echo "
        <tr>
            <td>" . ++$bil . "</td>
            <td>" . $rekod['ADMINNAME'] . "</td>
            <td>" . $rekod['ADMIN_ID'] . "</td>
            <td>" . $rekod['ADMINPHONE'] . "</td>
            <td>" . $rekod['ADMINPASS'] . "</td>
            <td>| <a href='delete.php?jadual=admin&medan_kp=admin_ID&kp=" . $rekod['ADMIN_ID'] . "' 
                onClick=\"return confirm('Confirm delete this admin?')\" >Delete</a> |
                | <a href='admin_update.php?adminName=" . $rekod['ADMINNAME'] . "&admin_ID=" . $rekod['ADMIN_ID'] . "
                &adminPhone=" . $rekod['ADMINPHONE'] . "&adminPass=" . $rekod['ADMINPASS'] . "' 
                onClick=\"return confirm('Confirm update admin data?')\" >Update</a> |</td>
        </tr>";
    }
    ?>
</table>
<br>
<br>
<br>