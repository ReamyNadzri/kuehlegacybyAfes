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

                <img class="" style="margin-top:20px; margin-left: 10px;" src="sources/header/logofull.svg" alt="" width="240px">

                <hr style="color:darkgrey">
                <a href="index.php" class="w3-bar-item w3-button w3-round w3-large" style="margin-left :6px; margin-right: 10px ;width: 94%; text-align: left ">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAACg0lEQVR4nO1XO4sUQRBuwSeogQ/QQDA4EUTEA0MDAx8IJgobiexN11TNVA2jXCR7grv5pQYGgoGG/gIx2FNRI2MRFUU0MBGXC+580/tytr3d23EertIfNAzTPV9933RV9YxSDg4ODv8dqtXqRiCZB+IPnSHzldnZTepfABHtBuInQPJjYKA8nWHeoyYZAHwIiF//Jr4/+J2m6IiaRHh+dBaIF3tiNfIXILloRve6Z2IRiM+txFENgr0e8nGP5GilUllflvY1gFzTyN8SIj9CEJ3oLTDX7Xu/duO7JpkzzybWHEsa1ch3SylWjXLLyvXnOgz322vNPTNnpdRtw9E2gHw1OWdeSKHiiWgHIC9Ygh54Xrxz2DMAsA2Q7w0IJXlcFdkFyA27ZkotVk18nYjWrfasWWPWWibeAPGdUgx4GJ4G5E+JQF818eW0PD4xaZLPwztWAQY08qXBYpWW70dn/piP+KRV3MUYMC0NiG9axfrSC4IDWblngmAfoDwrzIApSo380CJvXoii7bkEUEoZLsNpGxjVEMaCDsODmviV9eZvFHHI1Ov1tRrlmtUY3uownM6Qn9IqrbV1sUIqtYwWlRaa5H0pncHCSjGNFpUWgPyou40DKZSaKG1cGkihdmxTg6mJtNZbzNaJyOa/ZeB8HG/1/eiU0ZAbaW5Ky4wFY5ICyJQmfjHsYGrPgUxNrgHk2qhPg04b5tokG2iMYaDhDKSF2wFyKZQNLoXIpVA2JN/gqH+BrOdAHMcbCjnINPJS4tN2bpiJLAYq5rcV+Uo/DvJSfgZI7q8qLP/RzM9AGE4DyXKJ4pd9Xw7nZqBvAnkhmU55D93hbuYu3sHBwcHBQU0ofgIDUy9WZQWJdgAAAABJRU5ErkJggg==" alt="home" style="padding-bottom:3px;margin-right:5px;text-align:center; width:28px">
                    Home
                </a><br>

                <a href="kuehListing.php?search=a" class="w3-bar-item w3-button w3-round w3-large" style="margin-left :6px; margin-right: 10px ;width: 94%; text-align: left; margin-top: 30px ">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAADW0lEQVR4nO1ZTU9TQRSdLtStG8X4sVB/ggv8/gsmSo2Ksb47M/e9e6GQoEZiIroD/4CJK3VhYogLFcNawY1f/8CNW3GFARSKmDud1xIMhtJ5r9D0JJO8tK9n5szMvXPmVqkOOth6KDHvA8ugLT/VSF+0pR+AvOjbDCB/9t9FWg90qa0GQD4LSBNgaUkjr2ykybtg+bUxyZlWj18hlo9q5MlVA6xoS+8AaShC7k6SZC8i7pAmz8YkxzXyDUCecu/Wf/cmIjrSEhFguBcs/XSzizwHSKNRVN6z0d+LMG15TFua92JmTcyXVZ7QlkbqW4SfX4vjA5vlMqZ8UCOPr1qduyoPaEv3fYd/5FkpVQjBC5YGwdJyLmLAcK8P1GUTc09ofo1UTMWA7buksgrsekzQ7Uw6cWJ4uBYzpv9wFh1MpjGhskUhjRlAmgh+TrhZsjR/neiQyhiSPCQTauSVCPl0MGKZGT9DoyonuNSMbou9Cmg73IldaeScaBbunEGuiL2R56YJo5i0n5m3KmdopOlq30mpeTJn8ty2GlI5AyzdrAqhx02TORdbDbpulTOspZPeAn1smkysuBOSY3zU+tYDXX5FvjdPhvxbyIrF4k6VM8rl8i7vJH61hRCNPNsWWwuQvgYLdrkUBRldI33H/ad8jEy3RfoFy48CkDH4FDgVZHQN9U3v/YqcD7JPU4sSxCo0alEsLZVKg7uDkEq1o+p+eSwI4Ub6RH7gM9Z4QNK+E550IW8bbwwdC0qeWnk/Q0Hu6eugoJFe+IPwZXB2uXbKweTFDKuMAMh3fHKZAejbn0knUndKiw9SKAjNHxm+mBYfTMznVJaQUk1djFuZENusICtRr6DQslRsVNZIxaQxI0W2zXJJ8khjYk2raEyuqKwhdadazEjZ0/JYI+eMvOtT7EJqDNfUg52YXMqokgBWZTPXsTgAsRbuUqQHusQ1S5Nn+QyQb/krbG3QUmKSwJYVaJkYgZRspNrRyN8Kcj0A5Gdrzeh6YiCryuP6tiIpAfITbemTpFA/4DlA/gaWPwDSQxnU/64FPju2VkzgOvDSvwmArqp2EQN5pObQ0O0kxsTc0/ZidB6HZiZikBc7YrZFArA0orYjwCYX0m0GyPfUdoaJuSe3v7M76EA5/AVk6Osx7S8vQwAAAABJRU5ErkJggg==" alt="Cari Kueh" style="padding-bottom:3px;margin-right:8px;text-align:center; width:25px">
                    Cari
                </a><br>

                <?PHP
                    if (!empty($_SESSION['username'])) {
                    ?>

                <a href="index.php" class="w3-bar-item w3-button w3-round w3-large" style="margin-left :6px; margin-right: 10px ;width: 94%; text-align: left; color: #181513;">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAACXBIWXMAAAsTAAALEwEAmpwYAAABpUlEQVR4nO1bQUoEMRDML/QPih72EfoLMZ2Um1b8gPgG96DCnHyZ+guV3UNLEFFkJ8m6C7KxGnKaYSZddLqrq4lzW2gCvZaYXgRqY8vHtBDozPVoAn0tOf8NhDfXo0mD85/L9WhScZAAgBFgPAJgDjAmQbAKmOvRhGVQt5MHnKjuhpAmpZXf6TICBHrsofMqf4fOQzg/6g4Aj3S/Aoe/6w+AqEMrAPndrgHwSxwMSCAAYAQYjwCYA4xJMLIKDKWKwjII8gAjEQKZoJEKg72A/aZK/In5bW2GTs8uDvOUNW9qbOX+PQseXQIgUZ+bevis9sjlTn8AoH0SmzW9rgHwy8P//wDgKs8JQGAETMYiiUcgMgcMTIJgFTCWQZAHGIlQJBMcfvIEUmGwFwCbIbAbNLbDoB5gFEQiFaFhUypVm+5VMEpioCZoFEVBVdgoi4NzAatVCc4FQudzAdnQcLQ6aV4BgNpaGwD/cQu7EQDdK2x6tsLGi7e+azfHv4BMi7UBkJhu8i3s2o8E6aH4HUkHHumpuvGoj3463a/s6arl+ryPeltz8B08HyWRfLUk0QAAAABJRU5ErkJggg==" alt="external-bar-cryp-finance-line-line-icons-royyan-wijaya-4" style="padding-bottom:3px;margin-right:8px;text-align:center; width:25px">
                    Statistik Resipi
                </a><br>
                <a href="notification.php" class="w3-bar-item w3-button w3-round w3-large" style="margin-left :4px; margin-right: 10px ;width: 94%; text-align: left; color: #181513;">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAC7UlEQVR4nO2YPWgUURDHX2yiVoKiqFgYBUVE0EIsREUUlKBWFhriZWfem92ZyyVGjBIsItiLvSIIQbEWBG1sJJVaih8QtUmMVSxihMQoj+SSycUEvX13t4H9wYNj3+5/Zm7fm5l9xuTk5OTkZBGi0g4kYT8i5hazkkBKLoLjn0jy2w//28ZywawEEHkfkkyUnZ8bjn/4OZNFCoXCanDcDSRvFzrOr9HJG33N3xNR8bJ/xmSByPFRJP686B+fCeAMuOTs3+aA5AvGxWMNdR6sdCDJVIVj4+D4BTq+Zoxp8gMd9/lrfq4ikCkkjhrjPHGrdh5IhsGKO9/Ts2apZ4horSUmdDyigwDi1ro6Xyh0r/MOqyzzkog2/Ovz/l7/DKrgvaapF0DSO2+ch6oxPvMn8JAKotfUCyB+P2fYSlvVOlba5tOsvDP1oL1YXK8LVKlUaq5Wq1QqNeua8T/LsGqsTY6r4jSYVg8dD5b1vLapNQteO/FAaj3HD+cCqEe74Suu2sC30+ohyR21DzrDeLmcQce31BLqC6B3Q+ndDOPlcgaJB8oGo5gxrR5YceoNPDC1Rm+6yPGJ1Hpx8aQuiKbWgJPRssEkSTam1UPs2qSW0IipJZfieKsu/6F0gfhrWdfbCKW72JCTdtWEPQuny8/rkkqR5Kl63b5dDgIQX1ep+Uko3QojcgRJpsstsLWd20MeAIDjX7Pa0xHJYRMS/1UFJGOqAj+qQXp+rPbXmI3lXBBha5ND4HhyPtXJaC02WgfzNiT+plLqJMbxwaB53/fvkCT7TY3ooOIBdPIpWF2AJNmrss5EyHW/FBFzi26xozjeEyptNmRAig+mBZ+ODQvA8dWqA0CUnUD8sWEBOPngU6ypB/39/avQ8XflwKnyXOSS0zpF+ntNFgEn95Wj40B8d2aoAy0n90xWAShu0Y3ZovVMMkxEm02WIercXXmgO+v8K0iSXWaF0OQPfYH4Cjrp8X3U7DlpTk5OTo7JFH8A2rgNKR9hmJYAAAAASUVORK5CYII=" alt="appointment-reminders" style="padding-bottom:3px;margin-right:5px;text-align:center; width:30px">
                    Notifikasi
                </a>
                <a href="favorite.php" class="w3-bar-item w3-button w3-round w3-large" style="margin-left :6px; margin-right: 10px ;width: 94%; text-align: left; color: #181513;">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAABEElEQVR4nO2aMQ4CIRBFuYceyFNswRc2TOKRLOxMLPVEa6XGQi+gMbpZCxKbAYT8l9Ay+8KfKWCNIZWyXK7m1oUdvNzh5ZF7GTUJL9cSAtAU+ZzEw/qw7/p+ZjICTZExTrklUojobVayNiKbwcvC+nBU7QUnw2vfvCLvovqN7WTIK5IgbkhdhyJVnIhrp0cW6jKuwNTKBSgSgSeiABitCIyWAmC0IjBaCoDRisBoKQBGKwKjpQAYrQiMlgJgtCJMFwRhbb1cktye/FhGgxIfjpQiJd5HVBlFqpb4Fnk9wZmawZTVQ9UyaK3Z0YqI5dT6E6yTUxtTy8mmianV9f0MPpyrb/bppxrZwoVb1SLE5OEJ3fVbDkbibuIAAAAASUVORK5CYII=" alt="book-stack" style="padding-bottom:3px;margin-right:8px;text-align:center; width:25px">
                    Kegemaran 
                </a><br>
                <?PHP
                }if (empty($_SESSION['username'])) {
                ?>
                    <br><p class="w3-small" style="padding-left: 5px;">Untuk mulakan perkongsian anda dan simpan resipi yang anda minat, sila <a href="register.php">daftar atau log masuk.</a></p>
                <?PHP
                }
                ?>
                <!--<img src="sources/header/footer.png" class="w3-round-large" alt="" width="250px" style="margin-left: -16px; margin-top: 311px;">-->



            </div>

            <div class="w3-round-large w3-border" style="margin-left:15.6%; z-index:9999;"> <!--main content-->
                <!--TOP NAVIGATION-->

                <div class="w3-container w3-border-top w3-round-large w3-rest w3-white w3-row fixed-header" style="width: 83.4%; margin-top: 8px; padding:11px">

                    <div class="w3-cell w3-left" style="width:35%;"> <!--IF CONDITION HERE (search hidden)-->