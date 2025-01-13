<?PHP
# Memanggil fail header_admin.php
include('header_admin.php');
# Memanggil fail connection dari folder luaran
include('connection.php');

// Assuming $condb is your valid Oracle connection resource
// and has been included from connection.php

// ----------- bahagian 1 : memaparkan data dalam bentuk jadual

// arahan SQL mencari kereta yang masih belum dijual
$arahan_sql_cari = "
    SELECT k.*, t.TYPENAME, o.NAMESTATE, m.METHODNAME
    FROM kueh k
    LEFT JOIN FOODTYPE t ON k.FOODTYPECODE = t.FOODTYPECODE
    LEFT JOIN ORIGIN o ON k.ORIGINID = o.ORIGINCODE
    LEFT JOIN METHOD m ON k.METHODID = m.METHODID
";

// melaksanakan arahan sql cari tersebut
$laksana_sql_cari = oci_parse($condb, $arahan_sql_cari);

$execute_sql_cari = oci_execute($laksana_sql_cari);

// Note:  The results can be fetched now
// in a loop using oci_fetch_assoc()

// Example usage (fetching results in a loop):
/*
 while ($row = oci_fetch_assoc($laksana_sql_cari)) {
     // Access data using keys (e.g., $row['NUMPLATE'], $row['CARNAME'])
     // For example:
    echo $row['NUMPLATE'] . " - " . $row['CARNAME'] . "<br/>";
 }

 oci_free_statement($laksana_sql_cari);
 */
?>

<style>
    .adjust {
        width: 1000px
    }
</style>



<!-- menyediakan header bagi jadual -->
<!-- selepas header akan diselitkan dengan borang untuk mendaftar kereta baru -->

<div class="w3-container">
    <div class="w3-row">
        <div class="w3-col m6">
            <h4>LIST OF KUEH</h4>
        </div>
        <div class="w3-col m6">
            <a href="kueh_form.php" class="w3-button w3-blue w3-round-large w3-right">
                <i class="fas fa-plus"></i> Add New Kueh
            </a>
        </div>
    </div>
    <div class="w3-responsive">
        <table class="w3-table-all" id='saiz'>
            <thead>
                <tr class="w3-light-blue">
                    <th class="w3-center">Bil</th>
                    <th>Id</th>
                    <th>Name</th>
                    <th class="w3-hide-small">Description</th>
                    <th>Tag</th>
                    <th>Type</th>
                    <th>Origin</th>
                    <th>Method</th>
                    <th>Image</th>
                    <th class="w3-center">Details</th>
                    <th class="w3-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $bil = 0;
                while ($rekod = oci_fetch_assoc($laksana_sql_cari)) {
                    echo "<tr>
                        <td class='w3-center'>" . ++$bil . "</td>
                        <td>" . $rekod['KUEHID'] . "</td>
                        <td>" . $rekod['KUEHNAME'] . "</td>
                        <td class='w3-hide-small'>" . $rekod['KUEHDESC'] . "</td>
                        <td>" . $rekod['TAGKUEH'] . "</td>
                        <td>" . $rekod['TYPENAME'] . "</td>
                        <td>" . $rekod['ORIGIN'] . "</td>
                        <td>" . $rekod['METHODNAME'] . "</td>
                        <td>" . $rekod['IMAGE'] . "</td>
                        <td class='w3-center'>
                            <a href='kueh_edit_form.php?numPlate=" . $rekod['NUMPLATE'] . "' class='w3-button w3-small w3-round w3-amber'>View</a>
                        </td>
                        <td>
                            <a href='hapus.php?jadual=CAR&medan_kp=NUMPLATE&kp=" . $rekod['NUMPLATE'] . "' onClick=\"return confirm('Confirm to delete data?')\" 
                                class='w3-button w3-small w3-round w3-red'>Delete</a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<br>
<br>
<br>