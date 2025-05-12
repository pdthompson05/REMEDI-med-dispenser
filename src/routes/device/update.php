<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

$device_id = $_POST['device_id'] ?? null;
$temp = $_POST['temp'] ?? null;

if (!$device_id || !is_numeric($device_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing or invalid device ID']);
    exit;
}

// Collect slot data
$sensor_data = [];
for ($i = 0; $i < 4; $i++) {
    if (isset($_POST['slot' . $i])) {
        $sensor_data[] = [
            'slot' => $i,
            'taken' => intval($_POST['slot' . $i])
        ];
    }
}

// Optionally store this data to a DB (e.g., pill log or sensor history)
$stmt = $conn->prepare("INSERT INTO pill_log (device_id, slot, taken, timestamp) VALUES (?, ?, ?, NOW())");
foreach ($sensor_data as $slot) {
    $stmt->bind_param("iii", $device_id, $slot['slot'], $slot['taken']);
    $stmt->execute();
}
$stmt->close();

// Get pill quantities from `sensor` table
$stmt = $conn->prepare("SELECT sensor_id, med_count FROM sensor WHERE device_id = ?");
$stmt->bind_param('i', $device_id);
$stmt->execute();
$res = $stmt->get_result();

$quantities = [];
while ($row = $res->fetch_assoc()) {
    $quantities[] = $row;
}



echo json_encode([
    'status' => 'success',
    'message' => 'Data received',
    'device_id' => $device_id,
    'temp' => $temp,
    'slots' => $sensor_data,
    'pill_quantities' => $quantities
]);
?>