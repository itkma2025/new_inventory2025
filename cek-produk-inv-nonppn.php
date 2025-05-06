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
        <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
        <!-- END SWEET ALERT -->
        <section>
            <div class="container-fluid">
                <?php  
                    $id_role = $_SESSION['tiket_role'];
                    $sql_role = "SELECT * FROM user_role WHERE id_user_role='$id_role'";
                    $query_role = mysqli_query($connect, $sql_role) or die(mysqli_error($connect));
                    $data_role = mysqli_fetch_array($query_role);
                ?>
                <div class="card shadow p-2">
                    <div class="card-header text-center">
                        <h5><strong>DETAIL INVOICE NONPPN</strong></h5>
                    </div>
                    <?php
                    $id_inv = decrypt($_GET['id'], $key_global);
                    $sql = "SELECT 
                            nonppn.*, 
                            sr.id_user, sr.id_customer, sr.id_inv, sr.no_spk, sr.no_po, sr.tgl_pesanan, sr.petugas, sr.note AS note_spk,
                            cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
                            FROM inv_nonppn AS nonppn
                            JOIN spk_reg sr ON (nonppn.id_inv_nonppn = sr.id_inv)
                            JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                            JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                            JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                            WHERE nonppn.id_inv_nonppn = '$id_inv'";
                    $query = mysqli_query($connect, $sql);
                    $data = mysqli_fetch_array($query);
                    $ongkir = $data['ongkir'];
                    $sp_disc = $data['sp_disc'];
                    $jenis_inv = $data['kategori_inv'];
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
                                        $no = 1;
                                        $sql = "SELECT 
                                                    nonppn.*, 
                                                    sr.id_user, sr.id_customer, sr.id_inv, sr.no_spk, sr.no_po, sr.tgl_pesanan,
                                                    cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
                                                    FROM inv_nonppn AS nonppn
                                                    JOIN spk_reg sr ON (nonppn.id_inv_nonppn = sr.id_inv)
                                                    JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                                    JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                                                    JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                                                    WHERE nonppn.id_inv_nonppn = '$id_inv'";
                                        $query = mysqli_query($connect, $sql);
                                        $totalData = mysqli_num_rows($query);

                                        while ($data2 = mysqli_fetch_array($query)) {
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
                        <?php  
                            if ($data_role['role'] == "Super Admin" || $data_role['role'] == "Admin Penjualan") {
                                ?>
                                    <div class="text-center mt-3 mb-3">
                                        <button class="btn btn-info btn-md" data-bs-toggle="modal" data-bs-target="#editPelanggan"><i class="bi bi-pencil"></i> Edit Data Detail</button>
                                    </div>
                                <?php
                            }
                        ?>
                        <!-- Modal -->
                        <div class="modal fade" id="editPelanggan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Data Detail</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                   <form action="proses/proses-invoice-nonppn.php" method="POST">
                                        <input type="hidden" name="id_inv" value="<?php echo encrypt($id_inv, $key_global) ?>">
                                        <div class="mb-3">
                                            <label>Customer Invoice</label>
                                            <input type="text" class="form-control" name="cs_inv" value="<?php echo $data['cs_inv'] ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label>Alamat</label>
                                            <textarea class="form-control" name="alamat" cols="30" rows="3"><?php echo $data['alamat'] ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label>No PO</label>
                                            <input type="text" class="form-control" name="no_po" value="<?php echo $data['no_po'] ?>">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="ubah-cs-inv" class="btn btn-primary">Edit Data Detail</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                   </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tampil data -->
                <div class="card shadow">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="text-start">
                                    <a href="invoice-reguler.php?sort=baru" class="btn btn-warning btn-detail mb-2">
                                        <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                                    </a>
                                    <?php
                                        $sql_cek = "SELECT 
                                                        nonppn.id_inv_nonppn, kategori_inv,
                                                        sr.id_inv, sr.no_spk,
                                                        trx.status_trx 
                                                    FROM inv_nonppn AS nonppn
                                                    JOIN spk_reg sr ON (nonppn.id_inv_nonppn = sr.id_inv)
                                                    JOIN transaksi_produk_reg trx ON(sr.id_spk_reg = trx.id_spk)
                                                    WHERE nonppn.id_inv_nonppn = '$id_inv' AND status_trx = '1' ORDER BY no_spk ASC";
                                        $query_cek = mysqli_query($connect, $sql_cek);
                                        $data_cek = mysqli_fetch_array($query_cek);
                                        $total_data = mysqli_num_rows($query_cek);
                                    ?>
                                    <?php 
                                        if ($data_role['role'] == "Super Admin" || $data_role['role'] == "Admin Penjualan") {
                                            ?>
                                                <button class="btn btn-info btn-detail mb-2" data-bs-toggle="modal" data-bs-target="#ubahKat">
                                                    <i class="bi bi-arrow-left-right"></i> Ubah Kategori Invoice
                                                </button>
                                                <a href="#" class="btn btn-primary btn-detail mb-2" data-bs-toggle="modal" data-bs-target="#addSpk">
                                                    <i class="bi bi-plus-circle"></i> Tambah SPK
                                                </a>
                                                <?php
                                                if ($kat_inv == 'Spesial Diskon') {
                                                    echo '
                                                        <button class="btn btn-secondary mb-2" data-bs-toggle="modal" data-bs-target="#inputSpdisc"><i class="bi bi-percent"></i> Spesial Diskon</button>';
                                                }
                                                $sql_cek2 = "SELECT 
                                                                nonppn.id_inv_nonppn, kategori_inv,
                                                                sr.id_inv, sr.no_spk,
                                                                trx.status_trx 
                                                            FROM inv_nonppn AS nonppn
                                                            JOIN spk_reg sr ON (nonppn.id_inv_nonppn = sr.id_inv)
                                                            JOIN transaksi_produk_reg trx ON(sr.id_spk_reg = trx.id_spk)
                                                            WHERE nonppn.id_inv_nonppn = '$id_inv' AND status_trx = '0' ORDER BY no_spk ASC";
                                                $query_cek2 = mysqli_query($connect, $sql_cek2);
                                                $data_cek2 = mysqli_fetch_array($query_cek2);
                                                $total_data2 = mysqli_num_rows($query_cek2);
                                                if ($total_data2 == 0) {
                                                    echo ' 
                                                            <button class="btn btn-warning btn-detail mb-2" data-bs-toggle="modal" data-bs-target="#Dikirim">
                                                                <i class="bi bi-send"></i> Proses Dikirim
                                                            </button>
                                                            <a href="cetak-inv-nonppn-reg.php?id=' . encrypt($id_inv, $key_global) . '" class="btn btn-secondary mb-2"><i class="bi bi-printer-fill"></i> Cetak Invoice</a>
                                                            <input type="hidden" name="id_spk_reg" value="' . encrypt($id_inv, $key_global) . '">
                                                            <a href="generate_pdf_nonppn.php?id=' . encrypt($id_inv, $key_global) . '" class="btn btn-info mb-2"><i class="bi bi-file-pdf"></i> Download PDF</a>
                                                            ';
                                                }
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-end">
                                    <?php  
                                        $sql_total_inv = mysqli_query($connect, "SELECT total_inv FROM inv_nonppn WHERE id_inv_nonppn = '$id_inv'");
                                        $data_total_inv = mysqli_fetch_array($sql_total_inv);
                                        $tampil_total_inv = $data_total_inv['total_inv'];
                                    ?>
                                    <button type="button" class="btn btn-outline-dark">
                                        Total Invoice<br>
                                        Rp. <?php echo number_format($tampil_total_inv, 0,'.','.') ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Input SPdisc Inv -->
                    <div class="modal fade" id="inputSpdisc" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Input Data Spesial Diskon</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="proses/proses-invoice-nonppn.php" method="POST">
                                        <?php
                                        $id_inv_kat = $id_inv;
                                        $sql_kat = "SELECT nonppn.sp_disc FROM inv_nonppn AS nonppn WHERE nonppn.id_inv_nonppn = '$id_inv_kat'";
                                        $query_kat = mysqli_query($connect, $sql_kat);
                                        $data_kat = mysqli_fetch_array($query_kat);
                                        ?>
                                        <input type="hidden" name="id_inv" value="<?php echo $id_inv_kat ?>" readonly>
                                        <div class="mb-3">
                                            <label>Spesial Diskon (%)</label>
                                            <input type="number" step="any" class="form-control" name="spdisc" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary" name="ubah-sp">Update Kategori</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal -->
                    <div class="table-responsive p-3">
                        <button type="button" class="btn btn-secondary p-2">Nama Petugas : <?php echo $petugas ?></button>
                        <table class="table table-striped table-bordered" id="table3">
                            <?php
                            if ($total_data != 0) {
                                if ($data_cek['kategori_inv'] != 'Diskon') {
                                    ?>
                                        <thead>
                                            <tr class="text-white" style="background-color: #051683;">
                                                <th class="text-center p-3 text-nowrap" style="width:20px">No</th>
                                                <th class="text-center p-3 text-nowrap" style="width:80px">No. SPK</th>
                                                <th class="text-center p-3 text-nowrap" style="width:200px">Nama Produk</th>
                                                <th class="text-center p-3 text-nowrap" style="width:100px">Merk</th>
                                                <th class="text-center p-3 text-nowrap" style="width:80px">Qty Order</th>
                                                <th class="text-center p-3 text-nowrap" style="width:100px">Satuan</th>
                                                <th class="text-center p-3 text-nowrap" style="width:100px">Harga</th>
                                                <th class="text-center p-3 text-nowrap" style="width:80px">Total</th>
                                                <?php  
                                                    if ($data_role['role'] == "Super Admin" || $data_role['role'] == "Admin Penjualan") {
                                                        ?>
                                                            <th class="text-center p-3 text-nowrap" style="width:80px">Aksi</th>
                                                        <?php
                                                    }
                                                ?>
                                            </tr>
                                        </thead>
                                    <?php
                                } else {
                                    ?>
                                        <thead>
                                            <tr class="text-white" style="background-color: #051683;">
                                                <th class="text-center p-3 text-nowrap" style="width:20px">No</th>
                                                <th class="text-center p-3 text-nowrap" style="width:100px">No. SPK</th>
                                                <th class="text-center p-3 text-nowrap" style="width:200px">Nama Produk</th>
                                                <th class="text-center p-3 text-nowrap" style="width:100px">Merk</th>
                                                <th class="text-center p-3 text-nowrap" style="width:80px">Qty Order</th>
                                                <th class="text-center p-3 text-nowrap" style="width:100px">Satuan</th>
                                                <th class="text-center p-3 text-nowrap" style="width:100px">Harga</th>
                                                <th class="text-center p-3 text-nowrap" style="width:100px">Diskon</th>       
                                                <th class="text-center p-3 text-nowrap" style="width:80px">Total</th>
                                                <?php  
                                                    if ($data_role['role'] == "Super Admin" || $data_role['role'] == "Admin Penjualan") {
                                                        ?>
                                                            <th class="text-center p-3 text-nowrap" style="width:80px">Aksi</th>
                                                        <?php
                                                    }
                                                ?>
                                            </tr>
                                        </thead>
                                    <?php
                                }
                            }
                            ?>
                            <tbody>
                                <?php
                                include "koneksi.php";
                                $year = date('y');
                                $day = date('d');
                                $month = date('m');
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
                                                COALESCE(tpr.nama_produk, tpe.nama_produk, tpsm.nama_set_marwa, tpse.nama_set_ecat) AS nama_produk,
                                                COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                                                COALESCE(mr_produk.nama_merk, mr_produk_ecat.nama_merk, mr_set.nama_merk, mr_set_ecat.nama_merk)AS merk_produk -- Nama merk untuk produk reguler
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
                                            WHERE nonppn.id_inv_nonppn = '$id_inv' AND status_trx = '1' ORDER BY trx.created_date ASC";
                                $trx_produk_reg = mysqli_query($connect, $sql_trx);
                                while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                                    $namaProduk = $data_trx['nama_produk'];
                                    $id_produk = $data_trx['id_produk'];
                                    $satuan = $data_trx['satuan'];
                                    $nama_merk = $data_trx['merk_produk'];
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
                                        <td class="text-center"><?php echo $nama_merk ?></td>
                                        <td class="text-end text-nowrap"><?php echo number_format($data_trx['qty']) ?></td>
                                        <td class="text-center text-nowrap"><?php echo $satuan_produk ?></td>
                                        <td class="text-end text-nowrap"><?php echo number_format($data_trx['harga']) ?></td>
                                        <?php
                                        if ($total_data != 0) {
                                            if ($data_cek['kategori_inv'] == 'Diskon') {
                                                echo "<td class='text-end'>" . $disc . "</td>";
                                            }
                                        }
                                        ?>
                                       
                                        <td class="text-end text-nowrap"><?php echo number_format($data_trx['total_harga']) ?></td>
                                        <?php  
                                            if ($data_role['role'] == "Super Admin" || $data_role['role'] == "Admin Penjualan") {
                                                ?>
                                                    <td class="text-center text-nowrap">
                                                        <?php
                                                        if ($total_data != 0) {
                                                            if ($data_cek['kategori_inv'] == 'Diskon') {
                                                                echo '<button class="btn btn-warning btn-sm" data-id="' . $data_trx['id_transaksi'] . '" data-nama="' . $data_trx['nama_produk_spk'] . '" data-hargadisc="' . number_format($data_trx['harga']) . '" data-diskon="' . $data_trx['disc'] . '" data-qty="' . number_format($data_trx['qty']) . '" data-bs-toggle="modal" data-bs-target="#edit-diskon"><i class="bi bi-pencil"></i></button>';
                                                            } else {
                                                                echo '<button class="btn btn-warning btn-sm" data-id="' . $data_trx['id_transaksi'] . '"  data-nama="' . $data_trx['nama_produk_spk'] . '" data-harga="' . number_format($data_trx['harga']) . '" data-qty="' . number_format($data_trx['qty']) . '" data-bs-toggle="modal" data-bs-target="#edit"><i class="bi bi-pencil"></i></button>';
                                                            }
                                                        }
                                                        ?>
                                                    
                                                    </td>
                                                <?php
                                            }
                                        ?>
                                    </tr>
                                    <?php $no++; ?>
                                <?php } ?>
                            </tbody>
                            <!-- Modal -->
                            <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Data</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="proses/proses-invoice-nonppn.php" method="POST">
                                                <input type="hidden" name="id_trx" id="id_trx" readonly>
                                                <input type="hidden" name="id_inv" value="<?php echo $id_nonppn_decode ?>" readonly>
                                                <div class="mb-3">
                                                    <label><strong>Nama Produk</strong></label>
                                                    <input type="text" class="form-control" name="nama_produk" id="nama_produk" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label><strong>Harga</strong></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="basic-addon1">Rp</span>
                                                        <input type="text" class="form-control text-end harga_produk" name="harga_produk" id="harga_produk" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary" name="update-harga">Update Data</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="edit-diskon" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Harga</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="proses/proses-invoice-nonppn.php" method="POST">
                                                <input type="hidden" name="id_trx" id="id_trxdisc" readonly>
                                                <input type="hidden" name="id_inv" value="<?php echo $id_nonppn_decode ?>" readonly>
                                                <div class="mb-3">
                                                    <label><strong>Nama Produk</strong></label>
                                                    <input type="text" class="form-control" name="nama_produk" id="nama_produk_disc" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label><strong>Harga</strong></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="basic-addon1">Rp</span>
                                                        <input type="text" class="form-control text-end harga_produk" name="harga_produk" id="harga_produk_disc" required>
                                                        <input type="hidden" class="form-control text-end harga_produk" name="qty" id="qtydisc" required>
                                                    </div>
                                                </div>
                                                <div class="col-mb-3">
                                                    <label><b>Diskon</b></label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control text-end harga_produk" name="disc" id="discc" required>
                                                        <span class="input-group-text" id="basic-addon1">%</span>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary" name="update-harga-diskon">Update Data</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </table>
                    </div>
                </div>
                <!-- Kode Untuk Update Total Inv -->
                <?php  
                    $hasil_sp_disc = $sp_disc / 100;
                    $total_sp_disc = $sub_total * $hasil_sp_disc;
                    $grand_total = $sub_total - $total_sp_disc;
                    $grand_total_nonppn = $grand_total;
                    $update_total_inv = mysqli_query($connect, "UPDATE inv_nonppn SET total_inv = '$grand_total_nonppn' WHERE id_inv_nonppn = '$id_inv'")
                ?>
                <div class="container">
                    <?php
                    if ($total_data == 0) {
                        ?>
                            <h5 class="text-center">Cek Harga Produk</h5>
                        <?php
                    }
                    ?>
                </div>
                <form action="proses/proses-invoice-nonppn.php" method="POST">
                    <?php
                    $no = 1;
                    $sql_cek_harga = "SELECT 
                                        nonppn.id_inv_nonppn, 
                                        nonppn.kategori_inv,
                                        spk.id_inv, 
                                        spk.no_spk,
                                        trx.id_transaksi,
                                        trx.id_produk,
                                        trx.harga,
                                        trx.qty,
                                        trx.disc,
                                        trx.total_harga,
                                        trx.status_trx,
                                        trx.created_date,
                                        COALESCE(tpr.nama_produk, tpe.nama_produk, tpsm.nama_set_marwa, tpse.nama_set_ecat) AS nama_produk,
                                        COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                                        COALESCE(mr_produk.nama_merk, mr_produk_ecat.nama_merk, mr_set.nama_merk, mr_set_ecat.nama_merk) AS merk_produk
                                    FROM inv_nonppn AS nonppn
                                    LEFT JOIN spk_reg spk ON (nonppn.id_inv_nonppn = spk.id_inv)
                                    LEFT JOIN transaksi_produk_reg trx ON trx.id_spk = spk.id_spk_reg
                                    LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                                    LEFT JOIN tb_produk_ecat tpe ON trx.id_produk = tpe.id_produk_ecat
                                    LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                                    LEFT JOIN tb_produk_set_ecat tpse ON trx.id_produk = tpse.id_set_ecat
                                    LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                    LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk reguler
                                    LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                    LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                                    WHERE nonppn.id_inv_nonppn = '$id_inv' AND status_trx = '0' ORDER BY trx.created_date ASC";
                    $query_cek_harga = mysqli_query($connect, $sql_cek_harga);
                    $total_cek_harga = mysqli_num_rows($query_cek_harga);
                    while ($data_cek_harga = mysqli_fetch_array($query_cek_harga)) {
                        $namaProduk = $data_cek_harga['nama_produk'];
                        $id_produk = $data_cek_harga['id_produk'];
                        $satuan = $data_cek_harga['satuan'];
                        $nama_merk =$data_cek_harga['merk_produk'];
                        $satuan_produk = '';
                        $id_produk_substr = substr($id_produk, 0, 2);
                        if ($id_produk_substr == 'BR') {
                            $satuan_produk = $satuan;
                        } else {
                            $satuan_produk = 'Set';
                        }
                    ?>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6 mb-1">
                                    <input type="hidden" name="id_trx[]" id="id_<?php echo encrypt($data_cek_harga['id_transaksi'], $key_global) ?>" value="<?php echo encrypt($data_cek_harga['id_transaksi'], $key_global) ?>" readonly>
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1"><?php echo $no; ?></span>
                                        <?php $no++ ?>
                                        <input type="text" class="form-control mobile-text" name="nama_produk[]" value="<?php echo $namaProduk ?>" required>                      
                                    </div>
                                   
                                </div>
                                <div class="col-sm-1 mb-1">
                                    <input type="text" class="form-control bg-light text-center mobile-text" value="<?php echo $nama_merk ?>" readonly>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">Rp</span>
                                        <input type="text" class="form-control text-end harga_produk  mobile-text" name="harga_produk[]" value="<?php echo number_format($data_cek_harga['harga']) ?>" required>
                                    </div>
                                </div>
                                <?php
                                    if ($total_cek_harga != 0) {
                                        if ($data_cek_harga['kategori_inv'] == 'Diskon') {
                                            echo '  <div class="col-sm-1 mb-1">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control text-end" name="disc[]" value="' . number_format($data_cek_harga['disc']) . '" required>
                                                            <span class="input-group-text" id="basic-addon1">%</span>
                                                        </div>
                                                    </div>';
                                            ?>
                                                <div class="col-sm-2 mb-1">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control bg-light text-end  mobile-text" name="qty[]" value="<?php echo number_format($data_cek_harga['qty']) ?>" readonly>
                                                        <span class="input-group-text" id="basic-addon1"><?php echo $satuan_produk ?></span>
                                                    </div>
                                                </div>
                                            <?php
                                        } else {
                                            echo '  <input type="hidden" class="form-control text-end bg-light disc" name="disc[]" value="' . number_format($data_cek_harga['disc']) . '" readonly>
                                                    <span class="input-group-text d-none" id="basic-addon1">%</span>';

                                            $sql_cb_nonppn = $connect->query("SELECT jenis_cb FROM cashback_nonppn WHERE id_inv = '$id_inv'");
                                            $data_cb_nonppn =  mysqli_fetch_array($sql_cb_nonppn);

                                            $jenis_cb = $data_cb_nonppn['jenis_cb'];

                                            // Pecah data berdasarkan koma
                                            $jenisCbArray = explode(",", $jenis_cb);

                                            // Gabungkan data dengan tanda petik
                                            $result_jenis_cb = "'" . implode("','", $jenisCbArray) . "'";

                                            // Menampilkan keterangan cashback
                                            $ket_cb = $connect->query("SELECT ket_cashback FROM keterangan_cashback WHERE id_ket_cashback IN ($result_jenis_cb)");
                                            while($data_ket_cb =  mysqli_fetch_array($ket_cb)){
                                                $cek_ket_cb = $data_ket_cb['ket_cashback'];
                                            }

                                            if($cek_ket_cb == 'Per Barang'){
                                                ?>
                                                    <div class="col-sm-1 mb-1">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control text-end disc_cb" id="disc_cb" name="disc_cb[]" value="0" required>
                                                            <span class="input-group-text" id="basic-addon1">%</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2 mb-1">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control bg-light text-end  mobile-text" name="qty[]" value="<?php echo number_format($data_cek_harga['qty']) ?>" readonly>
                                                            <span class="input-group-text" id="basic-addon1"><?php echo $satuan_produk ?></span>
                                                        </div>
                                                    </div>
                                                <?php
                                            } else {
                                                ?>
                                                    <div class="col-sm-3 mb-1">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control bg-light text-end  mobile-text" name="qty[]" value="<?php echo number_format($data_cek_harga['qty']) ?>" readonly>
                                                            <span class="input-group-text" id="basic-addon1"><?php echo $satuan_produk ?></span>
                                                        </div>
                                                    </div>
                                                <?php
                                            }
                                        } 
                                    }
                                ?>
                            </div>
                        </div>

                    <?php } ?>
                    <div class="card-body mt-3 text-end">
                        <?php
                        if ($total_cek_harga != 0) {
                            echo '<button type="submit" class="btn btn-primary" name="simpan-cek-harga" id="simpan-data"><i class="bi bi-save"></i> Simpan</button>';
                        }
                        ?>
                    </div>
                </form>
            </div>
            <!-- Modal Kategori Inv -->
            <div class="modal fade" id="ubahKat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Kategori Invoice</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Jenis Invoice Saat Ini</label>
                                <input type="text" class="form-control bg-light" name="id_inv" value="<?php echo $jenis_inv ?>" readonly>
                            </div>
                            <form action="proses/proses-invoice-nonppn.php" method="POST">
                                <?php
                                $id_inv_kat = $id_inv;
                                $sql_kat = "SELECT 
                                            nonppn.*, 
                                            sr.id_customer, sr.id_inv, sr.no_spk, sr.no_po, sr.tgl_pesanan
                                            FROM inv_nonppn AS nonppn
                                            JOIN spk_reg sr ON (nonppn.id_inv_nonppn = sr.id_inv)
                                            WHERE nonppn.id_inv_nonppn = '$id_inv_kat'";
                                $query_kat = mysqli_query($connect, $sql_kat);
                                $data_kat = mysqli_fetch_array($query_kat);
                                ?>
                                <input type="hidden" name="id_inv" value="<?php echo $id_inv_kat ?>" readonly>
                                <div class="mb-3">
                                    <label>Pilih Jenis Invoice</label>
                                    <select name="kat_inv" class="form-select">
                                        <?php
                                        $kategori_inv = $data_kat['kategori_inv'];
                                        $pilihan_sisa = array('Reguler', 'Diskon', 'Spesial Diskon');
                                        foreach ($pilihan_sisa as $pilihan) {
                                            if ($pilihan != $kategori_inv) {
                                                echo '<option value="' . $pilihan . '">' . $pilihan . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary" name="ubah-kategori">Update Kategori</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal -->
        </section>
    </main><!-- End #main -->

    <!-- Modal Dikirim-->
    <div class="modal fade" id="Dikirim" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card-header">
                        <h1 class="text-center fs-5" id="exampleModalLabel">Proses Dikirim</h1>
                    </div>
                    <div class="card-body">
                        <?php
                        $uuid = generate_uuid();
                        $year = date('y');
                        $day = date('d');
                        $month = date('m');
                        ?>
                        <form action="proses/proses-invoice-nonppn.php" method="POST" enctype="multipart/form-data" id="form-kirim">
                            <input type="hidden" name="id_status" value="STATUS-<?php echo $year ?><?php echo $month ?><?php echo $uuid ?><?php echo $day ?>">
                            <input type="hidden" name="id_inv" value="<?php echo $id_inv ?>">
                            <div class="mb-3">
                                <label>Jenis Pengiriman</label>
                                <select id="jenis-pengiriman" name="jenis_pengiriman" class="form-select" required>
                                    <option value="">Pilih...</option>
                                    <option value="Driver">Driver</option>
                                    <option value="Ekspedisi">Ekspedisi</option>
                                    <option value="Diambil Langsung">Diambil Langsung</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label id="labelDriver" style="display: none;">Pilih Driver</label>
                                <select id="pengirim" name="pengirim" class="form-select" style="display: none;">
                                    <option value="">Pilih...</option>
                                    <?php
                                    include "koneksi.php";
                                    $sql_driver = mysqli_query($connect, "SELECT us.id_user_role, us.id_user, us.nama_user, us.approval, rl.role FROM user AS us JOIN user_role rl ON (us.id_user_role = rl.id_user_role) WHERE rl.role = 'Driver' AND approval = '1'");
                                    while ($data_driver = mysqli_fetch_array($sql_driver)) {
                                    ?>
                                        <option value="<?php echo $data_driver['id_user'] ?>"><?php echo $data_driver['nama_user'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3" id="ekspedisi" style="display: none;">
                                <label>Pilih Ekspedisi</label>
                                <select name="ekspedisi" id="pilihEkspedisi" class="form-select selectize-js">
                                    <option value=""></option>
                                    <?php
                                    include "koneksi.php";
                                    $sql_ekspedisi = mysqli_query($connect, "SELECT * FROM ekspedisi");
                                    while ($data_ekspedisi = mysqli_fetch_array($sql_ekspedisi)) {
                                    ?>
                                        <option value="<?php echo $data_ekspedisi['id_ekspedisi'] ?>"><?php echo $data_ekspedisi['nama_ekspedisi'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label style="display: none;" id="labelDikirimOleh">Dikirim Oleh</label>
                                <input type="text" class="form-control" name="dikirim" id="dikirim_oleh" style="display: none;">
                            </div>
                            <div class="mb-3">
                                <label style="display: none;" id="labelPj">Penanggung Jawab</label>
                                <input type="text" class="form-control" name="pj" id="penanggung_jawab" style="display: none;">
                            </div>
                            <div class="mb-3">
                                <label id="labelDate">Tanggal Kirim</label>
                                <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl" id="date" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" name="ubah-dikirim" id="dikirim" disabled><i class="bi bi-arrow-left-right"></i> Ubah Status</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDikirim"><i class="bi bi-x-circle"> Cancel</i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- kode JS Dikirim -->
        <?php include "page/upload-img.php";  ?>
        <!-- kode JS Dikirim -->

        <script>
            function checkFileName() {
                var file1 = document.getElementById('fileku1').value;
                var file2 = document.getElementById('fileku2').value;
                var file3 = document.getElementById('fileku3').value;

                if (file1 === file2 && file2 !== "") {
                    alert("Nama file ke 2 harus berbeda!");
                    document.getElementById('fileku2').value = "";
                    document.getElementById('imagePreview2').innerHTML = "";
                }

                if (file1 === file3 && file3 !== "") {
                    alert("Nama file ke 3 harus berbeda!");
                    document.getElementById('fileku3').value = "";
                    document.getElementById('imagePreview3').innerHTML = "";
                }

                if (file2 === file3 && file3 !== "") {
                    alert("Nama file ke 3 harus berbeda!");
                    document.getElementById('fileku3').value = "";
                    document.getElementById('imagePreview3').innerHTML = "";
                }
            }
        </script>

        <script>
            const jenisPengirimanSelect = document.getElementById('jenis-pengiriman');
            const pengirimSelect = document.getElementById('pengirim');
            const labelDriver = document.getElementById('labelDriver');
            const ekspedisiSelect = document.getElementById('ekspedisi');
            const ekspedisiSelectize = document.getElementById('pilihEkspedisi');
            const labelDikirimOleh = document.getElementById('labelDikirimOleh');
            const dikirimOleh = document.getElementById('dikirim_oleh');
            const pjLabel = document.getElementById('labelPj');
            const penanggungJawab = document.getElementById('penanggung_jawab');
            let isModalShown = false;

            jenisPengirimanSelect.addEventListener('change', function() {
                if (this.value === 'Driver') {
                    labelDriver.style.display = 'block'; // Menampilkan form input
                    pengirimSelect.style.display = 'block'; // Menampilkan form input
                    pengirimSelect.setAttribute('required', 'true');
                    ekspedisiSelect.style.display = 'none'; // Menyembunyikan form input
                    ekspedisiSelect.value = ''; // Mengatur ulang nilai menjadi kosong
                    ekspedisiSelectize.removeAttribute('required');
                    ekspedisiSelectize.classList.remove('selectize-js');
                    labelDikirimOleh.style.display = 'none';
                    dikirimOleh.style.display = 'none';
                    dikirimOleh.value = '';
                    dikirimOleh.removeAttribute('required');
                    pjLabel.style.display = 'none';
                    penanggungJawab.style.display = 'none';
                    penanggungJawab.removeAttribute('required');
                    penanggungJawab.value = '';
                    dikirim.disabled = false;
                } else if (this.value === 'Ekspedisi') {
                    pengirimSelect.value = ''; // Mengatur ulang nilai menjadi kosong
                    labelDriver.style.display = 'none'; // Menyembunyikan form input
                    pengirimSelect.style.display = 'none'; // Menyembunyikan form input
                    pengirimSelect.removeAttribute('required');
                    ekspedisiSelect.style.display = 'block'; // Menampilkan form input
                    ekspedisiSelectize.classList.add('selectize');
                    ekspedisiSelectize.classList.add('selectize-js');
                    ekspedisiSelectize.required = true;
                    labelDikirimOleh.style.display = 'block';
                    dikirimOleh.style.display = 'block';
                    dikirimOleh.setAttribute('required', 'true');
                    labelPj.style.display = 'block';
                    penanggungJawab.style.display = 'block';
                    penanggungJawab.setAttribute('required' , 'true');
                    dikirim.disabled = false;       
                } else if (this.value === 'Diambil Langsung') {
                    labelDriver.style.display = 'none'; // Menampilkan form input
                    pengirimSelect.style.display = 'none'; // Menampilkan form input
                    pengirimSelect.removeAttribute('required');
                    ekspedisiSelect.style.display = 'none'; // Menyembunyikan form input
                    ekspedisiSelect.value = ''; // Mengatur ulang nilai menjadi kosong
                    ekspedisiSelectize.removeAttribute('required');
                    ekspedisiSelectize.classList.remove('selectize-js');
                    labelDikirimOleh.style.display = 'none';
                    dikirimOleh.style.display = 'none';
                    dikirimOleh.value = '';
                    dikirimOleh.removeAttribute('required');
                    pjLabel.style.display = 'none';
                    penanggungJawab.style.display = 'none';
                    penanggungJawab.removeAttribute('required');
                    penanggungJawab.value = '';
                    dikirim.disabled = false;
                } else if (this.value === '') {
                    ekspedisiSelectize.removeAttribute('required');
                    ekspedisiSelectize.classList.remove('selectize-js');
                    pengirimSelect.value = ''; // Mengatur ulang nilai menjadi kosong
                    ekspedisiSelectize.value = ''; // Mengatur ulang nilai menjadi kosong
                    dikirimOleh.value = '';
                    penanggungJawab.value = '';
                    labelDriver.style.display = 'none'; // Menyembunyikan form input
                    pengirimSelect.style.display = 'none'; // Menyembunyikan form input
                    ekspedisiSelect.style.display = 'none'; // Menyembunyikan form input
                    labelDikirimOleh.style.display = 'none' // Menyembunyikan form input
                    labelPj.style.display = 'none' // Menyembunyikan form input
                    file1.style.display = 'none'; // Menyembunyikan form input
                    file2.style.display = 'none'; // Menyembunyikan form input
                    file3.style.display = 'none'; // Menyembunyikan form input
                    pengirimSelect.style.display = 'none'; // Menyembunyikan form input
                    dikirimOleh.style.display = 'none';
                    penanggungJawab.style.display = 'none';
                    dikirim.disabled = true;
                    document.getElementById('form-kirim').reset();
                }
                dikirim.addEventListener('shown.bs.modal', function() {
                    dikirim.disabled = true;
                });
                // Refresh halaman modal
                if (isModalShown) {
                    $('#Dikirim').modal('hide'); // Menyembunyikan modal
                    location.reload(); // Melakukan refresh halaman
                    $('#Dikirim').modal('show')
                }

                // Mendapatkan tombol "Cancel" berdasarkan ID
                const cancelButton = document.getElementById('cancelDikirim');

                // Fungsi untuk mengatur ulang input teks dan tombol
                // Event listener saat tombol "Cancel" ditekan
                cancelButton.addEventListener('click', function() {
                    dikirimOleh.value = '';
                    penanggungJawab.value = '';
                    jenisPengirimanSelect.value = '';
                    pengirimSelect.value = ''; // Mengatur ulang nilai menjadi kosong
                    ekspedisiSelect.value = ''; // Mengatur ulang nilai menjadi kosong
                    ekspedisiSelect.style.display = 'none'; // Menyembunyikan form input
                    labelDriver.style.display = 'none'; // Menyembunyikan form input
                    pengirimSelect.style.display = 'none'; // Menyembunyikan form input
                    labelDikirimOleh.style.display = 'none';
                    dikirimOleh.style.display = 'none';
                    pjLabel.style.display = 'none';
                    penanggungJawab.style.display = 'none';
                    dikirim.disabled = true;
                });
            });
          
        </script>
        <!-- End JS Dikirim -->
        <style>
            .preview-image {
                max-width: 100%;
                height: auto;
            }
        </style>
    </div>
    <!-- End Modal Dikirim -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <?php include "page/script.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
</body>

</html>

<!-- Modal Add SPK-->
<div class="modal fade" id="addSpk" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header text-center text-dark"><b>Tambah SPK Baru</b></div>
                    <div class="card-body mt-3">
                        <div class="table-responsive">
                            <?php
                            $no = 1;
                            $sql = "SELECT * FROM spk_reg WHERE id_inv = '$id_inv'";
                            $query = mysqli_query($connect, $sql);
                            $totalData = mysqli_num_rows($query);
                            ?>
                            <form action="proses/proses-invoice-nonppn.php" method="POST">
                                <table class="table table-bordered table-striped" id="table2">
                                    <thead>
                                        <tr class="text-white" style="background-color: navy;">
                                            <th class="text-center p-3" style="width: 20px">Pilih</th>
                                            <th class="text-center p-3" style="width: 30px">No</th>
                                            <th class="text-center p-3" style="width: 150px">No. SPK</th>
                                            <th class="text-center p-3" style="width: 150px">Tgl. SPK</th>
                                            <th class="text-center p-3" style="width: 150px">No. PO</th>
                                            <th class="text-center p-3" style="width: 200px">Nama Customer</th>
                                            <th class="text-center p-3" style="width: 150px">Note</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include "koneksi.php";
                                        $no = 1;
                                        $sql_inv = "SELECT sr.*, cs.nama_cs, cs.alamat
                                                                                FROM spk_reg AS sr
                                                                                JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                                                                WHERE status_spk = 'Siap Kirim' AND id_cs = '$id_cs'";
                                        $query_inv = mysqli_query($connect, $sql_inv);
                                        while ($data_inv = mysqli_fetch_array($query_inv)) {
                                        ?>
                                            <tr>
                                                <input type="hidden" name="id_inv" value="<?php echo $id_inv ?>">
                                                <td class="text-center"><input type="checkbox" name="id_spk[]" value="<?php echo $data_inv['id_spk_reg'] ?>"></td>
                                                <td class="text-center"><?php echo $no; ?></td>
                                                <td><?php echo $data_inv['no_spk'] ?></td>
                                                <td><?php echo $data_inv['tgl_spk'] ?></td>
                                                <td><?php echo $data_inv['no_po'] ?></td>
                                                <td><?php echo $data_inv['nama_cs'] ?></td>
                                                <td><?php echo $data_inv['note'] ?></td>
                                            </tr>
                                            <?php $no++ ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Close</button>
                                    <button type="submit" class="btn btn-primary" name="add-spk" id="add" disabled><i class="bi bi-plus-circle"></i> Add SPK</button>
                                </div>
                            </form>
                            <script>
                                // Mendapatkan checkbox SPK
                                const spkCheckboxes = document.querySelectorAll('input[name="id_spk[]"]');

                                // Mendapatkan tombol
                                const addButton = document.getElementById("add");


                                // Mendapatkan jumlah total checkbox yang dipilih
                                function getSelectedCheckboxCount() {
                                    let count = 0;
                                    spkCheckboxes.forEach(function(checkbox) {
                                        if (checkbox.checked) {
                                            count++;
                                        }
                                    });
                                    return count;
                                }

                                // Fungsi untuk mengaktifkan/menonaktifkan tombol berdasarkan jumlah checkbox yang dipilih
                                function toggleButton() {
                                    if (getSelectedCheckboxCount() > 0) {
                                        addButton.disabled = false;
                                    } else {
                                        addButton.disabled = true;
                                    }
                                }

                                // Event listener untuk setiap checkbox
                                spkCheckboxes.forEach(function(checkbox) {
                                    checkbox.addEventListener('change', toggleButton);
                                });

                                // Event listener untuk setiap checkbox
                                spkCheckboxes.forEach(function(checkbox) {
                                    checkbox.addEventListener('change', function() {
                                        // console.log("Total Data: " + <?php echo $totalData; ?>);
                                        // console.log("Total Checkbox: " + getSelectedCheckboxCount());

                                        const totalData = <?php echo $totalData; ?>;
                                        const maxAllowed = 10;

                                        if (totalData + getSelectedCheckboxCount() > maxAllowed) {
                                            const message = "Data Anda saat ini: " + totalData + " Anda hanya bisa menambahkan " + (maxAllowed - totalData) + " data.";
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Data melebihi batasan maksimum',
                                                text: message,
                                                didOpen: () => {
                                                    // Mengatur ulang semua checkbox menjadi tidak dipilih
                                                    spkCheckboxes.forEach(function(checkbox) {
                                                        checkbox.checked = false;
                                                    });
                                                }
                                            });
                                        }
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Add SPK -->

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

<!-- date picker with flatpick -->
<script type="text/javascript">
    flatpickr("#date", {
        dateFormat: "d/m/Y",
    });

    flatpickr("#tempo", {
        dateFormat: "d/m/Y",
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
            document.getElementById("simpan-data").click();
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


<!-- Edit Harga -->
<script>
    $('#edit-diskon').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var idTrx = button.data('id');
        var namaProduk = button.data('nama');
        var harga = button.data('hargadisc');
        var diskon = button.data('diskon');
        var qty = button.data('qty');

        $('#id_trxdisc').val(idTrx);
        $('#nama_produk_disc').val(namaProduk);
        $('#harga_produk_disc').val(harga);
        $('#discc').val(diskon);
        $('#qtydisc').val(qty);
    });

    $('#edit').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var idTrx = button.data('id');
        var namaProduk = button.data('nama');
        var harga = button.data('harga');
        var qty = button.data('qty');

        $('#id_trx').val(idTrx);
        $('#nama_produk').val(namaProduk);
        $('#harga_produk').val(harga);
        $('#qty').val(qty);
    });
</script>nis

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

<script>
    $(document).ready(function() {
        // Fungsi untuk menangani input pada Diskon SP
        $('.disc_cb').on('input', function() {
            let value = $(this).val();

            // Menghapus karakter yang tidak berupa angka atau titik (mengizinkan satu titik sebagai desimal)
            value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\..*/g, '$1'); // Mengizinkan hanya satu titik

            // Membatasi satu angka di belakang titik desimal
            const parts = value.split('.');
            if (parts.length > 1) {
                // Jika ada bagian desimal, ambil hanya satu angka setelah titik
                value = parts[0] + '.' + parts[1].charAt(0);
            }

            // Jika lebih dari 100, set nilai menjadi 100
            if (parseFloat(value) > 100) {
                value = '100';
            }

            // Set nilai kembali ke input
            $(this).val(value);
        });

        // Fungsi untuk menangani input pada Cashback Total Invoice
        $('.disc ').on('input', function() {
            let value = $(this).val();

            // Menghapus karakter yang tidak berupa angka atau titik (mengizinkan satu titik sebagai desimal)
            value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\..*/g, '$1'); // Mengizinkan hanya satu titik

            // Membatasi satu angka di belakang titik desimal
            const parts = value.split('.');
            if (parts.length > 1) {
                // Jika ada bagian desimal, ambil hanya satu angka setelah titik
                value = parts[0] + '.' + parts[1].charAt(0);
            }

            // Jika lebih dari 100, set nilai menjadi 100
            if (parseFloat(value) > 100) {
                value = '100';
            }

            // Set nilai kembali ke input
            $(this).val(value);
        });
    });
</script>
