<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set("session.cookie_httponly", 1);
ini_set("session.cookie_secure", 1);
ini_set("session.use_only_cookies", 1);
ini_set("session.cookie_samesite", "Lax");
session_start();

header("Content-Type: application/json");
require_once __DIR__.'/db.php';

try {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || empty($password)) {
        throw new Exception("Email and password required");
    }

    $stmt = $conn->prepare("
        SELECT user_id, email, password_hash
        FROM user
        WHERE email = ?
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        throw new Exception("Invalid email or password");
    }

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['last_activity'] = time();

    echo json_encode([
        'status' => 'success',
        'message' => 'Login successful',
        'redirect' => '/../../frontend/html/profile.html'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
?>