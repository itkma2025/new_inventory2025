<?php
$page = 'br-masuk';
$page2 = 'br-masuk-reg';
include "akses.php";
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
                    <div class="card-body">
                        <h5 class="text-center mt-3">Data Barang Masuk Lokal</h5>
                        <a href="barang-masuk-reg.php" class="btn btn-md btn-secondary text-end"><i class="bi bi-arrow-left"></i> Kembali</a>
                        <a href="input-inv-br-in-lokal.php" class="btn btn-primary btn-md"><i class="bi bi-plus-circle"></i> Tambah Data</a>
                        <div class="table-responsive pt-3">
                            <table class="table table-striped table-bordered" id="table1">
                                <thead>
                                    <tr class="text-white" style="background-color: navy;">
                                        <td class="text-center p-3" style="width: 30px">No</td>
                                        <td class="text-center p-3" style="width: 100px">No. Invoice</td>
                                        <td class="text-center p-3" style="width: 200px">Supplier</td>
                                        <td class="text-center p-3" style="width: 100px">Tgl. Invoice</td>
                                        <td class="text-center p-3" style="width: 100px">Total Pembelian</td>
                                        <td class="text-center p-3" style="width: 50px">Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    include "koneksi.php";
                                    $sql = "SELECT ibil.*, ibil.created_date AS created, us.*, sp.*
                                            FROM inv_br_in_lokal AS ibil
                                            LEFT JOIN user us ON (ibil.id_user = us.id_user)
                                            LEFT JOIN tb_supplier sp ON (ibil.id_sp = sp.id_sp)";
                                    $query = mysqli_query($connect, $sql);
                                    while ($data = mysqli_fetch_array($query)) {
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no ?></td>
                                            <td class="text-center"><?php echo $data['no_inv'] ?></td>
                                            <td><?php echo $data['nama_sp'] ?></td>
                                            <td class="text-center"><?php echo $data['tgl_inv'] ?></td>
                                            <td class="text-end"><?php echo number_format($data['total_pembelian']) ?></td>
                                            <td class="text-center">
                                                <a href="list-br-in-lokal.php?id=<?php echo base64_encode($data['id_inv_br_in_lokal']) ?>" class="btn btn-primary btn-sm rounded"><i class="bi bi-eye"></i></a>
                                                <a href="edit-br-in-lokal.php?id=<?php echo base64_encode($data['id_inv_br_in_lokal']) ?>" class="btn btn-warning btn-sm rounded"><i class="bi bi-pencil" style="font-size: 14px;"></i></a>
                                                <a href="proses/proses-br-in-lokal.php?id=<?php echo base64_encode($data['id_inv_br_in_lokal']) ?>" class="btn btn-danger btn-sm rounded delete-data"><i class="bi bi-trash" style="font-size: 14px;"></i></a>
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
        </section>
    </main><!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>

</html>