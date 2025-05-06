<?php
// simpan_data.php
include "akses.php";
// Ambil ID dari permintaan Ajax
$currentMonth = date('m');
$currentYear = date('Y');
$id_sph ="TRX-". $currentYear . generate_uuid() . $currentMonth;
$id = $_POST['id'];
$sph = trim($_POST['sph']);
$nama = $_POST['nama'];
$harga = $_POST['harga'];
$status = 0;

// Simpan data ke dalam database
// Lakukan operasi database sesuai dengan kebutuhan aplikasi Anda

// Contoh koneksi ke database dan penyimpanan data

// Lakukan operasi penyimpanan ke database sesuai dengan tabel dan struktur database Anda
$sql = $connect->query("INSERT INTO transaksi_produk_sph (id_transaksi, id_sph, id_produk, nama_produk_sph, harga, status_trx) 
                    VALUES ('$id_sph', '$sph', '$id', '$nama', '$harga', '$status')");

if ($sql) {
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
