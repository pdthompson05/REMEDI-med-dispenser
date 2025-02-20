<?php
// MySQLi
$DB_HOST = "localhost";
$DB_USER = "testuser";
$DB_PASS = "testpassword";
$DB_NAME = "testdb";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
echo "Connected successfully!";
?>
