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
        table{
            padding: 0 !important;
        }

        td, th {
            vertical-align: middle; /* Bisa juga 'top' atau 'bottom' */
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

        .card-body-custom {
            padding: 20px 20px 7px 20px;
            flex: 1 1 auto;
            color: var(--bs-card-color);
        }

        .card-title {
            padding: 0;
            font-size: 18px;
            font-weight: 500;
            color: #012970;
            font-family: "Poppins", sans-serif;
        }

        .card-icon-custom {
            font-size: 27px;
            line-height: 0;
            width: 50px;
            height: 50px;
            flex-shrink: 0;
            flex-grow: 0;
            color: #4154f1;
            background: #f6f6fe;
        }

        #cari-data {    
            padding-right: 2.5rem;
        }

        #resetButton {
            position: absolute;
            top: 50%;
            right: 0.5rem;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            z-index: 2;
            display: none; /* Default state: hidden */
        }

        #resetButton i {
            font-size: 1rem;
        }

        #customPagination {
            justify-content: end;
        }

        /* Ubah display pagination bawaan datatable menjadi NONE */
        div.dt-container div.dt-paging ul.pagination {
            display: none;
        }

        /* Animasi untuk perubahan ikon */
        #arrow-icon-transaksi, #arrow-icon-cashback {
            transition: transform 0.4s ease, opacity 0.4s ease;
        }

        .collapsing #arrow-icon-transaksi, .collapsing #arrow-icon-cashback {
            opacity: 0.5; /* Ikon sedikit transparan selama transisi */
        }

        .transparent {
            background-color: transparent !important;
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
                <div class="card-body mt-3">
                    <!-- Bordered Tabs -->
                    <ul class="nav nav-tabs nav-tabs-bordered" id="borderedTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="review-bukti-kirim.php?sort=baru" class="nav-link position-relative">
                                Perlu Direview
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary" id="total_perlu_review">
                                </span>
                            </a>
                        </li>
                        <li class="nav-item ms-3" role="presentation">
                            <a href="#" class="nav-link active position-relative">
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
                    <div class="p-3">
                        <div class="mb-4">
                            <button type="button" id="sudah-review-reg" class="btn btn-outline-dark" style="width: 150px;">Reguler</button>
                            <button type="button" id="sudah-review-ecat" class="btn btn-outline-dark" style="width: 150px;">Ecat</button> 
                            <button type="button" id="sudah-review-pl" class="btn btn-outline-dark active" style="width: 150px;">Ecat PL</button>
                        </div>
                        <div class="mt-4">
                            <div class="col-sm-2 mb-3">
                                <select name="sort" class="form-select" id="filter">
                                    <option value="baru" <?php if (isset($_GET['sort']) && $_GET['sort'] == "baru") {
                                                                                        echo "selected";
                                                                                    } ?>>Paling Baru</option>
                                    <option value="lama" <?php if (isset($_GET['sort']) && $_GET['sort'] == "lama") {
                                                                echo "selected";
                                                            } ?>>Paling Lama</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <?php
                                    require_once __DIR__ . "/function/function-enkripsi.php";
                                    include "koneksi-ecat.php";
                                    $session_url = $_SERVER['SCRIPT_NAME'] . '?';
                                    $_SESSION['url'] = $session_url;   
                                    $url = $_SESSION['url']; 
                                    $filter = '';
                                    if (isset($_GET['sort'])) {
                                        if ($_GET['sort'] == "baru") {
                                            $filter = "ORDER BY ibt.created_date DESC";
                                        } elseif ($_GET['sort'] == "lama") {
                                            $filter = "ORDER BY ibt.created_date ASC";
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
                                            WHERE 
                                                COALESCE(pl.status_transaksi)
                                                IN ('Diterima', 'Transaksi Selesai', 'Komplain Selesai')
                                                AND sk.status_review = '1' AND approval = '1' AND ibt.status_perbaikan = '0' $filter";
                                            $query = mysqli_query($connect_ecat, $sql);
                                            $total_data_sudah_review = mysqli_num_rows($query);

                                ?>
                                <div class="recent-sales">
                                    <div class="mt-3">
                                        <!-- Custom botton export and search -->
                                        <div class="d-flex justify-content-between flex-wrap">
                                            <div class="card mb-3 me-2 mt-2 transparent-card">
                                                <!-- Isi Button lain nya jika di butuhkan -->
                                            </div>
                                            <div class="card mb-3 mt-1 transparent-card">
                                                <div class="input-container">
                                                    <input type="text" class="form-control" placeholder="Cari Data" id="cari-data">
                                                    <button type="button" class="text-secondary" id="resetButton">
                                                        <i class="bi bi-x fs-5"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Custom botton export and search -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped w-100" id="tableNoExportNew">
                                                <thead>
                                                    <tr class="text-white" style="background-color: navy;">
                                                    <th class="text-center p-3 text-nowrap" style="width: 30px">No</th>
                                                        <th class="text-center p-3 text-nowrap" style="width: 100px">No. Invoice</th>
                                                        <th class="text-center p-3 text-nowrap" style="width: 150px">ID Paket</th>
                                                        <th class="text-center p-3 text-nowrap" style="width: 250px">Nama Paket</th>
                                                        <th class="text-center p-3 text-nowrap" style="width: 250px">Nama Satker</th>
                                                        <th class="text-center p-3 text-nowrap" style="width: 200px">Diupload Oleh</th>
                                                        <th class="text-center p-3 text-nowrap" style="width: 100px">Jenis Pengiriman</th>
                                                        <th class="text-center p-3 text-nowrap" style="width: 100px">Jenis Reject</th>
                                                        <th class="text-center p-3 text-nowrap" style="width: 100px">Alasan Reject</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        while ($data = mysqli_fetch_array($query)) {
                                                    ?>
                                                        <tr>
                                                            <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                                            <td class="text-center text-nowrap">
                                                                <div><?php echo $data['no_inv'] ?></div>
                                                                <div><?php echo date('d/m/Y', strtotime($data['tgl_inv'])) ?></div>
                                                            </td>
                                                            <td class="text-center text-nowrap">
                                                                <?php 
                                                                    echo !empty($data['no_paket']) ? $data['no_paket'] : '-';
                                                                ?>
                                                            </td>
                                                            <td class="text-wrap"><?php echo $data['nama_paket'] ?></td>
                                                            <td class="text-wrap"><?php echo $data['satker'] ?></td>
                                                            <td class="text-center">
                                                                <div><?php echo $data['user_created']; ?></div>
                                                                <div><?php echo date('d/m/Y H:i:s', strtotime($data['created_date'])); ?></div>
                                                            </td>
                                                            <td class="text-center text-nowrap">
                                                                <?php 
                                                                    if ($data['jenis_pengiriman'] == "Driver") {
                                                                        echo $data['jenis_pengiriman']."<br>";
                                                                        echo "(".$data['nama_driver'].")";
                                                                    } else if ($data['jenis_pengiriman'] == "Ekspedisi") {
                                                                        echo $data['jenis_pengiriman']."<br>";
                                                                        echo "(".$data['nama_ekspedisi'].")";
                                                                    } else {
                                                                        echo $data['jenis_pengiriman']."<br>";
                                                                        echo "(".$data['nama_penerima'].")";
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td class="text-center text-nowrap">
                                                                <?php 
                                                                    if($data['jenis_reject'] == '1'){
                                                                        echo "Gambar";
                                                                    } else if ($data['jenis_reject'] == '2'){
                                                                        echo "Data";
                                                                    } else {
                                                                        echo "Tidak Ada Jenis Terpilih";
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td class="text-center text-wrap"><?php echo $data['alasan']; ?></td>
                                                        </tr>
                                                        <?php $no++; ?>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- End Table Responsive -->
                                        <div class="text-start" id="totalData"></div>
                                        <!-- Custom pagination -->
                                        <nav>
                                            <ul class="pagination" id="customPagination">
                                            <!-- Pagination items will be inserted here by JavaScript -->
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div><!-- End Recent Sales -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal History -->
            <div class="modal fade" id="modalHistory" tabindex="-1"  data-bs-backdrop="static" aria-labelledby="modalDetailLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4>History Pengiriman</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="contentHistory">
                            <!-- Data akan dimuat di sini -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal History -->
        </section>
    </main><!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
    <!-- Fancybox -->
    <script src="assets/vendor/fancybox/fancybox.umd.js"></script>
    <!-- Datatable -->
    <script src="assets/js/datatable-custom-noexport.js"></script>
</body>
</html>
<script>
   $(document).ready(function(){
        $(document).on('change', '#filter', function() {
            var filterValue = $(this).val();

            // Ambil URL saat ini
            var currentUrl = new URL(window.location.href);
            
            // Update hanya parameter "sort"
            currentUrl.searchParams.set('sort', filterValue);

            // Redirect dengan URL yang sudah diperbarui
            window.location.href = currentUrl.toString();
        });
    });


    $(document).on('click', '.historyReview', function() {
        var id = $(this).data("id");

        $.ajax({
            url: "ajax/history-bukti-kirim.php", 
            type: "POST",
            data: { id: id },
            success: function (response) {
                $("#contentHistory").html(response);
            },
            error: function () {
                $("#contentHistory").html('<p class="text-danger">Gagal mengambil data.</p>');
            }
        });
    });
</script>

<script>
    let totalSudahReview = "<?php echo $total_data_sudah_review ?>";
    $('#sudah_review').text(totalSudahReview);
</script>
<script>
    $(document).ready(function(){
        $.ajax({
            url: "query/badge-perlu-review.php",
            type: "GET",
            dataType: "json",
            success: function(response) {
                console.log("Data diterima:", response);
                $("#total_perlu_review").text(response.total_data_perlu_review);
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
        $("#sudah-review-reg").on("click", function() {
            window.location.href = "menunggu-perbaikan-bukti-kirim.php?sort=baru"; // Ganti dengan URL tujuan
        });

        $("#sudah-review-ecat").on("click", function() {
            window.location.href = "menunggu-perbaikan-bukti-kirim-ecat.php?sort=baru"; // Ganti dengan URL tujuan
        });
    });
</script>


