<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome, <?php echo $user['name']; ?>!</h1>
    <p>Email: <?php echo $user['email']; ?></p>
    <img src="<?php echo $user['picture']; ?>" alt="Profile Picture">
    <br>
    <a href="logout.php">Logout</a>
</body>
</html>