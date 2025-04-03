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
        r.dosage,
        r.reminder_type,
        r.interval_hours,
        r.reminder_time,
        r.reminder_date,
        r.start_date,
        r.end_date
    FROM reminder r
    JOIN med m ON r.med_id = m.med_id
    WHERE r.user_id = ?
    ORDER BY r.reminder_date ASC, r.reminder_time ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$reminders = [];
while ($row = $result->fetch_assoc()) {
    $reminders[] = $row;
}

echo json_encode(["status" => "success", "data" => $reminders]);

$stmt->close();
$conn->close();
?>