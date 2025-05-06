<?php
require_once '../akses.php';
$page = 'inv';
$page2 = 'list-inv';
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
        <!-- <div class="loader loader">
            <div class="loading">
                <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div> -->
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
                            date_default_timezone_set('Asia/Jakarta');
                            $nama_user = $_SESSION['tiket_nama'];
                            $date = date('d-m-Y');
                            ?>
                            <p><b>Nama Driver : <?php echo $_SESSION['tiket_nama'] ?></b></p>
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
                                        <td class="text-center p-3 col-2 text-nowrap">Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    date_default_timezone_set('Asia/Jakarta');
                                    include "koneksi.php";
                                    $no = 1;
                                    $sql = "SELECT subquery.*,
                                                    tb_customer.nama_cs,
                                                    tb_customer.alamat,
                                                    user.nama_user
                                            FROM (
                                            SELECT status_kirim.*,
                                                    inv_nonppn.no_inv AS no_inv_nonppn,
                                                    inv_ppn.no_inv AS no_inv_ppn, 
                                                    inv_bum.no_inv AS no_inv_bum,
                                                    inv_nonppn.status_transaksi AS status_trx_nonppn,
                                                    inv_ppn.status_transaksi AS status_trx_ppn,
                                                    inv_bum.status_transaksi AS status_trx_bum,
                                                    spk_reg.id_inv AS id_inv_spk,
                                                    spk_reg.id_customer,
                                                    spk_reg.tgl_pesanan
                                            FROM status_kirim 
                                            LEFT JOIN inv_nonppn ON status_kirim.id_inv = inv_nonppn.id_inv_nonppn 
                                            LEFT JOIN inv_ppn ON status_kirim.id_inv = inv_ppn.id_inv_ppn 
                                            LEFT JOIN inv_bum ON status_kirim.id_inv = inv_bum.id_inv_bum
                                            LEFT JOIN spk_reg ON status_kirim.id_inv = spk_reg.id_inv
                                            ) AS subquery
                                            LEFT JOIN tb_customer ON subquery.id_customer = tb_customer.id_cs
                                            LEFT JOIN user ON subquery.dikirim_driver = user.id_user
                                            WHERE user.nama_user = 'Teguh Pambudi' AND subquery.status_trx_nonppn = 'Dikirim' AND subquery.status_trx_ppn = 'Dikirim' AND subquery.status_trx_bum = 'Dikirim'";
                                    $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                                    while ($data = mysqli_fetch_array($query)) {
                                    ?>
                                        <tr>
                                            <td class="text-center text-nowrap"><?php echo $no ?></td>
                                            <td class="text-nowrap text-center">
                                                <?php
                                                if (!empty($data['no_inv_nonppn'])) {
                                                    echo $data['no_inv_nonppn'];
                                                } elseif (!empty($data['no_inv_ppn'])) {
                                                    echo $data['no_inv_ppn'];
                                                } elseif (!empty($data['no_inv_bum'])) {
                                                    echo $data['no_inv_bum'];
                                                }
                                                ?>
                                            </td>
                                            <td class="text-nowrap text-center"><?php echo $data['tgl_pesanan'] ?></td>
                                            <td class="text-nowrap"><?php echo $data['nama_cs'] ?></td>
                                            <td class="text-nowrap-mobille"><?php echo $data['alamat'] ?></td>
                                            <td class="text-center text-nowrap">
                                                <?php
                                                if ($data['jenis_inv'] == 'nonppn') {
                                                    echo '<a href="tampil-isi-list-inv-nonppn.php?id=' . base64_encode($data['id_inv']) . '" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i> Lihat Data</a>';
                                                } elseif ($data['jenis_inv'] == 'ppn') {
                                                    echo '<a href="tampil-isi-list-inv-ppn.php?id=' . base64_encode($data['id_inv']) . '" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i> Lihat Data</a>';
                                                } elseif ($data['jenis_inv'] == 'bum') {
                                                    echo '<a href="tampil-isi-list-inv-bum.php?id=' . base64_encode($data['id_inv']) . '" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i> Lihat Data</a>';
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
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>

</html>