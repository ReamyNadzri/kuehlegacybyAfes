<?php
include('header_admin.php');
include('../connection.php');

$adminName = $_GET['adminName'];

$arahan_sql_cari = "SELECT * FROM ADMIN WHERE USERNAME = ?";
$stmt = mysqli_prepare($condb, $arahan_sql_cari);
mysqli_stmt_bind_param($stmt, 's', $adminName);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
    die("<script>alert('Admin not found'); window.history.back();</script>");
}
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminName = $_POST['adminName'];
    $adminEmail = $_POST['adminEmail'];
    $adminPass = $_POST['adminPass'];
    $oldUsername = $admin['USERNAME'];

    if (empty($adminName) || empty($adminEmail) || empty($adminPass)) {
        die("<script>alert('Please insert all the data'); window.history.back();</script>");
    }

    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        die("<script>alert('Invalid email format'); window.history.back();</script>");
    }

    $arahan_sql_update = "UPDATE ADMIN 
        SET USERNAME = ?, NAME = ?, EMAIL = ?, PASSWORD = ? 
        WHERE USERNAME = ?";

    $stmt_update = mysqli_prepare($condb, $arahan_sql_update);
    mysqli_stmt_bind_param($stmt_update, 'sssss', $adminName, $adminName, $adminEmail, $adminPass, $oldUsername);

    if (mysqli_stmt_execute($stmt_update)) {
        echo "<script>alert('Update Success'); window.location.href='admin_info.php';</script>";
    } else {
        echo "<script>alert('Update Failure'); window.history.back();</script>";
    }
    mysqli_stmt_close($stmt_update);
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

    <form method="POST" action="">
        <div class="container">
            <table class="table table-bordered">
                <tr>
                    <th>Username</th>
                    <td><input type="text" id="adminName" name="adminName" class="form-control"
                            value="<?php echo htmlspecialchars($admin['USERNAME']); ?>" required></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><input type="email" id="adminEmail" name="adminEmail" class="form-control"
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