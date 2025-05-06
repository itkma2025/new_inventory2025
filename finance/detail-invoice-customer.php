<?php
$page = 'list-cs';
require_once "../akses.php";
include 'function/class-finance.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'page/head.php'; ?>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link rel="stylesheet" type="text/css" media="all" href="daterangepicker.css" />

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.min.js"></script>
    <script type="text/javascript" src="daterangepicker.js"></script>
    <style>
        /* Custom styling for the date inputs */
        .form-control[type="date"] {
        appearance: none;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        }

        /* Optional: Adjust the date input height and font-size */
        .form-control[type="date"] {
        height: 38px;
        font-size: 14px;
        }

        /* Adjust the position of the dropdown */
        .dropdown {
        display: inline-block;
        position: relative;
        }

        /* Adjust the style of the dropdown menu items */
        .dropdown-menu {
        min-width: 350px;
        padding: 20px;
        }

        .dropdown-item{
            text-align: center;
            border: 1px solid #ced4da;
            margin-bottom: 10px;
        }

        .transparent-card {
            background-color: transparent !important;
            border: none; /* Jika Anda juga ingin menghapus border */
            box-shadow: none;
        }

        .input-container {
            position: relative;
            display: inline-block;
        }

        #cari-data {    
            padding-right: 2.5rem;
        }

        #resetButton {
            position: absolute;
            top: 50%;
            right: 0.5rem;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            z-index: 2;
            display: none; /* Default state: hidden */
        }

        #resetButton i {
            font-size: 1rem;
        }

        #customPagination {
            justify-content: end;
        }

        /* Ubah display pagination bawaan datatable menjadi NONE */
        div.dt-container div.dt-paging ul.pagination {
            display: none;
        }

        .dropdown-item-list.active {
            background-color: #6c757d;
            color: white;
        }

        .margin-left{
            margin-left: 10.255em !important;
        }

        .btn-filter{
            width: 300px !important;
        }

        @media screen and (max-width: 727px) {
            .card{
                width: 100%;
                margin:  0 0 12px 0 !important;
                
            }
            .card-body{
                width: 100%;
                margin:  0 0 12px 0 !important;
                padding: 0px 0px 0px 0px !important;
                
            }
        }
    </style>
</head>
<body>
    <!-- nav header -->
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include "page/sidebar.php" ?>
    <!-- end sidebar -->
    <main id="main" class="main">
        <!-- Loading -->
        <!-- <div class="loader loader">
            <div class="loading">
            <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div> -->
        <!-- End Loading -->
        <section>
            <div class="card shadow p-2">
                <div class="card-header text-center">
                    <?php
                        $id_cs = base64_decode($_GET['cs']);
                        require_once "query/detail-inv-cs.php";
                        $sql_cs = mysqli_query($connect, "SELECT nama_cs FROM tb_customer WHERE id_cs = '$id_cs'");
                        $data_cs = mysqli_fetch_array($sql_cs);
                        $session_url = "detail-invoice-customer.php?cs=".$_GET['cs'];
                        $_SESSION['url'] = $session_url;   
                        $url = $_SESSION['url']; 
                        $key = $key_finance;
                    ?>
                    <h5>Detail Penjualan <?php echo $data_cs['nama_cs'] ?></h5>
                </div>
                <div class="row mb-5">
                    <div class="col-sm-4">
                        <div class="row row-cols-1 row-cols-lg-2 g-2 g-lg-3">
                            <div class="col">
                                <div class="p-3">
                                    <div class="card transparent-card" style="width: 100%;">
                                        <label>Filter Tanggal :</label>
                                        <div class="dropdown">
                                            <button id="dropdownButton" class="btn btn-primary dropdown-toggle form-control" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Pilih Filter
                                            </button>
                                            <ul class="dropdown-menu form-control p-3">
                                                <button type="button" class="btn btn-outline-primary form-control mb-2 dropdown-item-list" id="hari_ini">Hari Ini</button>
                                                <button type="button" class="btn btn-outline-primary form-control mb-2 dropdown-item-list" id="minggu_ini">Minggu Ini</a>
                                                <button type="button" class="btn btn-outline-primary form-control mb-2 dropdown-item-list" id="bulan_ini">Bulan Ini</button>
                                                <button type="button" class="btn btn-outline-primary form-control mb-2 dropdown-item-list" id="bulan_kemarin">Bulan Kemarin</button>
                                                <button type="button" class="btn btn-outline-primary form-control mb-2 dropdown-item-list" id="tahun_ini">Tahun Ini</button>
                                                <button type="button" class="btn btn-outline-primary form-control mb-2 dropdown-item-list" id="tahun_kemarin">Tahun Kemarin</button>
                                                <li id="dateRangePicker" class="date-range-picker">
                                                    <input type="text" id="dateRange" class="form-control mb-2 text-center" placeholder="<?php echo $placeholder_input_date ?>">
                                                </li>
                                                <button type="button" class="btn btn-danger form-control" id="reset">Reset</button>
                                            </ul>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-3">
                                    <label>Filter Jenis Invoice :</label>
                                    <div class="input-group flex-nowrap">
                                        <select class="form-select" id="filterJenisInv">
                                            <option value="all">Semua</option>
                                            <option value="Non PPN">Non PPN</option>
                                            <option value="PPN">PPN</option>
                                            <option value="BUM">BUM</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="row row-cols-1 row-cols-lg-4 g-2 g-lg-3">
                            <div class="col">
                                <div class="p-3">
                                    <label>Filter Status Invoice :</label>
                                    <div class="input-group flex-nowrap">
                                        <select class="form-select" id="filterStatusTrx">
                                            <option value="all">Semua</option>
                                            <option value="Transaksi Selesai">Transaksi Selesai</option>
                                            <option value="Komplain Selesai">Komplain Selesai</option>
                                            <option value="Belum Dikirim">Belum Dikirim</option>
                                            <option value="Dikirim">Dikirim</option>
                                            <option value="Diterima">Diterima</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-3">
                                    <label>Filter Status Pembayaran :</label>
                                    <div class="input-group flex-nowrap">
                                        <select class="form-select" id="filterStatusPembayaran">
                                            <option value="all">Semua</option>
                                            <option value="Belum Bayar">Belum Bayar</option>
                                            <option value="Sudah Bayar">Sudah Bayar</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-3">
                                    <label>Filter Status Pelunasan :</label>
                                    <div class="input-group flex-nowrap">
                                        <select class="form-control" id="filterStatusLunas">
                                            <option value="all">Semua</option>
                                            <option value="Lunas">Lunas</option>
                                            <option value="Belum Lunas">Belum Lunas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-3">
                                    <label>Filter Status Tagihan :</label>
                                    <div class="input-group flex-nowrap">
                                        <select class="form-select" id="filterStatusTagihan">
                                            <option value="all">Semua</option>
                                            <option value="Sudah Ditagih">Sudah Ditagih</option>
                                            <option value="Belum Dibuat">Belum Ditagih</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm p-2">
                            <div class="text-center border p-2">
                                <p><b>Total Pembelian</b></p>
                                <p><?php echo $total_trx; ?></p>
                            </div>
                        </div>
                        <div class="col-sm p-2">
                            <div class="text-center border p-2">
                                <?php  
                                    // Jalankan query komplain
                                    $sql_total_komplain =  $connect->query("  SELECT 
                                                                            komplain.id_komplain, 
                                                                            komplain.no_komplain,
                                                                            COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                                                                            COALESCE(spk_nonppn.id_customer, spk_ppn.id_customer, spk_bum.id_customer) AS id_customer
                                                                        FROM inv_komplain AS komplain
                                                                        LEFT JOIN inv_nonppn nonppn ON komplain.id_inv = nonppn.id_inv_nonppn
                                                                        LEFT JOIN inv_ppn ppn ON komplain.id_inv = ppn.id_inv_ppn
                                                                        LEFT JOIN inv_bum bum ON komplain.id_inv = bum.id_inv_bum
                                                                        LEFT JOIN spk_reg spk_nonppn ON komplain.id_inv = spk_nonppn.id_inv 
                                                                        LEFT JOIN spk_reg spk_ppn  ON komplain.id_inv = spk_ppn.id_inv 
                                                                        LEFT JOIN spk_reg spk_bum  ON komplain.id_inv = spk_bum.id_inv
                                                                        WHERE COALESCE(spk_nonppn.id_customer, spk_ppn.id_customer, spk_bum.id_customer) = '$id_cs'
                                                                        GROUP BY komplain.id_komplain");
                                    $total_komplain_cs = mysqli_num_rows($sql_total_komplain);
                                ?>
                                <p><b>Total Komplain</b></p>
                                <p><?php echo $total_komplain_cs; ?></p>
                            </div>
                        </div>
                        <div class="col-sm p-2">
                            <div class="text-center border p-2">
                                <p><b>Nominal Transaksi</b></p>
                                <?php 
                                    $total_nominal_trx = 0;
                                    $trx_blm_bayar = 0; 
                                    while($data_nominal = mysqli_fetch_array($query_nominal)){
                                        $total_nominal_trx +=  $data_nominal['total_inv'];
                                        $total_nominal_bayar += $data_nominal['total_bayar'];
                                        $trx_blm_bayar = $total_nominal_trx - $total_nominal_bayar;
                                ?>
                                <?php } ?>
                                <p><?php echo number_format($total_nominal_trx,0,'.','.') ?></p>
                            </div>
                        </div>
                        <div class="col-sm p-2">
                            <div class="text-center border p-2">
                                <p><b>Nominal Belum Bayar</b></p>
                                <p><?php echo number_format($trx_blm_bayar,0,'.','.') ?></p>
                            </div>
                        </div>
                        <div class="col-sm p-2">
                            <div class="text-center border p-2">
                                <p><b>Nominal Jatuh Tempo</b></p>
                                <?php 
                                    $sql_trx_tempo = "  SELECT 
                                                            COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                                            STR_TO_DATE(COALESCE(NULLIF(nonppn.tgl_inv, ''), NULLIF(ppn.tgl_inv, ''), NULLIF(bum.tgl_inv, '')), '%d/%m/%Y') AS tgl_inv,
                                                            STR_TO_DATE(COALESCE(NULLIF(nonppn.tgl_tempo, ''), NULLIF(ppn.tgl_tempo, ''), NULLIF(bum.tgl_tempo, '')), '%d/%m/%Y') AS tgl_tempo,
                                                            fnc.total_inv AS total_inv,
                                                            SUM(COALESCE(fb.total_bayar, 0)) AS total_bayar
                                                        FROM finance AS fnc
                                                        LEFT JOIN inv_nonppn nonppn ON (fnc.id_inv = nonppn.id_inv_nonppn)
                                                        LEFT JOIN inv_ppn ppn ON (fnc.id_inv = ppn.id_inv_ppn)
                                                        LEFT JOIN inv_bum bum ON (fnc.id_inv = bum.id_inv_bum)
                                                        LEFT JOIN spk_reg ON (spk_reg.id_inv = fnc.id_inv)
                                                        LEFT JOIN finance_bayar fb ON (fnc.id_finance = fb.id_finance)
                                                        WHERE spk_reg.id_customer = '$id_cs' AND $sort_data
                                                        AND (
                                                            fnc.total_inv != COALESCE(fb.total_bayar, 0) OR
                                                            fb.total_bayar IS NULL
                                                        )
                                                        AND ( 
                                                            COALESCE(NULLIF(nonppn.tgl_tempo, ''), NULLIF(ppn.tgl_tempo, ''), NULLIF(bum.tgl_tempo, '')) IS NOT NULL 
                                                            AND STR_TO_DATE(COALESCE(NULLIF(nonppn.tgl_tempo, ''), NULLIF(ppn.tgl_tempo, ''), NULLIF(bum.tgl_tempo, '')), '%d/%m/%Y') <= CURDATE()
                                                        )
                                                        GROUP BY COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv)";

                                    $query_trx_tempo = mysqli_query($connect, $sql_trx_tempo);
                                    $total_data_trx = mysqli_num_rows($query_trx_tempo);

                                    // Inisialisasi variabel di luar loop
                                    $total_trx_inv = 0;
                                    $total_trx_bayar = 0;

                                    if ($total_data_trx != 0) {
                                        while ($data_trx_tempo = mysqli_fetch_array($query_trx_tempo)) {
                                            $total_trx_inv += $data_trx_tempo['total_inv'];
                                            $total_trx_bayar += $data_trx_tempo['total_bayar'];
                                        }
                                        $sisa_trx_tempo = $total_trx_inv - $total_trx_bayar;
                                        echo '<p>' . number_format($sisa_trx_tempo,0,'.','.') . '</p>';
                                    } else {
                                        echo '<p>0</p>';
                                    }
                                ?> 
                            </div>
                        </div>
                        <div class="col-sm p-2">
                            <div class="text-center border p-2">
                                <p><b>Tagihan Belum Bayar</b></p>
                                <?php 
                                    $sql_tagihan_belum_bayar = "SELECT 
                                                                    COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                                                    STR_TO_DATE(COALESCE(NULLIF(nonppn.tgl_inv, ''), NULLIF(ppn.tgl_inv, ''), NULLIF(bum.tgl_inv, '')), '%d/%m/%Y') AS tgl_inv,
                                                                    STR_TO_DATE(COALESCE(NULLIF(nonppn.tgl_tempo, ''), NULLIF(ppn.tgl_tempo, ''), NULLIF(bum.tgl_tempo, '')), '%d/%m/%Y') AS tgl_tempo,
                                                                    fnc.total_inv AS total_inv,
                                                                    fnc.status_tagihan,
                                                                    fnc.status_lunas,
                                                                    SUM(COALESCE(fb.total_bayar, 0)) AS total_bayar
                                                                FROM finance AS fnc
                                                                LEFT JOIN inv_nonppn nonppn ON (fnc.id_inv = nonppn.id_inv_nonppn)
                                                                LEFT JOIN inv_ppn ppn ON (fnc.id_inv = ppn.id_inv_ppn)
                                                                LEFT JOIN inv_bum bum ON (fnc.id_inv = bum.id_inv_bum)
                                                                LEFT JOIN spk_reg ON (spk_reg.id_inv = fnc.id_inv)
                                                                LEFT JOIN finance_bayar fb ON (fnc.id_finance = fb.id_finance)
                                                                WHERE spk_reg.id_customer = '$id_cs' AND fnc.status_tagihan = '1' AND fnc.status_lunas = '0' AND $sort_data
                                                                GROUP BY COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv)";

                                    $query_tagihan_belum_bayar = mysqli_query($connect, $sql_tagihan_belum_bayar);
                                    $total_data_tagihan_belum_bayar = mysqli_num_rows($query_tagihan_belum_bayar);

                                    // Inisialisasi variabel di luar loop
                                    $total_tagihan_belum_bayar = 0;
                                    $total_bayar = 0;
                                    $total_inv = 0;

                                    if ($total_data_tagihan_belum_bayar != 0) {
                                        while ($data_tagihan = mysqli_fetch_array($query_tagihan_belum_bayar)) {
                                            $total_bayar += $data_tagihan['total_bayar'];
                                            $total_inv += $data_tagihan['total_inv'];
                                            $total_tagihan_belum_bayar = $total_inv - $total_bayar;
                                            
                                        }
                                        echo '<p>' . number_format($total_tagihan_belum_bayar,0,'.','.') . '</p>';
                                    } else {
                                        echo '<p>0</p>';
                                    }
                                ?> 
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-start flex-wrap">
                        <div class="card mb-3 me-2 mt-2 transparent-card">
                            <a href="finance-customer.php?sort_data=bulan_ini" class="btn btn-warning btn-detail mb-2">
                                <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                            </a>
                        </div>
                    </div>
                     <!-- Custom botton export and search -->
                    <div class="d-flex justify-content-between flex-wrap">
                        <div class="card mb-3 me-2 mt-2 transparent-card">
                            <div class="btn-group" role="group" aria-label="Second group" id="export-file">
                            <button type="button" class="btn btn-secondary" id="btnPrint" title="Cetak">
                                <i class="bi bi-printer"></i>
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnExcel" title="Export to Excel">
                                <i class="bi bi-file-earmark-excel"></i>
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnCsv" title="Export to CSV">
                                <i class="bi bi-filetype-csv"></i>
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnPdf" title="Export to PDF">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </button>
                            </div>
                        </div>
                        <div class="card mb-3 me-2 transparent-card">
                            <div class="input-container">
                                <input type="text" class="form-control" placeholder="Cari Data" id="cari-data">
                                <button type="button" class="text-secondary" id="resetButton">
                                    <i class="bi bi-x fs-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <!-- End Custom botton export and search -->
                        <table id="tableExportNew" class="table table-striped nowrap" style="width:100%">
                            <thead>
                                <tr class="text-white" style="background-color: navy;">
                                    <td class="text-center p-3 text-nowrap">No</td>
                                    <td class="text-center p-3 text-nowrap">No. Invoice</td>
                                    <td class="text-center p-3 text-nowrap">Tgl. Invoice</td>
                                    <td class="text-center p-3 text-nowrap">Tgl. Tempo</td>
                                    <td class="text-center p-3 text-nowrap">Jenis Inv</td>
                                    <td class="text-center p-3 text-nowrap">Nominal Transaksi</td>
                                    <td class="text-center p-3 text-nowrap">Status Invoice</td>
                                    <td class="text-center p-3 text-nowrap">Jenis Pengiriman</td>
                                    <td class="text-center p-3 text-nowrap">Status Pembayaran</td>
                                    <td class="text-center p-3 text-nowrap">Sudah Bayar</td>
                                    <td class="text-center p-3 text-nowrap">Status Pelunasan</td>
                                    <td class="text-center p-3 text-nowrap">Status Tempo</td>
                                    <td class="text-center p-3 text-nowrap">Status Tagihan</td>
                                    <td class="text-center p-3 text-nowrap">Aksi</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php              
                                    while ($data = mysqli_fetch_array($query)) {
                                        $id_finance = $data['id_finance'];
                                        $id_inv = $data['id_inv'];
                                        $id_inv_substr = substr($id_inv, 0, 3); // Mengambil 3 karakter pertama
                                        $no_inv = $data['no_inv'];
                                        $total_inv = $data['total_inv'];
                                        $total_bayar = $data['total_bayar'];  
                                        $status_lunas = $total_inv - $total_bayar;
                                        $cs_inv = $data['cs_inv'];
                                        $tgl_inv = $data['tgl_inv'];
                                        $tgl_tempo_cek = $data['tgl_tempo'];
                                        $tgl_tempo = $data['tgl_tempo_convert'];
                                        $status_lunas = $data['status_lunas'];
                                        $date_now = date('Y-m-d');
                                        $status_trx = $data['status_trx'];
                                ?>
                                <?php  
                                    // Jalankan query revisi
                                    $sql_rev =  $connect->query("SELECT no_inv_revisi AS no_inv_rev FROM inv_revisi 
                                    WHERE id_inv = '$id_inv' GROUP BY no_inv_revisi");

                                    // Hitung total data yang didapat
                                    $total_data_rev =  mysqli_num_rows($sql_rev);
                                ?>
                                <tr>
                                    <td class="text-center text-nowrap"><?php echo $no ?></td>
                                    <td class="text-nowrap text-center">
                                    <?php  
                                        // Jika tidak ada data, tampilkan "Revisi"
                                        if($total_data_rev == 0){
                                            echo $no_inv; 
                                        } else {
                                            // Jika ada data, tampilkan nomor invoice yang relevan
                                            while($data_rev = mysqli_fetch_array($sql_rev)){
                                                echo $data_rev['no_inv_rev']; // Pastikan variabel yang di-echo adalah benar
                                            }
                                        }
                                    ?>
                                    </td>
                                    <td class="text-nowrap text-center"><?php echo date('d/m/Y', strtotime($tgl_inv)) ?></td>
                                    <td class="text-nowrap text-center">
                                        <?php 
                                            if(!empty($tgl_tempo_cek)){
                                                echo date('d/m/Y', strtotime($tgl_tempo));
                                            } else {
                                                echo "Tidak Ada Tempo";
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?php  
                                            if($id_inv_substr == 'NON'){
                                                echo 'Non PPN';
                                            } else if($id_inv_substr == 'PPN'){
                                                echo 'PPN';
                                            } else if($id_inv_substr == 'BUM'){
                                                echo 'BUM';
                                            }
                                        ?>
                                    </td>
                                    <td class="text-nowrap text-end"><?php echo number_format($total_inv,0,'.','.') ?></td> 
                                    <td class="text-nowrap text-center"><?php echo $status_trx ?></td> 
                                    <td class="text-nowrap text-center">
                                        <?php
                                            // Jika status kirim tidak ada data, tampilkan status "Revisi"
                                            if($total_data_rev == 0 ){
                                                $sql_status_kirim = $connect->query("SELECT 
                                                                                        sk.id_inv, 
                                                                                        sk.jenis_pengiriman,
                                                                                        us.nama_user AS nama_driver,
                                                                                        eks.nama_ekspedisi,
                                                                                        ip.nama_penerima
                                                                                    FROM status_kirim AS sk
                                                                                    LEFT JOIN $database2.user us ON sk.dikirim_driver = us.id_user
                                                                                    LEFT JOIN ekspedisi eks ON sk.dikirim_ekspedisi = eks.id_ekspedisi
                                                                                    LEFT JOIN inv_penerima ip ON (sk.id_inv = ip.id_inv)
                                                                                    WHERE sk.id_inv = '$id_inv' 
                                                                                    GROUP BY sk.id_inv");
                                                $data_status_kirim = mysqli_fetch_array($sql_status_kirim);
                                                if($status_trx == 'Belum Dikirim'){
                                                    echo $status_trx;
                                                } else {
                                                    $jenis_pengiriman =  $data_status_kirim['jenis_pengiriman'];
                                                    $nama_pengiriman = [
                                                        'Driver' => $data_status_kirim['nama_driver'],
                                                        'Ekspedisi' => $data_status_kirim['nama_ekspedisi'],
                                                        'Diambil Langsung' => $data_status_kirim['nama_penerima']
                                                    ][$jenis_pengiriman];
                                                    echo $jenis_pengiriman . '<br>';
                                                    echo "(" .$nama_pengiriman . ")";
                                                }
                                            } else {
                                                $sql_komplain = $connect->query("SELECT id_komplain FROM inv_komplain WHERE id_inv = '$id_inv'");
                                                $data_komplain = mysqli_fetch_array($sql_komplain);
                                                $id_komplain = $data_komplain['id_komplain'];
                                                $sql_status_kirim = $connect->query("SELECT     
                                                                                        rsk.id_komplain, 
                                                                                        rsk.jenis_pengiriman,
                                                                                        us.nama_user AS nama_driver,
                                                                                        eks.nama_ekspedisi,
                                                                                        ip.nama_penerima
                                                                                    FROM revisi_status_kirim AS rsk
                                                                                    LEFT JOIN $database2.user us ON rsk.dikirim_driver = us.id_user
                                                                                    LEFT JOIN ekspedisi eks ON rsk.dikirim_ekspedisi = eks.id_ekspedisi
                                                                                    LEFT JOIN inv_penerima_revisi ip ON (rsk.id_komplain = ip.id_komplain)
                                                                                    WHERE rsk.id_komplain = '$id_komplain' 
                                                                                    GROUP BY rsk.id_komplain");
                                                $data_status_kirim = mysqli_fetch_array($sql_status_kirim);
                                                if(empty($data_status_kirim['jenis_pengiriman'])){
                                                    echo "Tidak memilih jenis pengiriman";
                                                } else {
                                                    $jenis_pengiriman = $data_status_kirim['jenis_pengiriman'];
                                                    $nama_pengiriman = [
                                                        'Driver' => $data_status_kirim['nama_driver'],
                                                        'Ekspedisi' => $data_status_kirim['nama_ekspedisi'],
                                                        'Diambil Langsung' => $data_status_kirim['nama_penerima']
                                                    ][$jenis_pengiriman];
                                                    echo $jenis_pengiriman . '<br>';
                                                    echo "(" .$nama_pengiriman . ")";
                                                }
                                            }
                                        ?>
                                    </td>    
                                    <td class="text-nowrap text-center">
                                        <?php
                                        if($data['status_pembayaran'] == 1){
                                            echo "Sudah Bayar";
                                        }else{
                                            echo "Belum Bayar";
                                        }
                                        ?>
                                    </td>  
                                    <td class="text-nowrap text-end"><?php echo number_format($total_bayar,0,'.','.') ?></td>     
                                    <td class="text-nowrap text-center">
                                        <?php
                                            if($status_lunas == 1){
                                                echo "Lunas";
                                            }else{

                                                echo "Belum Lunas";
                                            }
                                        ?>
                                    </td>    
                                    <?php  
                                        if (!empty($tgl_tempo_cek) && $status_lunas == '0') {
                                        $timestamp_tgl_tempo = strtotime($tgl_tempo);
                                        $timestamp_now = strtotime($date_now);
                                        // Hitung selisih timestamp
                                        $selisih_timestamp = $timestamp_tgl_tempo - $timestamp_now;
                                        // Konversi selisih timestamp ke dalam hari
                                        $selisih_hari = floor($selisih_timestamp / (60 * 60 * 24));
                                        if ($tgl_tempo > $date_now){
                                            echo '<td class="text-end text-nowrap bg-secondary text-white">'. "Tempo < " .$selisih_hari. " Hari".'</td>';
                                        } else if ($tgl_tempo < $date_now){
                                            echo '<td class="text-end text-nowrap bg-danger text-white">'. "Tempo > " . abs($selisih_hari). " Hari".'</td>';
                                        } else if ($tgl_tempo == $date_now) {
                                            echo '<td class="text-end text-nowrap">Jatuh Tempo Hari ini</td>';
                                        } else {
                                            echo '<td class="text-end text-nowrap">Tidak Ada Tempo</td>';
                                        }
                                        } else {
                                            if ($status_lunas == '1'){
                                            echo '<td class="text-center text-nowrap">Sudah Lunas</td>';
                                            } else {
                                            echo '<td class="text-center text-nowrap">Tidak Ada Tempo</td>';
                                            }
                                        }
                                    ?> 
                                    <td class="text-center">
                                        <?php
                                        if (isset($data['status_tagihan']) && $data['status_tagihan'] == 0) {
                                            echo "Belum Dibuat";
                                        } else {
                                            echo (isset($data['no_tagihan']) ? ' ' . $data['no_tagihan'] : '');
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?php
                                            $cek_komplain = $connect->query("SELECT id_komplain FROM inv_komplain WHERE id_inv = '$id_inv'");
                                            $total_data_komplain = mysqli_num_rows($cek_komplain);
                                            while($data_komplain = mysqli_fetch_array($cek_komplain)){
                                                $id_komplain = $data_komplain['id_komplain'];
                                            ?>
                                            <?php } ?>
                                            <?php
                                            if ($id_inv_substr == 'NON') {
                                                if ($total_data_komplain != 0) {
                                                    echo '<a href="detail-produk-nonppn-revisi.php?id=' . base64_encode($id_komplain) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                                } else {
                                                    echo '<a href="detail-produk-inv-nonppn.php?id=' . base64_encode($id_inv) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                                }
                                            } elseif ($id_inv_substr == 'PPN') {
                                                if ($total_data_komplain != 0) {
                                                    echo '<a href="detail-produk-ppn-revisi.php?id=' . base64_encode($id_komplain) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                                } else {
                                                    echo '<a href="detail-produk-inv-ppn.php?id=' . base64_encode($id_inv) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                                }
                                            } elseif ($id_inv_substr == 'BUM') {
                                                if ($total_data_komplain != 0) {
                                                    echo '<a href="detail-produk-bum-revisi.php?id=' . base64_encode($id_komplain) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                                } else {
                                                    echo '<a href="detail-produk-inv-bum.php?id=' . base64_encode($id_inv) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                                }
                                            }
                                        ?>
                                        <?php  
                                            if (isset($data['status_tagihan']) && $data['status_tagihan'] == 0) {
                                            } else {
                                               ?>
                                                    <button class="btn btn-secondary btn-sm view_data" data-bs-toggle="modal" data-bs-target="#history" data-id="<?php echo encrypt($id_finance, $key_global) ?>" title="History Payment">
                                                        <i class="bi bi-receipt-cutoff"></i>
                                                    </button>
                                               <?php
                                            }
                                        
                                        ?>
                                    </td>
                                </tr>
                            <?php $no++ ?>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <!-- End Table Responsive -->
                <div class="text-start" id="totalData"></div>
                <!-- Custom pagination -->
                <nav>
                    <ul class="pagination" id="customPagination">
                    <!-- Pagination items will be inserted here by JavaScript -->
                    </ul>
                </nav>
                </div>
            </div>
        </section>
    </main><!-- End #main -->
    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
    <?php include "page/js-filter-detail-cs.php"  ?>
    <script src="../assets/js/datatable-custom-button.js"></script>
</body>
</html>
<!-- Modal utama History -->
<div class="modal fade" id="history" tabindex="-1" aria-labelledby="historyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyLabel">History Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body" id="detail_id">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- End Modal utama History -->

<!-- Modal gambar History -->
<div class="modal fade" id="bukti" data-bs-keyboard="false" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Bukti Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detail_bukti">
            </div>
        </div>
    </div>
</div>
<!-- End Modal gambar History -->
<!-- Script Untuk Modal History -->
<script>
    $('#imageModal').on('show.bs.modal', function () {
        $('#history').modal('hide'); // Sembunyikan modal utama saat modal gambar ditampilkan
    });
    
    $('#bukti').on('hidden.bs.modal', function () {
        $('#history').modal('show'); // Tampilkan kembali modal utama saat modal gambar disembunyikan
    });
</script>
<!-- ============================================= -->
<!-- Untuk menampilkan data Histori pada modal -->
<script>
    $(document).ready(function(){
        // Pastikan event delegasi diterapkan pada elemen dengan kelas 'view_data' yang ada di dalam tabel
        $(document).on('click', '.view_data', function(){
            var data_id = $(this).data("id");

            $.ajax({
                url: "convert-json-modal-history.php",
                method: "POST",
                data: {data_id: data_id},
                success: function(data){
                    $("#detail_id").html(data);
                    $("#history").modal('show');
                }
            });
        });
    });
</script>

<!--End Script Untuk Modal History -->

<script>
    function goBack() {
        window.history.back();
    }
</script> 

<!-- Filter date range -->
<script>
    $(document).ready(function () {
        var sortData = "<?php echo $get_sort ?>";
        // console.log(sortData);
        if (sortData == 'hari_ini'){
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Hari Ini');
            $("#hari_ini").addClass("active");
            $("#hari_ini").prop('disabled', true);
        } else if (sortData == 'minggu_ini'){
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Minggu Ini');
            $("#minggu_ini").addClass("active");
            $("#minggu_ini").prop('disabled', true);
        } else if (sortData == 'bulan_ini'){
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Bulan Ini');
            $("#bulan_ini").addClass("active");
            $("#bulan_ini").prop('disabled', true);
        } else if (sortData == 'bulan_kemarin'){
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Bulan Kemarin');
            $("#bulan_kemarin").addClass("active");
            $("#bulan_kemarin").prop('disabled', true);
        } else if (sortData == 'tahun_ini'){
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Tahun Ini');
            $("#tahun_ini").addClass("active");
            $("#tahun_ini").prop('disabled', true);
        } else if (sortData == 'tahun_kemarin'){
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Tahun Kemarin');
            $("#tahun_kemarin").addClass("active");
            $("#tahun_kemarin").prop('disabled', true);
        } else if (sortData == 'date_range'){
            // Function to update dropdown text with selected date range
            var textDate = "<?php echo $text_date_dropdown ?>";
            $("#dropdownButton").text(textDate);
            
        }
    });
</script>

<script>
    $(document).ready(function() {
        $("#hari_ini").click(function() {
            var url = "<?php echo $url; ?>&sort_data=hari_ini"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#minggu_ini").click(function() {
            var url = "<?php echo $url; ?>&sort_data=minggu_ini"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#bulan_ini").click(function() {
            var url = "<?php echo $url; ?>&sort_data=bulan_ini"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#bulan_kemarin").click(function() {
            var url = "<?php echo $url; ?>&sort_data=bulan_kemarin"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#tahun_ini").click(function() {
            var url = "<?php echo $url; ?>&sort_data=tahun_ini"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#tahun_kemarin").click(function() {
            var url = "<?php echo $url; ?>&sort_data=tahun_kemarin"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#reset").click(function() {
            var url = "<?php echo $url; ?>&sort_data=tahun_ini"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });   

    // Filter untuk date range
    $(document).ready(function () {
        // Initialize flatpickr date range picker with custom date format and daterange mode
        var dateRangePicker = flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "d/m/Y",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 1) {
                    var startDate = selectedDates[0];
                    var maxEndDate = new Date(startDate.getTime() + 31 * 24 * 60 * 60 * 1000); // Tanggal maksimal 90 hari setelah tanggal mulai

                    instance.set('minDate', startDate); // Setel tanggal minimal ke tanggal mulai
                    instance.set('maxDate', maxEndDate); // Setel tanggal maksimal ke 30 hari setelah tanggal mulai
                } else {
                    // Reset batas tanggal setelah memilih rentang tanggal
                    instance.set('minDate', null);
                    instance.set('maxDate', null);
                    redirectToUrl(); // Redirect ke URL setelah memilih rentang tanggal
                }
            }
        });

        // Function to check if a string is a valid date
        function isValidDate(dateString) {
            // Parse the date string and check if it's a valid date
            var date = new Date(dateString);
            return !isNaN(date.getTime());
        }

        // Function to redirect to URL with selected date range
        function redirectToUrl() {
            var selectedDates = dateRangePicker.selectedDates;
            if (selectedDates.length === 2) {
                var formattedStartDate = selectedDates[0].toLocaleDateString('en-GB');
                var formattedEndDate = selectedDates[1].toLocaleDateString('en-GB');

                var key = "<?php echo $key ?>";

                // Melakukan request AJAX untuk memanggil file PHP
                $.ajax({
                    url: '../ajax/function-enkripsi.php',
                    type: 'POST',
                    data: {
                        formattedStartDate: formattedStartDate,
                        formattedEndDate: formattedEndDate,
                        key: key
                    },
                    success: function(response) {
                        // response sudah berupa objek JavaScript jika tipe konten respons adalah JSON
                        var encryptedStartDate = response.startDate;
                        var encryptedEndDate = response.endDate;

                        var url = "<?php echo $url; ?>&sort_data=date_range&start_date=" + encodeURIComponent(encryptedStartDate) + "&end_date=" + encodeURIComponent(encryptedEndDate);
                        window.location.href = url;
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error(error);
                    }
                });
            }
        }

    });
</script>









