<?php
    require_once "akses.php";
    $page  = 'transaksi';
    $page2 = 'spk';
    require_once "function/class-spk.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <!-- FancyBox CSS -->
    <link rel="stylesheet" href="assets/vendor/fancybox/fancybox.css">
    <?php include "page/head.php";?>
    <link rel="stylesheet" href="assets/css/style-detail-produk-diterima.css">
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
        <?php
            if (isset($_SESSION['info'])) {
                echo '<div class="info-data" data-infodata="' . $_SESSION['info'] . '"></div>';
                unset($_SESSION['info']);
            }
        ?>
        <!-- END SWEET ALERT -->
        <section>
            <!-- Detail -->
            <?php include "page/detail-produk-diterima.php";?>
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
                                $tgl_inv_convert = $data_trx['tgl_inv'];


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
            <!-- Query untuk menangani jika terjadi nya selisih total invoice pada sisi finance -->
            <?php
                if ($jenis_inv == 'ppn'){
                    require_once __DIR__ . "/query/cek-total-inv-ppn.php";
                } else if ($jenis_inv == 'nonppn'){
                    require_once __DIR__ . "/query/cek-total-inv-nonppn.php";
                } if ($jenis_inv == 'bum') {
                    require_once __DIR__ . "/query/cek-total-inv-bum.php";
                } else {
                    header("Location: 404.php");
                }
            ?>

            <!-- Modal Bukti Kirim -->
            <?php require_once 'modal/bukti-kirim.php' ?>
            <!-- End Modal Bukti Kirim -->
            <!-- Modal edit data produk -->
            <?php require_once 'modal/transaksi-selesai.php' ?>
            <!-- End Modal edit data produk -->
        </section>
        <!-- Modal Bukti Kirim -->
        <?php require_once 'modal/invoice-diterima.php' ?>
        <!-- End Modal Bukti Kirim -->
    </main>
    <!-- Modal edit ongkir dan resi -->
    <?php include "modal/edit-ongkir-resi-diterima.php"; ?>
    <!-- End modal edit ongkir dan resi -->
    <!-- Footer -->
    <?php include "page/footer.php"?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <?php include "page/script.php"?>
    <!-- Fancybox -->
    <script src="assets/vendor/fancybox/fancybox.umd.js"></script>
</body>
</html>

<script>
    $(document).ready(function(){
        $("#cetakInv").addClass('d-none');
        $("#divInv").addClass('d-none');
    });
</script>