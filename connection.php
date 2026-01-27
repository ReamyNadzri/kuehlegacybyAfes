<?php
// MySQL Database Connection with Environment Variables
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database connection parameters from .env
$db_host = $_ENV['DB_HOST'];
$db_user = $_ENV['DB_USER'];
$db_pass = $_ENV['DB_PASS'];
$db_name = $_ENV['DB_NAME'];

// Create MySQLi connection
$condb = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$condb) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($condb, "utf8mb4");
