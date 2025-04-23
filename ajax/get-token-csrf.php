<?php
session_start();
date_default_timezone_set('Asia/Jakarta');  // Set timezone
require_once '../function/CSRFToken.php';

$csrf = new CSRFToken();
$csrf_token = $csrf->generateToken();
$expired_time = time() + 1200;
$expired_token = date('Y-m-d H:i:s', $expired_time);

$_SESSION['token_csrf'] = $csrf_token;
$_SESSION['token_exp'] = $expired_token;

echo json_encode([
    'token_csrf' => $csrf_token,
    'token_exp' => $expired_token
]);
