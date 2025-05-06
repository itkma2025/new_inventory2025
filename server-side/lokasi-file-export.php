<?php
include "../koneksi.php";

// Query untuk mengambil data
$sql = "SELECT id_lokasi, nama_lokasi, no_lantai, nama_area, no_rak FROM tb_lokasi_produk";
$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    // Mengeluarkan data dari setiap baris
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} 

$conn->close();

// Mengembalikan data dalam format JSON
echo json_encode($data);
?>
