<?php
session_start();
include('connection.php'); // Ensure database connection

// Determine logged-in username
$username = isset($_SESSION['google_user']) ? $_SESSION['google_user']['name'] : $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['KUEHID']) && !empty($username)) {
    $kuehID = $_POST['KUEHID'];

    $sql = "DELETE FROM FAVORITE WHERE KUEHID = :kuehID AND USERNAME = :username";
    $stmt = oci_parse($condb, $sql);
    oci_bind_by_name($stmt, ':kuehID', $kuehID);
    oci_bind_by_name($stmt, ':username', $username);

    $response = ["success" => false];

    if (oci_execute($stmt)) {
        $response["success"] = true;
    }

    echo json_encode($response);
} else {
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
}
?>
