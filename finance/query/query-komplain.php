<?php 
    $sort_option ="";
    $today = date('d/m/Y');
    $startWeek = date('d/m/Y', strtotime("-1 week"));
    $endWeek = date('d/m/Y', strtotime("now"));
    $thisWeekStart= date('d/m/Y',strtotime('last sunday'));
    $thisWeekEnd= date('d/m/Y',strtotime('next sunday'));
    $thisMonth = date('m');

    // Kode Khusus Untuk Last Mont
    // Dapatkan tanggal saat ini
    $tanggalSaatIni = new DateTime();

    // Set tanggal ke awal bulan
    $tanggalSaatIni->setDate($tanggalSaatIni->format('Y'), $tanggalSaatIni->format('m'), 1);

    // Kurangkan satu bulan dari tanggal saat ini
    $tanggalSaatIni->modify('-1 month');

    // Dapatkan bulan dalam format numerik (dengan angka nol di depan jika berlaku)
    $lastMonth = $tanggalSaatIni->format('m');

    // Tampilkan nilai bulan sebelumnya
    $thisYear = date('Y');
    $lastYear = date("Y",strtotime("-1 year"));
    if(isset($_GET['date_range']))
    {
        if($_GET['date_range'] == "today")
        {
            $sort_option = "DATE(STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y')) = CURDATE()";
        }

        elseif($_GET['date_range'] == "weekly")
        {
            $sort_option = "
                            WEEK(STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y')) = WEEK(CURDATE())
                            AND YEAR(STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y')) = YEAR(CURDATE())
                        ";
        }

        elseif($_GET['date_range'] == "monthly")
        {

            $sort_option = "
                            MONTH(STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y')) = MONTH(CURDATE())
                            AND YEAR(STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y')) = YEAR(CURDATE())
                        "; 
            
        }

        elseif($_GET['date_range'] == "lastMonth")
        {
            $sort_option = " 
                            MONTH(STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y')) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                            AND YEAR(STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y')) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                            ";  
        }

        elseif($_GET['date_range'] == "year")
        {
            $sort_option = "YEAR(STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y')) = YEAR(CURDATE())";
        }

        elseif($_GET['date_range'] == "lastyear")
        {
            $sort_option = "YEAR(STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y')) = YEAR(CURDATE()) - 1";
        }
    }
    if (isset($_GET["start_date"]) && isset($_GET["end_date"])) {
    $dt1 = $_GET["start_date"];
    $dt2 = $_GET["end_date"];
    $format_dt1 = date('d/m/Y', strtotime($dt1));
    $format_dt2 = date('d/m/Y', strtotime($dt2));
    $sort_option = "STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') BETWEEN STR_TO_DATE('$format_dt1', '%d/%m/%Y') AND STR_TO_DATE('$format_dt2', '%d/%m/%Y')";
    // Lakukan sesuatu dengan $sort_option, misalnya memproses data dari database
    }

    $status = isset($_GET['status']) ? $_GET['status'] : ''; // Periksa apakah status ada dalam query string

// $sql = "SELECT
//             id_inv,
//             cs_inv,
//             no_inv,
//             tanggal,
//             no_komplain,
//             kat_komplain,
//             kondisi_pesanan,
//             status_retur,
//             status_refund,
//             total_komplain_aktif,
//             total_komplain_selesai,
//             total_komplain,
//             status_komplain
//         FROM (
//             SELECT
//                 COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
//                 COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
//                 COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
//                 STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') AS tanggal,
//                 ik.no_komplain,
//                 ik.kat_komplain,
//                 ik.kondisi_pesanan,
//                 ik.status_retur,
//                 ik.status_refund,
//                 COUNT(CASE WHEN ik.status_komplain = '0' THEN 1 ELSE NULL END) AS total_komplain_aktif,
//                 COUNT(CASE WHEN ik.status_komplain = '1' THEN 1 ELSE NULL END) AS total_komplain_selesai,
//                 COUNT(ik.status_komplain) AS total_komplain,
//                 ik.status_komplain
//             FROM inv_komplain AS ik
//             LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
//             LEFT JOIN inv_ppn ppn ON ik.id_inv = ppn.id_inv_ppn
//             LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
//             WHERE $sort_option AND ik.tgl_komplain IS NOT NULL";

$sql = "SELECT
            COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
            COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
            COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
            COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi) AS status_transaksi,
            STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') AS tanggal,
            ik.id_komplain,
            ik.no_komplain,
            COUNT(CASE WHEN ik.status_komplain = '0' THEN 1 ELSE NULL END) AS total_komplain_aktif,
            COUNT(CASE WHEN ik.status_komplain = '1' THEN 1 ELSE NULL END) AS total_komplain_selesai,
            COUNT(ik.status_komplain) AS total_komplain,
            ik.status_komplain
        FROM inv_komplain AS ik
        LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
        LEFT JOIN inv_ppn ppn ON ik.id_inv = ppn.id_inv_ppn
        LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
        WHERE $sort_option AND ik.tgl_komplain IS NOT NULL
        GROUP BY id_inv, cs_inv, no_inv";

// Hanya tambahkan kondisi status jika $status tidak kosong
if (!empty($status)) {
    $sql .= " AND ik.status_komplain = '$status'";
}

// $sql .= " GROUP BY id_inv, cs_inv, no_inv, tanggal, no_komplain, kat_komplain, kondisi_pesanan, status_retur, status_refund, status_komplain
//         ) AS subquery";

$query = mysqli_query($connect, $sql);
$query2 = mysqli_query($connect, $sql);
?>