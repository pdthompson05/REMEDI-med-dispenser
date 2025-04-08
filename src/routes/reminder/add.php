<?php
session_start();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . '/../../config/db.php';
global $conn;

if (!isset($_SESSION['user_id'])) {
  echo json_encode(["status" => "error", "message" => "Not logged in"]);
  exit;
}

$user_id = $_SESSION['user_id'];
$med_id = $_POST['med_id'] ?? null;
$dosage = $_POST['dosage'] ?? null;
$reminder_type = $_POST['reminder_type'] ?? null;
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;

if (!$med_id || !$reminder_type || !$start_date || !$end_date) {
  echo json_encode(["status" => "error", "message" => "Missing required fields"]);
  exit;
}

// Validate med_id
$med_check = $conn->prepare("SELECT med_id FROM med WHERE user_id = ? AND med_id = ?");
$med_check->bind_param("ii", $user_id, $med_id);
$med_check->execute();
$med_check->store_result();
if ($med_check->num_rows === 0) {
  echo json_encode(["status" => "error", "message" => "Invalid med ID"]);
  exit;
}
$med_check->close();

// Insert into reminder table
$interval_hours = $_POST['interval_hours'] ?? null;
$reminder_time = null;
$reminder_date = null;

$sql = "INSERT INTO reminder (user_id, med_id, dosage, reminder_type, interval_hours, start_date, end_date, reminder_time, reminder_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iississss", $user_id, $med_id, $dosage, $reminder_type, $interval_hours, $start_date, $end_date, $reminder_time, $reminder_date);

if (!$stmt->execute()) {
  echo json_encode(["status" => "error", "message" => "Failed to save reminder"]);
  exit;
}

$reminder_id = $stmt->insert_id;
$stmt->close();

// Insert events into calendar_events
if ($reminder_type === "specific" && isset($_POST['times'])) {
  $times = $_POST['times'];
  $start = new DateTime($start_date);
  $end = new DateTime($end_date);

  while ($start <= $end) {
    foreach ($times as $time) {
      $datetime = $start->format('Y-m-d') . ' ' . $time . ':00';
      $insert_event = $conn->prepare("INSERT INTO calendar_events (user_id, med_id, event_datetime) VALUES (?, ?, ?)");
      $insert_event->bind_param("iis", $user_id, $med_id, $datetime);
      $insert_event->execute();
      $insert_event->close();
    }
    $start->modify('+1 day');
  }
} elseif ($reminder_type === "interval" && is_numeric($interval_hours)) {
  $start = new DateTime($start_date . " 00:00:00");
  $end = new DateTime($end_date . " 23:59:59");

  while ($start <= $end) {
    $insert_event = $conn->prepare("INSERT INTO calendar_events (user_id, med_id, event_datetime) VALUES (?, ?, ?)");
    $dt = $start->format('Y-m-d H:i:s');
    $insert_event->bind_param("iis", $user_id, $med_id, $dt);
    $insert_event->execute();
    $insert_event->close();
    $start->modify("+{$interval_hours} hours");
  }
} else {
  echo json_encode(["status" => "error", "message" => "Invalid reminder type or data"]);
  exit;
}

echo json_encode(["status" => "success", "message" => "Reminder and events created"]);

$conn->close();