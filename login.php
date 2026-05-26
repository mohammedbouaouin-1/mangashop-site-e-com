<?php

require_once 'includes/config.php';


if (isset($_SESSION['user'])) {
    header('Location: account.php');
    exit;
}

require_once 'controllers/login.php';
