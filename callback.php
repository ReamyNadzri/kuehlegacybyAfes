<?php
// Forward Google OAuth callback parameters to the organized auth/callback.php
header("Location: auth/callback.php?" . $_SERVER['QUERY_STRING']);
exit();
