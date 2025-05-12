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
$sensors = $_POST['sensors'] ?? null;

if (!$device_id || !is_array($sensors)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing device ID or sensor data']);
    exit;
}

$check = $conn->prepare('SELECT device_id FROM device WHERE device_id = ? AND user_id = ?');
$check->bind_param('ii', $device_id, $user_id);
$check->execute();
$res = $check->get_result();
if ($res->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized or device not found']);
    exit;
}
$check->close();

$del = $conn->prepare('DELETE FROM sensor WHERE device_id = ?');
$del->bind_param('i', $device_id);
$del->execute();
$del->close();

$insert = $conn->prepare('INSERT INTO sensor (device_id, med_id, med_count) VALUES (?, ?, ?)');
foreach ($sensors as $sensor) {
    $med_id = $sensor['med_id'];
    $med_count = $sensor['med_count'];
    $insert->bind_param('iii', $device_id, $med_id, $med_count);
    $insert->execute();
}
$insert->close();

echo json_encode(['status' => 'success', 'message' => 'Sensor configuration saved']);
$conn->close();
