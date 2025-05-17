<?php
    require_once "../akses.php";
    $page = 'list-inv';
    $page2 = 'list-inv-rev'; 
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
                        <!-- Query data -->
                        <?php  
                            require_once __DIR__. '/query/inv-revisi.php'; 
                            require_once __DIR__. "/query/menunggu-verif-invoice.php";
                            require_once __DIR__ . "/query/badge-inv-revisi.php";
                            require_once __DIR__ . "/query/badge-menunggu-verif.php";
                        ?>
                        <!-- End Query data -->

                        <!-- Tabs Menu -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="list-invoice-revisi.php" class="nav-link active">
                                    Invoice Revisi &nbsp;
                                    <span class="badge text-bg-secondary" id="revisi"></span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="menunggu-verif-invoice-reg-revisi.php" class="nav-link">
                                    Menunggu Verifikasi PJT &nbsp;
                                    <span class="badge text-bg-secondary" id="verif"></span>
                                </a>
                            </li>
                        </ul>
                        <div class="p-3">
                            <button type="button" id="reguler" class="btn btn-outline-info active" style="width: 150px;">
                                Reguler 
                                <span class="ms-1 badge text-bg-primary" id="badgeReg"></span>
                            </button>
                            <button type="button" id="ecat" class="btn btn-outline-info" style="width: 150px;">
                                Ecat
                                <span class="ms-1 badge text-bg-primary" id="badgeEcat"></span>
                            </button> 
                            <button type="button" id="ecat-pl" class="btn btn-outline-info" style="width: 150px;">
                                Ecat PL
                                <span class="ms-1 badge text-bg-primary" id="badgePl"></span>
                            </button>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-bordered" id="table2">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                        <td class="text-center p-3 col-2 text-nowrap" style="display: none;">No</td>
                                        <td class="text-center p-3 col-2 text-nowrap">Aksi</td>
                                        <td class="text-center p-3 col-2 text-nowrap">No Invoice</td>
                                        <td class="text-center p-3 col-2 text-nowrap">Nama Customer</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    date_default_timezone_set('Asia/Jakarta');
                                    require_once "../function/function-enkripsi.php";
                                    require_once "../function/uuid.php";
                                    $day = date('d');
                                    $month = date('m');
                                    $year = date('Y');
                                    $key = "Driver2024?";
                                    $no = 1;
                                    $query = mysqli_query($connect, $sql_rev) or die(mysqli_error($connect));
                                    while ($data = mysqli_fetch_array($query)) {
                                        $no_inv = $data['no_inv']; 
                                        $tgl_pesanan = $data['spk_tgl_pesanan'];
                                        $cs_inv = $data['cs_inv'];
                                        $alamat = $data['alamat'];
                                        $jenis_pengiriman = $data['jenis_pengiriman'];
                                        $jenis_penerima = $data['jenis_penerima'];
                                        $no_inv_revisi = $data['no_inv_rev'];
                                        $id_komplain = encrypt($data['id_komplain'], $key);
                                    ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $no++ ?></td>
                                        <td class="text-center text-nowrap align-middle">
                                            <?php
                                                    if($jenis_pengiriman = 'Driver' && $jenis_penerima == ''){
                                                        ?>
                                                            <a href="detail-invoice-revisi.php?id=<?php echo encrypt($data['id_inv'], $key)?>&&id_komplain=<?php echo $id_komplain ?>"
                                                            class="btn btn-primary btn-sm">
                                                                <i class="bi bi-arrow-repeat"></i> Proses
                                                            </a>
                                                        <?php
                                                    } else {
                                                       
                                                    }
                                                ?>
                                        </td>
                                        <td class="text-nowrap text-center">
                                            <?php echo $no_inv_revisi ?>
                                            <br>
                                            <?php echo date('H:i:s', strtotime($data['created_date'])) ?>
                                            <br>
                                            <?php echo $tgl_pesanan ?>
                                        </td>
                                        <td class="text-nowrap align-middle"><?php echo $cs_inv ?></td>
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
<script>
    let totalDataRevisi = "<?php echo $total_rev_reg ?>";
    let badgeReguler = "<?php echo $total_rev_reg ?>";
   

    console.log(totalDataRevisi);

    if(totalDataRevisi != 0){
        $('#revisi').text(totalDataRevisi);
    } else {
        $('#revisi').addClass('d-none');
    }

    $('#badgeReg').text(badgeReguler);

</script>