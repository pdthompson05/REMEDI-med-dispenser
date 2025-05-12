<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

$device_id = $_GET['device_id'] ?? null;
if (!$device_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing device ID']);
    exit;
}

$stmt = $conn->prepare('SELECT med_id, med_count FROM sensor WHERE device_id = ?');
$stmt->bind_param('i', $device_id);
$stmt->execute();
$result = $stmt->get_result();

$sensors = [];
while ($row = $result->fetch_assoc()) {
    $sensors[] = $row;
}

echo json_encode([
    'status' => 'success',
    'device_id' => $device_id,
    'sensors' => $sensors
]);