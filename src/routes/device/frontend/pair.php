<?php

require_once __DIR__.'/../../../config/db.php';
session_start();
header('Content-Type: application/json');

if (! isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$device_id = $_POST['device_id'] ?? null;

if (! $device_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing device ID']);
    exit;
}

$sql = 'UPDATE device SET user_id = ? WHERE device_id = ? AND user_id IS NULL';
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $user_id, $device_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['device_id'] = $device_id;
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Pairing failed. Device may already be paired.']);
}

$stmt->close();
$conn->close();
