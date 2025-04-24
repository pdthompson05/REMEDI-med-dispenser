<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once __DIR__.'/../../config/db.php';

if (! isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$device_id = $_POST['device_id'] ?? null;
$pairing_code = $_POST['pairing_code'] ?? null;

if (! $device_id || ! $pairing_code) {
    echo json_encode(['status' => 'error', 'message' => 'Missing device ID or pairing code']);
    exit;
}

$sql = 'UPDATE device SET user_id = ?, connected = 1 WHERE device_id = ? AND pairing_code = ? AND user_id IS NULL';

$stmt = $conn->prepare($sql);
$stmt->bind_param('iis', $user_id, $device_id, $pairing_code);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Device paired successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid pairing code or device already paired']);
}

$stmt->close();
$conn->close();
