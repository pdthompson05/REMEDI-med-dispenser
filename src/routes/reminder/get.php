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

$sql = "
  SELECT 
    r.reminder_id,
    r.med_id,
    m.med_name,
    r.reminder_type,
    r.interval_hours,
    r.reminder_time,
    r.reminder_date,
    r.start_date,
    r.end_date
  FROM reminder r
  JOIN med m ON r.med_id = m.med_id
  WHERE r.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];

while ($row = $result->fetch_assoc()) {
  $type = $row['reminder_type'];
  $med = $row['med_name'];

  if ($type === 'specific-time') {
    $start = new DateTime($row['start_date']);
    $end = new DateTime($row['end_date']);
    $end->modify('+1 day');

    while ($start < $end) {
      $datetimeStr = $start->format('Y-m-d') . ' ' . $row['reminder_time'];
      $events[] = [
        "event_datetime" => $datetimeStr,
        "med_name" => $med
      ];
      $start->modify('+1 day');
    }
  }

  else if ($type === 'interval') {
    $interval = (int)$row['interval_hours'];
    if ($interval > 0) {
      $start = new DateTime($row['start_date'] . ' 00:00:00');
      $end = new DateTime($row['end_date'] . ' 23:59:59');

      while ($start <= $end) {
        $events[] = [
          "event_datetime" => $start->format('Y-m-d H:i:s'),
          "med_name" => $med
        ];
        $start->modify("+{$interval} hours");
      }
    }
  }
}

echo json_encode(["status" => "success", "data" => $events]);
$stmt->close();
$conn->close();
?>