<?php
require __DIR__ . "/../vendor/autoload.php";
if (!isset($_ENV['GOOGLE_CLIENT_ID'])) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Google Sign-in Example</title>
</head>
<body>
<div id="g_id_onload"
    data-client_id="<?php echo $_ENV['GOOGLE_CLIENT_ID'] ?? ''; ?>"
    data-context="signin"
    data-ux_mode="popup"
    data-login_uri="<?php echo $_ENV['GOOGLE_REDIRECT_URI'] ?? 'http://localhost/kuehlegacybyAfes/auth/callback.php'; ?>"
    data-auto_select="true"
    >
 </div>
 <div class="g_id_signin" data-type="standard" data-size="large" data-theme="outline" data-text="sign_in_with" data-shape="rectangular" data-logo_alignment="left"></div>

<script src="https://accounts.google.com/gsi/client" async defer></script>
<script>
    function handleCredentialResponse(response) {
      console.log("Encoded JWT ID token: " + response.credential);

      // Send the JWT token to your PHP backend for verification
      fetch('/auth.php', {
         method: 'POST',
         headers: { 'Content-Type': 'application/json' },
         body: JSON.stringify({ token: response.credential })
      }).then((res)=>{
         return res.json()
      }).then(data=>{
        console.log("Data back from the server", data)
      })
    }
  </script>
</body>
</html>