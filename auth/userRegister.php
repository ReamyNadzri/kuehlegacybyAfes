<?php

include('../includes/header.php');
include('../connection.php'); 

// Check if form is submitted
if (isset($_POST['register'])) {
    // Get form data and sanitize inputs
    $username = $_POST['username'];
    $password = $_POST['password']; 
    $email = $_POST['email'];
    $phoneNum = $_POST['phoneNum'];
    $name = $_POST['name'];

    // Validate inputs based on database constraints
    $errors = [];

    // Username validation
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) > 50) {
        $errors[] = "Username must be less than 50 characters";
    }

    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) > 30) {
        $errors[] = "Password must be less than 30 characters";
    }

    // Email validation
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } elseif (strlen($email) > 100) {
        $errors[] = "Email must be less than 100 characters";
    }

    // Phone number validation
    if (!empty($phoneNum) && strlen($phoneNum) > 15) {
        $errors[] = "Phone number must be less than 11 characters";
    }

    // Name validation
    if (!empty($name) && strlen($name) > 100) {
        $errors[] = "Name must be less than 100 characters";
    }

    // Check if username already exists
    $sql = "SELECT COUNT(*) AS USER_COUNT FROM USERS WHERE USERNAME = ?";
    $stmt = mysqli_prepare($condb, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['USER_COUNT'] > 0) {
        $errors[] = "Username already exists";
    }
    mysqli_stmt_close($stmt);

    // Check if email already exists
    $sql = "SELECT COUNT(*) AS USER_COUNT FROM USERS WHERE EMAIL = ?";
    $stmt = mysqli_prepare($condb, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['USER_COUNT'] > 0) {
        $errors[] = "Email already exists";
    }
    mysqli_stmt_close($stmt);

    // If no errors, proceed with registration
    if (empty($errors)) {
        $sql = "INSERT INTO USERS (USERNAME, PASSWORD, EMAIL, PHONENUM, NAME) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($condb, $sql);

        mysqli_stmt_bind_param($stmt, "sssss", $username, $password, $email, $phoneNum, $name);

        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $_SESSION['successMessage'] = "Pendaftaran berjaya! Anda boleh log masuk sekarang.";
            echo "<script>window.location.href = 'userLogin.php';</script>";
            exit;
        } else {
            $errors[] = "Pendaftaran gagal. Sila cuba lagi.";
        }

        mysqli_stmt_close($stmt);
    }

    // If there were errors, output them as JSON for JavaScript
    if (!empty($errors)) {
        echo '<script>';
        echo 'var errors = ' . json_encode($errors) . ';';
        echo '</script>';
    }
}
?>

<head>
    <style>
        /* Glassmorphic Form Card Modal */
        .backdropCustom {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            box-shadow: 0 24px 64px rgba(28, 63, 36, 0.08), 0 8px 24px rgba(0, 0, 0, 0.02);
            padding: 2.5rem;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 2;
        }

        .form-input-custom {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--color-border);
            border-radius: 100px;
            font-size: 0.95rem;
            background-color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s var(--ease-custom);
            outline: none;
        }

        .form-input-custom:focus {
            border-color: var(--color-accent);
            background-color: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(28, 63, 36, 0.06);
        }

        .login-btn-custom {
            width: 100%;
            padding: 12px;
            background-color: var(--color-accent);
            color: white;
            border: none;
            border-radius: 100px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s var(--ease-custom);
        }

        .login-btn-custom:hover {
            background-color: #122c19;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(28, 63, 36, 0.15);
        }

        .image-side {
            position: absolute;
            top: 10%;
            bottom: 10%;
            width: 24%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .image-side img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .left-image {
            left: 5%;
        }

        .right-image {
            right: 5%;
        }

        .login-page-container {
            position: relative;
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
    </style>
    <title>Daftar Masuk</title>
</head>

<div class="login-page-container">
    <!-- Left Background Graphic -->
    <div class="image-side left-image d-none d-lg-flex">
        <img src="../sources/register/kueh2.png" alt="Left Image" class="img-fluid">
    </div>

    <!-- Center Glassmorphic Card (The Register Modal-like Form) -->
    <div class="backdropCustom w3-border">
        <h2 class="font-serif fw-bold text-center mb-4" style="font-family: var(--font-serif) !important; color: var(--color-accent);">Daftar Masuk</h2>

        <form method="POST" action="" class="d-flex flex-column gap-3">
            <div>
                <input type="text" name="username" class="form-input-custom" placeholder="Nama Pengguna" required>
            </div>
            <div>
                <input type="text" name="name" class="form-input-custom" placeholder="Nama Penuh" required>
            </div>
            <div>
                <input type="text" name="phoneNum" id="phoneNum" class="form-input-custom" placeholder="Nombor Telefon Pengguna" required maxlength="11">
            </div>
            <div>
                <input type="email" name="email" class="form-input-custom" placeholder="Email Pengguna" required>
            </div>
            <div>
                <input type="password" name="password" class="form-input-custom" placeholder="Kata laluan" required>
            </div>

            <button type="submit" name="register" class="login-btn-custom mt-2">Daftar</button>
        </form>

        <p class="text-center text-muted mt-4 mb-0" style="font-size: 0.9rem;">
            Sudah mempunyai akaun? <a href="userLogin.php" class="fw-bold" style="color: var(--color-accent);">Log Masuk</a>
        </p>
    </div>

    <!-- Right Background Graphic -->
    <div class="image-side right-image d-none d-lg-flex">
        <img src="../sources/register/kueh1.png" alt="Right Image" class="img-fluid">
    </div>
</div>

<img src="../sources/footer/footer.png" alt="Footer Banner" style="width: 100%; display: block; margin-top: 2rem;">

<!-- Page Closing Tags (Backwards-compatibility for 5-div open layout) -->
</main> <!-- content-body -->
</div> <!-- content-wrapper-compat-4 -->
</div> <!-- content-wrapper-compat-3 -->
</div> <!-- main-layout -->
</div> <!-- app-container -->

<?php include('../includes/footer.php'); ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof errors !== 'undefined' && errors.length > 0) {
            errors.forEach(function(error) {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'error',
                    title: error,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        }
        
        const phoneInput = document.getElementById("phoneNum");
        if (phoneInput) {
            phoneInput.addEventListener("input", function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 11);
            });
        }
    });
</script>
</body>
</html>