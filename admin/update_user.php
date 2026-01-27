<?php
include('connection.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $name = $_POST['name'];
    $password = $_POST['password'];

    // Update query to include PASSWORD
    $sql = "UPDATE USERS SET EMAIL = ?, NAME = ?, PASSWORD = ? WHERE USERNAME = ?";
    $stmt = mysqli_prepare($condb, $sql);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "ssss", $email, $name, $password, $username);

    // Execute the query
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header("Location: buyer_info.php?username=$username");
        exit;
    } else {
        echo "Error updating user profile.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($condb);
}
