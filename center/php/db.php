<?php
function loadEnv($path) {
    if (!file_exists($path)) {
        die("Error: .env file not found at $path");
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}
loadEnv(__DIR__ . '/../../.env');

$DB_HOST = getenv('DB_HOST');
$DB_USER = getenv('DB_USER');
$DB_PASS = getenv('DB_PASS');
$DB_NAME = getenv('DB_NAME');
$MAIL=getenv('MAIL');

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        die("Database connection failed: " . $conn->connect_error);
    }
    error_log("Database connection successful!");
} catch (Exception $e) {
    error_log("Connection error: " . $e->getMessage());
    die("Database connection failed: " . $e->getMessage());
}
?>