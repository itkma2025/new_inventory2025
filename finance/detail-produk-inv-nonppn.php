<?php
$page = 'list-cs';
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
    <link href="../assets/vendor/lightbox/dist/css/lightgallery.css" rel="stylesheet"/>

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
                                nonppn.sp_disc,
                                nonppn.tgl_tempo,
                                nonppn.ongkir,
                                nonppn.note_inv,
                                nonppn.status_transaksi AS status_trx,
                                sr.id_user, sr.id_customer, sr.no_spk, sr.no_po, sr.tgl_pesanan,
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
                    $status_trx = $data['status_trx'];
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
                                if ($status_trx == 'Belum Dikirim') {
                                } else {
                                    ?>
                                    <?php  
                                        $status_kirim = mysqli_query($connect, "SELECT jenis_pengiriman, dikirim_ekspedisi, jenis_penerima, dikirim_driver, dikirim_oleh, penanggung_jawab FROM status_kirim WHERE id_inv = '$id_inv'");
                                        $data_status_kirim = mysqli_fetch_array($status_kirim);
                                        $jenis_pengiriman =  $data_status_kirim['jenis_pengiriman'];
                                        $ekspedisi = $data_status_kirim['dikirim_ekspedisi'];
                                        $driver = $data_status_kirim['dikirim_driver'];


                                        $ekspedisi_kirim =  mysqli_query($connect, "SELECT sk.jenis_pengiriman, sk.dikirim_ekspedisi, sk.jenis_penerima, ex.nama_ekspedisi
                                                                                    FROM status_kirim AS sk
                                                                                    JOIN ekspedisi ex ON (sk.dikirim_ekspedisi = ex.id_ekspedisi)
                                                                                    WHERE sk.dikirim_ekspedisi = '$ekspedisi'");
                                        $data_ekspedisi_kirim = mysqli_fetch_array($ekspedisi_kirim);
                                        
                                        $driver_kirim =  mysqli_query($connect, "SELECT sk.jenis_pengiriman, sk.dikirim_driver, us.nama_user 
                                                                                    FROM status_kirim AS sk
                                                                                    JOIN $database2.user us ON (sk.dikirim_driver = us.id_user)
                                                                                    WHERE sk.dikirim_driver = '$driver'");
                                        $data_driver_kirim = mysqli_fetch_array($driver_kirim);

                                        $penerima =  mysqli_query($connect,"SELECT id_inv, nama_penerima, tgl_terima 
                                                                        FROM inv_penerima
                                                                        WHERE id_inv = '$id_inv'");
                                        $data_penerima = mysqli_fetch_array($penerima);
                                        $tgl_terima = $data_penerima["tgl_terima"];
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
                                            <div class="row mt-2">
                                                <div class="col-5">
                                                    <p style="float: left;">Diterima Oleh</p>
                                                    <p style="float: right;">:</p>
                                                </div>
                                                <div class="col-7">
                                                    <?php echo $data_penerima['nama_penerima'] ?>
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
                                                                <?php echo $data_status_kirim['jenis_penerima'] ?> (<?php echo $data_penerima['nama_penerima'] ?>)
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-5">
                                                                <p style="float: left;">Diterima Tanggal</p>
                                                                <p style="float: right;"> :</p>
                                                            </div>
                                                            <div class="col-7">
                                                                <?php echo $tgl_terima?>
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
                                    <?php
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
                            <a href="detail-invoice-customer.php?cs=<?php echo base64_encode($data['id_customer']) ?>&sort_data=tahun_ini" class="btn btn-warning btn-detail mb-2">
                                <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                            </a>
                            <?php  
                                $cek_bukti_terima ="SELECT nonppn.id_inv_nonppn, COALESCE(ibt.id_inv, 0) AS id_inv_bukti
                                                    FROM inv_nonppn AS nonppn 
                                                    LEFT JOIN inv_bukti_terima ibt ON(nonppn.id_inv_nonppn = ibt.id_inv)
                                                    WHERE nonppn.id_inv_nonppn = '$id_inv'";
                                $query_bukti_terima = mysqli_query($connect, $cek_bukti_terima);
                                $cek_data_bukti_terima = mysqli_fetch_array($query_bukti_terima);
                                $id_inv_bukti = $cek_data_bukti_terima['id_inv_bukti'];
                                $id_inv = $cek_data_bukti_terima['id_inv_nonppn'];

                                if($id_inv_bukti != 0){
                                    // Menampilkan ID Finance
                                    $sql_finance = $connect->query("SELECT id_finance FROM finance WHERE id_inv = '$id_inv'");
                                    $data_finance = mysqli_fetch_array($sql_finance);
                                    $id_finance =  $data_finance['id_finance'];
                                    ?>
                                        <!-- Button modal Bukti Terima -->
                                        <button type="button" class="btn btn-primary mb-2 view_bukti" data-bs-toggle="modal" data-bs-target="#buktiKirim" data-id="<?php echo $id_finance ?>">
                                            <i class="bi bi-file-earmark-image"></i> Bukti Terima
                                        </button>
                                        <!-- End Button Modal Bukti Terima -->
                                    <?php
                                }
                            ?>
                            <?php
                            $id_inv_nonppn = base64_decode($_GET['id']);
                            $sql_cek = "SELECT 
                                        nonppn.id_inv_nonppn,
                                        nonppn.kategori_inv,
                                        sr.no_spk,
                                        trx.status_trx
                                        FROM inv_nonppn AS nonppn
                                        JOIN spk_reg sr ON (nonppn.id_inv_nonppn = sr.id_inv)
                                        JOIN transaksi_produk_reg trx ON(sr.id_spk_reg = trx.id_spk)
                                        WHERE nonppn.id_inv_nonppn = '$id_inv_nonppn' AND status_trx = '1' ORDER BY no_spk ASC";
                            $query_cek = mysqli_query($connect, $sql_cek);
                            $data_cek = mysqli_fetch_array($query_cek);
                            $total_data = mysqli_num_rows($query_cek);
                            ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
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
                                $sql_trx = "SELECT 
                                                nonppn.id_inv_nonppn, 
                                                nonppn.kategori_inv,
                                                
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
                                            FROM inv_nonppn AS nonppn
                                            LEFT JOIN spk_reg spk ON (nonppn.id_inv_nonppn = spk.id_inv)
                                            LEFT JOIN transaksi_produk_reg trx ON trx.id_spk = spk.id_spk_reg
                                            LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                                            LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                                            LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                            LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                            WHERE nonppn.id_inv_nonppn = '$id_nonppn_decode' AND status_trx = '1' ORDER BY no_spk ASC";
                                $trx_produk_reg = mysqli_query($connect, $sql_trx);
                                while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                                    $namaProduk = detailSpk::getDetail($data_trx['nama_produk'], $data_trx['nama_set_marwa']);
                                    $nama_merk = detailSpk::getMerk($data_trx['merk_produk'], $data_trx['merk_set']);
                                    $disc = $data_trx['disc'];
                                    $id_produk = $data_trx['id_produk'];
                                    $satuan = $data_trx['satuan'];
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
                                        <td class="text-center"><?php echo $satuan ?></td>
                                        <td class="text-center text-nowrap"><?php echo $nama_merk ?></td>
                                        <td class="text-end"><?php echo number_format($data_trx['harga']) ?></td>
                                        <?php
                                        if ($total_data != 0) {
                                            if ($data_cek['kategori_inv'] == 'Diskon') {
                                                echo "<td class='text-end'>" . $disc . "</td>";
                                            }
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
            </div>
            <!-- Modal  Bukti Terima-->
            <div class="modal fade" id="buktiKirim" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Bukti Terima</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="buktiKirim-x"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card-body" id="bukti_kirim">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal Bukti Terima -->
        </section>
    </main><!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
    <!-- Script untuk lighbox -->
    <script src="../assets/vendor/lightbox/dist/js/picturefill.min.js"></script>
    <script src="../assets/vendor/lightbox/dist/js/lightgallery-all.min.js"></script>
    <script src="../assets/vendor/lightbox/lib/jquery.mousewheel.min.js"></script>

</body>
</html>
<!-- Untuk menampilkan data Histori pad amodal -->
<script>
   $(document).ready(function(){
    $('.view_bukti').click(function(){
        var data_id = $(this).data("id");
        var label = "Bukti Terima Barang";
        $.ajax({
            url: "bukti-kirim.php",
            method: "POST",
            data: {data_id: data_id, label: label},
            success: function(data){
                console.log("Response from server: " + data);
                $("#bukti_kirim").html(data);
                $("#buktiKirim").modal('show');
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log("Error: " + textStatus + " - " + errorThrown);
            }
        });
    });
});

</script>
<!--End Script Untuk Modal History -->