<?php
require_once 'includes/header.php';
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit();