<?php
session_start();

require __DIR__ . "/vendor/autoload.php";

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$client = new Google\Client;

$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);

if (!isset($_GET["code"])) {
    header("Location: login.php"); // Redirect back to the login page
    exit();
}

$token = $client->fetchAccessTokenWithAuthCode($_GET["code"]);

$client->setAccessToken($token["access_token"]);

$oauth = new Google\Service\Oauth2($client);

$userinfo = $oauth->userinfo->get();

// Store user info in the session

$_SESSION['google_user'] = [
    'email' => $userinfo->email,
    'familyName' => $userinfo->familyName,
    'givenName' => $userinfo->givenName,
    'name' => $userinfo->name,
    'picture' => $userinfo->picture
];


// Include the connection file
include('connection.php');

$emailGoogle = $userinfo->email;
$nameGoogle = $userinfo->givenName;
$usernameGoogle = $userinfo->name;
$pictureGoogle = $userinfo->picture;

$sql = "SELECT * FROM users WHERE email = ?";

$stmt = mysqli_prepare($condb, $sql);
mysqli_stmt_bind_param($stmt, 's', $emailGoogle);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$userG = mysqli_fetch_assoc($result);


if (!$userG) {

    $sqlG = "INSERT INTO users (username, email, name, image) VALUES (?, ?, ?, ?)";

    $stmtG = mysqli_prepare($condb, $sqlG);
    mysqli_stmt_bind_param($stmtG, 'ssss', $usernameGoogle, $emailGoogle, $nameGoogle, $pictureGoogle);

    $resultG = mysqli_stmt_execute($stmtG);
    if ($resultG) {
        // Store session data
        $_SESSION['username'] = $usernameGoogle;
        $_SESSION['usernameimage'] = $pictureGoogle;

        // Redirect to index page
        header("Location: index.php");
        exit();
    } else {
        echo "Error: Registration failed.";
    }
    mysqli_stmt_close($stmtG);
} else {
    // Successful login
    $_SESSION['username'] = $userG['USERNAME'];

    // Redirect to index page
    header("Location: index.php");
    exit();
}

mysqli_stmt_close($stmt);
mysqli_close($condb);
