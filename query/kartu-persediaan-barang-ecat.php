<?php 
    require_once "function/validasi-date.php";

    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    // Dekripsi data
    $decryptedStartDate = decrypt($start_date, $key);
    $decryptedEndDate = decrypt($end_date, $key);
    $text_date_dropdown = $decryptedStartDate . " - " .$decryptedEndDate;
    $placeholder_input_date = "";
    if($text_date_dropdown != ""){
        $placeholder_input_date = $text_date_dropdown;
    } else {
        $placeholder_input_date = "Select Date Range";
    }
    // echo $text_date_dropdown;
 
    // Menampilkan detail produk
    $sql_produk_reg = "SELECT 
                            pr.id_produk_ecat as 'produk_id',  
                            pr.nama_produk,
                            pr.gambar,
                            pr.deskripsi,  
                            mr.nama_merk,
                            kp.nama_kategori as kat_prod,
                            kp.no_izin_edar,
                            lok.nama_lokasi,
                            lok.no_lantai,
                            lok.nama_area,
                            lok.no_rak,
                            ksr.id_kartu_stock,
                            ksr.status_barang,
                            ksr.keterangan,
                            ksr.qty_in,
                            ksr.qty_out,
                            ksr.created_date,
                            ket_in.ket_in,
                            ket_out.ket_out,
                            us.nama_user as created_by
                        FROM tb_produk_ecat as pr
                        LEFT JOIN tb_merk mr ON (pr.id_merk = mr.id_merk)
                        LEFT JOIN tb_kat_produk kp ON (pr.id_kat_produk = kp.id_kat_produk)
                        LEFT JOIN tb_lokasi_produk lok ON (pr.id_lokasi = lok.id_lokasi)
                        LEFT JOIN stock_produk_ecat spe ON (pr.id_produk_ecat = spe.id_produk_ecat)
                        LEFT JOIN kartu_stock_ecat ksr ON (pr.id_produk_ecat = ksr.id_produk)
                        LEFT JOIN keterangan_in ket_in ON (ksr.jenis_barang_masuk = ket_in.id_ket_in)
                        LEFT JOIN keterangan_out ket_out ON (ksr.jenis_barang_keluar = ket_out.id_ket_out)
                        LEFT JOIN $database2.user AS us ON (ksr.created_by = us.id_user)
                        WHERE pr.id_produk_ecat ='$id_produk'";
        
    $query_produk = $connect->query($sql_produk_reg);

    // Filter date
    $get_sort = $_GET['sort_data'];
    $sort_data = "";
    if ($get_sort == "semua_data"){
        $sort_data = "ORDER BY ksr.created_date ASC";
    } else if ($get_sort == "hari_ini") {
        $sort_data = "AND DATE(ksr.created_date) = CURDATE() ORDER BY ksr.created_date ASC";
    } else if ($get_sort == "minggu_ini") {
        $sort_data = "AND WEEK(ksr.created_date) = WEEK(CURDATE()) AND YEAR(ksr.created_date) = YEAR(CURDATE()) ORDER BY ksr.created_date ASC";
    } else if ($get_sort == "bulan_ini") {
        $sort_data = "AND MONTH(ksr.created_date) = MONTH(CURDATE()) AND YEAR(ksr.created_date) = YEAR(CURDATE()) ORDER BY ksr.created_date ASC";
    } else if ($get_sort == "bulan_kemarin") {
        $sort_data = "AND MONTH(ksr.created_date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(ksr.created_date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) ORDER BY ksr.created_date ASC";
    } else if ($get_sort == "tahun_kemarin") {
        $sort_data = "AND YEAR(ksr.created_date) = YEAR(CURDATE()) - 1 ORDER BY ksr.created_date ASC";
    } else if ($get_sort == "date_range") {
        // Pengecekan apakah hasil dekripsi adalah tanggal yang valid
        if (!isValidDate($decryptedStartDate) || !isValidDate($decryptedEndDate)) {
            // Arahkan ke 404.php jika bukan tanggal yang valid
            echo '<script type="text/javascript">
                    window.location = "404.php";
                </script>';
            exit();
        } else {
            $sort_data = "AND DATE_FORMAT(ksr.created_date, '%d/%m/%Y') BETWEEN '$decryptedStartDate' AND '$decryptedEndDate' ORDER BY ksr.created_date ASC";
        }
    } else {
        echo '<script type="text/javascript">
                window.location = "404.php";
            </script>';
        exit();
    }

    // Menampilkan urutan ASC
    $sql_produk_asc = $sql_produk_reg . ' ' . $sort_data;
    $query_produk2 = $connect->query($sql_produk_asc);

    // Query untuk mendapatkan data terbaru
    $sql_produk_limit_stock = $sql_produk_reg . " ORDER BY ksr.created_date DESC LIMIT 1";
    $query_produk_limit_stock = $connect->query($sql_produk_limit_stock);

    $sql_produk_limit_in = $sql_produk_reg . " AND status_barang = '0' ORDER BY ksr.created_date DESC LIMIT 1";
    $query_produk_limit_in = $connect->query($sql_produk_limit_in);

    $sql_produk_limit_out = $sql_produk_reg . " AND status_barang = '1' ORDER BY ksr.created_date DESC LIMIT 1";
    $query_produk_limit_out = $connect->query($sql_produk_limit_out);

    // Menampilkan group by created_by
    $sql_produk_petugas = $sql_produk_reg . " GROUP BY ksr.created_by ORDER BY ksr.created_by ASC";
    $query_produk_petugas = $connect->query($sql_produk_petugas);

    // Menampilkan total stock
    $sql_total_stock = $sql_produk_reg . " ORDER BY ksr.created_date ASC";
    $query_total_stock = $connect->query($sql_total_stock);

    
    
?>