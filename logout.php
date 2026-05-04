<?php
require_once 'includes/header.php';
// session_start() is handled by header.php

$_SESSION = [];                   // clear session array
session_destroy();
header('Location: login.php');
exit();