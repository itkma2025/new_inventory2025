<?php
include "../koneksi.php"; // Memuat koneksi ke database
date_default_timezone_set('Asia/Jakarta');
$limit = 5;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

// Query utama untuk mengambil data
$sql_spk = "SELECT 
                spk.id_spk_reg, spk.no_spk, cs.nama_cs, spk.status_spk, 
                STR_TO_DATE(spk.notif_date, '%Y/%m/%d, %H:%i:%s') AS notif_date, 
                spk.status_notif
            FROM spk_reg AS spk
            LEFT JOIN tb_customer cs ON spk.id_customer = cs.id_cs
            WHERE spk.status_spk = 'Siap Kirim' 
            AND DATE(STR_TO_DATE(spk.notif_date, '%Y/%m/%d, %H:%i:%s')) = CURDATE()
            ORDER BY notif_date DESC
            LIMIT $limit OFFSET $offset";

$query = $connect->query($sql_spk);

// Query untuk menghitung total data tanpa LIMIT dan OFFSET
$sql_spk_count = "SELECT COUNT(*) AS total 
                    FROM spk_reg AS spk
                    WHERE spk.status_spk = 'Siap Kirim' 
                    AND DATE(STR_TO_DATE(spk.notif_date, '%Y/%m/%d, %H:%i:%s')) = CURDATE()";

$count_result = $connect->query($sql_spk_count);
$count_row = $count_result->fetch_assoc();
$totalData = $count_row['total'];

// Inisialisasi variabel respons
$response = [
    'notifications' => '',
    'totalData' => $totalData,
    'lastRowNumber' => null,
    'allDataLoaded' => ($offset + $limit) >= $totalData,
];

// Inisialisasi counter
$total_status_notif_0 = 0;
$total_status_notif_1 = 0;

// Inisialisasi variabel HTML untuk notifikasi
$html_notifications = '';

// Menyimpan hasil query dalam array
$results = [];
$row_number = $offset + 1; // Menghitung row number dari offset

// Loop melalui hasil query jika ada data
if ($query->num_rows > 0) {
    while ($row = $query->fetch_assoc()) {
        $row['row_number'] = $row_number++;
        $results[] = $row;

        if ($row['status_notif'] == 0) {
            $total_status_notif_0++;
        } elseif ($row['status_notif'] == 1) {
            $total_status_notif_1++;
        }
    }

    // Ambil nomor urut terakhir dari hasil query
    $last_row_number = end($results)['row_number'];
    $response['lastRowNumber'] = $last_row_number;

    // Loop untuk menghasilkan HTML notifikasi
    foreach ($results as $row) {
        // menampilkan waktu
        $notif_datetime = strtotime($row['notif_date']);
        $notif_date = date('Y-m-d H:i', $notif_datetime);
        $time_now = date('Y-m-d H:i');
        $date_notif = date('d M Y', $notif_datetime);

        // Buat objek DateTime dari string waktu
        $notif_datetime_obj = DateTime::createFromFormat('Y-m-d H:i', $notif_date);
        $current_datetime_obj = DateTime::createFromFormat('Y-m-d H:i', $time_now);

        // Hitung selisih waktu
        $interval = $current_datetime_obj->diff($notif_datetime_obj);

        // Ambil jumlah hari, jam, dan menit dari selisih waktu
        $days = $interval->d;
        $hours = $interval->h;
        $minutes = $interval->i;
        $time = "";

        // Tampilkan hasil dalam format yang sesuai
        if ($days > 0) {
            $time = $date_notif;
        } elseif ($hours > 0) {
            $time = $hours . " jam " . $minutes . " menit yang lalu";
        } else {
            $time = $minutes . " menit yang lalu";
        }

        $color_notif = "";
        $margin = "";
        $display = "";
        if ($row['status_notif'] != 0) {
            $color_notif = "";
            $margin = "margin-left:3px;";
            $display = "display: none;";
        } else {
            $color_notif = "background-color: #d7e9fb";
            $margin = "";
            $display = "";
        }

        // Konstruksi HTML untuk notifikasi
        $html_notifications .= '<a href="detail-produk-spk-reg-siap-kirim.php?id=' . base64_encode($row['id_spk_reg']) . '&status=1" class="text-dark">';
        $html_notifications .= '<li class="notification-item mt-2 p-2" style="' . $color_notif . '">';
        $html_notifications .= '<div class="me-1">';
        $html_notifications .= '</div>';
        $html_notifications .= '<div style="' . $margin . '">';
        $html_notifications .= '<div class="row"> <div class="col-1"></div><div class="col-9"><b style="font-size: 15px">' . $row['no_spk'] . '</b></div><div class="col-2 text-end"><span>' . date('H:i', $notif_datetime) . '</span></div></div>';
        $html_notifications .= '<div class="row"><div class="col-1"><input type="radio" class="me-2" style="' . $display . '" checked></div><div class="col-11"><p>Pesanan ' . $row['nama_cs'] . ' Siap Kirim</p></div></div>';
        $html_notifications .= '<div class="row"><div class="col-1"></div><div class="col-9"><p>' . $time . '</p></div><div class="col-2"></div></div>';
        $html_notifications .= '</div>';
        $html_notifications .= '</li>';
        $html_notifications .= '</a>';
    }
}

// Setel notifikasi HTML di respons
$response['notifications'] = $html_notifications;

// Mengirimkan respons dalam format JSON
echo json_encode($response);
?>