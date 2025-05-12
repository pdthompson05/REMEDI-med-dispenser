<?php

require_once __DIR__ . '/../../config/db.php';
header('Content-Type: application/json');
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (! $user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$sql = "SELECT device_id, connected, temperature FROM device WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $device = $result->fetch_assoc();

    if ($device) {
        echo json_encode(['status' => 'success', 'device' => $device]);
    } else {
        echo json_encode(['status' => 'success', 'device' => null]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
