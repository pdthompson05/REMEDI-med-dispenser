<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");
require_once "db.php";
global $conn;

$med_name = isset($_POST['med_name']) ? trim($_POST['med_name']) : '';
$amount_pills = isset($_POST['amount_pills']) ? trim($_POST['amount_pills']) : '';
$frequency = isset($_POST['frequency']) ? trim($_POST['frequency']) : '';
$hrs_btwn = isset($_POST['hrs_btwn']) ? trim($_POST['hrs_btwn']) : '';
$start_time = isset($_POST['start_time']) ? trim($_POST['start_time']) : '';
$cldr_day = isset($_POST['cldr_day']) ? trim($_POST['cldr_day']) : '';
$reminder = isset($_POST['reminder']) ? trim($_POST['reminder']) : '';

if (empty($med_name) || empty($amount_pills) || empty($frequency) || empty($hrs_btwn) || empty($start_time) || empty($cldr_day)) {
    echo json_encode(["status" => "error", "message" => "Fields required"]);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql_insert_med = "INSERT INTO med (user_id, med_name, amount_pills, frequency, hrs_btwn, start_time, cldr_day, reminder, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
$stmt_user = $conn->prepare($sql_insert_med);
$stmt_user->bind_param("isisisss", $user_id, $med_name, $amount_pills, $frequency, $hrs_btwn, $start_time, $cldr_day, $reminder);

?>