<?php
require_once '../akses.php';
$id_user = decrypt($_SESSION['tiket_id'], $key_global);
$page = 'list-inv';
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
            <!-- SWEET ALERT -->
            <?php
                if (isset($_SESSION['info'])) {
                    echo '<div class="info-data" data-infodata="' . $_SESSION['info'] . '" style="overvlow:hidden !important;"></div>';
                    unset($_SESSION['info']);
                }
            ?>
            <!-- END SWEET ALERT -->
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
                        <!-- Query data inv baru -->
                        <?php  
                            require_once __DIR__ . "/../koneksi-ecat.php";
                            include 'query/inv-baru-ecat.php'; 
                            $query_total_data = mysqli_query($connect_ecat, $sql) or die(mysqli_error($connect_ecat));
                            $total_data = mysqli_num_rows($query_total_data);

                            include "query/menunggu-verif-invoice.php";
                            $query_total_data_waiting_verif = mysqli_query($connect_ecat, $sql_waiting_verif) or die(mysqli_error($connect_ecat));
                            $total_data_waiting_verif = mysqli_num_rows($query_total_data_waiting_verif); 
                        ?>
                        <!-- End Query -->
                        <!-- Tabs menu -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#" class="nav-link active">
                                    Invoice Baru &nbsp;
                                    <?php  
                                        if ($total_data != 0){
                                            ?>
                                                <span class="badge text-bg-secondary"><?php echo $total_data; ?></span>
                                            <?php
                                        }
                                    ?>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="list-invoice-revisi.php" class="nav-link">
                                    Invoice Revisi &nbsp;
                                    <?php  
                                        if ($total_data_rev != 0){
                                            ?>
                                                <span class="badge text-bg-secondary"><?php echo $total_data_rev; ?></span>
                                            <?php
                                        }
                                    ?>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="menunggu-verif-invoice.php" class="nav-link">
                                    Menunggu Verifikasi PJT &nbsp;
                                    <?php  
                                        if ($total_data_waiting_verif != 0){
                                            ?>
                                                <span class="badge text-bg-secondary"><?php echo $total_data_waiting_verif; ?></span>
                                            <?php
                                        }
                                    ?>
                                </a>
                            </li>
                        </ul>
                        <div class="p-3">
                            <button type="button" class="btn btn-outline-primary" id="reguler">Reguler</button>
                            <button type="button" class="btn btn-outline-primary active" id="ecat">Ecat</button>
                            <button type="button" class="btn btn-outline-primary" id="ecat-pl">Ecat PL</button>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-bordered" id="table2">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                        <td class="text-center p-3 col-2 text-nowrap">Aksi</td>
                                        <td class="text-center p-3 col-2 text-nowrap">No Invoice</td>
                                        <td class="text-center p-3 col-2 text-nowrap">Nama Customer</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        date_default_timezone_set('Asia/Jakarta');
                                        require_once "../function/function-enkripsi.php";
                                        $key = "Driver2024?";
                                        $no = 1;
                                        $query = mysqli_query($connect_ecat, $sql) or die(mysqli_error($connect_ecat));
                                        while ($data = mysqli_fetch_array($query)) {
                                            $jenis_pengiriman = $data['jenis_pengiriman'];
                                            $jenis_penerima = $data['jenis_penerima'];
                                            $id_inv = $data['id_inv_ecat'];
                                            $id_inv_encrypt = encrypt($id_inv, $key);
                                            $approval = $data['approval'];
                                    ?>
                                    <tr>
                                        <td class="text-center text-nowrap" style="vertical-align: middle;">
                                            <?php
                                                if($jenis_pengiriman = 'Driver' && $jenis_penerima == '' || $approval == '1'){
                                                    ?>
                                                        <a href="detail-invoice-ecat.php?id=<?php echo $id_inv_encrypt ?>" class="btn btn-primary btn-sm"><i class="bi bi-arrow-repeat"></i> Proses</a>
                                                    <?php
                                                } else if ($jenis_pengiriman = 'Driver' && $jenis_penerima == 'Ekspedisi'){
                                                    ?>
                                                        <button class="btn btn-secondary btn-sm"><i class="bi bi-truck"></i> Dalam Perjalanan</button>
                                                    <?php
                                                }
                                            ?>
                                        </td>
                                        <td class="text-nowrap text-center">
                                            <?php echo $data['no_inv']; ?><br><?php echo date('H:i:s', strtotime($data['created_date'])) ?><br>(<?php echo date('d/m/Y', strtotime($data['tgl_pesanan'])) ?>)
                                        </td>
                                        <td class="text-nowrap align-middle"><?php echo $data['satker']; ?></td>
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

<!-- jquery 3.6.3 -->
<script src="../assets/js/jquery.min.js"></script>
<script>
$(document).ready(function() {
    function setActiveButton(buttonId) {
        $(".button").removeClass("active"); // Hapus class active dari semua button
        $("#" + buttonId).addClass("active"); // Tambahkan class active ke tombol yang diklik
    }

    $("#reguler").click(function() {
        window.location.href = "list-invoice-reg.php";
    });

    $("#ecat").click(function() {
        window.location.href = "list-invoice-ecat.php";
    });

    $("#ecat-pl").click(function() {
        window.location.href = "list-invoice-ecat-pl.php";
    });
});
</script>