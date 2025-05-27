<?php  
// Fungsi yang menerima string dan posisi '/'
function tambahRevisi($no_inv, $pecahkan, $revisi = 'Rev1') {
    if ($pecahkan !== false) {
        $part1 = substr($no_inv, 0, $pecahkan);     // Sebelum '/'
        $part2 = '/' . $revisi;                        // Revisi, misal /Rev1
        $part3 = substr($no_inv, $pecahkan);       // Setelah posisi '/'
        return $part1 . $part2 . $part3;               // Gabungkan
    } else {
        return $no_inv; // Jika tidak ada '/', kembalikan asli
    }
}

// Contoh Penggunaan
// $no_inv = "1020/KM/I/2025";

// Cari posisi '/' pertama di luar function
// $pecahkan = strpos($no_inv, '/');

// Panggil fungsi dengan parameter pecahkan dan posisi '/'
// $result = tambahRevisi($no_inv, $pecahkan);

// echo $result; // Output: 1020/Rev1/KM/I/2025


function incrementRevision($no_inv_revisi) {
    // Pisahkan no_inv_revisi menjadi 3 bagian: kode, revisi, dan sisanya
    $parts = explode('/', $no_inv_revisi, 3);

    if (count($parts) < 3) {
        // Jika format tidak sesuai, kembalikan no_inv_revisi asli
        return $no_inv_revisi;
    }

    $part1 = $parts[0]; // contoh: "1020"
    $part2 = $parts[1]; // contoh: "Rev4"

    // Cek dan ambil bagian huruf dan angka dari revisi
    if (preg_match('/(\D+)(\d+)/', $part2, $matches)) {
        $prefix = $matches[1];            // contoh: "Rev"
        $number = (int)$matches[2] + 1;   // contoh: 4 + 1 = 5
        $part2 = $prefix . $number;       // contoh: "Rev5"
    }

    $part3 = '/' . $parts[2]; // contoh: "/KM/I/2025"

    // Gabungkan kembali
    return $part1 . '/' . $part2 . $part3;
}

// Contoh penggunaan:
// $original = "1020/Rev4/KM/I/2025";
// $updated = incrementRevision($original);

// echo "<pre>";
// echo $updated; // Output: 1020/Rev5/KM/I/2025
// echo "</pre>";
?>