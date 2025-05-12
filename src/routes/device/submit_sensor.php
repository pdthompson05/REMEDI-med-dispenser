<?php

session_start();
require_once __DIR__.'/../../config/db.php';
header('Content-Type: application/json');

if (! isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$device_id = $_SESSION['device_id'] ?? null;

if (! is_numeric($device_id)) {
    echo json_encode(['status' => 'error', 'message' => 'No paired device']);
    exit;
}

$conn->prepare('DELETE FROM sensor WHERE device_id = ?')->bind_param('i', $device_id)->execute();

$insert = $conn->prepare('INSERT INTO sensor (device_id, med_id, med_count) VALUES (?, ?, ?)');

$valid = false;
for ($i = 1; $i <= 4; $i++) {
    $med_id = $_POST["slot_{$i}_med_id"] ?? null;
    $count = $_POST["slot_{$i}_count"] ?? null;

    if ($med_id && $count !== null && is_numeric($count)) {
        $insert->bind_param('iii', $device_id, $med_id, $count);
        $insert->execute();
        $valid = true;
    }
}

$insert->close();

if ($valid) {
    echo json_encode(['status' => 'success', 'message' => 'Sensor slots configured']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
}
