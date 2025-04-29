<?php
$host = "localhost";
$db = "student3-it313communityprojects.website";
$user = "esp32_01";
$pass = "checkout";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$device_id = $_POST['device_id'];
$temp = $_POST['temp'];
$magnet = $_POST['magnet'];
$timestamp = date("Y-m-d H:i:s"); // Server time

$sql = "INSERT INTO sensor_data (device_id, temperature, magnet, timestamp) 
        VALUES ('$device_id', '$temp', '$magnet', '$timestamp')";

if ($conn->query($sql) === TRUE) {
    echo "Success";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>