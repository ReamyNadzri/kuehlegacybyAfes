<?php
require_once 'vendor/autoload.php';
session_start();

// Initialize the Google Client
$client = new Google\Client();
$client->setClientId('86003731304-4p541pfhvb31digm8o2atjdck8rv15v0.apps.googleusercontent.com'); // Replace with your Client ID
$client->setClientSecret('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'); // Replace with your Client Secret
$client->setRedirectUri('http://localhost/kuehlegacybyAfes/callback.php');
$client->addScope('email');
$client->addScope('profile');

// Generate the authentication URL
$authUrl = $client->createAuthUrl();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login with Google</title>
</head>
<body>
    <h1>Login with Google</h1>
    <a href="<?php echo $authUrl; ?>">Sign in with Google</a>
</body>
</html>