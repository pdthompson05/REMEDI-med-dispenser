<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

require_once "db.php"; // DB connection
global $conn;

?>