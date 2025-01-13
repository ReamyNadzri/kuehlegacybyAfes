<?PHP
// php & Oracle DB connection file  
$user = "kuehlegacy"; //oracle username  
$pass = "kuehlegacy"; //Oracle password 
$host = "localhost:1521/xe"; //server name or ip address 
# membuka hubungan antara laman dan pangkalan data.
# menghantar 4 parameter asas iaitu
# nama host - ("localhost"), username SQL ("root"), katalaluan SQL (""), nama pangkalan data ("kereta_terpakai_basic")
$condb=oci_connect($user, $pass, $host); 
?>