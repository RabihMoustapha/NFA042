<?php
/**
 * Database configuration and connection (MySQLi, object-oriented)
 */
$db_host = 'localhost';
$db_user = 'root';        // ← change in production
$db_pass = '';            // ← change in production
$db_name = 'taskmanager_db';

// Enable exceptions for all mysqli errors
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('Database connection error: ' . $e->getMessage());
    http_response_code(500);
    die('Service unavailable. Please try again later.');
}