<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'gamers_social_db';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('DB connection error: ' . $e->getMessage());
    http_response_code(500);
    die('Service unavailable. Please try again later.');
}