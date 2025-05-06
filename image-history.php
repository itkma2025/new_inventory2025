<?php
require_once __DIR__ . '/akses.php'; // Otentikasi jika ada
require_once __DIR__ . '/function/function-enkripsi.php'; // Otentikasi jika ada
$default_image = __DIR__ . '/assets/img/no_img.jpg'; // Gambar default saat tidak ditemukan

// Validasi input
if (!isset($_GET['file'])) {
    http_response_code(400);
    exit('Nama file tidak ditentukan.');
}

$encrypted = $_GET['file'];
$decrypted = decrypt($encrypted, $key_global);
$nama_driver = htmlspecialchars(urldecode($_GET['driver']));
$filename = $decrypted;


$base_dir = "";
if ($filename && file_exists("gambar/bukti1/" . $filename)) {
    echo $base_dir = "gambar/bukti1/" . $filename;
} else if($filename && file_exists("gambar/bukti_kirim/" . $nama_driver . "/" . $filename)){
    $base_dir = "gambar/bukti_kirim/" . $nama_driver . "/" . $filename;
} else if($filename && file_exists("gambar/bukti_kirim/ecat/" . $nama_driver . "/" . $filename)){
    $base_dir = "gambar/bukti_kirim/ecat/" . $nama_driver . "/" . $filename;
} else if($filename && file_exists("gambar/bukti_kirim/pl/" . $nama_driver . "/" . $filename)){
    $base_dir = "gambar/bukti_kirim/pl/" . $nama_driver . "/" . $filename;
} else {
    $base_dir = $default_image;
}

$filepath = $base_dir;

// Gunakan gambar default jika tidak ditemukan
if (!file_exists($filepath)) {
    $filepath = $default_image;
}

// Bersihkan output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Tentukan tipe MIME
$mime = mime_content_type($filepath);
header("Content-Type: $mime");

// Header no-cache (opsional)
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Tampilkan file
readfile($filepath);
exit;


