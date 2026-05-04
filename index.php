<?php
/**
 * Entry point – redirects based on authentication state
 */
require_once 'includes/header.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit();