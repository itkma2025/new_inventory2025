<?php
$page = 'list-cs';
include 'akses.php';
include 'finance/function/class-finance.php';
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
    <link rel="stylesheet" type="text/css" media="all" href="daterangepicker.css" />

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.min.js"></script>
    <script type="text/javascript" src="daterangepicker.js"></script>
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

        .dropdown-item{
        text-align: center;
        border: 1px solid #ced4da;
        margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- nav header -->
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include "page/sidebar.php" ?>
    <!-- end sidebar -->
    <main id="main" class="main">
        <!-- Loading -->
        <!-- <div class="loader loader">
            <div class="loading">
            <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div> -->
        <!-- End Loading -->
        <section>
            <div class="card shadow p-4">
                <div class="card-header text-center">
                    <?php  
                        include 'koneksi.php';
                        $id_cs = base64_decode($_GET['cs']);
                        $sql_cs = mysqli_query($connect, "SELECT nama_cs FROM tb_customer WHERE id_cs = '$id_cs'");
                        $data_cs = mysqli_fetch_array($sql_cs);
                    ?>
                    <h5>Detail Penjualan <?php echo $data_cs['nama_cs'] ?></h5>
                </div>
                <div class="row mb-5">
                    <div class="col-sm-4">
                        <div class="row row-cols-1 row-cols-lg-2 g-2 g-lg-3">
                            <div class="col">
                                <div class="p-3">
                                    <label>Filter Sesuai Periode :</label>
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
                                                <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'today' ? 'active' : ''; ?>" href="?date_range=today&cs=<?php echo base64_encode($id_cs) ?>">Hari ini</a>
                                                <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'weekly' ? 'active' : ''; ?>" href="?date_range=weekly&cs=<?php echo base64_encode($id_cs) ?>">Minggu ini</a>
                                                <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'monthly' ? 'active' : ''; ?>" href="?date_range=monthly&cs=<?php echo base64_encode($id_cs) ?>">Bulan ini</a>
                                                <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'lastMonth' ? 'active' : ''; ?>" href="?date_range=lastMonth&cs=<?php echo base64_encode($id_cs) ?>">Bulan Kemarin</a>
                                                <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'year' ? 'active' : ''; ?>" href="?date_range=year&cs=<?php echo base64_encode($id_cs) ?>">Tahun ini</a>
                                                <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'lastyear' ? 'active' : ''; ?>" href="?date_range=lastyear&cs=<?php echo base64_encode($id_cs) ?>">Tahun Lalu</a>
                                                <a class="custom-dropdown-item dropdown-item rounded <?php echo (isset($_GET['date_range']) && $_GET['date_range'] === 'pilihTanggal') ? 'active' : ''; ?>">Pilih Tanggal</a>

                                            </form>
                                            <li><hr class="dropdown-divider"></li>
                                            <form action="" method="GET" class="form-group newsletter-group" id="dateForm">
                                                <div class="row p-2">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="start_date">From</label>
                                                        <input type="date" id="startDate" class="form-control form-control-md date-picker" placeholder="dd/mm/yyyy" name="start_date">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="end_date">To</label>
                                                        <input type="date" id="endDate" class="form-control form-control-md date-picker" placeholder="dd/mm/yyyy" name="end_date">
                                                    </div>
                                                    <input type="hidden" name="date_range" value="pilihTanggal">
                                                    <input type="hidden" name="cs" value="<?php echo base64_encode($id_cs) ?>">
                                                </div>
                                                
                                                <!-- Add the submit button with name="tampilkan" -->
                                                <a href="detail-invoice-customer.php?date_range=weekly" name="tampilkan" class="custom-dropdown-item dropdown-item rounded bg-danger text-white" id="resetLink">Reset</a>
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
                                                            date_range: 'pilihTanggal',
                                                            cs: '<?php echo base64_encode($id_cs); ?>'
                                                        });

                                                        const newUrl = `detail-invoice-customer.php?${queryParams.toString()}`;

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
                            <div class="col">
                                <div class="p-3">
                                    <label>Filter Jenis Invoice :</label>
                                    <div class="input-group flex-nowrap">
                                        <select class="form-select" id="filterJenisInv">
                                            <option value="all">Semua</option>
                                            <option value="Non PPN">Non PPN</option>
                                            <option value="PPN">PPN</option>
                                            <option value="BUM">BUM</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="row row-cols-1 row-cols-lg-4 g-2 g-lg-3">
                            <div class="col">
                                <div class="p-3">
                                    <label>Filter Status Invoice :</label>
                                    <div class="input-group flex-nowrap">
                                        <select class="form-select" id="filterStatusTrx">
                                            <option value="all">Semua</option>
                                            <option value="Transaksi Selesai">Transaksi Selesai</option>
                                            <option value="Dikirim">Dikirim</option>
                                            <option value="Diterima">Diterima</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-3">
                                    <label>Filter Status Pembayaran :</label>
                                    <div class="input-group flex-nowrap">
                                        <select class="form-select" id="filterStatusPembayaran">
                                            <option value="all">Semua</option>
                                            <option value="Belum Bayar">Belum Bayar</option>
                                            <option value="Sudah Bayar">Sudah Bayar</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-3">
                                    <label>Filter Status Pelunasan :</label>
                                    <div class="input-group flex-nowrap">
                                        <select class="form-control" id="filterStatusLunas">
                                            <option value="all">Semua</option>
                                            <option value="Lunas">Lunas</option>
                                            <option value="Belum Lunas">Belum Lunas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="p-3">
                                    <label>Filter Status Tagihan :</label>
                                    <div class="input-group flex-nowrap">
                                        <select class="form-select" id="filterStatusTagihan">
                                            <option value="all">Semua</option>
                                            <option value="Sudah Ditagih">Sudah Ditagih</option>
                                            <option value="Belum Dibuat">Belum Ditagih</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="card-body">
                    <?php  include "finance/query/detail-inv-cs.php"; ?>
                    <div class="row">
                        <div class="col-sm p-2">
                            <div class="text-center border p-3">
                                <p><b>Total Transaksi Pembelian</b></p>
                                <p><?php echo $total_trx; ?></p>
                            </div>
                        </div>
                        <div class="col-sm p-2">
                            <div class="text-center border p-3">
                                <p><b>Total Nominal Transaksi</b></p>
                                <?php 
                                    $total_nominal_trx = 0;
                                    $trx_blm_bayar = 0; 
                                    while($data_nominal = mysqli_fetch_array($query_nominal)){
                                        $total_nominal_trx +=  $data_nominal['total_inv'];
                                        $total_nominal_bayar += $data_nominal['total_bayar'];
                                        $trx_blm_bayar = $total_nominal_trx - $total_nominal_bayar;
                                ?>
                                <?php } ?>
                                <p><?php echo number_format($total_nominal_trx) ?></p>
                            </div>
                        </div>
                        <div class="col-sm p-2">
                            <div class="text-center border p-3">
                                <p><b>Total Transaksi Belum Bayar</b></p>
                                <p><?php echo number_format($trx_blm_bayar) ?></p>
                            </div>
                        </div>
                        <div class="col-sm p-2">
                            <div class="text-center border p-3">
                                <p><b>Total Transaksi Jatuh Tempo</b></p>
                                <?php 
                                    $sql_trx_tempo = "  SELECT 
                                                            COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                                            STR_TO_DATE(COALESCE(NULLIF(nonppn.tgl_inv, ''), NULLIF(ppn.tgl_inv, ''), NULLIF(bum.tgl_inv, '')), '%d/%m/%Y') AS tgl_inv,
                                                            STR_TO_DATE(COALESCE(NULLIF(nonppn.tgl_tempo, ''), NULLIF(ppn.tgl_tempo, ''), NULLIF(bum.tgl_tempo, '')), '%d/%m/%Y') AS tgl_tempo,
                                                            fnc.total_inv AS total_inv,
                                                            SUM(COALESCE(fb.total_bayar, 0)) AS total_bayar
                                                        FROM finance AS fnc
                                                        LEFT JOIN inv_nonppn nonppn ON (fnc.id_inv = nonppn.id_inv_nonppn)
                                                        LEFT JOIN inv_ppn ppn ON (fnc.id_inv = ppn.id_inv_ppn)
                                                        LEFT JOIN inv_bum bum ON (fnc.id_inv = bum.id_inv_bum)
                                                        LEFT JOIN spk_reg ON (spk_reg.id_inv = fnc.id_inv)
                                                        LEFT JOIN finance_bayar fb ON (fnc.id_finance = fb.id_finance)
                                                        WHERE spk_reg.id_customer = '$id_cs' AND $sort_option
                                                        AND (
                                                            fnc.total_inv != COALESCE(fb.total_bayar, 0) OR
                                                            fb.total_bayar IS NULL
                                                        )
                                                        AND ( 
                                                            COALESCE(NULLIF(nonppn.tgl_tempo, ''), NULLIF(ppn.tgl_tempo, ''), NULLIF(bum.tgl_tempo, '')) IS NOT NULL 
                                                            AND STR_TO_DATE(COALESCE(NULLIF(nonppn.tgl_tempo, ''), NULLIF(ppn.tgl_tempo, ''), NULLIF(bum.tgl_tempo, '')), '%d/%m/%Y') <= CURDATE()
                                                        )
                                                        GROUP BY COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv)";

                                    $query_trx_tempo = mysqli_query($connect, $sql_trx_tempo);
                                    $total_data_trx = mysqli_num_rows($query_trx_tempo);

                                    // Inisialisasi variabel di luar loop
                                    $total_trx_inv = 0;
                                    $total_trx_bayar = 0;

                                    if ($total_data_trx != 0) {
                                        while ($data_trx_tempo = mysqli_fetch_array($query_trx_tempo)) {
                                            $total_trx_inv += $data_trx_tempo['total_inv'];
                                            $total_trx_bayar += $data_trx_tempo['total_bayar'];
                                        }
                                        $sisa_trx_tempo = $total_trx_inv - $total_trx_bayar;
                                        echo '<p>' . number_format($sisa_trx_tempo) . '</p>';
                                    } else {
                                        echo '<p>0</p>';
                                    }
                                ?> 
                            </div>
                        </div>
                        <div class="col-sm p-2">
                            <div class="text-center border p-3">
                                <p><b>Total Tagihan Belum Bayar</b></p>
                                <?php 
                                    $sql_tagihan_belum_bayar = "SELECT 
                                                                    COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                                                    STR_TO_DATE(COALESCE(NULLIF(nonppn.tgl_inv, ''), NULLIF(ppn.tgl_inv, ''), NULLIF(bum.tgl_inv, '')), '%d/%m/%Y') AS tgl_inv,
                                                                    STR_TO_DATE(COALESCE(NULLIF(nonppn.tgl_tempo, ''), NULLIF(ppn.tgl_tempo, ''), NULLIF(bum.tgl_tempo, '')), '%d/%m/%Y') AS tgl_tempo,
                                                                    fnc.total_inv AS total_inv,
                                                                    fnc.status_tagihan,
                                                                    fnc.status_lunas,
                                                                    SUM(COALESCE(fb.total_bayar, 0)) AS total_bayar
                                                                FROM finance AS fnc
                                                                LEFT JOIN inv_nonppn nonppn ON (fnc.id_inv = nonppn.id_inv_nonppn)
                                                                LEFT JOIN inv_ppn ppn ON (fnc.id_inv = ppn.id_inv_ppn)
                                                                LEFT JOIN inv_bum bum ON (fnc.id_inv = bum.id_inv_bum)
                                                                LEFT JOIN spk_reg ON (spk_reg.id_inv = fnc.id_inv)
                                                                LEFT JOIN finance_bayar fb ON (fnc.id_finance = fb.id_finance)
                                                                WHERE spk_reg.id_customer = '$id_cs' AND fnc.status_tagihan = '1' AND fnc.status_lunas = '0' AND $sort_option
                                                                GROUP BY COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv)";

                                    $query_tagihan_belum_bayar = mysqli_query($connect, $sql_tagihan_belum_bayar);
                                    $total_data_tagihan_belum_bayar = mysqli_num_rows($query_tagihan_belum_bayar);

                                    // Inisialisasi variabel di luar loop
                                    $total_tagihan_belum_bayar = 0;
                                    $total_bayar = 0;
                                    $total_inv = 0;

                                    if ($total_data_tagihan_belum_bayar != 0) {
                                        while ($data_tagihan = mysqli_fetch_array($query_tagihan_belum_bayar)) {
                                            $total_bayar += $data_tagihan['total_bayar'];
                                            $total_inv += $data_tagihan['total_inv'];
                                            $total_tagihan_belum_bayar = $total_inv - $total_bayar;
                                            
                                        }
                                        echo '<p>' . number_format($total_tagihan_belum_bayar) . '</p>';
                                    } else {
                                        echo '<p>0</p>';
                                    }
                                ?> 
                                <p></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <button class="btn btn-warning btn-detail mb-2" onclick="goBack()">
                        <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                    </button>
                    <table id="table1" class="table table-striped nowrap" style="width:100%">
                        <thead>
                            <tr class="text-white" style="background-color: navy;">
                                <td class="text-center p-3 text-nowrap">No</td>
                                <td class="text-center p-3 text-nowrap">No. Invoice</td>
                                <td class="text-center p-3 text-nowrap">Tgl. Invoice</td>
                                <td class="text-center p-3 text-nowrap">Tgl. Tempo</td>
                                <td class="text-center p-3 text-nowrap">Jenis Inv</td>
                                <td class="text-center p-3 text-nowrap">Nominal Transaksi</td>
                                <td class="text-center p-3 text-nowrap">Status Invoice</td>
                                <td class="text-center p-3 text-nowrap">Status Pembayaran</td>
                                <td class="text-center p-3 text-nowrap">Sudah Bayar</td>
                                <td class="text-center p-3 text-nowrap">Status Pelunasan</td>
                                <td class="text-center p-3 text-nowrap">Status Tempo</td>
                                <td class="text-center p-3 text-nowrap">Status Tagihan</td>
                                <td class="text-center p-3 text-nowrap">Aksi</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php  
                            include "koneksi.php";                   
                            while ($data = mysqli_fetch_array($query)) {
                                $id_inv = $data['id_inv'];
                                $id_inv_substr = substr($id_inv, 0, 3); // Mengambil 3 karakter pertama
                                $no_inv = $data['no_inv'];
                                $total_inv = $data['total_inv'];
                                $total_bayar = $data['total_bayar'];
                                $status_lunas = $total_inv - $total_bayar;
                                $cs_inv = $data['cs_inv'];
                                $tgl_inv = $data['tgl_inv'];
                                $tgl_tempo_cek = $data['tgl_tempo'];
                                $tgl_tempo = $data['tgl_tempo_convert'];
                                $status_trx = $data['status_trx'];
                                $status_lunas = $data['status_lunas'];
                                $date_now = date('Y-m-d');
                        ?>
                            <tr>
                                <td class="text-center text-nowrap"><?php echo $no ?></td>
                                <td class="text-nowrap"><?php echo $no_inv ?></td>
                                <td class="text-nowrap text-center"><?php echo date('d/m/Y', strtotime($tgl_inv)) ?></td>
                                <td class="text-nowrap text-center">
                                    <?php 
                                        if(!empty($tgl_tempo_cek)){
                                            echo date('d/m/Y', strtotime($tgl_tempo));
                                        } else {
                                            echo "Tidak Ada Tempo";
                                        }
                                    ?>
                                </td>
                                <td class="text-center text-nowrap">
                                    <?php  
                                        if($id_inv_substr == 'NON'){
                                            echo 'Non PPN';
                                        } else if($id_inv_substr == 'PPN'){
                                            echo 'PPN';
                                        } else if($id_inv_substr == 'BUM'){
                                            echo 'BUM';
                                        }
                                    ?>
                                </td>
                                <td class="text-nowrap text-center"><?php echo number_format($total_inv) ?></td> 
                                <td class="text-nowrap text-center"><?php echo $status_trx ?></td>    
                                <td class="text-nowrap text-center">
                                    <?php
                                    if($data['status_pembayaran'] == 1){
                                        echo "Sudah Bayar";
                                    }else{
                                        echo "Belum Bayar";
                                    }
                                    ?>
                                </td>  
                                <td class="text-nowrap text-end"><?php echo number_format($total_bayar) ?></td>     
                                <td class="text-nowrap text-center">
                                    <?php
                                        if($status_lunas == 1){
                                            echo "Lunas";
                                        }else{

                                            echo "Belum Lunas";
                                        }
                                    ?>
                                </td>    
                                <?php  
                                    if (!empty($tgl_tempo_cek) && $status_lunas == '0') {
                                    $timestamp_tgl_tempo = strtotime($tgl_tempo);
                                    $timestamp_now = strtotime($date_now);
                                    // Hitung selisih timestamp
                                    $selisih_timestamp = $timestamp_tgl_tempo - $timestamp_now;
                                    // Konversi selisih timestamp ke dalam hari
                                    $selisih_hari = floor($selisih_timestamp / (60 * 60 * 24));
                                    if ($tgl_tempo > $date_now){
                                        echo '<td class="text-end text-nowrap bg-secondary text-white">'. "Tempo < " .$selisih_hari. " Hari".'</td>';
                                    } else if ($tgl_tempo < $date_now){
                                        echo '<td class="text-end text-nowrap bg-danger text-white">'. "Tempo > " . abs($selisih_hari). " Hari".'</td>';
                                    } else if ($tgl_tempo == $date_now) {
                                        echo '<td class="text-end text-nowrap">Jatuh Tempo Hari ini</td>';
                                    } else {
                                        echo '<td class="text-end text-nowrap">Tidak Ada Tempo</td>';
                                    }
                                    } else {
                                        if ($status_lunas == '1'){
                                        echo '<td class="text-center text-nowrap">Sudah Lunas</td>';
                                        } else {
                                        echo '<td class="text-center text-nowrap">Tidak Ada Tempo</td>';
                                        }
                                    }
                                ?> 
                                <td class="text-center">
                                    <?php
                                    if (isset($data['status_tagihan']) && $data['status_tagihan'] == 0) {
                                        echo "Belum Dibuat";
                                    } else {
                                        echo (isset($data['no_tagihan']) ? ' ' . $data['no_tagihan'] : '');
                                    }
                                    ?>
                                </td>
                                <td class="text-center text-nowrap">
                                    <?php
                                    if ($id_inv_substr == 'NON') {
                                        echo '<a href="detail-produk-inv-nonppn.php?id=' . base64_encode($id_inv) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                    } elseif ($id_inv_substr == 'PPN') {
                                        echo '<a href="detail-produk-inv-ppn.php?id=' . base64_encode($id_inv) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                    } elseif ($id_inv_substr == 'BUM') {
                                        echo '<a href="detail-produk-inv-bum.php?id=' . base64_encode($id_inv) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php $no++ ?>
                        <?php } ?>
                        </tbody>
                        <tr id="messageRow" style="display:none;">
                        <!-- <td colspan="10" class="text-center">Data Tidak Ditemukan</td> -->
                        <p id="total-count" style="display: none;">Jumlah data yang ditampilkan: 0</p>
                        </tr>
                    </table>
                </div>
            </div>
        </section>
    </main><!-- End #main -->
    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
    <?php include "finance/page/js-filter-detail-cs.php"  ?>
</body>
</html>
<script>
    function goBack() {
        window.history.back();
    }
</script>