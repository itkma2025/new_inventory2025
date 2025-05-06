<?php  
    $no = 1;
    require_once "../function/validasi-date.php";
    require_once "../function/function-enkripsi.php";
    
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    
    // Dekripsi data
    $decryptedStartDate = decrypt($start_date, $key_finance);
    $decryptedEndDate = decrypt($end_date, $key_finance);
    
    $text_date_dropdown = $decryptedStartDate . " - " . $decryptedEndDate;
    $placeholder_input_date = "";
    if ($text_date_dropdown != "") {
        $placeholder_input_date = $text_date_dropdown;
    } else {
        $placeholder_input_date = "Select Date Range";
    }
    // echo $text_date_dropdown;
    if (isset($_GET["start_date"]) && isset($_GET["end_date"])) {
        $dt1 = $_GET["start_date"];
        $dt2 = $_GET["end_date"];
        $format_dt1 = date('d/m/Y', strtotime($dt1));
        $format_dt2 = date('d/m/Y', strtotime($dt2));
        $sort_option = "STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y') BETWEEN STR_TO_DATE('$format_dt1', '%d/%m/%Y') AND STR_TO_DATE('$format_dt2', '%d/%m/%Y')";
        // Lakukan sesuatu dengan $sort_option, misalnya memproses data dari database
    }

    $get_sort = isset($_GET['sort_data']) ? $_GET['sort_data'] : '';
    $sort_data = '';

    if ($get_sort == 'hari_ini') {
        $sort_data = " DATE(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = CURDATE()";
    } else if ($get_sort == 'minggu_ini') {
        $sort_data = " WEEK(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = WEEK(CURDATE())
                        AND YEAR(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = YEAR(CURDATE())";
    } else if ($get_sort == 'bulan_ini') {
        $sort_data = " MONTH(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = MONTH(CURDATE())
                        AND YEAR(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = YEAR(CURDATE())";
    } else if ($get_sort == 'bulan_kemarin') {
        $sort_data = "MONTH(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                         AND YEAR(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
    } else if ($get_sort == 'tahun_ini') {
        $sort_data = "YEAR(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = YEAR(CURDATE())";
    } else if ($get_sort == 'tahun_kemarin') {
        $sort_data = "YEAR(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = YEAR(CURDATE()) - 1";
    } else if ($get_sort == 'date_range') {
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

        // Dekripsi data
        $decryptedStartDate = decrypt($start_date, $key_finance);
        $decryptedEndDate = decrypt($end_date, $key_finance);

        // Pengecekan apakah hasil dekripsi adalah tanggal yang valid
        if (!isValidDate($decryptedStartDate) || !isValidDate($decryptedEndDate)) {
            // Arahkan ke 404.php jika bukan tanggal yang valid
            echo '<script type="text/javascript">
                    window.location = "../404.php";
                </script>';
            exit();
        } else {
            $sort_data = "STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y') BETWEEN STR_TO_DATE('$decryptedStartDate', '%d/%m/%Y') AND STR_TO_DATE('$decryptedEndDate', '%d/%m/%Y')";
        }
    } else {
        echo '<script type="text/javascript">
                window.location = "../404.php";
            </script>';
        exit();
    }
    $total_nominal_trx = 0;
    $total_nominal_bayar = 0;
    $sql =" SELECT DISTINCT
                fnc.id_finance,
                fnc.jenis_inv,
                fnc.status_pembayaran,
                fnc.status_lunas,
                COALESCE(fnc.status_tagihan, 0) AS status_tagihan,
                spk.id_customer,
                cs.nama_cs AS nama_cs,
                COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi) AS status_trx,
                STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y') AS tgl_inv,
                COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo) AS tgl_tempo,
                STR_TO_DATE(COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo), '%d/%m/%Y') AS tgl_tempo_convert,
                COALESCE(nonppn.total_inv, ppn.total_inv, bum.total_inv) AS total_inv,
                ft.no_tagihan,
                COALESCE(total_bayar, 0) AS total_bayar
            FROM spk_reg AS spk
            LEFT JOIN inv_nonppn nonppn ON (spk.id_inv = nonppn.id_inv_nonppn)
            LEFT JOIN inv_ppn ppn ON (spk.id_inv = ppn.id_inv_ppn)
            LEFT JOIN inv_bum bum ON (spk.id_inv = bum.id_inv_bum)
            LEFT JOIN finance fnc ON (spk.id_inv = fnc.id_inv)
            LEFT JOIN finance_tagihan ft ON (fnc.id_tagihan = ft.id_tagihan)
            LEFT JOIN (
                SELECT id_finance, SUM(total_bayar) AS total_bayar
                FROM finance_bayar
                GROUP BY id_finance
            ) fb ON (fnc.id_finance = fb.id_finance)
            LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
            WHERE spk.id_customer = '$id_cs' AND COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi) <> 'Cancel Order' AND spk.status_spk <> 'Cancel Order' AND $sort_data
            GROUP BY COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) ORDER BY  STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y') ASC";
    $query = mysqli_query($connect, $sql);
    $query_nominal = mysqli_query($connect, $sql);
    $total_trx = mysqli_num_rows($query);
?>