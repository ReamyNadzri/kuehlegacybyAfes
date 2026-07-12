<?php
session_start();
include(__DIR__ . '/../connection.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Please Login to Add Favourite.']);
    exit;
}

// Get the kueh_id from the POST request
$data = json_decode(file_get_contents('php://input'), true);
$kueh_id = $data['kueh_id'] ?? null;

if (!$kueh_id) {
    echo json_encode(['success' => false, 'message' => 'Kueh ID is missing.']);
    exit;
}

// Get the username from the session
$username = $_SESSION['username'];

// Check if the kueh is already in the user's favorites
$sql_check = "SELECT COUNT(*) AS count FROM FAVORITE WHERE KUEHID = ? AND USERNAME = ?";
$stmt_check = mysqli_prepare($condb, $sql_check);
mysqli_stmt_bind_param($stmt_check, 'is', $kueh_id, $username);
mysqli_stmt_execute($stmt_check);
$result = mysqli_stmt_get_result($stmt_check);
$row = mysqli_fetch_assoc($result);
$isFavorite = ($row['count'] > 0);
mysqli_stmt_close($stmt_check);

if ($isFavorite) {
    // Remove from favorites
    $sql_delete = "DELETE FROM FAVORITE WHERE KUEHID = ? AND USERNAME = ?";
    $stmt_delete = mysqli_prepare($condb, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, 'is', $kueh_id, $username);
    if (mysqli_stmt_execute($stmt_delete)) {
        echo json_encode(['success' => true, 'isFavorite' => false]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($condb)]);
    }
    mysqli_stmt_close($stmt_delete);
} else {
    // Add to favorites
    $sql_insert = "INSERT INTO FAVORITE (USERNAME, KUEHID, DATEFAV) VALUES (?, ?, NOW())";
    $stmt_insert = mysqli_prepare($condb, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, 'si', $username, $kueh_id);
    if (mysqli_stmt_execute($stmt_insert)) {
        echo json_encode(['success' => true, 'isFavorite' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($condb)]);
    }
    mysqli_stmt_close($stmt_insert);
}

mysqli_close($condb);
