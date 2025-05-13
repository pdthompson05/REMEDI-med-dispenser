<?php

session_start();
require_once __DIR__ . '/../../../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$device_id = $_SESSION['device_id'] ?? ($_POST['device_id'] ?? null);

if (!is_numeric($device_id)) {
    echo json_encode(['status' => 'error', 'message' => 'No paired device']);
    exit;
}

// Prepare insert or update statement
$insert = $conn->prepare('INSERT INTO sensor (device_id, slot, med_id, med_count)
                          VALUES (?, ?, ?, ?)
                          ON DUPLICATE KEY UPDATE
                              med_id = VALUES(med_id),
                              med_count = VALUES(med_count),
                              updated_at = CURRENT_TIMESTAMP');

$checkMed = $conn->prepare('SELECT 1 FROM med WHERE med_id = ? AND user_id = ?');

$valid = false;

for ($i = 1; $i <= 4; $i++) {
    $slot = $i;
    $med_id = $_POST["slot_{$i}_med_id"] ?? null;
    $count = $_POST["slot_{$i}_count"] ?? null;

    if ($med_id && $count !== null && is_numeric($count)) {
        // Validate that med belongs to this user
        $checkMed->bind_param('ii', $med_id, $user_id);
        $checkMed->execute();
        $checkMed->store_result();

        if ($checkMed->num_rows === 0) {
            continue;
        }

        $insert->bind_param('iiii', $device_id, $slot, $med_id, $count);
        $insert->execute();
        $valid = true;
    }
}

$insert->close();
$checkMed->close();

if ($valid) {
    echo json_encode(['status' => 'success', 'message' => 'Sensor slots configured']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
}

$conn->close();