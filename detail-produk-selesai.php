<?php
    $page  = 'transaksi';
    $page2 = 'spk';
    require_once "akses.php";
    require_once "function/class-spk.php";
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
    <?php include "page/head.php";?>

    <style type="text/css">
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        .col-5 {
            flex: 0 0 50%; /* Gunakan 50% dari lebar kolom saat tampilan mobile */
            max-width: 50%;
        }

        .col-7 {
            flex: 0 0 50%; /* Gunakan 50% dari lebar kolom saat tampilan mobile */
            max-width: 50%;
        }

        p {
            white-space: nowrap; /* Mencegah teks berjalan ke baris baru */
            text-overflow: ellipsis; /* Menggantikan teks yang terpotong dengan elipsis (...) jika terlalu panjang */
        }

        .col-12 {
            padding: 0 0 0 0px !important;
        }

        @media only screen and (max-width: 500px) {
            body {
                font-size: 14px;
            }

            .btn-mobile{
                width: 100%;
            }

            .mobile {
                display: none;
            }

            .mobile-text {
                text-align: left !important;
            }

            #dt-search-0 {
                width: 100%;
            }

            .col-md-auto {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <!-- nav header -->
    <?php include "page/nav-header.php"?>
    <!-- end nav header -->

    <!-- sidebar -->
    <?php include "page/sidebar.php";?>
    <!-- end sidebar -->
    <main id="main" class="main">
        <!-- SWEET ALERT -->
        <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
        <!-- END SWEET ALERT -->
        <section>
            <?php
                $id_inv = decrypt($_GET['id'], $key_global);
                $jenis_inv = $_GET['jenis'];

                // Generate a secure random token
                $nonce = bin2hex(random_bytes(16));
                $_SESSION['nonce_token'] = $nonce; 

                // Kondisi untuk menampilkan data berdasarkan jenis invoice
                $modal_edit_inv = "";
                $action_proforma = '';
                if($jenis_inv == 'nonppn'){
                    $label_jenis = 'NON PPN';
                    require_once 'query/data-inv-nonppn.php';
                    require_once 'query/data-spk-proforma.php';
                    require_once 'query/jenis-cb-proforma.php';
                    $action_proforma = 'proses/proses-invoice-nonppn.php';
                    $cetak_inv = 'cetak-inv-nonppn-reg.php';
                    $cetak_pdf = 'generate_pdf_nonppn.php';
                    $sql_total_inv = mysqli_query($connect, "SELECT total_inv FROM inv_nonppn WHERE id_inv_nonppn = '$id_inv'");
                    if($status_transaksi_inv != 'Transaksi Selesai'){
                        ?>
                            <script>
                                window.location.href = 'invoice-reguler-selesai.php';
                            </script>
                        <?php
                    }
                } else if ($jenis_inv == 'ppn'){
                    $label_jenis = 'PPN';
                    require_once 'query/data-inv-ppn.php';
                    require_once 'query/data-spk-proforma.php';
                    require_once 'query/jenis-cb-proforma.php';
                    $action_proforma = 'proses/proses-invoice-ppn.php';
                    $cetak_inv = 'cetak-inv-ppn.php';
                    $cetak_pdf = 'generate_pdf_ppn.php';
                    $sql_total_inv = mysqli_query($connect, "SELECT total_inv FROM inv_ppn WHERE id_inv_ppn = '$id_inv'");
                    if($status_transaksi_inv != 'Transaksi Selesai'){
                        ?>
                            <script>
                                window.location.href = 'invoice-reguler-selesai.php';
                            </script>
                        <?php
                    }
                } else if ($jenis_inv == 'bum') {
                    $label_jenis = 'BUM';
                    require_once 'query/data-inv-bum.php';
                    require_once 'query/data-spk-proforma.php';
                    require_once 'query/jenis-cb-proforma.php';
                    $action_proforma = 'proses/proses-invoice-bum.php';
                    $cetak_inv = 'cetak-inv-bum-reg.php';
                    $cetak_pdf = 'generate_pdf_bum.php';
                    $sql_total_inv = mysqli_query($connect, "SELECT total_inv FROM inv_bum WHERE id_inv_bum = '$id_inv'");
                    if($status_transaksi_inv != 'Transaksi Selesai'){
                        ?>
                            <script>
                                window.location.href = 'invoice-reguler-selesai.php';
                            </script>
                        <?php
                    }
                } else {
                    header("Location:404.php");
                }
            ?>
            <div class="card shadow p-2">
                <div class="card-header text-center">
                    <h5><strong>DETAIL INVOICE <?php echo $label_jenis ?></strong></h5>
                </div>
                <!-- Start Row -->
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div class="card-body p-3 border">
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Tgl. Pesanan</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['tgl_pesanan'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. SPK</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7 overflow-auto">
                                    <?php
                                        $no = 1;
                                        while ($data_spk = mysqli_fetch_array($query_data_spk)) {
                                            $id_cs = $data_spk['id_customer'];
                                        ?>
                                            <p><?php echo $no; ?>. (<?php echo $data_spk['tgl_pesanan'] ?>) / <?php if (!empty($data_spk['no_po'])) {
                                                                                                                echo "(" . $data_spk['no_po'] . ")/";
                                                                                                            } ?> (<?php echo $data_spk['no_spk'] ?>)</p>
                                        <?php $no++; ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. Invoice</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['no_inv'] ?>
                                </div>
                            </div>
                            <?php
                                if ($data_inv['no_po'] != '') {
                                    echo '
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">No. PO</p>
                                            <p style="float: right;">:</p>
                                        </div>
                                        <div class="col-7">
                                            ' . $data_inv['no_po'] . '
                                        </div>
                                    </div>';
                                }
                            ?>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Tgl. Invoice</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['tgl_inv'] ?>
                                </div>
                            </div>
                            <?php
                                if ($data_inv['tgl_tempo'] != '') {
                                        echo '
                                        <div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">Tgl. Tempo</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data_inv['tgl_tempo'] . '
                                            </div>
                                        </div>';
                                    }
                            ?>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Jenis Invoice</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['kategori_inv'] ?>
                                </div>
                            </div>
                            <?php
                                if ($data_inv['kategori_inv'] == 'Spesial Diskon') {
                                    echo '<div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">Spesial Diskon</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data_inv['sp_disc'] . ' %
                                            </div>
                                        </div>';
                                }
                            ?>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Order Via</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['order_by'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Sales</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['nama_sales'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card-body p-3 border" style="min-height: 234px;">
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Pelanggan</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['nama_cs'] ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Pelanggan Inv</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['cs_inv'] ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Alamat</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php 
                                        if($data_inv['alamat_inv'] == ''){
                                            echo $data_inv['alamat'];
                                        } else {
                                            echo $data_inv['alamat_inv']; 
                                        }
                                    ?>
                                </div>
                            </div>
                            <?php
                                if ($data_inv['note_inv'] != '') {
                                        echo '
                                        <div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Note Invoice</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data_inv['note_inv'] . '
                                            </div>
                                        </div>';
                                    }
                            ?>

                            <?php
                                $status_kirim = mysqli_query($connect, "SELECT id_status_kirim, jenis_pengiriman, dikirim_ekspedisi, jenis_penerima, no_resi,dikirim_driver, dikirim_oleh, penanggung_jawab FROM status_kirim WHERE id_inv = '$id_inv'");
                                $data_status_kirim = mysqli_fetch_array($status_kirim);
                                $id_status_kirim = $data_status_kirim['id_status_kirim'];
                                $jenis_pengiriman =  $data_status_kirim['jenis_pengiriman'];
                                $ekspedisi = $data_status_kirim['dikirim_ekspedisi'];
                                $driver = $data_status_kirim['dikirim_driver'];
                                $no_resi = $data_status_kirim['no_resi'];

                                $ekspedisi_kirim =  mysqli_query($connect, "SELECT 
                                                                            sk.jenis_pengiriman, sk.dikirim_ekspedisi, sk.jenis_penerima, ex.nama_ekspedisi
                                                                            FROM status_kirim AS sk
                                                                            JOIN ekspedisi ex ON (sk.dikirim_ekspedisi = ex.id_ekspedisi)
                                                                            WHERE sk.dikirim_ekspedisi = '$ekspedisi'");
                                $data_ekspedisi_kirim = mysqli_fetch_array($ekspedisi_kirim);
                                
                                $driver_kirim =  mysqli_query($connect, "SELECT sk.jenis_pengiriman, sk.dikirim_driver, us.nama_user 
                                                                            FROM status_kirim AS sk
                                                                            LEFT JOIN $database2.user us ON (sk.dikirim_driver = us.id_user)
                                                                            WHERE sk.dikirim_driver = '$driver'");
                                $data_driver_kirim = mysqli_fetch_array($driver_kirim);
                                $nama_driver = $data_driver_kirim['nama_user'] ?? '';
                                $nama_driver = str_replace(' ', '_', $nama_driver);

                                $penerima =  mysqli_query($connect,"SELECT id_inv, nama_penerima 
                                                                FROM inv_penerima
                                                                WHERE id_inv = '$id_inv'");
                                $data_penerima = mysqli_fetch_array($penerima);
                                $nama_penerima = "";
                                if($data_penerima['nama_penerima'] != ""){
                                    $nama_penerima = $data_penerima['nama_penerima'];
                                }
                            ?>
                            
                            <?php
                                if ($jenis_pengiriman == 'Ekspedisi') {
                                    ?> 
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">Jenis Pengiriman</p>
                                            <p style="float: right;"> :</p>
                                        </div>
                                        <div class="col-7">
                                            <?php echo $data_ekspedisi_kirim['jenis_penerima'] ?> (<?php echo $data_ekspedisi_kirim['nama_ekspedisi'] ?>)
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">No. Resi</p>
                                            <p style="float: right;"> :</p>
                                        </div>
                                        <div class="col-7">
                                            <?php 
                                                if($no_resi != ''){
                                                    echo $no_resi; 
                                                } else {
                                                    echo "<b>No Resi Belum Di Input</b>";
                                                }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                  }else if ($jenis_pengiriman == 'Diambil Langsung'){
                                    ?>
                                        <div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Jenis Pengiriman</p>
                                                <p style="float: right;"> :</p>
                                            </div>
                                            <div class="col-7">
                                                <?php echo $jenis_pengiriman ?>
                                            </div>
                                        </div>
                                    <?php
                                    if(!empty($data_status_kirim['jenis_penerima'])){
                                        ?>
                                            <div class="row">
                                                <div class="col-5">
                                                    <p style="float: left;">Jenis Penerima</p>
                                                    <p style="float: right;"> :</p>
                                                </div>
                                                <div class="col-7">
                                                    <?php echo $data_status_kirim['jenis_penerima'] ?> 
                                                    <?php  
                                                        if(!empty($data_ekspedisi_kirim['nama_ekspedisi'])){
                                                            ?>
                                                                (<?php echo $data_ekspedisi_kirim['nama_ekspedisi'] ?>)
                                                            <?php
                                                        }
                                                    ?>
                                                    
                                                </div>
                                            </div>
                                            <?php  
                                                if(!empty($data_penerima['nama_penerima'])){
                                                    ?>
                                                         <div class="row">
                                                            <div class="col-5">
                                                                <p style="float: left;">Nama Penerima</p>
                                                                <p style="float: right;"> :</p>
                                                            </div>
                                                            <div class="col-7">
                                                                <?php echo $data_penerima['nama_penerima'] ?>
                                                            </div>
                                                        </div>
                                                    <?php
                                                }
                                            
                                            ?>
                                        <?php
                                    }
                                } else {
                                    if($data_status_kirim['jenis_penerima'] == "Customer" && $jenis_pengiriman == 'Driver'){
                                        ?>
                                        <div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Jenis Pengiriman</p>
                                                <p style="float: right;"> :</p>
                                            </div>
                                            <div class="col-7">
                                                <?php echo $jenis_pengiriman ?> (<?php echo $data_driver_kirim['nama_user'] ?>)
                                            </div>
                                        </div>
                                        <?php
                                        if(!empty($data_status_kirim['jenis_penerima'])){
                                            ?>
                                                <div class="row">
                                                    <div class="col-5">
                                                        <p style="float: left;">Jenis Penerima</p>
                                                        <p style="float: right;"> :</p>
                                                    </div>
                                                    <div class="col-7">
                                                        <?php echo $data_status_kirim['jenis_penerima'] ?> (<?php echo $nama_penerima ?>)
                                                    </div>
                                                </div>
                                            <?php
                                        }
                                    } else if ($data_status_kirim['jenis_penerima'] == "Ekspedisi" && $jenis_pengiriman == 'Driver'){
                                        ?>
                                            <div class="row mt-2">
                                                <div class="col-5">
                                                    <p style="float: left;">Jenis Pengiriman</p>
                                                    <p style="float: right;"> :</p>
                                                </div>
                                                <div class="col-7">
                                                    <?php echo $jenis_pengiriman ?> (<?php echo $data_driver_kirim['nama_user'] ?>)
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5">
                                                    <p style="float: left;">Jenis Penerima</p>
                                                    <p style="float: right;"> :</p>
                                                </div>
                                                <div class="col-7">
                                                    <?php echo $data_ekspedisi_kirim['jenis_penerima'] ?> (<?php echo $data_ekspedisi_kirim['nama_ekspedisi'] ?>)
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5">
                                                    <p style="float: left;">No. Resi</p>
                                                    <p style="float: right;"> :</p>
                                                </div>
                                                <div class="col-7">
                                                    <?php 
                                                        if($no_resi != ''){
                                                            echo $no_resi; 
                                                        } else {
                                                            echo "<b>No Resi Belum Di Input</b>";
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                        <?php

                                    }
                                }
                            ?>
                            <?php
                                if ($data_status_kirim['jenis_penerima'] == 'Ekspedisi'){
                                    if($no_resi != ''){
                                        if ($data_inv['free_ongkir'] == 0) {
                                            echo '<div class="row">
                                                    <div class="col-5">
                                                        <p style="float: left;">Ongkir</p>
                                                        <p style="float: right;"> :</p>
                                                    </div>
                                                    <div class="col-7">
                                                        ' . number_format($data_inv['ongkir'],0,'.','.') . '
                                                    </div>
                                                </div>';
                                        } else {
                                            echo '<div class="row">
                                                    <div class="col-5">
                                                        <p style="float: left;">Ongkir</p>
                                                        <p style="float: right;"> :</p>
                                                    </div>
                                                    <div class="col-7">
                                                        ' . number_format($data_inv['ongkir_free'],0,'.','.') . ' (Free Ongkir)
                                                    </div>
                                                </div>';  
                                        }
                                    } else {
                                        echo '<div class="row">
                                                    <div class="col-5">
                                                        <p style="float: left;">Ongkir</p>
                                                        <p style="float: right;"> :</p>
                                                    </div>
                                                    <div class="col-7">
                                                        <b>Ongkir Belum Di Input</b>
                                                    </div>
                                                </div>';  
                                    }
                                }
                            ?>
                            <?php
                                if (!empty($data_status_kirim['dikirim_oleh'])) {
                                    echo '<div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">Dikirim Oleh</p>
                                                <p style="float: right;"> :</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data_status_kirim['dikirim_oleh'] . '
                                            </div>
                                        </div>';
                                    }
                            ?>
                            <?php
                                if (!empty($data_status_kirim['penanggung_jawab'])) {
                                    echo '  <div class="row">
                                                <div class="col-5">
                                                    <p style="float: left;">PJ. Paket Kirim</p>
                                                    <p style="float: right;"> :</p>
                                                </div>
                                                <div class="col-7">
                                                    ' . $data_status_kirim['penanggung_jawab'] . '
                                                </div>
                                            </div>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <!-- End Row -->
                 <!-- Start Row -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="text-start">
                            <a href="invoice-reguler-selesai.php" class="btn btn-warning btn-detail mb-2 btn-mobile">
                                <i class="bi bi-arrow-left"></i>
                                Halaman Sebelumnya
                            </a> 
                             <!-- Button modal Bukti Terima -->
                             <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#buktiKirim">
                                <i class="bi bi-file-earmark-image"></i> Bukti Terima
                            </button>
                            <!-- End Button Modal Bukti Terima -->
                            <!-- Button Cetak Invoice -->
                            <?php
                                if ($role == "Super Admin" || $role == "Admin Penjualan" || $role == "Manager Gudang") {
                                    ?>
                                        <a href="<?php echo $cetak_inv ?>?id=<?php echo encrypt($id_inv, $key_global)?>" class="btn btn-secondary mb-2 btn-mobile"><i class="bi bi-printer-fill"></i> Cetak Invoice</a>
                            <?php } ?>
                             <!-- End Button Cetak Invoice -->
                        </div>
                    </div>
                </div>
                <!-- End Row -->
                <div class="table-responsive">
                    <div class="mb-3">
                        <button type="button" class="btn btn-secondary p-2 btn-mobile">Nama Petugas : <?php echo $petugas ?></button>
                    </div>
                    <div class="mb-3">
                    <?php  
                        $cashback_values = [];
                        while($data_ket_cb =  mysqli_fetch_array($ket_cb)){
                            $cashback_values[] = $data_ket_cb['ket_cashback']; // Menyimpan setiap nilai ke dalam array
                        }
                        $cek_ket_cb = implode(", ", $cashback_values); // Menggabungkan semua nilai menjadi satu string, dipisahkan dengan koma
                        if($status_cb == '1'){
                            if($jenis_cb == ''){
                                ?>
                                    <button type="button" class="btn btn-primary p-2 btn-mobile">Jenis Cashback : <?php echo "Jenis Cashback Belum Dipilih"; ?></button> 
                                <?php
                            } else {
                                ?>
                                    <button type="button" class="btn btn-primary p-2 btn-mobile">Jenis Cashback : <?php echo $cek_ket_cb; ?></button> 
                                <?php
                            }
                        } else {
                            ?>
                                <button type="button" class="btn btn-primary p-2 btn-mobile">Jenis Cashback : Tidak Ada Cashback</button>
                            <?php
                        }
                    ?>
                    </div>
                    <table class="table table-striped table-bordered" id="table3">
                        <thead>
                            <tr class="text-white" style="background-color: #051683;">
                                <th class="text-center p-3 text-nowrap" style="width:20px">No</th>
                                <th class="text-center p-3 text-nowrap" style="width:100px">No. SPK</th>
                                <th class="text-center p-3 text-nowrap" style="width:200px">Nama Produk</th>
                                <th class="text-center p-3 text-nowrap" style="width:100px">Merk</th>
                                <th class="text-center p-3 text-nowrap" style="width:80px">Qty Order</th>
                                <th class="text-center p-3 text-nowrap" style="width:100px">Satuan</th>
                                <th class="text-center p-3 text-nowrap" style="width:100px">Harga</th>
                                <?php
                                    if ($total_data_status_trx_1 != 0) {
                                        if ($data_cek['kategori_inv'] == 'Diskon') {
                                            ?>
                                                <th class="text-center p-3 text-nowrap" style="width:100px">Diskon</th> 
                                            <?php
                                        }
                                    }
                                    if (in_array('Per Barang', $cashback_values)){
                                        ?>
                                            <th class="text-center p-3 text-nowrap" style="width:100px">Diskon CB</th> 
                                        <?php
                                    }
                                ?>      
                                <th class="text-center p-3 text-nowrap" style="width:80px">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                require_once 'query/data-produk-proforma.php';
                                $total_invoice = 0;
                                $no = 1;
                                while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                                    $namaProduk = $data_trx['nama_produk'];
                                    $id_produk = $data_trx['id_produk'];
                                    $satuan = $data_trx['satuan'];
                                    $nama_merk = $data_trx['merk_produk'];
                                    $disc = $data_trx['disc'];
                                    $disc_cb = $data_trx['disc_cb'];
                                    $total_harga = $data_trx['total_harga'];
                                    $sub_total += $total_harga;

                                    $satuan_produk = '';
                                    $id_produk_substr = substr($id_produk, 0, 2);
                                    if ($id_produk_substr == 'BR') {
                                        $satuan_produk = $satuan;
                                    } else {
                                        $satuan_produk = 'Set';
                                    }
                            ?>
                                <tr>
                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data_trx['no_spk']; ?></td>
                                    <td class="text-nowrap"><?php echo $data_trx['nama_produk_spk'] ?></td>
                                    <td class="text-center"><?php echo $nama_merk ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['qty'],0,'.','.') ?></td>
                                    <td class="text-center text-nowrap"><?php echo $satuan_produk ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['harga'],0,'.','.') ?></td>
                                    <?php
                                        if ($total_data_status_trx_1 != 0) {
                                            if ($data_cek['kategori_inv'] == 'Diskon') {
                                                echo "<td class='text-end'>" . $disc . "</td>";
                                            }
                                        }
                                        if(in_array('Per Barang', $cashback_values)){
                                            ?>
                                                <td class='text-end'><?php echo $disc_cb ?></td>
                                            <?php
                                        }
                                    ?>
                                    
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['total_harga']) ?></td>
                                </tr>
                            <?php $no++; ?>
                            <?php } ?>
                        </tbody>
                    </table> 
                </div>
                <!-- Modal Bukti Kirim -->
                <?php require_once 'modal/bukti-kirim.php' ?>
                <!-- End Modal Bukti Kirim -->
            </div>
            <!-- Modal edit data produk -->
            <?php require_once 'modal/transaksi-selesai.php' ?>
            <!-- End Modal edit data produk -->
        </section>
    </main>
    <!-- Footer -->
    <?php include "page/footer.php"?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <?php include "page/script.php"?>
</body>
</html>