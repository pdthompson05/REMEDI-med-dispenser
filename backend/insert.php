<?php
$host = "localhost";
$db = "your_db";
$user = "your_user";
$pass = "your_password";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$device_id = $_POST['device_id'];
$temp = $_POST['temp'];
$humidity = $_POST['humidity'];
$timestamp = date("Y-m-d H:i:s"); // Server time

$sql = "INSERT INTO sensor_data (device_id, temperature, humidity, timestamp) 
        VALUES ('$device_id', '$temp', '$humidity', '$timestamp')";

if ($conn->query($sql) === TRUE) {
    echo "Success";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>