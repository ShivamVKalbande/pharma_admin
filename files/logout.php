<?php
require_once 'config.php';
require_once 'SecureSession.php';

SecureSession::init();

// Log logout
if (SecureSession::isLoggedIn()) {
    $username = SecureSession::getUsername();
    error_log("User logged out: {$username} from {$_SERVER['REMOTE_ADDR']}");
}

SecureSession::destroy();

header('Location: login.php?error=logout');
exit();
?>
