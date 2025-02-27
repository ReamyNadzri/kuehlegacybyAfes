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

                <a href="kuehListing.php?search=" class="w3-bar-item w3-button w3-round w3-large" style="margin-left:6px; margin-right:10px; width:94%; text-align:left; margin-top:30px">
                    <i class="bi bi-search me-2" style="font-size: 1.5rem;"></i>
                    Carian
                </a><br>

                <?PHP
                if (!empty($_SESSION['username'])) {
                ?>        

                    <a href="userProfile.php" class="w3-bar-item w3-button w3-round w3-large" style="margin-left:6px; margin-right:10px; width:94%; text-align:left; color:#181513;margin-top:6px">
                        <i class="bi bi-journal-text me-2" style="font-size: 1.5rem;"></i>
                        Resipi Anda
                    </a>

                    <a href="favorite.php" class="w3-bar-item w3-button w3-round w3-large" style="margin-left:6px; margin-right:10px; width:94%; text-align:left; color:#181513;margin-top:6px">
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



            </div>

            <div class="w3-round-large w3-border" style="margin-left:15.6%; z-index:9999;"> <!--main content-->
                <!--TOP NAVIGATION-->

                <div class="w3-container w3-border-top w3-round-large w3-rest w3-white w3-row fixed-header" style="width: 83.4%; margin-top: 8px; padding:8px">

                    <div class="w3-cell w3-left" style="width:35%;"> <!--IF CONDITION HERE (search hidden)-->

                        <?php include('searchbar.php') ?>

                    </div>
                    <div class="w3-cell w3-right"> <!--KONGSI-->
                        <a href="addKueh.php" class="w3-bar-item w3-button w3-round-large " style="background: #228B22; margin-left :6px; margin-right: 10px ;width: 94%; text-align: left; color: white;">
                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAACXBIWXMAAAsTAAALEwEAmpwYAAADvElEQVR4nO2bSWgUQRSG2yVxOYiCKHhwQVExIm45KUZPguLFs6JeRczJgwt4c01UhLiBBsQtghqNUS8SvKiI6EURFBHcBUHccEvySWHN8KbSy3S6ZqZ67O+S0PW6qv6/p6pra8+rYoABwHRgMVAHDPT+B/gnfD3wkkLeA5uAwV61AtQAZwnnBlAblVED0Aa8AbqpDL+B3THFXzTy+Azc138lzWGZHMUdviYQfwwYrtOHA60i7Rcw1i8jl8SrSu4sQnwtcMm4d2+ASY9EzGq/n72kHZgX2V4qSLHiRfx+EbfVTGyT4j3H0eLbixWv7zklYjeaiarDyzHPc1/85Zji1Zjgh4ifbwbI3r62ysRPBF6I+DtqvGAG5fHcFn/FEN8UU/wXYIZfoNMGWBKvWBEUnMdzDGAI0EFy8YrWVBmAHfF/xP+qr5uUCgO0+KsJxX/TM8IH4toG5w2wKL5Bp+0Q1w84bUDAq25fxD3jgeci/juwJGAU2HeS5YoB+sl32nryOn0Y8FSkr3XSAGAocM2yeLVAcsJIH+2cASUUf8TIs3AS5IIBWvz1Mog/H7g2SIUM0B1eZ5nE14RlmseyxrAyBxnT8FKJPxe5GIrAosaoMpuNih7sh/hFEeJPK6OLqUweS/qiyltHIWfC1u77Kf5kUeIV8i6vxADT9EAlx031/k8wyFHiW3wMLX4PgDIZoCoF3BXFPQNG9PfJ65gthvjjsXeCKJ8B20RRP8OW34DJUeJ13EVjOTz+NhhlMACYozc8cjSGxM7X21mh4kWTugBs7rPU5ZgBXaKYrqDKAkvVpoixjOUr3mbl8pQofyVKLlDM8olRnVmj8St5C8wtRZ0KKKUBWpjs+Pb7xIzxGQ4/BibYrk8lDFgpsv8EjDTMWQN8MMTfAkbZrkulDLgnst8lrqutt9uGcNU8ttvcxwf26E50d9kNAGYar71xwGw9UOkxxD/ps2Njpw5qo1XxqxIGNImsH+p23msIV53ePrVyY7PsWNoogQF61PeOYHr0NHWKrTJdM2B5gPBeva1dZ6ssVw1YZQj/qKfAU22V4boBQ3UfoMbny0JXZKrRAFfIDBAkcymlZAYIkrmUUjIDBMlcSimZAYJkLqWUzABBMpdSSmaAIJlLKSUzQBDmUncaDkv38wBGju6wwDdpOS4fB6Be6HoVFnhOBHZ4VYLxRcnpsMBFsq3ok9n1aWwO+mdf73PGeGHUjYepXlqKca4GOET10RL3tMhC/QXm6wp+OJkEVedXevdpQdHCMzK8/4q/u2PEUlJFCNwAAAAASUVORK5CYII=" alt="create-new--v2" style="padding-bottom:3px;margin-right:8px;text-align:center; width:22px">
                            Kongsi
                        </a><br>

                    </div>

                    <?PHP
                    if (empty($_SESSION['username'])) {
                    ?>

                        <div class="w3-cell w3-right" style="margin-right: 15px;"> <!--LOGIN (HIDDEN)-->
                            <a href="userLogin.php" class="w3-bar-item w3-button w3-round-large w3-border" style="margin-left :6px ;width: 94%; text-align: left;">
                                LogMasuk
                            </a><br>

                        </div>

                    <?PHP
                    } else {
                    ?>

                        <div class="w3-cell w3-right" style="margin-right: 15px;"> <!--logout (HIDDEN)-->
                            <a href="logout.php" class="w3-bar-item w3-button w3-round-large w3-border" style="margin-left :6px ;width: 94%; text-align: left;">
                                LogKeluar
                            </a><br>

                        </div>
                        <a href='userProfile.php'>
                            <div class="w3-cell w3-right text-black" style="margin-right: 15px; "> <!--PROFILE (HIDDEN)-->
                                <?php
                                if (isset($_SESSION['google_user'])) {
                                    $profilegambor = $_SESSION['google_user']['picture'];
                                    echo "<img src='$profilegambor'class='w3-circle' alt='test-account' style='text-align:center; width:40px'>";
                                } else {
                                    echo "<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAACXBIWXMAAAsTAAALEwEAmpwYAAAFdUlEQVR4nO1aS4hcRRR9UaPJxs9CVBT8oBmDEsVPFE0iGJxEMbixySKSTt+qe9+7t6dHRlQQlMadGozfGH8ouHAnoi5cmLjwFxJJ1CgiMaMjRpB8FxLJZybK7e6Zed0zk3RVve6ZhHegYJjX737Ou1V1696Kohw5cuTIkaOjIOq7FogTg7IBSDYZlEFAOQAkR2uj9jfv0mdA/JpBiSFJeqJTGdbKjUC8zqDsNiT/+Qwg/tOgPF+K+26IThHMMsQrgGSzr9NTDuRvrC3frzqimQgTxwsN8reZO94aFShbgMq3RDMFhYGBuUD8CiCPdNr5cRJ4BIhfKhaLc6bVeUiSHiD5oVuOT7JGfLcmjq+ZFudLJLcZ5H3T5XxqShwokSzqrvOY3GuQ/51u58cjQQ4ZkuXdcZ5kUbjz/Bcgvw8kTwMKAMlSnU7W9l3JzBfoUF0G5RcXEhD5jo46D0nSY4gPejmtOwRKnyZF7eozyM85RsL+jq0Jhdpq77Pg8Q79wt7R5ky0bO/I7gAor3oY83aIMYVC4UxDvMcj4l7MPMkB532e38wiazPE73oQMLyGyjdll96iW4anU6VSqZyThfJSqXIhED8CyIcdbdicSdpsiFe4fgFry8uijAHEFVc7dLvOQLG4HWxQdnbiwEJEs3X7dIyCr4OPtMaRdSB5NuoQgPgt5/XA8IIQheucwz+WB9IySjGvNsRDhvg9Q9IPxL3Wlufp3NZ1QncJY/ovMobn21geNCjPAMnPBmWg1R5DXPJYENd6E2B8ihmG5zeFrWdBRHcda/nmJnsML/CIyD8Csj5xNpyIzhszOO6700dGioRP0zYR0SU+cryyQ9Aanh8Bs8cIQH4ikICR1XF86ag8nTI+ciwxORNgUDb4KGuSQfJhCAH1UbYtMn3krHcngOTzcAJ4WygBgPJOMAHIG30i4PdQAoD472ACSL4IJ0AGPQhgr2pPi4zwognK7lACgGSvOwEkR0JDzZAcDyaA5EgLqRudCUA+7EwAOB4+Ws8AYGVVBs43Bj+UInV5dwgg2RuSA2gCkiEBQ6Nyi8WHz/d4f48zAQZl0FXRqkrl3BAC25nDSrL7+/yrTwRscjY2jheOEUjyVIYEPDkq19rkdg8Zn3WlBAbEr2eRTTbJtIItdr3hLAflZWcCDErsYfBwugBarVbPAOLfvAlAGVQZ44TKUtURSmJbsLY8zzNcD2nXaPyL8aMBBIwdiVVmowHiLsfI1c4EhGSDhuSj8VW7OEcvP3g4vzNdVwTiT3yjKPKFQX7Bk4Dj6QWxhHwXIB9ziKKj6X5fY+HzSqq0qONNACTJ9d7hS7yt6QsiS7vkpY+vtSMwynZfO6yV67wJUGhh0Z+E5mOozukT9Rcaz/qb3iFZ76s/uCg61gn2J0CNWBKlYGK5T1vak/xuf2uH19pkcYjuTMriCkD+yj8EJ/YItAucXtQA5eNiHF8x8XflZQFT8Mso0/s/5L7/6kDkW3316rteUYd8LPN7RIZkrcdXGCqKXDwqQ3sGWticSoc+S/cV9N16SX0G9CYKAwNzDfL37RnAu0oxm0KhcHZaRv2AxAeB5LF0sVP/NsiPN541FS9UhspqN5fQe0Nqa+YEKADk8hO2q1EGdQurVqtnRS3QlLbNaTScTn+b3+dCrWkypX7eV2K+KuokgGTJxJSUdxjilZMZ7nOO199OJadBxMq6zgkpeHcuS1mbLAbkf7QNrq0vvchwsnf0y7RLgO4SDrdSt6rz1iZ3R92EtZXLXLrA2uZqn4DmlthJMKthy8wGEPe2vZLH5Xui0w1A3NvOgai+h3NvdDqiqPt67TwgHwDxT7XOcX38qP/TZ+m8IUeOHDly5MiRI0fUOfwPWUs8T7LFLncAAAAASUVORK5CYII=' alt='test-account' style='text-align:center; width:40px'>";
                                }
                                echo "  Hi, " . $_SESSION['username'];
                                ?>

                            </div>
                        </a>
                    <?PHP
                    }
                    ?>
                </div>


                <div class="w3-round-large" style="height: 92%; margin-top: 4%">

                    <!--CONTENT START HERE-->
                    <div class="w3-container w3-white" style="margin-top:5px;width: 100%; height: 100%;">

                        <!--<div href="debug.html" class="w3-panel w3-yellow w3-border w3-animate-fading">
                                    <h3>Development Phase Notification</h3>
                                    <p>This website is currently under development. Some features may not be fully functional or available at this time. We appreciate your understanding and welcome your feedback to improve the platform.</p>
                                    <?php
                                    //include ('debug.html'); 
                                    //include ('popup.php'); 
                                    ?>



                                </div>-->