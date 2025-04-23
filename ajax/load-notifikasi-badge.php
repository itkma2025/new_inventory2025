<?php

include '../koneksi.php';  // Pastikan untuk menyertakan file koneksi database Anda

$sql_spk = "SELECT COUNT(*) AS total FROM spk_reg AS spk
            WHERE spk.status_spk = 'Siap Kirim' AND spk.status_notif = '0'";
$count_result = $connect->query($sql_spk);

if ($count_result) {
    $count_row = $count_result->fetch_assoc();
    $totalData = $count_row['total'];
    echo json_encode(['total' => $totalData]);
} else {
    echo json_encode(['total' => 0]);  // Return 0 if query fails
}
?>
