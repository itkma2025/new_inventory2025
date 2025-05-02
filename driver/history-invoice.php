<?php
require_once "../akses.php";
$page = 'hist-inv';
include "function/class-list-inv.php";
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
    <?php include "page/head.php"; ?>
    <style>
    .text-nowrap-mobile {
        /* Gaya untuk tampilan mobile */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    @media (min-width: 768px) {
        .text-nowrap-mobile {
            /* Gaya untuk tampilan desktop */
            white-space: normal;
            overflow: visible;
            text-overflow: inherit;
            max-width: none;
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
        <!-- Loading -->
        <div class="loader loader">
            <div class="loading">
                <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div>
        <!-- ENd Loading -->
        <div class="pagetitle">
            <h1>List Invoice</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">List Invoice</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="mt-4">
                            <?php
                            $date = date('d-m-Y');
                            ?>
                            <p><b>Nama Driver : <?php echo ucfirst(decrypt($_SESSION['tiket_nama'], $key_global)); ?></b></p>
                            <p><b>Tanggal : <?php echo $date; ?></b></p>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-bordered" id="table2">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                        <td class="text-center p-3 col-1 text-nowrap">No</td>
                                        <td class="text-center p-3 col-2 text-nowrap">No Invoice</td>
                                        <td class="text-center p-3 col-2 text-nowrap">Tgl. Order</td>
                                        <td class="text-center p-3 col-2 text-nowrap">Nama Customer</td>
                                        <td class="text-center p-3 col-3 text-nowrap">Alamat</td>
                                        <td class="text-center p-3 col-3 text-nowrap">Status Transaksi</td>
                                        <td class="text-center p-3 col-2 text-nowrap">Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $sql = "SELECT 
                                                sk.id_inv,
                                                sk.jenis_inv,
                                                sk.dikirim_driver,
                                                COALESCE(spk_nonppn.tgl_pesanan, spk_ppn.tgl_pesanan, spk_bum.tgl_pesanan) AS tgl_pesanan,
                                                COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                                                COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                                COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                                                COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi) AS status_transaksi,
                                                COALESCE( cs_spk_nonppn.alamat,  cs_spk_ppn.alamat,  cs_spk_bum.alamat) AS alamat
                                            FROM status_kirim AS sk
                                            LEFT JOIN inv_nonppn nonppn ON (sk.id_inv = nonppn.id_inv_nonppn)
                                            LEFT JOIN inv_ppn ppn ON (sk.id_inv = ppn.id_inv_ppn)
                                            LEFT JOIN inv_bum bum ON (sk.id_inv = bum.id_inv_bum)
                                            LEFT JOIN spk_reg spk_nonppn ON (nonppn.id_inv_nonppn = spk_nonppn.id_inv)
                                            LEFT JOIN spk_reg spk_ppn ON (ppn.id_inv_ppn = spk_ppn.id_inv)
                                            LEFT JOIN spk_reg spk_bum ON (bum.id_inv_bum = spk_bum.id_inv)
                                            LEFT JOIN tb_customer cs_spk_nonppn ON (spk_nonppn.id_customer = cs_spk_nonppn.id_cs)
                                            LEFT JOIN tb_customer cs_spk_ppn ON (spk_ppn.id_customer = cs_spk_ppn.id_cs)
                                            LEFT JOIN tb_customer cs_spk_bum ON (spk_bum.id_customer = cs_spk_bum.id_cs)
                                            WHERE sk.dikirim_driver = '$id_user'
                                            AND (nonppn.status_transaksi != 'Dikirim' OR ppn.status_transaksi != 'Dikirim' OR bum.status_transaksi != 'Dikirim')
                                            GROUP BY no_inv";
                                    $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                                    while ($data = mysqli_fetch_array($query)) {
                                        $no_inv = $data['no_inv'];
                                        $tgl_pesanan = $data['tgl_pesanan'];
                                        $cs_inv = $data['cs_inv'];
                                        $alamat = $data['alamat'];
                                        $status_trx = $data['status_transaksi'];
                                    ?>
                                    <tr>
                                        <td class="text-center text-nowrap"><?php echo $no ?></td>
                                        <td class="text-nowrap text-center"><?php echo $no_inv ?></td>
                                        <td class="text-nowrap text-center"><?php echo $tgl_pesanan ?></td>
                                        <td class="text-nowrap"><?php echo $cs_inv ?></td>
                                        <td class="text-nowrap-mobille wrap-text"><?php echo $alamat ?></td>
                                        <td class="text-nowrap-mobille"><?php echo $status_trx ?></td>
                                        <td class="text-center text-nowrap">
                                            <?php
                                                if ($data['jenis_inv'] == 'nonppn') {
                                                    echo '<a href="tampil-history-nonppn.php?id=' . base64_encode($data['id_inv']) . '" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i> Lihat Data</a>';
                                                } elseif ($data['jenis_inv'] == 'ppn') {
                                                    echo '<a href="tampil-history-ppn.php?id=' . base64_encode($data['id_inv']) . '" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i> Lihat Data</a>';
                                                } elseif ($data['jenis_inv'] == 'bum') {
                                                    echo '<a href="tampil-history-bum.php?id=' . base64_encode($data['id_inv']) . '" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i> Lihat Data</a>';
                                                }
                                            ?>
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
        </section>
    </main><!-- End #main -->
    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>

</html>