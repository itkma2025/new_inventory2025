<?php
$page  = 'transaksi';
$page2  = 'list-cmp';
require_once "akses.php";
require_once 'function/class-komplain.php';
require_once 'function/function-enkripsi.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link rel="stylesheet" href="assets/css/wrap-text.css">

    <?php include "page/head.php"; ?>
    <?php include "page/style-button-filterdate.php"; ?>

    <style>
        /* .table {
            border: none !important;
        } */
        @media (max-width: 767px) {

            /* Tambahkan aturan CSS khusus untuk tampilan mobile di bawah 767px */
            .col-12.col-md-2 {
                /* Contoh: Mengatur tinggi elemen select pada tampilan mobile */
                height: 50px;
            }
        }

        .btn.active {
            background-color: black;
            color: white;
            border-color: 1px solid white;
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
        <!-- Loading -->
        <!-- <div class="loader loader">
            <div class="loading">
                <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div> -->
        <!-- ENd Loading -->
        <section>
            <?php  
                $id = decrypt($_GET['id'], $key_spk);
                include "query/detail-komplain-nonppn.php";
                $id_inv = $data_kondisi['id_inv'];
            ?>
            <div class="card p-2">     
                <div class="row mb-2">
                    <!-- Kolom No Komplain (di atas) -->
                    <div class="col-md-3">
                        <button class="btn btn-secondary">No Komplain : <?php echo $data_detail['no_komplain'] ?></button>
                    </div>
                    <!-- Kolom Open (di tengah) -->
                    <div class="col-md-6 text-center">
                        <p><b>Detail Invoice Komplain</b></p>
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

                <div class="row">
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
                                            :  <?php 
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
                                        <td class="text-nowrap">: <?php echo $data_detail['no_inv'] ?></td>
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
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Pelanggan</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['nama_cs'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Pelanggan Invoice</td>
                                        <td class="text-nowrap">: <?php echo $data_detail['cs_inv'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Alamat</td>
                                        <td class="wrap-text">: <?php 
                                                if($data_detail['alamat_inv'] == ''){
                                                    echo $data_detail['alamat'];
                                                } else {
                                                    echo $data_detail['alamat_inv']; 
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php  
                                        if($data_driver['jenis_pengiriman'] == 'Ekspedisi'){
                                            if($data_driver['jenis_ongkir'] == 1) {
                                                ?>
                                                    <tr>
                                                        <td class="col-md-6 text-nowrap">Ongkos Kirim</td>
                                                        <td class="text-nowrap">: <?php echo number_format($data_detail['ongkir']) ?> (COD)</td>
                                                    </tr>
                                                <?php
                                            } else {
                                                ?>
                                                    <tr>
                                                        <td class="col-md-6 text-nowrap">Ongkos Kirim</td>
                                                        <td class="text-nowrap">: <?php echo number_format($data_detail['ongkir']) ?> </td>
                                                    </tr>
                                                <?php
                                            }
                                        }
                                    ?>
                                    <tr>
                                        <td class="col-md-6 text-nowrap">Jenis Pengiriman</td>
                                        <td class="text-nowrap">
                                            :   <?php  
                                                    if($data_driver['jenis_pengiriman'] == 'Driver'){
                                                        ?>
                                                            <?php echo $data_driver['jenis_pengiriman']?> (<?php echo $data_driver['nama_driver'] ?>)
                                                        <?php
                                                    } else if($data_driver['jenis_pengiriman'] == 'Ekspedisi'){
                                                        ?>
                                                            <?php echo $data_driver['jenis_pengiriman']?> (<?php echo $data_driver['nama_ekspedisi'] ?>)
                                                        <?php
                                                    } else {
                                                        ?>
                                                            <?php echo $data_driver['jenis_pengiriman']?> (<?php echo $data_driver['nama_penerima'] ?>)
                                                        <?php
                                                    }
                                            
                                                ?>

                                        </td>
                                    </tr>
                                    <?php  
                                        if(!empty($data_driver['jenis_penerima']) && $data_driver['jenis_pengiriman'] == 'Driver'){
                                            ?>
                                                <tr>
                                                    <td class="col-md-6 text-nowrap">Diterima Oleh</td>
                                                    <td class="text-nowrap">
                                                        :   <?php 
                                                                if($data_driver['jenis_penerima'] == 'Customer'){
                                                                    ?>
                                                                        <?php echo $data_driver['jenis_penerima'] ?> 
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                        <?php echo $data_driver['jenis_penerima'] ?> (<?php echo $data_driver['nama_ekspedisi'] ?>)
                                                                    <?php
                                                                }
                                                        
                                                            ?>
                                                
                                                    </td>
                                                </tr>
                                            <?php
                                        }
                                    
                                    ?>
                                    <?php  
                                        if(!empty($data_driver['nama_penerima'])){
                                            ?>
                                                <tr>
                                                    <td class="col-md-6 text-nowrap">Nama Penerima</td>
                                                    <td class="text-nowrap">: <?php echo $data_driver['nama_penerima'] ?></td>
                                                </tr>
                                            <?php
                                        }
                                    
                                    ?>
                                    <?php  
                                        if(!empty($data_driver['dikirim_oleh']) && !empty($data_driver['penanggung_jawab'])){
                                            ?>
                                                <tr>
                                                    <td class="col-md-6 text-nowrap">Dikirim Oleh</td>
                                                    <td class="text-nowrap">: <?php echo $data_driver['dikirim_oleh'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="col-md-6 text-nowrap">PJ. Paket Kirim</td>
                                                    <td class="text-nowrap">: <?php echo $data_driver['penanggung_jawab'] ?></td>
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
                    .d-flex.justify-content-between.flex-wrap {
                        flex-wrap: wrap;
                        justify-content: center;
                    }
                    .p-1 {
                        flex: 1 1 calc(33.33% - 10px);
                        margin: 5px;
                        text-align: center;
                    }
                    .text-end {
                        text-align: center;
                    }
                    .btn {
                        width: 100%;
                        white-space: nowrap; /* Mencegah teks yang terlalu panjang memecah */
                    }
                    .btn button {
                        display: block;
                        margin-top: 10px;
                    }
                }

            </style>
            <div class="card p-3">
                <div class="row">
                    <div class="col-md-7 mb-3">
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
                    <div class="row mb-3">
                        <div class="col-md-10">
                            <div class="d-flex justify-content-start flex-wrap mt-3">
                                <div class="p-1">
                                    <a href="invoice-komplain.php?date_range=year" class="btn btn-warning">
                                        <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                                    </a>
                                </div>
                                <div class="p-1"> 
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bukti">
                                        <i class="bi bi-image"></i> Bukti Terima Pengiriman
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="p-1 text-end">
                                <button class="btn border-dark">
                                    <b>Total Invoice Original</b><br>
                                    Rp. <a id="grandTotal" style="color:black; font-size:15px;"></a>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Default Tabs -->
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#" class="nav-link active">Original</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="detail-komplain-revisi-nonppn.php?id=<?php echo urlencode($_GET['id']); ?>" class="nav-link">Revisi</a>
                        </li>
                    </ul>
                    <div class="tab-content pt-2" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="table-responsive mt-4">
                                <table class="table table-bordered table-striped" id="table2">
                                    <thead>
                                        <tr class="text-white" style="background-color: navy;">
                                            <th class="text-center text-nowrap p-3">No</th>
                                            <th class="text-center text-nowrap p-3">No.SPK</th>
                                            <th class="text-center text-nowrap p-3">Nama Produk</th>
                                            <th class="text-center text-nowrap p-3">Merk</th>
                                            <th class="text-center text-nowrap p-3">Qty Order</th>
                                            <th class="text-center text-nowrap p-3">Satuan</th>
                                            <th class="text-center text-nowrap p-3">Harga</th>
                                            <?php  
                                                if($data_detail['kategori_inv'] == 'diskon'){
                                                    ?>
                                                            <th class="text-center text-nowrap p-3">Diskon</th>
                                                    <?php
                                                }
                                            
                                            ?>
                                            <th class="text-center text-nowrap p-3">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $no = 1;  
                                            $sub_total = 0;
                                            include "function/class-spk.php";
                                            while($data = mysqli_fetch_array($query)){
                                                $satuan = detailSpkFnc::getSatuan($data['id_produk']);
                                                $total_harga =  $data['harga'] * $data['qty'];
                                                $discount = $data['disc'] / 100; // 50% diskon
                                                $harga_final = $total_harga * (1 - $discount); // Harga akhir setelah diskon
                                                $sub_total += $harga_final;          
                                        ?>
                                        <tr>
                                            <td class="text-center text-nowrap"><?php echo $no ?></td>
                                            <td class="text-center text-nowrap"><?php echo $data['no_spk'] ?></td>
                                            <td class="text-nowrap"><?php echo $data['nama_produk_spk'] ?></td>
                                            <td class="text-center text-nowrap"><?php echo $data['merk'] ?></td>
                                            <td class="text-center text-nowrap"><?php echo $data['qty'] ?></td>
                                            <td class="text-center text-nowrap"><?php echo $satuan ?></td>
                                            <td class="text-end text-nowrap"><?php echo number_format($data['harga']) ?></td>
                                            <?php  
                                                if($data_detail['kategori_inv'] == 'diskon'){
                                                    ?>
                                                        <td class="text-end text-nowrap"><?php echo $data['disc'] ?></td>
                                                    <?php
                                                }
                                            
                                            ?>
                                            <td class="text-end text-nowrap"><?php echo number_format($harga_final) ?></td>
                                        </tr>
                                        <?php $no++ ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <!-- Menampilkan Total Invoice -->
                                <?php  
                                    $grand_total = $sub_total + $data_detail['ongkir'];
                                    $grand_total_formated = number_format($grand_total);
                                ?>
                            </div>
                        </div>
                    </div><!-- End Default Tabs -->
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
<!-- Modal -->
<div class="modal fade" id="bukti" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card-body">
                    <?php
                        include "koneksi.php";
                        $sql_bukti = "SELECT ibt.*, ip.id_inv, ip.nama_penerima, ip.tgl_terima, sk.jenis_penerima, sk.dikirim_ekspedisi, sk.no_resi, ex.nama_ekspedisi
                                        FROM inv_bukti_terima AS ibt
                                        LEFT JOIN inv_penerima ip ON (ibt.id_inv = ip.id_inv)
                                        LEFT JOIN status_kirim sk ON (ibt.id_inv = sk.id_inv)
                                        LEFT JOIN ekspedisi ex ON (ex.id_ekspedisi = sk.dikirim_ekspedisi) 
                                        WHERE ibt.id_inv = '$id_inv'";
                        $query_bukti = mysqli_query($connect, $sql_bukti);
                        $data_bukti = mysqli_fetch_array($query_bukti);
                        $gambar1 = $data_bukti['bukti_satu'];
                        $gambar_bukti1 = "gambar/bukti1/$gambar1";
                        $gambar2 = $data_bukti['bukti_dua'];
                        $gambar_bukti2 = "gambar/bukti2/$gambar2";
                        $gambar3 = $data_bukti['bukti_tiga'];
                        $gambar_bukti3 = "gambar/bukti3/$gambar3";
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    var grandTotal = '<?php echo $grand_total_formated ?>';
    document.getElementById('grandTotal').innerHTML = grandTotal;

</script>




