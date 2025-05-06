<?php
$page  = 'transaksi';
$page2 = 'spk';
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
                font-size: 10px;
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
        <section>
            <div class="container-fluid">
                <div class="card shadow p-2">
                    <div class="card-header text-center">
                        <h5><strong>DETAIL SPK</strong></h5>
                    </div>
                    <?php
                    include "koneksi.php";
                    $id_spk = base64_decode($_GET['id']);
                    $sql = "SELECT sr.*, cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
                    FROM spk_reg AS sr
                    JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                    JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                    JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                    WHERE sr.id_spk_reg = '$id_spk'";
                    $query = mysqli_query($connect, $sql);
                    $data = mysqli_fetch_array($query);
                    ?>
                    <div class="card-body">
                        <div class="row mt-3">
                            <div class="col-sm-6">
                                <div class="card-body p-3 border">
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">No. SPK</p>
                                            <p style="float: right;">:</p>
                                        </div>
                                        <div class="col-7">
                                            <?php echo $data['no_spk'] ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">Tanggal SPK</p>
                                            <p style="float: right;">:</p>
                                        </div>
                                        <div class="col-7">
                                            <?php echo $data['tgl_spk'] ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">No. PO</p>
                                            <p style="float: right;">:</p>
                                        </div>
                                        <div class="col-7">
                                            <?php
                                            if ($data['no_po'] != '') {
                                                echo $data['no_po'];
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">Tanggal Pesanan</p>
                                            <p style="float: right;">:</p>
                                        </div>
                                        <div class="col-7">
                                            <?php echo $data['tgl_pesanan'] ?>
                                        </div>
                                    </div>
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
                                            if ($data['note'] != '') {
                                                echo $data['note'];
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tampil data -->
                <div class="card shadow">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <div class="text-start mb-3">
                                <a href="transaksi-cancel.php?sort=baru" class="btn btn-warning btn-detail">
                                    <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                                </a>
                            </div>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                        <th class="text-center p-3" style="width:20px">No</th>
                                        <th class="text-center p-3" style="width:300px">Nama Produk</th>
                                        <th class="text-center p-3" style="width:100px">Merk</th>
                                        <th class="text-center p-3" style="width:100px">Harga</th>
                                        <th class="text-center p-3" style="width:80px">Qty Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include "koneksi.php";
                                    $year = date('y');
                                    $day = date('d');
                                    $month = date('m');
                                    $id_spk_decode = base64_decode($_GET['id']);
                                    $no = 1;
                                    $sql_trx = "SELECT sr.*, trx.*, spr.stock, tpr.nama_produk, tpr.harga_produk, mr.* 
                                                    FROM spk_reg AS sr
                                                    JOIN trx_cancel trx ON(sr.id_spk_reg = trx.id_spk)
                                                    JOIN stock_produk_reguler spr ON(trx.id_produk = spr.id_produk_reg)
                                                    JOIN tb_produk_reguler tpr ON(trx.id_produk = tpr.id_produk_reg)
                                                    JOIN tb_merk mr ON (tpr.id_merk = mr.id_merk)
                                                    WHERE sr.id_spk_reg = '$id_spk_decode'";
                                    $trx_produk_reg = mysqli_query($connect, $sql_trx);
                                    while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no; ?></td>
                                            <td><?php echo $data_trx['nama_produk'] ?></td>
                                            <td class="text-center"><?php echo $data_trx['nama_merk'] ?></td>
                                            <td class="text-end"><?php echo number_format($data_trx['harga_produk']) ?></td>
                                            <td class="text-end"><?php echo number_format($data_trx['qty']) ?></td>
                                        </tr>
                                        <?php $no++; ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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