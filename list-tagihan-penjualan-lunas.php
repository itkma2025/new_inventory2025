<?php
$page = 'list-tagihan';
$page2 = 'tagihan-penjualan';
include 'akses.php';

// Periksa apakah tanggal telah dipilih atau belum
$dateRanges = array('today', 'weekly', 'monthly', 'lastMonth', 'year', 'lastyear');
$selectedDateRange = isset($_GET['date_range']) && in_array($_GET['date_range'], $dateRanges) ? $_GET['date_range'] : 'pilihTanggal';

// Periksa apakah customer service telah dipilih atau belum
$nama_cs = isset($_GET['cs']) ? $_GET['cs'] : array();

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

    .separator {
      display: inline-block;
      width: 40px; /* Atur panjang pemisah sesuai keinginan */
      font-size: 1.2rem;
      font-weight: bold;
      text-align: center;
      color: #333; /* Ubah warna sesuai keinginan */
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
      <!-- <div class="loader loader">
        <div class="loading">
          <img src="img/loading.gif" width="200px" height="auto">
        </div>
      </div> -->
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
        <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
        <!-- END SWEET ALERT -->
        <div class="card">
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
            <div class="table-responsive">
                <table class="table table-responsive table-striped" id="table3">
                <thead>
                    <tr class="text-white" style="background-color: navy;">
                        <td class="p-3 text-center text-nowrap">No</td>
                        <td class="p-3 text-center text-nowrap">No. Tagihan</td>
                        <td class="p-3 text-center text-nowrap">Tgl. Tagihan</td>
                        <td class="p-3 text-center text-nowrap">Nama Customer</td>
                        <td class="p-3 text-center text-nowrap">Total Tagihan</td>
                        <td class="p-3 text-center text-nowrap">Total Bayar</td>
                        <td class="p-3 text-center text-nowrap">Sisa Tagihan</td>
                        <td class="p-3 text-center text-nowrap">Aksi</td>
                    </tr>
                </thead>
                <tbody>
                    <?php  
                    include "koneksi.php";
                    $no = 1;
                    $sort_option ="";
                    $today = date('d/m/Y');
                    $startWeek = date('d/m/Y', strtotime("-1 week"));
                    $endWeek = date('d/m/Y', strtotime("now"));
                    $thisWeekStart= date('d/m/Y',strtotime('last sunday')); 
                    $thisWeekEnd= date('d/m/Y',strtotime('next sunday'));
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
                    $lastYear = date("Y",strtotime("-1 year"));
                    if(isset($_GET['date_range']))
                    {
                        if($_GET['date_range'] == "today")
                        {
                            $sort_option = "DATE(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = CURDATE()";
                        }

                        elseif($_GET['date_range'] == "weekly")
                        {
                            $sort_option = "
                                            WEEK(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = WEEK(CURDATE())
                                            AND YEAR(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = YEAR(CURDATE())
                                        ";
                        }

                        elseif($_GET['date_range'] == "monthly")
                        {

                            $sort_option = "
                                            MONTH(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = MONTH(CURDATE())
                                            AND YEAR(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = YEAR(CURDATE())
                                        "; 
                            
                        }

                        elseif($_GET['date_range'] == "lastMonth")
                        {
                            $sort_option = " 
                                            MONTH(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                                            AND YEAR(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                                            ";  
                        }

                        elseif($_GET['date_range'] == "year")
                        {
                            $sort_option = "YEAR(STR_TO_DATE(tagihan.tgl_tagihan, '%d/%m/%Y')) = YEAR(CURDATE())";
                        }

                        elseif($_GET['date_range'] == "lastyear")
                        {
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
                                    fnc.status_lunas,
                                    COALESCE(byr.total_bayar, 0) AS total_pembayaran,
                                    COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv                           
                            FROM finance_tagihan AS tagihan
                            LEFT JOIN finance fnc ON (tagihan.id_tagihan = fnc.id_tagihan)
                            LEFT JOIN (
                                SELECT id_tagihan, SUM(total_bayar) AS total_bayar
                                FROM finance_bayar
                                GROUP BY id_tagihan
                            ) byr ON (tagihan.id_tagihan = byr.id_tagihan)
                            LEFT JOIN inv_nonppn nonppn ON (fnc.id_inv = nonppn.id_inv_nonppn)
                            LEFT JOIN inv_ppn ppn ON (fnc.id_inv = ppn.id_inv_ppn)
                            LEFT JOIN inv_bum bum ON (fnc.id_inv = bum.id_inv_bum)
                            WHERE $sort_option
                            GROUP BY tagihan.id_tagihan
                            HAVING tagihan.total_tagihan = total_pembayaran
                            ORDER BY tagihan.no_tagihan ASC";
                    $query = mysqli_query($connect, $sql);
                    while ($data = mysqli_fetch_array($query)) {
                        $total_bayar = $data ['total_pembayaran'];
                        $total_tagihan = $data['total_tagihan'];
                        $total_sisa_tagihan = $total_tagihan - $total_bayar;
                        $tgl_tagihan = $data['tgl_tagihan'];
                        $no_tagihan = $data['no_tagihan'];
                    ?>
                    <tr>
                    <td class="text-center"><?php echo $no; ?></td>
                    <td class="text-center text-nowrap">
                        <?php echo $data['no_tagihan'] ?><br>
                        <?php 
                        if($data['jenis_faktur'] != ''){
                            echo "(" . $data['jenis_faktur'] . ")";
                        }
                        
                        ?>
                    </td>
                    <td class="text-center text-nowrap"><?php echo $data['tgl_tagihan'] ?></td>
                    <td><?php echo $data['cs_inv']; ?></td>
                    <td class="text-end"><?php echo number_format($data['total_tagihan'],0,'.','.')?></td>
                    <td class="text-end"><?php echo number_format($total_bayar,0,'.','.')?></td>
                    <td class="text-end">
                        <?php
                        if($total_sisa_tagihan == 0){
                            echo '
                            <button type="button" class="btn btn-secondary btn-sm mb-2">
                                <i class="bi bi-check-circle"></i> Lunas
                            </button>';
                        }else{
                            echo number_format($total_sisa_tagihan,0,'.','.');
                        }
                        ?>
                    </td>
                    <td class="text-center text-nowrap">
                        <a href="detail-bill.php?id=<?php echo base64_encode($data['tagihan_id'])?>" class="btn btn-primary btn-sm" title="Detail Tagihan"><i class="bi bi-eye"></i> </a>
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