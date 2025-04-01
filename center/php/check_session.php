<?php
session_start();
header("Content-Type: application/json");

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['authenticated' => false]));
}

echo json_encode([
    'authenticated' => true,
    'user' => [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['email'] //hello
    ]
]);
?>