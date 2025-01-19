<?php
include('header_admin.php');
include('connection.php');


$sql = "SELECT K.KUEHID, K.KUEHNAME, P.LEVELSTAR, O.ORIGINCODE AS ORIGINID, O.NAMESTATE AS STATE, P.POPULARID, P.RATING, K.IMAGE, U.USERNAME 
        FROM USERS U
        JOIN FAVORITE F ON U.USERNAME = F.USERNAME
        JOIN KUEH K ON F.KUEHID = K.KUEHID
        JOIN POPULARITY P ON K.POPULARID = P.POPULARID
        JOIN ORIGIN O ON K.ORIGINID = O.ORIGINCODE";
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
            <td>Kueh ID</td>
            <td>Kueh Name</td>
            <td>Level Star</td>
            <td>Origin ID</td>
            <td>State</td>
            <td>Popularity ID</td>
            <td>Rating</td>
            <td>Image</td>
        </tr>

        <?php
        $bil = 0;
       
        while ($row = oci_fetch_assoc($stmt)) {
            echo "<tr>
                    <td>" . ++$bil . "</td>
                    <td>{$row['USERNAME']}</td>
                    <td>{$row['KUEHID']}</td>
                    <td>{$row['KUEHNAME']}</td>
                    <td>{$row['LEVELSTAR']}</td>
                    <td>{$row['ORIGINID']}</td>
                    <td>{$row['STATE']}</td>
                    <td>{$row['POPULARID']}</td>
                    <td>{$row['RATING']}</td>
                    <td><img src='data:image/jpeg;base64," . base64_encode($row['IMAGE']) . "' alt='Kueh Image' width='100' height='100'></td>
                </tr>";
        }
        ?>

    </table>

    <?php
   
    oci_free_statement($stmt);
    oci_close($condb);
    ?>

</body>
