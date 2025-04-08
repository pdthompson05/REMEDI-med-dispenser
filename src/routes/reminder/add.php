<?php
session_start();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . '/../../config/db.php';
global $conn;

// Check login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Sanitize inputs
$med_id = isset($_POST['med_id']) ? (int) $_POST['med_id'] : 0;
$dosage = isset($_POST['dosage']) ? trim($_POST['dosage']) : null;
$type = $_POST['reminder_type'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$interval_hours = isset($_POST['interval_hours']) ? (int) $_POST['interval_hours'] : null;
$time_inputs = isset($_POST['times']) ? $_POST['times'] : [];

if ($med_id <= 0 || empty($type) || empty($start_date) || empty($end_date)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// Validate medication ownership
$stmt = $conn->prepare("SELECT med_id FROM med WHERE med_id = ? AND user_id = ?");
$stmt->bind_param("ii", $med_id, $user_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Medication not found for user"]);
    exit;
}
$stmt->close();

// Insert reminder(s)
if ($type === "specific-time" && is_array($time_inputs)) {
    $query = "INSERT INTO reminder (user_id, med_id, dosage, reminder_type, reminder_time, reminder_date, start_date, end_date)
              VALUES (?, ?, ?, 'specific', ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    foreach ($time_inputs as $time) {
        // You can optionally generate reminder_date = start_date for now
        $stmt->bind_param("iisssss", $user_id, $med_id, $dosage, $time, $start_date, $start_date, $end_date);
        $stmt->execute();
    }

    $stmt->close();
    echo json_encode(["status" => "success", "message" => "Specific time reminders created"]);
    exit;

} elseif ($type === "interval" && $interval_hours > 0) {
    $query = "INSERT INTO reminder (user_id, med_id, dosage, reminder_type, interval_hours, reminder_date, start_date, end_date)
              VALUES (?, ?, ?, 'interval', ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    $stmt->bind_param("iisssss", $user_id, $med_id, $dosage, $interval_hours, $start_date, $start_date, $end_date);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["status" => "success", "message" => "Interval reminder created"]);
    exit;
} else {
    echo json_encode(["status" => "error", "message" => "Invalid reminder type or data"]);
    exit;
}
?>