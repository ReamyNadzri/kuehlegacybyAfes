<?php
include('header_admin.php');
include('connection.php');

// Get the username from the URL to fetch user details
$username = $_GET['username'];

// Fetch user data based on the username
$sql = "SELECT USERNAME, EMAIL, PHONENUM, PASSWORD, NAME FROM USERS WHERE USERNAME = :username";
$stmt = oci_parse($condb, $sql);
oci_bind_by_name($stmt, ":username", $username);
oci_execute($stmt);

// Fetch the user details
$row = oci_fetch_assoc($stmt);

// Close the connection
oci_free_statement($stmt);
oci_close($condb);

if (!$row) {
    header("Location: user_list.php");
    exit;
}
?>

<body style="background-color: #FFFAF0;">
    <link rel="stylesheet" href="style.css">
    <title>Edit User Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <h4>Edit User Profile</h4>

    <!-- Edit Form inside a Table -->
    <form method="POST" action="update_user.php">
        <div class="container">
            <table class="table table-bordered">
                <tr>
                    <th>Username</th>
                    <td><input type="text" id="username" name="username" class="form-control"
                            value="<?php echo htmlspecialchars($row['USERNAME']); ?>" readonly></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><input type="email" id="email" name="email" class="form-control"
                            value="<?php echo htmlspecialchars($row['EMAIL']); ?>" required></td>
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <td><input type="text" id="phone" name="phone" class="form-control"
                            value="<?php echo htmlspecialchars($row['PHONENUM']); ?>" required></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td>
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control"
                                value="<?php echo htmlspecialchars($row['PASSWORD']); ?>" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility()">
                                <i class="fa fa-eye" id="toggle-icon"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>Full Name</th>
                    <td><input type="text" id="name" name="name" class="form-control"
                            value="<?php echo htmlspecialchars($row['NAME']); ?>" required></td>
                </tr>
            </table>
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
            const passwordInput = document.getElementById('password');
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
