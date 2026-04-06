<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "taskmanager_db";   // or your existing DB name

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");
?>