<?php
include('header_admin.php');
include('connection.php');

// Fetch all users from the database
$sql = "SELECT USERNAME, PASSWORD, EMAIL, PHONENUM, NAME FROM USERS";
$stmt = oci_parse($condb, $sql);
oci_execute($stmt);
?>

<body class="" style="background-color: #FFFAF0;">
    <link rel="stylesheet" href="style.css">
    <title>Admin Panel - User Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <h4>User List</h4>
    <table class="w3-table-all" id='saiz' border='1'>
        <tr class="w3-light-blue">
            <td>Bil</td>
            <td>Username</td>
            <td>Email</td>
            <td>Phone Number</td>
            <td>Password</td>
            <td>Full Name</td>
            
        </tr>
        <?php
        $bil = 0;
        while ($row = oci_fetch_assoc($stmt)) {
            echo "<tr>
                    <td>" . ++$bil . "</td>
                    <td>{$row['USERNAME']}</td>
                    <td>{$row['EMAIL']}</td>
                    <td>{$row['PHONENUM']}</td>
                    <td>{$row['PASSWORD']}</td>
                    <td>{$row['NAME']}</td>
                  
                </tr>";
        }
        ?>
    </table>

    <?php
    // Close the database connection
    oci_free_statement($stmt);
    oci_close($condb);
    ?>

</body>