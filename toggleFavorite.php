<?php
session_start();
include('connection.php');

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
$sql_check = "SELECT COUNT(*) AS count FROM FAVORITE WHERE KUEHID = :kueh_id AND USERNAME = :username";
$stid_check = oci_parse($condb, $sql_check);
oci_bind_by_name($stid_check, ':kueh_id', $kueh_id);
oci_bind_by_name($stid_check, ':username', $username);
oci_execute($stid_check);
$row = oci_fetch_array($stid_check, OCI_ASSOC);
$isFavorite = ($row['COUNT'] > 0);

if ($isFavorite) {
    // Remove from favorites
    $sql_delete = "DELETE FROM FAVORITE WHERE KUEHID = :kueh_id AND USERNAME = :username";
    $stid_delete = oci_parse($condb, $sql_delete);
    oci_bind_by_name($stid_delete, ':kueh_id', $kueh_id);
    oci_bind_by_name($stid_delete, ':username', $username);
    if (oci_execute($stid_delete)) {
        echo json_encode(['success' => true, 'isFavorite' => false]);
    } else {
        $e = oci_error($stid_delete);
        echo json_encode(['success' => false, 'message' => $e['message']]);
    }
} else {
    // Add to favorites
    $sql_insert = "INSERT INTO FAVORITE (USERNAME, KUEHID, DATEFAV) VALUES (:username, :kueh_id, SYSDATE)";
    $stid_insert = oci_parse($condb, $sql_insert);
    oci_bind_by_name($stid_insert, ':username', $username);
    oci_bind_by_name($stid_insert, ':kueh_id', $kueh_id);
    if (oci_execute($stid_insert)) {
        echo json_encode(['success' => true, 'isFavorite' => true]);
    } else {
        $e = oci_error($stid_insert);
        echo json_encode(['success' => false, 'message' => $e['message']]);
    }
}

oci_close($condb);
