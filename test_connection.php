<?php
// Test Database Connection
require 'connection.php';

echo "=== DATABASE CONNECTION TEST ===\n\n";

if (isset($condb) && $condb) {
    echo "✓ Database Connected Successfully!\n";
    echo "  Host: " . $_ENV['DB_HOST'] . "\n";
    echo "  Database: " . $_ENV['DB_NAME'] . "\n";
    echo "  User: " . $_ENV['DB_USER'] . "\n\n";

    // Test query
    echo "=== TESTING BASIC QUERIES ===\n\n";

    // Count tables
    $tables = ['ADMIN', 'KUEH', 'USERS', 'FAVORITE', 'ITEMS', 'STEPS', 'FOODTYPE', 'METHOD', 'ORIGIN', 'POPULARITY', 'SHOP'];

    foreach ($tables as $table) {
        $sql = "SELECT COUNT(*) as count FROM $table";
        $result = mysqli_query($condb, $sql);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo "✓ $table: {$row['count']} records\n";
        } else {
            echo "✗ $table: Error - " . mysqli_error($condb) . "\n";
        }
    }

    echo "\n=== TESTING PREPARED STATEMENTS ===\n\n";

    // Test prepared statement
    $sql = "SELECT kuehId, kuehName FROM KUEH LIMIT 5";
    $stmt = mysqli_prepare($condb, $sql);
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        echo "✓ Prepared statements working\n";
        echo "  Sample recipes:\n";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "    - {$row['kuehName']} (ID: {$row['kuehId']})\n";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "✗ Prepared statement failed\n";
    }

    mysqli_close($condb);
} else {
    echo "✗ Database Connection Failed!\n";
    echo "  Error: " . mysqli_connect_error() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
