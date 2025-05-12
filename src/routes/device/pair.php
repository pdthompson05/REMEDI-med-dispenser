<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$device_id = $_POST['device_id'] ?? null;

if (!$device_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing device ID']);
    exit;
}

$check = $conn->prepare('SELECT user_id FROM device WHERE device_id = ?');
$check->bind_param('i', $device_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Device not found']);
    exit;
}

$row = $result->fetch_assoc();
if ($row['user_id'] !== null) {
    echo json_encode(['status' => 'error', 'message' => 'Device already paired']);
    exit;
}
$check->close();

$pair = $conn->prepare('UPDATE device SET user_id = ? WHERE device_id = ?');
$pair->bind_param('ii', $user_id, $device_id);
if ($pair->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Device successfully paired']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}
$pair->close();
$conn->close();
