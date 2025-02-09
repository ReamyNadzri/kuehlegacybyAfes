<?php


include('header.php');


include('connection.php'); // Ensure this file correctly sets up $condb

// Check if form is submitted
if (isset($_POST['register'])) {
    // Get form data and sanitize inputs
    $username = $_POST['username'];
    $password = $_POST['password']; // You should hash passwords before storing them
    $email = $_POST['email'];
    $phoneNum = $_POST['phoneNum'];
    $name = $_POST['name'];

    // Validate inputs based on database constraints
    $errors = [];

    // Username validation (VARCHAR2(50 BYTE))
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) > 50) {
        $errors[] = "Username must be less than 50 characters";
    }

    // Password validation (VARCHAR2(30 BYTE))
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) > 30) {
        $errors[] = "Password must be less than 30 characters";
    }

    // Email validation (VARCHAR2(100 BYTE))
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } elseif (strlen($email) > 100) {
        $errors[] = "Email must be less than 100 characters";
    }

    // Phone number validation (VARCHAR2(15 BYTE))
    if (!empty($phoneNum) && strlen($phoneNum) > 15) {
        $errors[] = "Phone number must be less than 11 characters";
    }

    // Name validation (VARCHAR2(100 BYTE))
    if (!empty($name) && strlen($name) > 100) {
        $errors[] = "Name must be less than 100 characters";
    }

    // Check if username already exists
    $sql = "SELECT COUNT(*) AS USER_COUNT FROM USERS WHERE USERNAME = :username";
    $stid = oci_parse($condb, $sql);
    oci_bind_by_name($stid, ":username", $username);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);

    if ($row['USER_COUNT'] > 0) {
        $errors[] = "Username already exists";
    }

    // Check if username already exists
    $sql = "SELECT COUNT(*) AS USER_COUNT FROM USERS WHERE EMAIL = :EMAIL";
    $stid = oci_parse($condb, $sql);
    oci_bind_by_name($stid, ":EMAIL", $email);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);

    if ($row['USER_COUNT'] > 0) {
        $errors[] = "Email already exists";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $sql = "INSERT INTO USERS (USERNAME, PASSWORD, EMAIL, PHONENUM, NAME) VALUES (:username, :password, :email, :phoneNum, :name)";
        $stid = oci_parse($condb, $sql);

        // Bind parameters
        oci_bind_by_name($stid, ":username", $username);
        oci_bind_by_name($stid, ":password", $password);
        oci_bind_by_name($stid, ":email", $email);
        oci_bind_by_name($stid, ":phoneNum", $phoneNum);
        oci_bind_by_name($stid, ":name", $name);

        // Execute the statement
        $result = oci_execute($stid, OCI_COMMIT_ON_SUCCESS);

        if ($result) {
            $_SESSION['successMessage'] = "Registration successful! You can now login.";
            
            echo "<script>
                window.location.href = 'userLogin.php';
            </script>";
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }

    // If there were errors, store them in session and redirect back to registration page
    if (!empty($errors)) {
        // Output errors as JSON for JavaScript
        echo '<script>';
        echo 'var errors = ' . json_encode($errors) . ';';
        echo '</script>';
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

        .backdropCustom {
            background: rgba(255, 255, 255, 0.2);
            /* Semi-transparent white background */
            backdrop-filter: blur(10px);
            /* Blur effect for glassmorphism */
            -webkit-backdrop-filter: blur(10px);
            /* For Safari support */
            border: 1px solid rgba(255, 255, 255, 0.3);
            /* Light border for glass effect */
            border-radius: 10px;
            /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
            padding: 30px;
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 2;
        }

        .w3-input1 {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .w3-button1 {
            width: 100%;
            padding: 10px;
            background-color: orange;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .w3-button1:hover {
            background-color: #e95b4f;
        }


        .input-container {
            position: relative;
            width: 100%;
            margin: 10px 0;
        }

        input {
            width: 100%;
            padding: 15px;
            padding-left: 40px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
            box-sizing: border-box;
        }


        .image-side {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 25%;
            /* Reduce the width of the image containers */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;

            /* Images are behind the login form */
        }

        .image-bottom {
            position: absolute;
            top: 0;
            bottom: 0;

            /* Reduce the width of the image containers */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;

            /* Images are behind the login form */
        }

        .image-side img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .left-image {
            left: 450px;
        }

        .bottom-image {
            bottom: 0;
            top: auto;
        }

        .right-image {
            right: 350px;
        }
    </style>

    <link rel="stylesheet" href="style.css">
    <title>Daftar Masuk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.cdnfonts.com/css/product-sans" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="" style="background-color: #FFFAF0;">
    <br><br><br><br><br><br>

    <div class="image-side left-image">
        <img src="sources/register/kueh2.png" alt="Left Image" class="img-fluid">
    </div>
    <div class="w3-cell" style="width:55%"></div>
    <div class="w3-container w3-cell backdropCustom w3-border" style="width: 400px; padding: 40px; ">

        <h2 class="w3-center w3-text-gray">Daftar Masuk</h2>

        <form method="post" action="">
            <div class="w3-margin-top">
                <input type="text" name="username" class="w3-input1 w3-border w3-round-large"
                    placeholder="Nama Pengguna" required>
            </div>
            <div class="">
                <input type="text" name="name" class="w3-input1 w3-border w3-round-large" placeholder="Nama Penuh"
                    required>
            </div>
            <div class="">
                <input type="text" name="phoneNum" class="w3-input1 w3-border w3-round-large"
                    placeholder="Nombor Telefon Pengguna" required maxlength="11">
            </div>
            <div class="">
                <input type="email" name="email" class="w3-input1 w3-border w3-round-large" placeholder="Email Pengguna"
                    required>
            </div>
            <div class="">
                <input type="password" name="password" class="w3-input1 w3-border w3-round-large"
                    placeholder="Kata laluan" required>
            </div>

            <button type="submit" name="register"
                class="w3-button1 w3-block w3-round-large w3-blue w3-margin-top w3-orange">Daftar</button>
        </form>
        <hr>

        <p class="w3-center w3-margin-top">Sudah mempunyai akaun? <a href="userLogin.php" class="w3-text-orange">Log
                Masuk</a></p>
    </div>

    <div class="image-side right-image">

        <img src="sources/register/kueh1.png" alt="Right Image" class="img-fluid">
    </div>
    <div class="image-bottom bottom-image">
        <img src="sources/footer/footer.png" class="img-fluid" alt="">
    </div>
    <!-- Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Display errors if any
            if (typeof errors !== 'undefined' && errors.length > 0) {
                errors.forEach(function (error) {
                    Swal.fire({
                        toast: true, // Enable toast mode
                        position: 'top', // Position at the top
                        icon: 'error', // Error icon
                        title: error, // Error message
                        showConfirmButton: false, // Hide the "OK" button
                        timer: 5000, // Auto-close after 5 seconds
                        timerProgressBar: true, // Show a progress bar
                        didOpen: (toast) => {
                            // Pause timer on hover
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                });
            }

            // Display success message if registration is successful
            if (typeof successMessage !== 'undefined') {
                Swal.fire({
                    toast: true, // Enable toast mode
                    position: 'top', // Position at the top
                    icon: 'success', // Success icon
                    title: successMessage, // Success message
                    showConfirmButton: false, // Hide the "OK" button
                    timer: 5000, // Auto-close after 5 seconds
                    timerProgressBar: true, // Show a progress bar
                    didOpen: (toast) => {
                        // Pause timer on hover
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });

                // Redirect to login page after 5 seconds
                setTimeout(function () {
                    window.location.href = "userLogin.php";
                }, 5000);
            }
        });
    </script>
    <br><br><br><br><br><br><br>
</body>