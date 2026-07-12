<?php
session_start();
include(__DIR__ . '/../connection.php'); // Ensure database connection

// Determine logged-in username
$username = isset($_SESSION['google_user']) ? $_SESSION['google_user']['name'] : $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['KUEHID']) && !empty($username)) {
    $kuehID = $_POST['KUEHID'];

    $sql = "DELETE FROM FAVORITE WHERE KUEHID = ? AND USERNAME = ?";
    $stmt = mysqli_prepare($condb, $sql);
    mysqli_stmt_bind_param($stmt, 'is', $kuehID, $username);

    $response = ["success" => false];

    if (mysqli_stmt_execute($stmt)) {
        $response["success"] = true;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($condb);

    echo json_encode($response);
} else {
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
}
