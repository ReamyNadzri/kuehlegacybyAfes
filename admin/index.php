<?php
session_start();

if (isset($_SESSION['adminid'])) {
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
  <h5>KUEH LEGACY @ 2025</h5>
</div>
<br>

<div class="w3-container w3-center w3-middle">
  <img src="../sources/admin.png" style="max-width:800px">

  <h2>WARNING!</h2>
  <h4>Now you are in the admin AFAS site.</h4>
  <h4>Please enter your information below.</h4><br>

  <button onclick="document.getElementById('id01').style.display='block'" class="w3-button w3-red w3-large w3-round-large">Login</button>

  <div id="id01" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom" style="max-width:600px">

      <div class="w3-center"><br>
        <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-xlarge w3-transparent w3-display-topright" title="Close Modal">&times;</span>
        <img src="sources/admin.jpg" alt="Avatar" style="width:100px;" class="w3-circle w3-border w3-margin-top">
      </div>

      <form class="w3-container w3-center w3-round-large" action="" method="POST"><br>
        <b>Username</b> <input type="text" name="username" required><br><br>

        <b>Password</b> <input type="password" name="password" required><br><br>
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
  <h5>LEGACY OF KUEH @ 2025</h5>
</footer>
</div>
</body>
</html>

<?php
// Check if POST data exists
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('connection.php');

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT USERNAME, PASSWORD FROM ADMIN WHERE USERNAME = :username AND PASSWORD = :password";
        $stmt = oci_parse($condb, $sql);

        oci_bind_by_name($stmt, ":username", $username);
        oci_bind_by_name($stmt, ":password", $password);

        oci_execute($stmt);

        if ($record = oci_fetch_assoc($stmt)) {
            $_SESSION['adminid'] = $record['USERNAME'];
            echo "<script>window.location.href='mainpage.php';</script>";
        } else {
            echo "<script>alert('Login failed. Please check your username or password.');</script>";
        }

        oci_free_statement($stmt);
    } else {
        echo "<script>alert('Please enter both username and password.');</script>";
    }

    oci_close($condb);
}
?>
