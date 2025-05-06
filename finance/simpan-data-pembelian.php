<?php
// simpan_data.php
require_once "../akses.php";
// Ambil ID dari permintaan Ajax
date_default_timezone_set('Asia/Jakarta');
$currentDay = date('d');
$currentMonth = date('m');
$currentYear = date('Y');
$id_trx_produk = "PR-PB-" . $currentYear . $currentDay . generate_uuid() . $currentMonth;
$id_produk = $_POST['id'];
$id_inv_pembelian = trim($_POST['inv']);
$status = 0;
$created_by = $_SESSION['tiket_nama'];

// Simpan data ke dalam database
// Lakukan operasi database sesuai dengan kebutuhan aplikasi Anda

// Contoh koneksi ke database dan penyimpanan data

// Lakukan operasi penyimpanan ke database sesuai dengan tabel dan struktur database Anda
$sql = "INSERT INTO trx_produk_pembelian (id_trx_produk, id_inv_pembelian, id_produk, status_trx, created_by) VALUES ('$id_trx_produk', '$id_inv_pembelian', '$id_produk', '$status', '$created_by')";

if (mysqli_query($connect, $sql)) {
    echo 'Data berhasil disimpan.';
} else {
    echo 'Terjadi kesalahan saat menyimpan data: ' . mysqli_error($connect);
}



function generate_uuid()
{
    return sprintf(
        '%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}
