<?php
include('connection.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $name = $_POST['name'];
    $password = $_POST['password']; 

    // Update query to include PASSWORD
    $sql = "UPDATE USERS SET EMAIL = :email, PHONENUM = :phone, NAME = :name, PASSWORD = :password WHERE USERNAME = :username";
    $stmt = oci_parse($condb, $sql);

    // Bind parameters
    oci_bind_by_name($stmt, ":username", $username);
    oci_bind_by_name($stmt, ":email", $email);
    oci_bind_by_name($stmt, ":phone", $phone);
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":password", $password);

    // Execute the query
    $result = oci_execute($stmt);

    if ($result) {
        header("Location: buyer_info.php?username=$username"); 
        exit;
    } else {
        echo "Error updating user profile.";
    }

    
    oci_free_statement($stmt);
    oci_close($condb);
}
?>
