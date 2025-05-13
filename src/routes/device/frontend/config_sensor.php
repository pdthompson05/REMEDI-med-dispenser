<?php

session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get device
$sql = 'SELECT device_id FROM device WHERE user_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No paired device']);
    exit;
}

$device = $result->fetch_assoc();
$device_id = $device['device_id'];
$stmt->close();

// Get sensor slots (by slot number)
$sql = 'SELECT slot, med_id, med_count FROM sensor WHERE device_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $device_id);
$stmt->execute();
$sensor_result = $stmt->get_result();

$slots = [];
while ($row = $sensor_result->fetch_assoc()) {
    $slot = $row['slot'];
    $med_id = $row['med_id'];
    $med_count = $row['med_count'];

    // Get med name
    $med_stmt = $conn->prepare('SELECT med_name FROM med WHERE med_id = ?');
    $med_stmt->bind_param('i', $med_id);
    $med_stmt->execute();
    $med_result = $med_stmt->get_result();
    $med_name = $med_result->fetch_assoc()['med_name'] ?? 'Unknown';
    $med_stmt->close();

    $slots[$slot] = [
        'med_id' => $med_id,
        'med_name' => $med_name,
        'med_count' => $med_count,
    ];
}
$stmt->close();

// Get all user medications
$sql = 'SELECT med_id, med_name FROM med WHERE user_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$meds_result = $stmt->get_result();

$meds = [];
while ($row = $meds_result->fetch_assoc()) {
    $meds[] = $row;
}
$stmt->close();

echo json_encode([
    'status' => 'success',
    'device_id' => $device_id,
    'slots' => $slots,
    'meds' => $meds,
]);
