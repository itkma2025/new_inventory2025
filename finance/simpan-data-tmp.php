<?php
// simpan_data.php
require_once "../akses.php";
// Ambil ID dari permintaan Ajax
$year = date('y');
$day = date('d');
$month = date('m');
$uuid = generate_uuid();
$id_inv = $_POST['inv'];
$id_tmp = "TRX-" . $year . "" . $month . "" . $uuid . "" . $day ;
$id_produk = $_POST['produk'];
$nama_produk = $_POST['namaProduk'];
$harga = $_POST['hargaProduk'];
$status = 0;


// Simpan data ke dalam database
// Lakukan operasi database sesuai dengan kebutuhan aplikasi Anda

// Contoh koneksi ke database dan penyimpanan data

// Lakukan operasi penyimpanan ke database sesuai dengan tabel dan struktur database Anda
$sql = mysqli_query($connect, "INSERT INTO tmp_produk_komplain (id_tmp, id_inv, id_produk, nama_produk, harga, status_tmp) VALUES ('$id_tmp', '$id_inv', '$id_produk', '$nama_produk', '$harga', '$status')");

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
