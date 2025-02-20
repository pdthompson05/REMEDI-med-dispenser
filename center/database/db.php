<?php
$DB_HOST = "localhost";
$DB_USER = "section3";
$DB_PASS = "SmartMeds-SP2025";
$DB_NAME = "db_remedi";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>