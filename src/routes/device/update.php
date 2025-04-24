<?php

require_once __DIR__.'/../../config/db.php';

$device_id = $_POST['device_id'];
$temp = $_POST['temp'];
$magnet = $_POST['humidity'];
$timestamp = date('Y-m-d H:i:s');

$sql = 'UPDATE device SET temperature = ?, humidity = ?, updated_at = ?, connected = 1 WHERE device_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('ddsi', $temp, $magnet, $timestamp, $device_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Device updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
