<?php
require_once "akses.php";
$page  = 'transaksi';
$page2 = 'list-review';
require_once "function/function-enkripsi.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <!-- FancyBox CSS -->
    <link rel="stylesheet" href="assets/vendor/fancybox/fancybox.css">
    <?php include "page/head.php"; ?>

    <style>
        /* Atur ukuran maksimal untuk Fancybox */
        .fancybox__container {
            width: 100vw !important;
            height: 100vh !important;
            z-index: 9999 !important; /* Fancybox selalu di depan */
        }

        .img-preview {
            height: 300px !important; /* Sesuaikan tinggi sesuai kebutuhan */
            width: 300px !important; /* Sesuaikan lebar sesuai kebutuhan */
        }

        table{
            padding: 0 !important;
        }
        @media (max-width: 767px) {

            /* Tambahkan aturan CSS khusus untuk tampilan mobile di bawah 767px */
            .col-12.col-md-2 {
                /* Contoh: Mengatur tinggi elemen select pada tampilan mobile */
                height: 50px;
            }
        }

        .btn.active {
            background-color: black;
            color: white;
            border-color: 1px solid white;
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
            <h1>Data Review Bukti Kirim</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Review Bukti Terima</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section>
            <!-- SWEET ALERT -->
            <?php
                if (isset($_SESSION['info'])) {
                    echo '<div class="info-data" data-infodata="' . $_SESSION['info'] . '"></div>';
                    unset($_SESSION['info']);
                }
            ?>
            <!-- END SWEET ALERT -->
            <div class="card">
                <?php  
                    require_once __DIR__ . "/query/badge-review-bukti-kirim.php";
                ?>
                <div class="card-body mt-3">
                    <!-- Bordered Tabs -->
                    <ul class="nav nav-tabs nav-tabs-bordered" id="borderedTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active position-relative" id="home-tab" data-bs-toggle="tab" data-bs-target="#bordered-home" type="button" role="tab" aria-controls="home" aria-selected="true">
                                Perlu Direview
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary" id="review">
                                </span>
                            </button>
                        </li>
                        <li class="nav-item ms-3" role="presentation">
                            <a href="menunggu-perbaikan-bukti-kirim.php?sort=baru" class="nav-link position-relative">
                                Menunggu Perbaikan
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary" id="total_sudah_review">
                                </span>
                            </a>
                        </li>
                        <li class="nav-item ms-3" role="presentation">
                            <a href="sudah-review-bukti-kirim.php?sort=baru" class="nav-link position-relative">
                                History Review
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary" id="total_sudah_review">
                                </span>
                            </a>
                        </li>
                    </ul>
                    <div class="p-3">
                        <div class="mb-4">
                            <button type="button" id="review-reg" class="btn btn-outline-dark active" style="width: 150px;">Reguler</button>
                            <button type="button" id="review-ecat" class="btn btn-outline-dark" style="width: 150px;">Ecat</button> 
                            <button type="button" id="review-pl" class="btn btn-outline-dark" style="width: 150px;">Ecat PL</button>
                        </div>
                        <div class="mt-4">
                            <div class="col-sm-2">
                                <select name="sort" class="form-select" id="filter">
                                    <option value="baru" <?php if (isset($_GET['sort']) && $_GET['sort'] == "baru") {
                                                                                        echo "selected";
                                                                                    } ?>>Paling Baru</option>
                                    <option value="lama" <?php if (isset($_GET['sort']) && $_GET['sort'] == "lama") {
                                                                echo "selected";
                                                            } ?>>Paling Lama</option>
                                </select>
                            </div>
                            <div class="bg-body rounded mt-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="table2">
                                        <thead>
                                            <tr class="text-white" style="background-color: navy;">
                                                <th class="text-center p-3 text-nowrap" style="width: 30px">No</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">No. Invoice</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 150px">No. PO</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 250px">Nama Customer</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">Kat. Inv</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 250px">Note Invoice</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">Jenis Pengiriman</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 80px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include "koneksi.php";
                                            $no = 1;
                                            $filter = '';
                                            if (isset($_GET['sort'])) {
                                                if ($_GET['sort'] == "baru") {
                                                    $filter = "ORDER BY tgl_inv DESC";
                                                } elseif ($_GET['sort'] == "lama") {
                                                    $filter = "ORDER BY tgl_inv ASC";
                                                } else {
                                                    ?>
                                                        <script>
                                                            window.location.replace('404.php');
                                                        </script>
                                                    <?php
                                                }
                                            }
                                            $sql = "SELECT 
                                                        COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                                                        COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                                        COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                                                        COALESCE(nonppn.kategori_inv, ppn.kategori_inv, bum.kategori_inv) AS kategori_inv,
                                                        COALESCE(nonppn.note_inv, ppn.note_inv, bum.note_inv) AS note_inv,
                                                        COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi) AS status_transaksi,
                                                        sr.no_po,
                                                        cs.nama_cs,
                                                        sk.jenis_pengiriman,
                                                        sk.dikirim_driver,
                                                        sk.dikirim_ekspedisi,
                                                        ip.nama_penerima,
                                                        us.nama_user,
                                                        ex.nama_ekspedisi,
                                                        ibt.approval
                                                    FROM spk_reg AS sr
                                                    LEFT JOIN inv_nonppn AS nonppn ON sr.id_inv = nonppn.id_inv_nonppn
                                                    LEFT JOIN inv_ppn AS ppn ON sr.id_inv = ppn.id_inv_ppn
                                                    LEFT JOIN inv_bum AS bum ON sr.id_inv = bum.id_inv_bum
                                                    LEFT JOIN tb_customer cs ON sr.id_customer = cs.id_cs
                                                    LEFT JOIN status_kirim sk ON sr.id_inv = sk.id_inv
                                                    LEFT JOIN inv_penerima ip ON sr.id_inv = ip.id_inv
                                                    LEFT JOIN $database2.user AS us ON sk.dikirim_driver = us.id_user
                                                    LEFT JOIN ekspedisi ex ON sk.dikirim_ekspedisi = ex.id_ekspedisi
                                                    LEFT JOIN inv_bukti_terima ibt ON sk.id_inv = ibt.id_inv
                                                    WHERE COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi) = 'Diterima' 
                                                    AND sk.status_review = '0' 
                                                    GROUP BY no_inv $filter";

                                            $query = mysqli_query($connect, $sql);
                                            $total_perlu_review = mysqli_num_rows($query);

                                            while ($data = mysqli_fetch_array($query)) {
                                               $id_inv = encrypt($data['id_inv'], $key_global);
                                            ?>
                                                <tr>
                                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                                    <td class="text-center text-nowrap">
                                                        <div><?php echo $data['no_inv'] ?></div>
                                                        <div>(<?php echo $data['tgl_inv'] ?>)</div>
                                                    </td>
                                                    <td class="text-center text-nowrap">
                                                        <?php 
                                                            if(!empty($data['no_po'])){
                                                                echo $data['no_po'];
                                                            } else {
                                                                echo '-';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="text-nowrap"><?php echo $data['nama_cs'] ?></td>
                                                    <td class="text-center text-nowrap"><?php echo $data['kategori_inv'] ?></td>
                                                    <td>
                                                        <?php
                                                            $note = $data['note_inv'];

                                                            $items = explode("\n", trim($note));

                                                            if(!empty($note)){
                                                                foreach ($items as $notes) {
                                                                    echo trim($notes) . '<br>';
                                                                }
                                                            }else{
                                                                echo 'Tidak Ada';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="text-center text-nowrap">
                                                        <?php 
                                                            if($data['jenis_pengiriman'] == "Driver"){
                                                                echo $data['jenis_pengiriman']."<br>";
                                                                echo "(".$data['nama_user'].")";
                                                            } else if($data['jenis_pengiriman'] == "Ekspedisi"){
                                                                echo $data['jenis_pengiriman']."<br>";
                                                                echo "(".$data['nama_ekspedisi'].")";
                                                            } else {
                                                                echo $data['jenis_pengiriman']."<br>";
                                                                echo "(".$data['nama_penerima'].")";
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="text-center text-nowrap">
                                                        <button type="button" data-id ="<?php echo urlencode($id_inv); ?>" class="btn btn-primary btn-sm mb-2 detailReview" id="detailReview" data-bs-toggle="modal" data-bs-target="#modalDetail" title="Lihat Bukti"><i class="bi bi-eye-fill"></i></buuton>
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
            </div>
            <!-- Modal Review -->
            <div class="modal fade" id="modalDetail" tabindex="-1"  data-bs-backdrop="static" aria-labelledby="modalDetailLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="contentReview">
                            <!-- Data akan dimuat di sini -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal Review -->
        </section>
    </main><!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
    <!-- Fancybox -->
    <script src="assets/vendor/fancybox/fancybox.umd.js"></script>
</body>
</html>
<script>
    $(document).ready(function(){
        $(document).on('change', '#filter', function() {
            var filterValue = $(this).val();
            if (filterValue === "baru") {
                window.location.replace('review-bukti-kirim.php?sort=baru');
            } else if (filterValue === "lama") {
                window.location.replace('review-bukti-kirim.php?sort=lama');
            } else {
                window.location.replace('404.php');
            }
        });
    });

    $(document).on('click', '.detailReview', function() {
        var id = $(this).data("id");

        $.ajax({
            url: "ajax/detail-review-bukti-kirim.php", 
            type: "POST",
            data: { id: id },
            success: function (response) {
                $("#contentReview").html(response);
            },
            error: function () {
                $("#contentReview").html('<p class="text-danger">Gagal mengambil data.</p>');
            }
        });
    });

    let totalData = "<?php echo $total_reg + $total_ecat + $total_pl ?>";
    $('#review').text(totalData);

</script>

<script>
    $(document).ready(function(){
        $.ajax({
            url: "query/badge-sudah-review.php",
            type: "GET",
            dataType: "json",
            success: function(response) {
                // console.log("Data diterima:", response);
                $("#total_sudah_review").text(response.total_data_sudah_review);
            },
            error: function(xhr, status, error) {
                // console.error("AJAX Error:", status, error);
                // console.log("Response Text:", xhr.responseText);
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $("#review-ecat").on("click", function() {
            window.location.href = "review-bukti-kirim-ecat.php"; // Ganti dengan URL tujuan
        });

        $("#review-pl").on("click", function() {
            window.location.href = "review-bukti-kirim-ecat-pl.php"; // Ganti dengan URL tujuan
        });
    });
</script>







