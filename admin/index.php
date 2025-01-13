<?php
# Start the session
session_start();

if ($_SESSION['adminid']) {
  echo "<script>window.location.href='mainpage.php';</script>";
} 
?>

<!DOCTYPE html>
<html>
<title>AFAS Admin Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<body>

<div class="w3-container w3-center w3-red">
  <h5>KUEH LEGACY 2019 @ 2025</h5>
</div>
<br>

<div class="w3-container w3-center w3-middle">
  <img src="../sources/admin.png" style="max-width:800px">

  <h2>WARNING!</h2>
  <h4>Now you in admin AFAS site.</h4>
  <h4>Please enter your information down below.</h4><br>

  <button onclick="document.getElementById('id01').style.display='block'" class="w3-button w3-red w3-large w3-round-large">Login</button>

  <div id="id01" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">

      <div class="w3-center"><br>
        <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">&times;</span>
        <img src="sources/admin.jpg" alt="Avatar" style="width:100px;" class="w3-circle w3-border w3-margin-top">
      </div>

      <form class="w3-container w3-center w3-round-large" action="" method="POST"><br>
        <b>Admin ID</b> <input type="text" name="admin_ID"><br><br>

        <b>Password</b> <input type="password" name="adminPass"><br><br>
        <input type="submit" class="w3-center w3-round-large w3-button w3-bar w3-red" value="Login"><br>
        <input class="w3-check w3-margin-top" type="checkbox" checked="checked"> Remember My ID <br><br>
      </form>

    </div>
  </div>
</div>
<br>
<br>

<br>
<br>
<footer class="w3-container w3-red w3-center">
  <h5>ABE FIWAN AUTO SALES 2019 @ 2025</h5>
</footer>
</div>
</body>
</html>

<?php
# Check if POST data exists
if (!empty($_POST)) {
  include('connection.php');


  if (isset($_POST['admin_ID']) && isset($_POST['adminPass'])) {
    $admin_ID = $_POST['admin_ID'];
    $adminPass = $_POST['adminPass'];
  
    $arahan_sql_cari = "
                          SELECT USERNAME, PASSWORD
                          FROM ADMIN
                          WHERE USERNAME = :ADMIN_ID AND PASSWORD = :ADMINPASS
                          ";
  
    $stmt = oci_parse($condb, $arahan_sql_cari);
  
    $bind_admin_id = oci_bind_by_name($stmt, ":ADMIN_ID", $admin_ID);
    $bind_admin_pass = oci_bind_by_name($stmt, ":ADMINPASS", $adminPass);

    $execute = oci_execute($stmt);
  
    if ($rekod = oci_fetch_assoc($stmt)) {
      
      $_SESSION['adminid'] = $rekod['USERNAME'];
      echo "<script>window.location.href='mainpage.php';</script>";
    } else {
      echo "<script>alert('Login Failure');</script>";
    }
  
    oci_free_statement($stmt);
  } else {
    echo "<script>alert('Missing Admin ID or Password');</script>";
  }
  
  oci_close($condb);
}
?>

<?php include('footer.php'); ?>
