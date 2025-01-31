<?php
include('header_admin.php');
include('connection.php');

// SQL Query to count total users who uploaded data
$userCountSql = "SELECT COUNT(DISTINCT USERNAME) AS total_users
                 FROM KUEH";

$userCountStmt = oci_parse($condb, $userCountSql);
oci_execute($userCountStmt);
$userCountRow = oci_fetch_assoc($userCountStmt);
$totalUsers = $userCountRow['TOTAL_USERS'];

// SQL Query to count total kueh added
$kuehCountSql = "SELECT COUNT(*) AS total_kueh
                 FROM KUEH";

$kuehCountStmt = oci_parse($condb, $kuehCountSql);
oci_execute($kuehCountStmt);
$kuehCountRow = oci_fetch_assoc($kuehCountStmt);
$totalKueh = $kuehCountRow['TOTAL_KUEH'];

// SQL Query to retrieve kueh data 
$sql = "SELECT K.KUEHID, K.KUEHNAME, P.LEVELSTAR, O.ORIGINCODE AS ORIGINID, 
               O.NAMESTATE AS STATE, P.POPULARID, P.RATING, K.IMAGE, 
               COALESCE(A.USERNAME, U.USERNAME) AS UPLOADED_BY
        FROM KUEH K
        LEFT JOIN POPULARITY P ON K.POPULARID = P.POPULARID
        LEFT JOIN ORIGIN O ON K.ORIGINID = O.ORIGINCODE
        LEFT JOIN USERS U ON K.USERNAME = U.USERNAME
        LEFT JOIN ADMIN A ON K.USERNAME = A.USERNAME";

$stmt = oci_parse($condb, $sql);
oci_execute($stmt);
?>

<body style="background-color: #FFFAF0;">
    <link rel="stylesheet" href="style.css">
    <title>Admin Panel - User Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <h4>Upload Info</h4>

    <!-- Bootstrap row and columns to display info side by side with less space between them -->
    <div class="row mb-4">
        <div class="col-12 col-md-6 d-flex align-items-center justify-content-start">
            <p class="mb-0"><strong>Total Users who have uploaded data:</strong> <?php echo $totalUsers; ?></p>
        </div>
        <div class="col-12 col-md-6 d-flex align-items-center justify-content-start">
            <p class="mb-0"><strong>Total Kueh added:</strong> <?php echo $totalKueh; ?></p>
        </div>
    </div>


    <table class="w3-table-all" id='saiz' border='1'>
        <tr class="w3-light-blue">
            <td>Bil</td>
            <td>Uploaded By</td>
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
            
            $imageData = $row['IMAGE'];
            $imageBase64 = "";
            if ($imageData instanceof OCILob) {
                $imageBase64 = base64_encode($imageData->load());
            }

            // Show the admin or user who uploaded the kueh
            echo "<tr>
                    <td>" . ++$bil . "</td>
                    <td>" . (!empty($row['UPLOADED_BY']) ? $row['UPLOADED_BY'] : $_SESSION['admin_username']) . "</td>
                    <td>{$row['KUEHID']}</td>
                    <td>{$row['KUEHNAME']}</td>
                    <td>{$row['LEVELSTAR']}</td>
                    <td>{$row['ORIGINID']}</td>
                    <td>{$row['STATE']}</td>
                    <td>{$row['POPULARID']}</td>
                    <td>{$row['RATING']}</td>
                    <td>";

            if (!empty($imageBase64)) {
                echo "<img src='data:image/jpeg;base64,{$imageBase64}' alt='Kueh Image' width='100' height='100'>";
            } else {
                echo "No Image";
            }

            echo "</td></tr>";
        }
        ?>

    </table>

    <?php
    oci_free_statement($stmt);
    oci_free_statement($userCountStmt);
    oci_free_statement($kuehCountStmt);
    oci_close($condb);
    ?>

</body>