<?php
$page = 'transaksi'; 
$page2 = 'spk';
include "akses.php";
include "function/class-spk.php";
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
            overflow: hidden;
            text-overflow: ellipsis; /* Menggantikan teks yang terpotong dengan elipsis (...) jika terlalu panjang */
        }
        @media only screen and (max-width: 500px) {
            body {
                font-size: 14px;
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
        <section>
            <div class="card shadow p-2">
                <div class="card-header text-center">
                    <h5>
                        <strong>DETAIL INVOICE NONPPN</strong>
                    </h5>
                </div>
                <?php
                    include "koneksi.php";
                    $id_inv = base64_decode($_GET['id']);
                    $sql = "SELECT
                            nonppn.id_inv_nonppn,
                            nonppn.no_inv,
                            nonppn.tgl_inv,
                            nonppn.kategori_inv,
                            nonppn.cs_inv,
                            nonppn.alamat_inv,
                            nonppn.sp_disc,
                            nonppn.tgl_tempo,
                            nonppn.ongkir,
                            nonppn.note_inv,
                            sr.id_user, sr.id_customer, sr.no_spk, sr.no_po, sr.tgl_pesanan, sr.petugas,
                            cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales
                            FROM inv_nonppn AS nonppn
                            JOIN spk_reg sr ON (nonppn.id_inv_nonppn = sr.id_inv)
                            JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                            JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                            JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                            WHERE nonppn.id_inv_nonppn = '$id_inv'";
                    $query = mysqli_query($connect, $sql);
                    $query2 = mysqli_query($connect, $sql);
                    $data = mysqli_fetch_array($query);
                    $sp_disc = $data['sp_disc'];
                    $ongkir = $data['ongkir'];
                    $petugas = $data['petugas'];
                ?>
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div class="card-body p-3 border">
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Tgl. Pesanan</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['tgl_pesanan'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. SPK</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php 
                                        $no = 1;
                                        while($data2 = mysqli_fetch_array($query2)){
                                                $id_inv = $data2['id_inv_nonppn'];
                                                $kat_inv = $data2['kategori_inv'];
                                                $id_cs = $data2['id_customer'];
                                                $tgl_pesanan = $data2['tgl_pesanan'];
                                                $no_spk = $data2['no_spk'];
                                                $no_po = $data2['no_po'];
                                            ?>
                                   
                                    <p>
                                        <?php echo $no ?>. (<?php echo $tgl_pesanan ?>) / <?php if (!empty($no_po)) { echo "(" . $no_po . ") /";} else {} ?>
                                        (<?php echo $no_spk ?>)
                                    </p>
                                    <?php $no++ ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. Invoice</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['no_inv'] ?>
                                </div>
                            </div>
                            <?php
                               if ($data['no_po'] != '') {
                                    echo '
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">No. PO</p>
                                            <p style="float: right;">:</p>
                                        </div>
                                        <div class="col-7">
                                            ' . $data['no_po'] . '
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
                                    <?php echo $data['tgl_inv'] ?>
                                </div>
                            </div>
                            <?php
                                if ($data['tgl_tempo'] != '') {
                                        echo '
                                        <div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">Tgl. Tempo</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data['tgl_tempo'] . '
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
                                    <?php echo $data['kategori_inv'] ?>
                                </div>
                            </div>
                            <?php
                                if ($data['kategori_inv'] == 'Spesial Diskon') {
                                    echo '<div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">Spesial Diskon</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data['sp_disc'] . ' %
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
                                    <?php echo $data['order_by'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Sales</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['nama_sales'] ?>
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
                                    <?php echo $data['nama_cs'] ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Pelanggan Inv</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['cs_inv'] ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Alamat</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php 
                                        if($data['alamat_inv'] == ''){
                                            echo $data['alamat'];
                                        } else {
                                            echo $data['alamat_inv']; 
                                        }
                                    ?>
                                </div>
                            </div>
                            <?php
                                if ($data['note_inv'] != '') {
                                        echo '
                                        <div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Note Invoice</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data['note_inv'] . '
                                            </div>
                                        </div>';
                                    }
                            ?>
                            <?php
                                if ($data['ongkir'] != 0) {
                                    echo '<div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Ongkir</p>
                                                <p style="float: right;"> :</p>
                                            </div>
                                            <div class="col-7">
                                                ' . number_format($data['ongkir']) . '
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
                                                                            JOIN user us ON (sk.dikirim_driver = us.id_user)
                                                                            WHERE sk.dikirim_driver = '$driver'");
                                $data_driver_kirim = mysqli_fetch_array($driver_kirim);

                                $penerima =  mysqli_query($connect,"SELECT id_inv, nama_penerima 
                                                                FROM inv_penerima
                                                                WHERE id_inv = '$id_inv'");
                                $data_penerima = mysqli_fetch_array($penerima);
                                $nama_penerima = "";
                                if($nama_penerima != ""){
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
                                            <?php echo $no_resi ?>
                                        </div>
                                    </div>
                                    <?php
                                  }else if($jenis_pengiriman == 'Diambil Langsung'){
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
                                    if($data_status_kirim['jenis_penerima'] == "Customer" || $jenis_pengiriman == 'Driver'){
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
            </div>
            <!-- Tampil data -->
            <div class="card shadow">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <div class="text-start mb-3">
                            <a href="invoice-reguler-dikirim.php?sort=baru" class="btn btn-warning btn-detail mb-2">
                                <i class="bi bi-arrow-left"></i>
                                Halaman Sebelumnya
                            </a>
                            <?php  
                                include "koneksi.php";
                                $id_role = $_SESSION['tiket_role'];
                                $sql_role = "SELECT * FROM user_role WHERE id_user_role='$id_role'";
                                $query_role = mysqli_query($connect, $sql_role) or die(mysqli_error($connect));
                                $data_role = mysqli_fetch_array($query_role);
                            ?>
                            <?php
                            $id_inv_nonppn = base64_decode($_GET['id']);
                            $sql_cek = "SELECT
                                            nonppn.id_inv_nonppn, kategori_inv,
                                            sk.jenis_pengiriman,
                                            sk.jenis_penerima
                                        FROM inv_nonppn AS nonppn
                                        LEFT JOIN status_kirim sk ON (sk.id_inv = nonppn.id_inv_nonppn)
                                        WHERE nonppn.id_inv_nonppn = '$id_inv_nonppn'";
                            $query_cek = mysqli_query($connect, $sql_cek);
                            $data_cek = mysqli_fetch_array($query_cek);
                            $total_data = mysqli_num_rows($query_cek);
                            ?>
                            <?php
                              if ($data_role['role'] == "Super Admin" || $data_role['role'] == "Admin Penjualan") {
                                ?>
                                    <?php
                                        include "koneksi.php";
                                        $sql_bukti_kirim = "SELECT id_inv FROM inv_bukti_terima WHERE id_inv = '$id_inv_nonppn'";
                                        $query_bukti_kirim = mysqli_query($connect, $sql_bukti_kirim);
                                        $total_row = mysqli_num_rows($query_bukti_kirim);
                                        if ($data_cek['jenis_pengiriman'] == 'Ekspedisi' && $data_cek['jenis_penerima'] == 'Ekspedisi') {
                                            ?>
                                                <button class="btn btn-primary btn-detail mb-2" data-bs-toggle="modal" data-bs-target="#buktiKirim">
                                                    <i class="bi bi-file-earmark-image"></i> Bukti Kirim
                                                </button>

                                                <button class="btn btn-secondary btn-detail mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#DiterimaEx">
                                                    <i class="bi bi-send"></i>
                                                    Diterima
                                                </button>

                                                <button class="btn btn-warning btn-detail mb-2" data-bs-toggle="modal"
                                                    data-bs-target="#editOngkir">
                                                    <i class="bi bi-pencil"></i>
                                                    Edit Ongkir Dan No. Resi
                                                </button>
                                            <?php 
                                        } else if ($total_row > 0 && $data_cek['jenis_pengiriman'] == 'Driver' && $data_cek['jenis_penerima'] == 'Ekspedisi'){
                                            echo '
                                            <button class="btn btn-primary btn-detail mb-2" data-bs-toggle="modal" data-bs-target="#buktiKirim">
                                                <i class="bi bi-file-earmark-image"></i> Bukti Kirim
                                            </button>

                                            <button class="btn btn-secondary btn-detail mb-2" data-bs-toggle="modal"
                                                data-bs-target="#DiterimaEx">
                                                <i class="bi bi-send"></i>
                                                Diterima
                                            </button>
                                            ';
                                        } else if ($data_cek['jenis_pengiriman'] == 'Driver' && $data_cek['jenis_penerima'] == '') {
                                            ?>
                                                <!-- Button trigger modal -->
                                                <button type="button" class="btn btn-secondary mb-2" data-bs-toggle="modal" data-bs-target="#ubahDriver">
                                                    <i class="bi bi-arrow-repeat"></i> Ubah Driver
                                                </button>
                                            <?php
                                        } else if ($data_cek['jenis_pengiriman'] == 'Diambil Langsung' && $data_cek['jenis_penerima'] == 'Customer'){
                                            ?>
                                                 <!-- Button trigger modal -->
                                                <button type="button" class="btn btn-secondary mb-2" data-bs-toggle="modal" data-bs-target="#diambil">
                                                    <i class="bi bi-arrow-repeat"></i> Diambil Oleh
                                                </button>
                                            <?php
                                        }
                                    ?>

                                    

                                    <!-- Ubah Jenis Pengiriman -->
                                    <a href="proses/proses-ubah-pengiriman.php?id_status_kirim=<?php echo base64_encode($id_status_kirim) ?>&&id_inv=<?php echo base64_encode($id_inv) ?>" class="btn btn-primary mb-2 update-data">
                                        <i class="bi bi-arrow-repeat"></i> Ubah Jenis Pengiriman
                                    </a>

                                    <?php
                                        // Eksekusi query SQL
                                        $result = mysqli_query($connect, "SELECT 
                                                                nonppn.id_inv_nonppn,
                                                                trx.status_trx
                                                                FROM inv_nonppn AS nonppn
                                                                JOIN spk_reg sr ON (nonppn.id_inv_nonppn = sr.id_inv)
                                                                JOIN transaksi_produk_reg trx ON(sr.id_spk_reg = trx.id_spk)
                                                                WHERE nonppn.id_inv_nonppn = '$id_inv_nonppn'  ORDER BY no_spk ASC");
                                        $tombolDitampilkan = false;
                                        // Inisialisasi variabel untuk status kosong
                                        while ($row = mysqli_fetch_array($result)) {
                                            $status_trx = $row['status_trx'];
                                        ?>
                                            <?php
                                            if ($total_data != 0 && $status_trx != 0 && !$tombolDitampilkan) {
                                                echo ' 
                                                        <input type="hidden" name="id_spk_reg" value="' . base64_encode($id_inv_nonppn) . '">
                                                        <a href="cetak-inv-nonppn-reg.php?id=' . base64_encode($id_inv_nonppn) . '" class="btn btn-secondary mb-2"><i class="bi bi-printer-fill"></i> Cetak Invoice</a>
                                                        <input type="hidden" name="id_spk_reg" value="' . base64_encode($id_inv_nonppn) . '">
                                                        <a href="generate_pdf_nonppn.php?id=' . base64_encode($id_inv_nonppn) . '" class="btn btn-info mb-2"><i class="bi bi-file-pdf"></i> Download PDF</a>
                                                        ';

                                                // Set variabel $tombolDitampilkan menjadi true
                                                $tombolDitampilkan = true;
                                            }
                                        ?>
                                    <?php } ?>
                                    <?php  
                                        $cek_button = mysqli_query($connect, "  SELECT 
                                                                                    id_inv_nonppn, kwitansi, surat_jalan
                                                                                FROM inv_nonppn 
                                                                                WHERE id_inv_nonppn = '$id_inv_nonppn'");
                                        $data_button = mysqli_fetch_array($cek_button);
                                        $id_inv_nonppn = $data_button['id_inv_nonppn'];
                                        $kwitansi = $data_button['kwitansi'];
                                        $surat_jalan = $data_button['surat_jalan'];
                                        if($kwitansi == '1' && $surat_jalan == '1'){
                                            ?>
                                                <a href="cetak-kwitansi.php?id_inv=<?php echo base64_encode($id_inv_nonppn) ?>" class="btn btn-success mb-2"><i class="bi bi-printer-fill"></i> Cetak Kwitansi</a>
                                                <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#cetakSuratJalan"><i class="bi bi-printer-fill"></i> Cetak Surat Jalan</button>
                                            <?php
                                        } else if($kwitansi == '1' && $surat_jalan == '0'){
                                            ?>
                                                <a href="cetak-kwitansi.php?id_inv=<?php echo base64_encode($id_inv_nonppn) ?>" class="btn btn-success mb-2"><i class="bi bi-printer-fill"></i> Cetak Kwitansi</a>
                                            <?php
                                        } else if($kwitansi == '0' && $surat_jalan == '1'){
                                            ?>
                                                <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#cetakSuratJalan"><i class="bi bi-printer-fill"></i> Cetak Surat Jalan</button>
                                            <?php
                                        } else {

                                        }

                                    
                                    ?>
                                <?php
                              }
                            ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <button type="button" class="btn btn-secondary p-2">Nama Petugas : <?php echo $petugas ?></button>
                        <table class="table table-striped table-bordered" id="table2">
                            <?php
                                if ($total_data != 0) {
                                    if ($data_cek['kategori_inv'] != 'Diskon') {
                                        echo '
                                        <thead>
                                            <tr class="text-white" style="background-color: #051683;">
                                                <th class="text-center text-nowrap p-3" style="width:20px">No</th>
                                                <th class="text-center text-nowrap p-3" style="width:80px">No. SPK</th>
                                                <th class="text-center text-nowrap p-3" style="width:200px">Nama Produk</th>
                                                <th class="text-center text-nowrap p-3" style="width:100px">Satuan</th>
                                                <th class="text-center text-nowrap p-3" style="width:100px">Merk</th>
                                                <th class="text-center text-nowrap p-3" style="width:100px">Harga</th>
                                                <th class="text-center text-nowrap p-3" style="width:80px">Qty Order</th>
                                                <th class="text-center text-nowrap p-3" style="width:80px">Total</th>
                                            </tr>
                                        </thead>';
                                        } else {
                                            echo '
                                        <thead>
                                            <tr class="text-white" style="background-color: #051683;">
                                                <th class="text-center text-nowrap p-3" style="width:20px">No</th>
                                                <th class="text-center text-nowrap p-3" style="width:100px">No. SPK</th>
                                                <th class="text-center text-nowrap p-3" style="width:200px">Nama Produk</th>
                                                <th class="text-center text-nowrap p-3" style="width:100px">Satuan</th>
                                                <th class="text-center text-nowrap p-3" style="width:100px">Merk</th>
                                                <th class="text-center text-nowrap p-3" style="width:100px">Harga</th>
                                                <th class="text-center text-nowrap p-3" style="width:100px">Diskon</th>
                                                <th class="text-center text-nowrap p-3" style="width:80px">Qty Order</th>
                                                <th class="text-center text-nowrap p-3" style="width:80px">Total</th>
                                            </tr>
                                        </thead>';
                                    }
                                }
                            ?>
                            <tbody>
                                <?php
                                    include "koneksi.php";
                                    $year = date('y');
                                    $day = date('d');
                                    $month = date('m');
                                    $id_nonppn_decode = base64_decode($_GET['id']);
                                    $no = 1;
                                    $sub_total = 0;
                                    $sql_trx = "SELECT 
                                                    nonppn.id_inv_nonppn, 
                                                    nonppn.kategori_inv,
                                                    nonppn.sp_disc,   
                                                    spk.id_inv, 
                                                    spk.no_spk,
                                                    trx.id_transaksi,
                                                    trx.id_produk,
                                                    trx.nama_produk_spk,
                                                    trx.harga,
                                                    trx.qty,
                                                    trx.disc,
                                                    trx.total_harga,
                                                    trx.status_trx,
                                                    trx.created_date,
                                                    COALESCE(tpr.nama_produk, tpsm.nama_set_marwa, tpe.nama_produk, tpse.nama_set_ecat) AS nama_produk, 
                                                    COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                                                    COALESCE(mr_produk.nama_merk, mr_set.nama_merk, mr_produk_ecat.nama_merk, mr_set_ecat.nama_merk) AS merk_produk
                                                FROM inv_nonppn AS nonppn
                                                LEFT JOIN spk_reg spk ON (nonppn.id_inv_nonppn = spk.id_inv)
                                                LEFT JOIN transaksi_produk_reg trx ON trx.id_spk = spk.id_spk_reg
                                                LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                                                LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                                                LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                                LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                                LEFT JOIN tb_produk_ecat tpe ON trx.id_produk = tpe.id_produk_ecat
                                                LEFT JOIN tb_produk_set_ecat tpse ON trx.id_produk = tpse.id_set_ecat
                                                LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk reguler
                                                LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                                                WHERE nonppn.id_inv_nonppn = '$id_nonppn_decode' AND status_trx = '1' ORDER BY trx.created_date ASC";
                                        $trx_produk_reg = mysqli_query($connect, $sql_trx);
                                        while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                                            $namaProduk = $data_trx['nama_produk'];
                                            $id_produk = $data_trx['id_produk'];
                                            $satuan = $data_trx['satuan'];
                                            $nama_merk = $data_trx['merk_produk'];
                                            $disc = $data_trx['disc'];
                                            $total_harga = $data_trx['total_harga'];
                                            $sub_total += $total_harga;
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
                                    <td class="text-center text-nowrap"><?php echo $satuan_produk ?></td>
                                    <td class="text-center text-nowrap"><?php echo $nama_merk ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['harga']) ?>
                                    </td>
                                    <?php
                                        if ($total_data != 0) {
                                            if ($data_cek['kategori_inv'] == 'Diskon') {
                                                echo "<td class='text-end'>" . $disc . "</td>";
                                            }
                                        }
                                    ?>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['qty']) ?></td>
                                    <td class="text-end text-nowrap">
                                        <?php echo number_format($data_trx['total_harga']) ?></td>
                                </tr>
                                <?php $no++;?>
                                <?php }?>
                            </tbody>
                            <!-- Modal -->
                        </table>
                    </div>
                </div>
                <!-- Kode Untuk Update Total Inv -->
                <?php  
                    $hasil_sp_disc = $sp_disc / 100;
                    $total_sp_disc = $sub_total * $hasil_sp_disc;
                    $grand_total = $sub_total - $total_sp_disc;
                    $grand_total_nonppn = $grand_total + $ongkir;
                    $update_total_inv = mysqli_query($connect, "UPDATE inv_nonppn SET total_inv = '$grand_total_nonppn' WHERE id_inv_nonppn = '$id_inv'")
                ?>
            </div>
        </section>
    </main>
    <!-- End #main -->

    <!-- Modal Ubah Driver -->
    <div class="modal fade" id="ubahDriver" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Ubah Driver</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="proses/proses-ubah-driver.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_inv" value="<?php echo $id_nonppn_decode ?>">
                        <div class="mb-3">
                            <label>Pilih Driver</label>
                            <select id="pengirim" name="pengirim" class="form-select" required>
                                <option value="">Pilih..</option>
                                <?php
                                include "koneksi.php";                               
                                $sql_driver = mysqli_query($connect, "SELECT us.id_user_role, us.id_user, us.nama_user, rl.role FROM user AS us JOIN user_role rl ON (us.id_user_role = rl.id_user_role) WHERE rl.role = 'Driver' AND  us.nama_user != '$nama_driver'");
                                while ($data_driver = mysqli_fetch_array($sql_driver)) {
                                ?>
                                    <option value="<?php echo $data_driver['id_user'] ?>"><?php echo $data_driver['nama_user'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">tutup</button>
                        <button type="submit" class="btn btn-primary" name="ubah-driver">Ubah Driver</button>
                    </div>
                </form>                                         
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var selectElement = document.getElementById("pengirim");

                selectElement.addEventListener("change", function() {
                    var selectedValue = selectElement.value;
                    var options = selectElement.options;

                    for (var i = 0; i < options.length; i++) {
                        if (options[i].value === selectedValue) {
                            options[i].style.display = "none";
                        } else {
                            options[i].style.display = "block";
                        }
                    }
                });
            });
        </script>
    </div>

    <!-- Modal Diambil Oleh -->
    <div class="modal fade" id="diambil" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Diambil Oleh</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="proses/proses-diambil-oleh-nonppn.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id_inv" value="<?php echo $id_nonppn_decode ?>">
                        <div class="mb-3">
                            <label>Diambil Oleh</label>
                            <input type="text" name="diambil_oleh" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Diambil Tanggal</label>
                            <input type="text" id="date" name="diambil_tanggal" class="form-control">
                        </div>
                         <div class="mb-3">
                            <label id="labelBukti1">Bukti Terima 1</label>
                            <br>
                            <input type="file" name="fileku1" id="fileku1" accept=".jpg, .jpeg, .png" onchange="compressAndPreviewImage(event)" required title="Pilih File">
                        </div>
                        <div class="mb-3 preview-image-2" id="imagePreview"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">tutup</button>
                        <button type="submit" class="btn btn-primary" name="diambil-oleh">Update Data</button>
                    </div>
                </form>                                         
            </div>
        </div>
        <?php include "page/upload-img.php";  ?>
        <?php include "page/cek-upload.php"; ?>
        <style>
            .preview-image {
                max-width: 100%;
                height: auto;
            }
        </style>
    </div>

    <!-- Modal Edit Ongkir-->
    <div class="modal fade" id="editOngkir" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Resi dan Ongkir</h1>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <form action="proses/proses-ubah-ongkir-resi.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_inv" value="<?php echo $data_cek['id_inv_nonppn']; ?>">
                            <div class="mb-3">
                                <label id="labelResi">No. Resi</label>
                                <input type="text" class="form-control" name="edit_resi" id="resiEdit" value="<?php echo $no_resi ?>" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label id="labelJenisOngkir">Jenis Ongkir</label>
                                <select id="jenis_ongkir_edit" name="jenis_ongkir_edit" class="form-select">
                                    <option>Pilih</option>
                                    <option value="0">Non COD</option>
                                    <option value="1">COD</option>
                                </select>
                            </div>
                            <div class="mb-3" id="ongkirDiv" style="display: none;">
                                <label id="labelOngkir">Ongkir</label>
                                <input type="text" class="form-control" name="edit_ongkir" id="edit_ongkir" value="<?php echo number_format($ongkir) ?>">
                            </div>
                            <div class="mb-3">
                                <label id="labelBukti1">Bukti Terima 1</label>
                                <input type="file" name="fileku1" id="fileku1" accept=".jpg, .jpeg, .png" onchange="compressAndPreviewImageEx(event)" required>
                            </div>
                            <div class="mb-3 preview-image-3" id="imagePreviewEx"></div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" name="ubah-ongkir-nonppn"><i class="bi bi-arrow-left-right"></i> Ubah Data</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelEdit"><i class="bi bi-x-circle"></i> Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- kode JS Dikirim -->
        <?php include "page/upload-img.php"; ?>
        <?php include "page/cek-upload.php"; ?>

        <style>
            .preview-image {
                max-width: 100%;
                height: auto;
            }
        </style>
         <script>
            var input = document.getElementById('resiEdit');

            input.addEventListener('input', function() {
                var sanitizedValue = input.value.replace(/[^A-Za-z0-9]/g, '');
                input.value = sanitizedValue;
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var jenisOngkirEdit = document.getElementById('jenis_ongkir_edit');
                var editOngkir = document.getElementById('edit_ongkir');
                var ongkirDiv = document.getElementById('ongkirDiv');
                var cancelEdit = document.getElementById('cancelEdit');

                jenisOngkirEdit.addEventListener('change', function() {
                    if (this.value === '0') {
                        ongkirDiv.style.display = 'block';
                        editOngkir.style.backgroundColor = '';
                        editOngkir.setAttribute('required', 'true');
                        editOngkir.removeAttribute('readonly');
                    } else if (this.value === '1'){
                        ongkirDiv.style.display = 'none';
                        editOngkir.setAttribute('readonly', 'true');
                    } else if (this.value === ''){
                        ongkirDiv.style.display = 'none';
                        editOngkir.removeAttribute('required');
                    }
                });

                cancelEdit.addEventListener('click', function() {
                    location.reload();
                });

                 // Menambahkan event listener untuk input edit_ongkir
                editOngkir.addEventListener("input", function () {
                    // Menghapus karakter selain angka dan koma
                    var formattedValue = editOngkir.value.replace(/[^\d,]/g, '');

                    // Memastikan nilai tidak melebihi 100 juta
                    var numericValue = Number(formattedValue.replace(/,/g, ''));
                    if (numericValue > 1000000000) {
                        numericValue = 1000000000;
                    }

                    // Memformat nilai ke dalam format angka yang diinginkan
                    formattedValue = numericValue.toLocaleString();

                    // Menetapkan nilai yang diformat ke input
                    editOngkir.value = formattedValue;
                });
            });
        </script>
    </div>
    <!-- End Modal Edit Ongkir -->

    <!-- Footer -->
    <?php include "page/footer.php"?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <?php include "page/script.php"?>

</body>

</html>
<!-- Modal Cetak Surat Jalan -->
<div class="modal fade" id="cetakSuratJalan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Cetak Surat Jalan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="cetak-surat-jalan.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="hidden" name="id_inv" value="<?php echo $id_inv ?>">
                        <input type="text" class="form-control" name="disetujui" placeholder="Input Nama Disetujui" required>
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="cetak">Ya, Cetak Surat Jalan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Diterima-->
<div class="modal fade" id="DiterimaEx" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Ubah Status</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <?php  
                    if($no_resi == ''){
                        echo "Silahkan isi no resi terlebih dahulu agar dapat melakukan proses diterima";
                    } else {
                        ?>
                            <div class="card-body">
                                <form action="proses/proses-invoice-nonppn-diterima.php" method="POST"
                                    enctype="multipart/form-data">
                                    <input type="hidden" name="id_inv" value="<?php echo $data_cek['id_inv_nonppn']; ?>">
                                    <input type="hidden" name="alamat" value="<?php echo $data['alamat']; ?>">
                                    <div class="mb-3">
                                        <label>Nama Penerima</label>
                                        <input type="text" class="form-control" name="nama_penerima" autocomplete="off" required>
                                    </div>
                                    <div class="mb-3">
                                        <label id="labelDate">Tanggal</label>
                                        <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl"
                                            id="date" >
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary" name="diterima_ekspedisi"><i class="bi bi-arrow-left-right"></i> Ubah Status</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelEkspedisi"><i class="bi bi-x-circle"></i> Cancel</button>
                                    </div>
                                </form>
                            </div>
                        <?php
                    }
                
                ?>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Dikirim -->

<!-- Modal Diterima-->
<div class="modal fade" id="Diterima" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Status</h1>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form action="proses/proses-invoice-nonppn-diterima.php" method="POST"
                        enctype="multipart/form-data">
                        <input type="hidden" name="id_inv" value="<?php echo $data_cek['id_inv']; ?>">
                        <input type="hidden" name="alamat" value="<?php echo $data['alamat']; ?>">
                        <div class="mb-3">
                            <label id="labelJenisPenerima" style="display:none;">Diterima Oleh</label>
                            <select name="diterima_oleh" id="jenis-penerima" class="form-select" style="display:none;">
                                <option value="">Pilih...</option>
                                <option value="Customer">Customer</option>
                                <option value="Ekspedisi">Ekspedisi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label style="display: none;" id="labelPenerima">Nama Penerima</label>
                            <input type="text" class="form-control" name="nama_penerima" id="penerima"
                                style="display: none;" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <div class="input-group flex-nowrap">
                                <input type="text" name="nama_ekspedisi" id="dropdown-input" style="display: none;" class="form-control" placeholder="Pilih Ekspedisi...." autocomplete="off">
                                <span class="input-group-text" id="clear-search" style="display: none;"><i class="bi bi-x-circle"></i></span>
                            </div>
                            <div id="dropdown-list" class="form-control" style="display:none;"></div> 
                        </div>
                        <div class="mb-3">
                            <label style="display: none;" id="labelResi">No. Resi</label>
                            <input type="text" class="form-control" name="resi" id="resi" style="display: none;" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label id="labelJenisOngkir" style="display: none;">Jenis Ongkir</label>
                            <select id="jenis_ongkir" name="jenis_ongkir" class="form-select" style="display: none;">
                                <option>Pilih</option>
                                <option value="0">Non COD</option>
                                <option value="1">COD</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label style="display: none;" id="labelOngkir">Ongkir</label>
                            <input type="text" class="form-control" name="ongkir" id="ongkos_kirim" style="display: none;">
                        </div>
                        <div class="mb-3">
                            <label id="labelDate">Tanggal</label>
                            <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl"
                                id="date" required="required">
                        </div>
                        <div class="mb-3">
                            <label id="labelBukti1" style="display: none;">Bukti Kirim 1</label>
                            <input type="file" name="fileku1" id="fileku1" accept="image/*"
                                onchange="compressAndPreviewImage(event)" style="display: none;">
                        </div>
                        <div class="mb-3" id="imagePreview" style="display: none;"></div>

                        <div class="mb-3">
                            <label id="labelBukti2" style="display: none;">Bukti Kirim 2</label>
                            <input type="file" name="fileku2" id="fileku2" accept="image/*"
                                onchange="compressAndPreviewImage2(event)" style="display: none;">
                        </div>
                        <div class="mb-3" id="imagePreview2" style="display: none;"></div>

                        <div class="mb-3">
                            <label id="labelBukti3" for="fileku" style="display: none;">Bukti Kirim 3</label>
                            <input type="file" name="fileku3" id="fileku3" accept="image/*"
                                onchange="compressAndPreviewImage3(event)" style="display: none;">
                        </div>
                        <div class="mb-3" id="imagePreview3" style="display: none;"></div>
                        <?php
                        if ($jenis_pengiriman == 'Driver') {
                            echo '
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary" name="diterima_driver" id="diterima" onclick="checkFileName()" disabled><i class="bi bi-arrow-left-right"></i> Ubah Status</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDriver"><i class="bi bi-x-circle"></i> Cancel</button>
                                </div>';
                        } else {
                            echo '
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" name="diterima_ekspedisi" id="diterima" onclick="checkFileName()"><i class="bi bi-arrow-left-right"></i> Ubah Status</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelEkspedisi"><i class="bi bi-x-circle"></i> Cancel</button>
                            </div>';
                        }
                        ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- kode JS Dikirim -->
    <?php include "page/upload-img.php";  ?>
    <?php include "page/cek-upload.php"; ?>

    <!-- Search Ekspedisi -->
    <script>
        // Data untuk dropdown
        const options = <?php
        include "koneksi.php";
    
        $sql_ekspedisi = mysqli_query($connect, "SELECT * FROM ekspedisi");
        $option_values = array();
        while ($data_ekspedisi = mysqli_fetch_array($sql_ekspedisi)) {
            $option_values[] = $data_ekspedisi['nama_ekspedisi'];
        }
        echo json_encode($option_values); 
        ?> ;
    
          const dropdownInput = document.getElementById('dropdown-input');
          const dropdownList = document.getElementById('dropdown-list');
          const clearSearch = document.getElementById('clear-search');
        
          dropdownInput.addEventListener('click', function() {
            dropdownList.style.display = 'block'; // Display the dropdown list when the input is clicked
            populateDropdownList(options.slice(0, 3));
          });
        
          dropdownInput.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const filteredOptions = options.filter(function(option) {
              return option.toLowerCase().indexOf(searchValue) > -1;
            });
        
            populateDropdownList(filteredOptions.slice(0, 3));
          });
        
          clearSearch.addEventListener('click', function() {
            dropdownInput.value = '';
            dropdownList.innerHTML = '';
          });
        
          document.addEventListener('click', function(event) {
            const targetElement = event.target;
            if (!dropdownInput.contains(targetElement) && !dropdownList.contains(targetElement)) {
              dropdownList.style.display = 'none'; // Hide the dropdown list when clicking outside the input and the list
            }
          });
        
          function populateDropdownList(options) {
            dropdownList.innerHTML = '';
        
            if (options.length > 0) {
              options.forEach(function(option) {
                const optionElement = document.createElement('div');
                optionElement.textContent = option;
                optionElement.classList.add('dropdown-item');
        
                optionElement.addEventListener('click', function() {
                  dropdownInput.value = option;
                  dropdownList.innerHTML = '';
                  dropdownList.style.display = 'none'; // Hide the dropdown list after an option is selected
                });
        
                dropdownList.appendChild(optionElement);
              });
            } else {
              const noResultElement = document.createElement('div');
              noResultElement.textContent = 'Tidak ada hasil';
              noResultElement.classList.add('dropdown-item');
              dropdownList.appendChild(noResultElement);
            }
          }
    </script>
    <script>
        const jenisPenerima = <?php echo json_encode($jenis_pengiriman); ?> ;
        const labeljenisPenerima = document.getElementById('labelJenisPenerima');
        const jenisPenerimaSelect = document.getElementById('jenis-penerima');
        const labelPenerima = document.getElementById('labelPenerima');
        const penerima = document.getElementById('penerima');
        const labelEkspedisi = document.getElementById('clear-search');
        const ekspedisiSelect = document.getElementById('dropdown-input');
        const labelJenisOngkir = document.getElementById('labelJenisOngkir');
        const jenisOngkir = document.getElementById('jenis_ongkir');
        const labelOngkir = document.getElementById('labelOngkir');
        const ongkir = document.getElementById('ongkos_kirim');
        const labelResi = document.getElementById('labelResi');
        const resi = document.getElementById('resi');
        const labelBukti1 = document.getElementById('labelBukti1');
        const labelBukti2 = document.getElementById('labelBukti2');
        const labelBukti3 = document.getElementById('labelBukti3');
        const file1 = document.getElementById('fileku1');
        const file2 = document.getElementById('fileku2');
        const file3 = document.getElementById('fileku3');
        const imagePreview = document.getElementById('imagePreview');
        const imagePreview2 = document.getElementById('imagePreview2');
        const imagePreview3 = document.getElementById('imagePreview3');
        const diterima = document.getElementById('diterima');
    
        if (jenisPenerima === 'Driver') {
            labelJenisPenerima.style.display = 'block'; // Menampilkan Form Input
            jenisPenerimaSelect.style.display = 'block'; // Menampilkan Form Input
            jenisPenerimaSelect.setAttribute('required', 'true');
            diterima.disabled = false;
    
            //Membuat event listener saat select data
            jenisPenerimaSelect.addEventListener('change', function () {
                if (this.value === 'Customer') {
                    labelPenerima.style.display = 'block'; // Menampilkan Form Input
                    penerima.style.display = 'block'; // Menampilkan Form Input
                    penerima.setAttribute('required', 'true'); // Membuat Atribut Required
                    labelEkspedisi.style.display = 'none'; // Menyembunyikan Form Input
                    ekspedisiSelect.style.display = 'none'; // Menyembunyikan Form Input
                    ekspedisiSelect.value = ''; // Reset Value
                    labelResi.style.display = 'none'; // Menyembunyikan Form Input
                    resi.style.display = 'none'; // Menyembunyikan Form Input
                    resi.value = ''; // Reset Value
                    labelJenisOngkir.style.display = 'none'; // Menampilkan Form Input
                    jenisOngkir.style.display = 'none';
                    jenisOngkir.removeAttribute('required');
                    labelOngkir.style.display = 'none';
                    ongkir.style.display = 'none';
                    ongkir.removeAttribute('required');
                    labelBukti1.style.display = 'block'; // Menampilkan form input
                    labelBukti2.style.display = 'block'; // Menampilkan form input
                    labelBukti3.style.display = 'block'; // Menampilkan form input
                    file1.style.display = 'block'; // Menampilkan form input
                    file1.setAttribute('required', 'true'); // Membuat Atribut Required
                    file2.style.display = 'block'; // Menampilkan form input
                    file3.style.display = 'block'; // Menampilkan form input
                    imagePreview.style.display = 'block'; // Menampilkan konten di dalam elemen "imagePreview"
                    imagePreview2.style.display = 'block'; // Menampilkan konten di dalam elemen "imagePreview2"
                    imagePreview3.style.display = 'block'; // Menampilkan konten di dalam elemen "imagePreview3"
                    file1.value = ''; // Mengatur ulang nilai menjadi kosong
                    file2.value = ''; // Mengatur ulang nilai menjadi kosong
                    file3.value = ''; // Mengatur ulang nilai menjadi kosong
                    imagePreview.innerHTML = ''; // Menghapus konten di dalam elemen "imagePreview"
                    imagePreview2.innerHTML = ''; // Menghapus konten di dalam elemen "imagePreview2"
                    imagePreview3.innerHTML = ''; // Menghapus konten di dalam elemen "imagePreview3"
                    ekspedisiSelect.removeAttribute('required', 'true'); // Membuat Atribut Required
                    resi.removeAttribute('required', 'true'); // Membuat Atribut Required
                } else if (this.value === 'Ekspedisi') {
                    labelPenerima.style.display = 'none'; // Menyembunyikan Form Input
                    penerima.style.display = 'none'; // Menyembunyikan Form Input
                    penerima.value = ''; // Reset Value
                    penerima.removeAttribute('required', 'true'); // Membuat Atribut Required
                    labelEkspedisi.style.display = 'block'; // Menampilkan Form Input
                    ekspedisiSelect.style.display = 'block'; // Menampilkan Form Input
                    ekspedisiSelect.setAttribute('required', 'true'); // Membuat Atribut Required
                    labelJenisOngkir.style.display = 'block'; // Menampilkan Form Input
                    jenisOngkir.style.display = 'block';
                    jenisOngkir.setAttribute('required', 'true');
                    labelOngkir.style.display = 'block';
                    ongkir.style.display = 'block';
                    ongkir.style.backgroundColor = '#f8f9fa';
                    ongkir.setAttribute('readonly', 'true');
                    ongkir.value = '0';
                    labelResi.style.display = 'block'; // Menampilkan Form Input
                    resi.style.display = 'block'; // Menampilkan Form Input
                    resi.setAttribute('required', 'true'); // Membuat Atribut Required
                    labelBukti1.style.display = 'block'; // Menampilkan form input
                    labelBukti2.style.display = 'block'; // Menampilkan form input
                    labelBukti3.style.display = 'block'; // Menampilkan form input
                    file1.style.display = 'block'; // Menampilkan form input
                    file1.setAttribute('required', 'true'); // Membuat Atribut Required
                    file2.style.display = 'block'; // Menampilkan form input
                    file3.style.display = 'block'; // Menampilkan form input
                    imagePreview.style.display = 'block'; // Menampilkan konten di dalam elemen "imagePreview"
                    imagePreview2.style.display = 'block'; // Menampilkan konten di dalam elemen "imagePreview2"
                    imagePreview3.style.display = 'block'; // Menampilkan konten di dalam elemen "imagePreview3"
                } else if (this.value === '') {
                    labelPenerima.style.display = 'none'; // Menyembunyikan Form Input
                    penerima.style.display = 'none'; // Menyembunyikan Form Input
                    penerima.value = ''; // Reset Value
                    labelEkspedisi.style.display = 'none'; // Menyembunyikan Form Input
                    ekspedisiSelect.style.display = 'none'; // Menyembunyikan Form Input
                    ekspedisiSelect.value = ''; // Reset Value
                    labelResi.style.display = 'none'; // Menyembunyikan Form Input
                    resi.style.display = 'none'; // Menyembunyikan Form Input
                    resi.value = ''; // Reset Value
                }
            });
    
        } else if (jenisPenerima === 'Ekspedisi') {
            labelPenerima.style.display = 'block'; // Menampilkan Form Input
            penerima.style.display = 'block'; // Menampilkan Form Input
            penerima.setAttribute('required', 'true'); // Membuat Atribut Required
        } else {
            console.log("Nilai jenis Penerima tidak valid");
        }
    
        // membuat refresh halaman modal tanpa menutup modal dialog
        let isModalShown = false;
        // Refresh halaman modal
        if (isModalShown) {
            $('#Diterima').modal('hide'); // Menyembunyikan modal
            location.reload(); // Melakukan refresh halaman
            $('#Diterima').modal('show'); // Menampilkan modal kembali
        }

        jenisOngkir.addEventListener('change', function() {
            if (this.value === '0') {
                ongkir.style.display = 'block';
                ongkir.style.backgroundColor = '';
                ongkir.removeAttribute('readonly');
                ongkir.setAttribute('required', 'true');
            } else {
                ongkir.style.display = 'block';
                ongkir.style.backgroundColor = '#f8f9fa';
                ongkir.removeAttribute('required');
                ongkir.setAttribute('readonly', 'true');
            }
        });
    
        // Mendapatkan tombol "Cancel" berdasarkan ID
        const cancelDriver = document.getElementById('cancelDriver');
        if (cancelDriver) {
            cancelDriver.addEventListener('click', function () {
                jenisPenerimaSelect.value = ''; // Mengatur ulang nilai menjadi kosong
                penerima.value = ''; // Mengatur ulang nilai menjadi kosong
                ekspedisiSelect.value = ''; // Mengatur ulang nilai menjadi kosong
                resi.value = ''; // Mengatur ulang nilai menjadi kosong
                jenisOngkir.value = ''; // Mengatur ulang nilai menjadi kosong
                ongkir.value = ''; // Mengatur ulang nilai menjadi kosong
                file1.value = ''; // Mengatur ulang nilai menjadi kosong
                file2.value = ''; // Mengatur ulang nilai menjadi kosong
                file3.value = ''; // Mengatur ulang nilai menjadi kosong
                imagePreview.innerHTML = ''; // Menghapus konten di dalam elemen "imagePreview"
                imagePreview2.innerHTML = ''; // Menghapus konten di dalam elemen "imagePreview2"
                imagePreview3.innerHTML = ''; // Menghapus konten di dalam elemen "imagePreview3"
                labelPenerima.style.display = 'none'; // Menyembunyikan form input
                penerima.style.display = 'none'; // Menyembunyikan form input
                labelEkspedisi.style.display = 'none'; // Menyembunyikan form input
                ekspedisiSelect.style.display = 'none'; // Menyembunyikan form input
                labelResi.style.display = 'none'; // Menyembunyikan form input
                resi.style.display = 'none'; // Menyembunyikan form input
                labelJenisOngkir.style.display = 'none'; // Menampilkan Form Input
                jenisOngkir.style.display = 'none';
                labelOngkir.style.display = 'none';
                ongkir.style.display = 'none';
                labelBukti1.style.display = 'none'; // Menyembunyikan form input
                labelBukti2.style.display = 'none'; // Menyembunyikan form input
                labelBukti3.style.display = 'none'; // Menyembunyikan form input
                file1.style.display = 'none'; // Menyembunyikan form input
                file2.style.display = 'none'; // Menyembunyikan form input
                file3.style.display = 'none'; // Menyembunyikan form input
            });
        } else {
            console.log("Button Cancel Driver Sedang Aktif");
        }
    
        // Mendapatkan tombol "Cancel" berdasarkan ID
        const cancelEkspedisi = document.getElementById('cancelEkspedisi');
        if (cancelEkspedisi) {
            cancelEkspedisi.addEventListener('click', function () {
                penerima.value = ''; // Mengatur ulang nilai menjadi kosong
            });
        } else {
            console.log("Button Cancel Ekspedisi Sedang Aktif");
        }
    </script>
    <!-- End JS Dikirim -->
    <style>
        .preview-image {
            max-width: 100%;
            height: auto;
        }
    </style>
</div>
<!-- End Modal Diterima -->

<!-- Modal Bukti Kirim-->
<div class="modal fade" id="buktiKirim" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Bukti Kirim</h1>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                <?php
                include "koneksi.php";
                $sql_bukti = "SELECT ibt.*, ip.id_inv, ip.nama_penerima, ip.tgl_terima, sk.jenis_penerima, sk.dikirim_ekspedisi, sk.no_resi, ex.nama_ekspedisi
                                FROM inv_bukti_terima AS ibt
                                LEFT JOIN inv_penerima ip ON (ibt.id_inv = ip.id_inv)
                                LEFT JOIN status_kirim sk ON (ibt.id_inv = sk.id_inv)
                                LEFT JOIN ekspedisi ex ON (ex.id_ekspedisi = sk.dikirim_ekspedisi) 
                                WHERE ibt.id_inv = '$id_inv'";
                $query_bukti = mysqli_query($connect, $sql_bukti);
                $data_bukti = mysqli_fetch_array($query_bukti);
                $gambar1 = $data_bukti['bukti_satu'];
                $gambar_bukti1 = "gambar/bukti1/$gambar1";
                $gambar2 = $data_bukti['bukti_dua'];
                $gambar_bukti2 = "gambar/bukti2/$gambar2";
                $gambar3 = $data_bukti['bukti_tiga'];
                $gambar_bukti3 = "gambar/bukti3/$gambar3";
                ?>
                <div class="mb-3">
                    <h6>Penerima : <?php echo $data_bukti['jenis_penerima'] ?> (<?php echo $data_bukti['nama_ekspedisi'] ?>)</h6>
                    <h6>No. Resi : <?php echo $data_bukti['no_resi'] ?></h6>
                    <h6>Tgl. Terima : <?php echo date('d/m/Y', strtotime($data_bukti['created_date']))?></h6>
                </div>
                <div id="carouselExample" class="carousel slide">
                    <div class="carousel-inner">
                        <?php if (!empty($gambar1)) : ?>
                            <div class="carousel-item active">
                                <img src="<?php echo $gambar_bukti1 ?>" class="d-block w-100">
                                <div class="text-center mt-3">
                                    <h5>Bukti Kirim 1</h5>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($gambar2)) : ?>
                            <div class="carousel-item">
                                <img src="<?php echo $gambar_bukti2 ?>" class="d-block w-100">
                                <div class="text-center mt-3">
                                    <h5>Bukti Kirim 2</h5>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($gambar3)) : ?>
                            <div class="carousel-item">
                                <img src="<?php echo $gambar_bukti3 ?>" class="d-block w-100">
                                <div class="text-center mt-3">
                                    <h5>Bukti Kirim 3</h5>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Bukti Kirim -->

<!-- Generat UUID -->
<?php
function generate_uuid()
{
    return sprintf(
        '%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}
?>
<!-- End Generate UUID -->

<script>
function refreshPage() {
    location.reload();
}
</script>

<script>
    $(document).ready(function() {
        $('.btn-detail').click(function() {
            var idSpk = $(this).data('spk');
            $('#spk').text(idSpk);

            $('button.btn-pilih').attr('data-spk', idSpk);

            $('#modalBarang').modal('show');
        });

        $(document).on('click', '.btn-pilih', function(event) {
            event.preventDefault();
            event.stopPropagation();

            var id = $(this).data('id');
            var spk = $(this).attr('data-spk');

            saveData(id, spk);
        });

        function saveData(id, spk) {
            $.ajax({
                url: 'simpan-data-spk.php',
                type: 'POST',
                data: {
                    id: id,
                    spk: spk
                },
                success: function(response) {
                    console.log('Data berhasil disimpan.');
                    $('button[data-id="' + id + '"]').prop('disabled', true);
                },
                error: function(xhr, status, error) {
                    console.error('Terjadi kesalahan saat menyimpan data:', error);
                }
            });
        }
    });
</script>

<!-- Fungsi menonaktifkan kerboard enter -->
<script>
document.addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        document
            .getElementById("simpan-data")
            .click();
    }
});
</script>

<!-- Number format untuk harga -->

<script>
// Mendapatkan referensi elemen input
var hargaProdukInputs = document.querySelectorAll('.harga_produk');

// Menambahkan event listener untuk memformat angka saat nilai berubah
hargaProdukInputs.forEach(function(input) {
    input.addEventListener('input', function() {
        formatNumber(input);
    });
});

// Fungsi untuk memformat angka dengan pemisah ribuan
function formatNumber(input) {
    var hargaProdukValue = input
        .value
        .replace(/[^0-9.-]+/g, '');

    if (hargaProdukValue !== '') {
        var formattedNumber = numberFormat(hargaProdukValue);
        input.value = formattedNumber;
    }
}

// Fungsi untuk memformat angka dengan pemisah ribuan
function numberFormat(number) {
    return new Intl
        .NumberFormat('en-US')
        .format(number);
}
</script>

<!-- Edit Harga -->
<script>
    $('#edit-diskon').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var idTrx = button.data('id');
        var harga = button.data('hargadisc');
        var diskon = button.data('diskon');
        var qty = button.data('qty');

        $('#id_trxdisc').val(idTrx);
        $('#harga_produk_disc').val(harga);
        $('#discc').val(diskon);
        $('#qtydisc').val(qty);
    });

$('#edit').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var idTrx = button.data('id');
    var harga = button.data('harga');
    var qty = button.data('qty');

    $('#id_trx').val(idTrx);
    $('#harga_produk').val(harga);
    $('#qty').val(qty);
});
</script>

<!-- date picker with flatpick -->
<script type="text/javascript">
    flatpickr("#date", {
        dateFormat: "d/m/Y",
        defaultDate: "today", // Set default date to today
    });

    flatpickr("#tempo", {
        dateFormat: "d/m/Y"
    });

    // untuk menampilkan tanggal hari ini
    var dateInput = document.getElementById('date');

    // Membuat objek tanggal hari ini
    var today = new Date();

    // Mendapatkan hari, bulan, dan tahun dari tanggal hari ini
    var day = String(today.getDate()).padStart(2, '0');
    var month = String(today.getMonth() + 1).padStart(2, '0');
    var year = today.getFullYear();

    // Mengatur nilai default input dengan format yang diinginkan
    dateInput.value = day + '/' + month + '/' + year;
</script>
<!-- end date picker -->

<script>
    // Mendapatkan referensi elemen input
    var hargaProdukInputs = document.querySelectorAll('#ongkos_kirim');

    // Menambahkan event listener untuk memformat angka saat nilai berubah
    hargaProdukInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            formatNumber(input);
        });
    });

    // Fungsi untuk memformat angka dengan pemisah ribuan
    function formatNumber(input) {
        var hargaProdukValue = input.value.replace(/[^0-9.-]+/g, '');

        if (hargaProdukValue !== '') {
            var formattedNumber = numberFormat(hargaProdukValue);
            input.value = formattedNumber;
        }
    }

    // Fungsi untuk memformat angka dengan pemisah ribuan
    function numberFormat(number) {
        return new Intl.NumberFormat('en-US').format(number);
    }
</script>