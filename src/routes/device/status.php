<?php

session_start();
header('Content-Type: application/json');
require_once __DIR__.'/../../config/db.php';

if (! isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = 'SELECT device_id, connected, updated_at FROM device WHERE user_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['status' => 'success', 'data' => $row]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No device paired']);
}

$stmt->close();
$conn->close();
