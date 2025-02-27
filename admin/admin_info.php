<?php

include('header_admin.php');
include('../connection.php');


$loggedInAdmin = $_SESSION['adminid'] ?? null;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminName = $_POST['adminName'];
    $adminEmail = $_POST['adminEmail']; 
    $adminPass = $_POST['adminPass'];

    // Ensure all fields are filled
    if (empty($adminName) || empty($adminEmail) || empty($adminPass)) {
        die("<script>alert('Please insert all the data');
        window.history.back();</script>");
    }

    // Validate email format
    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        die("<script>alert('Invalid email format. Please enter a valid email.');
        window.history.back();</script>");
    }

    // Check if username and email are the same
    if ($adminName == $adminEmail) {
        die("<script>alert('Username and Email cannot be the same.');
        window.history.back();</script>");
    }

    // Insert query to save admin data
    $arahan_sql_simpan = "INSERT INTO ADMIN 
        (USERNAME, NAME, EMAIL, PASSWORD) 
        VALUES (:USERNAME, :NAME, :EMAIL, :PASSWORD)";

    $stmt = oci_parse($condb, $arahan_sql_simpan);
    oci_bind_by_name($stmt, ':USERNAME', $adminName);  
    oci_bind_by_name($stmt, ':NAME', $adminName);      
    oci_bind_by_name($stmt, ':EMAIL', $adminEmail);    
    oci_bind_by_name($stmt, ':PASSWORD', $adminPass);  

    // Execute the statement
    if (oci_execute($stmt)) {
        echo "<script>alert('Registration Success');
        window.location.href='admin_info.php';</script>";
    } else {
        echo "<script>alert('Registration Failure');
        window.history.back();</script>";
    }
}


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
        <form action="" method="POST">
            <td>#</td>
            <td><input type="text" name="adminName" required></td> 
            <td><input type="text" name="adminName" required></td> 
            <td><input type="email" name="adminEmail" required></td> 
            <td><input type="password" id="adminPass" name="adminPass" required></td> 
            <td><input type="submit" value="Save" class="btn btn-success btn-sm"></td>
        </form>
    </tr>
    <?php
    $bil = 0;
    while ($rekod = oci_fetch_array($laksana_sql_cari, OCI_ASSOC + OCI_RETURN_NULLS)) {
       
        $deleteButton = ($rekod['USERNAME'] == $loggedInAdmin) ? "" : "<a href='admin_delete.php?adminName=" . urlencode($rekod['USERNAME']) . "' 
                onClick=\"return confirm('Are you sure you want to delete this admin?')\" 
                class='btn btn-danger btn-sm'>Delete</a>";

        echo "
        <tr>
            <td>" . ++$bil . "</td>
            <td>" . $rekod['USERNAME'] . "</td>
            <td>" . $rekod['NAME'] . "</td>
            <td>" . $rekod['EMAIL'] . "</td>
            <td>" . str_repeat("•", strlen($rekod['PASSWORD'])) . "</td> 
            <td>
                $deleteButton

                <a href='admin_update.php?adminName=" . urlencode($rekod['USERNAME']) . "&adminEmail=" . urlencode($rekod['EMAIL']) . "' 
                onClick=\"return confirm('Confirm update admin data?')\" 
                class='btn btn-warning btn-sm'>Update</a> 
            </td>
        </tr>";
    }
    ?>
</table>

<!-- Script for toggling password visibility -->
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('adminPass');
        const toggleIcon = document.getElementById('toggle-icon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>

<br><br><br>
