<?php
$page  = 'transaksi'; 
$page2  = 'list-cmp';
include "akses.php";
include 'function/class-komplain.php';
require_once 'function/function-enkripsi.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link rel="stylesheet" href="assets/css/wrap-text.css">
   
    <?php include "page/head.php"; ?>
    <?php include "page/style-button-filterdate.php"; ?>

    <style>

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
            <h1>Invoice Komplain</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Invoice Komplain</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section>
            <?php 
                include "query/query-komplain.php" ;
                $baseUrl = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            ?>
            
            <div class="card p-3">
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <label><b>Filter tanggal komplain :</b></label><br>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" style="min-width: 300px" data-bs-toggle="dropdown" aria-expanded="false">
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
                            <div class="dropdown-menu text-center" aria-labelledby="dropdownMenuButton">
                                <form action="" method="GET" class="form-group newsletter-group" id="resetLink">
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'today' ? 'active' : ''; ?>" href="?date_range=today">Hari ini</a>
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'weekly' ? 'active' : ''; ?>" href="?date_range=weekly">Minggu ini</a>
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'monthly' ? 'active' : ''; ?>" href="?date_range=monthly">Bulan ini</a>
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'lastMonth' ? 'active' : ''; ?>" href="?date_range=lastMonth">Bulan Kemarin</a>
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'year' ? 'active' : ''; ?>" href="?date_range=year">Tahun ini</a>
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'lastyear' ? 'active' : ''; ?>" href="?date_range=lastyear">Tahun Lalu</a>
                                    <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'pilihTanggal' ? 'active' : ''; ?>">Pilih Tanggal</a>
                                </form>
                                <li><hr class="dropdown-divider"></li>
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
                                    </div>
                                    
                                    <!-- Add the submit button with name="tampilkan" -->
                                    <a href="invoice-komplain.php?date_range=year" name="tampilkan" class="custom-dropdown-item dropdown-item rounded bg-danger text-white" id="resetLink">Reset</a>
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

                                            const newUrl = `invoice-komplain.php?${queryParams.toString()}`;

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
                    <div class="col-md-2">
                        <label><b>Filter status komplain :</b></label>
                        <select class="form-select" id="statusSelect">
                            <option value="All">Semua</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-around flex-wrap">
                    <?php  
                        $data_count = mysqli_fetch_array($query2);
                    ?>
                    <div class="card p-2 border border-black" style="width: 250px;">
                        <div class="text-center p-2">
                            <p><b>Total Komplain</b></p> 
                            <p>
                                <?php 
                                    if($total_komplain != 0){
                                        echo $total_komplain;
                                    }else{
                                        echo "0";
                                    } 
                                ?>
                            </p> 
                        </div>
                    </div>
                    <div class="card p-2 border border-black" style="width: 250px;">
                        <div class="text-center p-2">
                            <p><b>Komplain Aktif</b></p>  
                            <p>
                                <?php 
                                    if($total_aktif != 0){
                                        echo $total_aktif;
                                    }else{
                                        echo "0";
                                    } 
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="card p-2 border border-black" style="width: 250px;">
                        <div class="text-center p-2">
                            <p><b>Komplain Selesai</b></p>  
                            <p>
                                <?php 
                                    if($total_selesai != 0){
                                        echo $total_selesai;
                                    }else{
                                        echo "0";
                                    } 
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-2">
                    <table class="table table-bordered table-striped" id="table1">
                        <thead>
                            <tr class="text-white" style="background-color: navy">
                                <th class="text-center text-nowrap p-3">No</th>
                                <th class="text-center text-nowrap p-3">No. Komplain</th>
                                <th class="text-center text-nowrap p-3">Tgl. Komplain</th>
                                <th class="text-center text-nowrap p-3">Nama Customer</th>
                                <th class="text-center text-nowrap p-3">No. Invoice</th>
                                <th class="text-center text-nowrap p-3">Kategori Komplain</th>
                                <th class="text-center text-nowrap p-3">Alasan Komplain</th>
                                <th class="text-center text-nowrap p-3">Status</th>
                                <th class="text-center text-nowrap p-3">Total Komplain</th>
                                <th class="text-center text-nowrap p-3">Aksi</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                include "koneksi.php";
                                $no = 1;
                                
                                while($data = mysqli_fetch_array($query)){
                                    $id_komplain = $data['id_komplain'];
                                    $id_komplain_encrypt = encrypt($id_komplain, $key_spk);
                                    $id_inv = $data['id_inv'];
                                    $id_inv_substr = substr($id_inv, 0, 3);
                                    $status_trx = $data['status_transaksi'];
                            ?>
                            <tr>
                                <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                <td class="text-center text-nowrap"><?php echo $data['no_komplain'] ?></td>
                                <td class="text-center text-nowrap"><?php echo date('d/m/Y', strtotime($data['tanggal']))?></td>
                                <td class="text-nowrap"><?php echo $data['cs_inv'] ?></td>
                                <td class="text-center text-nowrap"><?php echo $data['no_inv'] ?></td>
                                <td class="text-center text-nowrap">
                                    <?php
                                        if($data['kat_komplain'] == 0){
                                            echo "Invoice";
                                        } else {
                                            echo "Barang";
                                        }
                                    ?>
                                </td>
                                <td class="wrap-text"><?php echo $alasan_komplain = komplain::getKondisi($data['kondisi_pesanan']); ?></td>
                                <td class="text-center text-nowrap">
                                    <?php 
                                        if ($data['status_komplain'] == 0){
                                            echo "Aktif";
                                            echo "<br>";
                                        
                                            // Menampilkan nama driver
                                            $driver = $connect->query("SELECT 
                                                                            sk.jenis_pengiriman, 
                                                                            sk.dikirim_driver, 
                                                                            sk.dikirim_ekspedisi,
                                                                            sk.diambil_oleh,
                                                                            ip.nama_penerima,
                                                                            COALESCE(us.nama_user, eks.nama_ekspedisi) AS pengirim
                                                                        FROM revisi_status_kirim AS sk
                                                                        LEFT JOIN $database2.user us ON(sk.dikirim_driver = us.id_user)
                                                                        LEFT JOIN ekspedisi eks ON(sk.dikirim_ekspedisi = eks.id_ekspedisi)
                                                                        LEFT JOIN inv_penerima_revisi ip ON(sk.id_komplain = ip.id_komplain)
                                                                        WHERE sk.id_komplain = '$id_komplain'");
                                            
                                            if($driver && mysqli_num_rows($driver) > 0) {
                                                $data_pengiriman = mysqli_fetch_array($driver);
                                                $jenis_pengiriman = isset($data_pengiriman['jenis_pengiriman']) ? $data_pengiriman['jenis_pengiriman'] : '';
                                                $pengirim = isset($data_pengiriman['pengirim']) ? $data_pengiriman['pengirim'] : '';
                                                $diambil_oleh = isset($data_pengiriman['diambil_oleh']) ? $data_pengiriman['diambil_oleh'] : '';
                                                $nama_penerima = isset($data_pengiriman['nama_penerima']) ? $data_pengiriman['nama_penerima'] : '';
                                            } else {
                                                $jenis_pengiriman = '';
                                                $pengirim = '';
                                                $diambil_oleh = '';
                                            }
                                        
                                            if($status_trx == "Komplain Dikirim"){
                                                echo "<b>Dikirim - " . $jenis_pengiriman . "</b><br>";
                                                echo "(". $pengirim .")";
                                            } else if($status_trx == "Komplain Diterima"){
                                                echo "<b>Diterima Oleh</b><br>";
                                                echo "(". $nama_penerima .")";
                                            } else if($status_trx == "Komplain Diambil"){
                                                echo "<b>" . $jenis_pengiriman . "</b><br>";
                                                echo "(". $diambil_oleh .")";
                                            } else {
                                                echo "(" . $status_trx . ")";
                                            }
                                        } else {
                                            echo "Selesai";
                                            echo "<br>";
                                            echo "(" . $status_trx . ")";
                                        }                                        
                                    ?>
                                </td>
                                <td class="text-center"><?php echo $data['total_komplain'] ?></td>
                                <td class="text-center">
                                    <?php  
                                        if ($id_inv_substr == 'NON') {
                                            ?>
                                                <a href="detail-komplain-nonppn.php?id=<?php echo urlencode($id_komplain_encrypt) ?>" class="btn btn-primary btn-sm" title="Detail"><i class="bi bi-eye"></i></a>
                                            <?php
                                        } else  if ($id_inv_substr == 'PPN') {
                                            ?>
                                                <a href="detail-komplain-ppn.php?id=<?php echo urlencode($id_komplain_encrypt) ?>" class="btn btn-primary btn-sm" title="Detail"><i class="bi bi-eye"></i></a>
                                            <?php
                                        }  if ($id_inv_substr == 'BUM'){
                                            ?>
                                                <a href="detail-komplain-bum.php?id=<?php echo urlencode($id_komplain_encrypt) ?>" class="btn btn-primary btn-sm" title="Detail"><i class="bi bi-eye"></i></a>
                                            <?php
                                        }


                                    
                                    ?>
                                </td>
                            </tr>
                            <?php $no++ ?>
                            <?php } ?>
                        </tbody>
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

</body>

</html>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const filterstatusSelect = document.getElementById('statusSelect');
    const dataTable = document.getElementById('table1');

    filterstatusSelect.addEventListener('change', applyFilters);

    function applyFilters() {
        const selectedValue = filterstatusSelect.value;
        const rows = dataTable.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cell = row.cells[7]; // Pastikan indeks 7 sesuai dengan kolom status

            if (cell) {
                const cellValue = cell.textContent.trim();
                let showRow = false;

                if (selectedValue === 'All') {
                    showRow = true;
                } else if (selectedValue === 'Aktif' && cellValue === 'Aktif') {
                    showRow = true;
                } else if (selectedValue === 'Selesai' && cellValue === 'Selesai') {
                    showRow = true;
                }

                row.style.display = showRow ? '' : 'none';
            }
        }
    }

    // Inisialisasi filter pada awal halaman
    applyFilters();
});
</script>





