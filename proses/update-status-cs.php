<?php
include "../akses.php";

// Mengambil data dari POST request
$id_cs = base64_decode($_POST['id_cs']);
$status_aktif = $_POST['status_aktif'];

// Debugging
// echo "ID CS: $id_cs<br>";
// echo "Status Aktif: $status_aktif<br>";

// Query untuk update status
$stmt = $connect->prepare("UPDATE tb_customer SET status_aktif = ? WHERE id_cs = ?");
$stmt->bind_param("is", $status_aktif, $id_cs);
$update_cs = $stmt->execute();

// Tutup koneksi
mysqli_stmt_close($stmt);
mysqli_close($connect);
