<?php

require_once __DIR__.'/../../config/db.php';
header('Content-Type: application/json');

$device_id = $_POST['device_id'] ?? null;
$med_id = $_POST['med_id'] ?? null;

if (! $device_id || ! $med_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing device or medication ID']);
    exit;
}

// Get sensor ID and user ID
$sql = 'SELECT s.sensor_id, s.med_count, d.user_id
        FROM sensor s
        JOIN device d ON s.device_id = d.device_id
        WHERE s.device_id = ? AND s.med_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $device_id, $med_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Sensor not found']);
    exit;
}

$row = $result->fetch_assoc();
$sensor_id = $row['sensor_id'];
$med_count = $row['med_count'];
$user_id = $row['user_id'];

$stmt->close();

// Update med count
$stmt = $conn->prepare('UPDATE sensor SET med_count = med_count - 1 WHERE sensor_id = ?');
$stmt->bind_param('i', $sensor_id);
$stmt->execute();
$stmt->close();

// Log dose history
$stmt = $conn->prepare('INSERT INTO dose_history (user_id, med_id, sensor_id, taken) VALUES (?, ?, ?, 1)');
$stmt->bind_param('iii', $user_id, $med_id, $sensor_id);
$stmt->execute();
$stmt->close();

echo json_encode(['status' => 'success', 'message' => 'Dose recorded']);
$conn->close();
