<?php
require_once "function/encrypt-decrypt-file.php";

// Ambil nama file dan nama customer dari parameter query string
$fileName = isset($_GET['file']) ? $_GET['file'] : '';
$customerName = isset($_GET['customer']) ? $_GET['customer'] : '';

if (empty($fileName) || empty($customerName)) {
    die('Nama file atau nama customer tidak diberikan');
}

// Tentukan path file berdasarkan struktur folder
$encryptedFilePath = 'Customer/' . $customerName . '/NPWP/' . $fileName;

if (!file_exists($encryptedFilePath)) {
    die('File tidak ditemukan: ' . $encryptedFilePath);
}

$decryptedContent = decryptFile($encryptedFilePath, $fileKey);
if ($decryptedContent === false) {
    die('Gagal mendekripsi file');
}

// Menentukan jenis konten berdasarkan ekstensi file
$fileInfo = pathinfo($encryptedFilePath);
$fileExtension = strtolower($fileInfo['extension']);
$contentType = getContentType($fileExtension);

if ($contentType === false) {
    die('Jenis file tidak didukung');
}

// Mengirim header yang sesuai untuk menampilkan file di browser
header('Content-Type: ' . $contentType);
echo $decryptedContent;
?>
