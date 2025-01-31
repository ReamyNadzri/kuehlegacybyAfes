<?php
include('header_admin.php');
include('../connection.php');


$adminName = $_GET['adminName'];


$arahan_sql_cari = "SELECT * FROM ADMIN WHERE USERNAME = :USERNAME";
$stmt = oci_parse($condb, $arahan_sql_cari);
oci_bind_by_name($stmt, ':USERNAME', $adminName);
oci_execute($stmt);

// Fetch the result
$admin = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS);
if (!$admin) {
    die("<script>alert('Admin not found'); window.history.back();</script>");
}

// Handle form submission for updating admin data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminName = $_POST['adminName'];
    $adminPhone = $_POST['adminPhone'];  
    $adminPass = $_POST['adminPass'];   

    // Ensure all fields are filled
    if (empty($adminName) || empty($adminPhone) || empty($adminPass)) {
        die("<script>alert('Please insert all the data');
        window.history.back();</script>");
    }

    // Update query to save the updated admin data
    $arahan_sql_update = "UPDATE ADMIN 
        SET USERNAME = :USERNAME, NAME = :NAME, EMAIL = :EMAIL, PASSWORD = :PASSWORD 
        WHERE USERNAME = :OLD_USERNAME";

    $stmt_update = oci_parse($condb, $arahan_sql_update);
    oci_bind_by_name($stmt_update, ':USERNAME', $adminName);
    oci_bind_by_name($stmt_update, ':NAME', $adminName);    
    oci_bind_by_name($stmt_update, ':EMAIL', $adminPhone);
    oci_bind_by_name($stmt_update, ':PASSWORD', $adminPass);
    oci_bind_by_name($stmt_update, ':OLD_USERNAME', $admin['USERNAME']);  

    // Execute the statement
    if (oci_execute($stmt_update)) {
        echo "<script>alert('Update Success');
        window.location.href='admin_info.php';</script>";
    } else {
        echo "<script>alert('Update Failure');
        window.history.back();</script>";
    }
}
?>

<body style="background-color: #FFFAF0;">
    <link rel="stylesheet" href="style.css">
    <title>Edit Administrator Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <h4>Edit Admin Information</h4> <br><br>

    <!-- Edit Form inside a Table -->
    <form method="POST" action="">
        <div class="container">
            <table class="table table-bordered">
                <tr>
                    <th>Username</th>
                    <td><input type="text" id="adminName" name="adminName" class="form-control"
                            value="<?php echo htmlspecialchars($admin['USERNAME']); ?>" ></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><input type="email" id="adminPhone" name="adminPhone" class="form-control"
                            value="<?php echo htmlspecialchars($admin['EMAIL']); ?>" required></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td>
                        <div class="input-group">
                            <input type="password" id="adminPass" name="adminPass" class="form-control"
                                value="<?php echo htmlspecialchars($admin['PASSWORD']); ?>" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility()">
                                <i class="fa fa-eye" id="toggle-icon"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
            </table>

            <br>
            <div class="row mt-3">
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Script for toggling password visibility -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('adminPass');
            const toggleIcon = document.getElementById('toggle-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>
