<?php
session_start();
header("Content-Type: application/json");
require_once "db.php";
global $conn;

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "email and password required"]);
    exit;
}

$sql = "SELECT user_id, email, password_hash FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);
if(!$stmt){
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit;
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true); // Prevent session fixation
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid email or password."]);
    }
}

$stmt->close();
$conn->close();
exit;
?>