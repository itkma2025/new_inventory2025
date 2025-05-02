<?php
require_once "../akses.php";
$page = 'list-inv';
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
    <link rel="stylesheet" href="../assets/css/camera.css">
    <?php include "page/head.php" ?>

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
        
        #Diterima{
            cursor: pointer;
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
        <section>
            <?php
                require_once "../function/function-enkripsi.php";
                $key = "Driver2024?";
                $id_inv = $_GET['id'];
                $id_inv_decrypt = decrypt($id_inv, $key);
                require_once "query/detail-inv-ecat.php";
            ?>
            <div class="card shadow p-2">
                <div class="card-header text-center">
                    <h5>
                        <strong>Detail Invoice Ecat</strong>
                    </h5>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div class="card-body p-3 border">
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. SPK</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php  
                                        $no = 1;
                                        while($detail_spk_trim = mysqli_fetch_array($spk_trim)){
                                            $no_spk = $detail_spk_trim['no_spk_ecat'];
                                            $no_paket = $detail_spk_trim['no_paket'];
                                            $tgl_pesanan = date("d/m/Y", strtotime($detail_spk_trim['tgl_pesanan_ecat']));
                                            $no_inv_ecat = $detail_spk_trim['no_inv_ecat'];
                                            $tgl_inv_ecat = date("d/m/Y", strtotime($detail_spk_trim['tgl_inv_ecat']));
                                            $nama_paket =  $detail_spk_trim['nama_paket'];
                                            $satker =  $detail_spk_trim['satker'];
                                            $alamat =  $detail_spk_trim['alamat'];
                                            $kota = $detail_spk_trim['kota'];
                                            $provinsi = $detail_spk_trim['provinsi'];
                                            $notes = $detail_spk_trim['notes'];
                                    ?>
                                         <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td style="width: 10px !important; padding: 0; margin:0;"><?php echo $no; ?></td>
                                                    <td style="width: 10px !important; padding: 0; margin:0;">.</td>
                                                    <td style="padding: 0; margin:0;">
                                                        <span class="text-wrap">(<?php echo $no_spk ?> - <?php echo $tgl_pesanan ?>)</span>
                                                    </td>
                                                </tr>
                                            </table>
                                         </div>
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
                                    <?php echo $no_inv_ecat ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Tgl. Invoice</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $tgl_inv_ecat ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">ID Paket</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $no_paket ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Nama Paket</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $nama_paket ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card-body p-3 border" style="min-height: 234px;">
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Satker</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $satker ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Alamat</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $alamat ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Kota / Kab</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $kota ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Provinsi</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $provinsi ?>
                                </div>
                            </div>
                            <?php
                                if ($notes != '') {
                                        echo '
                                        <div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Note Invoice</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $notes . '
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
                            <a href="menunggu-verif-invoice-ecat.php" class="btn btn-warning btn-detail mb-2">
                                <i class="bi bi-arrow-left"></i>
                                Halaman Sebelumnya
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
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
                            </thead>  
                            <tbody>
                                <?php  
                                    $no = 1;
                                    while($data_produk = mysqli_fetch_array($produk)){
                                        $id_produk = $data_produk['id_produk'];
                                        $id_produk_substr = substr($id_produk, 0, 2);
                                ?>
                                <tr>
                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data_produk['no_spk_ecat']; ?></td>
                                    <td class="text-nowrap"><?php echo $data_produk['nama_produk']; ?></td>
                                    <td class="text-center text-nowrap"><?php echo ($id_produk_substr == 'BR') ? $data_produk['satuan'] : "Set"; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data_produk['nama_merk']; ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_produk['harga']) ?></td>
                                    <td class="text-end"></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_produk['qty']) ?></td>
                                    <td class="text-end text-nowrap">
                                        <?php echo number_format($data_produk['total_harga']) ?>
                                    </td>
                                </tr>
                                <?php $no++ ?>
                                <?php } ?>
                            </tbody>
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



