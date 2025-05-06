<?php  
    require_once "../akses.php";
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

    if ($get_sort == 'semua_data') {
        $sort_data = "YEAR(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = YEAR(CURDATE()) ORDER BY STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')";
    } else if ($get_sort == 'hari_ini') {
        $sort_data = "DATE(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = CURDATE()";
    } else if ($get_sort == 'minggu_ini') {
        $sort_data = " WEEK(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = WEEK(CURDATE())
                        AND YEAR(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = YEAR(CURDATE())";
    } else if ($get_sort == 'bulan_ini') {
        $sort_data = "MONTH(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = MONTH(CURDATE())
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
        // echo '<script type="text/javascript">
        //         window.location = "../404.php";
        //     </script>';
        exit();
    }

    $sql = "SELECT 
                fnc.id_finance,
                fnc.id_inv,
                fnc.jenis_inv,
                fnc.total_cb,
                byr.id_finance, 
                SUM(byr.total_bayar) AS total_pembayaran,
                SUM(byr.total_potongan) AS total_potongan,
                SUM(byr_cb.total_bayar) AS total_pembayaran_cb,     
                spk.no_po,
                ppn.total_ppn AS total_ppn,
                COALESCE(cb_nonppn.status_cb, cb_ppn.status_cb, cb_bum.status_cb) AS status_cb,
                COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                COALESCE(nonppn.kategori_inv, ppn.kategori_inv, bum.kategori_inv) AS kategori_inv,
                COALESCE(nonppn.total_inv, ppn.total_inv, bum.total_inv) AS total_inv,
                COALESCE(nonppn.ongkir_free, ppn.ongkir_free, bum.ongkir_free) AS ongkir_free
            FROM finance AS fnc
            LEFT JOIN inv_nonppn nonppn ON fnc.id_inv = nonppn.id_inv_nonppn
            LEFT JOIN inv_ppn ppn ON fnc.id_inv = ppn.id_inv_ppn
            LEFT JOIN inv_bum bum ON fnc.id_inv = bum.id_inv_bum
            LEFT JOIN cashback_nonppn cb_nonppn ON fnc.id_inv = cb_nonppn.id_inv
            LEFT JOIN cashback_ppn cb_ppn ON fnc.id_inv = cb_ppn.id_inv
            LEFT JOIN cashback_bum cb_bum ON fnc.id_inv = cb_bum.id_inv
            LEFT JOIN spk_reg spk ON fnc.id_inv = spk.id_inv
            LEFT JOIN finance_bayar byr ON (fnc.id_finance = byr.id_finance)
            LEFT JOIN finance_bayar_cb byr_cb ON (fnc.id_finance = byr_cb.id_finance)
            WHERE $sort_data AND COALESCE(cb_nonppn.status_cb, cb_ppn.status_cb, cb_bum.status_cb) = '1'
            GROUP BY fnc.id_inv";
    $query = $connect->query($sql);
?>