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
        .table-responsive {
            overflow-x: auto;
        }
        .table {
            width: 100% !important;
        }

        input[type="text"]:read-only {
            background: #e9ecef;
        }

        textarea[type="text"]:read-only {
            background: #e9ecef;
        }

        pre {
            font-family: "Open Sans", sans-serif;
            font-size: 16px;
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
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body ms-2 mt-3">
                        <?php
                        $id = decrypt($_GET['id'], $key_global);
                        $add = $_GET['id'];
                        ?>
                        <div class="text-start">
                            <a href="barang-masuk-reg-import.php" class="btn btn-md btn-secondary text-end"><i class="bi bi-arrow-left"></i> Kembali</a>
                            <?php  
                                if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan" ) { 
                                    ?>
                                        <a href="input-isi-inv-br-import.php?id=<?php echo $add ?>" class="btn btn-md btn-primary text-end"><i class="bi bi-plus-circle"></i> Tambah data order</a>
                                    <?php
                                }
                            ?>
                        </div>
                        <div class="mt-3">
                            <div class="p-3">
                                <?php
                                $sql = "SELECT 
                                            ibi.id_inv_br_import, 
                                            ibi.no_inv,
                                            ibi.tgl_inv,
                                            ibi.tgl_order,
                                            ibi.no_awb,
                                            ibi.shipping_by,
                                            ibi.tgl_est,
                                            ibi.status_pengiriman,
                                            ibi.tgl_terima,
                                            ibi.keterangan,
                                            DATE_FORMAT(ibi.created_date, '%d/%m/%Y, %H:%i:%s') AS created_date,  -- Format tanggal Indonesia
                                            CASE 
                                                WHEN ibi.updated_date = '0000-00-00 00:00:00' THEN '-'
                                                ELSE DATE_FORMAT(ibi.updated_date, '%d/%m/%Y, %H:%i:%s')
                                            END AS updated_date,
                                            sp.nama_sp,
                                            sp.alamat, 
                                            uc.nama_user AS user_created, 
                                            uu.nama_user AS user_updated
                                        FROM inv_br_import AS ibi
                                        LEFT JOIN $database2.user AS uc ON (ibi.created_by = uc.id_user)
                                        LEFT JOIN $database2.user AS uu ON (ibi.updated_by = uu.id_user)
                                        LEFT JOIN tb_supplier sp ON (ibi.id_supplier = sp.id_sp)
                                        WHERE id_inv_br_import = '$id' LIMIT 1 ";
                                $query = mysqli_query($connect, $sql);
                                while ($data = mysqli_fetch_array($query)) {
                                ?>
                                    <div class="row">
                                        <div class="col-sm-6 border">
                                            <pre>
                                        <table>
                                            <tr>
                                                <td class="p-2" style="width: 130px">No. Invoice</td>
                                                <td class="p-2" style="width: 350px">: <?php echo $data['no_inv'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="p-2" style="width: 130px">Tgl. Invoice</td>
                                                <td class="p-2" style="width: 350px">: <?php echo $data['tgl_inv'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="p-2" style="width: 130px">Tgl. Order</td>
                                                <td class="p-2" style="width: 350px">: <?php echo $data['tgl_order'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="p-2" style="width: 130px">No. AWB</td>
                                                <td class="p-2" style="width: 350px">: <?php echo $data['no_awb'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="p-2" style="width: 130px">Dikirim Via</td>
                                                <td class="p-2" style="width: 350px">: <?php echo $data['shipping_by'] ?></td>
                                            </tr>
                                        </table>
                                    </pre>
                                        </div>
                                        <div class="col-sm-6 border">
                                            <pre>
                                        <table>
                                            <tr>
                                                <td class="p-2" style="width: 130px">Supplier</td>
                                                <td class="p-2" style="width: 350px">: <?php echo $data['nama_sp'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="p-2" style="width: 130px">Alamat</td>
                                                <td class="p-2" style="width: 350px">: <?php echo $data['alamat'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="p-2" style="width: 130px">Tgl. Estimasi</td>
                                                <td class="p-2" style="width: 350px">: <?php echo $data['tgl_est'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="p-2" style="width: 130px">Status</td>
                                                <td class="p-2" style="width: 350px">: <?php echo $data['status_pengiriman'] ?> <?php echo $data['tgl_terima'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="p-2" style="width: 130px">Keterangan</td>
                                                <td class="p-2" style="width: 350px">: <?php echo $data['keterangan'] ?></td>
                                            </tr>
                                        </table>
                                    </pre>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body mt-3">
                        <!-- Pills Tabs -->
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist" style="margin-left: 13px;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Produk Order</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Produk Actual</button>
                            </li>
                        </ul>
                        <div class="tab-content pt-2" id="myTabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="home-tab">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="table1">
                                        <thead>
                                            <tr class="text-white" style="background-color: navy;">
                                                <td class="text-center text-nowrap p-3">No</td>
                                                <td class="text-center text-nowrap p-3">Kode Produk</td>
                                                <td class="text-center text-nowrap p-3">Nama Produk</td>
                                                <td class="text-center text-nowrap p-3">Harga Penawaran</td>
                                                <td class="text-center text-nowrap p-3">Order</td>
                                                <td class="text-center text-nowrap p-3">Actual</td>
                                                <td class="text-center text-nowrap p-3">Status</td>
                                                <td class="text-center text-nowrap p-3">Aksi</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $sql = "SELECT 
                                                        iibi.id_isi_inv_br_import AS id_isi_inv,
                                                        iibi.id_inv_br_import,
                                                        iibi.harga_beli,
                                                        iibi.qty,
                                                        DATE_FORMAT(iibi.created_date, '%d/%m/%Y, %H:%i:%s') AS created_date,  -- Format tanggal Indonesia
                                                        CASE 
                                                            WHEN iibi.updated_date = '0000-00-00 00:00:00' THEN '-'
                                                            ELSE DATE_FORMAT(iibi.updated_date, '%d/%m/%Y, %H:%i:%s')
                                                        END AS updated_date,
                                                        uc.nama_user AS user_created, 
                                                        uu.nama_user as user_updated, 
                                                        COALESCE(tpr.nama_produk, tpe.nama_produk) AS nama_produk,
                                                        COALESCE(tpr.kode_produk, tpe.kode_produk) AS kode_produk,
                                                        SUM(act.qty_act) AS total_qty_act
                                                    FROM isi_inv_br_import AS iibi 
                                                    LEFT JOIN $database2.user AS uc ON (iibi.created_by = uc.id_user)
                                                    LEFT JOIN $database2.user AS uu ON (iibi.updated_by = uu.id_user)
                                                    LEFT JOIN inv_br_import ibi ON (iibi.id_inv_br_import = ibi.id_inv_br_import) 
                                                    LEFT JOIN tb_produk_reguler tpr ON (iibi.id_produk_reg = tpr.id_produk_reg) 
                                                    LEFT JOIN tb_produk_ecat tpe ON (iibi.id_produk_reg = tpe.id_produk_ecat) 
                                                    LEFT JOIN act_br_import act ON (iibi.id_isi_inv_br_import = act.id_isi_inv_br_import) 
                                                    WHERE iibi.id_inv_br_import = '$id' 
                                                    GROUP BY iibi.id_isi_inv_br_import";

                                            $query = mysqli_query($connect, $sql);
                                            while ($data = mysqli_fetch_array($query)) {
                                                $order = $data['qty'];
                                                $actual = $data['total_qty_act'];
                                                $total_min = $actual - $order;
                                            ?>
                                                <tr>
                                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                                    <td class="text-nowrap"><?php echo $data['kode_produk']; ?></td>
                                                    <td class="text-nowrap"><?php echo $data['nama_produk']; ?></td>
                                                    <td class="text-nowrap">$ <?php echo $data['harga_beli']; ?></td>
                                                    <td class="text-end text-nowrap"><?php echo number_format($data['qty']); ?></td>
                                                    <td class="text-end text-nowrap"><?php echo number_format($data['total_qty_act']); ?></td>
                                                    <?php
                                                    if ($actual == 0) {
                                                        echo "<td class='text-end'></td>";
                                                    } else if ($actual < $order) {
                                                        echo "<td class='text-end text-nowrap bg-danger text-white'> $total_min item</td>";
                                                    } else if ($actual > $order) {
                                                        echo "<td class='text-end text-nowrap bg-warning'>+$total_min item</td>";
                                                    } else {
                                                        echo "<td class='text-end text-nowrap bg-success text-white'>Oke</td>";
                                                    }
                                                    ?>
                                                    <td class="text-center text-nowrap">
                                                        <?php  
                                                            if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan" ) { 
                                                                ?>
                                                                    <a class="btn btn-secondary btn-sm" href="update-br-import.php?id=<?php echo encrypt($data['id_isi_inv'], $key_global); ?>" title="Input Actual">
                                                                        <i class="bi bi-box-seam"></i>
                                                                    </a>
                                                                <?php
                                                            }
                                                        ?>
                                                       
                                                        <a class="btn btn-info btn-sm" href="list-act-br-import.php?id=<?php echo encrypt($data['id_isi_inv'], $key_global); ?> && id_inv=<?php echo encrypt($data['id_inv_br_import'], $key_global); ?>" title="Detail Actual">
                                                            <i class="bi bi-info-circle"></i>
                                                        </a>
                                                        <?php  
                                                            if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan" ){ 
                                                                ?>
                                                                    <a class="btn btn-warning btn-sm" href="edit-br-import.php?id=<?php echo encrypt($data['id_isi_inv'], $key_global); ?> && id_inv=<?php echo encrypt($data['id_inv_br_import'], $key_global); ?>" title="Edit Data Order">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <a class="btn btn-danger btn-sm delete-data" href="proses/proses-br-in-import.php?hapus=<?php echo encrypt($data['id_isi_inv'], $key_global); ?> && id_inv=<?php echo encrypt($data['id_inv_br_import'], $key_global); ?> ">
                                                                        <i class="bi bi-trash"></i>
                                                                    </a>
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
                            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="tableExportNoAction">
                                        <thead>
                                            <tr class="text-white" style="background-color: navy;">
                                                <td class="text-center text-nowrap p-3 col-1">No</td>
                                                <td class="text-center text-nowrap p-3 col-2">Kode Produk</td>
                                                <td class="text-center text-nowrap p-3 col-4">Nama Produk</td>
                                                <td class="text-center text-nowrap p-3 col-3">Merk</td>
                                                <td class="text-center text-nowrap p-3 col-1">Qty</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $sql = "SELECT 
                                                    act.id_isi_inv_br_import AS id_isi_inv,  
                                                    COALESCE(tpr.nama_produk, tpe.nama_produk) AS nama_produk,
                                                    COALESCE(tpr.kode_produk, tpe.kode_produk) AS kode_produk,
                                                    SUM(act.qty_act) AS total_qty_act,
                                                    COALESCE(mr_reg.nama_merk, mr_ecat.nama_merk) AS merk
                                                    FROM isi_inv_br_import AS iibi
                                                    LEFT JOIN inv_br_import ibi ON (iibi.id_inv_br_import = ibi.id_inv_br_import) 
                                                    LEFT JOIN tb_produk_reguler tpr ON (iibi.id_produk_reg = tpr.id_produk_reg) 
                                                    LEFT JOIN tb_produk_ecat tpe ON (iibi.id_produk_reg = tpe.id_produk_ecat) 
                                                    LEFT JOIN act_br_import act ON (iibi.id_isi_inv_br_import = act.id_isi_inv_br_import) 
                                                    LEFT JOIN tb_merk mr_reg ON (tpr.id_merk = mr_reg.id_merk)
                                                    LEFT JOIN tb_merk mr_ecat ON (tpe.id_merk = mr_ecat.id_merk)
                                                    WHERE iibi.id_inv_br_import = '$id' AND act.qty_act IS NOT NULL GROUP BY iibi.id_isi_inv_br_import";
                                            $query = mysqli_query($connect, $sql);
                                            while ($data = mysqli_fetch_array($query)) {
                                            ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $no; ?></td>
                                                    <td class="text-nowrap"><?php echo $data['kode_produk']; ?></td>
                                                    <td class="text-nowrap"><?php echo $data['nama_produk']; ?></td>
                                                    <td class="text-center text-nowrap"><?php echo $data['merk']; ?></td>
                                                    <td class="text-end text-nowrap"><?php echo number_format($data['total_qty_act']); ?></td>
                                                </tr>
                                                <?php $no++ ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div><!-- End Pills Tabs -->     
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