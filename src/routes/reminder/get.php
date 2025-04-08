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
        r.start_date,
        r.end_date,
        rt.reminder_time
    FROM reminder r
    JOIN med m ON r.med_id = m.med_id
    LEFT JOIN reminder_times rt ON r.reminder_id = rt.reminder_id
    WHERE r.user_id = ?
    ORDER BY r.start_date ASC, rt.reminder_time ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$reminders = [];
while ($row = $result->fetch_assoc()) {
    $rid = $row['reminder_id'];
    if (!isset($reminders[$rid])) {
        $reminders[$rid] = [
            "reminder_id" => $row["reminder_id"],
            "med_id" => $row["med_id"],
            "med_name" => $row["med_name"],
            "dosage" => $row["dosage"],
            "reminder_type" => $row["reminder_type"],
            "interval_hours" => $row["interval_hours"],
            "start_date" => $row["start_date"],
            "end_date" => $row["end_date"],
            "times" => []
        ];
    }

    if ($row["reminder_time"]) {
        $reminders[$rid]["times"][] = $row["reminder_time"];
    }
}

echo json_encode(["status" => "success", "data" => array_values($reminders)]);
$stmt->close();
$conn->close();
?>