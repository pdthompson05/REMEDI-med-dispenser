<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");
require_once __DIR__ . '/../../config/db.php';
global $conn;

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$med_name = isset($_POST['med_name']) ? trim($_POST['med_name']) : '';
$strength = isset($_POST['strength']) ? trim($_POST['strength']) : '';
$rx_number = isset($_POST['rx_number']) ? trim($_POST['rx_number']) : '';
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

if (empty($med_name) || empty($strength) || empty($rx_number) || $quantity <= 0) {
    echo json_encode(["status" => "error", "message" => "All fields are required and quantity must be greater than 0"]);
    exit;
}

$sql = "INSERT INTO med (user_id, med_name, strength, rx_number, quantity, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssi", $user_id, $med_name, $strength, $rx_number, $quantity);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Medication added successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "SQL Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>