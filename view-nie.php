<?php  
session_start();
require_once __DIR__ . '/function/encrypt-decrypt-file.php';

// Pastikan file tersedia
$encryptedFileName = __DIR__ . "/" . $_SESSION['data_file_nie'];
$decryptedContent = decryptFile($encryptedFileName, $fileKey);

if ($decryptedContent === false) {
    die('Gagal mendekripsi file');
}

// Tentukan jenis konten
$fileInfo = pathinfo($encryptedFileName);
$fileExtension = strtolower($fileInfo['extension']);
$contentType = getContentType($fileExtension);

if ($contentType === false) {
    die('Jenis file tidak didukung');
}

// Nama file yang digunakan saat diunduh
$downloadFileName = "NIE_" . $_SESSION['data_nama_kategori'] . "." . $fileExtension;

// Header untuk menampilkan file di browser
header("Content-Type: $contentType");
header("Content-Disposition: inline; filename=\"$downloadFileName\""); // Tampilkan tapi bisa didownload
header("Content-Length: " . strlen($decryptedContent));
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Kirimkan isi file
echo $decryptedContent;
exit;
?>
