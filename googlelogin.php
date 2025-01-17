<!DOCTYPE html>
<html>
<head>
    <title>Google Sign-in Example</title>
</head>
<body>
<div id="g_id_onload"
    data-client_id="86003731304-ujapfaslp3bk71imksdn5oq21ebl8i07.apps.googleusercontent.com"
    data-context="signin"
    data-ux_mode="popup"
    data-login_uri="http://localhost/kuehlegacybyAfes/callback.php"  // YOUR REDIRECT URI
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