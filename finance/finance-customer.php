<?php
$page = 'list-cs';
$page2 = 'spk';
require_once "../akses.php";
include "../function/class-spk.php";
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
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
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
    </style>
</head>

<body>
    <!-- nav header -->
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include "page/sidebar.php"; ?>
    <!-- end sidebar -->
    <!-- Main Content -->
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Transaksi Customer</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Transaksi Customer</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <!-- Loading -->
        <div class="loader loader">
            <div class="loading">
            <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div>
        <!-- End Loading -->
        <!-- Query -->
        <?php
            include "query/query-fnc-cs.php";
            $session_url = "finance-customer.php?";
            $_SESSION['url'] = $session_url;   
            $url = $_SESSION['url']; 
            $key = $key_finance;

            // Query Total Komplain 
            $sql_total_komplain = $connect->query("SELECT DISTINCT
                                                        COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                                                        komplain.id_komplain,
                                                        spk.id_inv,
                                                        spk.id_customer
                                                    FROM spk_reg AS spk
                                                    LEFT JOIN inv_komplain komplain ON (komplain.id_inv = spk.id_inv) 
                                                    LEFT JOIN inv_nonppn nonppn ON spk.id_inv = nonppn.id_inv_nonppn
                                                    LEFT JOIN inv_ppn ppn ON spk.id_inv = ppn.id_inv_ppn
                                                    LEFT JOIN inv_bum bum ON spk.id_inv = bum.id_inv_bum
                                                    WHERE $sort_data AND komplain.id_komplain IS NOT NULL
                                                    GROUP BY spk.id_inv");
            $total_data_komplain = mysqli_num_rows($sql_total_komplain);

            $total = 0;
            $total_pending = 0;
            $total_bayar = 0; 
            $total_sisa_tagihan = 0;
            $sisa_belum_bayar = 0;
            $totalData = mysqli_num_rows($query2);
            while ($data2 = mysqli_fetch_array($query2)) {
                $total += $data2['total_nominal_inv_selesai'];
                $total_pending += $data2['total_nominal_inv_belum_selesai'];
                $total_bayar += $data2['total_bayar'];
                $sisa_belum_bayar = $total - $total_bayar;
                $total_sisa_tagihan += $data2['selisih_bayar_dan_nominal'];
            
            }
        ?>
        <section class="section dashboard">
            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="col-12">
                    <!-- Tombol Collapse Transaksi -->
                    <button class="btn btn-primary d-md-none mb-3 w-100" type="button" data-bs-toggle="collapse" data-bs-target="#rekapTransaksi" aria-expanded="false" aria-controls="rekapTransaksi">
                        Lihat rekapan transaksi 
                        <span id="arrow-icon-transaksi" class="ms-3 bi bi-caret-right-fill"></span> <!-- Ikon panah kanan di awal -->
                    </button>
                </div>
                <!-- Rekapan transaksi -->
                <div class="collapse d-md-block" id="rekapTransaksi">
                    <div class="row">
                        <!-- Total Customer -->
                        <div class="col-xxl-2 col-md-6">
                            <div class="card info-card sales-card rounded-4">
                                <div class="card-body-custom">
                                    <h5 class="card-title fs-6">Total Customer</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon-custom rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-2">
                                            <h6 class="fs-5"><?php echo $total_data; ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Total Customer -->

                        <!-- Total Komplain -->
                        <div class="col-xxl-2 col-md-6">
                            <div class="card info-card sales-card rounded-4">
                                <div class="card-body-custom">
                                    <h5 class="card-title fs-6">Total Komplain</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon-custom rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person-exclamation"></i>
                                        </div>
                                        <div class="ps-2">
                                            <h6 class="fs-5"><?php echo $total_data_komplain; ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Total Komplain -->

                        <!-- Total Nominal -->
                        <div class="col-xxl-2 col-md-6">
                            <div class="card info-card sales-card rounded-4">
                                <div class="card-body-custom">
                                    <h5 class="card-title fs-6">Total Nominal</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon-custom rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cash"></i>
                                        </div>
                                        <div class="ps-2">
                                            <h6 class="fs-5"><?php echo number_format($total,0,'.','.') ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Total Nominal -->

                        <!-- Total Pending Nominal -->
                        <div class="col-xxl-2 col-md-6">
                            <div class="card info-card sales-card rounded-4">
                                <div class="card-body-custom">
                                    <h5 class="card-title fs-6">Total Pending Nominal</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon-custom rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cash"></i>
                                        </div>
                                        <div class="ps-2">
                                            <h6 class="fs-5"><?php echo number_format($total_pending,0,'.','.') ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Total Pending Nominal -->

                        <!-- Total Tagihan -->
                        <div class="col-xxl-2 col-md-6">
                            <div class="card info-card sales-card rounded-4">
                                <div class="card-body-custom">
                                    <h5 class="card-title fs-6">Total Tagihan</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon-custom rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cash"></i>
                                        </div>
                                        <div class="ps-2">
                                            <h6 class="fs-5"><?php echo number_format($total_sisa_tagihan,0,'.','.') ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Total Tagihan -->

                        <!-- Total Belum Bayar -->
                        <div class="col-xxl-2 col-md-6">
                            <div class="card info-card sales-card rounded-4">
                                <div class="card-body-custom">
                                    <h5 class="card-title fs-6">Total Belum Bayar</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon-custom rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cash"></i>
                                        </div>
                                        <div class="ps-2">
                                            <h6 class="fs-5"><?php echo number_format($sisa_belum_bayar,0,'.','.') ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Total Belum Bayar -->
                    </div>
                </div>

                <div class="col-12">
                    <!-- Tombol Collapse Cashback -->
                    <button class="btn btn-primary d-md-none mb-3 w-100" type="button" data-bs-toggle="collapse" data-bs-target="#rekapCashback" aria-expanded="false" aria-controls="rekapCashback">
                        Lihat rekapan cashback
                        <span id="arrow-icon-cashback" class="ms-3 bi bi-caret-right-fill"></span> <!-- Ikon panah kanan di awal -->
                    </button>
                </div>
                <!-- Rekapan Cashback -->
                <div class="collapse d-md-block" id="rekapCashback">
                    <div class="row">
                        <!-- Cashback Per Barang -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="card info-card sales-card rounded-4">
                                <div class="card-body-custom">
                                    <h5 class="card-title fs-6">Cashback Per Barang</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon-custom rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cash"></i>
                                        </div>
                                        <div class="ps-2">
                                            <h6 class="fs-5">Coming Soon !</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Cashback Per Barang -->

                        <!-- Cashback Total Invoice -->
                        <div class="col-xxl-2 col-md-6">
                            <div class="card info-card sales-card rounded-4">
                                <div class="card-body-custom">
                                    <h5 class="card-title fs-6">Cashback Total Invoice</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon-custom rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cash"></i>
                                        </div>
                                        <div class="ps-2">
                                            <h6 class="fs-5">Coming Soon !</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Cashback Total Invoice -->

                        <!-- Cashback Pajak -->
                        <div class="col-xxl-2 col-md-6">
                            <div class="card info-card sales-card rounded-4">
                                <div class="card-body-custom">
                                    <h5 class="card-title fs-6">Cashback Pajak</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon-custom rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cash"></i>
                                        </div>
                                        <div class="ps-2">
                                            <h6 class="fs-5">Coming Soon !</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Cashback Pajak -->

                        <!-- Cashback Pengiriman -->
                        <div class="col-xxl-2 col-md-6">
                            <div class="card info-card sales-card rounded-4">
                                <div class="card-body-custom">
                                    <h5 class="card-title fs-6">Cashback Pengiriman</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon-custom rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cash"></i>
                                        </div>
                                        <div class="ps-2">
                                            <h6 class="fs-5">Coming Soon !</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Cashback Pengiriman -->

                        <!-- Cashback Pelunasan -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="card info-card sales-card rounded-4">
                                <div class="card-body-custom">
                                    <h5 class="card-title fs-6">Cashback Pelunasan</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon-custom rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cash"></i>
                                        </div>
                                        <div class="ps-2">
                                            <h6 class="fs-5">Coming Soon !</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Cashback Pelunasan -->
                    </div>
                </div>

                <!-- Data Transalsi Customer -->
                <div class="col-12">
                    <div class="card recent-sales">
                        <div class="card-body mt-3">
                            <!-- Custom botton export and search -->
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="card mb-3 me-2 mt-2 transparent-card">
                                    <button class="icon btn btn-sm btn-primary" href="#" data-bs-toggle="dropdown" title="Filter Data">
                                        <i class="bi bi-funnel-fill"> Filter Data</i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-start dropdown-menu-arrow p-3" style="width: 250px;">
                                        <li><button class="dropdown-item" id="hari_ini">Hari Ini</button></li>
                                        <li><button class="dropdown-item" id="minggu_ini">Minggu Ini</button></li>
                                        <li><button class="dropdown-item" id="bulan_ini">Bulan Ini</button></li>
                                        <li><button class="dropdown-item" id="bulan_kemarin">Bulan Kemarin</button></li>
                                        <li><button class="dropdown-item" id="tahun_ini">Tahun Ini</button></li>
                                        <li><button class="dropdown-item" id="tahun_kemarin">Tahun Kemarin</button></li>
                                        <li id="dateRangePicker" class="date-range-picker">
                                            <input type="text" id="dateRange" class="form-control mb-3 text-center" placeholder="<?php echo $placeholder_input_date ?>">
                                        </li>
                                        <button type="button" class="btn btn-danger form-control" id="reset">Reset</button>
                                    </ul>
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
                                        <tr class="text-white"  style="background-color: navy;">
                                            <th class="text-center text-nowrap p-3">No</th>
                                            <th class="text-center text-nowrap p-3">Nama Customer</th>
                                            <th class="text-center text-nowrap p-3">Transaksi Selesai</th>
                                            <th class="text-center text-nowrap p-3">Transaksi Pending</th>
                                            <th class="text-center text-nowrap p-3">Transaksi Komplain</th>
                                            <th class="text-center text-nowrap p-3">Total Nominal Transaksi (Rp)</th>
                                            <th class="text-center text-nowrap p-3">Total Nominal Pending (Rp)</th>
                                            <th class="text-center text-nowrap p-3">Total Nominal Tagihan (Rp)</th>
                                            <th class="text-center text-nowrap p-3">Total Belum Bayar (Rp)</th>
                                            <th class="text-center text-nowrap p-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php  
                                        while ($data = mysqli_fetch_array($query)) {
                                            $total_nominal_selesai = $data['total_nominal_inv_selesai'];
                                            $total_nominal_bayar = $data['total_bayar'];
                                            $sisa_nominal_tagihan =  $total_nominal_selesai - $total_nominal_bayar;
                                            $date_now = date('Y-m-d');
                                            $sisa_tagihan = $data['selisih_bayar_dan_nominal'];
                                            $id_cs = $data['id_cs'];
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no ?></td>
                                            <td class="text-nowrap" style="width: 400px;"><?php echo $data['nama_cs'] ?></td>
                                            <td class="text-nowrap text-center"><?php echo $data['total_transaksi_selesai'] ?></td>
                                            <td class="text-nowrap text-center"><?php echo $data['total_transaksi_belum_selesai'] ?></td>
                                            <td class="text-nowrap text-center">
                                                <?php 
                                                    $sql_komplain = $connect->query("SELECT DISTINCT
                                                                                        COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                                                                                        komplain.id_komplain,
                                                                                        spk.id_inv,
                                                                                        spk.id_customer
                                                                                    FROM spk_reg AS spk
                                                                                    LEFT JOIN inv_komplain komplain ON (komplain.id_inv = spk.id_inv) 
                                                                                    LEFT JOIN inv_nonppn nonppn ON spk.id_inv = nonppn.id_inv_nonppn
                                                                                    LEFT JOIN inv_ppn ppn ON spk.id_inv = ppn.id_inv_ppn
                                                                                    LEFT JOIN inv_bum bum ON spk.id_inv = bum.id_inv_bum
                                                                                    WHERE $sort_data AND komplain.id_komplain IS NOT NULL AND spk.id_customer = '$id_cs'
                                                                                    GROUP BY spk.id_inv");
                                                    $total_komplain =  mysqli_num_rows($sql_komplain);
                                                    echo $total_komplain;
                                                ?>
                                            </td>
                                            <td class="text-nowrap text-end" data-order="<?php echo $data['total_nominal_inv_selesai']; ?>">
                                                <?php echo number_format($data['total_nominal_inv_selesai'],0,'.','.') ?>
                                            </td>
                                            <td class="text-nowrap text-end" data-order="<?php echo $data['total_nominal_inv_belum_selesai']; ?>">
                                                <?php echo number_format($data['total_nominal_inv_belum_selesai'],0,'.','.') ?>
                                            </td>
                                            <td class="text-nowrap text-end" data-order="<?php echo $sisa_tagihan ?>">
                                                <?php echo number_format($sisa_tagihan,0,'.','.') ?>
                                            </td>
                                            <td class="text-nowrap text-end" data-order="<?php echo $sisa_nominal_tagihan ?>">
                                                <?php echo number_format($sisa_nominal_tagihan,0,'.','.') ?>
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <a href="detail-invoice-customer.php?cs=<?php echo base64_encode($data['id_cs']) ?>&sort_data=tahun_ini" class="btn btn-primary btn-sm" title="Detail Invoice">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php $no++ ?>
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
            </div><!-- End Left side columns -->
        </section>
    </main>
    <!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php"?>
    <script src="../assets/js/datatable-custom-noexport.js"></script>
</body>

</html>

<!-- Filter date range -->
<script>
   $(document).ready(function () {
        var sortData = "<?php echo $get_sort ?>";

        // Periksa URL saat ini
        var currentUrl = new URL(window.location.href);
        var currentSortData = currentUrl.searchParams.get('sort_data');

        if (sortData == 'hari_ini') {
            $("#dropdownButton").text('Hari Ini');
            $("#hari_ini").addClass("active").prop('disabled', true);
        } else if (sortData == 'minggu_ini') {
            $("#dropdownButton").text('Minggu Ini');
            $("#minggu_ini").addClass("active").prop('disabled', true);
        } else if (sortData == 'bulan_ini') {
            $("#dropdownButton").text('Bulan Ini');
            $("#bulan_ini").addClass("active").prop('disabled', true);
        } else if (sortData == 'bulan_kemarin') {
            $("#dropdownButton").text('Bulan Kemarin');
            $("#bulan_kemarin").addClass("active").prop('disabled', true);
        } else if (sortData == 'tahun_ini') {
            $("#dropdownButton").text('Tahun Ini');
            $("#tahun_ini").addClass("active").prop('disabled', true);
        } else if (sortData == 'tahun_kemarin') {
            $("#dropdownButton").text('Tahun Kemarin');
            $("#tahun_kemarin").addClass("active").prop('disabled', true);
        } else if (sortData == 'date_range') {
            var textDate = "<?php echo $text_date_dropdown ?>";
            $("#dropdownButton").text(textDate);
        }

        // Event handler untuk mengubah sort_data tanpa menghapus parameter lain
        $(".dropdown-item").on("click", function () {
            var newSortData = $(this).data("value"); // Ambil value dari dropdown item

            // Update hanya parameter "sort_data"
            currentUrl.searchParams.set('sort_data', newSortData);

            // Redirect ke URL yang diperbarui
            window.location.href = currentUrl.toString();
        });
    });
</script>

<script>
    $(document).ready(function() {
        $("#hari_ini").click(function() {
            var url = "<?php echo $url; ?>&sort_data=hari_ini"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#minggu_ini").click(function() {
            var url = "<?php echo $url; ?>&sort_data=minggu_ini"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#bulan_ini").click(function() {
            var url = "<?php echo $url; ?>&sort_data=bulan_ini"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#bulan_kemarin").click(function() {
            var url = "<?php echo $url; ?>&sort_data=bulan_kemarin"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#tahun_ini").click(function() {
            var url = "<?php echo $url; ?>&sort_data=tahun_ini"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#tahun_kemarin").click(function() {
            var url = "<?php echo $url; ?>&sort_data=tahun_kemarin"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#reset").click(function() {
            var url = "<?php echo $url; ?>&sort_data=bulan_ini"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });   

    // Filter untuk date range
    $(document).ready(function () {
        // Initialize flatpickr date range picker with custom date format and daterange mode
        var dateRangePicker = flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "d/m/Y",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 1) {
                    var startDate = selectedDates[0];
                    var maxEndDate = new Date(startDate.getTime() + 31 * 24 * 60 * 60 * 1000); // Tanggal maksimal 90 hari setelah tanggal mulai

                    instance.set('minDate', startDate); // Setel tanggal minimal ke tanggal mulai
                    instance.set('maxDate', maxEndDate); // Setel tanggal maksimal ke 30 hari setelah tanggal mulai
                } else {
                    // Reset batas tanggal setelah memilih rentang tanggal
                    instance.set('minDate', null);
                    instance.set('maxDate', null);
                    redirectToUrl(); // Redirect ke URL setelah memilih rentang tanggal
                }
            }
        });

        // Function to check if a string is a valid date
        function isValidDate(dateString) {
            // Parse the date string and check if it's a valid date
            var date = new Date(dateString);
            return !isNaN(date.getTime());
        }

        // Function to redirect to URL with selected date range
        function redirectToUrl() {
            var selectedDates = dateRangePicker.selectedDates;
            if (selectedDates.length === 2) {
                var formattedStartDate = selectedDates[0].toLocaleDateString('en-GB');
                var formattedEndDate = selectedDates[1].toLocaleDateString('en-GB');

                var key = "<?php echo $key ?>";

                // Melakukan request AJAX untuk memanggil file PHP
                $.ajax({
                    url: '../ajax/function-enkripsi.php',
                    type: 'POST',
                    data: {
                        formattedStartDate: formattedStartDate,
                        formattedEndDate: formattedEndDate,
                        key: key
                    },
                    success: function(response) {
                        // response sudah berupa objek JavaScript jika tipe konten respons adalah JSON
                        var encryptedStartDate = response.startDate;
                        var encryptedEndDate = response.endDate;

                        var url = "<?php echo $url; ?>&sort_data=date_range&start_date=" + encodeURIComponent(encryptedStartDate) + "&end_date=" + encodeURIComponent(encryptedEndDate);
                        window.location.href = url;
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error(error);
                    }
                });
            }
        }

    });
</script>
<!-- Kode untuk mengatur colapse aktif saat tampilan mobile -->
<script>
    $(document).ready(function () {
        // Fungsi untuk mengubah ikon saat collapse dibuka atau ditutup
        function toggleArrowIcon(collapseId, iconId) {
            const collapseElement = $(collapseId);
            const arrowIcon = $(iconId);

            // Periksa apakah collapse terbuka atau tertutup
            collapseElement.on('show.bs.collapse', function () {
                // Ganti ikon ke panah ke bawah saat terbuka
                arrowIcon.removeClass('bi-caret-right-fill').addClass('bi-caret-down-fill');
            });

            collapseElement.on('hide.bs.collapse', function () {
                // Ganti ikon ke panah ke kanan saat tertutup
                arrowIcon.removeClass('bi-caret-down-fill').addClass('bi-caret-right-fill');
            });
        }

        // Terapkan fungsi untuk kedua collapse
        toggleArrowIcon('#rekapTransaksi', '#arrow-icon-transaksi');
        toggleArrowIcon('#rekapCashback', '#arrow-icon-cashback');

        // Logika untuk memastikan hanya satu collapse yang terbuka
        $('#rekapTransaksi').on('show.bs.collapse', function () {
            // Tutup #rekapCashback ketika #rekapTransaksi dibuka
            $('#rekapCashback').collapse('hide');
        });

        $('#rekapCashback').on('show.bs.collapse', function () {
            // Tutup #rekapTransaksi ketika #rekapCashback dibuka
            $('#rekapTransaksi').collapse('hide');
        });
    });
</script>