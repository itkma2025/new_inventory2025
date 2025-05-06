<?php
require_once "../akses.php";
// Periksa apakah parameter 'menu' ada dalam URL
$halaman_sebelumnya = "";
$tambah_produk = "";
$button_pengiriman = "";
$button_edit_pengiriman = "";
$edit_details = "";
if (isset($_GET['menu']) && $_GET['menu'] === 'inv_pembelian') {
    $page = 'invoice';
    $page2  = 'pembelian';
    $halaman_sebelumnya = 'finance-inv-pembelian.php?date_range=year';
} else {
    $page = 'pembelian';
    $halaman_sebelumnya = 'data-pembelian.php?date_range=year';
    $tambah_produk = '<button type="button" class="btn btn-primary btn-detail mb-3" data-bs-toggle="modal" data-bs-target="#modalBarang"><i class="bi bi-plus-circle"></i> Tambah produk</button>';
    $button_pengiriman = '<button type="button" class="btn btn-secondary btn-detail mb-3" data-bs-toggle="modal" data-bs-target="#pengiriman"><i class="bi bi-truck"></i> Pilih jenis pengiriman</button>';
    $button_edit_pengiriman = '<button type="button" class="btn btn-warning btn-detail mb-3" data-bs-toggle="modal" data-bs-target="#editJenisPengiriman"><i class="bi bi-pencil"></i> Edit jenis pengiriman</button>';
    $edit_details = '<button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editDetailsData"><i class="bi bi-pencil"></i> Edit details data</button>';
}
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
    <link rel="stylesheet" href="../assets/css/wrap-text.css">
    <style>
        .animate__animated.animate__jackInTheBox {
            --animate-duration: 2s;
        }

        .animate__animated.animate__flipInX{
            --animate-duration: 2s;
        }
        th{
            padding-top: 15px !important;
            padding-bottom: 15px !important;
            padding-left: 25px !important;
            padding-right: 35px !important;
            text-align: center !important;
            white-space: nowrap !important;
            margin: 10px !important;
            background-color: navy !important;
            color: white !important;
        }

        .col-sm-0 {
            flex: 0 0 auto;
            width: 5.333333%;
        }

        .col-custom-sm-2 {
            flex: 0 0 auto;
            width: 10.666667%;
        }
        .col-custom-sm-5 {
            flex: 0 0 auto;
            width: 39.666667%;
        }
        .col-custom-sm-1 {
            flex: 0 0 auto;
            width: 7.333333%;
        }
        .card-body-custom {
            padding: 0 8px 7px 9px;
        }

        .form-control.error {
            border-color: #dc3545;
        }

        .form-select.error {
            border-color: #dc3545 !important;
        }

        .form-check-input.error {
            border-color: #dc3545 !important;
        }

        .error {
            border-color: #dc3545 !important;
        }

        .error-message {
            color: #dc3545;  
        }
        .bg-readonly{
            background-color: rgb(251,242,250);
        }

        .transparent-card {
            background-color: transparent !important;
            border: none; /* Jika Anda juga ingin menghapus border */
            box-shadow: none;
        }

        .input-container {
            position: relative;
            display: inline-block;
        }

        #cari-data {    
            padding-right: 2.5rem;
        }

        #resetButton {
            position: absolute;
            top: 50%;
            right: 0.5rem;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            z-index: 2;
            display: none; /* Default state: hidden */
        }

        #resetButton i {
            font-size: 1rem;
        }

        #customPagination {
            justify-content: end;
        }

        /* Ubah display pagination bawaan datatable menjadi NONE */
        div.dt-container div.dt-paging ul.pagination {
            display: none;
        }

        @media screen and (max-width: 727px) {
            .card{
                width: 100%;
                margin:  0 0 12px 0 !important;
            }
        }
    </style>
    <style type="text/css">
        .disable-click {
            pointer-events: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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

<body>
    <!-- nav header -->
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include "page/sidebar.php"; ?>
    <!-- end sidebar -->

    <main id="main" class="main">
        <section class="section dashboard">
            <?php
                if (isset($_SESSION['info-update'])) {
                    // Menambahkan script JavaScript untuk menampilkan SweetAlert
                    echo '<script>
                            var notifikasi = "' . $_SESSION['info-update'] . '";
                            Swal.fire({
                                icon: "success",
                                title: "Sukses",
                                text: "Data Berhasil Diupdate",
                            }).then(function() {
                                window.location.reload();
                            });
                        </script>';
                    // Unset session setelah output JavaScript
                    unset($_SESSION['info-update']);
                }
            ?>

            <?php
                if (isset($_SESSION['info'])) {
                    echo '<div class="info-data" data-infodata="' . $_SESSION['info'] . '"></div>';
                    unset($_SESSION['info']);
                }
            ?>
            <!-- END SWEET ALERT -->
            <div class="card">
                <div class="card-header text-center">
                    <h5 class="fw-bold">Detail Produk Pembelian Lokal</h5>
                </div>
                <div class="row p-3">
                    <?php 
                        $id_inv_pembelian = base64_decode($_GET['id']);
                        $no = 1;
                        $sql_pembelian = $connect->query(" SELECT 
                                                                pb.id_inv_pembelian,
                                                                pb.no_trx,
                                                                pb.no_inv,
                                                                pb.tgl_pembelian,
                                                                pb.tgl_tempo,
                                                                pb.total_pembelian,
                                                                pb.status_pembelian,
                                                                pb.status_pembayaran,
                                                                pb.jenis_trx,
                                                                pb.jenis_disc,
                                                                pb.sp_disc,
                                                                pb.note,
                                                                pb.path_inv,
                                                                sp.nama_sp,
                                                                sp.alamat
                                                            FROM inv_pembelian_lokal AS pb
                                                            LEFT JOIN tb_supplier sp ON (pb.id_sp = sp.id_sp)
                                                            WHERE id_inv_pembelian = '$id_inv_pembelian'
                                                            ORDER BY no_trx ASC
                                                        ");
                        $data_inv_pembelian = mysqli_fetch_array($sql_pembelian);
                        $jenis_disc = $data_inv_pembelian['jenis_disc'];
                        $sp_disc = $data_inv_pembelian['sp_disc'] / 100;
                        $jenis_transaksi = $data_inv_pembelian['jenis_trx'];
                        $no_inv_pembelian = "";
                        if($data_inv_pembelian['no_inv'] != ''){
                            $no_inv_pembelian = $data_inv_pembelian['no_inv'];
                        } else {
                            $no_inv_pembelian = "Tidak Ada";
                        }
                    
                    ?>
                    <div class="col mb-2">
                        <div class="border">
                            <div class="table-responsive">  
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <td class="col-md-3 text-nowrap">No. Transaksi Pembelian</td>
                                            <td class="col-md-9 text-nowrap">: <?php echo $data_inv_pembelian['no_trx'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-3 text-nowrap">Tgl. Pembelian</td>
                                            <td class="col-md-9 text-nowrap">: <?php echo $data_inv_pembelian['tgl_pembelian'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-3 text-nowrap">No. Invoice Pembelian</td>
                                            <td class="col-md-9 text-nowrap">: <?php echo $no_inv_pembelian ?></td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-3 text-nowrap">Jenis Transaksi</td>
                                            <td class="col-md-9 text-nowrap">: <?php echo $data_inv_pembelian['jenis_trx'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-3 text-nowrap">Jenis Diskon</td>
                                            <td class="col-md-9 text-nowrap">: <?php 
                                                                                    if ($data_inv_pembelian['jenis_disc'] == 'Spesial Diskon'){
                                                                                        echo 'Spesial Diskon (' . $data_inv_pembelian['sp_disc'] . '%)';
                                                                                    } else {
                                                                                        echo $data_inv_pembelian['jenis_disc'];
                                                                                    }
                                                                                             
                                                                                ?>
                                            </td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-2">
                        <div class="border" style="min-height: 218px;">
                            <div class="table-responsive">  
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <td class="col-md-3 text-nowrap">Tgl. Jatuh Tempo</td>
                                            <td class="col-md-9 text-nowrap">
                                                : <?php 
                                                    if($data_inv_pembelian['tgl_tempo'] != ''){
                                                        echo $data_inv_pembelian['tgl_tempo']; 
                                                    } else {
                                                        echo "Tidak Ada Tempo"; 
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-3 text-nowrap">Nama Supplier</td>
                                            <td class="col-md-9 text-nowrap">: <?php echo $data_inv_pembelian['nama_sp'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-3 d-flex align-items-stretch text-nowrap">Alamat Supplier</td>
                                            <td class="col-md-9 wrap-text">: <?php echo $data_inv_pembelian['alamat'] ?></td>
                                        </tr>
                                        <?php 
                                            if($data_inv_pembelian['note'] != '') {
                                                ?>
                                                    <tr>
                                                        <td class="col-md-3 text-nowrap">Notes Pembelian</td>
                                                        <td class="col-md-9 wrap-text">: <?php echo $data_inv_pembelian['note'] ?></td>
                                                    </tr>
                                                <?php
                                            }
                                        ?>
                                        <?php  
                                            $status_pengiriman = $connect->query("SELECT 
                                                                                        skp.id_inv_pembelian,
                                                                                        skp.jenis_pengiriman,
                                                                                        skp.diambil_oleh,
                                                                                        skp.dikirim_oleh,
                                                                                        skp.nama_kurir_pengirim,
                                                                                        skp.jenis_ongkir,
                                                                                        skp.nominal_ongkir,
                                                                                        skp.free_ongkir,
                                                                                        skp.tanggal,
                                                                                        ex.nama_ekspedisi,
                                                                                        us.nama_user
                                                                                    FROM status_kirim_pembelian AS skp
                                                                                    LEFT JOIN ekspedisi ex ON (skp.nama_ekspedisi = ex.id_ekspedisi)
                                                                                    LEFT JOIN $database2.user us ON (skp.diambil_oleh = us.id_user)
                                                                                    WHERE id_inv_pembelian = '$id_inv_pembelian'
                                                                                ");
                                            $data_status_kirim = mysqli_fetch_array($status_pengiriman);
                                            $total_data_pengiriman = mysqli_num_rows($status_pengiriman);
                                            if($total_data_pengiriman != 0){
                                                $jenis_pengiriman = $data_status_kirim['jenis_pengiriman'];
                                                if($jenis_pengiriman == "Diambil"){
                                                    $diambil_oleh = $data_status_kirim['nama_user'];
                                                    ?>
                                                        <tr>
                                                            <td class="col-md-3 text-nowrap">Jenis Pengiriman</td>
                                                            <td class="col-md-9 wrap-text">: <?php echo $jenis_pengiriman ?> (<?php echo $diambil_oleh ?>)</td>
                                                        </tr> 
                                                    <?php 

                                                } else {
                                                    $dikirim_oleh = $data_status_kirim['dikirim_oleh'];
                                                    if($dikirim_oleh == "Kurir Internal"){
                                                        $nama_kurir_internal = $data_status_kirim['nama_kurir_pengirim'];
                                                        ?>
                                                            <tr>
                                                                <td class="col-md-3 text-nowrap">Jenis Pengiriman</td>
                                                                <td class="col-md-9 wrap-text">: <?php echo $dikirim_oleh ?> (<?php echo $nama_kurir_internal ?>)</td>
                                                            </tr> 
                                                            <tr>
                                                                <td class="col-md-3 text-nowrap">Nominal Ongkir</td>
                                                                <td class="col-md-9 wrap-text">: <?php 
                                                                        if($data_status_kirim['free_ongkir'] == 1){
                                                                            echo "0 (Free Ongkir)";
                                                                        } else {
                                                                            echo number_format($data_status_kirim['nominal_ongkir']);
                                                                        }
                                                                      ?>
                                                                </td>
                                                            </tr> 
                                                        <?php                                               
                                                    } else {
                                                        $nama_ekspedisi = $data_status_kirim['nama_ekspedisi'];
                                                        ?>
                                                            <tr>
                                                                <td class="col-md-3 text-nowrap">Jenis Pengiriman</td>
                                                                <td class="col-md-9 wrap-text">: <?php echo $jenis_pengiriman ?> (<?php echo $nama_ekspedisi ?>)</td>
                                                            </tr> 
                                                            <tr>
                                                                <td class="col-md-3 text-nowrap">Nominal Ongkir</td>
                                                                <td class="col-md-9 wrap-text">: <?php 
                                                                        if($data_status_kirim['free_ongkir'] == 1){
                                                                            echo "0 (Free Ongkir)";
                                                                        } else {
                                                                            echo number_format($data_status_kirim['nominal_ongkir']);
                                                                        }
                                                                      ?>
                                                                </td>
                                                            </tr> 
                                                        <?php 
                                                    }
                                                }
                                            }
                                        ?>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center flex-wrap">
                        <div class="card text-center">
                            <?php echo $edit_details; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 p-3">                   
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-start flex-wrap">
                                <div class="card me-2 transparent-card">
                                    <a href="<?php echo $halaman_sebelumnya; ?>" class="btn btn-warning mb-3"><i class="bi bi-arrow-left-circle"></i> Halaman sebelumnya</a>
                                </div>
                                <div class="card me-2 transparent-card" id="addProduk" style="display: none;">
                                    <?php echo $tambah_produk ?>
                                </div>
                                <div class="card me-2 transparent-card" id="editProduk" style="display: block;">
                                    <button id="editProduk" class="btn btn-secondary"><i class="bi bi-pencil"></i> Edit Data Produk</button>
                                </div>
                                <div class="card me-2 transparent-card">
                                    <?php  
                                        // Button Pengiriman
                                        $cek_data_pengiriman = $connect->query("SELECT id_inv_pembelian FROM status_kirim_pembelian WHERE id_inv_pembelian = '$id_inv_pembelian' ");

                                        $total_data_pengiriman = mysqli_num_rows($cek_data_pengiriman);
                                        if($total_data_pengiriman == 0) {
                                        echo $button_pengiriman;
                                        } else {
                                            echo $button_edit_pengiriman;
                                        }                 
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end flex-wrap">
                                <div class="card me-2 transparent-card text-end">
                                    <div class="input-container">
                                        <input type="text" class="form-control" placeholder="Cari Data" id="cari-data">
                                        <button type="button" class="text-secondary" id="resetButton">
                                            <i class="bi bi-x fs-5"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end flex-wrap">
                        <div class="card me-2 transparent-card border p-2 text-center" style="min-width: 235px;">
                            <p class="fw-bold fs-6">Total Invoice</p>
                            <p>Rp <span id="totalInv"></span></p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <?php
                           
                            $no = 1;
                            $sql_pembelian_produk = "SELECT  
                                                        pb.id_inv_pembelian,
                                                        pb.id_inv_pembelian,
                                                        pb.status_pembelian,
                                                        trx.id_trx_produk,
                                                        trx.id_produk,
                                                        trx.nama_produk,
                                                        trx.harga,
                                                        trx.qty,
                                                        trx.disc,
                                                        trx.total_harga,
                                                        spr.stock, 
                                                        COALESCE(spr.stock, spe.stock) AS stock,
                                                        COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                                                        COALESCE(mr_produk.nama_merk, mr_produk_ecat.nama_merk, mr_set.nama_merk, mr_set_ecat.nama_merk) AS merk_produk -- Nama merk untuk produk
                                                    FROM trx_produk_pembelian AS trx
                                                    LEFT JOIN inv_pembelian_lokal pb ON pb.id_inv_pembelian = trx.id_inv_pembelian
                                                    LEFT JOIN stock_produk_reguler spr ON trx.id_produk = spr.id_produk_reg
                                                    LEFT JOIN stock_produk_ecat spe ON trx.id_produk = spe.id_produk_ecat
                                                    LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                                                    LEFT JOIN tb_produk_ecat tpe ON trx.id_produk = tpe.id_produk_ecat
                                                    LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                                                    LEFT JOIN tb_produk_set_ecat tpse ON trx.id_produk = tpse.id_set_ecat
                                                    LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                                    LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk ecat
                                                    LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                                    LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                                                    WHERE pb.id_inv_pembelian = '$id_inv_pembelian' AND status_trx = '1'";
                            $query1 = $connect->query($sql_pembelian_produk);  
                            $query2 = $connect->query($sql_pembelian_produk);                                        
                            $total_data_produk = mysqli_num_rows($query1);
                            $cek_status_pembelian = mysqli_fetch_array($query2);
                            $status_pembelian = $cek_status_pembelian['status_pembelian'] ?? '';
                        ?>
                        <?php  
                            if($total_data_produk != 0){
                                ?>
                                    <table class="table table-striped table-responsive" id="tableNoExportNew">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Produk</th>
                                                <th>Merk</th>
                                                <th>Qty Order</th>
                                                <th>Harga</th>
                                                <th>Diskon</th>
                                                <th>Total Harga</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $total_pembelian = 0;
                                                while($data_produk = mysqli_fetch_array($query1)){
                                                    $id_trx_produk = $data_produk['id_trx_produk'];
                                                    $id_produk = $data_produk['id_produk'];
                                                    $satuan = $data_produk['satuan'];
                                                    $satuan_produk = '';
                                                    $id_produk_substr = substr($id_produk, 0, 2);
                                                    if ($id_produk_substr == 'BR') {
                                                        $satuan_produk = $satuan;
                                                    } else {
                                                        $satuan_produk = 'Set';
                                                    } 
                                                    $total_pembelian += $data_produk['total_harga'];

                                                    $harga = $data_produk['harga'];
                                                    $qty = $data_produk['qty'];
                                                    $disc = $data_produk['disc'] / 100;
                                                    $sub_harga =  $harga * $qty;
                                                    $sub_harga_disc = $sub_harga * $disc;
                                                    $final_harga = $sub_harga - $sub_harga_disc;
                                                    $update_total_harga = $connect->query("UPDATE trx_produk_pembelian SET total_harga = '$final_harga' WHERE id_trx_produk = '$id_trx_produk'")
                                            ?>
                                            <tr>
                                                <td class="text-center"><?php echo $no; ?></td>
                                                <td class="text-nowrap"><?php echo $data_produk['nama_produk']; ?></td>
                                                <td class="text-center"><?php echo $data_produk['merk_produk']; ?></td>
                                                <td class="text-end"><?php echo number_format($data_produk['qty']); ?> <?php echo $satuan_produk; ?></td>
                                                <td class="text-end"><?php echo number_format($data_produk['harga']); ?></td>
                                                <td class="text-end"><?php echo $data_produk['disc']; ?></td>
                                                <td class="text-end">Rp <?php echo number_format($data_produk['total_harga'],0,'.','.'); ?></td>
                                                <td class="text-center">
                                                    <!-- Button Modal Edit -->
                                                    <button type="button" class="btn btn-warning btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#editProduk" title="Edit Produk" data-id="<?php echo $data_produk['id_trx_produk'] ?>" data-nama="<?php echo $data_produk['nama_produk'] ?>" data-merk="<?php echo $data_produk['merk_produk'] ?>" data-harga="<?php echo number_format($data_produk['harga']) ?>" data-disc="<?php echo $data_produk['disc'] ?>" data-qty="<?php echo number_format($data_produk['qty']) ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <a href="proses/produk-pembelian.php?hapus_trx=<?php echo base64_encode($data_produk['id_trx_produk']) ?>&&id_inv=<?php echo base64_encode($id_inv_pembelian) ?>"  class="btn btn-danger btn-sm delete-data" title="Hapus Produk"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                            <?php  $no++; ?>
                                            <?php } ?>
                                            <?php 
                                                $ongkir = 0;
                                                if($total_data_pengiriman != 0){
                                                    if($data_status_kirim['free_ongkir'] == 1){
                                                        $ongkir = 0;
                                                    } else {
                                                       $ongkir = $data_status_kirim['nominal_ongkir'];
                                                    }
                                                    if($jenis_transaksi == 'PPN'){
                                                        $sub_total_spdisc = $total_pembelian * $sp_disc;
                                                        $grand_total_spdisc = $total_pembelian - $sub_total_spdisc;
                                                        $grand_total_ppn = $grand_total_spdisc * 1.11;
                                                        $grand_total_fix =  $grand_total_ppn + $ongkir;
                                                        $update_total_pembelian = $connect->query("UPDATE inv_pembelian_lokal  SET total_pembelian = '$grand_total_fix' WHERE id_inv_pembelian = '$id_inv_pembelian'");
                                                    } else {
                                                        $sub_total_spdisc = $total_pembelian * $sp_disc;
                                                        $grand_total_spdisc = $total_pembelian - $sub_total_spdisc;
                                                        $grand_total_fix =  $grand_total_spdisc + $ongkir;
                                                        $update_total_pembelian = $connect->query("UPDATE inv_pembelian_lokal  SET total_pembelian = '$grand_total_fix' WHERE id_inv_pembelian = '$id_inv_pembelian'");
                                                    }
                                                } else {
                                                    if($jenis_transaksi == 'PPN'){
                                                        $sub_total_spdisc = $total_pembelian * $sp_disc;
                                                        $grand_total_spdisc = $total_pembelian - $sub_total_spdisc;
                                                        $grand_total_ppn = $grand_total_spdisc * 1.11;
                                                        $grand_total_fix =  $grand_total_ppn + $ongkir;
                                                        $update_total_pembelian = $connect->query("UPDATE inv_pembelian_lokal  SET total_pembelian = '$grand_total_fix' WHERE id_inv_pembelian = '$id_inv_pembelian'");
                                                    } else {
                                                        $sub_total_spdisc = $total_pembelian * $sp_disc;
                                                        $grand_total_spdisc = $total_pembelian - $sub_total_spdisc;
                                                        $grand_total_fix =  $grand_total_spdisc;
                                                        $update_total_pembelian = $connect->query("UPDATE inv_pembelian_lokal  SET total_pembelian = '$grand_total_fix' WHERE id_inv_pembelian = '$id_inv_pembelian'");
                                                    }
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                <?php
                            }
                        
                        ?>
                    </div>
                    <!-- End Table Responsive -->
                    <?php  
                        if($total_data_produk != 0){
                            ?>
                                <div class="text-start">
                                    <p>Total data :  Data</p>
                                </div>
                            <?php
                        }
                    ?>
                    <!-- Custom pagination -->
                    <nav>
                        <ul class="pagination" id="customPagination">
                        <!-- Pagination items will be inserted here by JavaScript -->
                        </ul>
                    </nav>
                    <?php
                        $no = 1;
                        $sql_pembelian_trx = $connect->query("SELECT  
                                                                    pb.id_inv_pembelian,
                                                                    pb.id_inv_pembelian,
                                                                    trx.id_trx_produk,
                                                                    trx.id_produk,
                                                                    trx.qty,
                                                                    trx.total_harga,
                                                                    spr.stock, 
                                                                    COALESCE(spr.stock, spe.stock) AS stock,
                                                                    COALESCE(tpr.nama_produk, tpe.nama_produk, tpsm.nama_set_marwa, tpse.nama_set_ecat) AS nama_produk,
                                                                    COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                                                                    COALESCE(tpr.harga_produk, tpe.harga_produk, tpsm.harga_set_marwa, tpse.harga_set_ecat) AS harga_produk,
                                                                    COALESCE(mr_produk.nama_merk, mr_produk_ecat.nama_merk, mr_set.nama_merk, mr_set_ecat.nama_merk) AS merk_produk -- Nama merk untuk produk
                                                                FROM trx_produk_pembelian AS trx
                                                                LEFT JOIN inv_pembelian_lokal pb ON pb.id_inv_pembelian = trx.id_inv_pembelian
                                                                LEFT JOIN stock_produk_reguler spr ON trx.id_produk = spr.id_produk_reg
                                                                LEFT JOIN stock_produk_ecat spe ON trx.id_produk = spe.id_produk_ecat
                                                                LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                                                                LEFT JOIN tb_produk_ecat tpe ON trx.id_produk = tpe.id_produk_ecat
                                                                LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                                                                LEFT JOIN tb_produk_set_ecat tpse ON trx.id_produk = tpse.id_set_ecat
                                                                LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                                                LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk ecat
                                                                LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                                                LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                                                                WHERE pb.id_inv_pembelian = '$id_inv_pembelian' AND status_trx = '0'
                                                                ");
                        $total_data_trx = mysqli_num_rows($sql_pembelian_trx);
                    ?>
                    <?php  
                        if($total_data_trx != 0){
                            ?>
                                <h5 class="text-center">Tambah Produk Pembelian</h5>
                                <div class="card-body-custom">
                                    <div class="row">
                                        <div class="col-sm-0">
                                            <P class="text-center">No</P>
                                        </div>
                                        <div class="col-custom-sm-5">
                                            <P class="text-center">Nama Produk</P>
                                        </div>
                                        <div class="col-custom-sm-1">
                                            <P class="text-center">Satuan</P>
                                        </div>
                                        <div class="col-sm-1">
                                            <P class="text-center">Merk</P>
                                        </div>
                                        <div class="col-custom-sm-2">
                                            <P class="text-center">Harga</P>
                                        </div>
                                        <div class="col-custom-sm-1">
                                            <P class="text-center">Disc</P>
                                        </div>
                                        <div class="col-custom-sm-1">
                                            <P class="text-center">Stock</P>
                                        </div>
                                        <div class="col-sm-1">
                                            <P class="text-center">Qty Order</P>
                                        </div>
                                        <div class="col-sm-0 text-center">
                                            <P class="text-center">Aksi</P>
                                        </div>
                                    </div>
                                </div>
                                <form action="proses/produk-pembelian.php" method="post">
                                    <?php
                                        while($data_trx = mysqli_fetch_array($sql_pembelian_trx)){
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
                                    <div class="card-body-custom">
                                        <div class="row">
                                            <div class="col-sm-0 mb-1">
                                                <input type="text" class="form-control text-center bg-light mobile" value="<?php echo $no; ?>" readonly>
                                                <?php $no++ ?>
                                            </div>
                                            <div class="col-custom-sm-5 mb-1">
                                                <input type="hidden" name="id_trx[]" id="id_<?php echo $data_trx['id_trx_produk'] ?>" value="<?php echo $data_trx['id_trx_produk'] ?>" readonly>
                                                <input type="hidden" name="id_inv" value="<?php echo $id_inv_pembelian ?>" readonly>
                                                <input type="hidden" class="form-control" name="id_produk_trx[]" value="<?php echo $data_trx['id_produk'] ?>" readonly>
                                                <input type="text" class="form-control bg-light text-wrap" name="nama_produk[]" value="<?php echo $data_trx['nama_produk']; ?>" readonly>
                                            </div>
                                            <div class="col-custom-sm-1 mb-1">
                                                <input type="text" class="form-control bg-light text-center mobile-text" value="<?php echo $satuan_produk; ?>" readonly>
                                            </div>
                                            <div class="col-sm-1 mb-1">
                                                <input type="text" class="form-control bg-light text-center text-wrap mobile-text text-break" value="<?php echo $data_trx['merk_produk']; ?>" readonly>
                                            </div>
                                            <div class="col-custom-sm-2 mb-1">
                                                <input type="text" class="form-control text-end mobile-text harga_produk" name="harga[]" maxlength="20" required>
                                            </div>
                                            <div class="col-custom-sm-1 mb-1">
                                                <input type="text" class="form-control bg-light text-end mobile-text disc-input" name="disc[]" id="disc" maxlength="4" oninput="validateInput(this)" readonly>
                                            </div>
                                            <div class="col-custom-sm-1 mb-1">
                                                <input type="text" class="form-control bg-light text-end mobile-text" name="stock" id="stock_<?php echo $data_trx['id_trx_produk'] ?>" value="<?php echo number_format($data_trx['stock']) ?>" readonly>
                                            </div>   
                                            <div class="col-sm-1 mb-1">
                                                <input type="text" class="form-control text-end mobile-text" name="qty_trx[]" id="qtyInput_<?php echo $data_trx['id_trx_produk']?>" oninput="checkStock('<?php echo $data_trx['id_trx_produk'] ?>')" maxlength="10" required>
                                            </div>
                                            <div class="col-sm-0 mb-1 text-center">
                                                <a href="proses/produk-pembelian.php?hapus_trx=<?php echo base64_encode($data_trx['id_trx_produk']) ?>&&id_inv=<?php echo base64_encode($id_inv_pembelian) ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="card-body mt-3 text-end">
                                        <button type="submit" class="btn btn-primary" name="simpan-trx" id="simpan-data"><i class="bi bi-save"></i> Simpan</button>
                                    </div>
                                    <script>
                                        function validateInput(input) {
                                            if (input.value > 100) {
                                                input.value = 100;
                                            }
                                        }

                                        function formatDecimal(input) {
                                            // Mengubah nilai input menjadi desimal dengan satu desimal
                                            input.value = parseFloat(input.value).toFixed(1);
                                        }
                                        var jenisDisc = "<?php echo $jenis_disc ?>";
                                        var disc =  document.getElementById('disc');
                                        if(jenisDisc == "Diskon Satuan"){
                                            var discInputs = document.getElementsByClassName('disc-input');
                                            for (var i = 0; i < discInputs.length; i++) {
                                                discInputs[i].removeAttribute('readonly');
                                                discInputs[i].setAttribute('required', 'true');
                                                discInputs[i].classList.remove('bg-light');
                                            }
                                        }
                                    </script>
                                </form>
                            <?php
                        }
                    ?>
                </div>
            </div>
            <!-- Modal Edit Details-->
            <div class="modal fade" id="editDetailsData" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Details Data</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="proses/pembelian.php" method="POST">
                                <input type="hidden" name="id_inv" value="<?php echo $id_inv_pembelian ?>">
                                <input type="hidden" name="nama_sp" value="<?php echo $data_inv_pembelian['nama_sp'] ?>">
                                <input type="hidden" name="path_inv" value="<?php echo $data_inv_pembelian['path_inv'] ?>">
                                <input type="hidden" class="form-control" name="no_inv_pembelian_lama" value="<?php echo $data_inv_pembelian['no_inv'] ?>">
                                <div class="mb-3">
                                    <label class="fw-bold">Tgl. Pembelian</label>
                                    <input type="text" class="form-control" id="date" name="tgl_pembelian" value="<?php echo $data_inv_pembelian['tgl_pembelian'] ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">No. Invoice Pembelian</label>
                                    <input type="text" class="form-control" name="no_inv_pembelian" value="<?php echo $data_inv_pembelian['no_inv'] ?>">
                                </div>
                                <div class="col-mb-3 mt-2">
                                    <label class="fw-bold mb-2">Jenis Transaksi</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_trx" value="PPN" <?php echo ($data_inv_pembelian['jenis_trx'] == 'PPN') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="inlineRadio1">PPN</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_trx" value="Non PPN" <?php echo ($data_inv_pembelian['jenis_trx'] == 'Non PPN') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="inlineRadio2">Non PPN</label>
                                    </div>
                                </div>
                                <div class="mb-3 mt-2">
                                    <label class="fw-bold">Tanggal Jatuh Tempo</label>
                                    <div class="input-group flex-nowrap">
                                        <input type="text" class="form-control" name="tgl_tempo" id="dateTempo" value="<?php echo $data_inv_pembelian['tgl_tempo'] ?>">
                                        <button type="button" class="input-group-text bg-danger text-white" id="resetTempo"> X </button>
                                    </div>
                                </div>
                                <div class="col-mb-3">
                                    <label class="fw-bold mb-2">Jenis Diskon</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_diskon" value="Tanpa Diskon" <?php echo ($data_inv_pembelian['jenis_disc'] == 'Tanpa Diskon') ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Tanpa Diskon</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_diskon" value="Diskon Satuan" <?php echo ($data_inv_pembelian['jenis_disc'] == 'Diskon Satuan') ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Diskon Satuan</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_diskon" value="Spesial Diskon" <?php echo ($data_inv_pembelian['jenis_disc'] == 'Spesial Diskon') ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Spesial Diskon</label>
                                    </div>
                                </div>
                                <div class="mb-3 mt-2" id="sp_disc" style="display: none;">
                                    <label class="fw-bold">Spesial Diskon</label>
                                    <input type="text" class="form-control" name="sp_disc" value="<?php echo $data_inv_pembelian['sp_disc'] ?>">
                                </div>
                                <div class="modal-footer mt-3">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary" name="edit-detail">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal Edit Details -->
            
            <!-- Modal Barang -->
            <div class="modal fade" id="modalBarang" data-bs-backdrop="static"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <form method="post" action=""> <!-- Tambahkan form dengan method POST -->
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Data Barang</h1>
                            </div>
                            <div class="modal-body">
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="prod-reg-tab" data-bs-toggle="pill" data-bs-target="#prod-reg" type="button" role="tab" aria-controls="prod-reg" aria-selected="true">Produk Reguler</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="prod-ecat-tab" data-bs-toggle="pill" data-bs-target="#prod-ecat" type="button" role="tab" aria-controls="prod-ecat" aria-selected="false">Produk E-Cat</button>
                                    </li>
                                </ul>
                                        
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="prod-reg" role="tabpanel" aria-labelledby="prod-reg-tab" tabindex="0">
                                        <div class="table-responsive position-relative"> <!-- Tambahkan class position-relative untuk posisi relatif -->
                                            <div id="loading-indicator" class="position-absolute top-50 start-50 translate-middle" style="display: none;">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                            <table class="table table-striped table-bordered" id="table3">
                                                <thead>
                                                    <tr class="text-white" style="background-color: #051683;">
                                                        <td class="text-center p-3 text-nowrap" style="width: 50px">No</td>
                                                        <td class="text-center p-3 text-nowrap" style="width: 350px">Kode Produk</td>
                                                        <td class="text-center p-3 text-nowrap" style="width: 350px">Nama Produk</td>
                                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Satuan</td>
                                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Merk</td>
                                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Stock</td>
                                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Aksi</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $id = $_GET['id'];
                                                    $selected_produk = [];
                                                    $no = 1;

                                                    // Mengambil data produk yang ada dalam trx_produk_pembelian untuk id_inv yang sedang aktif
                                                    $query_selected_produk = mysqli_query($connect, "SELECT id_produk FROM trx_produk_pembelian WHERE id_inv_pembelian = '$id_inv_pembelian'");
                                                    while ($selected_data = mysqli_fetch_array($query_selected_produk)) {
                                                        $selected_produk[] = $selected_data['id_produk'];
                                                    }

                                                    $sql = "SELECT 
                                                                COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa) AS id_produk,
                                                                COALESCE(tpr.kode_produk, tpsm.kode_set_marwa) AS kode_produk,
                                                                COALESCE(tpr.nama_produk, tpsm.nama_set_marwa) AS nama_produk,
                                                                COALESCE(mr_tpr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                                                                tpr.satuan,
                                                                spr.id_stock_prod_reg,
                                                                spr.stock,
                                                                tkp.min_stock, 
                                                                tkp.max_stock
                                                            FROM stock_produk_reguler AS spr
                                                            LEFT JOIN tb_produk_reguler AS tpr ON (tpr.id_produk_reg = spr.id_produk_reg)
                                                            LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spr.id_kat_penjualan)
                                                            LEFT JOIN tb_produk_set_marwa AS tpsm ON (tpsm.id_set_marwa = spr.id_produk_reg)
                                                            LEFT JOIN tb_merk AS mr_tpr ON (tpr.id_merk = mr_tpr.id_merk)
                                                            LEFT JOIN tb_merk AS mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
                                                            ORDER BY nama_produk ASC";

                                                    $query = mysqli_query($connect, $sql);

                                                    while ($data = mysqli_fetch_array($query)) {
                                                        $id_produk = $data['id_produk'];
                                                        $id_produk_substr = substr($id_produk, 0, 2);
                                                        $isChecked = in_array($id_produk, $selected_produk);
                                                        $isDisabled = false;

                                                        if ($data['stock'] == 0) {
                                                            $isDisabled = true; // Jika stock = 0, maka tombol pilih akan menjadi disabled
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                                            <td class="text-nowrap text-center"><?php echo $data['kode_produk']; ?></td>
                                                            <td class="text-nowrap"><?php echo $data['nama_produk']; ?></td>
                                                            <td class="text-center text-nowrap">
                                                                <?php 
                                                                if($id_produk_substr == 'BR'){
                                                                    echo $data['satuan'];
                                                                } else {
                                                                    echo "Set";
                                                                }
                                                                ?>
                                                            </td>
                                                            <td class="text-center text-nowrap"><?php echo $data['nama_merk']; ?></td>
                                                            <td class="text-center text-nowrap"><?php echo number_format($data['stock']); ?></td>
                                                            <td class="text-center text-nowrap">
                                                                <button class="pilih-produk btn btn-primary btn-sm" data-id="<?php echo $id_produk; ?>"  data-inv="<?php echo $id_inv_pembelian; ?>" <?php echo ($isChecked || $isDisabled) ? 'style="display:none"' : ''; ?>>Pilih</button>
                                                            </td>
                                                        </tr>
                                                        <?php $no++; ?>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="prod-ecat" role="tabpanel" aria-labelledby="prod-ecat-tab" tabindex="0">
                                        <div class="table-responsive position-relative"> <!-- Tambahkan class position-relative untuk posisi relatif -->
                                            <div id="loading-indicator" class="position-absolute top-50 start-50 translate-middle" style="display: none;">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                            <table class="table table-striped table-bordered" id="table5">
                                                <thead>
                                                    <tr class="text-white" style="background-color: #051683;">
                                                        <td class="text-center p-3 text-nowrap" style="width: 50px">No</td>
                                                        <td class="text-center p-3 text-nowrap" style="width: 350px">Kode Produk</td>
                                                        <td class="text-center p-3 text-nowrap" style="width: 350px">Nama Produk</td>
                                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Satuan</td>
                                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Merk</td>
                                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Stock</td>
                                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Aksi</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                   
                                                    $id = $_GET['id'];
                                                    $selected_produk = [];
                                                    $no = 1;

                                                    // Mengambil data produk yang ada dalam trx_produk_pembelian untuk id_inv yang sedang aktif
                                                    $query_selected_produk = mysqli_query($connect, "SELECT id_produk FROM trx_produk_pembelian WHERE id_inv_pembelian = '$id_inv_pembelian'");
                                                    while ($selected_data = mysqli_fetch_array($query_selected_produk)) {
                                                        $selected_produk[] = $selected_data['id_produk'];
                                                    }

                                                    $sql = "SELECT 
                                                                COALESCE(tpr.id_produk_ecat, tpsm.id_set_ecat) AS id_produk,
                                                                COALESCE(tpr.kode_produk, tpsm.kode_set_ecat) AS kode_produk,
                                                                COALESCE(tpr.nama_produk, tpsm.nama_set_ecat) AS nama_produk,
                                                                COALESCE(mr_tpr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                                                                tpr.satuan,
                                                                spr.id_stock_prod_ecat,
                                                                spr.stock,
                                                                tkp.min_stock, 
                                                                tkp.max_stock
                                                            FROM stock_produk_ecat AS spr
                                                            LEFT JOIN tb_produk_ecat AS tpr ON (tpr.id_produk_ecat = spr.id_produk_ecat)
                                                            LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spr.id_kat_penjualan)
                                                            LEFT JOIN tb_produk_set_ecat AS tpsm ON (tpsm.id_set_ecat = spr.id_produk_ecat)
                                                            LEFT JOIN tb_merk AS mr_tpr ON (tpr.id_merk = mr_tpr.id_merk)
                                                            LEFT JOIN tb_merk AS mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
                                                            ORDER BY nama_produk ASC";

                                                    $query = mysqli_query($connect, $sql);

                                                    while ($data = mysqli_fetch_array($query)) {
                                                        $id_produk = $data['id_produk'];
                                                        $id_produk_substr = substr($id_produk, 0, 2);
                                                        $isChecked = in_array($id_produk, $selected_produk);
                                                        $isDisabled = false;

                                                        if ($data['stock'] == 0) {
                                                            $isDisabled = true; // Jika stock = 0, maka tombol pilih akan menjadi disabled
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                                            <td class="text-nowrap text-center"><?php echo $data['kode_produk']; ?></td>
                                                            <td class="text-nowrap"><?php echo $data['nama_produk']; ?></td>
                                                            <td class="text-center text-nowrap">
                                                                <?php 
                                                                if($id_produk_substr == 'BR'){
                                                                    echo $data['satuan'];
                                                                } else {
                                                                    echo "Set";
                                                                }
                                                                ?>
                                                            </td>
                                                            <td class="text-center text-nowrap"><?php echo $data['nama_merk']; ?></td>
                                                            <td class="text-center text-nowrap"><?php echo number_format($data['stock']); ?></td>
                                                            <td class="text-center text-nowrap">
                                                                <button class="pilih-produk btn btn-primary btn-sm" data-id="<?php echo $id_produk; ?>"  data-inv="<?php echo $id_inv_pembelian; ?>" <?php echo ($isChecked || $isDisabled) ? 'style="display:none"' : ''; ?>>Pilih</button>
                                                            </td>
                                                        </tr>
                                                        <?php $no++; ?>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                        
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="refreshPage()">Close</button>
                            </div>
                        </form> <!-- Akhir dari form -->
                    </div>
                </div>
            </div>
            <!-- End Modal Barang-->
            <!-- Modal Jenis pengiriman-->   
            <div class="modal fade animate__animated animate__fadeInDown" id="pengiriman" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="card-header">
                                <h1 class="text-center fs-5 text-dark" id="exampleModalLabel">Pilih Jenis Pengiriman</h1>
                            </div>
                            <div class="card-body">
                                <?php
                                $uuid = generate_uuid();
                                $year = date('y');
                                $day = date('d');
                                $month = date('m');
                                ?>
                                <form action="proses/status-kirim-pembelian.php" method="POST" enctype="multipart/form-data" id="myForm">
                                    <input type="hidden" name="id_status_kirim" value="SKP-<?php echo $year ?><?php echo $month ?><?php echo $uuid ?><?php echo $day ?>">
                                    <input type="hidden" name="id_inv_pembelian" value="<?php echo $id_inv_pembelian ?>">
                                    <div class="mb-3 animate__animated animate__flipInX" style="display: block;">
                                        <label class="fw-bold mb-3">Jenis Pengiriman</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenis_pengiriman" id="diambil" value="Diambil" data-type="radio">
                                            <label class="form-check-label" for="diambil">Diambil Langsung</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenis_pengiriman" id="dikirim" value="Dikirim" data-type="radio">
                                            <label class="form-check-label" for="dikirim">Dikirim</label>
                                        </div>
                                        <small class="validasi" style="display: none;"><div class="error-message"><i class="bi bi-exclamation-triangle-fill"></i> Jenis pengiriman harus dipilih!</div></small>
                                    </div>
                                    <div class="mb-3 position-relative" id="diambilOleh" style="display: none;">
                                        <label class="fw-bold">Diambil Oleh</label>
                                        <select name="diambil_oleh" class="selectize-js" id="diambil_oleh" data-type="select">
                                            <option value="">Pilih...</option>
                                            <?php
                                           
                                            $sql_driver = mysqli_query($connect, "SELECT us.id_user_role, us.id_user, us.nama_user, us.is_approval, rl.nama_role FROM $database2.user AS us JOIN $database2.user_role rl ON (us.id_user_role = rl.id_user_role) WHERE rl.nama_role = 'Driver' AND us.is_approval = '1'");
                                            while ($data_driver = mysqli_fetch_array($sql_driver)) {
                                            ?>
                                                <option value="<?php echo $data_driver['nama_user'] ?>"><?php echo $data_driver['nama_user'] ?></option>
                                            <?php } ?>
                                            <option value="Sutarni">Sutarni</option>
                                            <option value="Sugiyatmi">Sugiyatmi</option>
                                            <option value="Nisa">Nisa</option>
                                            <option value="Nia">Nia</option>
                                            <option value="Anam">Anam</option>
                                            <option value="Purwono">Purwono</option>
                                            <option value="Surip">Surip</option>
                                            <option value="Agung">Agung</option>
                                        </select>
                                        <small class="validasi" style="display: none;"><div class="error-message"><i class="bi bi-exclamation-triangle-fill"></i> Diambil oleh harus isi!</div></small>
                                    </div>
                                    <!-- Untuk tampilan di kirim -->
                                    <div class="mb-3 animate__animated animate__flipInX" id="dikirimOleh" style="display:none;">
                                        <label class="fw-bold mb-3">Dikirim Oleh</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="dikirim_oleh" id="kurir_internal" value="Kurir Internal" data-type="radio">
                                            <label class="form-check-label" for="kurir_internal">Kurir Internal Perusahaan</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="dikirim_oleh" id="kurir_ekspedisi" value="Ekspedisi" data-type="radio">
                                            <label class="form-check-label" for="kurir_internal">Ekspedisi</label>
                                        </div>
                                        <small class="validasi" style="display: none;"><div class="error-message"><i class="bi bi-exclamation-triangle-fill"></i> Dikirim oleh harus dipilih!</div></small>
                                    </div>
                                    <!-- Untuk tampilan kurir internal -->
                                    <div class="mb-3 animate__animated animate__flipInX" id="kurirInternal" style="display: none;">
                                        <label class="fw-bold">Nama Kurir Pengirim</label>
                                        <input type="text" class="form-control" name="nama_kurir_pengirim" id="kurir_internal_input" data-type="text">
                                        <small class="validasi" style="display: none;"><div class="error-message"><i class="bi bi-exclamation-triangle-fill"></i> Nominal Ongkos kirim harus diisi!</div></small>
                                    </div>
                                    <!-- Untuk tampilan ekspedisi -->
                                    <div class="mb-3" id="ekspedisi" style="display:none;">
                                        <div lass="mb-3">
                                            <label class="fw-bold">Pilih Ekspedisi</label>
                                            <select name="ekspedisi" id="pilihEkspedisi" class="selectize-js-2" data-type="select">
                                                <option value="">Pilih...</option>
                                                <?php
                                               
                                                $sql_ekspedisi = mysqli_query($connect, "SELECT * FROM ekspedisi");
                                                while ($data_ekspedisi = mysqli_fetch_array($sql_ekspedisi)) {
                                                ?>
                                                    <option value="<?php echo $data_ekspedisi['id_ekspedisi'] ?>"><?php echo $data_ekspedisi['nama_ekspedisi'] ?></option>
                                                <?php } ?>
                                            </select>
                                            <small class="validasi" style="display: none;"><div class="error-message"><i class="bi bi-exclamation-triangle-fill"></i> Ekspedisi harus dipilih!</div></small>
                                        </div>
                                    </div>
                                    <div class="mb-3 animate__animated animate__flipInX" id="jenisOngkir" style="display:none;">
                                        <label class="fw-bold mb-3">Jenis Ongkir</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenis_ongkir" id="dengan_ongkir" value="1" data-type="radio">
                                            <label class="form-check-label" for="dengan_ongkir">Dengan Ongkir</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenis_ongkir" id="tanpa_ongkir" value="0" data-type="radio">
                                            <label class="form-check-label" for="tanpa_ongkir">Tanpa Ongkir</label>
                                        </div>
                                        <small class="validasi" style="display: none;"><div class="error-message"><i class="bi bi-exclamation-triangle-fill"></i> Jenis Ongkir harus dipilih!</div></small>
                                    </div> 
                                    <div class="mb-3 animate__animated animate__flipInX" id="ongkir" style="display: none;">
                                        <div class="row">
                                            <label class="fw-bold">Nominal Ongkir</label>
                                            <div class="col-sm-7">
                                                <input type="text" name="ongkir" id="ongkos_kirim" class="form-control" data-type="number">
                                            </div>
                                            <div class="col-sm-5">
                                                <div class="form-check mt-1">
                                                    <input class="form-check-input" name="free_ongkir" type="checkbox" value="1" id="free_ongkir">
                                                    <label class="form-check-label" for="free_ongkir">
                                                        Free Ongkir
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="validasi" style="display: none;"><div class="error-message"><i class="bi bi-exclamation-triangle-fill"></i> Nominal Ongkos kirim harus diisi!</div></small>
                                    </div> 
                                    <div class="mb-3 animate__animated animate__jackInTheBox" id="tgl_kirim" style="display: none;">
                                        <label class="fw-bold">Tanggal</label>
                                        <input type="text" name="tanggal" style="background-color:white;" class="bg-white form-control" id="date" data-type="tanggal">
                                        <small class="validasi" style="display: none;"><div class="error-message"><i class="bi bi-exclamation-triangle-fill"></i> Tanggal kirim harus diisi!</div></small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary" name="simpan-pengiriman" id="dikirim"><i class="bi bi-save"></i> Simpan Data</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDikirim"><i class="bi bi-x-circle"> Cancel</i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>  
                <script>
                    // Assuming you have a reference to your button element
                    var cancelDikirimButton = document.getElementById('cancelDikirim');

                    // Add a click event listener to the button
                    cancelDikirimButton.addEventListener('click', function () {
                        // Reload the page when the button is clicked
                        location.reload();
                    });
                    document.addEventListener('DOMContentLoaded', function () {
                        // Kode untuk form validasi
                        var inputs = document.querySelectorAll('[data-type="tanggal"], [data-type="text"], [data-type="number"]');
                        var selects = document.querySelectorAll('[data-type="select"]');
                        var radios = document.querySelectorAll('[data-type="radio"]');
                        var validasiElements = document.querySelectorAll('.validasi');

                        // Inisialisasi flatpicker pada input text
                        var textInputs = document.querySelectorAll('[data-type="tanggal"]');
                        textInputs.forEach(function (input) {
                            flatpickr(input, {
                                enableTime: false,
                                dateFormat: "d/m/Y",
                            });
                        });

                        // Menambahkan event listener pada form saat disubmit
                        document.getElementById('myForm').addEventListener('submit', function (event) {
                            var visibleElements = document.querySelectorAll('.mb-3[style*="display: block;"]');
                            var isValidationNeeded = true;

                            // Cek apakah ada elemen yang sedang ditampilkan
                            if (visibleElements.length > 0) {
                                // Lakukan validasi hanya jika ada elemen yang ditampilkan
                                validateElements(inputs, visibleElements);
                                validateElements(selects, visibleElements);
                                validateElements(radios, visibleElements);
                            }
                        });

                        function validateElements(elements, visibleElements) {
                            elements.forEach(function (element) {
                                var parentDiv = element.closest('.mb-3');
                                var validationElement = parentDiv.querySelector('.validasi');

                                // Periksa apakah elemen tersebut ada di antara elemen yang sedang ditampilkan
                                var isVisible = Array.from(visibleElements).includes(parentDiv);

                                if (isVisible) {
                                    if (element.tagName.toLowerCase() === 'select' && (element.classList.contains('selectize-js') || element.classList.contains('selectize-js-2'))) {
                                        // Jika itu elemen selectize.js
                                        var selectizeInput = parentDiv.querySelector('.selectize-input');

                                        if (!element.value) {
                                            event.preventDefault();
                                            validationElement.style.display = 'block';
                                            validationElement.classList.add('error');
                                            selectizeInput.classList.add('error');
                                        } else {
                                            validationElement.style.display = 'none';
                                            validationElement.classList.remove('error');
                                            selectizeInput.classList.remove('error');
                                        }
                                    } else if (element.type === 'radio') {
                                        // Jika itu elemen radio
                                        var radioGroupName = element.name;
                                        var checkedRadios = document.querySelectorAll('input[name="' + radioGroupName + '"]:checked');

                                        if (checkedRadios.length === 0) {
                                            event.preventDefault();
                                            validationElement.style.display = 'block';
                                            validationElement.classList.add('error');
                                        } else {
                                            validationElement.style.display = 'none';
                                            validationElement.classList.remove('error');
                                        }
                                    } else {
                                        // Jika itu elemen biasa
                                        if (!element.value) {
                                            event.preventDefault();
                                            validationElement.style.display = 'block';
                                            validationElement.classList.add('error');
                                            element.classList.add('error');
                                        } else {
                                            validationElement.style.display = 'none';
                                            validationElement.classList.remove('error');
                                            element.classList.remove('error');
                                        }
                                    }
                                }
                            });
                        }
                        // Kode untuk style display none or block and required false or true
                        var radioJenisPengiriman = document.getElementsByName('jenis_pengiriman');
                        var radioDikirimOleh = document.getElementsByName('dikirim_oleh');
                        var radioJenisOngkir = document.getElementsByName('jenis_ongkir');
                        var divDiambil = document.getElementById('diambilOleh');
                        var selectDiambil = document.getElementById('diambil_oleh');
                        var divDikirimOleh = document.getElementById('dikirimOleh');
                        var divTglKirim = document.getElementById('tgl_kirim');
                        var divEkspedisi = document.getElementById('ekspedisi');
                        var divJenisOngkir = document.getElementById('jenisOngkir');
                        var divOngkir = document.getElementById('ongkir');
                        var ongkosKirim = document.getElementById('ongkos_kirim');
                        var divKurirInternal = document.getElementById('kurirInternal');
                        var pilihEkspedisi = document.getElementById('pilihEkspedisi');
                        var inputKurirInternal = document.getElementById('kurir_internal_input');
                        var divKurirInternal = document.getElementById('kurirInternal');
                        var inputTglKirim = document.getElementsByName("tanggal")[0];
                        var freeOngkir = document.getElementById('free_ongkir');
                       

                        // Menambahkan event listener untuk input edit_ongkir
                        ongkosKirim.addEventListener("input", function () {
                            // Menghapus karakter selain angka dan koma
                            var formattedValue = ongkosKirim.value.replace(/[^\d,]/g, '');

                            // Memastikan nilai tidak melebihi 100 juta
                            var numericValue = Number(formattedValue.replace(/,/g, ''));
                            if (numericValue > 1000000000) {
                                numericValue = 1000000000;
                            }

                            // Memformat nilai ke dalam format angka yang diinginkan
                            formattedValue = numericValue.toLocaleString();

                            // Menetapkan nilai yang diformat ke input
                            ongkosKirim.value = formattedValue;
                        });
                        

                        radioJenisPengiriman.forEach(function (radio) {
                            radio.addEventListener('change', function () {
                                if (radio.checked) {
                                    var selectedValue = radio.value;
                                    if (selectedValue == 'Diambil') {
                                        divDiambil.style.display = 'block';
                                        divTglKirim.style.display = 'block';
                                        divDikirimOleh.style.display = 'none';
                                        // Menghapus atribut 'required' dari setiap elemen radioDikirimOleh
                                        // Mengubah properti checked dari semua radioDikirimOleh menjadi false
                                        radioDikirimOleh.forEach(function (radioDikirim) {
                                            radioDikirim.checked = false;
                                        });

                                        divEkspedisi.style.display = 'none';
                                        divJenisOngkir.style.display = 'none';
                                        inputTglKirim.value = "";
                                        divKurirInternal.style.display = 'none';
                                        divOngkir.style.display = 'none';
                                        ongkosKirim.value = '';
                                    } else if(selectedValue == 'Dikirim'){
                                        divDiambil.style.display = 'none';
                                        divTglKirim.style.display = 'block';
                                        divDikirimOleh.style.display = 'block';
                                        selectDiambil.selectize.clear();
                                        inputTglKirim.value = "";
                                        divKurirInternal.style.display = 'none';
                                        divOngkir.style.display = 'none';
                                        ongkosKirim.value = '';
                                    }
                                }
                            });
                        });

                        radioDikirimOleh.forEach(function (radioDikirim) {
                            radioDikirim.addEventListener('change', function () {
                                if (radioDikirim.checked) {
                                    var selectedValueDikirim = radioDikirim.value;
                                    if (selectedValueDikirim == 'Kurir Internal') {
                                        divEkspedisi.style.display = 'none';
                                        divJenisOngkir.style.display = 'none';
                                        // Mengubah properti checked dari semua radioDikirimOleh menjadi false
                                        radioJenisOngkir.forEach(function (jenisOngkir) {
                                            jenisOngkir.checked = false;
                                        });
                                        // Use the clear method from Selectize to reset the selected value
                                        pilihEkspedisi.selectize.clear();
                                        divKurirInternal.style.display = 'block';
                                        divJenisOngkir.style.display = 'block';
                                        divOngkir.style.display = 'none';
                                        inputTglKirim.value = "";
                                        freeOngkir.checked = false;
                                        ongkosKirim.value = '';
                                    } else if(selectedValueDikirim == 'Ekspedisi'){
                                        divEkspedisi.style.display = 'block';
                                        divJenisOngkir.style.display = 'block';
                                        inputKurirInternal.value = '';
                                        divKurirInternal.style.display = 'none';
                                        inputTglKirim.value = "";
                                        freeOngkir.checked = false;
                                         // Mengubah properti checked dari semua radioDikirimOleh menjadi false
                                         radioJenisOngkir.forEach(function (jenisOngkir) {
                                            jenisOngkir.checked = false;
                                        });
                                        divOngkir.style.display = 'none';
                                        ongkosKirim.value = '';
                                    } 
                                } else {
                                    divKurirInternal.style.display = 'none';
                                    divOngkir.style.display = 'none';
                                    ongkosKirim.value = '';
                                }
                            });
                        });
                        radioJenisOngkir.forEach(function (jenisOngkir) {
                            jenisOngkir.addEventListener('change', function () {
                                if (jenisOngkir.checked) {
                                    var selectedValueJenisOngkir = jenisOngkir.value;
                                    if (selectedValueJenisOngkir == '1') {
                                        divOngkir.style.display = 'block';
                                        ongkosKirim.value = '';
                                    } else if(selectedValueJenisOngkir == '0'){
                                        divOngkir.style.display = 'none';
                                        freeOngkir.checked = false;
                                        ongkosKirim.value = '';
                                    }
                                }
                            });
                        });
                    });
                </script>
            </div>
            <!-- End Modal Jenis pengiriman -->
            <!-- modal edit jenis pengiriman -->
            <div class="modal fade" id="editJenisPengiriman" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Jenis Pengiriman</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <b>Apakah anda yakin ingin merubah jenis pengiriman saat ini?</b>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <a href="proses/status-kirim-pembelian.php?edit-pengiriman=<?php echo base64_encode($id_inv_pembelian); ?>" class="btn btn-danger">Ya, saya yakin</a>
                    </div>
                    </div>
                </div>
            </div>
            <!--Modal Edit Produk -->
            <div class="modal fade" id="editProduk" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Produk</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="proses/produk-pembelian.php" method="POST">
                                <input type="hidden" id="idTrxValue" name="id_trx" class="form-control">
                                <input type="hidden" id="idInv" name="id_inv" class="form-control" value="<?php echo $id_inv_pembelian ?>">
                                <div class="mb-3">
                                    <label class="text-start">Nama Produk</label>
                                    <input type="text" id="namaTmpValue" name="nama_produk_edit" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="text-start">Merk Produk</label>
                                    <input type="text" id="merkTmpValue" class="form-control bg-light" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="text-start">Harga</label>
                                    <input type="text" id="hargaTmpValue" name="harga_edit" class="form-control harga-produk-edit" maxlength="25">
                                </div>
                                <?php  
                                    if($jenis_disc == "Diskon Satuan"){
                                        ?>
                                            <div class="mb-3">
                                                <label class="text-start">Diskon</label>
                                                <input type="text" id="discTmpValue" name="disc_edit" class="form-control">
                                            </div>
                                        <?php
                                    } else {
                                        ?>
                                            <div class="mb-3">
                                                <label class="text-start">Diskon</label>
                                                <input type="text" id="discTmpValue" name="disc_edit" class="form-control bg-light" readonly>
                                            </div>
                                        <?php
                                    }
                                ?>
                                <div class="mb-3">
                                    <label class="text-start">Qty</label>
                                    <input type="text" id="qtyTmpValue" name="qty_edit" class="form-control">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary" id="edit" name="edit">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <?php include "page/script.php" ?>
    <!-- Datatable Custom -->
    <script src="../assets/js/datatable-custom-noexport.js"></script>
</body>

</html>
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

<script>
    function refreshPage() {
        location.reload();
    }
</script>

<script>
    $(document).on('click', '.pilih-produk', function(event) {
        event.preventDefault();
        event.stopPropagation();

        // Tampilkan indikator proses saat tombol diklik
        $('#loading-indicator').show();

        // Tambahkan kelas blur pada tabel
        $('table').addClass('blur');

        var id = $(this).data('id');
        var inv = $(this).data('inv');

        saveData(id, inv);
    });

    function saveData(id, inv) {
        // Periksa apakah tombol sudah diklik sebelumnya
        if ($('button[data-id="' + id + '"]').prop('hidden')) {
            console.log('Tombol sudah diklik sebelumnya.');
            return; // Jangan lakukan apa-apa jika tombol sudah diklik sebelumnya
        }

        // Nonaktifkan tombol yang dipilih segera setelah diklik
        $('button[data-id="' + id + '"]').prop('hidden', 'true');

        // Tampilkan indikator proses saat permintaan AJAX dimulai
        $('#loading-indicator').show();

        // Tambahkan kelas blur pada tabel
        $('table').addClass('blur');

        // Tampilkan console log untuk id dan inv sebelum mengirim data
        console.log('Mengirim data dengan ID:', id, 'dan INV:', inv);

        $.ajax({
            url: 'simpan-data-pembelian.php',
            type: 'POST',
            data: {
                id: id,
                inv: inv
            },
            timeout: 7000, // Set timeout ke 7 detik
            success: function(response) {
                console.log('Respons dari server:', response);

                // Periksa respons dan lakukan operasi sesuai kebutuhan
                if (response.trim() === 'Data berhasil disimpan.') {
                    // Berikan jeda waktu 5 detik sebelum menonaktifkan tombol
                    setTimeout(function() {
                        // Sembunyikan indikator proses setelah selesai jeda waktu
                        $('#loading-indicator').hide();

                        // Hilangkan kelas blur dari tabel setelah menonaktifkan tombol
                        $('table').removeClass('blur');
                    }, 5000); // Jeda waktu dalam milidetik (5 detik = 5000 milidetik)
                } else {
                    // Tampilkan pesan kesalahan atau lakukan tindakan yang sesuai
                    console.error('Terjadi kesalahan saat menyimpan data:', response);

                    // Sembunyikan indikator proses jika terjadi kesalahan
                    $('#loading-indicator').hide();

                    // Hilangkan kelas blur dari tabel jika terjadi kesalahan
                    $('table').removeClass('blur');
                }
            },
            error: function(xhr, status, error) {
                if (status === 'timeout') {
                    console.error('Koneksi timeout setelah 7 detik.');
                    // Tindakan yang perlu diambil jika koneksi timeout
                } else {
                    console.error('Terjadi kesalahan saat menyimpan data:', error);
                }

                // Sembunyikan indikator proses jika terjadi kesalahan atau timeout
                $('#loading-indicator').hide();

                // Hilangkan kelas blur dari tabel jika terjadi kesalahan atau timeout
                $('table').removeClass('blur');
            },
            complete: function() {
                // Sembunyikan indikator proses setelah selesai
                $('#loading-indicator').hide();
            }
        });
    }
</script>

<!-- Kode Untuk Qty   -->
<script>
    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function formatInputValue(value) {
        return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function checkStock(inputId) {
        var stockElement = document.getElementById('stock_' + inputId);
        var qtyInput = document.getElementById('qtyInput_' + inputId);

        var stock = parseInt(stockElement.value.replace(/,/g, ''), 10);
        var qty = parseInt(qtyInput.value.replace(/,/g, ''), 10);

        // Handle NaN by defaulting to 1
        qty = isNaN(qty) ? 1 : Math.max(1, qty);

        qtyInput.value = formatNumber(qty);

        // if (qty > stock) {
        //     qtyInput.value = formatNumber(stock);
        // }
    }

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
<script>
    // Mendapatkan referensi elemen input
    var hargaProdukInputs = document.querySelectorAll('.harga_produk');

    // Menambahkan event listener untuk memformat angka saat nilai berubah
    hargaProdukInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            formatNumberHarga(input);
        });
    });

    // Fungsi untuk memformat angka dengan pemisah ribuan
    function formatNumberHarga(input) {
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

<!-- Edit Data -->
<script>
    $(document).ready(function () {
        // Function to format harga or qty with a comma as a thousand separator
        function formatNumber(number) {
            // Check if the number is a string
            if (typeof number !== 'string') {
                number = number.toString();
            }

            // Remove non-numeric characters and existing commas before formatting
            var numberWithoutCommas = number.replace(/[^0-9]/g, '');

            return numberWithoutCommas.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Function to handle button click event
        $('.btn-edit').on('click', function () {
            // Get data attributes from the button
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var merk = $(this).data('merk');
            var harga = $(this).data('harga');
            var disc = $(this).data('disc');
            var qty = $(this).data('qty');

            // Set values in the modal
            $('#idTrxValue').val(id);
            $('#namaTmpValue').val(nama);
            $('#merkTmpValue').val(merk);
            $('#hargaTmpValue').val(formatNumber(harga));
            $('#discTmpValue').val(disc);
            $('#qtyTmpValue').val(formatNumber(qty));

            // Show the modal
            $('#editProduk').modal('show');

            // Add event listener for harga input field
            $('#hargaTmpValue').on('input', function () {
                // Format the harga value with a comma as a thousand separator
                var formattedHarga = formatNumber($(this).val());

                // Set the formatted harga value in the input field
                $(this).val(formattedHarga);
            });

            // Add event listener for qty input field
            $('#qtyTmpValue').on('input', function () {
                // Format the qty value with a comma as a thousand separator
                var formattedQty = formatNumber($(this).val());

                // Set the formatted qty value in the input field
                $(this).val(formattedQty);
            });

            // Add input validation to allow only numeric input for qty
            $('#qtyTmpValue').on('keypress', function (event) {
                var charCode = event.which;
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    event.preventDefault();
                }
            });
        });
    });
</script>

<!-- date picker with flatpick -->
<script type="text/javascript">
    flatpickr("#dateTempo", {
        dateFormat: "d/m/Y",
    });
</script>
<!-- end date picker -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var resetButton = document.getElementById("resetTempo");
        var inputTanggalTempo = document.getElementById("dateTempo");

        // Mendengarkan klik pada tombol resetTempo
        resetButton.addEventListener("click", function () {
            // Mereset nilai input tanggal_tempo menjadi kosong
            inputTanggalTempo.value = "";
        });
    });
</script>

<script>
    // Menambahkan event listener untuk setiap radio button
    var radioButtons = document.getElementsByName('jenis_diskon');
    var spDisc = document.getElementById('sp_disc');

    radioButtons.forEach(function(radioButton) {
        radioButton.addEventListener('change', function() {
            // Menampilkan value ke dalam console log saat radio button diubah
            console.log('Jenis Diskon yang dipilih:', this.value);
            if(this.value == 'Spesial Diskon'){
                spDisc.style.display = 'block';
            } else if (this.value == 'Diskon Satuan'){
                spDisc.style.display = 'none';
            } else if (this.value == 'Tanpa Diskon'){
                spDisc.style.display = 'none';
            }
        });
    });
</script>

<script>
    var totalInvoice = "<?php echo $total_pembelian ?>";
    var formattedTotalInvoice = formatNumberInd(totalInvoice);
    $('#totalInv').text(formattedTotalInvoice);
</script>

<!-- Kode untuk kondisi button tambah produk -->
<script>
    var statusDiterima = "<?php echo $status_pembelian; ?>";
    var addButton =  document.getElementById('addProduk');
    var editButton =  document.getElementById('editProduk');
    if(statusDiterima != 1){
        addButton.style.display= "block";
        editButton.style.display= "none";
    } else {
        addButton.style.display= "none";
        editButton.style.display= "block";
    }

    editButton.addEventListener('click', function() {
        addButton.style.display = "block";
        editButton.style.display= "none";
    });
</script>