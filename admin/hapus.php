<?PHP 
# menyemak kewujudan data GET
if(!empty($_GET))
{
    # Memanggil fail connection dari folder luaran
    include ('../connection.php');

    # Mengambil data GET
    $jadual = $_GET['jadual'];
    $medan_kp = $_GET['medan_kp'];
    $kp = $_GET['kp'];

    # Start transaction
    oci_execute(oci_parse($condb, "BEGIN TRANSACTION"));

    # Arahan force menghapuskan data
    $arahan_sql_check_pembelian = "SELECT COUNT(*) AS COUNT_ROWS FROM purchase WHERE $medan_kp = :kp";
    
    # Parse and execute check query
    $stmt_check = oci_parse($condb, $arahan_sql_check_pembelian);
    oci_bind_by_name($stmt_check, ":kp", $kp);
    oci_execute($stmt_check);
    $row = oci_fetch_array($stmt_check, OCI_ASSOC);

    if($row['COUNT_ROWS'] > 0) {
        echo"<script>
        let text = 'You cannot delete customer data who have already bought a car! Do you want to force delete customer data?';
        if (confirm(text) == true) {";

        #Arahan laksana paksaan penghapusan data di purchase
        $arahan_sql_paksaan_hapus = "DELETE FROM purchase WHERE $medan_kp = :kp";
        
        #melaksanakan proses hapus rekod secara paksa
        $stmt_force = oci_parse($condb, $arahan_sql_paksaan_hapus);
        oci_bind_by_name($stmt_force, ":kp", $kp);
        
        if(oci_execute($stmt_force)) {
            oci_commit($condb);
            echo"alert('Force Delete Purchase Record Successfully');";
            
            # Delete from main table
            $arahan_sql_hapus = "DELETE FROM $jadual WHERE $medan_kp = :kp";
            $stmt_hapus = oci_parse($condb, $arahan_sql_hapus);
            oci_bind_by_name($stmt_hapus, ":kp", $kp);
            
            if(oci_execute($stmt_hapus)) {
                oci_commit($condb);
                echo"window.location.href='buyer_info.php';</script>";
            } else {
                $e = oci_error($stmt_hapus);
                oci_rollback($condb);
                echo"alert('Error deleting from $jadual: " . htmlentities($e['message']) . "');</script>";
            }
        } else {
            $e = oci_error($stmt_force);
            oci_rollback($condb);
            echo"alert('Error force deleting: " . htmlentities($e['message']) . "');</script>";
        }
        
        # Free statements
        oci_free_statement($stmt_check);
        oci_free_statement($stmt_force);
        oci_free_statement($stmt_hapus);
    } else {
        # Direct delete if no purchase records
        $arahan_sql_hapus = "DELETE FROM $jadual WHERE $medan_kp = :kp";
        $stmt_hapus = oci_parse($condb, $arahan_sql_hapus);
        oci_bind_by_name($stmt_hapus, ":kp", $kp);
        
        if(oci_execute($stmt_hapus)) {
            oci_commit($condb);
            echo"<script>alert('Delete Record Successfully');
                 window.location.href='buyer_info.php';</script>";
        } else {
            $e = oci_error($stmt_hapus);
            oci_rollback($condb);
            echo"<script>alert('Error deleting: " . htmlentities($e['message']) . "');</script>";
        }
        oci_free_statement($stmt_hapus);
    }
    
    # Close connection
    oci_close($condb);
}
?>