<?PHP
session_start();
date_default_timezone_set("Asia/Kuala_Lumpur");
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <link rel="stylesheet" href="style.css">
    <title>Legacy Kueh System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://fonts.cdnfonts.com/css/product-sans" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="" style="background-color: #FFFAF0;">

    <style>
        .button:hover {
            background-color: #AF2B1E;
        }

        .sidebar {
            position: fixed;
        }

        body {
            font-family: 'Product Sans', sans-serif;
            margin: 0;

        }

        h1 {
            font-family: 'Product Sans', sans-serif;

        }

        h2,
        h3,
        h4 {
            font-family: 'Product Sans', sans-serif;

        }

        p {
            font-family: 'Product Sans', sans-serif;

        }

        a {
            font-family: 'Product Sans', sans-serif;

        }

        .image-container {
            position: relative;
            /* Parent container for positioning */
            width: 100%;
            max-width: 800px;
            margin: auto;
        }

        .image-container img {
            width: 100%;
            display: block;
        }

        .image-text {
            position: absolute;
            /* Overlay text */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            /* Text color */
            font-size: 2em;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
            /* Optional shadow */
            font-weight: bold;
            text-align: center;
        }

        /* For fixed header styling */
        .fixed-header {
            position: fixed;
            top: 3px;
            width: 100%;
            background-color: #333;
            color: white;
            padding: 10px 20px;
            z-index: 1000;
        }

        .content {
            margin-top: 80px;
            /* Push content below fixed header */
        }
    </style>


    <div class="" style="height: 18px;width: 100%; position: fixed; background-color: #FFFAF0; z-index: 1000;"></div>
    <div class="w3-row">



        <div class="w3-round-large w3-row" style="margin:10px; z-index:0">


            <!--sidebar of kueh system-->
            <div class="sidebar w3-container w3-border w3-round-large w3-cell w3-white" style="width: 15%; height:98%; z-index: 1001;">

                <img class="" style="margin-top:20px; margin-left: 9px;" src="sources/header/logofull.svg" alt="" width="240px">

                <hr style="color:darkgrey">
                <a href="index.php" class="w3-bar-item w3-button w3-round w3-large" style="margin-left:6px; margin-right:10px; width:94%; text-align:left">
                    <i class="bi bi-house me-2" style="font-size: 1.5rem;"></i>
                    Home
                </a><br>

                <style>
                    @keyframes glow {
                        0% {
                            box-shadow: 0 0 5px #FF0000;
                        }

                        40% {
                            box-shadow: 0 0 20px #FF0000;
                        }

                        80% {
                            box-shadow: 0 0 5px #FF0000;
                        }
                    }

                    .glow-button {
                        animation: glow 2s infinite;
                        background-color: #FFE4E1;
                        transition: all 0.3s ease;
                    }

                    .glow-button:hover {
                        background-color: #FF0000;
                        color: white;
                        transform: scale(1.02);
                    }
                </style>
                <a href="kuehListing.php?search=" class="w3-bar-item w3-button w3-round w3-large glow-button" style="margin-left:6px; margin-right:10px; width:94%; text-align:left; margin-top:30px">
                    <i class="bi bi-search me-2" style="font-size: 1.5rem;"></i>
                    Mulakan Carian
                </a><br>

                <?PHP
                if (!empty($_SESSION['username'])) {
                ?>

                    <a href="userProfile.php" class="w3-bar-item w3-button w3-round w3-large" style="margin-left:6px; margin-right:10px; width:94%; text-align:left; color:#181513;  margin-top:6px">
                        <i class="bi bi-journal-text me-2" style="font-size: 1.5rem;"></i>
                        Resipi Anda
                    </a>

                    <a href="favorite.php" class="w3-bar-item w3-button w3-round w3-large" style="margin-left:6px; margin-right:10px; width:94%; text-align:left; color:#181513; margin-top:6px">
                        <i class="bi bi-bookmark-heart me-2" style="font-size: 1.5rem;"></i>
                        Kegemaran
                    </a><br>
                <?PHP
                }
                if (empty($_SESSION['username'])) {
                ?>
                    <br>
                    <p class="w3-small" style="padding-left: 5px;">Untuk mulakan perkongsian anda dan simpan resipi yang anda minat, sila <a href="register.php">daftar atau log masuk.</a></p>
                <?PHP
                }
                ?>
                <!--<img src="sources/header/footer.png" class="w3-round-large" alt="" width="250px" style="margin-left: -16px; margin-top: 311px;">-->
                <hr>
                <hr><hr><hr>
                <a href="kuehListing.php?search=johor" class="w3-bar-item w3-button w3-round w3-large">

                    <img src="sources/negeri/johor.png" alt="" class="w3-round-large w3-hover-opacity w3-card" style="height: 75px; width: 100%; object-fit: cover;">


                </a>
                <a href="kuehListing.php?search=kedah" class="w3-bar-item w3-button w3-round w3-large">

                    <img src="sources/negeri/kedah.png" alt="" class="w3-round-large w3-hover-opacity w3-card" style="height: 75px; width: 100%; object-fit: cover;">


                </a>
                <a href="kuehListing.php?search=kelantan" class="w3-bar-item w3-button w3-round w3-large">

                    <img src="sources/negeri/kelantan.png" alt="" class="w3-round-large w3-hover-opacity w3-card" style="height: 75px; width: 100%; object-fit: cover;">


                </a>
                <a href="kuehListing.php?search=terengganu"class="w3-bar-item w3-button w3-round w3-large">

                    <img src="sources/negeri/terengganu.png" alt="" class="w3-round-large w3-hover-opacity w3-card" style="height: 75px; width: 100%; object-fit: cover;">


                </a>


            </div>

            <div class="w3-round-large w3-border" style="margin-left:15.6%; z-index:9999;"> <!--main content-->
                <!--TOP NAVIGATION-->

                <div class="w3-container w3-border-top w3-round-large w3-rest w3-white w3-row fixed-header" style="width: 83.4%; margin-top: 8px; padding:11px">

                    <div class="w3-cell w3-left" style="width:35%;"> <!--IF CONDITION HERE (search hidden)-->