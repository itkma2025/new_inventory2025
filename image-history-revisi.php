<?php
require_once __DIR__ . '/akses.php'; 
require_once __DIR__ . '/function/function-enkripsi.php';

$default_image = __DIR__ . '/assets/img/no_img.jpg';

// Validasi input
if (!isset($_GET['file'])) {
    http_response_code(400);
    exit('Nama file tidak ditentukan.');
}

$encrypted = $_GET['file'];
$decrypted = decrypt($encrypted, $key_global);
$filename = $decrypted;
$nama_driver = htmlspecialchars(urldecode($_GET['driver'] ?? ''));

// Daftar folder yang akan dicek, urutkan berdasarkan prioritas
$folders = [
    "gambar-revisi/bukti_kirim/" . $nama_driver,
    "gambar-revisi/bukti_kirim/ecat/" . $nama_driver,
    "gambar-revisi/bukti_kirim/pl/" . $nama_driver,
    "gambar-revisi/bukti_kirim/ekspedisi", // fallback umum untuk ekspedisi
    "gambar-revisi/bukti1"                 // fallback umum lama
];

// Coba cari file di semua folder di atas
$filepath = '';
foreach ($folders as $folder) {
    $try_path = __DIR__ . '/' . $folder . '/' . $filename;
    if (file_exists($try_path)) {
        $filepath = $try_path;
        break;
    }
}

// Jika tidak ditemukan, gunakan default
if (!$filepath || !file_exists($filepath)) {
    $filepath = $default_image;
}

// Bersihkan output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Tentukan MIME type
$mime = mime_content_type($filepath);
header("Content-Type: $mime");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Kirimkan gambar
readfile($filepath);
exit;
