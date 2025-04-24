<?php

require_once __DIR__.'/env.php'; // loadEnv
loadEnv(__DIR__.'/../../.env'); // loadEnv for .env file

$DB_HOST = getenv('DB_HOST');
$DB_USER = getenv('DB_USER');
$DB_PASS = getenv('DB_PASS');
$DB_NAME = getenv('DB_NAME');
$MAIL = getenv('MAIL');

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    exit('Database connection failed: '.$conn->connect_error);
}
