<?php
session_start();
session_unset();     // clear $_SESSION
session_destroy();   // destroy session on server

header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'message' => 'Logged out']);
