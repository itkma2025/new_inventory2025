<?php
function hitungSelisihHari($tglAwal, $tglAkhir) {
    $timestampAwal = strtotime($tglAwal);
    $timestampAkhir = strtotime($tglAkhir);
    
    // Hitung selisih timestamp
    $selisihTimestamp = $timestampAkhir - $timestampAwal;
    
    // Konversi selisih timestamp ke dalam hari
    $selisihHari = floor($selisihTimestamp / (60 * 60 * 24));
    
    return $selisihHari;
}

// Contoh penggunaan fungsi
// $tglAwal = "2024-04-29";
// $tglAkhir = "2024-05-05";
// $selisihHari = hitungSelisihHari($tglAwal, $tglAkhir);
// echo "Selisih hari antara $tglAwal dan $tglAkhir adalah: $selisihHari hari";
?>
