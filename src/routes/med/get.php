<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header('Content-Type: application/json');

require_once __DIR__.'/../../config/db.php';
global $conn;

if (! isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = 'SELECT med_id, med_name, strength, rx_number, quantity FROM med WHERE user_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$meds = [];
while ($row = $result->fetch_assoc()) {
    $meds[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $meds]);

$stmt->close();
$conn->close();
