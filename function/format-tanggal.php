<?php
function formatTanggalIndonesia($tanggal, $tampilkanHari = true) {
    // Array untuk nama hari dalam bahasa Indonesia
    $hari = [
        "Sunday" => "Minggu",
        "Monday" => "Senin",
        "Tuesday" => "Selasa",
        "Wednesday" => "Rabu",
        "Thursday" => "Kamis",
        "Friday" => "Jumat",
        "Saturday" => "Sabtu"
    ];

    // Array untuk nama bulan dalam bahasa Indonesia
    $bulan = [
        "01" => "Januari",
        "02" => "Februari",
        "03" => "Maret",
        "04" => "April",
        "05" => "Mei",
        "06" => "Juni",
        "07" => "Juli",
        "08" => "Agustus",
        "09" => "September",
        "10" => "Oktober",
        "11" => "November",
        "12" => "Desember"
    ];

    // Membuat objek DateTime dari tanggal yang diberikan
    $date = new DateTime($tanggal);

    // Mendapatkan nama hari, tanggal, bulan, dan tahun
    $namaHari = $hari[$date->format("l")];
    $tanggal = $date->format("d");
    $namaBulan = $bulan[$date->format("m")];
    $tahun = $date->format("Y");

    // Mendapatkan waktu
    $jam = $date->format("H");
    $menit = $date->format("i");
    $detik = $date->format("s");

    // Mengembalikan format tanggal dan waktu dalam bahasa Indonesia
    if ($tampilkanHari) {
        if ($jam == "00" && $menit == "00" && $detik == "00") {
            return "$namaHari, $tanggal $namaBulan $tahun";
        } else {
            return "$namaHari, $tanggal $namaBulan $tahun $jam:$menit:$detik";
        }
    } else {
        if ($jam == "00" && $menit == "00" && $detik == "00") {
            return "$tanggal $namaBulan $tahun";
        } else {
            return "$tanggal $namaBulan $tahun $jam:$menit:$detik";
        }
    }
}

// Contoh penggunaan fungsi
// $date_now = date('Y-m-d H:i:s');
// echo formatTanggalIndonesia($date_now, false); // Menggunakan false untuk tidak menampilkan hari
?>
