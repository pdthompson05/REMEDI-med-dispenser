<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");
require_once "db.php";
global $conn;

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT u.email, p.first_name, p.last_name, p.date_of_birth, p.caretaker_name, p.caretaker_email, p.profile_picture 
        FROM user u 
        JOIN user_profile p ON u.user_id = p.user_id 
        WHERE u.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Add a default profile picture if none exists
    $row['profile_picture'] = $row['profile_picture'] 
        ? "https://section-three.it313communityprojects.website/center/php/" . $row['profile_picture'] 
        : "https://via.placeholder.com/200";

    echo json_encode(["status" => "success", "data" => $row]);
} else {
    echo json_encode(["status" => "error", "message" => "Profile not found"]);
}

$stmt->close();
$conn->close();
?>