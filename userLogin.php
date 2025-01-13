<?php
// Include the connection file
include('connection.php');


session_start();

// Check if the form is submitted
if (isset($_POST['login'])) {
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    $sql = "SELECT * FROM users WHERE email = :email AND password = :password";

   
    $stmt = oci_parse($condb, $sql);


    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':password', $password);

    
    oci_execute($stmt);

    // Fetch the result
    $user = oci_fetch_assoc($stmt);

    if ($user) {
        // Successful login
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
   
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="w3-container w3-display-middle" style="max-width: 400px; background-color: #fff; padding: 40px; border-radius: 8px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
        <h2 class="w3-center w3-text-gray">Login Masuk</h2>

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

        <p class="w3-center w3-margin-top">Masih lagi tiada akaun? <a href="userRegister.php" class="w3-text-orange">Daftar Sekarang</a></p>
    </div>
</body>
</html>
