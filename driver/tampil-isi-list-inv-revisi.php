<?php
$page = 'list-inv';
include "akses.php";
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
    <?php include "page/head.php" ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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
                        <strong>DETAIL INVOICE</strong>
                    </h5>
                </div>
                <?php
                    include "koneksi.php";
                    $id_inv = base64_decode($_GET['id']);
                    $id_komplain = base64_decode($_GET['id_komplain']);
                    $sql = "SELECT
                                COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                                COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                                COALESCE(nonppn.kategori_inv, ppn.kategori_inv, bum.kategori_inv) AS kategori_inv,
                                COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                                COALESCE(nonppn.sp_disc, ppn.sp_disc, bum.sp_disc) AS sp_disc,
                                COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo) AS tgl_tempo,
                                COALESCE(nonppn.ongkir, ppn.ongkir, bum.ongkir) AS ongkir,
                                COALESCE(nonppn.note_inv, ppn.note_inv, bum.note_inv) AS note_inv,
                                sr.id_spk_reg, sr.id_user, sr.id_customer, sr.no_spk, sr.no_po, sr.tgl_pesanan,
                                cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales
                            FROM spk_reg AS sr
                            LEFT JOIN inv_nonppn nonppn ON sr.id_inv = nonppn.id_inv_nonppn
                            LEFT JOIN inv_ppn ppn ON sr.id_inv = ppn.id_inv_ppn
                            LEFT JOIN inv_bum bum ON sr.id_inv = bum.id_inv_bum
                            JOIN tb_customer cs ON sr.id_customer = cs.id_cs
                            JOIN tb_orderby ordby ON sr.id_orderby = ordby.id_orderby
                            JOIN tb_sales sl ON sr.id_sales = sl.id_sales
                            WHERE nonppn.id_inv_nonppn = '$id_inv' OR ppn.id_inv_ppn = '$id_inv' OR bum.id_inv_bum = '$id_inv'";
                    $query = mysqli_query($connect, $sql);
                    $query2 = mysqli_query($connect, $sql);
                    $data = mysqli_fetch_array($query);
                    $sp_disc = $data['sp_disc'];
                    $ongkir = $data['ongkir'];
                    $id_spk = $data['id_spk_reg'];
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
                                                $id_inv = $data2['id_inv'];
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
                                    <?php  
                                        $sql_inv_revisi = mysqli_query($connect, "SELECT id_inv, max(no_inv_revisi) AS no_inv_revisi FROM inv_revisi WHERE id_inv = '$id_inv'");
                                        $data_inv_revisi = mysqli_fetch_array($sql_inv_revisi);
                                    ?>
                                    <?php echo $data_inv_revisi['no_inv_revisi'] ?>
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
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card-body p-3 border" style="min-height: 234px;">
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Sales</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['nama_sales'] ?>
                                </div>
                            </div>
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
                                $status_kirim = mysqli_query($connect, "SELECT jenis_pengiriman, dikirim_ekspedisi, jenis_penerima, dikirim_driver, dikirim_oleh, penanggung_jawab FROM revisi_status_kirim WHERE id_komplain = '$id_komplain'");
                                $data_status_kirim = mysqli_fetch_array($status_kirim);
                                $jenis_pengiriman =  $data_status_kirim['jenis_pengiriman'];
                                $ekspedisi = $data_status_kirim['dikirim_ekspedisi'];
                                $driver = $data_status_kirim['dikirim_driver'];


                                $ekspedisi_kirim =  mysqli_query($connect, "SELECT sk.jenis_pengiriman, sk.dikirim_ekspedisi, sk.jenis_penerima, ex.nama_ekspedisi
                                                                            FROM revisi_status_kirim AS sk
                                                                            JOIN ekspedisi ex ON (sk.dikirim_ekspedisi = ex.id_ekspedisi)
                                                                            WHERE sk.dikirim_ekspedisi = '$ekspedisi'");
                                $data_ekspedisi_kirim = mysqli_fetch_array($ekspedisi_kirim);
                                
                                $driver_kirim =  mysqli_query($connect, "SELECT sk.jenis_pengiriman, sk.dikirim_driver, us.nama_user 
                                                                            FROM revisi_status_kirim AS sk
                                                                            JOIN user us ON (sk.dikirim_driver = us.id_user)
                                                                            WHERE sk.dikirim_driver = '$driver'");
                                $data_driver_kirim = mysqli_fetch_array($driver_kirim);

                                $penerima =  mysqli_query($connect,"SELECT id_komplain, nama_penerima, created_date 
                                                                FROM inv_penerima_revisi
                                                                WHERE id_komplain = '$id_komplain' ORDER BY created_date DESC");
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
                                    <?php
                                } else {
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
                                                        <?php
                                                            if($data_status_kirim['jenis_penerima'] == 'Ekspedisi'){
                                                                ?>
                                                                    <?php echo $data_status_kirim['jenis_penerima'] ?> (<?php echo $data_ekspedisi_kirim['nama_ekspedisi'] ?>)
                                                                <?php
                                                            } else {
                                                                ?>
                                                                    <?php echo $data_status_kirim['jenis_penerima'] ?> (<?php echo $data_penerima['nama_penerima'] ?>)
                                                                <?php
                                                            }
                                                        ?>
                                                        
                                                    </div>
                                                </div>
                                            <?php
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
                            <a href="list-invoice-revisi.php" class="btn btn-warning btn-detail mb-2">
                                <i class="bi bi-arrow-left"></i>
                                Halaman Sebelumnya
                            </a>
                            <?php
                            $id_inv = base64_decode($_GET['id']);
                            $sql_cek = "SELECT DISTINCT
                                            COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                                            COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                            STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') AS tanggal,
                                            ik.id_komplain,
                                            ik.no_komplain,
                                            kk.kat_komplain,
                                            kk.kondisi_pesanan,
                                            kk.status_refund,
                                            COALESCE(spk_nonppn.id_spk_reg, spk_ppn.id_spk_reg, spk_bum.id_spk_reg) AS id_spk,
                                            COALESCE(spk_nonppn.no_spk, spk_ppn.no_spk, spk_bum.no_spk) AS no_spk,
                                            tpk.id_tmp,
                                            tpk.id_produk,
                                            tpk.nama_produk,
                                            tpk.harga,
                                            tpk.qty,
                                            tpk.disc,
                                            tpk.total_harga,
                                            COALESCE(mr_produk.nama_merk, mr_set.nama_merk) AS merk,
                                            spr.stock
                                        FROM inv_komplain AS ik
                                        LEFT JOIN komplain_kondisi kk ON kk.id_komplain = ik.id_komplain
                                        LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
                                        LEFT JOIN inv_ppn ppn ON ik.id_inv = ppn.id_inv_ppn
                                        LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
                                        LEFT JOIN spk_reg spk_nonppn ON ik.id_inv = spk_nonppn.id_inv
                                        LEFT JOIN spk_reg spk_ppn ON ik.id_inv = spk_ppn.id_inv
                                        LEFT JOIN spk_reg spk_bum ON ik.id_inv = spk_bum.id_inv
                                        LEFT JOIN tmp_produk_komplain tpk ON spk_nonppn.id_inv = tpk.id_inv OR spk_ppn.id_inv = tpk.id_inv OR spk_bum.id_inv = tpk.id_inv
                                        LEFT JOIN tb_produk_reguler pr ON tpk.id_produk = pr.id_produk_reg
                                        LEFT JOIN tb_produk_set_marwa tpsm ON tpk.id_produk = tpsm.id_set_marwa
                                        LEFT JOIN tb_merk mr_produk ON pr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                        LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                        LEFT JOIN stock_produk_reguler spr ON tpk.id_produk = spr.id_produk_reg
                                        WHERE nonppn.id_inv_nonppn = '$id_inv' OR ppn.id_inv_ppn = '$id_inv' OR bum.id_inv_bum = '$id_inv' AND status_tmp = '1' AND status_br_refund = '0'";
                            $query_cek = mysqli_query($connect, $sql_cek);
                            $data_cek = mysqli_fetch_array($query_cek);
                            $total_data = mysqli_num_rows($query_cek);
                            $id_komplain = $data_cek['id_komplain'];
                            ?>
                            <?php
                                include "koneksi.php";
                                $sql_bukti_kirim = "SELECT * FROM inv_bukti_terima_revisi WHERE id_komplain = '$id_komplain'";
                                $query_bukti_kirim = mysqli_query($connect, $sql_bukti_kirim);
                                $total_row = mysqli_num_rows($query_bukti_kirim);
                                if ($total_row > 0) { 
                                    echo '
                                    <button class="btn btn-primary btn-detail mb-2" data-bs-toggle="modal" data-bs-target="#buktiKirim">
                                        <i class="bi bi-file-earmark-image"></i> Bukti Kirim
                                    </button>

                                    <button class="btn btn-secondary btn-detail mb-2" data-bs-toggle="modal"
                                        data-bs-target="#Diterima">
                                        <i class="bi bi-send"></i>
                                        Diterima
                                    </button>
                                    ';
                                }else{
                                    echo '
                                    <button class="btn btn-secondary btn-detail mb-2" data-bs-toggle="modal"
                                        data-bs-target="#Diterima">
                                        <i class="bi bi-send"></i>
                                        Diterima
                                    </button>
                                    ';
                                }
                            ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="text-white" style="background-color: #051683;">
                                    <th class="text-center text-nowrap p-3" style="width:20px">No</th>
                                    <th class="text-center text-nowrap p-3" style="width:200px">Nama Produk</th>
                                    <th class="text-center text-nowrap p-3" style="width:100px">Satuan</th>
                                    <th class="text-center text-nowrap p-3" style="width:100px">Merk</th>
                                    <th class="text-center text-nowrap p-3" style="width:80px">Qty Order</th>
                                    <th class="text-center text-nowrap p-3" style="width:100px">Harga</th>
                                    <th class="text-center text-nowrap p-3" style="width:100px">Diskon</th>
                                    <th class="text-center text-nowrap p-3" style="width:80px">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    include "koneksi.php";
                                    $year = date('y');
                                    $day = date('d');
                                    $month = date('m');
                                    $id_nonppn_decode = base64_decode($_GET['id']);
                                    $no = 1;
                                    $sql_trx = "SELECT DISTINCT
                                                    tpk.id_tmp,
                                                    tpk.id_produk,
                                                    tpk.nama_produk,
                                                    tpk.harga,
                                                    tpk.qty,
                                                    tpk.disc,
                                                    tpk.total_harga,
                                                    COALESCE(mr_produk.nama_merk, mr_set.nama_merk) AS merk,
                                                    spr.stock,
                                                    pr.satuan
                                                FROM tmp_produk_komplain AS tpk
                                                LEFT JOIN tb_produk_reguler pr ON tpk.id_produk = pr.id_produk_reg
                                                LEFT JOIN tb_produk_set_marwa tpsm ON tpk.id_produk = tpsm.id_set_marwa
                                                LEFT JOIN tb_merk mr_produk ON pr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                                LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                                LEFT JOIN stock_produk_reguler spr ON tpk.id_produk = spr.id_produk_reg
                                                WHERE tpk.id_inv = '$id_inv' AND status_tmp = '1' AND status_br_refund = '0'";
                                        $trx_produk_reg = mysqli_query($connect, $sql_trx);
                                        while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                                            $id_produk = $data_trx['id_produk'];
                                            $satuan = $data_trx['satuan'];
                                            $merk = $data_trx['merk'];
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
                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                    <td class="text-nowrap"><?php echo $data_trx['nama_produk'] ?></td>
                                    <td class="text-center text-nowrap"><?php echo $satuan_produk ?></td>
                                    <td class="text-center text-nowrap"><?php echo $merk ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['qty']) ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['harga']) ?></td>
                                    <td class='text-end'><?php echo $data_trx['disc'] ?></td>
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
            </div>
        </section>
    </main>
    <!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php"?>
    <!-- End Footer -->

    <?php include "page/script.php"?>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>
</body>

</html>

<!-- Modal Diterima-->
<div class="modal fade" id="Diterima" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Status Kirim</h1>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal" aria-label="Close" id="cancelDriver">X</button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <?php 
                        $id_inv = $data_cek['id_inv'];  
                        $inv_revisi = mysqli_query($connect, "SELECT id_inv, max(no_inv_revisi) AS no_inv_revisi FROM inv_revisi WHERE id_inv = '$id_inv'");
                        $data_inv_revisi = mysqli_fetch_array($inv_revisi);
                        $no_inv_revisi = $data_inv_revisi['no_inv_revisi'];
                    ?>
                    <form action="proses/proses-invoice-revisi.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_komplain" value="<?php echo $id_komplain; ?>">
                        <input type="hidden" name="id_inv" value="<?php echo $data_cek['id_inv']; ?>">
                        <input type="hidden" name="alamat" value="<?php echo $data['alamat']; ?>">
                        <input type="hidden" name="id_spk" value="<?php echo $data_cek['id_spk']; ?>">
                        <input type="hidden" name="no_komplain" value="<?php echo $data_cek['no_komplain']; ?>">
                        <div class="mb-3">  
                            <label><b>No. Invoice</b></label>
                            <input type="text" class="form-control" value="<?php echo $no_inv_revisi; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label id="labelJenisPenerima"><b>Diterima Oleh</b></label>
                            <select name="diterima_oleh" id="jenis-penerima" class="form-select" required>
                                <option value="">Pilih...</option>
                                <option value="Customer">Customer</option>
                                <option value="Ekspedisi">Ekspedisi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label><b>Tanggal</b></label>
                            <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl" id="dateFormat" required>
                        </div>
                        <!-- Diterima CS -->
                        <div id="cs" style="display: none;">
                            <div class="mb-3">
                                <label id="labelPenerima"><b>Nama Penerima</b></label>
                                <input type="text" class="form-control" name="nama_penerima" id="penerima" autocomplete="off" required>
                            </div>
                        </div>

                        <!-- Diterima Ekspedisi -->
                        <div id="eks" style="display: none;">
                            <div class="mb-3">
                                <label id="labelPenerima"><b>Pilih Ekspedisi</b></label>
                                <div class="input-group flex-nowrap">
                                    <input type="text" name="nama_ekspedisi" id="dropdown-input" class="form-control" placeholder="Pilih..." autocomplete="off" required>
                                    <span class="input-group-text" id="clear-search"><i class="bi bi-x-circle"></i></span>
                                </div>
                                <div id="dropdown-list" class="form-control"></div> 
                            </div>
                            <div class="mb-3">
                                <label id="labelResi"><b>No. Resi</b></label>
                                <input type="text" class="form-control" name="resi" id="resi" autocomplete="off" required>
                            </div>
                            <div class="mb-3">
                                <label id="labelJenisOngkir"><b>Jenis Ongkir</b></label>
                                <select id="jenis_ongkir" name="jenis_ongkir" class="form-select" required>
                                    <option value="">Pilih</option>
                                    <option value="0">Non COD</option>
                                    <option value="1">COD</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label id="labelOngkir"><b>Ongkir</b></label>
                                <input type="text" class="form-control" name="ongkir" id="ongkir" required oninput="formatNumber(this)">
                            </div>
                        </div> 
                        <div class="mb-3">
                            <label id="labelBukti1"><b>Bukti Terima 1</b></label>
                            <input type="file" name="fileku1" id="fileku1" accept=".jpg, .jpeg, .png"
                                onchange="compressAndPreviewImage(event)" required>
                        </div>
                        <div class="mb-3" id="imagePreview" ></div>

                        <div class="mb-3">
                            <label id="labelBukti2"><b>Bukti Terima 2</b></label>
                            <input type="file" name="fileku2" id="fileku2" accept=".jpg, .jpeg, .png"
                                onchange="compressAndPreviewImage2(event)">
                        </div>
                        <div class="mb-3" id="imagePreview2"></div>

                        <div class="mb-3">
                            <label id="labelBukti3" for="fileku"><b>Bukti Terima 3</b></label>
                            <input type="file" name="fileku3" id="fileku3" accept=".jpg, .jpeg, .png"
                                onchange="compressAndPreviewImage3(event)">
                        </div>
                        <div class="mb-3" id="imagePreview3"></div> 
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="diterima" id="diterima" onclick="checkFileName()"><i class="bi bi-arrow-left-right"></i> Ubah Status</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDriver"><i class="bi bi-x-circle"></i> Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- kode JS Dikirim -->
    <?php include "page/upload-img.php";  ?>
    <?php include "page/cek-upload.php"; ?>
    <?php include "page/search-ekspedisi.php"; ?>
    <?php include "page/kondisi-diterima.php"; ?>

    <!-- End JS Dikirim -->
    <style>
    .preview-image {
        max-width: 100%;
        height: auto;
    }
    </style>
    <script>
        flatpickr("#dateFormat", {
            dateFormat: "d/m/Y",
            defaultDate: "today"
        });
    </script>
</div>
<!-- End Modal Diterima-->

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
                $sql_bukti = " SELECT 
                                    ibt.id_komplain, ibt.bukti_satu, ibt.bukti_dua, ibt.bukti_tiga, ibt.created_date, ip.id_komplain, ip.nama_penerima, ip.tgl_terima, ip.created_date, sk.jenis_penerima, sk.dikirim_ekspedisi, sk.no_resi, ex.nama_ekspedisi
                                FROM inv_bukti_terima_revisi AS ibt
                                LEFT JOIN inv_penerima_revisi ip ON (ibt.id_komplain = ip.id_komplain)
                                LEFT JOIN revisi_status_kirim sk ON (ibt.id_komplain = sk.id_komplain)
                                LEFT JOIN ekspedisi ex ON (ex.id_ekspedisi = sk.dikirim_ekspedisi) 
                                WHERE ibt.id_komplain = '$id_komplain' ORDER BY ip.created_date AND ibt.created_date DESC LIMIT 1";
                $query_bukti = mysqli_query($connect, $sql_bukti);
                $data_bukti = mysqli_fetch_array($query_bukti);
                $gambar1 = $data_bukti['bukti_satu'];
                $gambar_bukti1 = "../gambar-revisi/bukti1/$gambar1";
                $gambar2 = $data_bukti['bukti_dua'];
                $gambar_bukti2 = "../gambar-revisi/bukti2/$gambar2";
                $gambar3 = $data_bukti['bukti_tiga'];
                $gambar_bukti3 = "../gambar-revisi/bukti3/$gambar3";
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
     // Mendapatkan tanggal hari ini
     const today = new Date();
    const dd = String(today.getDate()).padStart(2, '0');
    const mm = String(today.getMonth() + 1).padStart(2, '0'); // Januari dimulai dari 0
    const yyyy = today.getFullYear();
    
    // Format tanggal hari ini menjadi "dd/mm/yyyy"
    const formattedDate = dd + '/' + mm + '/' + yyyy;
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