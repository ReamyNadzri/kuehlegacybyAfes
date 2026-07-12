<?php

require __DIR__ . "/../vendor/autoload.php";

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$client = new Google\Client;

$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();

include('../includes/header.php');
include('../connection.php');

// Check for success message
if (isset($_SESSION['successMessage'])) {
    $successMessage = $_SESSION['successMessage'];
    unset($_SESSION['successMessage']); 
}

// Check if the form is submitted
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";

    $stmt = mysqli_prepare($condb, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Successful login
        $_SESSION['username'] = $user['USERNAME'];
        $_SESSION['email'] = $user['EMAIL'];
        $_SESSION['phoneNum'] = $user['PHONENUM'];

        echo "<script>window.location.href = '../index.php';</script>";
        exit;
    } else {
        $errorMessage = "E-mel atau kata laluan tidak sah.";
    }
}
?>

<head>
    <style>
        .gsi-material-button {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            -webkit-appearance: none;
            background-color: WHITE;
            background-image: none;
            border: 1px solid #747775;
            border-radius: 100px;
            color: #1f1f1f;
            cursor: pointer;
            font-family: var(--font-sans), sans-serif;
            font-size: 14px;
            height: 44px;
            letter-spacing: 0.25px;
            outline: none;
            overflow: hidden;
            padding: 0 24px;
            position: relative;
            text-align: center;
            transition: background-color .218s, border-color .218s, box-shadow .218s;
            vertical-align: middle;
            white-space: nowrap;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .gsi-material-button:hover {
            background-color: #F8F9FA;
            border-color: var(--color-border-hover);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .gsi-material-button .gsi-material-button-icon {
            height: 20px;
            margin-right: 12px;
            min-width: 20px;
            width: 20px;
        }

        .gsi-material-button .gsi-material-button-content-wrapper {
            align-items: center;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            height: 100%;
            justify-content: center;
            position: relative;
            width: 100%;
        }

        .gsi-material-button .gsi-material-button-contents {
            font-weight: 600;
        }

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
    <title>Log Masuk</title>
</head>

<div class="login-page-container">
    <!-- Left Background Graphic -->
    <div class="image-side left-image d-none d-lg-flex">
        <img src="../sources/register/kueh2.png" alt="Seni Kuih Tradisional">
    </div>

    <!-- Center Glassmorphic Card (The Login Modal-like Form) -->
    <div class="backdropCustom w3-border">
        <h2 class="font-serif fw-bold text-center mb-4" style="font-family: var(--font-serif) !important; color: var(--color-accent);">Log Masuk</h2>

        <!-- Display error message -->
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger rounded-3 py-2 text-center mb-4" style="font-size: 0.9rem;">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="d-flex flex-column gap-3">
            <div>
                <input type="email" name="email" class="form-input-custom" placeholder="E-mel" required>
            </div>
            <div>
                <input type="password" name="password" class="form-input-custom" placeholder="Kata laluan" required>
            </div>
            <button type="submit" name="login" class="login-btn-custom mt-2">Log Masuk</button>
        </form>

        <div class="d-flex align-items-center my-4">
            <hr class="flex-grow-1 border-top" style="color: var(--color-border);">
            <span class="mx-3 text-muted font-mono" style="font-size: 0.75rem;">ATAU</span>
            <hr class="flex-grow-1 border-top" style="color: var(--color-border);">
        </div>

        <a href="<?= $url ?>" class="text-decoration-none">
            <button class="gsi-material-button">
                <div class="gsi-material-button-content-wrapper">
                    <div class="gsi-material-button-icon">
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" style="display: block;">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                        </svg>
                    </div>
                    <span class="gsi-material-button-contents">Sign in with Google</span>
                </div>
            </button>
        </a>

        <p class="text-center text-muted mt-4 mb-0" style="font-size: 0.9rem;">
            Masih tiada akaun? <a href="userRegister.php" class="fw-bold" style="color: var(--color-accent);">Daftar Sekarang</a>
        </p>
    </div>

    <!-- Right Background Graphic -->
    <div class="image-side right-image d-none d-lg-flex">
        <img src="../sources/register/kueh1.png" alt="Right Image">
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
    <?php if (isset($successMessage)): ?>
        Swal.fire({
            toast: true,
            position: 'top',
            icon: 'success',
            title: '<?php echo addslashes($successMessage); ?>',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    <?php endif; ?>
</script>
</body>
</html>