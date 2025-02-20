<?php
session_start();
header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set("log_errors", 1);
ini_set("error_log", "/var/www/it313communityprojects.website/section-three/error.log");

error_log("TEST: PHP is running and writing logs.");
echo json_encode(["status" => "debug", "message" => "PHP is running!"]);
exit;
?>
