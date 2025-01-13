<?php
session_start(); 
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement to fetch user
    $sql = "SELECT * FROM users WHERE email = :email AND password = :password";
    $stid = oci_parse($condb, $sql);

    oci_bind_by_name($stid, ':email', $email);
    oci_bind_by_name($stid, ':password', $password);

    // Execute the statement
    oci_execute($stid);

    // Fetch the result
    $row = oci_fetch_assoc($stid);

    if ($row) {
        $_SESSION['user_id'] = $row['USERNAME']; 

        header('Location: index.php');
        exit();
    } else {
        echo "Invalid email or password.";
    }

    oci_free_statement($stid);
    oci_close($condb);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            height: 100vh;
            overflow: hidden;
            transition: background-color 0.3s, color 0.3s;
        }

        .container-fluid {
            position: relative;
            height: 100vh;
        }

        .image-side {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 33.33%;
            /* Each image takes up 1/3 of the screen */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
            /* Images are behind the login form */
        }

        .left-image {
            left: 250px;
        }

        .right-image {
            right: 250px;
        }

        .login-container {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            z-index: 2;
            /* Login form is above the images */
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.9);
            /* White background with 90% opacity */
            animation: fadeIn 0.5s ease-in-out;
        }



        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-primary {
            padding: 0.75rem;
            border-radius: 10px;
            width: 100%;
            background-color: #228B22;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #1c6c1c;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
            color: #2d3436;
        }

        .form-check-label {
            color: #636e72;
        }

        .register-link {
            text-align: center;
            margin-top: 1rem;
            color: #636e72;
        }

        .forgot-password {
            color: #6c5ce7;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .forgot-password:hover {
            color: #5b4cc4;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <!-- Left Image -->
        <div class="image-side left-image">
            <img src="sources/login/kuih2-bg.png" alt="Left Image" class="img-fluid">
        </div>

        <!-- Login Form -->
        <div class="login-container">
            <div class="login-card">
                <h2 class="login-header">Log masuk</h2>
                <form action="" method="POST">
                    <div class="mb-3 form-floating">
                        <input type="text" class="form-control" id="email" placeholder="E-mel" name="email" required>
                        <label for="email">E-mel</label>
                    </div>
                    <div class="mb-3 input-group">
                        <input type="password" class="form-control" id="password" placeholder="kata laluan"
                            name="password" required>
                        <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <a href="#" class="forgot-password">Lupa kata laluan anda?</a>
                    </div>
                    <button type="submit" class="btn btn-primary" id="login-button">
                        <span id="login-text">Log masuk</span>
                        <span id="login-spinner" class="spinner-border spinner-border-sm d-none" role="status"
                            aria-hidden="true"></span>
                    </button>
                    <div class="register-link">
                        Belum ada akaun? <a href="#" class="forgot-password">Daftar</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Image -->
        <div class="image-side right-image">
            <img src="sources/login/kueh1.png" alt="Right Image" class="img-fluid">
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Password Visibility
        document.getElementById('toggle-password').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });

        // Show Loading Spinner on Form Submit
        document.querySelector('form').addEventListener('submit', function () {
            document.getElementById('login-text').classList.add('d-none');
            document.getElementById('login-spinner').classList.remove('d-none');
        });
    </script>
</body>

</html>