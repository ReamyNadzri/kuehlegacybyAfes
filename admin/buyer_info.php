<?PHP
# Memanggil fail header_admin.php
include ('header_admin.php');
# Memanggil fail connection dari folder luaran
include ('../connection.php');

# arahan SQL mencari kereta yang masih belum dijual
$arahan_sql_cari="SELECT * FROM customer";
# Melaksanakan arahan SQL mencari kereta yang masih belum dijual
$stmt = oci_parse($condb, $arahan_sql_cari);
oci_execute($stmt);
?>
<!-- menyediakan header bagi jadual -->
<h4>Customer List</h4>
<table class="w3-table-all" id='saiz' border='1'>
    <tr class="w3-light-blue">
        <td>Bil</td>
        <td>Customer</td>
        <td>ID</td>
        <td>Phone Number</td>
        <td></td>
    </tr>
    <?PHP  
    $bil=0;
    # pemboleh ubah $rekod mengambail semua data yang ditemui oleh $stmt    
    while ($rekod = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS))
    {
        # sistem akan memaparkan data $rekod baris demi baris sehingga habis
        echo "
        <tr>
            <td>".++$bil."</td>
            <td>".$rekod['CUSTOMERNAME']."</td>
            <td>".$rekod['CUSTOMER_ID']."</td>
            <td>".$rekod['CUSTOMERTELNUM']."</td>
            <td><a href='hapus.php?jadual=customer&medan_kp=customer_ID&kp=".$rekod['CUSTOMER_ID']."' onClick=\"return confirm('Confirm delete this item??')\" >Delete</a></td>
        </tr>";
    }
    ?>
</table>
<br>
<br>
<br>