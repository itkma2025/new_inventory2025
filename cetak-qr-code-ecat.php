<?php
require __DIR__ . "/assets/vendor/autoload.php";

use Zxing\QrReader;

include "koneksi.php";
include "function/function-enkripsi.php";
include 'assets/Qrcode/qrlib.php';
date_default_timezone_set('Asia/Jakarta');
$today = date('d/m/Y, H:i:s');
$key = "KM@2024?";

// Mendapatkan ID produk
$id = decrypt($_GET['id'], $key_global);
$sql = "SELECT pr.nama_produk, pr.kode_produk, qr.id_produk_qr, qr.url_qr, qr.qr_img
        FROM tb_produk_ecat AS pr
        LEFT JOIN qr_link_ecat qr ON (pr.id_produk_ecat = qr.id_produk_qr)
        WHERE qr.id_produk_qr = '$id'";
$query = mysqli_query($connect, $sql);
$cek_data = mysqli_num_rows($query);
$data = mysqli_fetch_array($query); 

// Kode untuk menampilkan total row qr link
$sql_qr = $connect->query("SELECT id_link FROM qr_link_ecat");
$total_data = mysqli_num_rows($sql_qr);

// Inisialisasi variabel
$size = 300;
$correctionLevel = 'M';
$logoPath = 'assets/img/KMA.png';
$server_saat_ini = $_SERVER['SERVER_NAME'];
$outputDir = "gambar/QRcode-ecat/"; // Pastikan folder ini ada dan dapat diakses

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true); // Buat folder jika belum ada
}

if ($cek_data > 0) {
    // Jika data QR sudah ada
    $id_produk_qr = $data['id_produk_qr'];
    $img = $data['qr_img'];
    $url = $data['url_qr'];
    $kode_produk = $data['kode_produk']; // Mendapatkan kode produk

    // Ganti tanda '/' dengan '_' pada kode produk
    $kode_produk_sanitized = str_replace('/', '_', $kode_produk);

    // Ganti spasi dan karakter tidak diizinkan pada nama produk menjadi '_'
    $nama_produk_sanitized = preg_replace('/[^\w]+/', '_', $data['nama_produk']);

    // Mendapatkan komponen URL
    $parsedUrl = parse_url($url);
    if ($parsedUrl && isset($parsedUrl['scheme'])) {
        $domain = $parsedUrl['scheme'] . '://';
        if (isset($parsedUrl['host'])) {
            $domain .= $parsedUrl['host'];
        }
    } else {
        $domain = '';
    }

    // Jika domain berbeda, hapus QR lama dan buat QR baru
    if ($server_saat_ini != $domain) {
        unlink($outputDir . $img);

        // Gunakan nama file lama tanpa menambah urutan baru, tambahkan kode produk
        $new_nama_qr_img = $kode_produk_sanitized . '_' . $nama_produk_sanitized . ".png";

        $outputFile = $outputDir . $new_nama_qr_img;
        $url_qr = $server_saat_ini . "/detail-produk-ecat.php?id=" . encrypt($id_produk_qr, $key);
        QRcode::png($url_qr, $outputFile, $correctionLevel, $size, 2);

        // Proses menambahkan logo ke QR
        $QR = imagecreatefrompng($outputFile);
        $logo = imagecreatefrompng($logoPath);
        $QR_width = imagesx($QR);
        $QR_height = imagesy($QR);
        $logo_width = imagesx($logo);
        $logo_height = imagesy($logo);
        $centerX = ($QR_width - $logo_width) / 2;
        $centerY = ($QR_height - $logo_height) / 2;
        imagecopy($QR, $logo, $centerX, $centerY, 0, 0, $logo_width, $logo_height);
        imagepng($QR, $outputFile);

        imagedestroy($QR);
        imagedestroy($logo);

        // Update database dengan URL baru
        $sql_update_qr = $connect->query("UPDATE qr_link_ecat SET url_qr = '$url_qr', qr_img = '$new_nama_qr_img', updated_date = '$today' WHERE id_produk_qr = '$id_produk_qr'");

        if ($sql_update_qr) {
            header("Location:tampil-qr-code-ecat.php?id=" . encrypt($id_produk_qr, $key));
        }
    } else {
        header("Location:tampil-qr-code-ecat.php?id=" . encrypt($id_produk_qr, $key));
    }
} else {
    // Jika data tidak ditemukan, buat QR baru
    $sql_produk = $connect->query("SELECT id_produk_ecat, nama_produk, kode_produk FROM tb_produk_ecat WHERE id_produk_ecat = '$id'");
    $data_produk = mysqli_fetch_array($sql_produk);

    $id_produk = $data_produk['id_produk_ecat'];
    $encrypt_id_produk = encrypt($id_produk, $key);
    $nama_produk = $data_produk['nama_produk'];
    $kode_produk = $data_produk['kode_produk'];

    // Ganti tanda '/' dengan '_' pada kode produk
    $kode_produk_sanitized = str_replace('/', '_', $kode_produk);

    // Ganti spasi dan karakter tidak diizinkan pada nama produk menjadi '_'
    $nama_produk_sanitized = preg_replace('/[^\w]+/', '_', $nama_produk);

    $url_qr = $server_saat_ini . "/detail-produk-ecat.php?id=" . $encrypt_id_produk;

    // Gunakan format nama file dengan menambahkan kode produk di depan nama produk
    $new_nama_qr_img = $kode_produk_sanitized . '_' . $nama_produk_sanitized . ".png";
    $outputFile = $outputDir . $new_nama_qr_img;

    QRcode::png($url_qr, $outputFile, $correctionLevel, $size, 2);

    // Proses menambahkan logo ke QR
    $QR = imagecreatefrompng($outputFile);
    $logo = imagecreatefrompng($logoPath);
    $QR_width = imagesx($QR);
    $QR_height = imagesy($QR);
    $logo_width = imagesx($logo);
    $logo_height = imagesy($logo);
    $centerX = ($QR_width - $logo_width) / 2;
    $centerY = ($QR_height - $logo_height) / 2;
    imagecopy($QR, $logo, $centerX, $centerY, 0, 0, $logo_width, $logo_height);
    imagepng($QR, $outputFile);

    imagedestroy($QR);
    imagedestroy($logo);

    // Simpan ke database
    $simpan_qr = $connect->query("INSERT INTO qr_link_ecat (id_produk_qr, url_qr, qr_img) VALUES ('$id_produk', '$url_qr', '$new_nama_qr_img')");

    if ($simpan_qr) {
        header("Location:tampil-qr-code-ecat.php?id=" . encrypt($id_produk, $key));
    }
}
