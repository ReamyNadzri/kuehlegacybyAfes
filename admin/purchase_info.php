<?php
# Include the admin header
include('header_admin.php');

# Include the connection file for Oracle Database
include('../connection.php');

# SQL Query to fetch cars that have been purchased
$query = "
    SELECT customer.customerName, 
           customer.customer_ID, 
           customer.customerTelNum, 
           car.numPlate, 
           car.carName, 
           car.carType, 
           model.modelName, 
           car.color, 
           car.yearManufac, 
           car.initialPrice, 
           purchase.purchaseDate, 
           purchase.deposit, 
           purchase.balancePayment
    FROM customer
    INNER JOIN purchase ON purchase.customer_ID = customer.customer_ID
    INNER JOIN car ON purchase.numPlate = car.numPlate
    INNER JOIN model ON car.model_ID = model.model_ID
";

# Execute the query using OCI8
$stid = oci_parse($condb, $query);
oci_execute($stid);
?>
<!-- Generate table header -->
<h4>List of Customers</h4>
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
    <?php 
    $bil = 0;

    # Loop through the result set
    while ($row = oci_fetch_assoc($stid)) {
        echo "
        <tr>
            <td>".++$bil."</td>
            <td>".$row['CUSTOMERNAME']."</td>
            <td>".$row['CUSTOMER_ID']."</td>
            <td>".$row['CUSTOMERTELNUM']."</td>
            <td>".$row['NUMPLATE']."</td>
            <td>".$row['CARNAME']."</td>
            <td>".$row['CARTYPE']."</td>
            <td>".$row['MODELNAME']."</td>
            <td>".$row['COLOR']."</td>
            <td>".$row['YEARMANUFAC']."</td>
            <td>".$row['INITIALPRICE']."</td>
            <td>".$row['PURCHASEDATE']."</td>
            <td>".$row['DEPOSIT']."</td>
            <td>".$row['BALANCEPAYMENT']."</td>
        </tr>";
    }

    # Free resources
    oci_free_statement($stid);
    ?>
</table>
<br>
<br>
<br>
