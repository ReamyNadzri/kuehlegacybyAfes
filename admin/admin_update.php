<!-- Memanggil fail header_admin.php -->
<?PHP include('header_admin.php'); ?>
<!-- menyediakan borang untuk mengemaskini data admin-->
<form action='' method='POST'>
    New Admin Name : <input type='text' name='nama_baru' value='<?php echo $_GET['adminName']; ?>'><br><br>
    New Admin ID : <input type='text' name='nokp_baru' value='<?php echo $_GET['admin_ID']; ?>'><br><br>
    New Phone Number : <input type='text' name='notel_baru' value='<?php echo $_GET['adminPhone']; ?>'><br><br>
    New Password : <input type='password' name='katalaluan_baru' value='<?php echo $_GET['adminPass']; ?>'><br><br>
    <input class="w3-button w3-light-blue" type='submit' value='Update'>
</form>

<?PHP
# menyemak kewujudan data POST
if (!empty($_POST)) {
    # Memanggil fail connection dari folder luaran
    include('../connection.php');

    # mengambil data POST
    $nama_baru = trim($_POST['nama_baru']);
    $nokp_baru = trim($_POST['nokp_baru']);
    $notel_baru = trim($_POST['notel_baru']);
    $katalaluan_baru = trim($_POST['katalaluan_baru']);
    $nokp_lama = trim($_GET['admin_ID']);

    # Start transaction
    oci_execute(oci_parse($condb, "BEGIN TRANSACTION"));

    # Arahan untuk mengemaskini data ke dalam jadual admin
    $arahan_sql_update = "UPDATE ADMIN 
     SET ADMIN_ID = :NOKP_BARU, 
         ADMINNAME = :NAMA_BARU, 
         ADMINPASS = :KATALALUAN_BARU, 
         ADMINPHONE = :NOTEL_BARU 
     WHERE ADMIN_ID = :NOKP_LAMA";

    # melaksanakan proses mengemaskini dalam syarat IF
    $stmt = oci_parse($condb, $arahan_sql_update);
    oci_bind_by_name($stmt, ':NOKP_BARU', $nokp_baru);
    oci_bind_by_name($stmt, ':NAMA_BARU', $nama_baru);
    oci_bind_by_name($stmt, ':KATALALUAN_BARU', $katalaluan_baru);
    oci_bind_by_name($stmt, ':NOTEL_BARU', $notel_baru);
    oci_bind_by_name($stmt, ':NOKP_LAMA', $nokp_lama);

    if (oci_execute($stmt)) {
        # Commit transaction
        oci_commit($condb);
        oci_free_statement($stmt);
        oci_close($condb);
        # peroses mengemaskini berjaya.
        echo "<script>alert('Update Success');
        window.location.href='admin_info.php';
        </script>";
    } else {
        # proses mengemaskini gagal
        echo "<script>alert('Update Failure');
        window.history.back();</script>";
    }
}

?>