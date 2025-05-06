<?php

require_once __DIR__.'/../../config/db.php';
$device_id = $_POST['device_id'] ?? null;
$temp = $_POST['temp'] ?? null;
$magnet = $_POST['magnet'] ?? null;

if (! $device_id || ! is_numeric($temp) || ! is_numeric($magnet)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$sql = 'UPDATE device SET temperature = ?, magnet = ?, connected = 1 WHERE device_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('ddi', $temp, $magnet, $device_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Device updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
