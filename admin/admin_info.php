<?php
include('header_admin.php');
include('../connection.php');

# Menyemak kewujudan data POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminName = $_POST['adminName'];
    $adminPhone = $_POST['adminPhone'];  // Should be EMAIL based on the table schema
    $adminPass = $_POST['adminPass'];

    # Ensure all fields are filled
    if (empty($adminName) || empty($adminPhone) || empty($adminPass)) {
        die("<script>alert('Please insert all the data');
        window.history.back();</script>");
    }

    # Insert query to save admin data
    $arahan_sql_simpan = "INSERT INTO ADMIN 
        (USERNAME, NAME, EMAIL, PASSWORD) 
        VALUES (:USERNAME, :NAME, :EMAIL, :PASSWORD)";

    $stmt = oci_parse($condb, $arahan_sql_simpan);
    oci_bind_by_name($stmt, ':USERNAME', $adminName);  // Using adminName as USERNAME
    oci_bind_by_name($stmt, ':NAME', $adminName);      // Using adminName as NAME
    oci_bind_by_name($stmt, ':EMAIL', $adminPhone);    // Using adminPhone as EMAIL
    oci_bind_by_name($stmt, ':PASSWORD', $adminPass);  // Using adminPass as PASSWORD

    # Execute the statement
    if (oci_execute($stmt)) {
        echo "<script>alert('Registration Success');
        window.location.href='admin_info.php';</script>";
    } else {
        echo "<script>alert('Registration Failure');
        window.history.back();</script>";
    }
}

# Fetch admin records
$arahan_sql_cari = "SELECT * FROM ADMIN";
$laksana_sql_cari = oci_parse($condb, $arahan_sql_cari);
oci_execute($laksana_sql_cari);
?>

<h4>List of Administrators</h4>
<table class="w3-table-all" id="saiz" border="1">
    <tr class="w3-light-blue">
        <td>Bil</td>
        <td>Username</td>
        <td>Name</td>
        <td>Email</td>
        <td>Password</td>
        <td>Actions</td>
    </tr>
    <tr>
        <form action='' method='POST'>
            <td>#</td>
            <td><input type='text' name='adminName' required></td>  <!-- Used for USERNAME -->
            <td><input type='text' name='adminName' required></td>  <!-- Used for NAME -->
            <td><input type='text' name='adminPhone' required></td> <!-- Used for EMAIL -->
            <td><input type='password' name='adminPass' required></td> <!-- Used for PASSWORD -->
            <td><input type="submit" value="Save" class="btn btn-success btn-sm"></td>

        </form>
    </tr>
    <?php
    $bil = 0;
    while ($rekod = oci_fetch_array($laksana_sql_cari, OCI_ASSOC + OCI_RETURN_NULLS)) {
        echo "
        <tr>
            <td>" . ++$bil . "</td>
            <td>" . $rekod['USERNAME'] . "</td>
            <td>" . $rekod['NAME'] . "</td>
            <td>" . $rekod['EMAIL'] . "</td>
            <td>" . $rekod['PASSWORD'] . "</td>
            <td>
                 <a href='delete.php?jadual=ADMIN&medan_kp=USERNAME&kp=" . $rekod['USERNAME'] . "' 
                onClick=\"return confirm('Confirm delete this admin?')\" class='btn btn-danger btn-sm'>Delete</a> 
                 <a href='admin_update.php?adminName=" . $rekod['USERNAME'] . "&adminPhone=" . $rekod['EMAIL'] . "&adminPass=" . $rekod['PASSWORD'] . "' 
                onClick=\"return confirm('Confirm update admin data?')\" class='btn btn-warning btn-sm'>Update</a> 
            </td>
        </tr>";
    }
    ?>
</table>
<br><br><br>
