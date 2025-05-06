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
                    <h5><strong>DETAIL INVOICE PPN</strong></h5>
                </div>
                <?php
                include "koneksi.php";
                $id_inv = base64_decode($_GET['id']);
                $sql = "SELECT 
                        ppn.*, 
                        sr.id_user, sr.id_customer, sr.id_inv, sr.no_spk, sr.no_po, sr.tgl_pesanan, sr.note AS note_spk, sr.petugas,
                        cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
                        FROM inv_ppn AS ppn
                        JOIN spk_reg sr ON (ppn.id_inv_ppn = sr.id_inv)
                        JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                        JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                        JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                        WHERE ppn.id_inv_ppn = '$id_inv'";
                $query = mysqli_query($connect, $sql);
                $data = mysqli_fetch_array($query);
                $ongkir = $data['ongkir'];
                $sp_disc = $data['sp_disc'];
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
                                    include "koneksi.php";
                                    $id_inv = base64_decode($_GET['id']);
                                    $no = 1;
                                    $sql = "SELECT 
                                                ppn.*, 
                                                sr.id_user, sr.id_customer, sr.id_inv, sr.no_spk, sr.no_po, sr.tgl_pesanan,
                                                cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
                                                FROM inv_ppn AS ppn
                                                JOIN spk_reg sr ON (ppn.id_inv_ppn = sr.id_inv)
                                                JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                                JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                                                JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                                                WHERE ppn.id_inv_ppn = '$id_inv'";
                                    $query = mysqli_query($connect, $sql);
                                    $totalData = mysqli_num_rows($query);

                                    while ($data2 = mysqli_fetch_array($query)) {
                                        $id_inv = $data2['id_inv_ppn'];
                                        $kat_inv = $data2['kategori_inv'];
                                        $id_cs = $data2['id_customer'];
                                    ?>
                                        <p><?php echo $no; ?>. (<?php echo $data2['tgl_pesanan'] ?>) / <?php if (!empty($data2['no_po'])) {
                                                                                                            echo "(" . $data2['no_po'] . ")/";
                                                                                                        } ?> (<?php echo $data2['no_spk'] ?>)</p>
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
                                                <p style="float: left;">Sp. Diskon</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data['sp_disc'] . ' %
                                            </div>
                                        </div>';
                            }
                            ?>

                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card-body p-3 border" style="min-height: 234px;">
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
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Pelanggan</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['nama_cs'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Pelanggan Inv</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['cs_inv'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Alamat</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['alamat'] ?>
                                </div>
                            </div>
                            <?php
                                $note_spk = $data['note_spk'];

                                $items_spk = explode("\n", trim($note_spk));
                                if (!empty($note_spk)) {
                                    echo '
                                        <div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Note SPK</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                    ';

                                    foreach ($items_spk as $notes_spk) {
                                        echo trim($notes_spk) . '<br>';
                                    }

                                    echo '
                                            </div>
                                        </div>';
                                }
                            ?>
                            <?php
                                $note_inv = $data['note_inv'];

                                $items_inv = explode("\n", trim($note_inv));
                                if (!empty($note_inv)) {
                                    echo '
                                        <div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Note Invoice</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                    ';

                                    foreach ($items_inv as $notes_inv) {
                                        echo trim($notes_inv) . '<br>';
                                    }

                                    echo '
                                            </div>
                                        </div>';
                                }
                            ?>
                            <?php
                            if ($data['ongkir'] != 0) {
                                echo '<div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">Ongkir</p>
                                            <p style="float: right;">:</p>
                                        </div>
                                        <div class="col-7">
                                            ' . number_format($data['ongkir']) . '
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
                    <div class="text-start">
                        <a href="invoice-reguler.php?sort=baru" class="btn btn-warning btn-detail">
                            <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                        </a>
                    </div>
                </div>            
                <div class="table-responsive p-3">
                    <button type="button" class="btn btn-secondary p-2">Nama Petugas : <?php echo $petugas ?></button>
                    <table class="table table-striped table-bordered" id="table3">
                        <?php
                            $id_inv_ppn = base64_decode($_GET['id']);
                            $sql_cek = "SELECT 
                                        ppn.id_inv_ppn, kategori_inv,
                                        sr.id_inv, sr.no_spk,
                                        trx.status_trx 
                                        FROM inv_ppn AS ppn
                                        JOIN spk_reg sr ON (ppn.id_inv_ppn = sr.id_inv)
                                        JOIN transaksi_produk_reg trx ON(sr.id_spk_reg = trx.id_spk)
                                        WHERE ppn.id_inv_ppn = '$id_inv_ppn' AND status_trx = '1' ORDER BY no_spk ASC";
                            $query_cek = mysqli_query($connect, $sql_cek);
                            $data_cek = mysqli_fetch_array($query_cek);
                            $total_data = mysqli_num_rows($query_cek);
                            ?>
                        <?php
                        if ($total_data != 0) {
                            if ($data_cek['kategori_inv'] != 'Diskon') {
                                echo '
                                    <thead>
                                        <tr class="text-white" style="background-color: #051683;">
                                            <th class="text-center p-3 text-nowrap" style="width:20px">No</th>
                                            <th class="text-center p-3 text-nowrap" style="width:80px">No. SPK</th>
                                            <th class="text-center p-3 text-nowrap" style="width:200px">Nama Produk</th>
                                            <th class="text-center p-3 text-nowrap" style="width:100px">Satuan</th>
                                            <th class="text-center p-3 text-nowrap" style="width:100px">Merk</th>
                                            <th class="text-center p-3 text-nowrap" style="width:100px">Harga</th>
                                            <th class="text-center p-3 text-nowrap" style="width:80px">Qty Order</th>
                                            <th class="text-center p-3 text-nowrap" style="width:80px">Total</th>
                                        </tr>
                                    </thead>';
                            } else {
                                echo '
                                    <thead>
                                        <tr class="text-white" style="background-color: #051683;">
                                            <th class="text-center p-3 text-nowrap" style="width:20px">No</th>
                                            <th class="text-center p-3 text-nowrap" style="width:100px">No. SPK</th>
                                            <th class="text-center p-3 text-nowrap" style="width:200px">Nama Produk</th>
                                            <th class="text-center p-3 text-nowrap" style="width:100px">Satuan</th>
                                            <th class="text-center p-3 text-nowrap" style="width:100px">Merk</th>
                                            <th class="text-center p-3 text-nowrap" style="width:100px">Harga</th>
                                            <th class="text-center p-3 text-nowrap" style="width:100px">Diskon</th>
                                            <th class="text-center p-3 text-nowrap" style="width:80px">Qty Order</th>
                                            <th class="text-center p-3 text-nowrap" style="width:80px">Total</th>
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
                            $id_ppn_decode = base64_decode($_GET['id']);
                            $no = 1;
                            $sub_total = 0;
                            $sql_trx = "SELECT 
                                            ppn.id_inv_ppn, 
                                            ppn.kategori_inv,
                                            ppn.sp_disc,
                                            
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
                                    <td class="text-center text-nowrap"><?php echo $satuan_produk ?></td>
                                    <td class="text-center text-nowrap"><?php echo $nama_merk ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['harga']) ?></td>
                                    <?php
                                    if ($total_data != 0) {
                                        if ($data_cek['kategori_inv'] == 'Diskon') {
                                            echo "<td class='text-end'>" . $disc . "</td>";
                                        }
                                    }
                                    ?>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['qty']) ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['total_harga']) ?></td>
                                </tr>
                                <?php $no++; ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>   
        </section>
    </main><!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>
</html>