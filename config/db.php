<?php
// Database configuration - MySQLi procedural style
$db_host = "localhost";
$db_user = "root";      // default WAMP username
$db_pass = "";          // default WAMP password
$db_name = "taskmanager_db";

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8");
?>