<?php
// php & Oracle DB connection file  
$user = "kuehlegacy"; // Oracle username  
$pass = "kuehlegacy"; // Oracle password  
$host = "localhost:1521/xe"; 


$condb = oci_connect($user, $pass, $host); 

if (!$condb) {
    $e = oci_error();
    echo "Connection failed: " . $e['message'];
} else {
    echo "";
}
?>
