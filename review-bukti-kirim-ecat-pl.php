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
        <div class="loader loader">
            <div class="loading">
                <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div>
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
                            <a href="menunggu-perbaikan-bukti-kirim.php?sort=baru&sort_data=bulan_ini" class="nav-link position-relative">
                                Menunggu Perbaikan
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary" id="total_sudah_review">
                                </span>
                            </a>
                        </li>
                        <li class="nav-item ms-3" role="presentation">
                            <a href="sudah-review-bukti-kirim.php?sort=baru&sort_data=bulan_ini" class="nav-link position-relative">
                                History Review
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary" id="total_sudah_review">
                                </span>
                            </a>
                        </li>
                    </ul>
                    <div class="card p-3">
                        <div class="mb-4">
                            <button type="button" id="review-reg" class="btn btn-outline-dark" style="width: 150px;">Reguler</button>
                            <button type="button" id="review-ecat" class="btn btn-outline-dark" style="width: 150px;">Ecat</button>
                            <button type="button" id="review-pl" class="btn btn-outline-dark active" style="width: 150px;">Ecat PL</button>
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
                                                <th class="text-center p-3 text-nowrap" style="width: 250px">Nama Satker</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 250px">Alamat</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">Jenis Invoice</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">Jenis Pengiriman</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 250px">Note Invoice</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 80px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include "koneksi-ecat.php";
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
                                                        COALESCE(pl.id_inv_pl) AS id_inv,
                                                        COALESCE(pl.no_inv_pl) AS no_inv,
                                                        COALESCE(pl.tgl_inv_pl) AS tgl_inv,
                                                        COALESCE(pl.satker_inv) AS satker,
                                                        COALESCE(pl.alamat_inv) AS alamat,
                                                        COALESCE(pl.jenis_invoice) AS jenis_inv,
                                                        COALESCE(pl.status_transaksi) AS status_transaksi,
                                                        COALESCE(NULLIF(pl.notes, ''), '-') AS notes,
                                                        sk.jenis_pengiriman,
                                                        sk.id_driver,
                                                        sk.id_ekspedisi,
                                                        ip.nama_penerima,
                                                        tbp.nama_provinsi AS nama_wilayah,
                                                        us.nama_user,
                                                        ex.nama_ekspedisi,
                                                        ibt.approval
                                                    FROM tb_spk_pl AS spk_pl
                                                    LEFT JOIN inv_pl AS pl ON spk_pl.id_inv_pl = pl.id_inv_pl
                                                    LEFT JOIN status_kirim sk ON spk_pl.id_inv_pl = sk.id_inv_ecat
                                                    LEFT JOIN inv_penerima ip ON spk_pl.id_inv_pl = ip.id_inv_ecat
                                                    LEFT JOIN tb_perusahaan tp ON spk_pl.id_perusahaan = tp.id_perusahaan
                                                    LEFT JOIN tb_provinsi tbp ON tp.id_provinsi = tbp.id_provinsi
                                                    LEFT JOIN $database2.user AS us ON sk.id_driver = us.id_user
                                                    LEFT JOIN $db.ekspedisi ex ON sk.id_ekspedisi = ex.id_ekspedisi
                                                    LEFT JOIN inv_bukti_terima ibt ON sk.id_inv_ecat = ibt.id_inv_ecat
                                                    WHERE COALESCE(pl.status_transaksi) = 'Diterima' 
                                                    AND sk.status_review = '0' 
                                                    GROUP BY no_inv $filter";
                                            $query = $connect_ecat->query($sql);
                                            $total_perlu_review = mysqli_num_rows($query);

                                            while ($data = mysqli_fetch_array($query)) {
                                                $data['id_inv'];
                                            ?>
                                                <tr>
                                                    <td class="text-center text-nowrap align-middle"><?php echo $no; ?></td>
                                                    <td class="text-center text-nowrap align-middle">
                                                        <?php echo $data['no_inv'] ?><br>
                                                        (<?php echo date('d/m/Y', strtotime($data['tgl_inv'])) ?>)
                                                    </td>
                                                    <td class="text-center text-wrap align-middle">
                                                        <?php echo $data['satker'] ?>
                                                    </td>
                                                    <td class="text-wrap align-middle"><?php echo $data['alamat'] ?></td>
                                                    <td class="text-center text-wrap align-middle"><?php echo $data['jenis_inv'] ?></td>
                                                    <td class="text-center text-nowrap align-middle">
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
                                                    <td class="text-center text-wrap align-middle"><?php echo $data['notes'] ?></td>
                                                    <td class="text-center text-nowrap align-middle">
                                                        <button type="button" data-id ="<?php echo encrypt($data['id_inv'], $key_global) ?>" class="btn btn-primary btn-sm mb-2 detailReview" id="detailReview" data-bs-toggle="modal" data-bs-target="#modalDetail" title="Lihat Bukti"><i class="bi bi-eye-fill"></i></buuton>
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
            url: "ajax/detail-review-bukti-kirim-ecat-pl.php", 
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

    let totalData = "<?php echo $total_perlu_review ?>";
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
        $("#review-reg").on("click", function() {
            window.location.href = "review-bukti-kirim.php"; // Ganti dengan URL tujuan
        });

        $("#review-ecat").on("click", function() {
            window.location.href = "review-bukti-kirim-ecat.php"; // Ganti dengan URL tujuan
        });
    });
</script>







