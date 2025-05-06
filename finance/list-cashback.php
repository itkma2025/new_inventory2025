<?php
  $page = 'list-tagihan';
  $page2 = 'tagihan-cb';
  require_once "../akses.php";
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
  <link href="../assets/vendor/lightbox/dist/css/lightgallery.css" rel="stylesheet"/>
  <style>
        table{
            width: 100%;
        }
        p{
            margin-bottom: 0px !important;
        }

        .btn-sm{
            padding-top: 0px !important;
            padding-bottom: 0px !important;
            padding-left: 6px !important;
            padding-right: 6px !important;
        }

        td{
            text-align: left;
            padding: 2px;
        }

        th{
            text-align: center;
            padding: 4px;
            color: white;
        }

        .card-body {
            padding: 0 20px 0px 10px;
        }

        .dropdown-item-list.active {
            background-color: #6c757d;
            color: white;
        }

        .margin-left{
            margin-left: 10.255em !important;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5); /* Adjust the opacity as needed */
        }

        .filter-wrapper {
           margin-right: 15px !important;
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

        /* Untuk merubah bawaan datatable */
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

        .transparent-card {
            background: none !important;
        }

        @media (max-width: 576px) {
            .filter-wrapper {
                width: 100% !important;
                margin-right: 0 !important;
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
        <div class="loader loader">
            <div class="loading">
            <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div>
        <!-- End Loading -->
        <section>
            <!-- Alert -->
            <?php
                if (isset($_SESSION['info'])) {
                    echo '<div class="info-data" data-infodata="' . $_SESSION['info'] . '"></div>';
                    unset($_SESSION['info']);
                }
            ?>
            <!-- End alert -->
            <div class="card p-3">
                <?php  
                    $session_url = "list-cashback.php?";
                    $_SESSION['url'] = $session_url;   
                    $url = $_SESSION['url']; 
                    include "query/fnc-cashback.php";
                ?>
                <div class="card-header text-center">
                    <h5>Data Cashback Management</h5>
                </div>
                <div class="card-body mt-3">
                    <div class="d-flex flex-wrap justify-content-start">
                        <div class="mb-3 filter-wrapper" style="width: 250px;">
                            <label>Filter jenis invoice</label>
                            <select id="filterJenisInv" class="form-select">
                                <option value="">Pilih...</option>
                                <option value="nonppn">Non PPN</option>
                                <option value="ppn">PPN</option>
                                <option value="bum">BUM</option>
                            </select>
                        </div>
                        <div class="mb-3 filter-wrapper" style="width: 250px;">
                            <label>Filter jenis cashback</label>
                            <select id="filterJenisCb" class="form-select">
                                <option value="">Pilih...</option>
                                <option value="Per Barang">Per Barang</option>
                                <option value="Pengiriman">Pengiriman</option>
                                <option value="Pajak">Pajak</option>
                                <option value="Total Invoice">Total Invoice</option>
                            </select>
                        </div>
                    </div>
                    <!-- Custom botton export and search -->
                    <div class="d-flex justify-content-between flex-wrap">
                        <div class="card mb-3 me-2 mt-2 shadow-none">
                            <button class="icon btn btn-sm btn-primary" href="#" data-bs-toggle="dropdown" title="Filter Data">
                                <i class="bi bi-funnel-fill fs-6"> Filter Data</i>
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
                                    <th class="text-center text-nowrap p-3">No Invoice</th>
                                    <th class="text-center text-nowrap p-3">Tgl Invoice</th>
                                    <th class="text-center text-nowrap p-3">Jenis Invoice</th>
                                    <th class="text-center text-nowrap p-3">No. PO</th>
                                    <th class="text-center text-nowrap p-3">Nama Customer</th>
                                    <th class="text-center text-nowrap p-3">Kat. Inv</th>
                                    <th class="text-center text-nowrap p-3">Jenis Cashback</th>
                                    <th class="text-center text-nowrap p-3">Total Invoice</th>
                                    <th class="text-center text-nowrap p-3">Total Cashback</th>
                                    <th class="text-center text-nowrap p-3">Status Pembayaran Cashback</th>
                                    <th class="text-center text-nowrap p-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php  
                                $no = 1;
                                while ($data = mysqli_fetch_array($query)) {
                                    $jenis_inv = $data['jenis_inv'];
                                    $id_inv = $data['id_inv'];
                                    $free_ongkir = $data['free_ongkir'];
                                    $ongkir_free = $data['ongkir_free'];
                                    $total_inv = $data['total_inv'];
                                    $total_ppn = $data['total_ppn'];
                                    $grand_total_inv += $total_inv;
                                    $total_bayar = $data['total_pembayaran'] + $data['total_potongan'];
                                    $sisa_tagihan = $total_inv - $total_bayar;
                                    $total_pembayaran_cb = $data['total_pembayaran_cb'];
                                    if ($jenis_inv == 'nonppn') {
                                        $action = "proses/proses-invoice-nonppn.php";
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
                                        $action = "proses/proses-invoice-bum.php";
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
                                                                    trx.total_cb
                                                                FROM spk_reg AS spk
                                                                LEFT JOIN transaksi_produk_reg trx ON (spk.id_spk_reg = trx.id_spk)
                                                                LEFT JOIN finance fnc ON (spk.id_inv = fnc.id_inv)
                                                                WHERE spk.id_inv = '$id_inv'");
                                    while($data_trx = mysqli_fetch_array($sql_trx)){
                                        $total_cb_per_produk += $data_trx['total_cb'];
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
                                        $history_payment = ""; // Inisialisasi variabel
                                        $status_bayar = "";
                                        if ($total_pembayaran_cb == 0) {
                                            $status_bayar = '<span class="badge bg-warning">Belum Bayar</span>';
                                        } else {
                                            // Set status pembayaran
                                            if ($total_pembayaran_cb == $tagihanCbBayar) {
                                                $status_bayar = '<span class="badge bg-success">Lunas</span>';
                                            } else {
                                                $status_bayar = '<span class="badge bg-secondary">Sudah Bayar</span>' .$id_finance;
                                            }
                                            // Tetapkan tombol history payment
                                            $history_payment = '<button class="btn btn-info btn-sm view_data mb-2" data-bs-toggle="modal" data-bs-target="#history" data-id="' . encrypt($id_finance, $key_finance) . '" data-cb="'. $tagihanCbBayar .'" title="History Payment"><i class="bi bi-card-checklist"></i></button>';
                                        }
                                    }
                                ?>
                                <tr>
                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data['no_inv']; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data['tgl_inv']; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $jenis_inv; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data['no_po']; ?></td>
                                    <td class="text-nowrap"><?php echo $data['cs_inv']; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data['kategori_inv']; ?></td>
                                    <td class="text-center text-nowrap">
                                        <button class="btn btn-secondary btn-sm rounded-circle p-2" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="<?php echo $cek_ket_cb ?>">
                                            <?php echo $jumlah_jenis_cb ?>
                                        </button>
                                    </td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data['total_inv'],0,'.','.'); ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($tagihanCbBayar,0,'.','.'); ?></td>
                                    <td class="text-center text-nowrap">
                                        <?php  
                                            if($total_pembayaran_cb == 0){
                                                ?>
                                                    <span class="badge bg-warning">Belum Bayar</span>
                                                <?php
                                            } else if($total_pembayaran_cb == $tagihanCbBayar){
                                                ?>
                                                    <span class="badge bg-success">Lunas</span>
                                                <?php
                                            } else {
                                                ?>
                                                    <span class="badge bg-secondary">Sudah Bayar</span>
                                                <?php
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?php echo $history_payment ?>
                                    </td>
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
        </section>
        <!-- Modal utama History -->
        <div class="modal fade" id="history" tabindex="-1" aria-labelledby="historyLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyLabel">History Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                    <div class="card-body" id="detail_id">
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
                </div>
            </div>
        </div>
        <!-- End Modal utama History -->
        <!-- Modal gambar History -->
        <div class="modal fade" id="bukti" data-bs-keyboard="false" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Bukti Transfer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="detail_bukti">
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal gambar History -->
    </main>
        
    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
    <!-- Script untuk datatable -->
    <script src="../assets/js/datatable-custom-noexport.js"></script>

    <!-- Script untuk lighbox -->
    <script src="../assets/vendor/lightbox/dist/js/picturefill.min.js"></script>
    <script src="../assets/vendor/lightbox/dist/js/lightgallery-all.min.js"></script>
    <script src="../assets/vendor/lightbox/lib/jquery.mousewheel.min.js"></script>
</body>
</html>
<!-- Filter date range -->
<script>
    $(document).ready(function () {
        var sortData = "<?php echo $get_sort ?>";
        // console.log(sortData);
        if (sortData == 'hari_ini'){
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Hari Ini');
            $("#hari_ini").addClass("active");
            $("#hari_ini").prop('disabled', true);
        } else if (sortData == 'minggu_ini'){
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Minggu Ini');
            $("#minggu_ini").addClass("active");
            $("#minggu_ini").prop('disabled', true);
        } else if (sortData == 'bulan_ini'){
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Bulan Ini');
            $("#bulan_ini").addClass("active");
            $("#bulan_ini").prop('disabled', true);
        } else if (sortData == 'bulan_kemarin'){
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Bulan Kemarin');
            $("#bulan_kemarin").addClass("active");
            $("#bulan_kemarin").prop('disabled', true);
        } else if (sortData == 'tahun_ini'){
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Tahun Ini');
            $("#tahun_ini").addClass("active");
            $("#tahun_ini").prop('disabled', true);
        } else if (sortData == 'tahun_kemarin'){
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Tahun Kemarin');
            $("#tahun_kemarin").addClass("active");
            $("#tahun_kemarin").prop('disabled', true);
        } else if (sortData == 'date_range'){
            // Function to update dropdown text with selected date range
            var textDate = "<?php echo $text_date_dropdown ?>";
            $("#dropdownButton").text(textDate);
            
        }
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

<!-- Filter untuk jenis Cashback -->
<script>
    $(document).ready(function () {
        // Inisialisasi DataTables
        var table = $('#tableNoExportNew').DataTable();

        // Filter Jenis Invoice
        $('#filterJenisInv').on('change', function () {
            var filterValue = $(this).val(); // Ambil nilai dari dropdown

            // Terapkan filter berdasarkan nilai dropdown ke kolom yang sesuai
            table
                .column(3) // Index kolom jenis invoice (mulai dari 0)
                .search(filterValue) // Filter data berdasarkan nilai dropdown
                .draw(); // Gambar ulang tabel
        });

        // Filter Jenis Cashback
        $('#filterJenisCb').on('change', function () {
            var filterValue = $(this).val(); // Ambil nilai dari dropdown

            // Terapkan filter menggunakan fungsi custom
            table.rows().every(function () {
                var $node = $(this.node());
                var title = $node.find('td:eq(7) button').attr('data-bs-title'); // Ambil nilai data-bs-title

                if (filterValue === "" || (title && title.includes(filterValue))) {
                    $node.show(); // Tampilkan baris jika cocok
                } else {
                    $node.hide(); // Sembunyikan baris jika tidak cocok
                }
            });

            table.draw(false); // Gambar ulang tabel tanpa reset pagination
        });

    });
</script>


<!-- Script Untuk Modal History -->
<script>
    $('#imageModal').on('show.bs.modal', function () {
        $('#history').modal('hide'); // Sembunyikan modal utama saat modal gambar ditampilkan
    });
    
    $('#bukti').on('hidden.bs.modal', function () {
        $('#history').modal('show'); // Tampilkan kembali modal utama saat modal gambar disembunyikan
    });
</script>
<!-- ============================================= -->
<!-- Untuk menampilkan data Histori pada modal -->
<script>
    $(document).ready(function(){
        $('.view_data').click(function(){
            var data_id = $(this).data("id");
            var data_cb = $(this).data("cb");
            console.log(data_id);
            $.ajax({
                url: "modal/history-cb.php",
                method: "POST",
                data: {
                        data_id: data_id,
                        data_cb: data_cb
                    },
                success: function(data){
                    $("#detail_id").html(data)
                    $("#history").modal('show')
                }
            })
        })
    })
</script>
<!--End Script Untuk Modal History -->

