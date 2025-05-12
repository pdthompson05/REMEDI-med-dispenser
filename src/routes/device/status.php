<?php
require_once __DIR__.'/../../config/db.php';
header('Content-Type: application/json');

$device_id = $_POST['device_id'] ?? null;
$temp = $_POST['temp'] ?? null;

if (! $device_id || ! is_numeric($temp)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$sql = 'UPDATE device SET temperature = ?, connected = 1, updated_at = NOW() WHERE device_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('di', $temp, $device_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Device status updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
