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
// Debug: Check if the authorization code is present
if (!isset($_GET['code'])) {
    die('Authorization code not found.');
}

// Handle the authorization code
try {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    // Get user info
    $oauth2 = new Google\Service\Oauth2($client);
    $userInfo = $oauth2->userinfo->get();

    // Store user data in session
    $_SESSION['user'] = [
        'email' => $userInfo->email,
        'name' => $userInfo->name,
        'picture' => $userInfo->picture,
    ];

    // Redirect to a welcome page
    header('Location: welcome.php');
    exit();
} catch (Exception $e) {
    // Handle errors
    die('Error: ' . $e->getMessage());
}
?>