<?php
session_start();
header("Content-Type: application/json");
require_once "db.php";

global $conn;

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($username) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Username and password required"]);
    exit;
}

$stmt = $conn->prepare("SELECT user_id, username, password_hash FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Wrong password"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Wrong username"]);
}

$stmt->close();
$conn->close();
exit;
?>