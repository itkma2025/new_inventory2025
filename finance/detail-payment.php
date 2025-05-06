<?php
    $page = 'list-tagihan';
    $page2 = 'tagihan-pembelian';
    require_once "../akses.php";
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
    <link href="../assets/vendor/lightbox/dist/css/lightgallery.css" rel="stylesheet" />

    <style type="text/css">
        .img-fluid {
            height: 48vh !important;
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
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include "page/sidebar.php"; ?>
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
            <div class="card">
                <div class="card-header text-center">
                        <h5><strong>DETAIL PEMBAYARAN</strong></h5>
                </div>
                <div class="card-body">
                    <?php  
                        include "query/detail-payment.php";
                        $detail = mysqli_fetch_array($connect->query($sql_inv_pembelian));
                        $id_supplier = $detail['id_sp'];
                        $query_tagihan = $connect->query($sql_inv_pembelian);
                        $total_bayar = 0;
                        while($detail_tagihan = mysqli_fetch_array($query_tagihan)){
                            $total_tagihan = $detail_tagihan['total_tagihan'];
                            $total_bayar += $detail_tagihan['total_bayar'];
                            $sisa_tagihan = $total_tagihan - $total_bayar;
                        }
                        
                    ?>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>No. Pembayaran</label>
                            <input type="text" class="form-control" value="<?php echo $detail['no_pembayaran']; ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tgl. Pembayaran</label>
                            <input type="text" class="form-control" value="<?php echo $detail['tgl_pembayaran']; ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Nama Supplier</label>
                            <input type="text" class="form-control" value="<?php echo $detail['nama_sp']; ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="card-body mt-2">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Total Tagihan</label>
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping">Rp</span>
                                    <input type="text" class="form-control text-end" value="<?php echo number_format($total_tagihan,0,',',','); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Total Bayar</label>
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping">Rp</span>
                                    <input type="text" class="form-control text-end" value="<?php echo number_format($total_bayar,0,',',','); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Total Sisa Tagihan</label>
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping">Rp</span>
                                    <input type="text" class="form-control text-end" value="<?php echo number_format($sisa_tagihan,0, ',', ',')  ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Table data pembelian -->
                <div class="table-responsive p-3">
                    <table class="table table-striped table-bordered" id="table1">
                        <thead>
                            <tr class="text-white" style="background-color: navy;">
                                <td class="text-center text-nowrap p-3">No</td>
                                <td class="text-center text-nowrap p-3">No. Invoice</td>
                                <td class="text-center text-nowrap p-3">Jenis Invoice</td>
                                <td class="text-center text-nowrap p-3">Nama Supplier</td>
                                <td class="text-center text-nowrap p-3">Tgl. Invoice</td>
                                <td class="text-center text-nowrap p-3">Tgl. Tempo</td>
                                <td class="text-center text-nowrap p-3">Total Invoice</td>
                                <td class="text-center text-nowrap p-3">Total Pembayaran</td>
                                <td class="text-center text-nowrap p-3">Sisa Tagihan</td>
                                <td class="text-center text-nowrap p-3">Status Lunas</td>
                                <td class="text-center text-nowrap p-3">Status Tempo</td>
                                <td class="text-center text-nowrap p-3">Aksi</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  
                                include "../function/hitung-selisih-hari.php";
                                $no = 1;
                                $query_detail = $connect->query($sql_inv_pembelian);
                                while($detail_table = mysqli_fetch_array($query_detail)){
                                    $id_inv_pembelian = $detail_table['id_inv_pembelian'];
                                    $id_bayar = $detail_table['id_bayar'];
                                    $id_pembayaran = $detail_table['id_pembayaran'];
                                    $id_sp = $detail_table['id_sp'];
                                    $nama_sp = $detail_table['nama_sp'];
                                    $total_pembelian = $detail_table['total_pembelian'];
                                    $total_bayar = $detail_table['total_bayar'];
                                    $tagihan_tersisa = $total_pembelian - $total_bayar;
                                    // Kondisi status lunas
                                    $status_lunas = "";
                                    if($tagihan_tersisa == 0){
                                        $status_lunas = "Lunas";
                                    } else {
                                        $status_lunas = "Belum Lunas";
                                    }

                                    // Kondisi status tempo
                                    $date_now = date('Y-m-d');
                                    $tgl_tempo = $detail_table['tgl_tempo'];
                                    $tgl_tempo_convert = $detail_table['tgl_tempo_convert'];
                                    $hitung_selisih = hitungSelisihHari($date_now, $tgl_tempo_convert);
                                    $bg_color = '';
                                    $selisih_hari = '';
                                    if (!empty($tgl_tempo)) {
                                        if ($tgl_tempo_convert > $date_now){
                                            $bg_color = 'bg-secondary text-white';
                                            $selisih_hari = "Tempo < " . $hitung_selisih . "Hari";
                                        } else if ($tgl_tempo_convert < $date_now){
                                            $bg_color = 'bg-danger text-white';
                                            $selisih_hari = "Tempo > " . abs($hitung_selisih) . "Hari";
                                        } else if ($tgl_tempo_convert == $date_now) {
                                            $selisih_hari = "Jatuh Tempo Hari ini";
                                        } else {
                                            $selisih_hari = "Tidak Ada Tempo";
                                        }
                                    } else {
                                        $selisih_hari = "Tidak Ada Tempo";
                                    }     

                                    $jenis_inv = $detail_table['jenis_trx'];
                                    $id_inv = $detail_table['id_inv_pembelian'];
                                    $no_inv = '';
                                    if($detail_table['no_inv'] != ''){
                                        $no_inv = $detail_table['no_inv'];
                                    } else {
                                        $no_inv = "Tidak ada";
                                    }
                            ?>
                            <tr>
                                <td class="text-center nomor-urut"><?php echo $no; ?></td>
                                <td class="text-center text-nowrap"><?php echo $no_inv; ?></td>
                                <td class="text-center text-nowrap"><?php echo $detail_table['jenis_trx']; ?></td>
                                <td class="text-center text-nowrap"><?php echo $detail_table['nama_sp']; ?></td>
                                <td class="text-center text-nowrap"><?php echo $detail_table['tgl_pembelian']; ?></td>
                                <td class="text-center text-nowrap"><?php echo $detail_table['tgl_tempo']; ?></td>
                                <td class="text-end text-nowrap"><?php echo number_format($detail_table['total_pembelian'],0,'.','.'); ?></td>
                                <td class="text-end text-nowrap"><?php echo number_format($detail_table['total_bayar'],0,'.','.'); ?></td>
                                <td class="text-end text-nowrap"><?php echo number_format($tagihan_tersisa,0,'.','.'); ?></td>
                                <td class="text-center text-nowrap"><?php echo $status_lunas ?></td>
                                <td class="text-center text-nowrap <?php echo $bg_color ?>"><?php echo $selisih_hari ?></td>
                                <td class="text-center text-nowrap">
                                    <?php  
                                        if($tagihan_tersisa != 0){
                                            ?>
                                                <button type="button" class="btn btn-warning btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#sudahBayar" data-id ="<?php echo $id_inv ?>" data-jenis ="<?php echo $jenis_inv ?>" data-total ="<?php echo number_format($tagihan_tersisa, 0, '.', '.') ?>">
                                                    <i class="bi bi-cash-coin"> Bayar</i>
                                                </button>
                                                <br>
                                                <button class="btn btn-primary btn-sm view_data" data-bs-toggle="modal" data-bs-target="#history" data-id="<?php echo $id_inv_pembelian ?>"><i class="bi bi-info-circle"></i> History Payment</button>
                                            <?php
                                        } else {
                                            ?> 
                                                <button class="btn btn-secondary btn-sm mb-2"><i class="bi bi-check-circle"></i> Lunas</button>
                                                <br>
                                                <button class="btn btn-primary btn-sm view_data" data-bs-toggle="modal" data-bs-target="#history" data-id="<?php echo $id_inv_pembelian ?>"><i class="bi bi-info-circle"></i> History Payment</button>
                                            <?php
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php $no++ ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <?php include "page/script.php" ?>
        <?php include "modal/detail-payment-pembayaran.php" ?>
    </main><!-- End #main -->
    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
</body>
</html>
<?php include "modal/detail-payment-history.php" ?>
<!-- date picker with flatpick -->
<script type="text/javascript">
    flatpickr("#date", {
    dateFormat: "d/m/Y",
  });
</script>
<!-- end date picker -->
