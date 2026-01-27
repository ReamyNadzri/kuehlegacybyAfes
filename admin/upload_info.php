<?php
include('header_admin.php');
include('connection.php');

// SQL Query to count total users who uploaded data
$userCountSql = "SELECT COUNT(DISTINCT USERNAME) AS total_users FROM KUEH";
$userCountStmt = mysqli_prepare($condb, $userCountSql);
mysqli_stmt_execute($userCountStmt);
$result = mysqli_stmt_get_result($userCountStmt);
$userCountRow = mysqli_fetch_assoc($result);
$totalUsers = $userCountRow['total_users'];
mysqli_stmt_close($userCountStmt);

// SQL Query to count total kueh added
$kuehCountSql = "SELECT COUNT(*) AS total_kueh FROM KUEH";
$kuehCountStmt = mysqli_prepare($condb, $kuehCountSql);
mysqli_stmt_execute($kuehCountStmt);
$result2 = mysqli_stmt_get_result($kuehCountStmt);
$kuehCountRow = mysqli_fetch_assoc($result2);
$totalKueh = $kuehCountRow['total_kueh'];
mysqli_stmt_close($kuehCountStmt);

// SQL Query to retrieve kueh data 
$sql = "SELECT K.KUEHID, K.KUEHNAME, P.LEVEL, O.ORIGINCODE AS ORIGINID, 
               O.NAMESTATE AS STATE, P.POPULARID, K.IMAGE, 
               COALESCE(A.USERNAME, U.USERNAME) AS UPLOADED_BY
        FROM KUEH K
        LEFT JOIN POPULARITY P ON K.POPULARID = P.POPULARID
        LEFT JOIN ORIGIN O ON K.ORIGINID = O.ORIGINCODE
        LEFT JOIN USERS U ON K.USERNAME = U.USERNAME
        LEFT JOIN ADMIN A ON K.USERNAME = A.USERNAME";

$stmt = mysqli_prepare($condb, $sql);
mysqli_stmt_execute($stmt);
$kueh_result = mysqli_stmt_get_result($stmt);
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
    <div class="row mb-1">
        <div class="col-12 col-md-6 d-flex align-items-center justify-content-between">
            <p class="mb-0"><strong>Total Users who have uploaded data:</strong> <?php echo $totalUsers; ?></p>
        </div>
        <div class="col-12 col-md-6 d-flex align-items-center justify-content-between">
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
            <td>Image</td>
        </tr>

        <?php
        $bil = 0;
        while ($row = mysqli_fetch_assoc($kueh_result)) {

            $imagePath = !empty($row['IMAGE']) ? '../kueh_images/' . $row['IMAGE'] : null;

            // Show the admin or user who uploaded the kueh
            echo "<tr>
                    <td>" . ++$bil . "</td>
                    <td>" . (!empty($row['UPLOADED_BY']) ? $row['UPLOADED_BY'] : $_SESSION['admin_username']) . "</td>
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