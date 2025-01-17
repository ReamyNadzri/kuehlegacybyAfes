<?php

require __DIR__ . "/vendor/autoload.php";

$client = new Google\Client;

$client->setClientId("86003731304-ujapfaslp3bk71imksdn5oq21ebl8i07.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/kuehlegacybyAfes/callback.php");

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();

include('header.php');
include('connection.php');

// Check if the form is submitted
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];


    $sql = "SELECT * FROM users WHERE email = :email AND password = :password";

    $stmt = oci_parse($condb, $sql);

    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':password', $password);

    oci_execute($stmt);


    $user = oci_fetch_assoc($stmt);

    if ($user) {
        // Successful login
        $_SESSION['username'] = $user['USERNAME'];
        $_SESSION['email'] = $user['EMAIL'];
        $_SESSION['phoneNum'] = $user['USERNAME'];
        $_SESSION['username'] = $user['USERNAME'];
        $_SESSION['username'] = $user['USERNAME'];
        
        // Redirect to index page
        header("Location: index.php");
        exit();
    } else {
        // Login failed
        $errorMessage = "Invalid email or password.";
    }
}

?>
<body class="" style="background-color: #FFFAF0;">

        <link rel="stylesheet" href="style.css">
        <title>Log Masuk</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link href="https://fonts.cdnfonts.com/css/product-sans" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<br><br><br><br><br>
<div class="w3-container w3-row">
    <div class="w3-cell" style="width:55%"></div>
    <div class="w3-container w3-cell" style="width: 400px; background-color: #fff; padding: 40px; border-radius: 8px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
        <style>
          
          .gsi-material-button {
                -moz-user-select: none;
                -webkit-user-select: none;
                -ms-user-select: none;
                -webkit-appearance: none;
                background-color: WHITE;
                background-image: none;
                border: 1px solid #747775;
                -webkit-border-radius: 4px;
                border-radius: 4px;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
                color: #1f1f1f;
                cursor: pointer;
                font-family: 'Roboto', arial, sans-serif;
                font-size: 14px;
                height: 40px;
                letter-spacing: 0.25px;
                outline: none;
                overflow: hidden;
                padding: 0 12px;
                position: relative;
                text-align: center;
                -webkit-transition: background-color .218s, border-color .218s, box-shadow .218s;
                transition: background-color .218s, border-color .218s, box-shadow .218s;
                vertical-align: middle;
                white-space: nowrap;
                width: auto;
                max-width: 400px;
                min-width: min-content;
                }

                .gsi-material-button .gsi-material-button-icon {
                height: 20px;
                margin-right: 12px;
                min-width: 20px;
                width: 20px;
                }

                .gsi-material-button .gsi-material-button-content-wrapper {
                -webkit-align-items: center;
                align-items: center;
                display: flex;
                -webkit-flex-direction: row;
                flex-direction: row;
                -webkit-flex-wrap: nowrap;
                flex-wrap: nowrap;
                height: 100%;
                justify-content: center;
                position: relative;
                width: 100%;
                }

                .gsi-material-button .gsi-material-button-contents {
                -webkit-flex-grow: 0;
                flex-grow: 0;
                font-family: 'Roboto', arial, sans-serif;
                font-weight: 500;
                overflow: hidden;
                text-overflow: ellipsis;
                vertical-align: top;
                }

                .gsi-material-button .gsi-material-button-state {
                -webkit-transition: opacity .218s;
                transition: opacity .218s;
                bottom: 0;
                left: 0;
                opacity: 0;
                position: absolute;
                right: 0;
                top: 0;
                }

                .gsi-material-button:disabled {
                cursor: default;
                background-color: #ffffff61;
                border-color: #1f1f1f1f;
                }

                .gsi-material-button:disabled .gsi-material-button-contents {
                opacity: 38%;
                }

                .gsi-material-button:disabled .gsi-material-button-icon {
                opacity: 38%;
                }

                .gsi-material-button:not(:disabled):active .gsi-material-button-state, 
                .gsi-material-button:not(:disabled):focus .gsi-material-button-state {
                background-color: #303030;
                opacity: 12%;
                }

                .gsi-material-button:not(:disabled):hover {
                -webkit-box-shadow: 0 1px 2px 0 rgba(60, 64, 67, .30), 0 1px 3px 1px rgba(60, 64, 67, .15);
                box-shadow: 0 1px 2px 0 rgba(60, 64, 67, .30), 0 1px 3px 1px rgba(60, 64, 67, .15);
                }

                .gsi-material-button:not(:disabled):hover .gsi-material-button-state {
                background-color: #303030;
                opacity: 8%;
                }


        </style>
        <h2 class="w3-center w3-text-gray">Log Masuk</h2>

        <!-- Display error message if login fails -->
        <?php if (isset($errorMessage)): ?>
            <div class="w3-text-red w3-center"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="w3-margin-bottom">
                <input type="email" name="email" class="w3-input w3-border w3-round-large" placeholder="Email" required>
            </div>
            <div class="w3-margin-bottom">
                <input type="password" name="password" class="w3-input w3-border w3-round-large" placeholder="Kata laluan" required>
            </div>
            <button type="submit" name="login" class="w3-button w3-block w3-round-large w3-blue w3-margin-top w3-orange">Login</button>
        </form>
        <hr> 
        
         <a href="<?= $url ?>">
            <button class="gsi-material-button" style="margin-left: 22%">
                <div class="gsi-material-button-state"></div>
                <div class="gsi-material-button-content-wrapper">
                    <div class="gsi-material-button-icon">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" xmlns:xlink="http://www.w3.org/1999/xlink" style="display: block;">
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                        <path fill="none" d="M0 0h48v48H0z"></path>
                    </svg>
                    </div>
                    <span class="gsi-material-button-contents">Sign in with Google</span>
                    <span style="display: none;">Sign in with Google</span>
                </div>
            </button>
        </a>
        <p class="w3-center w3-margin-top">Masih lagi tiada akaun? <a href="userRegister.php" class="w3-text-orange">Daftar Sekarang</a></p>
    </div>

    
</div>
</body>

    <img src="sources/footer/footer.png" alt="" style="width: 100%;">