<?php
require_once "akses.php";
$page = 'br-masuk';
$page2 = 'br-masuk-import';
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">

    <style>
        #table2 {
            cursor: pointer;
        }

        #table3 {
            cursor: pointer;
        }

        input[type="text"]:read-only {
            background: #e9ecef;
        }

        textarea[type="text"]:read-only {
            background: #e9ecef;
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
        <section>
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) {
                                                        echo $_SESSION['info'];
                                                    }
                                                    unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body mt-3">
                        <div class="text-start" style="margin-left: 12px;">
                            <?php $id_inv = $_GET['id_inv']; ?>
                            <a href="tampil-br-import.php?id=<?php echo $id_inv ?>" class="btn btn-secondary"><i class="bi bi-arrow-left-square-fill" style="color: white; font-size: 18px;"></i> Kembali</a>
                        </div>
                        <h3 class="text-center">Details Barang Diterima </h3>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-striped" id="table1">
                                <thead>
                                    <tr class="text-white" style="background-color: navy;">
                                        <td class="text-center p-3 text-nowrap">No</td>
                                        <td class="text-center p-3 text-nowrap">Nama Barang</td>
                                        <td class="text-center p-3 text-nowrap">Merk</td>
                                        <td class="text-center p-3 text-nowrap">Harga</td>
                                        <td class="text-center p-3 text-nowrap">Qty</td>
                                        <td class="text-center p-3 text-nowrap">Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $id = decrypt($_GET['id'], $key_global);
                                    $no = 1;
                                    $sql = "SELECT 
                                                act.id_act_br_import,
                                                act.id_isi_inv_br_import,
                                                SUM(act.qty_act) AS total_qty_act, 
                                                iibi.id_isi_inv_br_import, 
                                                iibi.id_inv_br_import,
                                                pr.nama_produk, 
                                                mr.nama_merk
                                            FROM act_br_import AS act
                                            LEFT JOIN isi_inv_br_import iibi ON (act.id_isi_inv_br_import = iibi.id_isi_inv_br_import)
                                            LEFT JOIN tb_produk_reguler pr ON (act.id_produk_reg = pr.id_produk_reg)
                                            LEFT JOIN tb_merk mr ON (pr.id_merk = mr.id_merk)
                                            WHERE act.id_isi_inv_br_import ='$id' GROUP BY act.id_produk_reg";
                                    $query = mysqli_query($connect, $sql);
                                    while ($data = mysqli_fetch_array($query)) {
                                    ?>
                                        <tr>
                                            <td class="text-nowrap text-center"><?php echo $no; ?></td>
                                            <td class="text-nowrap"><?php echo $data['nama_produk']; ?></td>
                                            <td class="text-nowrap"><?php echo $data['nama_merk']; ?></td>
                                            <td class="text-nowrap"><?php echo $data['harga']; ?></td>
                                            <td class="text-nowrap text-end"><?php echo number_format($data['total_qty_act'], 0, '.', '.'); ?></td>
                                            <td class="text-nowrap text-center">
                                                <a href="edit-act-br-import.php?edit-act=<?php echo encrypt($data['id_act_br_import'], $key_global) ?> && id=<?php echo encrypt($data['id_isi_inv_br_import'], $key_global); ?> && id_inv=<?php echo encrypt($data['id_inv_br_import'], $key_global) ?>" class="btn btn-warning btn-sm rounded"><i class="bi bi-pencil" style="font-size: 14px;"></i></a>
                                                <a href="proses/proses-br-in-import.php?hapus-act=<?php echo encrypt($data['id_act_br_import'], $key_global) ?> && id=<?php echo encrypt($data['id_isi_inv_br_import'], $key_global); ?> && id_inv=<?php echo encrypt($data['id_inv_br_import'], $key_global) ?>" class="btn btn-danger delete-data btn-sm rounded"><i class="bi bi-trash" style="font-size: 14px;"></i></a>
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
        </section>
    </main><!-- End #main -->
    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>

</html>