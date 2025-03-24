<?php
session_start();
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");
require_once "db.php";
global $conn;

if (!isset($_SESSION['user_id'])) {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$date_of_birth = (!empty($_POST['date_of_birth'])) ? trim($_POST['date_of_birth']) : NULL;
$caretaker_name = (!empty($_POST['caretaker_name'])) ? trim($_POST['caretaker_name']) : NULL;
$caretaker_email = (!empty($_POST['caretaker_email'])) ? trim($_POST['caretaker_email']) : NULL;

if (empty($first_name) || empty($last_name) || empty($email)) {
    echo json_encode(["status" => "error", "message" => "Required fields are missing"]);
    exit;
}

$sql_check_email = "SELECT user_id FROM user WHERE email = ? AND user_id != ?";
$stmt_check = $conn->prepare($sql_check_email);
$stmt_check->bind_param("si", $email, $user_id);
$stmt_check->execute();
$stmt_check->store_result();
if ($stmt_check->num_rows > 0) {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Email is already in use"]);
    exit;
}
$stmt_check->close();

$sql_update_user = "UPDATE user SET email = ? WHERE user_id = ?";
$stmt_user = $conn->prepare($sql_update_user);
$stmt_user->bind_param("si", $email, $user_id);
$stmt_user->execute();
$stmt_user->close();

$sql_update_profile = "UPDATE user_profile SET first_name = ?, last_name = ?, date_of_birth = ?, caretaker_name = ?, caretaker_email = ? WHERE user_id = ?";
$stmt_profile = $conn->prepare($sql_update_profile);
$stmt_profile->bind_param("sssssi", $first_name, $last_name, $date_of_birth, $caretaker_name, $caretaker_email, $user_id);

if ($stmt_profile->execute()) {
    ob_clean();
    echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
} else {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "SQL Error: " . $stmt_profile->error]);
}

$stmt_profile->close();
$conn->close();
?>