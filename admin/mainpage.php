<?php
# Memanggil fail header_admin.php
include('header_admin.php');
# Memanggil fail connection dari folder luaran
# memanggil fail connection
include('connection.php');

$adminid = $_SESSION['adminid'];

if (!isset($adminid)) {
  die("<script>
  window.location.href='index.php';</script>");
}

?>

<head>
  <meta charset="utf-8">
  <title></title>

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.0/css/all.css">
  <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-colors-ios.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.js"></script>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="https://bootstrapmade.com/assets/css/demo.css?v=5.3">
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">

</head>

<?php

// Assuming $condb is the valid Oracle connection resource
// It should be included in a `connection.php` file as mentioned before

// -----------------------------------------------------------------------------
// arahan SQL untuk mencari bilangan kereta yang ada yang pernah berdaftar (jadual kereta)
$arahan_sql_bilkueh = "SELECT COUNT(KUEHID) AS BILKUEH FROM KUEH";

// Laksanakan arahan mencari bilangan kereta yang ada yang pernah berdaftar
$laksana_sql_bilkueh = oci_parse($condb, $arahan_sql_bilkueh);

$execute_bilkueh = oci_execute($laksana_sql_bilkueh);

// pembolehubah $rekod_bilkereta mengambil data bilangan kereta yang pernah berdaftar
$rekod_bilkueh = oci_fetch_assoc($laksana_sql_bilkueh);

/* // -----------------------------------------------------------------------------
// arahan SQL untuk mencari bilangan kereta yang telah ada di jual (jadual pembelian)
$arahan_sql_bilkeretajual = "SELECT COUNT(KUEHID) AS BIL_KUEH FROM PURCHASE";

// laksanakan arahan  mencari bilangan kereta yang telah ada di jual
$laksana_sql_bilkeretajual = oci_parse($condb, $arahan_sql_bilkeretajual);

$execute_bilkeretajual = oci_execute($laksana_sql_bilkeretajual);

// pembolehubah $rekod_bilkeretajual mengambil data bilangan kereta yang telah dijual
$rekod_bilkeretajual = oci_fetch_assoc($laksana_sql_bilkeretajual);

// -----------------------------------------------------------------------------
// arahan SQL untuk mengira jumlah harga_awal kereta yang telah dijual
$arahan_sql_untung = "SELECT SUM(INITIALPRICE) AS untung FROM CAR
WHERE NUMPLATE IN (SELECT NUMPLATE FROM PURCHASE)";

// laksanakan arahan mengira jumlah harga_awal kereta yang telah dijual
$laksana_sql_untung = oci_parse($condb, $arahan_sql_untung);

$execute_untung = oci_execute($laksana_sql_untung);


// pemboleh ubah $rekod_untung mengambil data jumlah keuntungan
$rekod_untung = oci_fetch_assoc($laksana_sql_untung);
 */
// -----------------------------------------------------------------------------
$arahan_sql_biladmin = "SELECT COUNT(USERNAME) AS biladmin FROM ADMIN";

// Laksanakan arahan mencari bilangan kereta yang ada yang pernah berdaftar
$laksana_sql_biladmin = oci_parse($condb, $arahan_sql_biladmin);

$execute_biladmin = oci_execute($laksana_sql_biladmin);

// pembolehubah $rekod_biladmin mengambil data bilangan admin
$rekod_biladmin = oci_fetch_assoc($laksana_sql_biladmin);

/* users count */
$arahan_sql_bilcust = "SELECT COUNT(USERNAME) AS bilcust FROM USERS";

// Laksanakan arahan mencari bilangan pelanggan
$laksana_sql_bilcust = oci_parse($condb, $arahan_sql_bilcust);

$execute_bilcust = oci_execute($laksana_sql_bilcust);

// pembolehubah $rekod_bilcust mengambil data bilangan pelanggan
$rekod_bilcust = oci_fetch_assoc($laksana_sql_bilcust);

// --------------------------------------------------------------------------------------------------------------------
$arahan_sql_biltype = "SELECT COUNT(FOODTYPECODE) AS biltype FROM FOODTYPE";

// Laksanakan arahan mencari bilangan kereta yang ada yang pernah berdaftar
$laksana_sql_biltype = oci_parse($condb, $arahan_sql_biltype);


$execute_biltype = oci_execute($laksana_sql_biltype);

// pembolehubah $rekod_biladmin mengambil data bilangan admin
$rekod_biltype = oci_fetch_assoc($laksana_sql_biltype);


// ---  End of Queries ----
// Free resources at the end to avoid issues later
oci_free_statement($laksana_sql_bilkueh);
oci_free_statement($laksana_sql_biltype);
oci_free_statement($laksana_sql_biladmin);
oci_free_statement($laksana_sql_bilcust);

// Note that connection is not closed, as it is used by the next scripts

?>

<body style="background-image: none!important; background-color: white; scroll-behavior: smooth; overflow: scroll!important;">
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

  <!-- Dashboard -->
  <div style=" margin:0 auto; width:80%;">

    <header class="w3-container" style="padding: top 5px;">
      <h3><i class="fa fa-dashboard"></i> Quick Overview</h3>
    </header>

    <div class="w3-row-padding w3-margin-bottom">
      <div class="w3-quarter">
        <div class="w3-container w3-card-2 w3-border w3-round-xlarge w3-padding-16">
          <div class="w3-left "><i class="fas fa-cookie w3-xxxlarge" style="padding-top: 10px;"></i></div>
          <div class="w3-right">
            <h2 class="num">
              <?php echo $rekod_bilkueh['BILKUEH']; ?>
            </h2>
          </div>
          <div class="w3-clear"></div>
          <h3>Total Kueh</h3>
        </div>
      </div>
      <div class="w3-quarter">
        <div class="w3-container w3-card-2 w3-border w3-round-xlarge w3-padding-16">
          <div class="w3-left "><i class="fas fa-cookie w3-xxxlarge" style="padding-top: 10px;"></i></div>
          <div class="w3-right">
            <h2 class="num">
              <?php echo $rekod_biltype['BILTYPE']; ?>
            </h2>
          </div>
          <div class="w3-clear"></div>
          <h3>Total Kueh Type</h3>
        </div>
      </div>
      <!-- <div class="w3-quarter">
        <div class="w3-container w3-card-2 w3-border w3-round-xlarge w3-padding-16">
          <div class="w3-left"><i class="fas fa-cart-arrow-down w3-xxxlarge" style="padding-top: 10px;"></i></div>
          <div class="w3-right">
            <h2 class="num">
              <?php /* echo $rekod_bilkeretajual['BIL_KUEH']; */ ?>
            </h2>
          </div>
          <div class="w3-clear"></div>
          <h3>Sold</h3>
        </div>
      </div> -->
      <!-- <div class="w3-quarter">
        <div class="w3-container w3-card-2 w3-border w3-round-xlarge w3-padding-16">
          <div class="w3-left"><i class="fas fa-pause w3-xxxlarge" style="padding-top: 10px;"></i></div>
          <div class="w3-right">
            <h2 class="num">
              <?php /* echo $rekod_bilkereta['BIL_KUEH'] - $rekod_bilkeretajual['BIL_KUEH']; */ ?>
            </h2>
          </div>
          <div class="w3-clear"></div>
          <h3>Available</h3>
        </div>
      </div> -->
      <!-- <div class="w3-quarter">
        <div class="w3-container w3-card-2 w3-border w3-round-xlarge w3-padding-16">
          <div class="w3-left"><i class="far fa-money-bill-alt w3-xxxlarge" style="padding-top: 10px;"></i></div>
          <div class="w3-right">
            <h3 style="position:relative;bottom:-20px"></h3>
            <h2 class="num">
              <?php /* echo $rekod_untung['UNTUNG']; */ ?>
            </h2>
          </div>
          <div class="w3-clear"></div>
          <h3>Total Sale (RM)</h3>
        </div>
      </div> -->
    </div>

    <div class="w3-row-padding w3-margin-bottom">
      <div class="w3-quarter">
        <div class="w3-container w3-card-2 w3-border w3-round-xlarge w3-padding-16">
          <div class="w3-left "><i class="fas fa-user w3-xxxlarge" style="padding-top: 10px;"></i></div>
          <div class="w3-right">
            <h2 class="num">
              <?php echo $rekod_bilcust['BILCUST']; ?>
            </h2>
          </div>
          <div class="w3-clear"></div>
          <h3>Total Users</h3>
        </div>
      </div>
      <div class="w3-quarter">
        <div class="w3-container w3-card-2 w3-border w3-round-xlarge w3-padding-16">
          <div class="w3-left"><i class="fas fa-user w3-xxxlarge" style="padding-top: 10px;"></i></div>
          <div class="w3-right">
            <h2 class="num">
              <?php echo $rekod_biladmin['BILADMIN']; ?>
            </h2>
          </div>
          <div class="w3-clear"></div>
          <h3>Total Admin</h3>
        </div>
      </div>
      <div class="w3-half w3-container" style="height:60px">
        <a href="../index.php">
          <div class="w3-container w3-card-2 w3-border w3-round-xlarge w3-padding-16">
            <div class="w3-right w3-cell">
              <img class="w3-col w3-margin w3-row-padding" style="height:90px; width: auto;" src='sources/logo1.png'>
            </div>
            <div class="w3-clear w3-cell">
              <h3>View the Website</h3>
            </div>
          </div>
        </a>
      </div>
    </div>
    <hr>

    <script type="text/javascript">
      $(".num").counterUp({
        delay: 10,
        time: 2000
      });
    </script>

  </div>
</body>

</html>