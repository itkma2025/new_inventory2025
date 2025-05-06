<?php
$page = 'spk';
require_once "../akses.php";
require_once "../function/class-spk.php";
require_once "../function/function-enkripsi.php";
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
            text-overflow: ellipsis; /* Menggantikan teks yang terpotong dengan elipsis (...) jika terlalu panjang */
        }

        .col-12 {
            padding: 0 0 0 0px !important;
        }

        @media only screen and (max-width: 500px) {
            body {
                font-size: 14px;
            }

            .btn-mobile{
                width: 100%;
            }

            .mobile {
                display: none;
            }

            .mobile-text {
                text-align: left !important;
            }

            #dt-search-1 {
                width: 100%;
            }

            .col-md-auto {
                padding: 0;
            }

            .row {
                --bs-gutter-x: 0.rem;
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
            <?php
                $id_inv = decrypt($_GET['id'], $key_global);
                $jenis_inv = $_GET['jenis'];

                // Generate a secure random token
                $nonce = bin2hex(random_bytes(16));
                $_SESSION['nonce_token'] = $nonce; 

                // Query untuk kondisi role akses
                $id_role = $_SESSION['tiket_role'];
                $sql_role = "SELECT * FROM user_role WHERE id_user_role='$id_role'";
                $query_role = mysqli_query($connect, $sql_role) or die(mysqli_error($connect));
                $data_role = mysqli_fetch_array($query_role);

                // Kondisi untuk menampilkan data berdasarkan jenis invoice
                if($jenis_inv == 'nonppn'){
                    $label_jenis = 'NON PPN';
                    require_once '../query/data-inv-nonppn.php';
                    require_once '../query/data-spk-proforma.php';
                    require_once '../query/jenis-cb-proforma.php';
                    $sql_total_inv = mysqli_query($connect, "SELECT total_inv FROM inv_nonppn WHERE id_inv_nonppn = '$id_inv'");
                    if($status_transaksi_inv != 'Belum Dikirim'){
                        ?>
                            <script>
                                window.location.href = 'invoice-reguler.php?sort=baru';
                            </script>
                        <?php
                    }
                } else if ($jenis_inv == 'ppn'){
                    $label_jenis = 'PPN';
                    require_once '../query/data-inv-ppn.php';
                    require_once '../query/data-spk-proforma.php';
                    require_once '../query/jenis-cb-proforma.php';
                    $sql_total_inv = mysqli_query($connect, "SELECT total_inv FROM inv_ppn WHERE id_inv_ppn = '$id_inv'");
                    if($status_transaksi_inv != 'Belum Dikirim'){
                        ?>
                            <script>
                                window.location.href = 'invoice-reguler.php?sort=baru';
                            </script>
                        <?php
                    }
                } else if ($jenis_inv == 'bum') {
                    $label_jenis = 'BUM';
                    require_once '../query/data-inv-bum.php';
                    require_once '../query/data-spk-proforma.php';
                    require_once '../query/jenis-cb-proforma.php';
                    $sql_total_inv = mysqli_query($connect, "SELECT total_inv FROM inv_bum WHERE id_inv_bum = '$id_inv'");
                    if($status_transaksi_inv != 'Belum Dikirim'){
                        ?>
                            <script>
                                window.location.href = 'invoice-reguler.php?sort=baru';
                            </script>
                        <?php
                    }
                } else {
                    header("Location:404.php");
                }
            ?>
            <div class="card shadow p-2">
                <div class="card-header text-center">
                    <h5><strong>DETAIL INVOICE <?php echo $label_jenis ?></strong></h5>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div class="card-body p-3 border">
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Tgl. Pesanan</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['tgl_pesanan'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. SPK</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7 overflow-auto">
                                    <?php
                                    $no = 1;
                                    // Inisialisasi array kosong untuk menyimpan id_spk_reg
                                    $id_spk_reg_array = [];
                                    while ($data_spk = mysqli_fetch_array($query_data_spk)) {
                                        $id_spk_reg_array[] = $data_spk['id_spk_reg'];
                                    ?>
                                        <p><?php echo $no; ?>. (<?php echo $data_spk['tgl_pesanan'] ?>) / <?php if (!empty($data_spk['no_po'])) {
                                                                                                            echo "(" . $data_spk['no_po'] . ")/";
                                                                                                        } ?> (<?php echo $data_spk['no_spk'] ?>)</p>
                                        <?php $no++; ?>
                                    <?php } ?>
                                    <?php  
                                        // Gabungkan data menjadi satu string dengan tanda kutip
                                        $gabungan_spk = "'" . implode("','", $id_spk_reg_array) . "'";
                                        
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. Invoice</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['no_inv'] ?>
                                </div>
                            </div>
                            <?php
                                if ($data_inv['no_po'] != '') {
                                    echo '
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">No. PO</p>
                                            <p style="float: right;">:</p>
                                        </div>
                                        <div class="col-7">
                                            ' . $data_inv['no_po'] . '
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
                                    <?php echo $data_inv['tgl_inv'] ?>
                                </div>
                            </div>
                            <?php
                                if ($data_inv['tgl_tempo'] != '') {
                                        echo '
                                        <div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">Tgl. Tempo</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data_inv['tgl_tempo'] . '
                                            </div>
                                        </div>';
                                    }
                            ?>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Kategori Invoice</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['kategori_inv'] ?>
                                </div>
                            </div>
                            <?php
                            if ($data_inv['kategori_inv'] == 'Spesial Diskon') {
                                ?>
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">Sp. Diskon</p>
                                            <p style="float: right;">:</p>
                                        </div>
                                        <div class="col-7">
                                            <?php echo $data_inv['sp_disc'] ?> %
                                        </div>
                                    </div>
                                <?php
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
                                    <?php echo $data_inv['order_by'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Sales</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['nama_sales'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Pelanggan</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['nama_cs'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Pelanggan Inv</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_inv['cs_inv'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Alamat</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php 
                                        if($data_inv['alamat_inv'] == ''){
                                            echo $data_inv['alamat'];
                                        } else {
                                            echo $data_inv['alamat_inv']; 
                                        }
                                    ?>
                                </div>
                            </div>
                            <?php
                                $note_spk = $data_inv['note_spk'];

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
                                $note_inv = $data_inv['note_inv'];

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
                            if ($data_inv['ongkir'] != 0) {
                                echo '<div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">Ongkir</p>
                                            <p style="float: right;">:</p>
                                        </div>
                                        <div class="col-7">
                                            ' . number_format($data_inv['ongkir']) . '
                                        </div>
                                    </div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-10">
                        <div class="text-start">
                            <a href="invoice-reguler.php?sort=baru" class="btn btn-warning btn-detail mb-2 btn-mobile">
                                <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-end">
                            <?php  
                                $data_total_inv = mysqli_fetch_array($sql_total_inv);
                                $tampil_total_inv = $data_total_inv['total_inv'];
                            ?>
                            <button type="button" class="btn btn-outline-dark btn-mobile">
                                Total Invoice<br>
                                Rp. <span id="total_inv"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mb-3 mt-3">
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
                <div class="table-responsive">
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
                                require_once '../query/data-produk-proforma.php';
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
                                                $kat_inv = $data_inv['kategori_inv'];
                                                echo "<td class='text-end'>" . $disc . "</td>";
                                            }
                                        }
                                        if(in_array('Per Barang', $cashback_values)){
                                            // untuk proses edit produk
                                            $cb_per_barang = 'Per Barang';
                                            ?>  
                                                <td class='text-end'><?php echo $disc_cb ?></td>
                                            <?php
                                        }
                                    ?>
                                    
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['total_harga'],0,'.','.') ?></td>
                                </tr>
                            <?php $no++; ?>
                            <?php } ?>
                        </tbody>
                    </table>
                    <!-- Kode Untuk Update Total Inv -->
                    <?php  
                        if ($_GET['jenis'] == 'nonppn') {
                            $hasil_sp_disc = $sp_disc / 100;
                            $total_sp_disc = $sub_total * $hasil_sp_disc;
                            $grand_total = $sub_total - $total_sp_disc;
                            $grand_total_nonppn = $grand_total;
                            $update_total_inv = mysqli_query($connect, "UPDATE inv_nonppn SET total_inv = '$grand_total_nonppn' WHERE id_inv_nonppn = '$id_inv'");
                            $grand_total = number_format($grand_total_nonppn,0,'.','.');
                        } else if ($_GET['jenis'] == 'ppn') {
                            $hasil_sp_disc = $sp_disc / 100;
                            $total_sp_disc = $sub_total * $hasil_sp_disc;
                            $grand_total = $sub_total - $total_sp_disc;
                            $grand_total_ppn = $grand_total;
                            $update_total_inv = mysqli_query($connect, "UPDATE inv_ppn SET total_inv = '$grand_total_ppn' WHERE id_inv_ppn = '$id_inv'");
                            $grand_total = number_format($grand_total_ppn,0,'.','.');
                        } else if ($_GET['jenis'] == 'bum') {
                            $hasil_sp_disc = $sp_disc / 100;
                            $total_sp_disc = $sub_total * $hasil_sp_disc;
                            $grand_total = $sub_total - $total_sp_disc;
                            $grand_total_bum = $grand_total;
                            $update_total_inv = mysqli_query($connect, "UPDATE inv_bum SET total_inv = '$grand_total_bum' WHERE id_inv_bum = '$id_inv'");
                            $grand_total = number_format($grand_total_bum,0,'.','.');
                        } else {
                            ?>
                                <script type="text/javascript">
                                    window.location.href = "../404.php";
                                </script>
                            <?php
                        }
                    ?>
                </div>
                <div class="container">
                    <?php
                        if ($total_data_status_trx_1 == 0) {
                            ?>
                               
                            <?php
                        }
                    ?>
                </div>
                <form action="<?php echo $action_proforma ?>" method="POST">
                    <?php
                        $no = 1;
                        $total_cek_harga = mysqli_num_rows($query_cek_harga);
                        
                        if ($total_data_status_trx_1 == 0) {
                            ?>
                                <h5 class="text-center">Cek Harga Produk</h5>
                                <div class="row">
                                    <div class="col-sm-6 mb-1 text-center">
                                        <label class="fw-bold">Nama Produk</label>
                                    </div>
                                    <div class="col-sm-1 mb-1 text-center">
                                        <label class="fw-bold">Merk</label>
                                    </div>

                                    <div class="col-sm-2 mb-1 text-center">
                                        <label class="fw-bold">Harga</label>
                                    </div>

                                    <?php if ($total_cek_harga != 0): ?>
                                        <?php  
                                            if($kat_inv == 'Diskon'){
                                                ?>
                                                    <div class="col-sm-1 mb-1 text-center">
                                                        <label class="fw-bold">Diskon PR</label>
                                                    </div>
                                                <?php
                                            }
                                        ?>
                                        <?php if (in_array('Per Barang', $cashback_values)): ?>
                                            <div class="col-sm-1 mb-1 text-center">
                                                <label class="fw-bold">Diskon CB</label>
                                            </div>
                                        <?php endif; ?>
                                        <?php
                                            $col_size = (in_array('Per Barang', $cashback_values)) ? '1' : '2';

                                            // Tambahkan 1 ke ukuran kolom jika $kat_inv bukan 'Diskon'
                                            if ($kat_inv != 'Diskon') {
                                                $col_size += 1;
                                            }
                                        ?>
                                        <div class="col-sm-<?php echo $col_size; ?> mb-1 text-center">
                                            <label class="fw-bold">Qty</label>
                                        </div>

                                    <?php else: ?>
                                        <input type="hidden" class="form-control text-end bg-light disc" name="disc[]" value="<?php echo $data_cek_harga['disc'] ?>" readonly>
                                    <?php endif; ?>
                                </div>
                            <?php
                        }
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
                        <div class="row">
                            <div class="col-sm-6 mb-1">
                                <input type="hidden" name="id_trx[]" id="id_<?php echo encrypt($data_cek_harga['id_transaksi'], $key_global) ?>" value="<?php echo encrypt($data_cek_harga['id_transaksi'], $key_global) ?>" readonly>
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1"><?php echo $no++; ?></span>
                                    <input type="text" class="form-control mobile-text" name="nama_produk[]" value="<?php echo $namaProduk ?>" required>
                                </div>
                            </div>

                            <div class="col-sm-1 mb-1">
                                <input type="text" class="form-control bg-light text-center mobile-text" value="<?php echo $nama_merk ?>" readonly>
                            </div>

                            <div class="col-sm-2 mb-1">
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                    <input type="text" class="form-control text-end harga_produk mobile-text" name="harga_produk[]" value="<?php echo number_format($data_cek_harga['harga']) ?>" required>
                                </div>
                            </div>
                            <?php if ($total_cek_harga != 0): ?>
                                <?php  
                                    if($kat_inv == 'Diskon'){
                                        ?>
                                            <div class="col-sm-1 mb-1">
                                                <div class="input-group">
                                                    <input type="text" class="form-control text-end" name="disc[]" oninput="formatDiscount(this)" required>
                                                    <span class="input-group-text" id="basic-addon1">%</span>
                                                </div>
                                            </div>
                                        <?php
                                    }
                                ?>

                                <?php if (in_array('Per Barang', $cashback_values)): ?>
                                    <div class="col-sm-1 mb-1">
                                        <div class="input-group">
                                            <input type="text" class="form-control text-end" id="disc_cb" name="disc_cb[]" value="<?php echo isset($data_cek_harga['disc_cb']) ? $data_cek_harga['disc_cb'] : '0' ?>" oninput="formatDiscount(this)" required>
                                            <span class="input-group-text" id="basic-addon1">%</span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="col-sm-<?php echo $col_size; ?> mb-1">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light text-end mobile-text" name="qty[]" value="<?php echo number_format($data_cek_harga['qty']) ?>" readonly>
                                        <span class="input-group-text" id="basic-addon1"><?php echo $satuan_produk ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
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
            <?php 
                $perBarang = '';
                $gabungan_spk_encrypt = '';
                if (in_array('Per Barang', $cashback_values)){
                    $perBarang = 'True';  
                } else {
                    $gabungan_spk_encrypt = encrypt($gabungan_spk, $key_global);
                    $perBarang = 'False';  
                }
            ?>
       </section>
    </main><!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <?php include "page/script.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
</body>
</html>
<!-- Menampilkan total invoice -->
<script>
    let total_inv = "<?php echo $grand_total; ?>";
    $('#total_inv').text(total_inv);
</script>
<script src="function-js/format-number.js"></script>
<script src="function-js/format-diskon.js"></script>
<script src="function-js/detail-proforma-inv.js"></script>

<script>
    // Ajax Update Disc CB Jika Jenis CB terpilih bukan 
$(document).ready(function() {
    var perBarang = "<?php echo $perBarang ?>";

    if(perBarang === 'False'){
        var id_spk = "<?php echo $gabungan_spk_encrypt ?>";
        console.log(id_spk);
        $.ajax({
            type: 'POST',
            url: 'ajax/update-cb-per-produk.php', // Pastikan file PHP ini benar
            data: { 
                id_spk: id_spk
            },
            cache: false, // Ini mencegah browser menyimpan cache
            success: function(response) {
                // console.log("Raw Response:", response);
            },
            error: function(xhr, status, error) {
                // console.error("AJAX Error:", status, error);
            }
        });
    }
});
</script>