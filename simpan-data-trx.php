<?php
// simpan_data.php
include "akses.php";
// Ambil ID dari permintaan Ajax
$currentDay = date('d');
$currentYear = date('ym');
$id_tmp = $currentYear . generate_uuid() . $currentDay;
$id = $_POST['id'];
$spk = trim($_POST['spk']);
$status = 0;

// Simpan data ke dalam database
// Lakukan operasi database sesuai dengan kebutuhan aplikasi Anda

// Contoh koneksi ke database dan penyimpanan data

// Lakukan operasi penyimpanan ke database sesuai dengan tabel dan struktur database Anda
$sql = mysqli_query($connect, "INSERT INTO transaksi_produk_reg (id_tmp, id_spk, id_produk, status_tmp) VALUES ('$id_tmp', '$spk', '$id', '$status')");

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
