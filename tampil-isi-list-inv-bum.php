<?php
$page = 'inv';
$page2 = 'list-inv';
include "akses.php";
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
        @media only screen and (max-width: 500px) {
            body {
                font-size: 15px;
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
                    <h5><strong>DETAIL INVOICE BUM</strong></h5>
                </div>
                <?php
                include "koneksi.php";
                $id_inv = base64_decode($_GET['id']);
                $sql = "SELECT 
                            bum.*, 
                            sr.id_user, sr.id_customer, sr.id_inv, sr.no_spk, sr.no_po, sr.tgl_pesanan,
                            cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
                            FROM inv_bum AS bum
                            JOIN spk_reg sr ON (bum.id_inv_bum = sr.id_inv)
                            JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                            JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                            JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                            WHERE bum.id_inv_bum = '$id_inv'";
                $query = mysqli_query($connect, $sql);
                $data = mysqli_fetch_array($query);
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
                                            bum.*,
                                            sr.id_user, sr.id_customer, sr.id_inv, sr.no_spk, sr.no_po, sr.tgl_pesanan,
                                            cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
                                            FROM inv_bum AS bum
                                            JOIN spk_reg sr ON (bum.id_inv_bum = sr.id_inv)
                                            JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                            JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                                            JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                                            WHERE bum.id_inv_bum = '$id_inv'";
                                    $query = mysqli_query($connect, $sql);
                                    $totalData = mysqli_num_rows($query);
                                    while ($data2 = mysqli_fetch_array($query)) {
                                        $id_inv = $data2['id_inv_bum'];
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
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Tgl. Invoice</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['tgl_inv'] ?>
                                </div>
                            </div>
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
                                    <p style="float: left;">Alamat</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['alamat'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Note</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php
                                    if ($data['note_inv'] != '') {
                                        echo $data['note_inv'];
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </div>
                            </div>
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
            <div class="card shadow">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <div class="text-start mb-3">
                            <a href="list-invoice.php?sort=baru" class="btn btn-warning btn-detail">
                                <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                            </a>
                            <?php
                            $id_inv_bum = base64_decode($_GET['id']);
                            $sql_cek = "SELECT 
                                        bum.id_inv_bum, kategori_inv,
                                        sr.id_inv, sr.no_spk,
                                        trx.*, 
                                        spr.stock, 
                                        tpr.nama_produk, 
                                        tpr.harga_produk, mr.* 
                                        FROM inv_bum AS bum
                                        JOIN spk_reg sr ON (bum.id_inv_bum = sr.id_inv)
                                        JOIN transaksi_produk_reg trx ON(sr.id_spk_reg = trx.id_spk)
                                        JOIN stock_produk_reguler spr ON(trx.id_produk = spr.id_produk_reg)
                                        JOIN tb_produk_reguler tpr ON(trx.id_produk = tpr.id_produk_reg)
                                        JOIN tb_merk mr ON (tpr.id_merk = mr.id_merk)
                                        WHERE bum.id_inv_bum = '$id_inv_bum' AND status_trx = '1' ORDER BY no_spk ASC";
                            $query_cek = mysqli_query($connect, $sql_cek);
                            $data_cek = mysqli_fetch_array($query_cek);
                            $total_data = mysqli_num_rows($query_cek);
                            ?>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered">
                        <?php
                        if ($total_data != 0) {
                            if ($data_cek['kategori_inv'] != 'Diskon') {
                                echo '
                                        <thead>
                                            <tr class="text-white" style="background-color: #051683;">
                                                <th class="text-center p-3" style="width:20px">No</th>
                                                <th class="text-center p-3" style="width:80px">No. SPK</th>
                                                <th class="text-center p-3" style="width:200px">Nama Produk</th>
                                                <th class="text-center p-3" style="width:100px">Merk</th>
                                                <th class="text-center p-3" style="width:100px">Harga</th>
                                                <th class="text-center p-3" style="width:80px">Qty Order</th>
                                                <th class="text-center p-3" style="width:80px">Total</th>
                                            </tr>
                                        </thead>';
                            } else {
                                echo '
                                        <thead>
                                            <tr class="text-white" style="background-color: #051683;">
                                                <th class="text-center p-3" style="width:20px">No</th>
                                                <th class="text-center p-3" style="width:100px">No. SPK</th>
                                                <th class="text-center p-3" style="width:200px">Nama Produk</th>
                                                <th class="text-center p-3" style="width:100px">Merk</th>
                                                <th class="text-center p-3" style="width:100px">Harga</th>
                                                <th class="text-center p-3" style="width:100px">Diskon</th>
                                                <th class="text-center p-3" style="width:80px">Qty Order</th>
                                                <th class="text-center p-3" style="width:80px">Total</th>
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
                            $id_bum_decode = base64_decode($_GET['id']);
                            $no = 1;
                            $sql_trx = "SELECT 
                                        bum.id_inv_bum,
                                        sr.id_inv, sr.no_spk,
                                        trx.*, 
                                        spr.stock, 
                                        tpr.nama_produk, 
                                        tpr.harga_produk, mr.* 
                                        FROM inv_bum AS bum
                                        JOIN spk_reg sr ON (bum.id_inv_bum = sr.id_inv)
                                        JOIN transaksi_produk_reg trx ON(sr.id_spk_reg = trx.id_spk)
                                        JOIN stock_produk_reguler spr ON(trx.id_produk = spr.id_produk_reg)
                                        JOIN tb_produk_reguler tpr ON(trx.id_produk = tpr.id_produk_reg)
                                        JOIN tb_merk mr ON (tpr.id_merk = mr.id_merk)
                                        WHERE bum.id_inv_bum = '$id_bum_decode' AND status_trx = '1' ORDER BY no_spk ASC";
                            $trx_produk_reg = mysqli_query($connect, $sql_trx);
                            while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                                $disc = $data_trx['disc'];
                                $id_spk = $data_trx['id_spk'];
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $no; ?></td>
                                    <td class="text-center"><?php echo $data_trx['no_spk']; ?></td>
                                    <td><?php echo $data_trx['nama_produk'] ?></td>
                                    <td class="text-center"><?php echo $data_trx['nama_merk'] ?></td>
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
                        <!-- Modal -->
                    </table>
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