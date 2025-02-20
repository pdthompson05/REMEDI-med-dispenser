<?php
session_start();
header("Content-Type: application/json");
require_once "db.php";
global $conn;