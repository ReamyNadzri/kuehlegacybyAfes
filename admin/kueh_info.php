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
    SELECT k.*, t.TYPENAME, o.NAMESTATE AS ORIGIN, m.METHODNAME
    FROM kueh k
    LEFT JOIN FOODTYPE t ON k.FOODTYPECODE = t.FOODTYPECODE
    LEFT JOIN ORIGIN o ON k.ORIGINID = o.ORIGINCODE
    LEFT JOIN METHOD m ON k.METHODID = m.METHODID
";

// melaksanakan arahan sql cari tersebut
$laksana_sql_cari = oci_parse($condb, $arahan_sql_cari);
$execute_sql_cari = oci_execute($laksana_sql_cari);

?>

<style>
    .adjust {
        width: 1000px;
    }

    img {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }
</style>

<div class="w3-container">
    <div class="w3-row">
        <div class="w3-col m6">
            <h4>LIST OF KUEH</h4>
        </div>
        <div class="w3-col m6">
            <a href="kueh_form.php" class="w3-button w3-blue w3-round-large w3-right mb-3">
                <i class="fas fa-plus"></i> Add New Kueh
            </a>
        </div>
    </div>
    <div class="w3-responsive">
        <table class="w3-table-all" id='saiz'>
            <thead>
                <tr class="w3-light-blue">
                    <th class="w3-center">Bil</th>
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
                <?php
                $bil = 0;
                while ($rekod = oci_fetch_assoc($laksana_sql_cari)) {
                    // Fetch image BLOB data and convert it to base64
                    $imageData = $rekod['IMAGE'];
                    $base64Image = '';
                    if ($imageData instanceof OCILob && $imageData->size() > 0) {
                        $base64Image = base64_encode($imageData->load());
                    }

                    echo "<tr>
                        <td class='w3-center'>" . ++$bil . "</td>
                        <td>" . htmlspecialchars($rekod['KUEHNAME']) . "</td>
                        <td class='w3-hide-small'>" . htmlspecialchars($rekod['KUEHDESC']) . "</td>
                        <td>" . htmlspecialchars($rekod['TAGKUEH']) . "</td>
                        <td>" . htmlspecialchars($rekod['TYPENAME']) . "</td>
                        <td>" . htmlspecialchars($rekod['ORIGIN']) . "</td>
                        <td>" . htmlspecialchars($rekod['METHODNAME']) . "</td>
                        <td>";
                    if ($base64Image) {
                        echo "<img src='data:image/jpeg;base64," . $base64Image . "' alt='Kueh Image'>";
                    } else {
                        echo "No Image";
                    }
                    echo "</td>
                        <td class='w3-center' style='vertical-align: middle;'>
                            <a href='kueh_edit_form.php?kuehId=" . htmlspecialchars($rekod['KUEHID']) . "' class='w3-button w3-small w3-round w3-amber'>View</a>
                        </td>
                        <td class='w3-center' style='vertical-align: middle;'>
                            <a href='hapus.php?jadual=KUEH&medan_kp=KUEHID&kp=" . htmlspecialchars($rekod['KUEHID']) . "' onClick=\"return confirm('Confirm to delete data?')\" 
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