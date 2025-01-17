<!DOCTYPE html>
<html>
<head>
    <title>Google Sign-in Example</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <style>/* Basic Header Styles */
#main-header {
    background-color: #f0f0f0;
    padding: 10px;
    margin-bottom: 20px;
}

#header-content{
   display: flex;
    justify-content: space-between;
    align-items: center;
    margin: auto;
    width: 80%;
    max-width: 1200px;
}

#user-profile {
  display: flex;
  align-items: center;
}

#user-profile img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 10px;
}

/* Main content styles */

#content {
  margin: auto;
    width: 80%;
    max-width: 1200px;
}</style>

    <header id="main-header">
        <div id="header-content">
             <h1>My Application</h1>
             <div id="user-profile" style="display:none;">
                 <img id="profile-pic" src="" alt="Profile Picture" >
                 <span id="user-name"></span>
                 <button id="signout-button">Sign Out</button>
             </div>
             <div id="signin-div">
                <div id="g_id_onload"
                    data-client_id="86003731304-ujapfaslp3bk71imksdn5oq21ebl8i07.apps.googleusercontent.com"
                    data-context="signin"
                    data-ux_mode="popup"
                    data-login_uri="http://localhost/kuehlegacybyAfes/callback.php"
                    data-auto_select="true"
                >
                </div>
                <div class="g_id_signin" data-type="standard" data-size="large" data-theme="outline" data-text="sign_in_with" data-shape="rectangular" data-logo_alignment="left"></div>
            </div>
         </div>
     </header>

    <div id="content">
        <!-- Your main page content here -->
        <p>Welcome to my amazing app!</p>
    </div>

    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        let userToken = null;
        function handleCredentialResponse(response) {
            userToken = response.credential
            console.log("handleCredentialResponse: Encoded JWT ID token: " + response.credential);

            // Send the JWT token to your PHP backend for verification
            fetch('/auth.php', {
               method: 'POST',
               headers: { 'Content-Type': 'application/json' },
               body: JSON.stringify({ token: response.credential })
            }).then((res)=>{
                return res.json()
            }).then(data=>{
                console.log("Data back from the server", data)
                if (data && data.user) {
                    updateUserProfile(data.user);
                    document.cookie = `user=${JSON.stringify(data.user)}; path=/; secure; httponly; samesite=strict`
                  }
            }).catch((err)=>{
                console.log("An error occured: ", err)
                document.getElementById('signin-div').innerHTML = `<p> Could not sign in, please try again </p>`
            })
        }


        function updateUserProfile(user){
            console.log("updateUserProfile is called, user data:", user)
            document.getElementById('signin-div').style.display = "none"
            document.getElementById('user-profile').style.display = "flex";
            document.getElementById('profile-pic').src = user.picture;
            document.getElementById('user-name').textContent = user.name;
        }
        function signOut() {
           if(!userToken){
                document.cookie = `user=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
                document.getElementById('user-profile').style.display = "none";
                document.getElementById('signin-div').style.display = "flex";
               return;
           }
            google.accounts.id.disableAutoSelect()
            google.accounts.id.revoke(userToken, done => {
                console.log("User Revoked", done);
                document.cookie = `user=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
                document.getElementById('user-profile').style.display = "none";
                document.getElementById('signin-div').style.display = "flex";
                userToken=null;
            });
        }

        window.onload = (event) => {
            console.log("Window.onload event is called");
             const userCookie = document.cookie.match('(^|;)\\s*user=([^;]+)');
            if (userCookie) {
                let user = JSON.parse(userCookie.pop());
                if(user){
                    updateUserProfile(user);
                 }
             }
            document.getElementById("signout-button").addEventListener("click", signOut)
        };
      </script>
</body>
</html>