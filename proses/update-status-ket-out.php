<?php
require_once "../akses.php";
// Function Encrypt dan Decrypt
require_once "../function/function-enkripsi.php";

// Mengambil data dari POST request
$id_ket_out = decrypt($_POST['id_ket_out'], $key_global);
$status_aktif = $_POST['status_aktif'];

// Debugging
// echo "ID CS: $id_ket_out<br>";
// echo "Status Aktif: $status_aktif<br>";

// Query untuk update status
$stmt = $connect->prepare("UPDATE keterangan_out SET status_aktif = ? WHERE id_ket_out = ?");
$stmt->bind_param("is", $status_aktif, $id_ket_out);
$update = $stmt->execute();

// Tutup koneksi
mysqli_stmt_close($stmt);
mysqli_close($connect);
