<?PHP
# Memanggil fail header_admin.php
include('header_admin.php');
# Memanggil fail connection dari folder luaran
include('../connection.php');

# menyemak kewujudan data POST
if (!empty($_POST)) {
    $tambahan = "AND purchaseDate like '%" . $_POST['month'] . "%'";
} else {
    $tambahan = " ";
}

# arahan SQL untuk mencari data penjualan mengikut month
$arahan_sql_cari = "SELECT * FROM CUSTOMER, PURCHASE, CAR, MODEL
WHERE PURCHASE.CUSTOMER_ID = CUSTOMER.CUSTOMER_ID AND
PURCHASE.NUMPLATE = CAR.NUMPLATE AND
CAR.MODEL_ID = MODEL.MODEL_ID AND
PURCHASE.NUMPLATE = CAR.NUMPLATE $tambahan";

# melaksanakan arahan SQL mencari data penjualan 
$laksana_sql_cari = oci_parse($condb, $arahan_sql_cari);
oci_execute($laksana_sql_cari);
?>

<!-- Menyediakan form untuk memilih month-->
<h1>List of Customer</h1>
<form class="w3-container w3-light-blue w3-input" action='' method='POST' style="padding-top: 20px;">
    <select name='month'>
        <option value selected disabled>Pilih bulan tarikh masuk</option>
        <option value='-01-'>January</option>
        <option value='-02-'>February</option>
        <option value='-03-'>March</option>
        <option value='-04-'>April</option>
        <option value='-05-'>May</option>
        <option value='-06-'>June</option>
        <option value='-07-'>July</option>
        <option value='-08-'>August</option>
        <option value='-09-'>September</option>
        <option value='-10-'>October</option>
        <option value='-11-'>November</option>
        <option value='-12-'>Disember</option>
    </select>

    <input class="w3-button w3-round-xlarge w3-green" type='submit' value='Search' style="margin-left: 20px;">
    <br><br>
</form>

<!-- menyediakan header untuk jadual yang hendak memaparkan data yang dicari -->

<table class="w3-table-all" id='saiz' border='1'>
    <tr class="w3-light-blue">
        <td style="text-align: center;">Bil</td>
        <td style="text-align: center;">Name</td>
        <td style="text-align: center;">Customer ID</td>
        <td style="text-align: center;">Phone Number</td>
        <td style="text-align: center;">Plate Number</td>
        <td style="text-align: center;">Car Name</td>
        <td style="text-align: center; width: 150px;">Car Type</td>
        <td style="text-align: center;">Model</td>
        <td style="text-align: center;">Colour</td>
        <td style="text-align: center;">Year Manufacture</td>
        <td style="text-align: center;">Initial Price</td>
        <td style="text-align: center; width: 117px;">Purchase Date</td>
        <td style="text-align: center;">Deposit</td>
        <td style="text-align: center;">Balance Payment</td>
    </tr>
    <?PHP
    $bil = 0;
    # pembolehubah $rekod mengambil data yang dicari
    while ($rekod = oci_fetch_array($laksana_sql_cari, OCI_ASSOC + OCI_RETURN_NULLS)) {
        echo "
    <tr>
        <td>" . ++$bil . "</td>
        <td>" . $rekod['CUSTOMERNAME'] . "</td>
        <td>" . $rekod['CUSTOMER_ID'] . "</td>
        <td>" . $rekod['CUSTOMERTELNUM'] . "</td>
        <td>" . $rekod['NUMPLATE'] . "</td>
        <td>" . $rekod['CARNAME'] . "</td>
        <td>" . $rekod['CARTYPE'] . "</td>
        <td>" . $rekod['MODELNAME'] . "</td>
        <td>" . $rekod['COLOR'] . "</td>
        <td>" . $rekod['YEARMANUFAC'] . "</td>
        <td>" . $rekod['INITIALPRICE'] . "</td>
        <td>" . $rekod['PURCHASEDATE'] . "</td>
        <td>" . $rekod['DEPOSIT'] . "</td>
        <td>" . $rekod['BALANCEPAYMENT'] . "</td>
    </tr>";
    }
    ?>
</table>
<br>
<br>
<br>