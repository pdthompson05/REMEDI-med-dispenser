<?php
require_once __DIR__.'/../../config/db.php';
header('Content-Type: application/json');

$device_id = $_POST['device_id'] ?? null;
$slot_number = $_POST['slot_number'] ?? null; // Expected: 1–4
$magnet_value = $_POST['magnet'] ?? null;

if (!is_numeric($device_id) || !in_array($slot_number, [1, 2, 3, 4]) || !is_numeric($magnet_value)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

// Get the correct sensor row
$sql = "SELECT s.sensor_id, s.med_id, d.user_id, s.med_count
        FROM sensor s
        JOIN device d ON s.device_id = d.device_id
        WHERE s.device_id = ? LIMIT 4 OFFSET ?";
$stmt = $conn->prepare($sql);
$offset = $slot_number - 1;
$stmt->bind_param("ii", $device_id, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Sensor not found']);
    exit;
}

$sensor = $result->fetch_assoc();
$sensor_id = $sensor['sensor_id'];
$med_id = $sensor['med_id'];
$user_id = $sensor['user_id'];
$med_count = $sensor['med_count'];

$stmt->close();

// Decrement med count and update magnet
$new_count = max(0, $med_count - 1);
$update = $conn->prepare("UPDATE sensor SET magnet = ?, med_count = ?, updated_at = NOW() WHERE sensor_id = ?");
$update->bind_param("dii", $magnet_value, $new_count, $sensor_id);
$update->execute();
$update->close();

// Log dose taken
$log = $conn->prepare("INSERT INTO dose_history (user_id, med_id, sensor_id, taken) VALUES (?, ?, ?, 1)");
$log->bind_param("iii", $user_id, $med_id, $sensor_id);
$log->execute();
$log->close();

echo json_encode(['status' => 'success', 'message' => 'Sensor updated and dose logged']);
$conn->close();
