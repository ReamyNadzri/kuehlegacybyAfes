<?php
session_start();

require __DIR__ . "/vendor/autoload.php";

$client = new Google\Client;

$client->setClientId("86003731304-ujapfaslp3bk71imksdn5oq21ebl8i07.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/kuehlegacybyAfes/callback.php");

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

    $sql = "SELECT * FROM users WHERE email = :email";


    $stmt = oci_parse($condb, $sql);

    oci_bind_by_name($stmt, ':email', $emailGoogle);


    oci_execute($stmt);

    // Fetch the result
    $userG = oci_fetch_assoc($stmt);


    if(!$userG){

        $sqlG = "INSERT INTO users (username, email, name, image) VALUES (:username, :email, :name, :image)";

        $stmtG = oci_parse($condb, $sqlG);

        oci_bind_by_name($stmtG, ':username', $usernameGoogle);
        oci_bind_by_name($stmtG, ':email', $emailGoogle);
        oci_bind_by_name($stmtG, ':name', $nameGoogle);
        oci_bind_by_name($stmtG, ':image', $pictureGoogle);

        $resultG = oci_execute($stmtG);
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
    }else{
        // Successful login
        $_SESSION['username'] = $userG['USERNAME'];

        // Redirect to index page
         header("Location: index.php");
        exit();

    }
?>