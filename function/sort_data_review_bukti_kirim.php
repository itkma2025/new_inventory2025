<?php  
    $no = 1;
    require_once "validasi-date.php";
    require_once "function-enkripsi.php";
    
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    
    // Dekripsi data
    $decryptedStartDate = decrypt($start_date, $key_global);
    $decryptedEndDate = decrypt($end_date, $key_global);
    
    $text_date_dropdown = $decryptedStartDate . " - " . $decryptedEndDate;
    $placeholder_input_date = "";
    if ($text_date_dropdown != "") {
        $placeholder_input_date = $text_date_dropdown;
    } else {
        $placeholder_input_date = "Select Date Range";
    }
    // echo $text_date_dropdown;
    if (isset($_GET["start_date"]) && isset($_GET["end_date"])) {
         $dt1 = $decryptedStartDate;
         $dt2 = $decryptedEndDate;
         $format_dt1 = date('d/m/Y', strtotime($dt1));
         $format_dt2 = date('d/m/Y', strtotime($dt2));
        $sort_data = " ibt.created_date BETWEEN STR_TO_DATE('$dt1', '%d/%m/%Y') AND STR_TO_DATE('$dt2', '%d/%m/%Y')";
        // Lakukan sesuatu dengan $sort_option, misalnya memproses data dari database
        
    }

    $get_sort = isset($_GET['sort_data']) ? $_GET['sort_data'] : '';
    $sort_data = '';

    if ($get_sort == 'semua_data') {
        $sort_data = "YEAR(ibt.created_date) = YEAR(CURDATE()) ORDER BY ibt.created_date";
    } else if ($get_sort == 'hari_ini') {
        $sort_data = "DATE(ibt.created_date) = CURDATE()";
    } else if ($get_sort == 'minggu_ini') {
        $sort_data = " WEEK(ibt.created_date) = WEEK(CURDATE())
                        AND YEAR(ibt.created_date) = YEAR(CURDATE())";
    } else if ($get_sort == 'bulan_ini') {
        $sort_data = "MONTH(ibt.created_date) = MONTH(CURDATE())
                        AND YEAR(ibt.created_date) = YEAR(CURDATE())";
    } else if ($get_sort == 'bulan_kemarin') {
        $sort_data = "MONTH(ibt.created_date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                        AND YEAR(ibt.created_date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
    } else if ($get_sort == 'tahun_ini') {
        $sort_data = "YEAR(ibt.created_date) = YEAR(CURDATE())";
    } else if ($get_sort == 'tahun_kemarin') {
        $sort_data = "YEAR(ibt.created_date) = YEAR(CURDATE()) - 1";
    } else if ($get_sort == 'date_range') {
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

        // Dekripsi data
        $decryptedStartDate = decrypt($start_date, $key_global);
        $decryptedEndDate = decrypt($end_date, $key_global);

        // Pengecekan apakah hasil dekripsi adalah tanggal yang valid
        if (!isValidDate($decryptedStartDate) || !isValidDate($decryptedEndDate)) {
            // Arahkan ke 404.php jika bukan tanggal yang valid
            echo '<script type="text/javascript">
                    window.location = "../404.php";
                </script>';
            exit();
        } else {
            $sort_data = "ibt.created_date BETWEEN STR_TO_DATE('$dt1', '%d/%m/%Y') AND STR_TO_DATE('$dt2', '%d/%m/%Y')";
        }
    } else {
        echo '<script type="text/javascript">
                window.location = "../404.php";
            </script>';
        exit();
    }
?>