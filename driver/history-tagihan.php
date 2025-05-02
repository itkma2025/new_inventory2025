<?php
require_once "../akses.php";
$page = 'hist-tagihan';
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
                                        <td class="text-center p-3 col-2 text-nowrap">No Tagihan</td>
                                        <td class="text-center p-3 col-2 text-nowrap">Tgl. Tagihan</td>
                                        <td class="text-center p-3 col-2 text-nowrap">Nama Customer</td>
                                        <td class="text-center p-3 col-3 text-nowrap">Alamat</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $no = 1;
                                        $sql = "SELECT
                                                    COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                                                    cs.nama_cs,
                                                    cs.alamat,
                                                    bill.id_tagihan,
                                                    bill.no_tagihan, 
                                                    bill.tgl_tagihan, 
                                                    bill.total_tagihan, 
                                                    bill.id_driver,
                                                    bill.nama_penerima
                                                FROM spk_reg AS spk
                                                LEFT JOIN inv_nonppn nonppn ON (spk.id_inv = nonppn.id_inv_nonppn)
                                                LEFT JOIN inv_ppn ppn ON (spk.id_inv = ppn.id_inv_ppn)
                                                LEFT JOIN inv_bum bum ON (spk.id_inv = bum.id_inv_bum)
                                                LEFT JOIN finance fnc ON (spk.id_inv = fnc.id_inv)
                                                LEFT JOIN finance_tagihan bill ON (fnc.id_tagihan = bill.id_tagihan)
                                                LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                                WHERE id_driver = '$id_user'
                                                GROUP BY bill.no_tagihan";
                                        $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                                        while ($data = mysqli_fetch_array($query)) {   
                                            $id_tagihan = $data['id_tagihan'];
                                            $nama_penerima = $data['nama_penerima'];
                                    ?>
                                    <?php  
                                        if(!empty($nama_penerima)){
                                            ?>
                                    <tr>
                                        <td class="text-center text-nowrap"><?php echo $no ?></td>
                                        <td class="text-nowrap text-center"><?php echo $data['no_tagihan'] ?></td>
                                        <td class="text-nowrap text-center"><?php echo $data['tgl_tagihan'] ?></td>
                                        <td class="text-nowrap"><?php echo $data['nama_cs'] ?></td>
                                        <td class="text-nowrap-mobille wrap-text"><?php echo $data['alamat'] ?></td>
                                    </tr>
                                    <?php $no++; ?>
                                    <?php
                                        }
                                    
                                    ?>
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