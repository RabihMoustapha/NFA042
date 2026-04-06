<?php
// Redirect to dashboard if logged in, otherwise to login
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>