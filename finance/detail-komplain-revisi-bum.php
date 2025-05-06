<?php
    $page  = 'list-komplain';
    require_once "../akses.php";
    require_once '../function/class-komplain.php';
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
    <link rel="stylesheet" href="../assets/css/wrap-text.css">
    <link rel="stylesheet" href="../assets/css/button-file-upload.css">
    <link href="../assets/vendor/lightbox/dist/css/lightgallery.css" rel="stylesheet" />

    <?php include "page/head.php"; ?>
    <?php include "page/style-button-filterdate.php"; ?>

    <style>
        .label-mobile {
            display: none;
        }

        .disable-click {
            pointer-events: none;
        }

        .disable-scroll {
            overflow: hidden;
        }

        @media (max-width: 767px) {

            body {
                font-size: 14px;
            }

            .mobile {
                display: none;
            }

            .mobile-text {
                text-align: left !important;
            }

            /* Tambahkan aturan CSS khusus untuk tampilan mobile di bawah 767px */
            .col-12.col-md-2 {
                /* Contoh: Mengatur tinggi elemen select pada tampilan mobile */
                height: 50px;
            }

            .card-mobile {
                display: none;
            }

            .label-mobile {
                display: block;
            }

        }

        .btn.active {
            background-color: black;
            color: white;
            border-color: 1px solid white;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

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

<body id="scroll">
    <!-- nav header -->
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include "page/sidebar.php"; ?>
    <!-- end sidebar -->


    <main id="main" class="main">
        <!-- Loading -->
        <!-- <div class="loader loader">
            <div class="loading">
                <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div> -->
        <!-- ENd Loading -->
        <section>
            <?php  
                include "koneksi.php";
                $id_role = $_SESSION['tiket_role'];
                $sql_role = "SELECT * FROM user_role WHERE id_user_role='$id_role'";
                $query_role = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                $data_role = mysqli_fetch_array($query_role);
              
                $id = decrypt($_GET['id'], $key_spk);
                include "../query/detail-komplain-bum.php";
                $id_inv = $data_kondisi['id_inv'];
                $no_inv = $data_detail['no_inv'];
                $alamat = $data_detail['alamat'];
                include "../query/produk-komplain-tmp.php";
                $cek_status_cancel = $connect->query("SELECT id_inv_bum, status_transaksi, total_inv FROM inv_bum WHERE id_inv_bum = '$id_inv'");
                $data_status_cancel = mysqli_fetch_array($cek_status_cancel);
                $status_transaksi = $data_status_cancel['status_transaksi'];
                $rev_total_inv =  $data_status_cancel['total_inv'];

                $id_inv_substr = $id_inv;
                $inv_id = substr($id_inv_substr, 0, 3);
                $jenis_inv = "";
                if ($inv_id == "NON"){
                    $jenis_inv = "nonppn";
                } else if ($inv_id == "PPN"){
                    $jenis_inv = "ppn";
                } else if ($inv_id == "BUM"){
                    $jenis_inv = "bum";
                }

                // query untuk cek no invoice
                $cek_no_inv = mysqli_query($connect,"   SELECT 
                                                            bum.id_inv_bum AS id_inv,
                                                            max(rev.no_inv_revisi) AS no_inv_revisi
                                                        FROM inv_revisi AS rev
                                                        LEFT JOIN inv_komplain ik ON rev.id_inv = ik.id_inv
                                                        LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
                                                        WHERE '$id_inv' IN (bum.id_inv_bum) GROUP BY id_inv
                                            ");
                $total_row_rev = mysqli_num_rows($cek_no_inv);
                $data_inv_rev = mysqli_fetch_array($cek_no_inv);
                $no_inv_fix = '';
                if($total_row_rev == 0){
                    $no_inv_fix = $no_inv;
                } else {
                    $no_inv_fix = $data_inv_rev['no_inv_revisi'];
                }
            ?>
           
            <div class="card p-3">
                <div class="row mb-2">
                    <!-- Kolom No Komplain (di atas) -->
                    <div class="col-md-3">
                        <button class="btn btn-secondary">No Komplain : <?php echo $data_detail['no_komplain'] ?></button>
                    </div>
                    <!-- Kolom Open (di tengah) -->
                    <div class="col-md-6 text-center">
                        <p><b>Detail Invoice Revisi</b></p>
                    </div>
                    <!-- Kolom Details (paling bawah) -->
                    <div class="col-md-3 text-end">
                        <button class="btn btn-secondary">
                            <?php 
                                if($data_detail['status_komplain'] == 0){
                                    echo "Open";
                                } else {
                                    echo "Selesai";
                                }
                            ?>
                        </button>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="border p-3">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Tgl. Pesanan</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['tgl_pesanan'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">No. SPK</td>
                                        <td class="text-nowrap">
                                            : <?php 
                                                    $no = 1;
                                                    $total_rows = mysqli_num_rows($query_detail2); // Menghitung total baris data
                                                    while ($data_detail2 = mysqli_fetch_array($query_detail2)) {
                                                        $no_spk = $data_detail2['no_spk'];
                                                        $tgl_pesanan = $data_detail2['tgl_pesanan'];
                                                        $no_po = $data_detail2['no_po'];
                                                        
                                                        // Mengecek apakah ini adalah baris kedua atau lebih
                                                        if ($no > 1) {
                                                            echo "<br>"; // Menambahkan baris baru setelah baris pertama
                                                        }
                                                        
                                                        echo $no . ". (" . $tgl_pesanan . ")";
                                                        
                                                        // Menampilkan nomor PO jika tersedia
                                                        if (!empty($no_po)) {
                                                            echo " / (" . $no_po . ")";
                                                        }
                                                        
                                                        echo " / (" . $no_spk . ")";
                                                        
                                                        $no++;
                                                    }
                                                ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">No. Invoice</td>
                                        <td class="text-nowrap">: <?php echo $no_inv_fix ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Tgl.Invoice</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['tgl_inv'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Jenis Invoice</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['kategori_inv'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Order Via</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['order_by'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Sales</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['nama_sales'] ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border p-3">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <?php  
                                        $cek_inv_revisi = $connect->query("SELECT pelanggan_revisi, alamat_revisi FROM inv_revisi WHERE id_inv = '$id_inv' ORDER BY created_date DESC");
                                        $total_data_inv_rev = mysqli_num_rows($cek_inv_revisi);
                                        $data_cek_inv_rev = mysqli_fetch_array($cek_inv_revisi);
                                        
                                        $pelanggan = '';
                                        $alamat = '';
                                        if ($total_data_inv_rev > 0){
                                            $pelanggan = $data_cek_inv_rev['pelanggan_revisi'];
                                            $alamat = $data_cek_inv_rev['alamat_revisi'];
                                        } else {
                                            if($data_detail['alamat_inv'] == ''){
                                                $pelanggan = $data_detail['cs_inv'];
                                                $alamat = $data_detail['alamat'];
                                            } else {
                                                $pelanggan = $data_detail['cs_inv'];
                                                $alamat = $data_detail['alamat_inv'];  
                                            }
                                            
                                        }
                                    ?>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Pelanggan</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['nama_cs'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Pelanggan Invoice</td>
                                        <td class="text-nowrap">: <?php echo $pelanggan ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Alamat</td>
                                        <td class="wrap-text">: <?php echo $alamat ?></td>
                                    </tr>
                                    <?php 
                                        if($total_driver_rev != 0 && $data_driver_rev['jenis_pengiriman'] == 'Ekspedisi'){
                                            ?>
                                                <tr>
                                                    <td class="col-md-6 text-nowrap">Ongkos Kirim</td>
                                                    <td class="text-nowrap">: <?php 
                                                            if($data_driver_rev['free_ongkir'] == 1){
                                                                echo "0 (Free Ongkir)";
                                                            } else  {
                                                                if($data_driver_rev['jenis_ongkir'] == 1){
                                                                    echo number_format($data_driver_rev['ongkir']) . " (COD)";
                                                                } else {
                                                                    echo number_format($data_driver_rev['ongkir']);
                                                                }
                                                            }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php
                                        }
                                    ?>
                                    <?php  
                                        if($total_driver_rev != 0){
                                            ?>
                                                <tr>
                                                    <td class="col-md-6 text-nowrap">Jenis Pengiriman</td>
                                                    <td class="text-nowrap">
                                                        : <?php  
                                                                if($data_driver_rev['jenis_pengiriman'] == 'Driver'){
                                                                    ?>
                                                                        <?php echo $data_driver_rev['jenis_pengiriman']?>
                                                                        (<?php echo $data_driver_rev['nama_driver'] ?>)
                                                                    <?php
                                                                } else if($data_driver_rev['jenis_pengiriman'] == 'Ekspedisi'){
                                                                    ?>
                                                                        <?php echo $data_driver_rev['jenis_pengiriman']?>
                                                                        <?php
                                                                } else {
                                                                    ?>
                                                                        <?php echo $data_driver_rev['jenis_pengiriman']?>
                                                                        (<?php echo $data_driver_rev['diambil_oleh'] ?>)
                                                                    <?php
                                                                }
                                                            ?>

                                                    </td>
                                                </tr>
                                                <?php  
                                                    if(!empty($data_driver_rev['jenis_pengiriman'] && $data_driver_rev['jenis_penerima'])){
                                                        ?>
                                                            <tr>
                                                                <td class="col-md-6 text-nowrap">Diterima Oleh</td>
                                                                <td class="text-nowrap">
                                                                    : <?php 
                                                                            if($data_driver_rev['jenis_penerima'] == 'Customer'){
                                                                                ?>
                                                                                    <?php echo $data_driver_rev['jenis_penerima'] ?>
                                                                                    <?php
                                                                            } else {
                                                                                ?>
                                                                                    <?php echo $data_driver_rev['nama_ekspedisi'] ?>
                                                                                <?php
                                                                            }
                                                                    
                                                                        ?>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                    }          
                                                ?>
                                                <?php  
                                                    if(!empty($data_driver_rev['jenis_pengiriman'] && $data_driver_rev['jenis_penerima'])){
                                                        if($data_driver_rev['jenis_penerima'] == 'Customer'){
                                                        } else {
                                                            ?>
                                                                <tr>
                                                                    <td class="col-md-6 text-nowrap">No. Resi</td>
                                                                    <td class="text-nowrap">
                                                                        : <?php echo $data_driver_rev['no_resi'] ?>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                            
                                                        <?php
                                                    }          
                                                ?>
                                                <?php   
                                                    if(!empty($data_driver_rev['nama_penerima'])){
                                                        ?>
                                                            <tr>
                                                                <td class="col-md-6 text-nowrap">Nama Penerima (CS)</td>
                                                                <td class="text-nowrap">: <?php echo $data_driver_rev['nama_penerima'] ?></td>
                                                            </tr>
                                                        <?php
                                                    }
                                                ?>
                                                <?php  
                                                    if(!empty($data_driver_rev['dikirim_oleh']) && !empty($data_driver_rev['penanggung_jawab'])){
                                                        ?>
                                                            <tr>
                                                                <td class="col-md-6 text-nowrap">Dikirim Oleh</td>
                                                                <td class="text-nowrap">: <?php echo $data_driver_rev['dikirim_oleh'] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="col-md-6 text-nowrap">PJ. Paket Kirim</td>
                                                                <td class="text-nowrap">: <?php echo $data_driver_rev['penanggung_jawab'] ?>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                    }
                                                            
                                                ?>
                                            <?php 
                                        } 
                                    ?>
                                    <?php  
                                        if ($data_kondisi['catatan'] != ""){
                                            ?>
                                                <tr>
                                                    <td class="col-md-6 text-nowrap">Catatan</td>
                                                    <td class="text-nowrap">: <?php echo $data_kondisi['catatan'] ?></td>
                                                </tr>
                                            <?php 
                                        } 
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                /* CSS untuk layar kecil (misalnya, ukuran layar ponsel) */
                @media (max-width: 767px) {
                    .w-100 {
                        flex-wrap: wrap;
                        justify-content: center;
                    }

                    .p-2 {
                        flex: 1 1 calc(33.33% - 10px);
                        margin: 5px;
                        /* text-align: center; */
                    }

                    .text-end {
                        text-align: center;
                    }

                    .btn {
                        width: 100%;
                        white-space: nowrap;
                    }

                    .btn button {
                        display: block;
                        margin-top: 10px;
                    }

                }
            </style>
            <div class="card p-3"> 
                <div class="row mb-3">
                    <div class="col-md-7 mb-2">
                        <p class="bg-secondary text-center text-white p-2" style="border-radius: 5px;">
                            <?php echo $alasan_komplain = komplain::getKondisi($data_kondisi['kondisi_pesanan']); ?>
                        </p>
                    </div>
                    <div class="col-md-5 text-end">
                        <p class="btn btn-secondary">
                            <?php  
                                if($data_kondisi['kat_komplain'] == 0) {
                                    echo "Invoice";
                                } else {
                                    echo "Barang";
                                }
                            ?>
                        </p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <a href="invoice-komplain.php?date_range=year" class="btn btn-warning mb-3">
                                <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                            </a>
                            <?php  
                                $cek_bukti_terima = mysqli_query($connect, "SELECT id_komplain FROM inv_bukti_terima_revisi WHERE id_komplain = '$id'");
                                $total_data_bukti = mysqli_num_rows($cek_bukti_terima);
                                if($total_data_bukti != '0'){
                                    ?>
                                        <button class="btn btn-info mb-3" data-bs-toggle="modal" data-bs-target="#bukti">
                                            <i class="bi bi-image"></i> Bukti Terima Revisi
                                        </button>
                                    <?php
                                }
                            ?>
                        </div>
                        <div class="col-md-4">
                            <div class="text-end">
                                <?php  
                                    if($jenis_inv == 'ppn'){
                                        ?>
                                            <button class="btn border-dark">
                                                <b>Total Invoice Revisi</b><br>
                                                Rp. <?php echo number_format($rev_total_inv); ?>
                                            </button>
                                            <?php
                                    } else {
                                        ?>
                                            <button class="btn border-dark">
                                                <?php  
                                                            $total_harga_revisi = 0;
                                                            while($data_total = mysqli_fetch_array($query_produk_total)){
                                                                $total_harga =  $data_total['harga'] * $data_total['qty'];
                                                                $discount = $data_total['disc'] / 100; // 50% diskon
                                                                $harga_final = $total_harga * (1 - $discount); // Harga akhir setelah diskon   
                                                                $total_harga_revisi += $harga_final;
                                                                } 
                                                                $grand_total_revisi = $total_harga_revisi + $data_detail['ongkir'];
                                                            ?>
                                                <b>Total Invoice Revisi</b><br>
                                                Rp. <?php echo number_format($grand_total_revisi); ?>
                                            </button>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                        <!-- Default Tabs -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="detail-komplain-bum.php?id=<?php echo encrypt($id, $key_spk) ?>"
                                    class="nav-link">Original</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#" class="nav-link active">Revisi</a>
                            </li>
                        </ul>
                        <div class="tab-content pt-2" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered table-striped" id="table2">
                                        <thead>
                                            <tr class="text-white" style="background-color: navy;">
                                                <th class="text-center text-nowrap p-3">No</th>
                                                <th class="text-center text-nowrap p-3">Nama Produk</th>
                                                <th class="text-center text-nowrap p-3">Merk</th>
                                                <th class="text-center text-nowrap p-3">Qty Order</th>
                                                <th class="text-center text-nowrap p-3">Satuan</th>
                                                <th class="text-center text-nowrap p-3">Harga</th>
                                                <th class="text-center text-nowrap p-3">Diskon</th>
                                                <th class="text-center text-nowrap p-3">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $no = 1;  
                                                include "function/class-spk.php";
                                                $cek_query_produk = "";
                                                if($status_transaksi != 'Cancel Order'){
                                                    $cek_query_produk = $query_produk;
                                                } else {
                                                    $cek_query_produk = $query_produk_cancel;
                                                }
                                                while($data_tmp = mysqli_fetch_array($cek_query_produk)){
                                                    $satuan = detailSpkFnc::getSatuan($data_tmp['id_produk']);
                                                    $total_harga =  $data_tmp['harga'] * $data_tmp['qty'];
                                                    $discount = $data_tmp['disc'] / 100; // 50% diskon
                                                    $harga_final = $total_harga * (1 - $discount); // Harga akhir setelah diskon   
                                                    $id_tmp = !empty($data_tmp['id_tmp']) ? $data_tmp['id_tmp'] : $data_tmp['id_trx'];
                                            ?>
                                            <tr>
                                                <td class="text-center text-nowrap"><?php echo $no ?></td>
                                                <td class="text-nowrap"><?php echo $data_tmp['nama_produk'] ?></td>
                                                <td class="text-center text-nowrap"><?php echo $data_tmp['merk'] ?></td>
                                                <td class="text-center text-nowrap">
                                                    <?php echo number_format($data_tmp['qty']) ?></td>
                                                <td class="text-center text-nowrap"><?php echo $satuan ?></td>
                                                <td class="text-end text-nowrap">
                                                    <?php echo number_format($data_tmp['harga']) ?></td>
                                                <td class="text-end text-nowrap"><?php echo $data_tmp['disc'] ?></td>
                                                <td class="text-end text-nowrap"><?php echo number_format($harga_final) ?>
                                                </td>
                                            </tr>
                                            <?php $no++ ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </section>
        <!-- Modal Bukti Terima -->
        <?php
            $sql_bukti = "  SELECT 
                                ibt.id_bukti_terima, ibt.id_komplain, ibt.bukti_satu, ibt.created_date, ip.id_komplain, ip.nama_penerima, ip.tgl_terima, ip.created_date, sk.jenis_penerima, sk.dikirim_ekspedisi, sk.no_resi, sk.tgl_kirim, ex.nama_ekspedisi
                            FROM inv_bukti_terima_revisi AS ibt
                            LEFT JOIN inv_penerima_revisi ip ON (ibt.id_komplain = ip.id_komplain)
                            LEFT JOIN revisi_status_kirim sk ON (ibt.id_komplain = sk.id_komplain)
                            LEFT JOIN ekspedisi ex ON (ex.id_ekspedisi = sk.dikirim_ekspedisi) 
                            WHERE ibt.id_komplain = '$id' ORDER BY ip.created_date  DESC LIMIT 1";
            $query_bukti = mysqli_query($connect, $sql_bukti);
            if (mysqli_num_rows($query_bukti) > 0) {
                $data_bukti = mysqli_fetch_array($query_bukti);
                $id_bukti_terima = $data_bukti['id_bukti_terima'];
                $gambar1 = $data_bukti['bukti_satu'];
                $gambar_bukti1 = "../gambar-revisi/bukti1/$gambar1";
                $jenis_penerima = $data_bukti['jenis_penerima'];
                $no_resi = $data_bukti['no_resi'];
                $tgl_terima = $data_bukti['tgl_terima'];
            }
        ?>
        <div class="modal fade" id="bukti" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="card mb-3">
                                <div class="row g-0">
                                    <div class="col-md-6">
                                        <img id="buktiTerimaImg" data-src="<?php echo $gambar_bukti1 ?>"
                                            class="img-fluid" alt="...">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card-body p-5">
                                            <div class="mb-3">
                                                <?php  
                                                    if($data_bukti['nama_penerima'] != ''){
                                                        ?>
                                                            <label class="fw-bold">Nama Penerima:</label>
                                                            <P><?php echo $data_bukti['nama_penerima']; ?></P>
                                                        <?php 
                                                        if ($jenis_penerima == 'Ekspedisi') {
                                                            ?>
                                                                <label class="fw-bold">No Resi:</label>
                                                                <P><?php echo $no_resi; ?></P>
                                                            <?php
                                                        }
                                                        ?>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php  
                                                        if($tgl_terima){
                                                            ?>
                                                                <label class="fw-bold">Tanggal Terima:</label>
                                                                <p><?php echo $data_bukti['tgl_terima']?></p>
                                                            <?php
                                                        } else {
                                                            ?>
                                                                <label class="fw-bold">Tanggal Kirim:</label>
                                                                <p><?php echo $data_bukti['tgl_kirim']?></p>
                                                            <?php
                                                        }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Bukti Terima -->
    </main>
    <!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
    <!-- Selectize JS -->
    <?php include "page/script.php" ?>
    <script>
        $(document).ready(function() {
            // Function untuk menampilkan modal bukti terima
            $('#bukti').on('show.bs.modal', function(event) {
                var imgSrc = '<?php echo $gambar_bukti1 ?>';
                $('#buktiTerimaImg').attr('src', imgSrc).attr('data-src', imgSrc);
            });

            // Event handler saat gambar bukti terima diklik
            $(document).on('click', '#buktiTerimaImg', function() {
                // Sembunyikan modal bukti terima
                $('#bukti').modal('hide');

                // Inisialisasi lightGallery
                $(this).lightGallery({
                    dynamic: true,
                    dynamicEl: [{
                        src: $(this).data('src') // URL gambar yang akan ditampilkan
                    }]
                });

                // Ubah atribut dan gaya CSS dari elemen body
                $('body').attr('scroll', 'no').addClass('disable-scroll');
            });

            // Event handler saat lightGallery ditutup
            $(document).on('onCloseAfter.lg', '#buktiTerimaImg', function() {
                // Tampilkan kembali modal bukti terima
                $('#bukti').modal('show');

                // Hapus atribut dan gaya CSS dari elemen body
                $('body').removeAttr('scroll').removeClass('disable-scroll');
            });
        });
    </script>
    <script src="../assets/vendor/lightbox/dist/js/picturefill.min.js"></script>
    <script src="../assets/vendor/lightbox/dist/js/lightgallery-all.min.js"></script>
    <script src="../assets/vendor/lightbox/lib/jquery.mousewheel.min.js"></script>
</body>

</html>