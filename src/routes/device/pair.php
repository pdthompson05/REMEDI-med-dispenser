<?php

session_start();
require_once __DIR__.'/../../config/db.php';
header('Content-Type: application/json');

if (! isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$device_id = $_POST['device_id'] ?? null;

if (! $device_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing device_id']);
    exit;
}

// Only allow pairing if device is unpaired (user_id IS NULL)
$sql = 'UPDATE device SET user_id = ? WHERE device_id = ? AND user_id IS NULL';
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $user_id, $device_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Device paired']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Device already paired or does not exist']);
}

$stmt->close();
$conn->close();
