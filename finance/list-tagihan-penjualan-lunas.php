<?php
    $page = 'list-tagihan';
    $page2 = 'tagihan-penjualan';
    require_once "../akses.php";
    require_once "../function/function-enkripsi.php";
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'page/head.php'; ?>

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <style>
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

        .dropdown-item {
            text-align: center;
            border: 1px solid #ced4da;
            margin-bottom: 10px;
        }

        .separator {
            display: inline-block;
            width: 40px;
            /* Atur panjang pemisah sesuai keinginan */
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            color: #333;
            /* Ubah warna sesuai keinginan */
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

    <div id="content">
        <main id="main" class="main">
            <!-- Loading -->
            <div class="loader loader">
                <div class="loading">
                    <img src="img/loading.gif" width="200px" height="auto">
                </div>
            </div>
            <!-- End Loading -->
            <div class="pagetitle">
                <h1>List Tagihan Penjualan</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item active">List Tagihan Penjualan</li>
                    </ol>
                </nav>
            </div><!-- End Page Title -->
            <section>
                <!-- SWEET ALERT -->
                <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) {
                                                            echo $_SESSION['info'];
                                                        }
                                                        unset($_SESSION['info']); ?>"></div>
                <!-- END SWEET ALERT -->
                <div class="card">
                    <!-- Kode untuk perhitungan CB -->
                    <?php  
                        $sql_cb = "SELECT DISTINCT
                                    fnc.id_finance,
                                    fnc.id_inv,
                                    fnc.jenis_inv,
                                    byr.id_finance, 
                                    SUM(byr.total_bayar) AS total_pembayaran,
                                    SUM(byr.total_potongan) AS total_potongan,
                                    SUM(byr_cb.total_bayar) AS total_pembayaran_cb,     
                                    spk.no_po,
                                    ppn.total_ppn AS total_ppn,
                                    COALESCE(cb_nonppn.status_cb, cb_ppn.status_cb, cb_bum.status_cb) AS status_cb,
                                    COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                    COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                                    COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                                    COALESCE(nonppn.kategori_inv, ppn.kategori_inv, bum.kategori_inv) AS kategori_inv,
                                    COALESCE(nonppn.total_inv, ppn.total_inv, bum.total_inv) AS total_inv,
                                    COALESCE(nonppn.ongkir_free, ppn.ongkir_free, bum.ongkir_free) AS ongkir_free
                                FROM finance AS fnc
                                LEFT JOIN inv_nonppn nonppn ON fnc.id_inv = nonppn.id_inv_nonppn
                                LEFT JOIN inv_ppn ppn ON fnc.id_inv = ppn.id_inv_ppn
                                LEFT JOIN inv_bum bum ON fnc.id_inv = bum.id_inv_bum
                                LEFT JOIN cashback_nonppn cb_nonppn ON fnc.id_inv = cb_nonppn.id_inv
                                LEFT JOIN cashback_ppn cb_ppn ON fnc.id_inv = cb_ppn.id_inv
                                LEFT JOIN cashback_bum cb_bum ON fnc.id_inv = cb_bum.id_inv
                                LEFT JOIN spk_reg spk ON fnc.id_inv = spk.id_inv
                                LEFT JOIN finance_bayar byr ON (fnc.id_finance = byr.id_finance)
                                LEFT JOIN finance_bayar_cb byr_cb ON (fnc.id_finance = byr_cb.id_finance)
                                WHERE COALESCE(cb_nonppn.status_cb, cb_ppn.status_cb, cb_bum.status_cb) = '1'
                                GROUP BY fnc.id_finance";
                        $query_cb = $connect->query($sql_cb);
                        while ($data_cb = mysqli_fetch_array($query_cb)) {
                            $jenis_inv = $data_cb['jenis_inv'];
                            $id_inv = $data_cb['id_inv'];
                            $free_ongkir = $data_cb['free_ongkir'];
                            $ongkir_free = $data_cb['ongkir_free'];
                            $total_inv = $data_cb['total_inv'];
                            $total_ppn = $data_cb['total_ppn'];
                            $grand_total_inv += $total_inv;
                            $total_bayar = $data_cb['total_pembayaran'] + $data_cb['total_potongan'];
                            $sisa_tagihan = $total_inv - $total_bayar;
                            $total_pembayaran_cb = $data_cb['total_pembayaran_cb'];
                            if ($jenis_inv == 'nonppn') {
                                $sql_status_cb = $connect->query("SELECT status_cb, jenis_cb, cb_total_inv, cb_pajak FROM cashback_nonppn WHERE id_inv = '$id_inv'");
                                $data_status_cb = mysqli_fetch_array($sql_status_cb);
                                $status_cb = $data_status_cb['status_cb'] ?? '';
                                $jenis_cb = $data_status_cb['jenis_cb'] ?? '';
                                // Pecah data berdasarkan koma
                                $jenisCbArray = explode(",", $jenis_cb);

                                // Gabungkan data dengan tanda petik
                                $result_jenis_cb = "'" . implode("','", $jenisCbArray) . "'";
                                
                                // Menampilkan keterangan cashback
                                $ket_cb = $connect->query("SELECT ket_cashback FROM keterangan_cashback WHERE id_ket_cashback IN ($result_jenis_cb)");
                            } else if ($jenis_inv == 'ppn') {
                                $action = "proses/proses-invoice-ppn.php";
                                $sql_status_cb = $connect->query("SELECT status_cb, jenis_cb, cb_total_inv, cb_pajak FROM cashback_ppn WHERE id_inv = '$id_inv'");
                                $data_status_cb = mysqli_fetch_array($sql_status_cb);
                                $status_cb = $data_status_cb['status_cb'] ?? '';
                                $jenis_cb = $data_status_cb['jenis_cb'] ?? '';
                                // Pecah data berdasarkan koma
                                $jenisCbArray = explode(",", $jenis_cb);

                                // Gabungkan data dengan tanda petik
                                $result_jenis_cb = "'" . implode("','", $jenisCbArray) . "'";
                                
                                // Menampilkan keterangan cashback
                                $ket_cb = $connect->query("SELECT ket_cashback FROM keterangan_cashback WHERE id_ket_cashback IN ($result_jenis_cb)");
                            } else if ($jenis_inv == 'bum') {
                                $sql_status_cb = $connect->query("SELECT status_cb, jenis_cb, cb_total_inv, cb_pajak FROM cashback_bum WHERE id_inv = '$id_inv'");
                                $data_status_cb = mysqli_fetch_array($sql_status_cb);
                                $status_cb = $data_status_cb['status_cb'] ?? '';
                                $jenis_cb = $data_status_cb['jenis_cb'] ?? '';
                                // Pecah data berdasarkan koma
                                $jenisCbArray = explode(",", $jenis_cb);

                                // Gabungkan data dengan tanda petik
                                $result_jenis_cb = "'" . implode("','", $jenisCbArray) . "'";
                                
                                // Menampilkan keterangan cashback
                                $ket_cb = $connect->query("SELECT ket_cashback FROM keterangan_cashback WHERE id_ket_cashback IN ($result_jenis_cb)");
                            } else {
                                ?>
                                    <script type="text/javascript">
                                        window.location.href = "../404.php";
                                    </script>
                                <?php
                            } 

                            $cashback_values = [];
                            while($data_ket_cb =  mysqli_fetch_array($ket_cb)){
                                $cashback_values[] = $data_ket_cb['ket_cashback']; // Menyimpan setiap nilai ke dalam array
                            }
                            $cek_ket_cb = implode(", ", $cashback_values); // Menggabungkan semua nilai menjadi satu string, dipisahkan dengan koma
                            $jumlah_jenis_cb = count($cashback_values); // Menghitung jumlah elemen dalam array

                            // query untuk menampilkan data transaksi
                            $total_cb_per_produk = 0;
                            $sql_trx = $connect->query("SELECT 
                                                            spk.id_spk_reg,
                                                            spk.id_inv,
                                                            fnc.id_finance,
                                                            fnc.total_cb AS fnc_total_cb,
                                                            trx.total_cb AS trx_total_cb
                                                        FROM spk_reg AS spk
                                                        LEFT JOIN transaksi_produk_reg trx ON (spk.id_spk_reg = trx.id_spk)
                                                        LEFT JOIN finance fnc ON (spk.id_inv = fnc.id_inv)
                                                        WHERE spk.id_inv = '$id_inv'");
                            while($data_trx = mysqli_fetch_array($sql_trx)){
                                $fnc_total_cb = $data_trx['fnc_total_cb'];
                                $total_cb_per_produk += $data_trx['trx_total_cb'];
                                $id_finance = $data_trx['id_finance'];
                                $totalTagihan = $total_inv;
                                $totalPpn = $total_ppn;
                                $cbPajak = $data_status_cb['cb_pajak'];
                                $cbPerBarang = $total_cb_per_produk;
                                $cbPengiriman = $ongkir_free;
                                $cbTotalInv = $data_status_cb['cb_total_inv'];
                                $totalTagihanCb = $totalTagihan - $totalPpn;
                                $hasilCbPajak = $totalPpn * $cbPajak / 100;
                                $hasilCbTotalInv = $totalTagihanCb * ($cbTotalInv / 100);
                                $tagihanCbBayar = $hasilCbPajak + $hasilCbTotalInv + $cbPerBarang;
                            }
                        ?>
                    <?php } ?>
                    <div class="card-body p-3">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="list-tagihan-penjualan.php?date_range=year" class="nav-link">Tagihan Belum Lunas</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#" class="nav-link active">Tagihan Lunas</a>
                            </li>
                        </ul>
                        <div class="col">
                            <div class="p-3">
                                <label>Filter Sesuai Periode :</label><br>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" style="min-width: 170px" data-bs-toggle="dropdown" aria-expanded="false">
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
                                            <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'today' ? 'active' : ''; ?>" href="?date_range=today">Hari ini</a>
                                            <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'weekly' ? 'active' : ''; ?>" href="?date_range=weekly">Minggu ini</a>
                                            <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'monthly' ? 'active' : ''; ?>" href="?date_range=monthly">Bulan ini</a>
                                            <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'lastMonth' ? 'active' : ''; ?>" href="?date_range=lastMonth">Bulan Kemarin</a>
                                            <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'year' ? 'active' : ''; ?>" href="?date_range=year">Tahun ini</a>
                                            <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'lastyear' ? 'active' : ''; ?>" href="?date_range=lastyear">Tahun Lalu</a>
                                            <a class="custom-dropdown-item dropdown-item rounded <?php echo (isset($_GET['date_range']) && $_GET['date_range'] === 'pilihTanggal') ? 'active' : ''; ?>">Pilih Tanggal</a>

                                        </form>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <form action="" method="GET" class="form-group newsletter-group" id="dateForm">
                                            <div class="row p-2">
                                                <div class="col-md-6 mb-3">
                                                    <label for="startDate">From</label>
                                                    <input type="date" id="startDate" class="form-control form-control-md date-picker" placeholder="dd/mm/yyyy" name="start_date">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="endDate">To</label>
                                                    <input type="date" id="endDate" class="form-control form-control-md date-picker" placeholder="dd/mm/yyyy" name="end_date">
                                                </div>
                                                <input type="hidden" name="date_range" value="pilihTanggal">
                                                <input type="hidden" name="cs" value="<?php echo base64_encode($id_cs) ?>">
                                            </div>

                                            <!-- Add the submit button with name="tampilkan" -->
                                            <a href="list-tagihan-penjualan-lunas.php?date_range=year" name="tampilkan" class="custom-dropdown-item dropdown-item rounded bg-danger text-white" id="resetLink">Reset</a>
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
                                                        // isi kode php jika filter berada di dalam detail yang memiliki get id misal id_cs
                                                        // cs: ''
                                                    });

                                                    const newUrl = `list-tagihan-penjualan-lunas.php?${queryParams.toString()}`;

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
                        </div>
                        <div class="table-responsive">
                            <table class="table table-responsive table-striped" id="tableExport">
                                <thead>
                                    <tr class="text-white" style="background-color: navy;">
                                        <td class="p-3 text-center text-nowrap">No</td>
                                        <td class="p-3 text-center text-nowrap">No. Tagihan</td>
                                        <td class="p-3 text-center text-nowrap">Tgl. Tagihan</td>
                                        <td class="p-3 text-center text-nowrap">Nama Customer</td>
                                        <td class="p-3 text-center text-nowrap">Total Tagihan</td>
                                        <td class="p-3 text-center text-nowrap">Total Bayar</td>
                                        <td class="p-3 text-center text-nowrap">Total Potongan</td>
                                        <td class="p-3 text-center text-nowrap">Total Cashback</td>
                                        <td class="p-3 text-center text-nowrap">Sisa Tagihan</td>
                                        <td class="p-3 text-center text-nowrap">Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include "koneksi.php";
                                    $no = 1;
                                    $sort_option = "";
                                    $today = date('d/m/Y');
                                    $startWeek = date('d/m/Y', strtotime("-1 week"));
                                    $endWeek = date('d/m/Y', strtotime("now"));
                                    $thisWeekStart = date('d/m/Y', strtotime('last sunday'));
                                    $thisWeekEnd = date('d/m/Y', strtotime('next sunday'));
                                    $thisMonth = date('m');

                                    // Kode Khusus Untuk Last Mont
                                    // Dapatkan tanggal saat ini
                                    $tanggalSaatIni = new DateTime();

                                    // Set tanggal ke awal bulan
                                    $tanggalSaatIni->setDate($tanggalSaatIni->format('Y'), $tanggalSaatIni->format('m'), 1);

                                    // Kurangkan satu bulan dari tanggal saat ini
                                    $tanggalSaatIni->modify('-1 month');

                                    // Dapatkan bulan dalam format numerik (dengan angka nol di depan jika berlaku)
                                    $lastMonth = $tanggalSaatIni->format('m');

                                    // Tampilkan nilai bulan sebelumnya
                                    $thisYear = date('Y');
                                    $lastYear = date("Y", strtotime("-1 year"));
                                    if (isset($_GET['date_range'])) {
                                        if ($_GET['date_range'] == "today") {
                                            $sort_option = "DATE(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = CURDATE()";
                                        } elseif ($_GET['date_range'] == "weekly") {
                                            $sort_option = "
                                            WEEK(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = WEEK(CURDATE())
                                            AND YEAR(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = YEAR(CURDATE())
                                        ";
                                        } elseif ($_GET['date_range'] == "monthly") {

                                            $sort_option = "
                                            MONTH(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = MONTH(CURDATE())
                                            AND YEAR(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = YEAR(CURDATE())
                                        ";
                                        } elseif ($_GET['date_range'] == "lastMonth") {
                                            $sort_option = " 
                                            MONTH(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                                            AND YEAR(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                                            ";
                                        } elseif ($_GET['date_range'] == "year") {
                                            $sort_option = "YEAR(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = YEAR(CURDATE())";
                                        } elseif ($_GET['date_range'] == "lastyear") {
                                            $sort_option = "YEAR(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = YEAR(CURDATE()) - 1";
                                        }
                                    }
                                    if (isset($_GET["start_date"]) && isset($_GET["end_date"])) {
                                        $dt1 = $_GET["start_date"];
                                        $dt2 = $_GET["end_date"];
                                        $format_dt1 = date('d/m/Y', strtotime($dt1));
                                        $format_dt2 = date('d/m/Y', strtotime($dt2));
                                        $sort_option = "STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y') BETWEEN STR_TO_DATE('$format_dt1', '%d/%m/%Y') AND STR_TO_DATE('$format_dt2', '%d/%m/%Y')";
                                        // Lakukan sesuatu dengan $sort_option, misalnya memproses data dari database
                                    }
                                    $sql = "SELECT  
                                                tagihan.id_tagihan AS tagihan_id,
                                                tagihan.no_tagihan,
                                                STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y') AS tgl_tagihan,
                                                tagihan.jenis_faktur,
                                                tagihan.total_tagihan,
                                                tagihan.created_date,
                                                fnc.status_lunas,
                                                fnc.status_lunas_cb,
                                                SUM(fnc.total_cb) AS total_cb, 
                                                COALESCE(byr.total_bayar, 0) AS total_pembayaran,
                                                COALESCE(byr_cb.total_bayar, 0) AS total_pembayaran_cb,    
                                                COALESCE(byr.total_potongan, 0) AS total_potongan,
                                                COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv                           
                                            FROM finance_tagihan AS tagihan
                                            LEFT JOIN finance fnc ON (tagihan.id_tagihan = fnc.id_tagihan)
                                            LEFT JOIN (
                                                SELECT 
                                                id_tagihan, 
                                                SUM(total_bayar) AS total_bayar,
                                                SUM(total_potongan) AS total_potongan
                                                FROM finance_bayar
                                                GROUP BY id_tagihan
                                            ) byr ON (tagihan.id_tagihan = byr.id_tagihan)
                                            LEFT JOIN (
                                                SELECT 
                                                id_tagihan, 
                                                SUM(total_bayar) AS total_bayar
                                                FROM finance_bayar_cb
                                                GROUP BY id_tagihan
                                            ) byr_cb ON (tagihan.id_tagihan = byr_cb.id_tagihan)
                                            LEFT JOIN inv_nonppn nonppn ON (fnc.id_inv = nonppn.id_inv_nonppn)
                                            LEFT JOIN inv_ppn ppn ON (fnc.id_inv = ppn.id_inv_ppn)
                                            LEFT JOIN inv_bum bum ON (fnc.id_inv = bum.id_inv_bum)
                                            WHERE $sort_option
                                            GROUP BY tagihan.id_tagihan
                                            HAVING tagihan.total_tagihan = (total_pembayaran + total_potongan) AND SUM(fnc.total_cb) = total_pembayaran_cb
                                            ORDER BY tagihan.created_date ASC";
                                    $query = mysqli_query($connect, $sql);
                                    while ($data = mysqli_fetch_array($query)) {
                                        $total_pembayaran_cb = $data['total_pembayaran_cb'];
                                        $total_bayar = $data['total_pembayaran'] + $data['total_potongan'];
                                        $total_tagihan = $data['total_tagihan'];
                                        $total_sisa_tagihan = $total_tagihan - $total_bayar;
                                        $tgl_tagihan = $data['tgl_tagihan'];
                                        $no_tagihan = $data['no_tagihan'];
                                        $sisa_tagihan_cb = $data['total_cb'] - $total_pembayaran_cb;
                                        $total_tagihan_akhir = $total_sisa_tagihan + $sisa_tagihan_cb;
                                        $total_pembayaran = "";
                                        $total_potongan = "";
                                        $total_cb = "";
                                        if($data['total_pembayaran'] == '0'){
                                            $total_pembayaran = "Belum Ada Pembayaran";
                                        } else {
                                            $total_pembayaran = number_format($data['total_pembayaran'], 0, '.', '.');
                                        }

                                        if($data['total_potongan'] == '0'){
                                            $total_potongan = "Tidak Ada Potongan";
                                        } else {
                                            $total_potongan = number_format($data['total_potongan'], 0, '.', '.');
                                        }

                                        if($data['total_cb'] == '0'){
                                            $total_cb = "Tidak Ada Potongan";
                                        } else {
                                            $total_cb = number_format($data['total_cb'], 0, '.', '.');
                                        }
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no; ?></td>
                                            <td class="text-center text-nowrap">
                                                <?php echo $data['no_tagihan'] ?><br>
                                                <?php
                                                if ($data['jenis_faktur'] != '') {
                                                    echo "(" . $data['jenis_faktur'] . ")";
                                                }

                                                ?>
                                            </td>
                                            <td class="text-end"><?php echo date('d/m/Y', strtotime($data['tgl_tagihan'])) ?></td>
                                            <td><?php echo $data['cs_inv']; ?></td>
                                            <td class="text-end"><?php echo number_format($data['total_tagihan'], 0, '.', '.') ?></td>
                                            <td class="text-end" id="total_bayar"><?php echo $total_pembayaran; ?></td>
                                            <td class="text-end" id="total_potongan"><?php echo $total_potongan; ?></td>
                                            <td class="text-end" id="total_cb"><?php echo $total_cb; ?></td>
                                            <td class="text-end">
                                                <?php
                                                if ($total_sisa_tagihan == 0) {
                                                    echo '
                                                    <button type="button" class="btn btn-secondary btn-sm mb-2">
                                                        <i class="bi bi-check-circle"></i> Lunas
                                                    </button>';
                                                } else {
                                                    echo number_format($total_sisa_tagihan, 0, '.', '.');
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <a href="detail-bill.php?id=<?php echo encrypt($data['tagihan_id'], $key_finance) ?>" class="btn btn-primary btn-sm" title="Detail Tagihan"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                        <?php $no++ ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </main><!-- End #main -->
    </div>
    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>

</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
 $(document).ready(function() {
    // Kode untuk kondisi 0
    const totalBayar = $('#total_bayar').html();
    const totalPotongan = $('#total_potongan').html();
    const totalCb = $('#total_cb').html();

    if(totalBayar === 'Belum Ada Pembayaran'){
      $('#total_bayar').addClass('text-center');
      $('#total_bayar').addClass('text-nowrap');
      $('#total_bayar').removeClass('text-end');
    } else {
      $('#total_bayar').removeClass('text-center');
      $('#total_bayar').removeClass('text-nowrap');
      $('#total_bayar').addClass('text-end');
    }

    if(totalPotongan === 'Tidak Ada Potongan'){
      $('#total_potongan').addClass('text-center');
      $('#total_potongan').addClass('text-nowrap');
      $('#total_potongan').removeClass('text-end');
    } else {
      $('#total_potongan').removeClass('text-center');
      $('#total_potongan').removeClass('text-nowrap');
      $('#total_potongan').addClass('text-end');
    }

    if(totalCb === 'Tidak Ada Cashback'){
      $('#total_cb').addClass('text-center');
      $('#total_cb').addClass('text-nowrap');
      $('#total_cb').removeClass('text-end');
    } else {
      $('#total_cb').removeClass('text-center');
      $('#total_cb').removeClass('text-nowrap');
      $('#total_cb').addClass('text-end');
    }
  });
</script>