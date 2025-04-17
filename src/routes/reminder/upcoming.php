<?php
session_start();
header("Content-Type: application/json");
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id'])) {
  echo json_encode(["status" => "error", "message" => "Not logged in"]);
  exit;
}

$user_id = $_SESSION['user_id'];

$now = new DateTime();
$upcoming = clone $now;
$upcoming->modify('+10 minutes');

$now_str = $now->format('Y-m-d H:i:s');
$upcoming_str = $upcoming->format('Y-m-d H:i:s');

$sql = "
  SELECT m.med_name, c.event_datetime
  FROM calendar_events c
  JOIN med m ON c.med_id = m.med_id
  WHERE c.user_id = ? AND c.event_datetime BETWEEN ? AND ?
  ORDER BY c.event_datetime ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $now_str, $upcoming_str);
$stmt->execute();
$result = $stmt->get_result();

$reminders = [];
while ($row = $result->fetch_assoc()) {
  $reminders[] = $row;
}

echo json_encode(["status" => "success", "data" => $reminders]);

$stmt->close();
$conn->close();