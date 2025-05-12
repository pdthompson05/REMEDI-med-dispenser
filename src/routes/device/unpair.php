<?php

require_once __DIR__ . '/../../config/db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$device_id = $_POST['device_id'] ?? $_SESSION['device_id'] ?? null;

if (!is_numeric($device_id)) {
    echo json_encode(['status' => 'error', 'message' => 'No valid device ID']);
    exit;
}

// Unpair only if device belongs to user
$sql = 'UPDATE device SET user_id = NULL WHERE device_id = ? AND user_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $device_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    unset($_SESSION['device_id']); // Clear session link
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Unpairing failed']);
}

$stmt->close();
$conn->close();