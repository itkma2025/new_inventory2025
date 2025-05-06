<?php
$page  = 'transaksi';
$page2 = 'spk';
require_once "../akses.php";
include "../function/class-spk.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <?php include "page/head.php"; ?>

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
        /* style validasi */
        .form-control.error {
            border-color: #dc3545;
        }

        .form-select.error {
            border-color: #dc3545 !important;
        }

        .form-check-input.error {
            border-color: #dc3545 !important;
        }

        .error {
            border-color: #dc3545 !important;
        }

        .error-message {
            color: #dc3545;  
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
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include "page/sidebar.php"; ?>
    <!-- end sidebar -->


    <main id="main" class="main">
        <!-- SWEET ALERT -->
        <section>
            <div class="card shadow p-2">
                <div class="card-header text-center">
                    <h5>
                        <strong>DETAIL INVOICE PPN</strong> 
                    </h5>
                </div>
                <?php
                    include "koneksi.php";
                    $id_inv = base64_decode($_GET['id']);
                    $sql = "SELECT
                            ppn.id_inv_ppn,
                            ppn.no_inv,
                            ppn.tgl_inv,
                            ppn.kategori_inv,
                            ppn.cs_inv,
                            ppn.sp_disc,
                            ppn.tgl_tempo,
                            ppn.ongkir,
                            ppn.note_inv,
                            sr.id_user, sr.id_customer, sr.no_spk, sr.no_po, sr.tgl_pesanan, sr.petugas,
                            cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales
                            FROM inv_ppn AS ppn
                            JOIN spk_reg sr ON (ppn.id_inv_ppn = sr.id_inv)
                            JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                            JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                            JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                            WHERE ppn.id_inv_ppn = '$id_inv'";
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
                                                $id_inv = $data2['id_inv_ppn'];
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
                                    <?php echo $data['alamat'] ?>
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
                                $status_kirim = mysqli_query($connect, "SELECT jenis_pengiriman, dikirim_ekspedisi, jenis_penerima, dikirim_driver, dikirim_oleh, no_resi, penanggung_jawab FROM status_kirim WHERE id_inv = '$id_inv'");
                                $data_status_kirim = mysqli_fetch_array($status_kirim);
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
                                                        <?php echo $data_status_kirim['jenis_penerima'] ?> (<?php echo $data_penerima['nama_penerima'] ?>)
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
                            <a href="invoice-reguler-selesai.php" class="btn btn-warning btn-detail mb-2">
                                <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                            </a>
                            <!-- Button modal Bukti Terima -->
                            <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#bukti">
                                <i class="bi bi-file-earmark-image"></i> Bukti Terima
                            </button>

                            <?php  
                                $finance =  mysqli_query($connect, "SELECT id_inv, status_tagihan FROM finance WHERE id_inv = '$id_inv'");
                                $cek_finance = mysqli_fetch_array($finance);
                                $status_tagihan = $cek_finance['status_tagihan'];
                                if($status_tagihan == '1'){
                                    ?>
                                        <button class="btn btn-secondary mb-2"><i class="bi bi-check"></i> Tagihan Sudah Dibuat</button>
                                    <?php
                                } else {
                                    ?>
                                        <button class="btn btn-warning mb-2" data-bs-toggle="modal" data-bs-target="#modalKomplain"><i class="bi bi-info-circle"></i> Komplain</button>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <button type="button" class="btn btn-secondary p-2">Nama Petugas : <?php echo $petugas ?></button>
                        <table class="table table-striped table-bordered" id="table2">
                            <?php
                                if ($data['kategori_inv'] != 'Diskon') {
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
                            ?>
                            <tbody>
                                <?php
                                include "koneksi.php";
                                $year = date('y');
                                $day = date('d');
                                $month = date('m');
                                $id_ppn_decode = base64_decode($_GET['id']);
                                $no = 1;
                                $sql_trx = "SELECT 
                                                ppn.id_inv_ppn, 
                                                ppn.kategori_inv,
                                                
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
                                                tpr.nama_produk,
                                                tpr.satuan,
                                                mr_produk.nama_merk AS merk_produk, -- Nama merk untuk produk reguler
                                                tpsm.nama_set_marwa,
                                                tpsm.harga_set_marwa,
                                                mr_set.nama_merk AS merk_set -- Nama merk untuk produk set
                                            FROM inv_ppn AS ppn
                                            LEFT JOIN spk_reg spk ON (ppn.id_inv_ppn = spk.id_inv)
                                            LEFT JOIN transaksi_produk_reg trx ON trx.id_spk = spk.id_spk_reg
                                            LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                                            LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                                            LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                            LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                            WHERE ppn.id_inv_ppn = '$id_ppn_decode' AND status_trx = '1' ORDER BY no_spk ASC";
                                $trx_produk_reg = mysqli_query($connect, $sql_trx);
                                while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                                    $namaProduk = detailSpk::getDetail($data_trx['nama_produk'], $data_trx['nama_set_marwa']);
                                    $id_produk = $data_trx['id_produk'];
                                    $satuan = $data_trx['satuan'];
                                    $nama_merk = detailSpk::getMerk($data_trx['merk_produk'], $data_trx['merk_set']);
                                    $disc = $data_trx['disc'];
                                    $satuan_produk = '';
                                    $id_produk_substr = substr($id_produk, 0, 2);
                                    if ($id_produk_substr == 'BR') {
                                        $satuan_produk = $satuan;
                                    } else {
                                        $satuan_produk = 'Set';
                                    }
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $no; ?></td>
                                        <td class="text-center text-nowrap"><?php echo $data_trx['no_spk']; ?></td>
                                        <td class="text-nowrap"><?php echo $data_trx['nama_produk_spk'] ?></td>
                                        <td class="text-center"><?php echo $satuan_produk ?></td>
                                        <td class="text-center text-nowrap"><?php echo $nama_merk ?></td>
                                        <td class="text-end"><?php echo number_format($data_trx['harga']) ?></td>
                                        <?php
                                            if ($data_trx['kategori_inv'] == 'Diskon') {
                                                echo "<td class='text-end'>" . $disc . "</td>";
                                            }
                                        ?>
                                        <td class="text-end"><?php echo number_format($data_trx['qty']) ?></td>
                                        <td class="text-end"><?php echo number_format($data_trx['total_harga']) ?></td>
                                    </tr>
                                    <?php $no++; ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal  Bukti Terima-->
                <div class="modal fade" id="bukti" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Bukti Terima</h1>
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
                                $gambar_bukti1 = "../gambar/bukti1/$gambar1";
                                $gambar2 = $data_bukti['bukti_dua'];
                                $gambar_bukti2 = "../gambar/bukti2/$gambar2";
                                $gambar3 = $data_bukti['bukti_tiga'];
                                $gambar_bukti3 = "../gambar/bukti3/$gambar3";
                                $jenis_penerima = $data_bukti['jenis_penerima'];
                                $no_resi = $data_bukti['no_resi'];
                                ?>
                                <div class="mb-3">
                                    <h6>Nama Penerima : <?php echo $data_bukti['nama_penerima'] ?></h6>
                                    <?php if ($jenis_penerima == 'Ekspedisi') {
                                        echo'
                                            <h6>No. Resi :' . $no_resi . '</h6> 
                                        ';
                                    }
                                    ?>
                                    <h6>Tgl. Terima : <?php echo date('d/m/Y', strtotime($data_bukti['created_date']))?></h6>
                                </div>
                                <div id="carouselExample" class="carousel slide">
                                    <div class="carousel-inner">
                                        <?php if (!empty($gambar1)) : ?>
                                            <div class="carousel-item active">
                                                <img src="<?php echo $gambar_bukti1 ?>" class="d-block w-100">
                                                <div class="text-center mt-3">
                                                    <h5>Bukti Terima 1</h5>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($gambar2)) : ?>
                                            <div class="carousel-item">
                                                <img src="<?php echo $gambar_bukti2 ?>" class="d-block w-100">
                                                <div class="text-center mt-3">
                                                    <h5>Bukti Terima 2</h5>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($gambar3)) : ?>
                                            <div class="carousel-item">
                                                <img src="<?php echo $gambar_bukti3 ?>" class="d-block w-100">
                                                <div class="text-center mt-3">
                                                    <h5>Bukti Terima 3</h5>
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
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal Bukti Terima -->
            </div>
        </section>
    </main><!-- End #main -->
    <!-- Modal Komplain -->
    <div class="modal fade" id="modalKomplain" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Komplain Invoice</h1>
                </div>
                <div class="modal-body">
                    <form action="proses/komplain.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_inv" value="<?php echo $id_inv; ?>">
                        <div id="tidak_sesuai_form">
                            <div class="mb-3">
                                <label><b>Tanggal Komplain</b></label>
                                <input type="text" class="form-control" name="tgl" id="tgl_komplain" maxlength="10" autocomplete="off" required>
                            </div>
                            <div class="mb-3" style="display:block;">
                                <label><b>Pilih Kategori Komplain</b></label>
                                <select name="kat_komplain" id="kat_komplain" class="form-select" required>
                                    <option value="">Pilih Kategori...</option>
                                    <option value="0">Invoice</option>
                                    <option value="1">Barang</option>
                                </select>
                            </div>
                            <label><b>Pilih Kondisi Pesanan</b></label>
                            <div class="mb-3 border p-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="kondisi_pesanan" id="kondisi_pesanan0" value="0" required>
                                    <label class="form-check-label" for="kondisi_pesanan0">
                                        Faktur sesuai, tetapi barang yang diterima adalah jenis yang salah.
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="kondisi_pesanan" id="kondisi_pesanan1" value="1" required>
                                    <label class="form-check-label" for="kondisi_pesanan1">
                                        Faktur sesuai, namun jumlah barang yang diterima kurang dari yang diharapkan.
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="kondisi_pesanan" id="kondisi_pesanan2" value="2" required>
                                    <label class="form-check-label" for="kondisi_pesanan2">
                                        Faktur sesuai, tetapi pelanggan meminta revisi harga.
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="kondisi_pesanan" id="kondisi_pesanan3" value="3" required>
                                    <label class="form-check-label" for="kondisi_pesanan3">
                                        Faktur dan barang sesuai, tetapi barang yang diterima rusak, cacat,atau memiliki masalah kualitas sehingga tidak berfungsi sesuai yang diharapkan.
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="kondisi_pesanan" id="kondisi_pesanan4" value="4" required>
                                    <label class="form-check-label" for="kondisi_pesanan4">
                                        Faktur tidak sesuai, tetapi barang dan jumlahnya cocok dengan pesanan.
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="kondisi_pesanan" id="kondisi_pesanan5" value="5" required>
                                    <label class="form-check-label" for="kondisi_pesanan5">
                                        Pelanggan meminta pengembalian barang / uang karena ketidakcocokan pesanan.
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label><b>Retur Barang</b></label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="retur" id="retur_ya" value="1" required>
                                            <label class="form-check-label" for="inlineRadio1">Ya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="retur" id="retur_tidak" value="0" required>
                                            <label class="form-check-label" for="inlineRadio2">Tidak</label>
                                        </div>
                                    </div>                                     
                                    <div class="col-md-6" id="refundDana" style="display: none;">
                                        <label><b>Refund Dana</b></label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="refund" id="refund_ya" value="1" required>
                                            <label class="form-check-label" for="inlineRadio1">Ya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="refund" id="refund_tidak" value="0" required>
                                            <label class="form-check-label" for="inlineRadio2">Tidak</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label><b>Catatan Khusus (*)</b></label>
                                <textarea class="form-control" name="catatan" id="catatan" cols="30" rows="5"></textarea>
                                <p>Jumlah Karakter: <span id="hitungKarakter">0</span></p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="cancelKomplain" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" name="komplain-ppn">Proses Komplain</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- kode JS Dikirim -->
        <?php include "../page/kondisi-diterima.php"; ?>
    </div>
    <!-- End Modal Komplain -->            

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>

</body>
</html>
<script>
    flatpickr("#tgl_komplain", {
        dateFormat: "d/m/Y",
        allowInput: true,
        monthSelectorType: "static", // Menggunakan tampilan bulan statis
        onReady: function(selectedDates, dateStr, instance) {
            var input = instance.input;
            input.addEventListener('input', function(event) {
                var val = input.value;

                // Memeriksa jika nilai kosong, atur nilai kembali menjadi kosong
                if (val === '') {
                    input.value = '';
                    return;
                }

                // Memeriksa apakah panjang string adalah 2
                if (val.length === 2 && !val.includes('/')) {
                    var day = parseInt(val);
                    if (isNaN(day) || day > 31) {
                        // Jika nilai tidak valid, atur nilai menjadi 31
                        val = '31';
                        val += '/';
                    } else {
                        val += '/';
                    }
                } else if (val.length === 5 && !val.includes('/', 3)) {
                    var parts = val.split('/');
                    var day = parseInt(parts[0]);
                    var month = parseInt(parts[1]);

                    // Memeriksa apakah nilai bulan melebihi 12
                    if (month > 12) {
                        // Jika ya, atur nilai bulan menjadi 12
                        parts[1] = '12';
                    }

                    // Menggabungkan kembali bagian-bagian tanggal setelah diperiksa
                    val = parts.join('/');
                    val += '/';
                }
                
                input.value = val;
            });

            // Menangani tombol backspace
            input.addEventListener('keydown', function(event) {
                if (event.key === 'Backspace') {
                    var val = input.value;
                    var lastIndex = val.lastIndexOf('/');
                    if (lastIndex === val.length - 1) {
                        // Jika '/' adalah karakter terakhir, hapus 3 karakter terakhir (termasuk '/')
                        input.value = val.slice(0, -1);
                    }
                }
            });
            
            var monthDropdown = instance.monthElements[0];

            // Menghentikan penutupan dropdown pilihan bulan saat dibuka
            monthDropdown.addEventListener('mousedown', function(event) {
                event.stopPropagation();
            });
        }
    });
</script>