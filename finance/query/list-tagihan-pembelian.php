<?php  
    $key = "payment2024";
    $no = 1;
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
            $sort_option = "DATE(STR_TO_DATE(fp.tgl_pembayaran, '%d/%m/%Y')) = CURDATE()";
        }

        elseif($_GET['date_range'] == "weekly")
        {
            $sort_option = "
                            WEEK(STR_TO_DATE(fp.tgl_pembayaran, '%d/%m/%Y')) = WEEK(CURDATE())
                            AND YEAR(STR_TO_DATE(fp.tgl_pembayaran, '%d/%m/%Y')) = YEAR(CURDATE())
                        ";
        }

        elseif($_GET['date_range'] == "monthly")
        {

            $sort_option = "
                            MONTH(STR_TO_DATE(fp.tgl_pembayaran, '%d/%m/%Y')) = MONTH(CURDATE())
                            AND YEAR(STR_TO_DATE(fp.tgl_pembayaran, '%d/%m/%Y')) = YEAR(CURDATE())
                        "; 
            
        }

        elseif($_GET['date_range'] == "lastMonth")
        {
            $sort_option = " 
                            MONTH(STR_TO_DATE(fp.tgl_pembayaran, '%d/%m/%Y')) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                            AND YEAR(STR_TO_DATE(fp.tgl_pembayaran, '%d/%m/%Y')) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                            ";  
        }

        elseif($_GET['date_range'] == "year")
        {
            $sort_option = "YEAR(STR_TO_DATE(fp.tgl_pembayaran, '%d/%m/%Y')) = YEAR(CURDATE())";
        }

        elseif($_GET['date_range'] == "lastyear")
        {
            $sort_option = "YEAR(STR_TO_DATE(fp.tgl_pembayaran, '%d/%m/%Y')) = YEAR(CURDATE()) - 1";
        }
    }
    if (isset($_GET["start_date"]) && isset($_GET["end_date"])) {
    $dt1 = $_GET["start_date"];
    $dt2 = $_GET["end_date"];
    $format_dt1 = date('d/m/Y', strtotime($dt1));
    $format_dt2 = date('d/m/Y', strtotime($dt2));
    $sort_option = "STR_TO_DATE(fp.tgl_pembayaran, '%d/%m/%Y') BETWEEN STR_TO_DATE('$format_dt1', '%d/%m/%Y') AND STR_TO_DATE('$format_dt2', '%d/%m/%Y')";
    // Lakukan sesuatu dengan $sort_option, misalnya memproses data dari database
    }
    $sql_pembayaran = $connect->query(" SELECT
                                            fp.id_pembayaran,
                                            fp.no_pembayaran,
                                            fp.tgl_pembayaran,
                                            fp.jenis_faktur,
                                            fp.total_tagihan,
                                            byr.id_bayar,
                                            COALESCE(SUM(byr.total_bayar), 0) AS total_bayar,
                                            ipl.id_inv_pembelian,
                                            ipl.status_lunas,
                                            sp.nama_sp
                                        FROM finance_pembayaran_produk_lokal AS fp  
                                        LEFT JOIN inv_pembelian_lokal ipl ON fp.id_pembayaran = ipl.id_pembayaran
                                        LEFT JOIN 
                                            (SELECT id_bayar, id_inv_pembelian, COALESCE(total_bayar, 0) AS total_bayar FROM finance_bayar_pembelian) byr 
                                            ON ipl.id_inv_pembelian = byr.id_inv_pembelian
                                        LEFT JOIN tb_supplier sp ON ipl.id_sp =  sp.id_sp
                                        WHERE $sort_option AND ipl.status_lunas = '0'
                                        GROUP BY sp.nama_sp
                                    ");
?>