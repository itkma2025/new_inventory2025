<?php
// Pastikan tidak ada whitespace atau HTML sebelum atau setelah tag PHP
// Hapus semua output debug atau echo di seluruh file
// Hapus semua require/include yang mungkin mencetak HTML

// Aktifkan output buffering untuk menangkap error
ob_start();

// Matikan tampilan error ke output
ini_set('display_errors', 0);
error_reporting(E_ERROR);

header("Content-Type: application/json"); 
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . "/../akses.php";

// Hapus output buffer sebelum mengeluarkan konten JSON
ob_end_clean();

// Query data terbaru
$sql = "
            SELECT 
                COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv
            FROM spk_reg AS sr
            LEFT JOIN inv_nonppn AS nonppn ON sr.id_inv = nonppn.id_inv_nonppn
            LEFT JOIN inv_ppn AS ppn ON sr.id_inv = ppn.id_inv_ppn
            LEFT JOIN inv_bum AS bum ON sr.id_inv = bum.id_inv_bum
            LEFT JOIN status_kirim sk ON sr.id_inv = sk.id_inv
            LEFT JOIN inv_bukti_terima ibt ON sk.id_inv = ibt.id_inv
            WHERE COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi) = 'Diterima' 
            AND sk.status_review = '0' 
            GROUP BY no_inv
        ";

$query = mysqli_query($connect, $sql);

// Cek jika query gagal
if (!$query) {
    die(json_encode(["error" => "Query failed: " . mysqli_error($connect)]));
}

// Hitung total data
$total_data_perlu_review = mysqli_num_rows($query);

// Pastikan hanya JSON yang dioutput
die(json_encode(["total_data_perlu_review" => $total_data_perlu_review]));
?>
