<?php
session_start();
require_once __DIR__.'/../../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) && !isset($_GET['device_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT d.device_id, d.pairing_code, d.connected, m.med_name, s.sensor_id, s.med_count
        FROM device d
        LEFT JOIN sensor s ON d.device_id = s.device_id
        LEFT JOIN med m ON s.med_id = m.med_id
        WHERE d.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$devices = [];
while ($row = $result->fetch_assoc()) {
    $devices[$row['device_id']]['device_id'] = $row['device_id'];
    $devices[$row['device_id']]['pairing_code'] = $row['pairing_code'];
    $devices[$row['device_id']]['connected'] = $row['connected'];
    $devices[$row['device_id']]['slots'][] = [
        'sensor_id' => $row['sensor_id'],
        'med_name' => $row['med_name'],
        'med_count' => $row['med_count']
    ];
}

echo json_encode(['status' => 'success', 'data' => array_values($devices)]);
