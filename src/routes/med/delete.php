<?php

session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__.'/../../config/db.php';
global $conn;

if (! isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$med_id = isset($_POST['med_id']) ? (int) $_POST['med_id'] : 0;

if ($med_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid med ID',
        'debug' => [
            'raw_post' => $_POST,
            'received_med_id' => $_POST['med_id'] ?? 'NOT SET',
        ],
    ]);
    exit;
}

$sql = 'DELETE FROM med WHERE med_id = ? AND user_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $med_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Medication deleted']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete']);
}

$stmt->close();
$conn->close();
