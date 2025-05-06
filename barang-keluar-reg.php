<?php
require_once "akses.php";
$page = 'br-keluar';
$page2 = 'br-keluar-reg';
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
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info'];} unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center mt-3">Data Barang Keluar Reguler</h5>
                        <?php  
                            if ($role == "Super Admin" || $role == "Manager Gudang"  || $role == "Admin Gudang") { 
                                ?>
                                    <a href="input-br-out-reg.php" class="btn btn-primary btn-md" style="margin-left: 12px;"><i class="bi bi-plus-circle"></i>
                                    Tambah Data</a>
                                <?php
                            }
                        ?>
                        <div class="table-responsive pt-3">
                            <table class="table table-striped table-bordered" id="table1">
                                <thead>
                                    <tr class="text-white" style="background-color: navy;">
                                        <td class="text-center text-nowrap p-3" style="width: 30px">No</td>
                                        <td class="text-center text-nowrap p-3" style="width: 230px">Nama Produk</td>
                                        <td class="text-center text-nowrap p-3" style="width: 80px">Merk</td>
                                        <td class="text-center text-nowrap p-3" style="width: 50px">Qty</td>
                                        <td class="text-center text-nowrap p-3" style="width: 150px">Keterangan</td>
                                        <td class="text-center text-nowrap p-3" style="width: 100px">Dibuat Oleh</td>
                                        <?php  
                                            if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Gudang") { 
                                                ?>
                                                     <td class="text-center p-3" style="width: 50px">Aksi</td>
                                                <?php
                                            }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    include "koneksi.php";
                                    $sql = "SELECT  
                                                ibor.id_isi_br_out_reg,
                                                ibor.qty,
                                                STR_TO_DATE(ibor.created_date, '%d/%m/%Y, %H:%i:%s')  AS created, 
                                                COALESCE(pr.nama_produk, ecat.nama_produk) AS nama_produk,
                                                mr.nama_merk, 
                                                ket.ket_out ,
                                                uc.nama_user AS user_created
                                            FROM isi_br_out_reg AS ibor
                                            LEFT JOIN tb_produk_reguler pr ON(ibor.id_produk_reg = pr.id_produk_reg)
                                            LEFT JOIN tb_produk_ecat ecat ON(ibor.id_produk_reg = ecat.id_produk_ecat)
                                            LEFT JOIN tb_merk mr ON(mr.id_merk = pr.id_merk OR mr.id_merk = ecat.id_merk)
                                            LEFT JOIN keterangan_out ket ON(ibor.id_ket_out = ket.id_ket_out)
                                            LEFT JOIN $database2.user AS uc ON (ibor.created_by = uc.id_user)
                                            ORDER BY created DESC";
                                    $query = mysqli_query($connect, $sql);
                                    while ($data = mysqli_fetch_array($query)) {
                                    ?>
                                    <tr>
                                        <td class="text-center text-nowrap"><?php echo $no ?></td>
                                        <td class="text-nowrap"><?php echo $data['nama_produk'] ?></td>
                                        <td class="text-center text-nowrap"><?php echo $data['nama_merk'] ?></td>
                                        <td class="text-end text-nowrap"><?php echo number_format($data['qty']) ?></td>
                                        <td class="text-nowrap"><?php echo $data['ket_out'] ?></td>
                                        <td class="text-center text-nowrap">
                                            <?php echo $data['user_created'] ?><br>
                                            (<?php echo date('d/m/Y, H:i', strtotime($data['created'])) ?>)
                                        </td>
                                        <?php  
                                            if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Gudang") { 
                                                ?>
                                                    <td class="text-center">
                                                        <a href="edit-br-out-reg.php?id=<?php echo encrypt($data['id_isi_br_out_reg'], $key_global) ?>"
                                                            class="btn btn-warning btn-sm rounded"><i class="bi bi-pencil"
                                                                style="font-size: 14px;"></i></a>
                                                        <a href="proses/proses-br-out-reg.php?hapus=<?php echo encrypt($data['id_isi_br_out_reg'], $key_global) ?>"
                                                            class="btn btn-danger btn-sm rounded delete-data"><i class="bi bi-trash"
                                                                style="font-size: 14px;"></i></a>
                                                    </td>
                                                <?php
                                            }
                                        ?>
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
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>

</html>