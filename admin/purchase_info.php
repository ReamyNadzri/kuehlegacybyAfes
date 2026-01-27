<?php
include('header_admin.php');
include('connection.php');


$sql = "SELECT K.KUEHID, K.KUEHNAME, P.LEVEL, O.ORIGINCODE AS ORIGINID, O.NAMESTATE AS STATE, P.POPULARID, K.IMAGE, U.USERNAME 
        FROM USERS U
        JOIN FAVORITE F ON U.USERNAME = F.USERNAME
        JOIN KUEH K ON F.KUEHID = K.KUEHID
        JOIN POPULARITY P ON K.POPULARID = P.POPULARID
        JOIN ORIGIN O ON K.ORIGINID = O.ORIGINCODE";
$stmt = mysqli_prepare($condb, $sql);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
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
            <td>Image</td>
        </tr>

        <?php
        $bil = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $imagePath = !empty($row['IMAGE']) ? '../kueh_images/' . $row['IMAGE'] : null;
            echo "<tr>
                    <td>" . ++$bil . "</td>
                    <td>{$row['USERNAME']}</td>
                    <td>{$row['KUEHID']}</td>
                    <td>{$row['KUEHNAME']}</td>
                    <td>{$row['LEVEL']}</td>
                    <td>{$row['ORIGINID']}</td>
                    <td>{$row['STATE']}</td>
                    <td>{$row['POPULARID']}</td>
                    <td>";
            if ($imagePath && file_exists($imagePath)) {
                echo "<img src='{$imagePath}' alt='Kueh Image' width='100' height='100'>";
            } else {
                echo "No Image";
            }
            echo "</td></tr>";
        }
        ?>

    </table>

    <?php

    mysqli_stmt_close($stmt);
    mysqli_close($condb);
    ?>

</body>