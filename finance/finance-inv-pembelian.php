<?php
  $page = 'invoice';
  $page2  = 'pembelian';
  require_once "../akses.php";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'page/head.php'; ?>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link rel="stylesheet" type="text/css" media="all" href="daterangepicker.css" />
    <link rel="stylesheet" href="assets/css/fnc-inv-pembelian.css">
</head>

<body>
    <!-- nav header -->
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include "page/sidebar.php"; ?>
    <!-- end sidebar -->

    <div id="content">
        <main id="main" class="main">
            <!-- Loading -->
            <!-- <div class="loader loader">
                <div class="loading">
                    <img src="img/loading.gif" width="200px" height="auto">
                </div>
            </div> -->
            <!-- End Loading -->
            <div class="pagetitle">
                <h1>Data Invoice Pembelian</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">Invoice</li>
                    </ol>
                </nav>
            </div><!-- End Page Title -->
            <section>
                <!-- SWEET ALERT -->
                <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
                <!-- END SWEET ALERT -->
                <div class="card p-3">
                    <?php include "filter/fnc-inv-pembelian.php"; ?>
                    <!-- Buat Pembayaran -->
                    <div class="col-md-3">
                        <div class="p-3">
                            <form action="create-payment.php" method="GET" onsubmit="prepareFormData()">
                                <!-- <textarea type="hidden" id="idInv" name="inv_id[]" class="form-control" cols="80" rows="10"></textarea> -->
                                <input type="hidden" id="idInv" name="inv_id[]" class="form-control">
                                <button type="submit" id="createPayment" class="btn btn-primary btn-md form-control"><i class="bi bi-file-earmark-plus"></i> Buat Pembayaran</button>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <!-- End Buat Pembayaran -->
                        <table id="tableInv" class="table table-striped nowrap" style="width:100%">
                            <thead>
                                <tr class="text-white" style="background-color: navy;">
                                <td class="text-center p-3">Pilih</td>
                                <td class="text-center p-3">No</td>
                                <td class="text-center p-3">No. Transaksi Pembelian</td>
                                <td class="text-center p-3">No. Faktur Pembelian</td>
                                <td class="text-center p-3">Nama Supplier</td>
                                <td class="text-center p-3">Jenis Transaksi</td>
                                <td class="text-center p-3">Tgl. Pembelian</td>
                                <td class="text-center p-3">Tgl. Tempo</td>
                                <td class="text-center p-3">Total Tagihan</td>
                                <td class="text-center p-3">Status Tempo</td>
                                <td class="text-center p-3">Aksi</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  
                                    include "query/fnc-inv-pembelian.php";
                                    $key = 'pembelian2024';
                                    while ($data = mysqli_fetch_array($query)) {
                                        $id_inv_pembelian = $data['id_inv_pembelian'];
                                        $id_inv_pembelian_encrypt = encrypt($id_inv_pembelian, $key);
                                        // Menghapus padding '=' dari hasil enkripsi
                                        $id_inv_pembelian_encrypt = rtrim($id_inv_pembelian_encrypt, '=');
                                        // Kondisi no invoice jika kosong
                                        $no_inv = "";
                                        if($data['no_inv'] != ''){
                                            $no_inv = $data['no_inv'];
                                        } else {
                                            $no_inv = 'Tidak ada';
                                        }

                                        // Menampilkan status tempo (Selisih hari)
                                        $date_now = date('Y-m-d');
                                        $tgl_pembelian = $data['tgl_pembelian'];
                                        $tgl_pembelian_convert = $data['tgl_pembelian_convert'];
                                        $tgl_tempo = $data['tgl_tempo'];
                                        $tgl_tempo_convert = $data['tgl_tempo_convert'];

                                        // Menampilkan Tempo
                                        $tempo = "";
                                        if(!empty($tgl_tempo)){
                                            $tempo = $tgl_tempo;
                                        } else {
                                            $tempo = "Tidak Ada Tempo";
                                        }

                                        // Hitung selisih hari
                                        $selisih_hari = "";
                                        $hitung_selisih = hitungSelisihHari($date_now, $tgl_tempo_convert);
                                        $bg_color = '';
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
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <?php  
                                        if ($data['status_pembayaran'] == 0) {
                                            ?>
                                                <input type="checkbox" name="id_inv_pembelian[]"  value="<?php echo $id_inv_pembelian_encrypt ?>" data-supplier="<?php echo $data['nama_sp'] ?>" data-jenis="<?php echo $data['jenis_trx'] ?>" onclick="updateInputValue(this)">
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center text-nowrap"><?php echo $no ?></td>
                                    <td class="text-nowrap text-center"><?php echo $data['no_trx'] ?></td>
                                    <td class="text-nowrap text-center"><?php echo $no_inv; ?></td>
                                    <td class="text-nowrap"><?php echo $data['nama_sp'] ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data['jenis_trx']?></td>
                                    <td class="text-nowrap text-center"><?php echo $tgl_pembelian ?></td>
                                    <td class="text-nowrap text-center"><?php echo $tempo ?></td>
                                    <td class="text-nowrap text-end"><?php echo number_format($data['total_pembelian'])?></td>
                                    <td class="text-nowrap <?php echo $bg_color; ?>"><?php echo $selisih_hari ?></td>
                                    <td class="text-center text-nowrap">
                                        <a href="detail-produk-pembelian-lokal.php?id='<?php echo base64_encode($data['id_inv_pembelian']) ?>'&menu=inv_pembelian" class="btn btn-primary btn-sm" title="Detail Pembelian"><i class="bi bi-eye"></i></a>
                                    </td>
                                </tr>
                                <?php $no++ ?>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main><!-- End #main -->
    </div>
    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
    <script src="js/fnc-inv-pembayaran.js"></script>
</body>
</html>
