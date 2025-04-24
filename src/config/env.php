<?php

function loadEnv($path)
{
    if (! file_exists($path)) {
        exit("Error: .env file not found at $path\n");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            putenv(trim($key).'='.trim($value));
        }
    }
}
