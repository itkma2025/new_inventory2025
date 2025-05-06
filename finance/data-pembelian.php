<?php
$page = 'pembelian';
require_once "../akses.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <?php include "page/head.php";?>
    <link href="../assets/vendor/lightbox/dist/css/lightgallery.css" rel="stylesheet"/>
    <link href="../assets/css/img-hover.css" rel="stylesheet"/>
    <style>
        th{
            padding-top: 15px !important;
            padding-bottom: 15px !important;
            padding-left: 25px !important;
            padding-right: 35px !important;
            text-align: center !important;
            white-space: nowrap !important;
            margin: 10px !important;
            background-color: navy !important;
        }
        /* Custom styling for the date inputs */
        .form-control[type="date"] {
            appearance: none;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Optional: Adjust the date input height and font-size */
        .form-control[type="date"] {
            height: 38px;
            font-size: 14px;
        }

        .form-control.error {
            border-color: #dc3545;
        }

        .form-select.error {
            border-color: #dc3545 !important;
        }

        .form-check-input.error {
            border-color: #dc3545 !important;
        }

        .error {
            border-color: #dc3545 !important;
        }

        .error-message {
            color: #dc3545;  
        }

        /* Adjust the position of the dropdown */
        .dropdown {
            display: inline-block;
            position: relative;
        }

        /* Adjust the style of the dropdown menu items */
        .dropdown-menu {
            min-width: 350px;
            padding: 20px;
        }

        .dropdown-item{
            text-align: center;
            border: 1px solid #ced4da;
            margin-bottom: 10px;
        }

        .separator {
            display: inline-block;
            width: 40px; /* Atur panjang pemisah sesuai keinginan */
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            color: #333; /* Ubah warna sesuai keinginan */
        }

        .disabled-select{
            pointer-events: none;
            background-color: #0d6efd;
            color: white;
        }

        .btn-filter{
            width: 349px;
        }

        .transparent-card {
            background-color: transparent !important;
            border: none; /* Jika Anda juga ingin menghapus border */
            box-shadow: none;
        }

        .input-container {
            position: relative;
            display: inline-block;
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
        @media screen and (max-width: 1800px) {
            .justify-content-start{
                justify-content: space-between;
            }
            .col-md-3{
                width: 349px;
            }
        }

        @media screen and (max-width: 1200px) {
            .justify-content-start{
                justify-content: space-between;
            }
            .col-md-3{
                width: 349px;
            }
        }

        @media screen and (max-width: 880px) {
            .justify-content-start{
                justify-content: space-between;
            }
            .col-md-3{
                width: 349px;
            }
        }
        @media screen and (max-width: 825px) {
            .justify-content-start{
                justify-content: space-between;
            }

            .btn-filter{
                width: 300px;
            }
            .col-md-3{
                width: 300px;
            }
        }

        @media screen and (max-width: 727px) {
            .justify-content-start{
                justify-content: space-between;
            }

            .btn-filter{
                width: 250px;
            }
            .col-md-3{
                width: 250px;
            }

            .card{
                width: 100%;
                margin:  0 0 12px 0 !important;
            }
        }
        @media screen and (max-width: 460px) {
            .btn-filter{
                width: 250px;
            }

            .col-md-3{
                width: 250px;
            }

            label{
                font-size: 14px;
            }
            .form-select{
                font-size: 14px;
            }
            .btn{
                font-size: 14px;
            }
        }
        @media screen and (max-width: 390px) {
            .btn-filter{
                width: 250px;
            }

            .col-md-3{
                width: 250px;
            }
        }
        @media screen and (max-width: 330px) {
            .btn-filter{
                width: 200px;
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
        <section class="section dashboard">
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="card p-3">
                <div class="card-header">
                    <h5 class="text-center fw-bold">Invoice Pembelian Produk Lokal</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-start flex-wrap">
                        <div class="card mb-2 me-2 transparent-card">
                            <?php
                                // Mendapatkan bagian dari URL yang berisi parameter GET
                                $queryString = $_SERVER['QUERY_STRING'];

                                // Daftar parameter yang ingin dihapus
                                $parametersToRemove = ['status_pembelian', 'status_pembayaran'];

                                // Simpan nilai-nilai filter dalam variabel terpisah
                                $dateRangeFilter = '';
                                $statusBayarFilter = '';
                                $jenisInvFilter = '';
                                $statusTagihanFilter = '';

                                // Loop melalui daftar parameter dan hapus dari URL
                                foreach ($parametersToRemove as $parameter) {
                                    $queryString = preg_replace('/' . $parameter . '=[^&]+&?/', '', $queryString);
                                }

                                // Fungsi untuk menambahkan atau mengganti nilai parameter dalam URL
                                function addOrReplaceParameter($queryString, $paramName, $paramValue = '') {
                                    // Membersihkan duplikasi tanda & sebelum menambahkan parameter baru
                                    $queryString = rtrim($queryString, '&');

                                    // Hapus parameter yang memiliki nama sama sebelum menambahkan yang baru
                                    $queryString = preg_replace('/' . $paramName . '=[^&]+&?/', '', $queryString);

                                    if (!empty($paramValue)) {
                                        // Jika nilai parameter tidak kosong, tambahkan parameter ke URL
                                        $queryString .= (empty($queryString) ? '' : '&') . $paramName . '=' . $paramValue;
                                    }

                                    return $queryString;
                                }

                                // Memeriksa apakah parameter date_range sudah ada dalam URL
                                if (strpos($queryString, 'date_range') === false) {
                                    // Jika tidak ada, tambahkan parameter date_range ke URL
                                    $queryString = (empty($queryString) ? '' : $queryString . '&') . 'date_range=year';
                                }

                                // Menyimpan nilai-nilai filter yang telah diaplikasikan
                                $dateRangeFilter = isset($_GET['date_range']) ? $_GET['date_range'] : '';
                                $statusBayarFilter = isset($_GET['status_pembayaran']) ? $_GET['status_pembayaran'] : '';
                                $jenisInvFilter = isset($_GET['jenis_inv']) ? $_GET['jenis_inv'] : '';
                                $statusPembelianFilter = isset($_GET['status_pembelian']) ? $_GET['status_pembelian'] : '';

                                // Menambah atau mengganti nilai parameter status_bayar dalam URL
                                $queryString = addOrReplaceParameter($queryString, 'status_pembayaran', $statusBayarFilter);

                                // Menambah atau mengganti nilai parameter jenis_inv dalam URL
                                $queryString = addOrReplaceParameter($queryString, 'jenis_inv', $jenisInvFilter);

                                // Menambah atau mengganti nilai parameter status_tagihan dalam URL
                                $queryString = addOrReplaceParameter($queryString, 'status_pembelian', $statusPembelianFilter);

                                // echo $queryString;
                            ?>

                            <!-- Mengganti date_range dan mempertahankan nilai-nilai filter yang telah diaplikasikan -->
                            <?php
                                if (!empty($dateRangeFilter)) {
                                    $queryString = addOrReplaceParameter($queryString, 'date_range', $dateRangeFilter);
                                }
                            ?>
                            <label>Filter Tanggal Pembelian :</label>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle btn-filter" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php
                                    // Menentukan teks yang ditampilkan berdasarkan nilai dari parameter date_range
                                    $selectedOption = isset($_GET['date_range']) ? $_GET['date_range'] : 'today';
                                    if ($selectedOption === "today") {
                                        echo "Hari ini";
                                    } elseif ($selectedOption === "weekly") {
                                        echo "Minggu ini";
                                    } elseif ($selectedOption === "monthly") {
                                        echo "Bulan ini";
                                    } elseif ($selectedOption === "lastMonth") {
                                        echo "Bulan Kemarin";
                                    } elseif ($selectedOption === "year") {
                                        echo "Tahun ini";
                                    } elseif ($selectedOption === "lastyear") {
                                        echo "Tahun Lalu";
                                    } else {
                                        echo "Pilih Tanggal";
                                    }
                                    ?>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <form action="" method="GET" class="form-group newsletter-group" id="resetLink">
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'today' ? 'active' : ''; ?>" href="?<?php echo $queryString ?>&date_range=today">Hari ini</a>
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'weekly' ? 'active' : ''; ?>" href="?<?php echo $queryString ?>&date_range=weekly">Minggu ini</a>
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'monthly' ? 'active' : ''; ?>" href="?<?php echo $queryString ?>&date_range=monthly">Bulan ini</a>
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'lastMonth' ? 'active' : ''; ?>" href="?<?php echo $queryString ?>&date_range=lastMonth">Bulan Kemarin</a>
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'year' ? 'active' : ''; ?>" href="?<?php echo $queryString ?>&date_range=year">Tahun ini</a>
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'lastyear' ? 'active' : ''; ?>" href="?<?php echo $queryString ?>&date_range=lastyear">Tahun Lalu</a>
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'pilihTanggal' ? 'active' : ''; ?>">Pilih Tanggal</a>
                                    </form>
                                    <li><hr class="dropdown-divider"></li>
                                    <form action="" method="GET" class="form-group newsletter-group" id="dateForm">
                                    <div class="row p-2">
                                        <div class="col-md-6 mb-3">
                                            <label>From</label>
                                            <input type="date" id="startDate" class="form-control form-control-md date-picker" placeholder="dd/mm/yyyy" name="start_date">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>To</label>
                                            <input type="date" id="endDate" class="form-control form-control-md date-picker" placeholder="dd/mm/yyyy" name="end_date">
                                        </div>
                                        <input type="hidden" name="date_range" value="pilihTanggal">
                                    </div>
                                    
                                    <!-- Add the submit button with name="tampilkan" -->
                                    <a href="finance-inv.php?date_range=monthly" name="tampilkan" class="custom-dropdown-item dropdown-item rounded bg-danger text-white" id="resetLink">Reset</a>
                                    </form>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const endDateInput = document.getElementById('endDate');
                                            const startDateInput = document.getElementById('startDate');
                                            const dateForm = document.getElementById('dateForm');
                                            const resetLink = document.getElementById('resetLink');

                                            // Cek apakah data tanggal tersimpan di localStorage
                                            const savedStartDate = localStorage.getItem('startDate');
                                            const savedEndDate = localStorage.getItem('endDate');

                                            if (savedStartDate) {
                                                startDateInput.value = savedStartDate;
                                            }

                                            if (savedEndDate) {
                                                endDateInput.value = savedEndDate;
                                            }

                                            startDateInput.addEventListener('change', () => {
                                                const startDateValue = new Date(startDateInput.value);
                                                const maxEndDateValue = new Date(startDateValue);
                                                maxEndDateValue.setDate(maxEndDateValue.getDate() + 30);

                                                endDateInput.value = ''; // Reset nilai endDate

                                                endDateInput.min = startDateValue.toISOString().split('T')[0];
                                                endDateInput.max = maxEndDateValue.toISOString().split('T')[0];

                                                endDateInput.disabled = false; // Aktifkan kembali input endDate
                                            });

                                            endDateInput.addEventListener('change', () => {
                                                const startDateValue = new Date(startDateInput.value);
                                                const endDateValue = new Date(endDateInput.value);

                                                const daysDifference = Math.floor((endDateValue - startDateValue) / (1000 * 60 * 60 * 24));

                                                if (daysDifference > 30) {
                                                    endDateInput.value = '';
                                                }

                                                startDateInput.value = startDateValue.toISOString().split('T')[0]; // Menampilkan pada field startDate
                                                endDateInput.value = endDateValue.toISOString().split('T')[0]; // Menampilkan pada field endDate

                                                const queryParams = new URLSearchParams({
                                                    start_date: startDateValue.toISOString().split('T')[0],
                                                    end_date: endDateValue.toISOString().split('T')[0],
                                                    date_range: 'pilihTanggal'
                                                });

                                                const newUrl = `finance-inv.php?${queryParams.toString()}`;

                                                dateForm.action = newUrl;
                                                dateForm.submit();

                                                // Simpan tanggal ke localStorage
                                                localStorage.setItem('startDate', startDateInput.value);
                                                localStorage.setItem('endDate', endDateInput.value);
                                            });

                                            resetLink.addEventListener('click', () => {
                                                // Hapus data dari localStorage
                                                localStorage.removeItem('startDate');
                                                localStorage.removeItem('endDate');

                                                // Hapus nilai dari field input
                                                startDateInput.value = '';
                                                endDateInput.value = '';
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-2 me-2 transparent-card">
                            <label>Status Pembelian :</label>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" style="min-width: 100px" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php
                                    // Menentukan teks yang ditampilkan berdasarkan nilai dari parameter date_range
                                    $statusPembelian = isset($_GET['status_pembelian']) ? $_GET['status_pembelian'] : 'Semua';
                                    if ($statusPembelian === "Semua" || $statusPembelian === "") {
                                    echo "Semua Status Pembelian";
                                    } elseif ($statusPembelian === "Belum Diterima") {
                                    echo "Belum Diterima";
                                    } elseif ($statusPembelian === "Sudah Diterima") {
                                    echo "Sudah Diterima";
                                    }
                                ?>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <form action="" method="GET" class="form-group newsletter-group" id="resetLink">
                                        <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['status_pembelian']) && $_GET['status_pembelian'] === '' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&status_pembelian=">Semua</a>
                                        <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['status_pembelian']) && $_GET['status_pembelian'] === 'Belum Diterima' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&status_pembelian=Belum Diterima">Belum Diterima</a>
                                        <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['status_pembelian']) && $_GET['status_pembelian'] === 'Sudah Diterima' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&status_pembelian=Sudah Diterima">Sudah Diterima</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-2 me-2 transparent-card">
                            <label>Status Pembayaran :</label>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" style="min-width: 170px" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php
                                    // Menentukan teks yang ditampilkan berdasarkan nilai dari parameter date_range
                                    $statusPembayaran = isset($_GET['status_pembayaran']) ? $_GET['status_pembayaran'] : 'Semua';
                                    if ($statusPembayaran === "Semua" || $statusPembayaran === "") {
                                    echo "Semua Status Pembayaran";
                                    } elseif ($statusPembayaran === "Belum Bayar") {
                                    echo "Belum Bayar";
                                    } elseif ($statusPembayaran === "Sudah Bayar") {
                                    echo "Sudah Bayar";
                                    }
                                ?>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <form action="" method="GET" class="form-group newsletter-group" id="resetLink">
                                        <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['status_pembayaran']) && $_GET['status_pembayaran'] === '' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&status_pembayaran=">Semua</a>
                                        <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['status_pembayaran']) && $_GET['status_pembayaran'] === 'Belum Bayar' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&status_pembayaran=Belum Bayar">Belum Bayar</a>
                                        <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['status_pembayaran']) && $_GET['status_pembayaran'] === 'Sudah Bayar' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&status_pembayaran=Sudah Bayar">Sudah Bayar</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-start flex-wrap">
                        <div class="card mb-2 me-2 transparent-card">
                            <a href="form-pembelian.php" class="btn btn-primary btn-md"><i class="bi bi-plus-circle"></i> Tambah data pembelian</a>
                        </div>
                    </div>
                    <!-- Custom botton export and search -->
                    <div class="d-flex justify-content-between flex-wrap">
                        <div class="card mb-3 me-2 mt-2 transparent-card">
                            <div class="btn-group" role="group" aria-label="Second group" id="export-file">
                            <button type="button" class="btn btn-secondary" id="btnPrint" title="Cetak">
                                <i class="bi bi-printer"></i>
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnExcel" title="Export to Excel">
                                <i class="bi bi-file-earmark-excel"></i>
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnCsv" title="Export to CSV">
                                <i class="bi bi-filetype-csv"></i>
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnPdf" title="Export to PDF">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </button>
                            </div>
                        </div>
                        <div class="card mb-3 me-2 transparent-card">
                            <div class="input-container">
                                <input type="text" class="form-control" placeholder="Cari Data" id="cari-data">
                                <button type="button" class="text-secondary" id="resetButton">
                                    <i class="bi bi-x fs-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- End Custom botton export and search -->
                    <!-- Table Responsive -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tableExportNew">
                            <thead>
                                    <tr class="text-white">
                                        <th>No</th>
                                        <th>No. Transaksi Pembelian</th>
                                        <th>No. Faktur Pembelian</th>
                                        <th>Tgl. Pembelian</th>
                                        <th>Nama Supplier</th>
                                        <th>Total Pembelian</th>
                                        <th>Tgl. Tempo</th>
                                        <th>Status Pembelian</th>
                                        <th>Status Pembayaran</th>
                                        <th>Aksi</th>
                                    </tr>
                            </thead>
                            <tbody>
                                    <?php  
                                        require_once "../function/function-enkripsi.php";
                                        $no = 1;
                                        $sort_option ="";
                                        if(isset($_GET['date_range']))
                                        {
                                            if($_GET['date_range'] == "today")
                                            {
                                                $sort_option = "DATE(STR_TO_DATE(pb.tgl_pembelian, '%d/%m/%Y')) = CURDATE()";
                                            }

                                            elseif($_GET['date_range'] == "weekly")
                                            {
                                                $sort_option = "
                                                                WEEK(STR_TO_DATE(pb.tgl_pembelian, '%d/%m/%Y')) = WEEK(CURDATE())
                                                                AND YEAR(STR_TO_DATE(pb.tgl_pembelian, '%d/%m/%Y')) = YEAR(CURDATE())
                                                            ";
                                            }

                                            elseif($_GET['date_range'] == "monthly")
                                            {

                                                $sort_option = "
                                                                MONTH(STR_TO_DATE(pb.tgl_pembelian, '%d/%m/%Y')) = MONTH(CURDATE())
                                                                AND YEAR(STR_TO_DATE(pb.tgl_pembelian, '%d/%m/%Y')) = YEAR(CURDATE())
                                                            "; 
                                                
                                            }

                                            elseif($_GET['date_range'] == "lastMonth")
                                            {
                                                $sort_option = " 
                                                                MONTH(STR_TO_DATE(pb.tgl_pembelian, '%d/%m/%Y')) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                                                                AND YEAR(STR_TO_DATE(pb.tgl_pembelian, '%d/%m/%Y')) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                                                                ";  
                                            }

                                            elseif($_GET['date_range'] == "year")
                                            {
                                                $sort_option = "YEAR(STR_TO_DATE(pb.tgl_pembelian, '%d/%m/%Y')) = YEAR(CURDATE())";
                                            }

                                            elseif($_GET['date_range'] == "lastyear")
                                            {
                                                $sort_option = "YEAR(STR_TO_DATE(pb.tgl_pembelian, '%d/%m/%Y')) = YEAR(CURDATE()) - 1";
                                            } 

                                            elseif($_GET['date_range'] == "pilihTanggal")
                                            {

                                                if (isset($_GET["start_date"]) && isset($_GET["end_date"])) {
                                                    $dt1 = $_GET["start_date"];
                                                    $dt2 = $_GET["end_date"];
                                                    $format_dt1 = date('d/m/Y', strtotime($dt1));
                                                    $format_dt2 = date('d/m/Y', strtotime($dt2));
                                                    $sort_option .= "STR_TO_DATE(pb.tgl_pembelian, '%d/%m/%Y') BETWEEN STR_TO_DATE('$format_dt1', '%d/%m/%Y') AND STR_TO_DATE('$format_dt2', '%d/%m/%Y')";
                                                }
                                            } 

                                        }

                                        if(isset($_GET['status_pembayaran'])){
                                            if($_GET['status_pembayaran'] == "Belum Bayar"){
                                                $sort_option .= "AND status_pembayaran = '0'";
                                            }else if ($_GET['status_pembayaran'] == "Sudah Bayar"){
                                                $sort_option .= "AND status_pembayaran = '1'";
                                            }
                                        }


                                        $sql_pembelian = $connect->query(" SELECT 
                                                                                pb.id_inv_pembelian AS id_inv,
                                                                                pb.no_trx,
                                                                                pb.no_inv,
                                                                                pb.tgl_pembelian,
                                                                                pb.tgl_tempo,
                                                                                pb.total_pembelian,
                                                                                pb.status_pembelian,
                                                                                pb.status_pembayaran,
                                                                                pb.status_delete,
                                                                                sp.nama_sp,
                                                                                COALESCE(skp.id_status_kirim, '0') AS id_status_kirim
                                                                            FROM inv_pembelian_lokal AS pb
                                                                            LEFT JOIN tb_supplier sp ON (pb.id_sp = sp.id_sp)
                                                                            LEFT JOIN status_kirim_pembelian skp ON (pb.id_inv_pembelian = skp.id_inv_pembelian)
                                                                            WHERE $sort_option AND status_delete = '0' ORDER BY no_trx ASC
                                                                        ");
                                        $totalData = mysqli_num_rows($sql_pembelian);
                                        while($data_pembelian = mysqli_fetch_array($sql_pembelian)){
                                            $id_inv_pembelian = $data_pembelian['id_inv']; 
                                            $status_pembelian = "";
                                            $status_pembayaran = "";
                                            if($data_pembelian['status_pembelian'] == '0'){
                                                $status_pembelian = "Belum Diterima";
                                            } else {
                                                $status_pembelian = "Sudah Diterima";
                                            }

                                            if($data_pembelian['status_pembayaran'] == '0'){
                                                $status_pembayaran = "Belum Bayar";
                                            } else {
                                                $status_pembayaran = "Sudah Bayar";
                                            }
                                            $id_status_kirim = $data_pembelian['id_status_kirim'];
                                            $no_inv = "";
                                            if($data_pembelian['no_inv'] == ''){
                                                $no_inv = "Tidak Ada";
                                            } else {
                                                $no_inv = $data_pembelian['no_inv'];
                                            }

                                            $tgl_tempo = "";
                                            if($data_pembelian['tgl_tempo'] != ''){
                                                $tgl_tempo = $data_pembelian['tgl_tempo'];
                                            } else {
                                                $tgl_tempo = "Tidak Ada Tempo";
                                            }
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $no; ?></td>
                                        <td class="text-center"><?php echo $data_pembelian['no_trx']; ?></td>
                                        <td class="text-center"><?php echo $no_inv; ?></td>
                                        <td class="text-center"><?php echo $data_pembelian['tgl_pembelian']; ?></td>
                                        <td class="text-start"><?php echo $data_pembelian['nama_sp']; ?></td>
                                        <td class="text-end">Rp <?php echo number_format($data_pembelian['total_pembelian'],0,'.','.') ?></td>
                                        <td class="text-center"><?php echo $tgl_tempo; ?></td>
                                        <td class="text-center"><?php echo $status_pembelian; ?></td>
                                        <td class="text-center"><?php echo $status_pembayaran; ?></td>
                                        <td class="text-center text-nowrap">
                                            <a href="detail-produk-pembelian-lokal.php?id='<?php echo base64_encode($data_pembelian['id_inv']) ?>'" class="btn btn-primary btn-sm" title="Detail Pembelian"><i class="bi bi-eye"></i></a>
                                            <?php  
                                                $cek_bukti_terima = $connect->query("SELECT id_inv_pembelian FROM inv_bukti_terima_pembelian WHERE id_inv_pembelian = '$id_inv_pembelian'");
                                                $total_row_bukti = mysqli_num_rows($cek_bukti_terima);
                                                
                                                if($total_row_bukti == 0){
                                                    if($id_status_kirim == 0){
                                                    } else {
                                                        ?>
                                                            <button type="button" class="btn btn-success btn-sm btn-ubah-status" data-bs-toggle="modal" data-bs-target="#ubahStatusDiterima" data-id="<?php echo $data_pembelian['id_inv'] ?>" data-sp="<?php echo $data_pembelian['nama_sp']; ?>" data-nofaktur="<?php echo $no_inv; ?>" data-nominal="<?php echo number_format($data_pembelian['total_pembelian']) ?>" data-tglpembelian="<?php echo $data_pembelian['tgl_pembelian']; ?>">
                                                                <i class="bi bi-send"></i>
                                                            </button>
                                                        <?php
                                                    }
                                                    
                                                } else {
                                                    ?>
                                                        <!-- Bukti terima -->
                                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#buktiTerima" data-id="<?php echo $data_pembelian['id_inv'] ?>">
                                                        <i class="bi bi-info-circle"></i>
                                                        </button>
                                                    <?php
                                                }

                                                if($status_pembayaran != "Sudah Bayar"){
                                                    ?>
                                                        <!-- Bukti terima -->
                                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapus" data-id="<?php echo encrypt($data_pembelian['id_inv'], $key_finance) ?>" data-sp="<?php echo $data_pembelian['nama_sp']; ?>" data-notrx="<?php echo $data_pembelian['no_trx']; ?>">
                                                        <i class="bi bi-trash"></i>
                                                        </button>
                                                    <?php
                                                }
                                            ?>
                                            <!-- Button Diterima -->
                                        </td>
                                    </tr>
                                    <?php $no++; ?>
                                    <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table Responsive -->
                    <div class="text-start" id="totalData">
                       
                    </div>
                    <!-- Custom pagination -->
                    <nav>
                        <ul class="pagination" id="customPagination">
                        <!-- Pagination items will be inserted here by JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </section>
        <!-- Modal Diterima -->
        <div class="modal fade" id="ubahStatusDiterima" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Status Diterima</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="refreshPage()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3"><h5 class="fw-bold">Konfirmasi Penerimaan Barang</h5></div>
                        <form action="proses/status-kirim-pembelian.php" method="POST" enctype="multipart/form-data" id="myForm">
                            <?php  
                                $year = date('y');
                                $day = date('d');
                                $month = date('m');
                                $uuid = uuid();
                                $generate_uuid = "BP". $year . "" . $month . "" . $uuid . "" . $day ;
                            ?>
                            <input type="hidden" name="id_bukti_terima" class="form-control bg-light" value="<?php echo $generate_uuid ?>">
                            <input type="hidden" id="id_inv" name="id_inv" class="form-control bg-light">
                            <input type="hidden" id="sp" name="nama_sp" class="form-control bg-light">
                            <div class="mb-3">
                                <label class="fw-bold">No Faktur Pembelian</label>
                                <input type="text" id="no_faktur" name="no_faktur" class="form-control bg-light" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Nominal Pembelian</label>
                                <input type="text" id="nominal" class="form-control bg-light" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Tanggal Pembelian</label>
                                <input type="text" id="tgl_pembelian" class="form-control bg-light" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Tanggal Terima</label>
                                <input type="text" id="tgl" name="tgl_terima" class="form-control">
                                <small class="validasi tgl" style="display: none;"><div class="error-message"><i class="bi bi-exclamation-triangle-fill"></i> Tanggal kirim harus diisi!</div></small>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Bukti Terima (*)</label>
                            </div>
                            <div class="mb-3">
                                <input type="file" name="fileku1" id="fileku1" accept=".jpg, .jpeg, .png" onchange="compressAndPreviewImage(event)">
                                <small class="validasi fileku1" style="display: none;"><div class="error-message"><i class="bi bi-exclamation-triangle-fill"></i> Gambar harus dipilih!</div></small>
                            </div>
                            <div class="mb-3 preview-image" id="imagePreview"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="refreshPage()">Tutup</button>
                                <button type="submit" class="btn btn-primary" name="upload">Proses Diterima</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Modal Diterima -->
            <!-- date picker with flatpick -->
            <script type="text/javascript">
                // Fungsi untuk menjalankan skrip JavaScript saat modal ditampilkan
                function initModalScript() {
                    var inputTgl = document.getElementById("tgl");
                    var datepicker = flatpickr(inputTgl, {
                        dateFormat: "d/m/Y",
                        onChange: function(selectedDates, dateStr, instance) {
                            console.log("Nilai setelah memilih tanggal:", dateStr);
                        }
                    });

                    // Menambahkan event listener pada form saat disubmit
                    document.getElementById('myForm').addEventListener('submit', function(event) {
                        var fileInput = document.getElementById('fileku1');

                        // Debugging: Check the value of fileInput
                        console.log("Nilai fileInput:", fileInput.value);

                        // Cek apakah nilai tanggal telah dipilih dan file telah diunggah
                        if (!datepicker.selectedDates.length) {
                            // Jika tidak, tampilkan pesan validasi dan hentikan pengiriman formulir
                            document.querySelector('.tgl').style.display = 'block';
                            event.preventDefault(); // Mencegah pengiriman formulir
                        }else if(!fileInput.value){
                            document.querySelector('.fileku1').style.display = 'block';
                            event.preventDefault(); // Mencegah pengiriman formulir
                        } else {
                            // Sembunyikan pesan validasi jika input sudah diisi
                            document.querySelector('.tgl').style.display = 'none';
                            document.querySelector('.fileku1').style.display = 'none';
                        }
                    });
                    // Log nilai sebelum memilih tanggal saat modal ditampilkan
                    console.log("Nilai sebelum memilih tanggal:", inputTgl.value);
                }

                // Menambahkan event listener pada modal yang akan dijalankan saat modal ditampilkan
                document.getElementById('ubahStatusDiterima').addEventListener('shown.bs.modal', function () {
                    // Panggil fungsi initModalScript saat modal ditampilkan
                    initModalScript();
                });
            </script>
            <!-- end date picker -->
        </div>
        <!-- kode JS Dikirim -->
        <?php include "page/upload-img.php";  ?>
        <style>
            .preview-image {
                max-width: 100%;
                height: auto;
            }
        </style>
        <!-- kode JS Dikirim -->
        <!-- End Modal Bukti Terima -->

        <!-- Modal bukti terima-->
        <div class="modal fade" id="buktiTerima" data-bs-backdrop="static"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Bukti Terima</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="result"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="hapus" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            Apakah ada yakin ingin menghapus data pembelian dengan <b>No.Transaksi : <span id="no_trx"></span> (<span id="sp"></span>) </b>?<br><br>
                            <b>Notes: Data akan hilang dan jika di hapus di sarankan untuk membuat data pembelian baru.</b>
                        </p>
                        <form action="proses/hapus-data-pembelian.php" method="post">
                            <input type="hidden" id="id_inv" name="id_inv" class="form-control bg-light" readonly>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary" name="hapus">Ya, Hapus</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </main><!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
    <!-- Datatable Custom -->
    <script src="../assets/js/datatable-custom-button.js"></script>
    <!-- Script untuk lighbox -->
    <script src="../assets/vendor/lightbox/dist/js/picturefill.min.js"></script>
    <script src="../assets/vendor/lightbox/dist/js/lightgallery-all.min.js"></script>
    <script src="../assets/vendor/lightbox/lib/jquery.mousewheel.min.js"></script>
</body>

</html>
<?php  
    function uuid() {
        $data = openssl_random_pseudo_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s', str_split(bin2hex($data), 4));
    }
?>
<script>
    // untuk menampilkan data pada atribut <td>
    $('#ubahStatusDiterima').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var sp = button.data('sp');
        var noFaktur = button.data('nofaktur');
        var nominal = button.data('nominal');
        var tglPembelian = button.data('tglpembelian');
        
        var modal = $(this);
        modal.find('.modal-body #id_inv').val(id);
        modal.find('.modal-body #sp').val(sp);
        modal.find('.modal-body #no_faktur').val(noFaktur);
        modal.find('.modal-body #nominal').val(nominal);
        modal.find('.modal-body #tgl_pembelian').val(tglPembelian);
    })
</script>
<script>
    // untuk menampilkan data pada atribut <td>
    $('#hapus').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var sp = button.data('sp');
        var noTrx = button.data('notrx');
        
        var modal = $(this);
        modal.find('.modal-body #id_inv').val(id);
        modal.find('.modal-body #sp').html(sp);
        modal.find('.modal-body #no_trx').html(noTrx);
    })
</script>
<script>
    // Menangani klik pada elemen dengan kelas lg-close menggunakan jQuery
    $(document).ready(function() {
        $('#close-modal').click(function() {
            // Memuat ulang halaman
            location.reload();
        });
    });
</script>
<script>
    function refreshPage() {
        location.reload();
    }
</script>

<script>
    $('#buktiTerima').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        
        var modal = $(this);
        modal.find('.modal-body #id_inv').val(id);
        
        // Menggunakan AJAX untuk mengirim nilai id_inv ke file PHP
        $.ajax({
            type: 'POST', // Metode pengiriman data, dalam hal ini menggunakan POST
            url: 'bukti-terima-pembelian.php',  // Ganti dengan path yang benar
            data: { id_inv: id }, // Data yang akan dikirim ke server, dalam hal ini nilai id_inv
            success: function(response) {
                // Memasukkan hasil respons ke dalam modal body
                modal.find('.modal-body #result').html(response);
            },
            error: function(error) {
                // Fungsi ini akan dijalankan jika terjadi kesalahan saat melakukan permintaan
                // Handle error
                console.error(error);
            }
        });
    });
</script>
