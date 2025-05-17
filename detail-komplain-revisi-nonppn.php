<?php
    $page  = 'transaksi';
    $page2  = 'list-cmp';
    require_once "akses.php";
    require_once 'function/class-komplain.php';
    require_once "function/function-enkripsi.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link rel="stylesheet" href="assets/css/wrap-text.css">
    <link rel="stylesheet" href="assets/css/button-file-upload.css">
    <link href="assets/vendor/lightbox/dist/css/lightgallery.css" rel="stylesheet" />

    <?php include "page/head.php"; ?>
    <?php include "page/style-button-filterdate.php"; ?>

    <style>
        .label-mobile {
            display: none;
        }

        .disable-click {
            pointer-events: none;
        }

        .disable-scroll {
            overflow: hidden;
        }

        @media (max-width: 767px) {

            body {
                font-size: 14px;
            }

            .mobile {
                display: none;
            }

            .mobile-text {
                text-align: left !important;
            }

            /* Tambahkan aturan CSS khusus untuk tampilan mobile di bawah 767px */
            .col-12.col-md-2 {
                /* Contoh: Mengatur tinggi elemen select pada tampilan mobile */
                height: 50px;
            }

            .card-mobile {
                display: none;
            }

            .label-mobile {
                display: block;
            }

        }

        .btn.active {
            background-color: black;
            color: white;
            border-color: 1px solid white;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Menghilangkan garis pada input */
        input {
            border: none;
            outline: none;
            background: none;
            width: 100%;
        }

        @media only screen and (max-width: 500px) {
            body {
                font-size: 14px;
            }

            .mobile {
                display: none;
            }

            .mobile-text {
                text-align: left !important;
            }
        }
    </style>
</head>

<body id="scroll">
    <!-- nav header -->
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include "page/sidebar.php"; ?>
    <!-- end sidebar -->


    <main id="main" class="main">
        <!-- Loading -->
        <!-- <div class="loader loader">
            <div class="loading">
                <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div> -->
        <!-- ENd Loading -->
        <section>
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <?php  
                $id = decrypt($_GET['id'], $key_spk);
                include "query/detail-komplain-nonppn.php";
                $id_inv = $data_kondisi['id_inv'];
                $no_inv = $data_detail['no_inv'];
                $alamat = $data_detail['alamat'];
                $nama_driver = $data_driver_rev['nama_driver'] ?? '';
                $nama_driver = str_replace(' ', '_', $nama_driver);
                include "query/produk-komplain-tmp.php";
                $cek_status_cancel = $connect->query("SELECT id_inv_nonppn, status_transaksi FROM inv_nonppn WHERE id_inv_nonppn = '$id_inv'");
                $data_status_cancel = mysqli_fetch_array($cek_status_cancel);
                $status_transaksi = $data_status_cancel['status_transaksi'];

                $id_inv_substr = $id_inv;
                $inv_id = substr($id_inv_substr, 0, 3);
                $jenis_inv = "";
                if ($inv_id == "NON"){
                    $jenis_inv = "nonppn";
                } else if ($inv_id == "PPN"){
                    $jenis_inv = "ppn";
                } else if ($inv_id == "BUM"){
                    $jenis_inv = "bum";
                }

                // query untuk cek no invoice
                $cek_no_inv = mysqli_query($connect,"   SELECT 
                                                            nonppn.id_inv_nonppn AS id_inv,
                                                            max(rev.no_inv_revisi) AS no_inv_revisi
                                                        FROM inv_revisi AS rev
                                                        LEFT JOIN inv_komplain ik ON rev.id_inv = ik.id_inv
                                                        LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
                                                        WHERE '$id_inv' IN (nonppn.id_inv_nonppn) GROUP BY id_inv
                                            ");
                $total_row_rev = mysqli_num_rows($cek_no_inv);
                $data_inv_rev = mysqli_fetch_array($cek_no_inv);
                $no_inv_fix = '';
                if($total_row_rev == 0){
                    $no_inv_fix = $no_inv;
                } else {
                    $no_inv_fix = $data_inv_rev['no_inv_revisi'];
                }
            ?>
            <div class="card p-2">
                <div class="row mb-2">
                    <!-- Kolom No Komplain (di atas) -->
                    <div class="col-md-3">
                        <button class="btn btn-secondary">No Komplain : <?php echo $data_detail['no_komplain'] ?></button>
                    </div>
                    <!-- Kolom Open (di tengah) -->
                    <div class="col-md-6 text-center">
                        <p><b>Detail Invoice Revisi</b></p>
                    </div>
                    <!-- Kolom Details (paling bawah) -->
                    <div class="col-md-3 text-end">
                        <button class="btn btn-secondary">
                            <?php 
                                if($data_detail['status_komplain'] == 0){
                                    echo "Open";
                                } else {
                                    echo "Selesai";
                                }
                            ?>
                        </button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="border p-3">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Tgl. Pesanan</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['tgl_pesanan'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">No. SPK</td>
                                        <td class="text-nowrap">
                                            : <?php 
                                                    $no = 1;
                                                    $total_rows = mysqli_num_rows($query_detail2); // Menghitung total baris data
                                                    while ($data_detail2 = mysqli_fetch_array($query_detail2)) {
                                                        $no_spk = $data_detail2['no_spk'];
                                                        $tgl_pesanan = $data_detail2['tgl_pesanan'];
                                                        $no_po = $data_detail2['no_po'];
                                                        
                                                        // Mengecek apakah ini adalah baris kedua atau lebih
                                                        if ($no > 1) {
                                                            echo "<br>"; // Menambahkan baris baru setelah baris pertama
                                                        }
                                                        
                                                        echo $no . ". (" . $tgl_pesanan . ")";
                                                        
                                                        // Menampilkan nomor PO jika tersedia
                                                        if (!empty($no_po)) {
                                                            echo " / (" . $no_po . ")";
                                                        }
                                                        
                                                        echo " / (" . $no_spk . ")";
                                                        
                                                        $no++;
                                                    }
                                                ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">No. Invoice</td>
                                        <td class="text-nowrap">: <?php echo $no_inv_fix ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Tgl.Invoice</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['tgl_inv'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Jenis Invoice</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['kategori_inv'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Order Via</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['order_by'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Sales</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['nama_sales'] ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border p-3">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <?php  
                                        $cek_inv_revisi = $connect->query("SELECT pelanggan_revisi, alamat_revisi FROM inv_revisi WHERE id_inv = '$id_inv' ORDER BY created_date DESC");
                                        $total_data_inv_rev = mysqli_num_rows($cek_inv_revisi);
                                        $data_cek_inv_rev = mysqli_fetch_array($cek_inv_revisi);
                                        
                                        $pelanggan = '';
                                        $alamat = '';
                                        if ($total_data_inv_rev > 0){
                                            $pelanggan = $data_cek_inv_rev['pelanggan_revisi'];
                                            $alamat = $data_cek_inv_rev['alamat_revisi'];
                                        } else {
                                            if($data_detail['alamat_inv'] == ''){
                                                $pelanggan = $data_detail['cs_inv'];
                                                $alamat = $data_detail['alamat'];
                                            } else {
                                                $pelanggan = $data_detail['cs_inv'];
                                                $alamat = $data_detail['alamat_inv'];  
                                            }
                                            
                                        }
                                    ?>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Pelanggan</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['nama_cs'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Pelanggan Invoice</td>
                                        <td class="text-nowrap">: <?php echo $pelanggan ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Alamat</td>
                                        <td class="wrap-text">: <?php echo $alamat ?></td>
                                    </tr>
                                    <?php 
                                        if($total_driver_rev != 0 && $data_driver_rev['jenis_pengiriman'] == 'Ekspedisi'){
                                            ?>
                                                <tr>
                                                    <td class="col-md-6 text-nowrap">Ongkos Kirim</td>
                                                    <td class="text-nowrap">: <?php 
                                                            if($data_driver_rev['free_ongkir'] == 1){
                                                                echo "0 (Free Ongkir)";
                                                            } else  {
                                                                if($data_driver_rev['jenis_ongkir'] == 1){
                                                                    echo number_format($data_driver_rev['ongkir']) . " (COD)";
                                                                } else {
                                                                    echo number_format($data_driver_rev['ongkir']);
                                                                }
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php
                                        }
                                    ?>
                                    <?php  
                                        if($total_driver_rev != 0){
                                            ?>
                                                <tr>
                                                    <td class="col-md-6 text-nowrap">Jenis Pengiriman</td>
                                                    <td class="text-nowrap">
                                                        : <?php  
                                                                if($data_driver_rev['jenis_pengiriman'] == 'Driver'){
                                                                    ?>
                                                                        <?php echo $data_driver_rev['jenis_pengiriman']?>
                                                                        (<?php echo $data_driver_rev['nama_driver'] ?>)
                                                                    <?php
                                                                } else if($data_driver_rev['jenis_pengiriman'] == 'Ekspedisi'){
                                                                    ?>
                                                                        <?php echo $data_driver_rev['jenis_pengiriman']?>
                                                                        <?php
                                                                } else {
                                                                    ?>
                                                                        <?php echo $data_driver_rev['jenis_pengiriman']?>
                                                                        (<?php echo $data_driver_rev['diambil_oleh'] ?>)
                                                                    <?php
                                                                }
                                                            ?>
                                                    </td>
                                                </tr>
                                                <?php  
                                                    if(!empty($data_driver_rev['jenis_pengiriman'] && $data_driver_rev['jenis_penerima'])){
                                                        ?>
                                                            <tr>
                                                                <td class="col-md-6 text-nowrap">Diterima Oleh</td>
                                                                <td class="text-nowrap">
                                                                    : <?php 
                                                                            if($data_driver_rev['jenis_penerima'] == 'Customer'){
                                                                                ?>
                                                                                    <?php echo $data_driver_rev['jenis_penerima'] ?>
                                                                                    <?php
                                                                            } else {
                                                                                ?>
                                                                                    <?php echo $data_driver_rev['nama_ekspedisi'] ?>
                                                                                <?php
                                                                            }
                                                                    
                                                                        ?>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                    }          
                                                ?>
                                                <?php  
                                                    if(!empty($data_driver_rev['jenis_pengiriman'] && $data_driver_rev['jenis_penerima'])){
                                                        if($data_driver_rev['jenis_penerima'] == 'Customer'){
                                                        } else {
                                                            ?>
                                                                <tr>
                                                                    <td class="col-md-6 text-nowrap">No. Resi</td>
                                                                    <td class="text-nowrap">
                                                                        : <?php echo $data_driver_rev['no_resi'] ?>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                            
                                                        <?php
                                                    }          
                                                ?>
                                                <?php   
                                                    if(!empty($data_driver_rev['nama_penerima'])){
                                                        ?>
                                                            <tr>
                                                                <td class="col-md-6 text-nowrap">Nama Penerima (CS)</td>
                                                                <td class="text-nowrap">: <?php echo $data_driver_rev['nama_penerima'] ?></td>
                                                            </tr>
                                                        <?php
                                                    }
                                                ?>
                                                <?php  
                                                    if(!empty($data_driver_rev['dikirim_oleh']) && !empty($data_driver_rev['penanggung_jawab'])){
                                                        ?>
                                                            <tr>
                                                                <td class="col-md-6 text-nowrap">Dikirim Oleh</td>
                                                                <td class="text-nowrap">: <?php echo $data_driver_rev['dikirim_oleh'] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="col-md-6 text-nowrap">PJ. Paket Kirim</td>
                                                                <td class="text-nowrap">: <?php echo $data_driver_rev['penanggung_jawab'] ?>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                    }
                                                            
                                                ?>
                                            <?php  
                                        } 
                                    ?>
                                    <?php  
                                        if ($data_kondisi['catatan'] != ""){
                                            ?>
                                                <tr>
                                                    <td class="col-md-6 text-nowrap">Catatan</td>
                                                    <td class="text-nowrap">: <?php echo $data_kondisi['catatan'] ?></td>
                                                </tr>
                                            <?php 
                                        } 
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- kode untuk status TRX Dikirim atau Selesai -->
                <?php 
                    $id_kmpl = $id;
                    $sql_kmpl = mysqli_query($connect, "SELECT status_komplain FROM inv_komplain WHERE id_komplain = '$id_kmpl'");
                    $data_kmpl = mysqli_fetch_array($sql_kmpl);
                    $sql_rev = mysqli_query($connect, "SELECT id_inv, status_pengiriman, status_trx_komplain, status_trx_selesai, created_date FROM inv_revisi WHERE id_inv = '$id_inv' ORDER BY created_date DESC LIMIT 1");
                    $data_rev = mysqli_fetch_array($sql_rev);
                    $total_data_rev = mysqli_num_rows($sql_rev);
                    $status_kmpl = $data_kmpl['status_komplain'];

                    if ($total_data_rev != '0' && $status_kmpl == '0') {
                        $status_pengiriman = $data_rev['status_pengiriman'];
                        $status_trx_komplain = $data_rev['status_trx_komplain'];
                        $status_trx_selesai = $data_rev['status_trx_selesai'];
                        
                        if($status_pengiriman == "1" && $status_trx_komplain == "1" && $status_trx_selesai == "1") {
                            
                        } else if ($status_pengiriman == '1' && $status_trx_komplain == '0' && $status_trx_selesai == '0') {
                            ?>
                                <div class="text-center mb-3">
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#edit-details"><i class="bi bi-pencil"></i> Edit data detail</button>
                                </div>
                            <?php
                        } else if ($status_pengiriman == "1" && $status_trx_komplain == "1" && $status_trx_selesai == "0") {
                            ?>
                                <div class="text-center mb-3">
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#edit-details"><i class="bi bi-pencil"></i> Edit data detail</button>
                                </div>
                            <?php
                        } else if ($status_pengiriman == "0" && $status_trx_komplain == "0" && $status_trx_selesai == "0") {
                            ?>

                             <?php
                        } 
                    } else if ($total_data_rev == '0' && $status_kmpl == '0'){
                        ?>
                            <div class="text-center mb-3">
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#edit-details"><i class="bi bi-pencil"></i> Edit data detail</button>
                            </div>
                        <?php
                    }
                ?>
            </div>
            <style>
                /* CSS untuk layar kecil (misalnya, ukuran layar ponsel) */
                @media (max-width: 767px) {
                    .w-100 {
                        flex-wrap: wrap;
                        justify-content: center;
                    }

                    .p-2 {
                        flex: 1 1 calc(33.33% - 10px);
                        margin: 5px;
                        /* text-align: center; */
                    }

                    .text-end {
                        text-align: center;
                    }

                    .btn {
                        width: 100%;
                        white-space: nowrap;
                    }

                    .btn button {
                        display: block;
                        margin-top: 10px;
                    }

                }
            </style>
            <div class="card p-3">
                <div class="row">
                    <div class="col-md-7 mb-2">
                        <p class="bg-secondary text-center text-white p-2" style="border-radius: 5px;">
                            <?php echo $alasan_komplain = komplain::getKondisi($data_kondisi['kondisi_pesanan']); ?>
                        </p>
                    </div>
                    <div class="col-md-5 text-end">
                        <p class="btn btn-secondary">
                            <?php  
                                if($data_kondisi['kat_komplain'] == 0) {
                                    echo "Invoice";
                                } else {
                                    echo "Barang";
                                }
                            ?>
                        </p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <a href="invoice-komplain.php?date_range=year" class="btn btn-warning mb-3">
                                <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                            </a>
                            <?php  
                                $cek_bukti_terima = mysqli_query($connect, "SELECT id_komplain FROM inv_bukti_terima_revisi WHERE id_komplain = '$id'");
                                $total_data_bukti = mysqli_num_rows($cek_bukti_terima);
                                if($total_data_bukti != '0'){
                                    ?>
                                        <button class="btn btn-info mb-3" data-bs-toggle="modal" data-bs-target="#bukti">
                                            <i class="bi bi-image"></i> Bukti Terima Revisi
                                        </button>
                                    <?php
                                }
                            ?>
                            <?php
                                if($status_transaksi != 'Cancel Order'){
                                    // Cek apakah ada data yang ditemukan
                                    if (mysqli_num_rows($query_produk) > 0) {
                                    } else {
                                        // Tidak ada data yang ditemukan
                                        ?>
                                            <button class='btn btn-danger mb-3 cancel-order-btn' data-id='<?php echo encrypt($id_inv, $key_spk) ?>'><i class='bi bi-x-circle'></i> Cancel Order</button>
                                        <?php
                                    }
                                }   
                            ?>
                            <?php
                                if ($total_data_rev != '0' && $status_kmpl == '0') {
                                    $status_pengiriman = $data_rev['status_pengiriman'];
                                    $status_trx_komplain = $data_rev['status_trx_komplain'];
                                    $status_trx_selesai = $data_rev['status_trx_selesai'];
                                    
                                    if($status_pengiriman == "1" && $status_trx_komplain == "1" && $status_trx_selesai == "1") {
                                        
                                    } else if ($status_pengiriman == '1' && $status_trx_komplain == '0' && $status_trx_selesai == '0') {
                                        ?>
                                            <button href="#" class="btn btn-secondary mb-3" data-bs-toggle="modal"
                                                data-bs-target="#ubahStatus">
                                                <i class="bi bi-arrow-left-right"></i> Ubah Status
                                            </button>
                                        <?php
                                    } else if ($status_pengiriman == "1" && $status_trx_komplain == "1" && $status_trx_selesai == "0") {
                                        ?>
                                            <button href="#" class="btn btn-secondary mb-3" data-bs-toggle="modal"
                                                data-bs-target="#ubahStatus">
                                                <i class="bi bi-arrow-left-right"></i> Ubah Status
                                            </button>
                                        <?php
                                    } else if ($status_pengiriman == "0" && $status_trx_komplain == "0" && $status_trx_selesai == "0") {
                                        ?>
                                            <button href="#" class="btn btn-secondary mb-3" data-bs-toggle="modal"
                                                data-bs-target="#ubahStatus">
                                                <i class="bi bi-arrow-left-right"></i> Ubah Status
                                            </button>
                                        <?php
                                    } 
                                } else if ($total_data_rev == '0' && $status_kmpl == '0'){
                                    ?>
                                        <button href="#" class="btn btn-secondary mb-3" data-bs-toggle="modal"
                                            data-bs-target="#ubahStatus">
                                            <i class="bi bi-arrow-left-right"></i> Ubah Status
                                        </button>
                                    <?php
                                }
                            ?>
                            <?php  
                                $cek_jenis_pengiriman = mysqli_query($connect, "SELECT 
                                                                                        id_status_kirim_revisi, id_komplain, jenis_penerima, status_kirim, created_date
                                                                                    FROM revisi_status_kirim
                                                                                    WHERE id_komplain = '$id'
                                                                                    AND created_date = (SELECT MAX(created_date) FROM revisi_status_kirim WHERE id_komplain = '$id');
                                                                                    ");
                                $data_cek_jenis_pengiriman = mysqli_fetch_array($cek_jenis_pengiriman);
                                $id_status_kirim_revisi = $data_cek_jenis_pengiriman['id_status_kirim_revisi'] ?? null; // Menggunakan null coalescing operator
                                $total_cek_jenis_pengiriman = mysqli_num_rows($cek_jenis_pengiriman);

                                if($total_cek_jenis_pengiriman != 0){
                                    if($data_cek_jenis_pengiriman['jenis_penerima'] == 'Ekspedisi' && $data_cek_jenis_pengiriman['status_kirim'] == '0'){
                                        ?>
                                            <button class="btn btn-secondary mb-3" data-bs-toggle="modal"
                                            data-bs-target="#DiterimaEx">
                                            <i class="bi bi-send"></i>
                                            Diterima
                                            </button>

                                        <?php
                                    }
                                }
                            ?>
                            <?php  
                                $sql_ubah_pengiriman = $connect->query("SELECT id_komplain FROM revisi_status_kirim WHERE id_komplain = '$id'");
                                $total_data_pengiriman = mysqli_num_rows($sql_ubah_pengiriman);
                                if($total_data_pengiriman != 0 && $data_detail['status_komplain'] == 0) {
                                    ?>
                                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#ubahJenisPengiriman">
                                            <i class="bi bi-truck"></i>
                                            Ubah Jenis Pengiriman
                                        </button>

                                        <?php  
                                            if($data_driver_rev['jenis_pengiriman'] == 'Ekspedisi'){
                                                ?>
                                                    <button type="button" class="btn btn-warning mb-3" data-bs-toggle="modal" data-bs-target="#ubahResi" data-id = "<?php echo encrypt($id_status_kirim_revisi, $key_spk) ?>">
                                                        <i class="bi bi-truck"></i>
                                                        Edit Resi dan Ongkir
                                                    </button>
                                                <?php
                                            }
                                        ?>
                                    <?php
                                }                               
                            ?>
                        </div>
                        <div class="col-md-4">
                            <div class="text-end">
                                <?php  
                                    if($jenis_inv == 'ppn'){
                                        ?>
                                            <button class="btn border-dark">
                                                <?php  
                                                            $total_harga_revisi = 0;
                                                            while($data_total = mysqli_fetch_array($query_produk_total)){
                                                                $total_harga =  $data_total['harga'] * $data_total['qty'];
                                                                $discount = $data_total['disc'] / 100; // 50% diskon
                                                                $harga_final = $total_harga * (1 - $discount); // Harga akhir setelah diskon   
                                                                $total_harga_revisi += $harga_final;
                                                                } 
                                                                $grand_total_revisi = $total_harga_revisi * 1.11 + $data_detail['ongkir'];
                                                            ?>
                                                <b>Total Invoice Revisi</b><br>
                                                Rp. <?php echo number_format($grand_total_revisi); ?>
                                            </button>
                                            <?php
                                    } else {
                                        ?>
                                            <button class="btn border-dark">
                                                <?php  
                                                            $total_harga_revisi = 0;
                                                            while($data_total = mysqli_fetch_array($query_produk_total)){
                                                                $total_harga =  $data_total['harga'] * $data_total['qty'];
                                                                $discount = $data_total['disc'] / 100; // 50% diskon
                                                                $harga_final = $total_harga * (1 - $discount); // Harga akhir setelah diskon   
                                                                $total_harga_revisi += $harga_final;
                                                                } 
                                                                $grand_total_revisi = $total_harga_revisi + $data_detail['ongkir'];
                                                            ?>
                                                <b>Total Invoice Revisi</b><br>
                                                Rp. <?php echo number_format($grand_total_revisi); ?>
                                            </button>
                                        <?php
                                    }
                                
                                
                                ?>
                            </div>
                        </div>
                        <!-- Default Tabs -->
                        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="detail-komplain-nonppn.php?id=<?php echo urlencode($_GET['id']) ?>"
                                    class="nav-link">Original</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#" class="nav-link active">Revisi</a>
                            </li>
                        </ul>
                        
                        <?php  
                            if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") { 
                                ?>
                                    <div class="d-flex justify-content-start mb-3 flex-wrap">
                                        <?php  
                                            if($status_kmpl == '0') {
                                                ?>
                                                    <div class="p-2" id="edit" style="display: block;">
                                                        <button type="button" class="btn btn-warning" id="edit-button">
                                                            <i class="bi bi-pencil"></i> Edit Data Produk
                                                        </button>
                                                    </div>
                                                <?php 
                                            } else {
                                                ?>
                                                    <div class="p-2" id="edit" style="display: none;">
                                                        <button type="button" class="btn btn-warning" id="edit-button">
                                                            <i class="bi bi-pencil"></i> Edit Data Produk
                                                        </button>
                                                    </div>
                                                <?php 
                                            }
                                        ?>
                                        <div class="p-2" id="selesai-edit" style="display: none;">
                                            <button type="button" class="btn btn-warning" id="selesai-edit-button">
                                                <i class="bi bi-pencil"></i> Selesai Edit
                                            </button>
                                        </div>
                                        <div class="p-2">
                                            <button type="button" class="btn btn-primary tambahData"
                                                data-inv="<?php echo $id_inv ?>" data-bs-toggle="modal"
                                                data-bs-target="#tambahData" style="display: none;">
                                                <i class="bi bi-plus-circle"></i> Tambah data produk
                                            </button>
                                        </div>
                                        <div class="p-2 text-start">
                                            <?php  
                                                $id_komplain = $id;
                                                $sql_komplain = mysqli_query($connect, "SELECT status_refund, status_retur FROM komplain_kondisi WHERE id_komplain = '$id_komplain'");
                                                $data_status_refund = mysqli_fetch_array($sql_komplain);
                                                if($data_status_refund['status_retur'] == 1 && $data_status_refund['status_refund'] == 1) {
                                                    ?>
                                                        <a href="#" class="btn btn-secondary mb-3" data-bs-toggle="modal"
                                                            data-bs-target="#bayarRefund">
                                                            <i></i> Pembayaran Refund
                                                        </a>
                                                    <?php
                                                }
                                            ?>
                                        </div>
                                        <!-- kode untuk kondisi button cetak -->
                                        <?php  
                                            $sql_rev = mysqli_query($connect, "SELECT id_inv_revisi, id_inv, no_inv_revisi FROM inv_revisi WHERE id_inv = '$id_inv' ORDER BY no_inv_revisi DESC LIMIT 1");
                                            $cek_rev = mysqli_fetch_array($sql_rev);
                                            $id_inv_revisi = $cek_rev['id_inv_revisi'] ?? null; // Menggunakan null coalescing operator
                                            $total_data = mysqli_num_rows($sql_rev);
                                            
                                        ?>
                                        <?php  
                                            if($total_data != '0'){
                                                ?>
                                                    <div class="p-2 text-start">
                                                        <a href="cetak-inv-revisi-nonppn.php?id=<?php echo encrypt($id_inv, $key_spk) ?>&&id_komplain= <?php echo encrypt($id, $key_spk)?>"
                                                            class="btn btn-primary mb-3">
                                                            <i></i> Cetak Invoice Revisi Non PPN
                                                        </a>
                                                    </div>
                                                <?php
                                            }
                                        ?>
                                    </div>
                                <?php
                            }
                        ?>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="table2">
                                        <thead>
                                            <tr class="text-white" style="background-color: navy;">
                                                <th class="text-center text-nowrap p-3">No</th>
                                                <th class="text-center text-nowrap p-3">Nama Produk</th>
                                                <th class="text-center text-nowrap p-3">Merk</th>
                                                <th class="text-center text-nowrap p-3">Qty Order</th>
                                                <th class="text-center text-nowrap p-3">Satuan</th>
                                                <th class="text-center text-nowrap p-3">Harga</th>
                                                <th class="text-center text-nowrap p-3">Diskon</th>
                                                <th class="text-center text-nowrap p-3">Total</th>
                                                <?php  
                                                    if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") { 
                                                        if($data_detail['status_komplain'] == 0){
                                                            ?>
                                                                <th class="text-center text-nowrap p-3">Aksi</th>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $no = 1;  
                                                include "function/class-spk.php";
                                                $cek_query_produk = "";
                                                if($status_transaksi != 'Cancel Order'){
                                                    $cek_query_produk = $query_produk;
                                                } else {
                                                    $cek_query_produk = $query_produk_cancel;
                                                }
                                                while($data_tmp = mysqli_fetch_array($cek_query_produk)){
                                                    $satuan = detailSpkFnc::getSatuan($data_tmp['id_produk']);
                                                    $total_harga =  $data_tmp['harga'] * $data_tmp['qty'];
                                                    $discount = $data_tmp['disc'] / 100; // 50% diskon
                                                    $harga_final = $total_harga * (1 - $discount); // Harga akhir setelah diskon   
                                                    $id_tmp = !empty($data_tmp['id_tmp']) ? $data_tmp['id_tmp'] : $data_tmp['id_trx'];
                                            ?>
                                            <tr>
                                                <td class="text-center text-nowrap"><?php echo $no ?></td>
                                                <td class="text-nowrap"><?php echo $data_tmp['nama_produk'] ?></td>
                                                <td class="text-center text-nowrap"><?php echo $data_tmp['merk'] ?></td>
                                                <td class="text-center text-nowrap">
                                                    <?php echo number_format($data_tmp['qty']) ?></td>
                                                <td class="text-center text-nowrap"><?php echo $satuan ?></td>
                                                <td class="text-end text-nowrap">
                                                    <?php echo number_format($data_tmp['harga']) ?></td>
                                                <td class="text-end text-nowrap"><?php echo $data_tmp['disc'] ?></td>
                                                <td class="text-end text-nowrap"><?php echo number_format($harga_final) ?>
                                                </td>
                                                <?php  
                                                    if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") { 
                                                        if($data_detail['status_komplain'] == 0){
                                                            ?>
                                                                <td class="text-center">
                                                                    <div class="text-center aksi" style="display: none;">
                                                                        <button class="btn btn-warning btn-sm" title="Edit Data"
                                                                            data-bs-toggle="modal" data-bs-target="#editData"
                                                                            data-id="<?php echo $data_tmp['id_tmp'] ?>"
                                                                            data-id-produk="<?php echo $data_tmp['id_produk'] ?>"
                                                                            data-nama="<?php echo $data_tmp['nama_produk'] ?>"
                                                                            data-merk="<?php echo $data_tmp['merk'] ?>"
                                                                            data-harga="<?php echo $data_tmp['harga']  ?>"
                                                                            data-disc="<?php echo $data_tmp['disc'] ?>"
                                                                            data-stock="<?php if($data_tmp['stock'] == 0){echo '0';}else{echo $data_tmp['stock']; } ?>"
                                                                            data-qty="<?php echo $data_tmp['qty'] ?>"
                                                                            data-qty-edit="<?php echo $data_tmp['qty'] ?>">
                                                                            <i class="bi bi-pencil"></i>
                                                                        </button>
                                                                        <?php  
                                                                            $id_komplain = $id;
                                                                            $sql_komplain = mysqli_query($connect, "SELECT status_refund, status_retur FROM komplain_kondisi WHERE id_komplain = '$id_komplain'");
                                                                            $data_status_refund = mysqli_fetch_array($sql_komplain);
                                                                            if($data_status_refund['status_retur'] == 1 && $data_status_refund['status_refund'] == 0){
                                                                                ?>
                                                                                    <a href="proses/produk-tmp-revisi-nonppn.php?hapus_tmp=<?php echo encrypt($id_tmp, $key_spk) ?>&&id_komplain=<?php echo encrypt($id_komplain, $key_spk) ?>"
                                                                                    class="btn btn-danger btn-sm delete-data"><i
                                                                                        class="bi bi-trash"></i></a>
                                                                                <?php
                                                                            } else if($data_status_refund['status_retur'] == 1 && $data_status_refund['status_refund'] == 1){
                                                                                ?>
                                                                                    <button type="button" class="btn btn-danger btn-sm"
                                                                                        data-bs-toggle="modal" data-bs-target="#hapus"
                                                                                        data-id="<?php echo $data_tmp['id_tmp'] ?>"
                                                                                        data-total="<?php echo $harga_final ?>">
                                                                                        <i class="bi bi-trash"></i>
                                                                                    </button>
                                                                                <?php
                                                                            }
                                                                        ?>
                                                                    </div>
                                                                </td>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                            </tr>
                                            <?php $no++ ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php
                            $no = 1;
                            $sql = "SELECT DISTINCT
                                        nonppn.id_inv_nonppn AS id_inv,
                                        STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') AS tanggal,
                                        ik.id_komplain,
                                        tpk.id_tmp,
                                        tpk.id_produk,
                                        tpk.nama_produk,
                                        tpk.harga,
                                        tpk.qty,
                                        tpk.disc,
                                        tpk.total_harga,
                                        tpk.status_tmp,
                                        spr.stock,
                                        COALESCE(mr_produk.nama_merk, mr_set.nama_merk) AS merk
                                    FROM inv_komplain AS ik 
                                    LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
                                    LEFT JOIN tmp_produk_komplain tpk ON nonppn.id_inv_nonppn = tpk.id_inv
                                    LEFT JOIN stock_produk_reguler spr ON tpk.id_produk = spr.id_produk_reg
                                    LEFT JOIN tb_produk_reguler pr ON tpk.id_produk = pr.id_produk_reg
                                    LEFT JOIN tb_produk_set_marwa tpsm ON tpk.id_produk = tpsm.id_set_marwa
                                    LEFT JOIN tb_merk mr_produk ON pr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                    LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                    WHERE (nonppn.id_inv_nonppn = '$id_inv') AND tpk.status_tmp = '0'";
                            $query = mysqli_query($connect, $sql);
                            $totalRows = mysqli_num_rows($query);
                            if ($totalRows != 0) {
                        ?>
                        <div class="card">
                            <br>
                            <h5 class="text-center">Tambahan Produk Revisi</h5>
                            <div class="card-body p-2 card-mobile">
                                <div class="row p-1">
                                    <div class="col-sm-1 mb-2">
                                        <input type="text" class="form-control text-center mobile"
                                            style="border: none;" value="No" readonly>
                                    </div>
                                    <div class="col-sm-3 mb-2">
                                        <input type="text" class="form-control text-center" style="border: none;"
                                            value="Nama Produk">
                                    </div>
                                    <div class="col-sm-1 mb-2">
                                        <input type="text" class="form-control text-center mobile-text"
                                            style="border: none;" value="Satuan" readonly>
                                    </div>
                                    <div class="col-sm-1 mb-2">
                                        <input type="text" class="form-control text-center mobile-text"
                                            style="border: none;" value="Merk" readonly>
                                    </div>
                                    <div class="col-sm-2 mb-2">
                                        <input type="text" class="form-control text-center mobile-text"
                                            style="border: none;" value="Harga">
                                    </div>
                                    <div class="col-sm-1 mb-2">
                                        <input type="text" class="form-control text-center mobile-text"
                                            style="border: none;" value="Stock" readonly>
                                    </div>
                                    <div class="col-sm-1 mb-2">
                                        <input type="text" class="form-control text-center mobile-text"
                                            style="border: none;" value="Qty" readonly>
                                    </div>
                                    <div class="col-sm-1 mb-2">
                                        <input type="text" class="form-control text-center mobile-text"
                                            style="border: none;" value="Diskon" readonly>
                                    </div>
                                    <div class="col-sm-1 mb-2 text-center">
                                        <input type="text" class="form-control text-center mobile-text"
                                            style="border: none;" value="Aksi" readonly>
                                    </div>
                                </div>
                            </div>
                            <?php
                                } else {
                                }

                            while ($data = mysqli_fetch_array($query)) {
                                $id_inv = $data['id_inv'];
                                $satuan = detailSpkFnc::getSatuan($data['id_produk']);  
                                // $uuid = generate_uuid();
                                $isEmpty = false; // Setel variabel pengecekan menjadi false jika ada data
                            ?>
                            <form action="proses/produk-tmp-revisi-nonppn.php" method="POST"
                                enctype="multipart/form-data">
                                <div class="card-body p-2">
                                    <div class="row p-1">
                                        <div class="col-sm-1 mb-2">
                                            <input type="text" class="form-control text-center bg-light mobile"
                                                value="<?php echo $no; ?>" readonly>
                                            <?php $no++ ?>
                                        </div>
                                        <div class="col-sm-3 mb-2">
                                            <label class="form-control mobile-text fw-bold label-mobile"
                                                style="border: none;">Nama Produk</label>
                                            <input type="hidden" name="id_komplain" value="<?php echo encrypt($id, $key_spk) ?>"
                                                readonly>
                                            <input type="hidden" name="id_tmp[]"
                                                id="id_<?php echo $data['id_tmp'] ?>"
                                                value="<?php echo $data['id_tmp'] ?>" readonly>
                                            <input type="hidden" class="form-control" name="id_produk_tmp[]"
                                                value="<?php echo $data['id_produk'] ?>" readonly>
                                            <input type="text" class="form-control" name="nama_produk[]"
                                                value="<?php echo $data['nama_produk']; ?>">
                                        </div>
                                        <div class="col-sm-1 mb-2">
                                            <label class="form-control mobile-text fw-bold label-mobile"
                                                style="border: none;">Satuan</label>
                                            <input type="text" class="form-control bg-light text-center mobile-text"
                                                value="<?php echo $satuan; ?>" readonly>
                                        </div>
                                        <div class="col-sm-1 mb-2">
                                            <label class="form-control mobile-text fw-bold label-mobile"
                                                style="border: none;">Merk</label>
                                            <input type="text" class="form-control bg-light text-center mobile-text"
                                                value="<?php echo $data['merk'] ?>" readonly>
                                        </div>
                                        <div class="col-sm-2 mb-2">
                                            <label class="form-control mobile-text fw-bold label-mobile"
                                                style="border: none;">Harga</label>
                                            <input type="text" class="form-control text-end mobile-text"
                                                name="harga[]" value="<?php echo number_format($data['harga']) ?>"
                                                oninput="formatNumberHarga(this)">
                                        </div>
                                        <div class="col-sm-1 mb-2">
                                            <label class="form-control mobile-text fw-bold label-mobile"
                                                style="border: none;">Stock</label>
                                            <input type="text" class="form-control bg-light text-end mobile-text"
                                                name="stock[]" id="stock_<?php echo $data['id_tmp'] ?>"
                                                value="<?php echo number_format($data['stock']) ?>" readonly>
                                        </div>
                                        <div class="col-sm-1 mb-2">
                                            <label class="form-control mobile-text fw-bold label-mobile"
                                                style="border: none;">Qty</label>
                                            <input type="text" class="form-control text-end mobile-text"
                                                name="qty_tmp[]" id="qtyInput_<?php echo $data['id_tmp'] ?>"
                                                oninput="checkStock('<?php echo $data['id_tmp'] ?>')" required>
                                        </div>
                                        <div class="col-sm-1 mb-2">
                                            <label class="form-control mobile-text fw-bold label-mobile"
                                                style="border: none;">Diskon</label>
                                            <input type="text" class="form-control text-end mobile-text"
                                                name="disc[]" oninput="validasiDiskon(this)" required>
                                        </div>
                                        <div class="col-sm-1 mb-2 text-center">
                                            <a href="proses/produk-tmp-revisi-nonppn.php?hapus_tmp=<?php echo encrypt($data['id_tmp'], $key_spk) ?>&&id_komplain=<?php echo encrypt($id, $key_spk) ?>"
                                                class="btn btn-danger btn-sm delete-data"><i
                                                    class="bi bi-trash"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="card-body mt-3 text-end">
                                    <?php  
                                        if ($totalRows != 0) {
                                            echo '<button type="submit" class="btn btn-primary" name="simpan-tmp" id="simpan-data"><i class="bi bi-save"></i> Simpan</button>';
                                        }
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Modal Bukti Terima -->
        <?php
            $sql_bukti = "  SELECT 
                                ibt.id_bukti_terima, ibt.id_komplain, ibt.bukti_satu, ibt.created_date, ip.id_komplain, ip.nama_penerima, ip.tgl_terima, ip.created_date, sk.jenis_penerima, sk.dikirim_ekspedisi, sk.no_resi, sk.tgl_kirim, ex.nama_ekspedisi
                            FROM inv_bukti_terima_revisi AS ibt
                            LEFT JOIN inv_penerima_revisi ip ON (ibt.id_komplain = ip.id_komplain)
                            LEFT JOIN revisi_status_kirim sk ON (ibt.id_komplain = sk.id_komplain)
                            LEFT JOIN ekspedisi ex ON (ex.id_ekspedisi = sk.dikirim_ekspedisi) 
                            WHERE ibt.id_komplain = '$id_komplain' ORDER BY ip.created_date  DESC LIMIT 1";
            $query_bukti = mysqli_query($connect, $sql_bukti);
            if (mysqli_num_rows($query_bukti) > 0) {
                $data_bukti = mysqli_fetch_array($query_bukti);
                $id_bukti_terima = $data_bukti['id_bukti_terima'];
                echo $gambar1 = $data_bukti['bukti_satu'];
                $gambar_bukti1 = "gambar-revisi/bukti1/$gambar1";
                $jenis_penerima = $data_bukti['jenis_penerima'];
                $no_resi = $data_bukti['no_resi'];
                $tgl_terima = $data_bukti['tgl_terima'];
                $img = "";
                if ($gambar1 && file_exists("gambar-revisi/bukti_kirim/" . $gambar1)) {
                    $img = "gambar-revisi/bukti1/" . $gambar1;
                } else if($gambar1 && file_exists("gambar/bukti_kirim/" . $nama_driver . "/" . $gambar1)){
                    $img = "gambar-revisi/bukti_kirim/" . $nama_driver . "/" . $gambar1;
                } else {
                    $img = "assets/img/no_img.jpg";
                }
            }
        ?>
        <div class="modal fade" id="bukti" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="card mb-3">
                                <div class="row g-0">
                                    <div class="col-md-6">
                                        <img id="buktiTerimaImg" data-src="<?php echo $gambar_bukti1 ?>"
                                            class="img-fluid" alt="...">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card-body p-5">
                                            <div class="mb-3">
                                                <?php  
                                                if($data_bukti['nama_penerima'] != ''){
                                                    ?>
                                                <label class="fw-bold">Nama Penerima:</label>
                                                <P><?php echo $data_bukti['nama_penerima']; ?></P>
                                                <?php if ($jenis_penerima == 'Ekspedisi') {
                                                            ?>
                                                <label class="fw-bold">No Resi:</label>
                                                <P><?php echo $no_resi; ?></P>
                                                <?php
                                                        }
                                                        ?>
                                                <?php
                                                }
                                            ?>
                                                <?php  
                                                if($tgl_terima){
                                                    ?>
                                                <label class="fw-bold">Tanggal Terima:</label>
                                                <p><?php echo $data_bukti['tgl_terima']?></p>
                                                <?php
                                                } else {
                                                    ?>
                                                <label class="fw-bold">Tanggal Kirim:</label>
                                                <p><?php echo $data_bukti['tgl_kirim']?></p>
                                                <?php
                                                }
                                            ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <!-- End Modal Bukti Terima -->
    </main><!-- End #main -->
    <!-- Modal edit detail -->
    <div class="modal fade" id="edit-details" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Data Detail</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="proses/proses-ubah-detail-revisi.php" method="post">
                        <?php  
                            // Cek kondisi detail, jika data pada inv rev tidak ada maka harus ubah status dikirim terlebih dahulu
                            $cek_detail = $connect->query("SELECT id_inv FROM inv_revisi WHERE id_inv = '$id_inv'");
                            $total_cek_detail = mysqli_num_rows($cek_detail);
                            if($total_cek_detail == 0){
                                echo "Silahkan ubah status dikirim terlebih dahulu";
                            } else {
                                ?>  
                                    <input type="hidden" value="<?php echo base64_encode($id) ?>" name="id_komplain">
                                    <input type="hidden" value="<?php echo $no_inv_fix ?>" name="no_inv_rev">
                                    <div class="mb3">
                                        <label>Pelanggan Invoice</label>
                                        <input type="text" class="form-control" name="cs_inv" value="<?php echo $pelanggan?>">
                                    </div>
                                    <div class="mb3">
                                        <label>Alamat</label>
                                        <textarea type="text" class="form-control" name="alamat" rows="3"><?php echo $alamat ?></textarea>
                                    </div>
                                <?php 
                            }
                        
                        ?>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" name="ubah-detail-rev-nonppn">Ubah data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Ubah Status -->
    <div class="modal fade" id="ubahStatus">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Status Transaksi Komplain</h1>
                </div>
                <div class="modal-body">
                    <form action="proses/proses-ubah-status-trx-rev-nonppn.php" method="POST"
                        enctype="multipart/form-data">
                        <input type="hidden" name="id_komplain" value="<?php echo $id ?>">
                        <input type="hidden" name="id_inv" value="<?php echo $id_inv ?>">
                        <input type="hidden" name="no_inv" value="<?php echo $no_inv_fix ?>">
                        <input type="hidden" name="cs_inv" value="<?php echo $data_detail['cs_inv'] ?>">
                        <input type="hidden" name="alamat" value="<?php echo $data_detail['alamat'] ?>">
                        <input type="hidden" name="total_inv" value="<?php echo $grand_total_revisi ?>">
                        <input type="hidden" name="jenis_inv" value="<?php echo $jenis_inv ?>">
                        <div class="mb-3">
                            <p>Pilih aksi yang akan dilakukan untuk komplain pelanggan ini</p>
                        </div>
                        <div class="mb-3">
                            <?php  
                                // Menentukan apakah radio button "Dikirim" dan "Transaksi Selesai" harus ditampilkan atau disembunyikan
                                if ($total_data_rev != '0' && $status_kmpl == '0') {
                                    $status_pengiriman = $data_rev['status_pengiriman'];
                                    $status_trx_komplain = $data_rev['status_trx_komplain'];
                                    $status_trx_selesai = $data_rev['status_trx_selesai'];
                                    
                                    $show_dikirim = "";
                                    $show_selesai = "";
                                    $show_cancel = "";

                                    if($status_pengiriman == "1" && $status_trx_komplain == "1" && $status_trx_selesai == "1"){
                                        $show_dikirim = "none";
                                        $show_selesai = "none";
                                        $show_cancel = "none";
                                    } else if($status_pengiriman == "1" && $status_trx_komplain == "1" && $status_trx_selesai == "0"){
                                        $show_dikirim = "none";
                                        $show_selesai = "inline-block";
                                        $show_cancel = "inline-block";
                                    } else if ($status_pengiriman == '1' && $status_trx_komplain == '0' && $status_trx_selesai == '0') {
                                        $show_dikirim = "inline-block";
                                        $show_selesai = "inline-block";
                                        $show_cancel = "inline-block";
                                    } else if ($status_pengiriman == "0" && $status_trx_komplain == "0" && $status_trx_selesai == "0") {
                                        $show_dikirim = "inline-block";
                                        $show_selesai = "inline-block";
                                        $show_cancel = "inline-block";
                                    } else {
                                        $show_dikirim = "none";
                                        $show_selesai = "none";
                                        $show_cancel = "inline-block";
                                    }
                                } else if ($total_data_rev == '0' && $status_kmpl == '0'){
                                    $show_dikirim = "inline-block";
                                    $show_selesai = "inline-block";
                                    $show_cancel = "inline-block";
                                } else {
                                    $show_dikirim = "none";
                                    $show_selesai = "none";
                                    $show_cancel = "inline-block";
                                }
                            ?>
                            <!-- Display Dikirim -->
                            <div class="form-check form-check-inline" style="display: <?php echo $show_dikirim ?>;">
                                <input class="form-check-input" type="radio" name="status_kirim" id="dikirim" value="dikirim">
                                <label class="form-check-label" for="dikirim">Dikirim</label>
                            </div>

                            <!-- Display Transaksi Selesai -->
                            <div class="form-check form-check-inline" style="display: <?php echo $show_selesai ?>;">
                                <input class="form-check-input" type="radio" name="status_kirim" id="selesai" value="selesai">
                                <label class="form-check-label" for="selesai">Transaksi Selesai</label>
                            </div>

                            <!-- Display Transaksi Cancel (Always visible) -->
                            <div class="form-check form-check-inline" style="display: <?php echo $show_cancel ?>;">
                                <input class="form-check-input" type="radio" name="status_kirim" id="cancel" value="cancel">
                                <label class="form-check-label" for="cancel">Transaksi Cancel</label>
                            </div>
                        </div>
                        <div id="trxKirim" style="display: none;">
                            <div class="mb-3">
                                <label>Jenis Pengiriman</label>
                                <select class="form-select" name="jenis_pengiriman" id="jenis_pengiriman">
                                    <option value="">Pilih</option>
                                    <option value="Driver">Driver</option>
                                    <option value="Ekspedisi">Expedisi</option>
                                    <option value="Diambil Langsung">Diambil Langsung</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3" id="jenis_driver" style="display: none;">
                            <label id="labelDriver">Pilih Driver</label>
                            <select id="pengirim" name="pengirim" class="form-select" required>
                                <option value="">Pilih...</option>
                                <?php
                                $sql_driver = mysqli_query($koneksi2, "SELECT us.id_user_role, us.id_user, us.nama_user, rl.nama_role FROM $database2.user AS us JOIN user_role rl ON (us.id_user_role = rl.id_user_role) WHERE rl.nama_role = 'Driver'");
                                while ($data_driver_rev = mysqli_fetch_array($sql_driver)) {
                                ?>
                                <option value="<?php echo $data_driver_rev['id_user'] ?>">
                                    <?php echo $data_driver_rev['nama_user'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3" id="jenis_ekspedisi" style="display: none;">
                            <div class="mb-3">
                                <label id="labelEkspedisi">Pilih Ekspedisi</label>
                                <select id="ekspedisi" name="ekspedisi" class="form-select selectize-js" required>
                                    <option value="">Pilih...</option>
                                    <?php
                                    $sql_ekspedisi = mysqli_query($connect, "SELECT * FROM ekspedisi");
                                    while ($data_ekspedisi = mysqli_fetch_array($sql_ekspedisi)) {
                                    ?>
                                    <option value="<?php echo $data_ekspedisi['id_ekspedisi'] ?>">
                                        <?php echo $data_ekspedisi['nama_ekspedisi'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label id="labelResi">No. Resi</label>
                                <input type="text" class="form-control" name="resi" id="resi">
                            </div>
                            <div class="mb-3">
                                <label id="labelJenisOngkir">Jenis Ongkir</label>
                                <select id="jenis_ongkir" name="jenis_ongkir" class="form-select">
                                    <option>Pilih</option>
                                    <option value="0">Non COD</option>
                                    <option value="1">COD</option>
                                </select>
                            </div>
                            <div class="mb-3" id="ongkir" style="display: block;">
                                <div class="row">
                                    <label>Nominal Ongkir</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" style="background-color: #f8f9fa;"
                                            name="ongkir" id="ongkos_kirim" readonly>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-check mt-1">
                                            <input class="form-check-input" name="free_ongkir" type="checkbox" value="1"
                                                id="free_ongkir">
                                            <label class="form-check-label" for="free_ongkir" id="free_ongkir_label">
                                                Free Ongkir
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label id="labelDikirimOleh">Dikirim Oleh</label>
                                <input type="text" class="form-control" name="dikirim" id="dikirim_oleh">
                            </div>
                            <div class="mb-3">
                                <label id="labelPj">Penanggung Jawab</label>
                                <input type="text" class="form-control" name="pj" id="penanggung_jawab">
                            </div>
                        </div>
                        <div class="mb-3" id="jenis_diambil" style="display: none;">
                            <label id="labelDiambil">Diambil Oleh</label>
                            <input type="text" name="diambil_oleh" id="diambil" class="form-control">
                        </div>
                        <div class="mb-3" id="alasanCancel" style="display: none;">
                            <label id="labelResi">Alasan Cancel</label>
                            <input type="text" class="form-control" name="alasan_cancel" id="alasan_cancel">
                        </div>
                        <div class="mb-3" id="tanggal" style="display: none;">
                            <label id="labelDate">Tanggal</label>
                            <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl"
                                id="date" required>
                        </div>
                        <div class="mb-3" id="buktiTerima" style="display: none;">
                            <div class="preview-image mb-3">
                                <img id="imagePreviewAdd" src="#" alt="Preview Image" style="display:none;">
                                <p id="imageSizeAdd" style="display:none;"></p>
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <div class="fileUpload btn btn-primary">
                                        <span id="uploadButtonTextAdd"><i class="bi bi-upload"></i> Upload Bukti Terima</span>
                                        <input class="upload" type="file" name="fileku" id="formFileAdd" accept=".jpg, .png, .jpeg" onchange="compressImage(event, 'Add')">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                id="cancelDikirim">Tutup</button>
                            <button type="submit" class="btn btn-primary" name="ubah-status"> Ubah Status</button>
                        </div>
                    </form>
                </div>
                <style>
                .preview-image {
                    max-width: 100%;
                    height: auto;
                }
                </style>
                <!-- Selectize JS -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var dikirim = document.getElementById('dikirim');
                        var selesai = document.getElementById('selesai');
                        var cancel = document.getElementById('cancel');
                        var trxKirim = document.getElementById('trxKirim');
                        var jenisPengiriman = document.getElementById('jenis_pengiriman');
                        var jenisEkspedisi = document.getElementById('jenis_ekspedisi');
                        var jenisDriver = document.getElementById('jenis_driver');
                        var jenisDiambil = document.getElementById('jenis_diambil');
                        var ekspedisiSelectize = document.getElementById('ekspedisi-selectized');
                        var pengirim = document.getElementById('pengirim');
                        var resi = document.getElementById('resi');
                        var jenis_ongkir = document.getElementById('jenis_ongkir');
                        var bukti = document.getElementById('buktiTerima');
                        var diambil = document.getElementById('diambil');
                        var alasanCancel = document.getElementById('alasan_cancel');
                        var tanggal = document.getElementById('tanggal');
                        var ongkos_kirim = document.getElementById('ongkos_kirim');
                        var freeOngkirCheckbox = document.getElementById('free_ongkir');
                        var freeOngkirLabel = document.getElementById('free_ongkir_label');
                        var penanggung_jawab = document.getElementById('penanggung_jawab');
                        var dikirim_oleh = document.getElementById('dikirim_oleh');
                        var imgPreview = document.getElementById('imagePreviewAdd');
                        var fileku = document.getElementById('formFileAdd');

                        dikirim.addEventListener('change', updateVisibility);
                        selesai.addEventListener('change', updateVisibility);
                        cancel.addEventListener('change', updateVisibility);

                        function updateVisibility() {
                            if (dikirim.checked) {
                                trxKirim.style.display = 'block';
                                jenisPengiriman.style.display = 'block';
                                jenisPengiriman.setAttribute('required', 'true');
                                tanggal.style.display = 'none';
                                alasanCancel.style.display = 'none';
                                alasanCancel.removeAttribute('required');
                                jenisDiambil.style.display = 'none';
                            } else if (selesai.checked) {
                                trxKirim.style.display = 'none';
                                jenisPengiriman.style.display = 'none';
                                jenisPengiriman.value = '';
                                jenisDriver.style.display = 'none';
                                jenisEkspedisi.style.display = 'none';
                                diambil.value = '';
                                diambil.removeAttribute('required');
                                imgPreview.src = '#';
                                imgPreview.style.display = 'none';
                                fileku.value = '';
                                fileku.removeAttribute('required');
                                bukti.style.display = 'none';
                                tanggal.style.display = 'block';
                                alasanCancel.style.display = 'none';
                                alasanCancel.removeAttribute('required');
                                jenisPengiriman.removeAttribute('required');
                                ekspedisiSelectize.removeAttribute('required');
                                pengirim.removeAttribute('required');
                                jenisDiambil.style.display = 'none';
                            } else if (cancel.checked) {
                                trxKirim.style.display = 'none';
                                jenisPengiriman.style.display = 'none';
                                jenisPengiriman.value = '';
                                jenisDriver.style.display = 'none';
                                jenisEkspedisi.style.display = 'none';
                                diambil.value = '';
                                diambil.removeAttribute('required');
                                imgPreview.src = '#';
                                imgPreview.style.display = 'none';
                                fileku.value = '';
                                fileku.removeAttribute('required');
                                bukti.style.display = 'none';
                                tanggal.style.display = 'block';
                                alasanCancel.style.display = 'block';
                                alasanCancel.setAttribute('required', 'true');
                                jenisPengiriman.removeAttribute('required');
                                ekspedisiSelectize.removeAttribute('required');
                                pengirim.removeAttribute('required');
                                jenisDiambil.style.display = 'none';
                            }
                        }
                        jenisPengiriman.addEventListener('change', function() {
                            if (this.value === 'Driver') {
                                jenisDriver.style.display = 'block';
                                jenisEkspedisi.style.display = 'none';
                                jenisDiambil.style.display = 'none';
                                ekspedisi.selectize.clear();
                                ekspedisiSelectize.removeAttribute('required');
                                resi.value = '';
                                resi.removeAttribute('required');
                                ongkos_kirim.value = '';
                                jenis_ongkir.value = '';
                                jenis_ongkir.removeAttribute('required');
                                freeOngkirCheckbox.checked = false; // Hilangkan centang jika ada
                                diambil.value = '';
                                diambil.removeAttribute('required');
                                dikirim_oleh.value = '';
                                penanggung_jawab.value = '';
                                imgPreview.src = '#';
                                imgPreview.style.display = 'none';
                                fileku.value = '';
                                fileku.removeAttribute('required');
                                bukti.style.display = 'none';
                                tanggal.removeAttribute('required');
                                tanggal.style.display = 'block';
                                pengirim.style.display = 'block';
                                pengirim.setAttribute('required', 'true');
                            } else if (this.value === 'Ekspedisi') {
                                jenisEkspedisi.style.display = 'block';
                                jenisDriver.style.display = 'none';
                                pengirim.value = '';
                                pengirim.removeAttribute('required');
                                ekspedisiSelectize.setAttribute('required', 'true');
                                resi.setAttribute('required', 'true');
                                jenis_ongkir.setAttribute('required', 'true');
                                tanggal.removeAttribute('required');
                                tanggal.style.display = 'block';
                                ongkos_kirim.removeAttribute('required');
                                penanggung_jawab.removeAttribute('required');
                                jenisDiambil.style.display = 'none';
                                diambil.value = '';
                                diambil.removeAttribute('required');
                                bukti.style.display = 'block';
                                dikirim_oleh.removeAttribute('required');
                                fileku.setAttribute('required', 'true');
                            } else if (this.value === 'Diambil Langsung') {
                                jenisDriver.style.display = 'none';
                                jenisDiambil.style.display = 'block';
                                diambil.setAttribute('required', 'true');
                                pengirim.removeAttribute('required');
                                jenisEkspedisi.style.display = 'none';
                                ekspedisi.selectize.clear();
                                ekspedisiSelectize.removeAttribute('required');
                                resi.value = '';
                                resi.removeAttribute('required');
                                ongkos_kirim.value = '';
                                jenis_ongkir.value = '';
                                jenis_ongkir.removeAttribute('required');
                                freeOngkirCheckbox.checked = false; // Hilangkan centang jika ada
                                dikirim_oleh.value = '';
                                penanggung_jawab.value = '';
                                tanggal.removeAttribute('required');
                                pengirim.style.display = 'none';
                                tanggal.style.display = 'block';
                                bukti.style.display = 'block';
                                fileku.setAttribute('required', 'true');
                            } else {
                                jenisEkspedisi.style.display = 'none';
                                jenisDriver.style.display = 'none';
                                ekspedisi.selectize.clear();
                                ekspedisiSelectize.removeAttribute('required');
                                bukti.removeAttribute('required');
                                pengirim.removeAttribute('required');
                                tanggal.style.display = 'none';
                                bukti.style.display = 'none';
                                jenisDiambil.style.display = 'none';
                                diambil.value = '';
                                diambil.removeAttribute('required');
                            }
                        });

                        jenis_ongkir.addEventListener('change', function() {
                            if (this.value === '0') {
                                ongkos_kirim.style.display = 'block';
                                ongkos_kirim.style.backgroundColor = '';
                                ongkos_kirim.removeAttribute('readonly');
                                ongkos_kirim.setAttribute('required', 'true');
                                freeOngkirCheckbox.style.display = 'block'; // Hilangkan centang jika ada
                                freeOngkirLabel.style.display = 'block'; // Hilangkan centang jika ada
                            } else {
                                ongkos_kirim.style.display = 'block';
                                ongkos_kirim.style.backgroundColor = '#f8f9fa';
                                ongkos_kirim.removeAttribute('required');
                                ongkos_kirim.setAttribute('readonly', 'true');
                                ongkos_kirim.value = '0';
                                freeOngkirCheckbox.style.display = 'none'; // Hilangkan centang jika ada
                                freeOngkirLabel.style.display = 'none'; // Hilangkan centang jika ada
                            }
                        });


                        ongkos_kirim.addEventListener('input', function() {
                            formatNumber(ongkos_kirim);
                        });

                        function formatNumber(input) {
                            var value = input.value.replace(/\D/g, ''); // Menghapus karakter non-digit
                            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Menambahkan pemisah ribuan dengan titik
                            input.value = value;
                        }

                        const cancelButton = document.getElementById('cancelDikirim');
                            cancelButton.addEventListener('click', function() {
                            location.reload();
                        });
                    });
                    flatpickr("#date", {
                        dateFormat: "d/m/Y",
                        defaultDate: "today"
                    });
                </script>
            </div>
        </div>
    </div>
    <!-- End Modal Ubah Status -->

    <!-- Modal Ubah Jenis Pengiriman-->
    <div class="modal fade" id="ubahJenisPengiriman" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Ubah Jenis Pengiriman</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="proses/proses-ubah-status-trx-rev-nonppn.php" method="POST"
                        enctype="multipart/form-data">
                        <input type="hidden" name="id_komplain" value="<?php echo $id ?>">
                        <input type="hidden" name="id_status_kirim_revisi" value="<?php echo $id_status_kirim_revisi ?>">
                        <input type="hidden" name="id_inv" value="<?php echo $id_inv ?>">
                        <input type="hidden" name="id_inv_revisi" value="<?php echo $id_inv_revisi ?>">
                        <input type="hidden" name="id_bukti_terima" value="<?php echo $id_bukti_terima ?>">
                        <input type="hidden" name="bukti_sebelumnya" value="<?php echo $gambar1 ?>">
                        <div id="trxKirim">
                            <div class="mb-3">
                                <label>Jenis Pengiriman</label>
                                <select class="form-select" name="jenis_pengiriman" id="ubah_jenis_pengiriman" required>
                                    <option value="">Pilih</option>
                                    <option value="Driver">Driver</option>
                                    <option value="Ekspedisi">Expedisi</option>
                                    <option value="Diambil Langsung">Diambil Langsung</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3" id="ubah_jenis_driver" style="display: none;">
                            <label id="labelDriver">Pilih Driver</label>
                            <select id="ubah-pengirim" name="pengirim" class="form-select" required>
                                <option value="">Pilih...</option>
                                <?php
                                $sql_driver = mysqli_query($koneksi2, "SELECT us.id_user_role, us.id_user, us.nama_user, rl.nama_role FROM $database2.user AS us JOIN user_role rl ON (us.id_user_role = rl.id_user_role) WHERE rl.nama_role = 'Driver'");
                                while ($data_driver_rev = mysqli_fetch_array($sql_driver)) {
                                ?>
                                <option value="<?php echo $data_driver_rev['id_user'] ?>">
                                    <?php echo $data_driver_rev['nama_user'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3" id="ubah_jenis_ekspedisi" style="display: none;">
                            <div class="mb-3">
                                <label id="labelEkspedisi">Pilih Ekspedisi</label>
                                <select id="ekspedisi-ubah" name="ekspedisi" class="form-select selectize-js">
                                    <option value="">Pilih...</option>
                                    <?php
                                    $sql_ekspedisi = mysqli_query($connect, "SELECT * FROM ekspedisi");
                                    while ($data_ekspedisi = mysqli_fetch_array($sql_ekspedisi)) {
                                    ?>
                                    <option value="<?php echo $data_ekspedisi['id_ekspedisi'] ?>">
                                        <?php echo $data_ekspedisi['nama_ekspedisi'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label id="labelResi">No. Resi</label>
                                <input type="text" class="form-control" name="resi" id="ubah_resi">
                            </div>
                            <div class="mb-3">
                                <label id="labelJenisOngkir">Jenis Ongkir</label>
                                <select id="ubah_jenis_ongkir" name="jenis_ongkir" class="form-select">
                                    <option>Pilih</option>
                                    <option value="0">Non COD</option>
                                    <option value="1">COD</option>
                                </select>
                            </div>
                            <div class="mb-3" id="ubah_ongkir" style="display: block;">
                                <div class="row">
                                    <label>Nominal Ongkir</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" style="background-color: #f8f9fa;"
                                            name="ongkir" id="ubah_ongkos_kirim" readonly>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="form-check mt-1">
                                            <input class="form-check-input" name="free_ongkir" type="checkbox" value="1"
                                                id="ubah_free_ongkir">
                                            <label class="form-check-label" for="ubah_free_ongkir" id="ubah_free_ongkir_label">
                                                Free Ongkir
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label id="labelDikirimOleh">Dikirim Oleh</label>
                                <input type="text" class="form-control" name="dikirim" id="ubah_dikirim_oleh">
                            </div>
                            <div class="mb-3">
                                <label id="labelPj">Penanggung Jawab</label>
                                <input type="text" class="form-control" name="pj" id="ubah_penanggung_jawab">
                            </div>
                        </div>
                        <div class="mb-3" id="ubah_jenis_diambil" style="display: none;">
                            <label id="labelDiambil">Diambil Oleh</label>
                            <input type="text" name="diambil_oleh" id="ubah_diambil" class="form-control">
                        </div>
                        <div class="mb-3" id="alasanCancel">
                            <label id="labelResi">Alasan Perubahan</label>
                            <input type="text" class="form-control" name="alasan_ubah" id="alasan_ubah" required>
                        </div>
                        <div class="mb-3" id="ubah_tanggal">
                            <label id="labelDate">Tanggal</label>
                            <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl"
                                id="ubah_date" required>
                        </div>
                        <div class="mb-3" id="buktiTerimaUbah" style="display: none;">
                            <div class="preview-image mb-3">
                                <img id="imagePreviewEdit" src="#" alt="Preview Image" style="display:none;">
                                <p id="imageSizeEdit" style="display:none;"></p>
                            </div>
                            <div class="mb-3">
                                <div class="input-group">
                                    <div class="fileUpload btn btn-primary">
                                        <span id="uploadButtonTextEdit"><i class="bi bi-upload"></i> Upload Bukti Terima</span>
                                        <input class="upload" type="file" name="fileku" id="formFileEdit" accept=".jpg, .png, .jpeg" onchange="compressImage(event, 'Edit')">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                id="cancelDikirim">Tutup</button>
                            <button type="submit" class="btn btn-primary" name="ubah-pengiriman"> Ubah Status</button>
                        </div>
                    </form>
                </div>
                <style>
                .preview-image {
                    max-width: 100%;
                    height: auto;
                }
                </style>
                <!-- Selectize JS -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var jenisPengiriman = document.getElementById('ubah_jenis_pengiriman');
                        var jenisEkspedisi = document.getElementById('ubah_jenis_ekspedisi');
                        var jenisDriver = document.getElementById('ubah_jenis_driver');
                        var jenisDiambil = document.getElementById('ubah_jenis_diambil');
                        var ekspedisiSelectize = document.getElementById('ekspedisi-ubah');
                        var pengirim = document.getElementById('ubah-pengirim');
                        var resi = document.getElementById('ubah_resi');
                        var jenis_ongkir = document.getElementById('ubah_jenis_ongkir');
                        var bukti = document.getElementById('buktiTerimaUbah');
                        var diambil = document.getElementById('ubah_diambil');
                        var ongkos_kirim = document.getElementById('ubah_ongkos_kirim');
                        var penanggung_jawab = document.getElementById('ubah_penanggung_jawab');
                        var dikirim_oleh = document.getElementById('ubah_dikirim_oleh');
                        var imgPreview = document.getElementById('imagePreviewEdit');
                        var fileku = document.getElementById('formFileEdit');
                        var alasanUbah = document.getElementById('alasan_ubah');
                        var freeOngkirCheckbox = document.getElementById('ubah_free_ongkir');
                        var freeOngkirLabel = document.getElementById('ubah_free_ongkir_label');
                        
                        jenisPengiriman.addEventListener('change', function() {
                            if (this.value === 'Driver') {
                                jenisDriver.style.display = 'block';
                                jenisEkspedisi.style.display = 'none';
                                jenisDiambil.style.display = 'none';
                                ekspedisiSelectize.selectize.clear();
                                ekspedisiSelectize.removeAttribute('required');
                                resi.value = '';
                                resi.removeAttribute('required');
                                ongkos_kirim.value = '';
                                jenis_ongkir.value = '';
                                jenis_ongkir.removeAttribute('required');
                                freeOngkirCheckbox.checked = false; // Hilangkan centang jika ada
                                diambil.value = '';
                                diambil.removeAttribute('required');
                                dikirim_oleh.value = '';
                                penanggung_jawab.value = '';
                            
                                imgPreview.style.display = 'none';
                                fileku.value = '';
                                fileku.removeAttribute('required');
                                bukti.style.display = 'none';
                                tanggal.removeAttribute('required');
                                tanggal.style.display = 'block';
                                pengirim.style.display = 'block';
                                pengirim.setAttribute('required', 'true');
                                alasanUbah.value = '';
                            } else if (this.value === 'Ekspedisi') {
                                jenisEkspedisi.style.display = 'block';
                                jenisDriver.style.display = 'none';
                                pengirim.value = '';
                                pengirim.removeAttribute('required');
                                ekspedisiSelectize.setAttribute('required', 'true');
                                resi.setAttribute('required', 'true');
                                jenis_ongkir.setAttribute('required', 'true');
                                tanggal.removeAttribute('required');
                                ongkos_kirim.removeAttribute('required');
                                penanggung_jawab.removeAttribute('required');
                                jenisDiambil.style.display = 'none';
                                diambil.value = '';
                                diambil.removeAttribute('required');
                                bukti.style.display = 'block';
                                dikirim_oleh.removeAttribute('required');
                                fileku.setAttribute('required', 'true');
                                alasanUbah.value = '';
                            } else if (this.value === 'Diambil Langsung') {
                                jenisDriver.style.display = 'none';
                                jenisDiambil.style.display = 'block';
                                diambil.setAttribute('required', 'true');
                                pengirim.removeAttribute('required');
                                jenisEkspedisi.style.display = 'none';
                                ekspedisiSelectize.selectize.clear();
                                ekspedisiSelectize.removeAttribute('required');
                                resi.value = '';
                                resi.removeAttribute('required');
                                ongkos_kirim.value = '';
                                jenis_ongkir.value = '';
                                jenis_ongkir.removeAttribute('required');
                                freeOngkirCheckbox.checked = false; // Hilangkan centang jika ada
                                dikirim_oleh.value = '';
                                penanggung_jawab.value = '';
                                tanggal.removeAttribute('required');
                                pengirim.style.display = 'none';
                                tanggal.style.display = 'block';
                                bukti.style.display = 'block';
                                fileku.setAttribute('required', 'true');
                                alasanUbah.value = '';
                            } else {
                                jenisEkspedisi.style.display = 'none';
                                jenisDriver.style.display = 'none';
                                ekspedisi.selectize.clear();
                                ekspedisiSelectize.removeAttribute('required');
                                bukti.removeAttribute('required');
                                pengirim.removeAttribute('required');
                                tanggal.style.display = 'none';
                                bukti.style.display = 'none';
                                jenisDiambil.style.display = 'none';
                                diambil.value = '';
                                diambil.removeAttribute('required');
                            }
                        });

                        jenis_ongkir.addEventListener('change', function() {
                            if (this.value === '0') {
                                ongkos_kirim.style.display = 'block';
                                ongkos_kirim.style.backgroundColor = '';
                                ongkos_kirim.removeAttribute('readonly');
                                ongkos_kirim.setAttribute('required', 'true');
                                freeOngkirCheckbox.style.display = 'block'; // Hilangkan centang jika ada
                                freeOngkirLabel.style.display = 'block'; // Hilangkan centang jika ada
                            } else {
                                ongkos_kirim.style.display = 'block';
                                ongkos_kirim.style.backgroundColor = '#f8f9fa';
                                ongkos_kirim.removeAttribute('required');
                                ongkos_kirim.setAttribute('readonly', 'true');
                                ongkos_kirim.value = '0';
                                freeOngkirCheckbox.style.display = 'none'; // Hilangkan centang jika ada
                                freeOngkirLabel.style.display = 'none'; // Hilangkan centang jika ada
                            }
                        });

                        ongkos_kirim.addEventListener('input', function() {
                            formatNumber(ongkos_kirim);
                        });

                        function formatNumber(input) {
                            var value = input.value.replace(/\D/g, ''); // Menghapus karakter non-digit
                            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Menambahkan pemisah ribuan dengan titik
                            input.value = value;
                        }


                        const cancelButton = document.getElementById('cancelDikirim');
                            cancelButton.addEventListener('click', function() {
                            location.reload();
                        });

                        flatpickr("#ubah_date", {
                            dateFormat: "d/m/Y",
                            defaultDate: "today"
                        });
                    });
                </script>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
    <!-- Selectize JS -->
    <?php include "page/script.php" ?>
    <script>
        $(document).ready(function() {
            // Function untuk menampilkan modal bukti terima
            $('#bukti').on('show.bs.modal', function(event) {
                var imgSrc = '<?php echo $gambar_bukti1 ?>';
                $('#buktiTerimaImg').attr('src', imgSrc).attr('data-src', imgSrc);
            });

            // Event handler saat gambar bukti terima diklik
            $(document).on('click', '#buktiTerimaImg', function() {
                // Sembunyikan modal bukti terima
                $('#bukti').modal('hide');

                // Inisialisasi lightGallery
                $(this).lightGallery({
                    dynamic: true,
                    dynamicEl: [{
                        src: $(this).data('src') // URL gambar yang akan ditampilkan
                    }]
                });

                // Ubah atribut dan gaya CSS dari elemen body
                $('body').attr('scroll', 'no').addClass('disable-scroll');
            });

            // Event handler saat lightGallery ditutup
            $(document).on('onCloseAfter.lg', '#buktiTerimaImg', function() {
                // Tampilkan kembali modal bukti terima
                $('#bukti').modal('show');

                // Hapus atribut dan gaya CSS dari elemen body
                $('body').removeAttr('scroll').removeClass('disable-scroll');
            });
        });
    </script>
    <script src="assets/vendor/lightbox/dist/js/picturefill.min.js"></script>
    <script src="assets/vendor/lightbox/dist/js/lightgallery-all.min.js"></script>
    <script src="assets/vendor/lightbox/lib/jquery.mousewheel.min.js"></script>
</body>

</html>

<!-- Modal Refund -->
<div class="modal fade" id="bayarRefund" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card text-center p-3">
                    <p class="text-center" style="font-size: 20px;"><b>Barang Refund Dana</b></p>
                    <div class="d-flex justify-content-center">
                        <div class="card p-3 border">
                            <p class="text-center" style="font-size: 18px;">
                                Total Nilai Refund <br>
                                <?php  
                                    $grand_total_refund = 0;
                                    while($total_refund = mysqli_fetch_array($query_total_refund)){
                                        $harga_total =  $total_refund['harga'] * $total_refund['qty'];
                                        $disc = $total_refund['disc'];
                                        $hasil_disc = $disc / 100;
                                        $harga_final = $harga_total * (1 - $hasil_disc); // Harga akhir setelah diskon  
                                        $grand_total_refund += $harga_final;
                                    }
                                ?>
                                <?php  echo number_format($grand_total_refund)?>
                            </p>
                        </div>
                    </div>
                    <div class="table-responsive p-3">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="text-white" style="background-color: navy;">
                                    <th class="text-center text-nowrap p-3">No</th>
                                    <th class="text-center text-nowrap p-3">Nama Produk</th>
                                    <th class="text-center text-nowrap p-3">Satuan</th>
                                    <th class="text-center text-nowrap p-3">Merk</th>
                                    <th class="text-center text-nowrap p-3">Qty Order</th>
                                    <th class="text-center text-nowrap p-3">Harga</th>
                                    <th class="text-center text-nowrap p-3">Diskon</th>
                                    <th class="text-center text-nowrap p-3">Total</th>
                                    <th class="text-center text-nowrap p-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $no = 1;
                                    while($data_refund = mysqli_fetch_array($query_refund)){
                                        $id_produk = $data_refund['id_produk'];
                                        $total_harga =  $data_refund['harga'] * $data_refund['qty'];
                                        $discount = $data_refund['disc'] / 100; // 50% diskon
                                        $harga_final = $total_harga * (1 - $discount); // Harga akhir setelah diskon  
                                        $id_produk_substr = substr($id_produk, 0, 2);
                                        $pcs = 'Pcs';
                                        $set = 'Set';    
                                ?>
                                <tr>
                                    <td class="text-center text-nowrap"><?php echo $no ?></td>
                                    <td class="text-nowrap text-start"><?php echo $data_refund['nama_produk'] ?></td>
                                    <td class="text-center text-nowrap">
                                        <?php 
                                                    if($id_produk_substr == 'BR'){
                                                        echo $pcs;
                                                    } else {
                                                        echo $set;
                                                    }   
                                                ?>
                                    </td>
                                    <td class="text-center text-nowrap"><?php echo $data_refund['merk'] ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data_refund['qty'] ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_refund['harga']) ?>
                                    </td>
                                    <td class="text-end text-nowrap"><?php echo $data_refund['disc'] ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($harga_final) ?></td>
                                    <td class="text-center text-nowrap">
                                        <a href="proses/produk-tmp-revisi-nonppn.php?batal_refund=<?php echo base64_encode($data_refund['id_tmp']) ?>&&id_komplain=<?php echo encrypt($id, $key_spk) ?>"
                                            class="btn btn-danger btn-sm" title="Batal Refund"><i class="bi bi-x-circle"></i></a>
                                    </td>
                                </tr>
                                <?php $no++ ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Refund -->

<!-- Modal Add Produk -->
<div class="modal fade" id="tambahData" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form method="post" action="">
                <!-- Tambahkan form dengan method POST -->
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Data Barang</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive position-relative">
                        <!-- Tambahkan class position-relative untuk posisi relatif -->
                        <div id="loading-indicator" class="position-absolute top-50 start-50 translate-middle"
                            style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered" id="table3">
                            <thead>
                                <tr class="text-white" style="background-color: #051683;">
                                    <td class="text-center p-3 text-nowrap">No</td>
                                    <td class="text-center p-3 text-nowrap">Kode Produk</td>
                                    <td class="text-center p-3 text-nowrap">Nama Produk</td>
                                    <td class="text-center p-3 text-nowrap">Satuan</td>
                                    <td class="text-center p-3 text-nowrap">Merk</td>
                                    <td class="text-center p-3 text-nowrap">Stock</td>
                                    <td class="text-center p-3 text-nowrap">Aksi</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $id = htmlspecialchars($_GET['id']);
                                $selected_produk = [];
                                $no = 1;

                                // Mengambil data produk yang ada dalam tmp_produk_spk untuk id_spk yang sedang aktif
                                $query_selected_produk = mysqli_query($connect, "SELECT id_produk FROM tmp_produk_komplain WHERE id_inv = '$id_inv'");
                                while ($selected_data = mysqli_fetch_array($query_selected_produk)) {
                                    $selected_produk[] = $selected_data['id_produk'];
                                }

                                $sql = "SELECT 
                                            COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa) AS id_produk,
                                            COALESCE(tpr.kode_produk, tpsm.kode_set_marwa) AS kode_produk,
                                            COALESCE(tpr.nama_produk, tpsm.nama_set_marwa) AS nama_produk,
                                            COALESCE(tpr.harga_produk, tpsm.harga_set_marwa) AS harga_produk,
                                            COALESCE(mr_tpr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                                            spr.id_stock_prod_reg,
                                            spr.stock,
                                            tkp.min_stock, 
                                            tkp.max_stock
                                        FROM stock_produk_reguler AS spr
                                        LEFT JOIN tb_produk_reguler AS tpr ON (tpr.id_produk_reg = spr.id_produk_reg)
                                        LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spr.id_kat_penjualan)
                                        LEFT JOIN tb_produk_set_marwa AS tpsm ON (tpsm.id_set_marwa = spr.id_produk_reg)
                                        LEFT JOIN tb_merk AS mr_tpr ON (tpr.id_merk = mr_tpr.id_merk)
                                        LEFT JOIN tb_merk AS mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
                                        ORDER BY nama_produk ASC";

                                $query = mysqli_query($connect, $sql);

                                while ($data = mysqli_fetch_array($query)) {
                                    $id_produk = $data['id_produk'];
                                    $id_produk_substr = substr($id_produk, 0, 2);
                                    $isChecked = in_array($id_produk, $selected_produk);
                                    $isDisabled = false;

                                    if ($data['stock'] == 0) {
                                        $isDisabled = true; // Jika stock = 0, maka tombol pilih akan menjadi disabled
                                    }
                                ?>
                                <tr>
                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data['kode_produk']; ?></td>
                                    <td class="text-nowrap"><?php echo $data['nama_produk']; ?></td>
                                    <td class="text-center text-nowrap">
                                        <?php 
                                            if($id_produk_substr == 'BR'){
                                                echo "Pcs";
                                            } else {
                                                echo "Set";
                                            }
                                            ?>
                                    </td>
                                    <td class="text-center text-nowrap"><?php echo $data['nama_merk']; ?></td>
                                    <td class="text-center text-nowrap"><?php echo number_format($data['stock']); ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <button class="btn-pilih btn btn-primary btn-sm"
                                            data-inv="<?php echo $id_inv; ?>" data-id-produk="<?php echo $id_produk; ?>"
                                            data-nama-produk="<?php echo $data['nama_produk']; ?>"
                                            data-harga="<?php echo $data['harga_produk']; ?>"
                                            <?php echo ($isChecked || $isDisabled) ? 'disabled' : ''; ?>>Pilih</button>
                                    </td>
                                </tr>
                                <?php $no++; ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        onclick="refreshPage()">Tutup</button>
                </div>
            </form> <!-- Akhir dari form -->
        </div>
    </div>
</div>
<!-- End Add Produk -->

<!-- Modal Hapus -->
<div class="modal fade" id="hapus" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <form action="proses/produk-tmp-revisi-nonppn.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <p>
                            Pilih jenis hapus untuk barang ini: <br>
                            * Jika anda memilih delete only maka nilai barang tidak akan masuk kedalam perhitungan
                            refund (akan hapus permanen)<br>
                            ** Jika anda memilih delete refund maka nilai barang akan masuk kedalam perhitungan refund
                        </p>
                        <input type="hidden" name="id_tmp" id="id_tmp">
                        <input type="hidden" name="id_komplain" value="<?php echo $id ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary" name="hapus-produk-tmp">Delete Only</button>

                        <button type="submit" class="btn btn-primary" name="hapus-produk-refund">Delete Refund</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Hapus -->

<!-- Modal Edit -->
<div class="modal fade" id="editData" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Edit Produk Revisi</h5>
            </div>
            <div class="modal-body">
                <form action="proses/produk-tmp-revisi-nonppn.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_tmp" id="id_tmp">
                    <input type="hidden" name="id_produk" id="id_produk">
                    <input type="hidden" name="id_komplain" value="<?php echo $id ?>">
                    <div class="mb-3">
                        <label for="nama_produk_edit">Nama Produk</label>
                        <input type="text" class="form-control" name="nama_produk" id="nama_produk_edit" required>
                    </div>
                    <div class="mb-3">
                        <label for="merk_edit">Merk</label>
                        <input type="text" class="form-control bg-light" name="merk" id="merk_edit" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="harga_edit">Harga</label>
                        <input type="text" class="form-control" name="harga" id="harga_edit"
                            oninput="formatNumberHarga(this)" required>
                    </div>
                    <div class="mb-3">
                        <label for="disc_edit">Diskon</label>
                        <input type="text" class="form-control" name="disc" id="disc_edit"
                            oninput="validasiDiskon(this)" required>
                    </div>
                    <div class="mb-3" style="display: block;">
                        <label for="stock_edit">Stock</label>
                        <input type="text" class="form-control bg-light" name="stock" id="stock_edit" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="qty_original">Qty Saat Ini</label>
                        <input type="text" class="form-control bg-light" name="qty" id="qty_original" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="qty_edit">Qty Revisi</label>
                        <input type="text" class="form-control" name="qty_edit" id="qty_edit"
                            oninput="formatNumberHarga(this)" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="ubah-data">Ubah Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dikirim-->
<div class="modal fade" id="DiterimaEx" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Status</h1>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal"
                    aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form action="proses/proses-invoice-diterima-revisi.php" method="POST"
                        enctype="multipart/form-data">
                        <input type="hidden" name="id_komplain" value="<?php echo $id; ?>">
                        <input type="hidden" name="id_inv" value="<?php echo $id_inv; ?>">
                        <input type="hidden" name="alamat" value="<?php echo $alamat; ?>">
                        <input type="hidden" name="jenis_inv" value="<?php echo $jenis_inv; ?>">
                        <div class="mb-3">
                            <label>Nama Penerima</label>
                            <input type="text" class="form-control" name="nama_penerima" autocomplete="off" required>
                        </div>
                        <div class="mb-3">
                            <label id="labelDate">Tanggal</label>
                            <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl"
                                id="date" required="required">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="diterima_ekspedisi"><i
                                    class="bi bi-arrow-left-right"></i> Ubah Status</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                id="cancelEkspedisi"><i class="bi bi-x-circle"></i> Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Dikirim -->
<!-- Modal Edit Resi dan Ongkir -->
<div class="modal fade" id="ubahResi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Data Resi dan Ongkir</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="proses/proses-ubah-status-trx-rev-nonppn.php" method="post">
                    <div id="ubahResiBody"></div> <!-- Tempat data tabel akan dimuat -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="ubah-ongkir">Ubah Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Event saat modal akan ditampilkan
    $('#ubahResi').on('shown.bs.modal', function (event) {
        // Ambil tombol yang memicu modal
        var button = $(event.relatedTarget);
        
        // Ambil nilai dari atribut data-id
        var idStatusKirim = button.data('id');
        $.ajax({
            type: 'POST',
            url: 'ajax/data-status-kirim-rev.php', // Pastikan file PHP ini benar
            data: { id_status_kirim: idStatusKirim },
            success: function(response) {
                console.log("Raw Response:", response);
                $('#ubahResiBody').html(response); // Isi modal body dengan HTML dari response
                $('#ubahResi').modal('show'); // Tampilkan modal
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
            }
        });
    });
</script>

<!-- Menampilkan data konfirmasi saat Hapus Data -->
<script>
    // untuk menampilkan data pada atribut <td>
    $('#hapus').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var total = button.data('total');

        var modal = $(this);
        modal.find('.modal-body #id_tmp').val(id);
        modal.find('.modal-body #total_harga').val(total);
    })
</script>

<!-- Kode untuk Edit Data -->
<script>
    $('#editData').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id_edit = button.data('id');
        var id_produk = button.data('id-produk');
        var nama_edit = button.data('nama');
        var merk_edit = button.data('merk');
        var harga_edit = button.data('harga');
        var disc_edit = button.data('disc');
        var stock_edit = button.data('stock');
        var qty_original = button.data('qty');
        var qty_edit = button.data('qty-edit');

        // Menggunakan toLocaleString() untuk memformat harga, stock, dan qty menjadi angka dengan tanda ribuan
        var formattedHarga = parseFloat(harga_edit).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        var formattedStock = parseFloat(stock_edit).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        var formattedOriginal = parseFloat(qty_original).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        var formattedQty = parseFloat(qty_edit).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        var modal = $(this);
        modal.find('.modal-body #id_tmp').val(id_edit);
        modal.find('.modal-body #id_produk').val(id_produk);
        modal.find('.modal-body #nama_produk_edit').val(nama_edit);
        modal.find('.modal-body #merk_edit').val(merk_edit);
        modal.find('.modal-body #harga_edit').val(formattedHarga);
        modal.find('.modal-body #disc_edit').val(disc_edit);

        var stock_input_edit = modal.find('.modal-body #stock_edit');
        var qty_input_original = modal.find('.modal-body #qty_original');
        var qty_input_edit = modal.find('.modal-body #qty_edit');

        stock_input_edit.val(formattedStock);
        qty_input_original.val(formattedOriginal);
        qty_input_edit.val(formattedQty);

        // Menambahkan event listener untuk mengontrol input qty agar tidak melebihi stok
        qty_input_edit.on('input', function() {
            var qtyValue = parseFloat(qty_input_edit.val().replace(/\./g, '').replace(',', '')) || 0;
            var stockValue = parseFloat(stock_input_edit.val().replace(/\./g, '').replace(',', '')) || 0;
            var qtyOriginalValue = parseFloat(qty_input_original.val().replace(/\./g, '').replace(',', '')) || 0;

            // Hitung batas maksimum qty yang bisa dimasukkan (stok + qty sebelumnya)
            var maxQty = stockValue + qtyOriginalValue;

            // Jika qty lebih besar dari stok + qty sebelumnya, set nilai qty menjadi maxQty
            if (qtyValue > maxQty) {
                qty_input_edit.val(maxQty.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')); // Format angka dengan tanda ribuan
            }
        });
    });
</script>

<!-- Display Block dan None kolom aksi -->
<script>
    // Inisialisasi variabel nilai awal
    var toggleValue = 0;
    var edit = document.getElementById("edit");
    var selesai = document.getElementById("selesai-edit");
    var editButton = document.getElementById("edit-button");
    var selesaiEditButton = document.getElementById("selesai-edit-button");
    var aksiElements = document.querySelectorAll(".aksi");
    var tambahDataButton = document.querySelector("button.tambahData");

    editButton.addEventListener("click", function() {
        // Toggle nilai antara 0 dan 1
        toggleValue = 1 - toggleValue;

        // Lakukan sesuatu berdasarkan nilai toggle
        if (toggleValue === 1) {
            // Jika nilai adalah 1, lakukan tindakan ketika tombol diaktifkan
            // console.log("Nilai saat ini adalah 1");
            aksiElements.forEach(function(aksi) {
                aksi.style.display = 'block';
            });
            selesai.style.display = 'block';
            edit.style.display = 'none';
            tambahDataButton.style.display = 'block';
        } else {
            // Jika nilai adalah 0, lakukan tindakan ketika tombol dinonaktifkan
            console.log("Nilai saat ini adalah 0");
            aksiElements.forEach(function(aksi) {
                aksi.style.display = 'none';
            });
            selesai.style.display = 'none';
            edit.style.display = 'block';
            tambahDataButton.style.display = 'none';
        }
    });

    selesaiEditButton.addEventListener("click", function() {
        // Reset nilai toggle ke 0 saat tombol "Selesai Edit" diklik
        toggleValue = 0;

        // Lakukan tindakan saat tombol "Selesai Edit" diklik
        // console.log("Nilai saat ini adalah 0");
        aksiElements.forEach(function(aksi) {
            aksi.style.display = 'none';
        });
        selesai.style.display = 'none';
        edit.style.display = 'block';
        tambahDataButton.style.display = 'none';
    });
</script>

<!-- Kode Untuk Qty   -->
<script>
    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function formatInputValue(value) {
        return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function checkStock(inputId) {
        var stock = parseInt(document.getElementById('stock_' + inputId).value.replace(/,/g,
        '')); // Menggunakan ID yang sesuai untuk elemen stock
        var qtyInput = document.getElementById('qtyInput_' + inputId); // Menggunakan ID yang sesuai untuk elemen qtyInput
        var qty = qtyInput.value.replace(/,/g, '');

        qtyInput.value = formatInputValue(qty);

        if (parseInt(qty) > stock) {
            qtyInput.value = formatNumber(stock);
        }
    }
</script>

<!-- Refresh page -->
<script>
    function refreshPage() {
        location.reload();
    }
</script>

<!-- Kode untuk tambah data -->
<script>
    $(document).on('click', '.btn-pilih', function(event) {
        event.preventDefault();
        event.stopPropagation();

        // Tampilkan indikator proses saat tombol diklik
        $('#loading-indicator').show();

        // Tambahkan kelas blur pada tabel
        $('table').addClass('blur');

        var inv = $(this).data('inv'); // Ganti 'data-id-produk' menjadi 'data-inv'
        var produk = $(this).data('id-produk');
        var namaProduk = $(this).data('nama-produk');
        var hargaProduk = $(this).data('harga');

        saveData(inv, produk, namaProduk, hargaProduk);
    });

    function saveData(inv, produk, namaProduk, hargaProduk) {
        // Nonaktifkan tombol yang dipilih segera setelah diklik
        $('.btn-pilih[data-id-produk="' + produk + '"]').prop('disabled', true);

        $.ajax({
            url: 'simpan-data-tmp.php',
            type: 'POST',
            data: {
                inv: inv,
                produk: produk,
                namaProduk: namaProduk,
                hargaProduk: hargaProduk
            },
            timeout: 7000,
            success: function(response) {
                console.log('Data berhasil disimpan.');

                // Berikan jeda waktu 5 detik sebelum menonaktifkan tombol
                setTimeout(function() {
                    // Sembunyikan indikator proses setelah selesai jeda waktu
                    $('#loading-indicator').hide();

                    // Hilangkan kelas blur dari tabel setelah menonaktifkan tombol
                    $('table').removeClass('blur');
                }, 5000);
            },
            error: function(xhr, status, error) {
                if (status === 'timeout') {
                    console.error('Koneksi timeout setelah 7 detik.');
                    // Tindakan yang perlu diambil jika koneksi timeout
                } else {
                    console.error('Terjadi kesalahan saat menyimpan data:', error);
                }

                // Sembunyikan indikator proses jika terjadi kesalahan atau timeout
                $('#loading-indicator').hide();

                // Hilangkan kelas blur dari tabel jika terjadi kesalahan atau timeout
                $('table').removeClass('blur');
            },
            complete: function() {
                // Sembunyikan indikator proses setelah selesai
                $('#loading-indicator').hide();
            }
        });
    }
</script>


<!-- Format Number Harga -->
<script>
    function formatNumberHarga(input) {
        // Mengambil nilai input
        var inputValue = input.value;

        // Menghapus semua karakter kecuali angka dan tanda koma (,)
        var cleanedValue = inputValue.replace(/[^0-9,]/g, '');

        // Menghapus tanda koma (,) tambahan yang mungkin ada
        cleanedValue = cleanedValue.replace(/,/g, '');

        // Mengubah nilai input menjadi format angka yang sesuai
        var formattedValue = Number(cleanedValue).toLocaleString('en-US');

        // Memasukkan kembali nilai yang telah diformat ke dalam input
        input.value = formattedValue;
    }
</script>

<!-- Kode Diskon -->
<script>
    function validasiDiskon(input) {
        // Hapus karakter selain angka, titik (.), dan tanda persen (%)
        input.value = input.value.replace(/[^0-9.%]/g, '');

        // Hapus tanda persen (%) yang ada di akhir input
        if (input.value.endsWith('%')) {
            input.value = input.value.slice(0, -1);
        }

        // Pisahkan angka sebelum dan sesudah titik
        var parts = input.value.split('.');
        var angkaDepan = parts[0] || ""; // Bagian sebelum titik atau string kosong jika tidak ada
        var angkaBelakang = parts[1] || ""; // Bagian setelah titik atau string kosong jika tidak ada

        // Hanya tambahkan titik dan angka desimal jika angkaBelakang ada
        if (angkaBelakang) {
            // Format ulang nilai diskon dengan satu angka desimal
            if (angkaBelakang.length > 1) {
                angkaBelakang = angkaBelakang.substring(0, 1);
            }
            input.value = angkaDepan + "." + angkaBelakang;
        }

        // Konversi input ke dalam format angka dengan satu angka desimal
        var nilaiDiskon = parseFloat(input.value);

        // Batasi nilai diskon maksimum menjadi 100
        if (!isNaN(nilaiDiskon) && nilaiDiskon > 100) {
            input.value = "100";
        }
    }
</script>

<!-- date picker with flatpick -->
<script type="text/javascript">
    flatpickr("#date", {
        dateFormat: "d/m/Y",
        defaultDate: "today"
    });
</script>
<!-- end date picker -->

<!-- Compress image  -->
<script src="assets/js/new-compress-image.js"></script>

<script>
    $(document).ready(function() {
        $('.cancel-order-btn').on('click', function() {
            var id_inv = $(this).data('id');

            // Menampilkan SweetAlert konfirmasi
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin membatalkan pesanan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, batalkan!',
                cancelButtonText: 'Tidak, batalkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika pengguna mengkonfirmasi, lakukan AJAX
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/komplain-cancel-nonppn.php', // Pastikan file PHP ini benar
                        data: {
                            id_inv: id_inv
                        },
                        success: function(response) {
                            console.log("Raw Response:", response);
                            console.log('oke');
                            // Menampilkan pesan sukses setelah berhasil
                            Swal.fire(
                                'Dibatalkan!',
                                'Pesanan telah dibatalkan.',
                                'success'
                            );
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", status, error);
                            console.log('gagal');
                            // Menampilkan pesan error
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat membatalkan pesanan.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>
