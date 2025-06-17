<?php
require_once __DIR__ . '/../akses.php';


// Ambil data dari POST
$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? '';

if ($action === 'bukti_revisi') {
    // Jalankan file modal sesuai request
    ob_start();
    include_once __DIR__ . '/../modal/bukti-kirim-revisi.php';
    echo ob_get_clean();
} else {
    http_response_code(400);
    header("Location: ../404.php");
}
