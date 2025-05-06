<?php
  $page = 'invoice';
  $page2  = 'penjualan';
  require_once "../akses.php";
  require_once 'function/class-finance.php';
  require_once "../function/update-total-inv-finance.php";

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

    .disabled-select{
      pointer-events: none;
      background-color: #0d6efd;
      color: white;
      
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
        <h1>Data Invoice</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">Invoice</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->
      <section>
        <!-- SWEET ALERT -->
        <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
        <!-- END SWEET ALERT -->
        <div class="card p-3">
          <?php
            // Mendapatkan bagian dari URL yang berisi parameter GET
            $queryString = $_SERVER['QUERY_STRING'];

            // Daftar parameter yang ingin dihapus
            $parametersToRemove = ['status_bayar', 'jenis_inv', 'status_tagihan'];

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
                $queryString = (empty($queryString) ? '' : $queryString . '&') . 'date_range=weekly';
            }

            // Menyimpan nilai-nilai filter yang telah diaplikasikan
            $dateRangeFilter = isset($_GET['date_range']) ? $_GET['date_range'] : '';
            $statusBayarFilter = isset($_GET['status_bayar']) ? $_GET['status_bayar'] : '';
            $jenisInvFilter = isset($_GET['jenis_inv']) ? $_GET['jenis_inv'] : '';
            $statusTagihanFilter = isset($_GET['status_tagihan']) ? $_GET['status_tagihan'] : '';

            // Menambah atau mengganti nilai parameter status_bayar dalam URL
            $queryString = addOrReplaceParameter($queryString, 'status_bayar', $statusBayarFilter);

            // Menambah atau mengganti nilai parameter jenis_inv dalam URL
            $queryString = addOrReplaceParameter($queryString, 'jenis_inv', $jenisInvFilter);

            // Menambah atau mengganti nilai parameter status_tagihan dalam URL
            $queryString = addOrReplaceParameter($queryString, 'status_tagihan', $statusTagihanFilter);

            // echo $queryString;
          ?>

              <!-- Mengganti date_range dan mempertahankan nilai-nilai filter yang telah diaplikasikan -->
          <?php
            if (!empty($dateRangeFilter)) {
                $queryString = addOrReplaceParameter($queryString, 'date_range', $dateRangeFilter);
            }
          ?>
          <div class="p-2" style="margin-left: 5px;">
            <div class="row">
              <div class="col-sm-2">
                <div class="row row-cols-1 row-cols-lg-1 g-2 g-lg-3">
                  <div class="col">
                    <div class="card">
                      <button class="btn btn-secondary btn-md" id="export-button">Export Excel <i class="bi bi-file-earmark-excel"></i></button>
                    </div>
                  </div>
                  <!-- <div class="col">
                    <div class="card" id="cs">
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCs">
                        Filter by Customer
                      </button>
                    </div>
                  </div> -->
                </div>
              </div>
              <div class="col-sm-10">
                <div class="row row-cols-1 row-cols-lg-4 g-2 g-lg-3">
                    <div class="col">
                      <div class="card">
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
                                    <label for="start_date">From</label>
                                    <input type="date" id="startDate" class="form-control form-control-md date-picker" placeholder="dd/mm/yyyy" name="start_date">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_date">To</label>
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
                    </div>
                    <!-- <div class="col">
                      <div class="card">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" style="min-width: 170px" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php
                              // Menentukan teks yang ditampilkan berdasarkan nilai dari parameter date_range
                              // $statusBayar = isset($_GET['status_bayar']) ? $_GET['status_bayar'] : 'Semua';
                              // if ($statusBayar === "Semua" || $statusBayar === "") {
                              //   echo "Semua Status Bayar";
                              // } elseif ($statusBayar === "Belum Bayar") {
                              //   echo "Status Belum Bayar";
                              // } elseif ($statusBayar === "Sudah Bayar") {
                              //   echo "Status Sudah Bayar";
                              // }
                            ?>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <form action="" method="GET" class="form-group newsletter-group" id="resetLink">
                              <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['status_bayar']) && $_GET['status_bayar'] === '' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&status_bayar=">Semua Status</a>
                              <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['status_bayar']) && $_GET['status_bayar'] === 'Belum Bayar' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&status_bayar=Belum Bayar">Status Belum Bayar</a>
                              <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['status_bayar']) && $_GET['status_bayar'] === 'Sudah Bayar' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&status_bayar=Sudah Bayar">Status Sudah Bayar</a>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div> -->
                    <div class="col">
                      <div class="card">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" style="min-width: 170px" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php
                              // Menentukan teks yang ditampilkan berdasarkan nilai dari parameter date_range
                              $jenisInv = isset($_GET['jenis_inv']) ? $_GET['jenis_inv'] : 'Semua';
                              if ($jenisInv === "Semua" || $jenisInv === "") {
                                echo "Semua Jenis Invoice";
                              } elseif ($jenisInv === "nonppn") {
                                echo "Non PPN";
                              } elseif ($jenisInv === "ppn") {
                                echo "PPN";
                              }elseif ($jenisInv === "bum") {
                                echo "BUM";
                              }
                            ?>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <form action="" method="GET" class="form-group newsletter-group" id="resetLink">
                              <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['jenis_inv']) && $_GET['jenis_inv'] === '' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&jenis_inv=">Semua</a>
                              <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['jenis_inv']) && $_GET['jenis_inv'] === 'nonppn' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&jenis_inv=nonppn">Non PPN</a>
                              <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['jenis_inv']) && $_GET['jenis_inv'] === 'ppn' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&jenis_inv=ppn">PPN</a>
                              <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['jenis_inv']) && $_GET['jenis_inv'] === 'bum' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&jenis_inv=bum">BUM</a>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>  
                    <!-- <div class="col">
                      <div class="card">
                        <div class="btn-group">
                          <button type="button" class="btn btn-primary dropdown-toggle" style="min-width: 170px" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php
                              // Menentukan teks yang ditampilkan berdasarkan nilai dari parameter date_range
                              // $statusTagihan = isset($_GET['status_tagihan']) ? $_GET['status_tagihan'] : 'Semua';
                              // if ($statusTagihan === "Semua" || $statusTagihan === "") {
                              //   echo "Semua Status Tagihan";
                              // } elseif ($statusTagihan === "Belum Dibuat") {
                              //   echo "Belum Dibuat";
                              // } elseif ($statusTagihan === "Sudah Dibuat") {
                              //   echo "Sudah Dibuat";
                              // }
                            ?>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <form action="" method="GET" class="form-group newsletter-group" id="resetLink">
                              <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['status_tagihan']) && $_GET['status_tagihan'] === '' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&status_tagihan=">Semua</a>
                              <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['status_tagihan']) && $_GET['status_tagihan'] === 'Belum Dibuat' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&status_tagihan=Belum Dibuat">Belum Dibuat</a>
                              <a class="custom-dropdown-item dropdown-item rounded <?php echo isset($_GET['status_tagihan']) && $_GET['status_tagihan'] === 'Sudah Dibuat' ? 'disabled-select' : ''; ?>" href="?<?php echo $queryString ?>&status_tagihan=Sudah Dibuat">Sudah Dibuat</a>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>    -->
                  </div> 
              </div>
            </div>
          </div>   
          <div class="row">
            <div class="col-md-12 mb-3" id="cs">
                <?php if (!empty($nama_cs)): ?>
                    <?php foreach ($nama_cs as $cs): ?>
                        <div class="input-group mb-3" id="cs">
                            <input type="text" class="form-control" value="<?php echo $cs ?>">
                            <button class="btn btn-outline-dark deleteButton" data-cs="<?php echo $cs ?>">
                              <span class="text-dark">X</span>
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
          </div>
          <?php        
            // Memeriksa apakah parameter 'date_range' telah diterima melalui URL
            if (isset($_GET['date_range'])) {
              $baseUrl = $_GET['date_range'];
            } else {
            }
          ?>
          <div class="table-responsive">
              <div class="col-md-8 mb-3 mt-2" style="margin-left: 12px;">
                <form action="create-bill.php" method="GET" onsubmit="prepareFormData()">
                    <!-- <textarea type="hidden" id="idInv" name="inv_id[]" class="form-control" cols="80" rows="10"></textarea> -->
                    <input type="hidden" id="idInv" name="inv_id[]" class="form-control">
                    <button type="submit" id="createBill" class="btn btn-primary btn-md">Buat Tagihan</button>
                </form>
              </div>
              <table id="tableInv" class="table table-striped nowrap" style="width:100%">
                <thead>
                  <tr class="text-white" style="background-color: navy;">
                    <td class="text-center p-3">Pilih</td>
                    <td class="text-center p-3">No</td>
                    <td class="text-center p-3">No. Invoice</td>
                    <td class="text-center p-3">Tgl. Invoice</td>
                    <td class="text-center p-3">Jenis Inv</td>
                    <td class="text-center p-3">Customer</td>
                    <td class="text-center p-3">Customer Inv</td>
                    <td class="text-center p-3">Tgl. Tempo</td>
                    <td class="text-center p-3">Total Tagihan</td>
                    <td class="text-center p-3">Status Tempo</td>
                    <td class="text-center p-3">Aksi</td>
                  </tr>
                </thead>
                <tbody>
                  <?php  
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
                            $sort_option = "DATE(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = CURDATE()";
                        }

                        elseif($_GET['date_range'] == "weekly")
                        {
                            $sort_option = "
                                            WEEK(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = WEEK(CURDATE())
                                            AND YEAR(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = YEAR(CURDATE())
                                          ";
                        }

                        elseif($_GET['date_range'] == "monthly")
                        {

                            $sort_option = "
                                            MONTH(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = MONTH(CURDATE())
                                            AND YEAR(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = YEAR(CURDATE())
                                          "; 
                            
                        }

                        elseif($_GET['date_range'] == "lastMonth")
                        {
                            $sort_option = " 
                                            MONTH(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                                            AND YEAR(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                                            ";  
                        }

                        elseif($_GET['date_range'] == "year")
                        {
                            $sort_option = "YEAR(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = YEAR(CURDATE()) ";
                        }

                        elseif($_GET['date_range'] == "lastyear")
                        {
                            $sort_option = "YEAR(STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y')) = YEAR(CURDATE()) - 1 ";
                        } 

                        elseif($_GET['date_range'] == "pilihTanggal")
                        {
                          if (isset($_GET["start_date"]) && isset($_GET["end_date"])) {
                            $dt1 = $_GET["start_date"];
                            $dt2 = $_GET["end_date"];
                            $format_dt1 = date('d/m/Y', strtotime($dt1));
                            $format_dt2 = date('d/m/Y', strtotime($dt2));
                            $sort_option .= "STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y') BETWEEN STR_TO_DATE('$format_dt1', '%d/%m/%Y') AND STR_TO_DATE('$format_dt2', '%d/%m/%Y')";
                          }
                        } 

                    }

                    if(isset($_GET['status_bayar'])){
                      if($_GET['status_bayar'] == "Belum Bayar"){
                        $sort_option .= "AND status_pembayaran = '0'";
                      }else if ($_GET['status_bayar'] == "Sudah Bayar"){
                        $sort_option .= "AND status_pembayaran = '1'";
                      }
                    }

                    if(isset($_GET['jenis_inv'])){
                      if($_GET['jenis_inv'] == "nonppn"){
                        $sort_option .= "AND jenis_inv = 'nonppn'";
                      }else if ($_GET['jenis_inv'] == "ppn"){
                        $sort_option .= "AND jenis_inv = 'ppn'";
                      }else if ($_GET['jenis_inv'] == "bum"){
                        $sort_option .= "AND jenis_inv = 'bum'";
                      }
                    }


                    if(isset($_GET['status_tagihan'])){
                      if($_GET['status_tagihan'] == "Belum Dibuat"){
                        $sort_option .= "AND status_tagihan = '0'";
                      }else if ($_GET['status_tagihan'] == "Sudah Dibuat"){
                        $sort_option .= "AND status_tagihan = '1'";
                      }
                    }

                    
                    $sql = "SELECT 
                              -- finance
                              fnc.jenis_inv,
                              fnc.status_pembayaran,
                              fnc.status_tagihan,
                              fnc.total_inv,
                              fnc.status_lunas,
                              STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y') AS tgl_inv,
                              COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo) AS tgl_tempo,
                              STR_TO_DATE(COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo), '%d/%m/%Y') AS tgl_tempo_convert,
                              COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                              nonppn.id_inv_nonppn AS id_inv_nonppn,
                              nonppn.no_inv AS no_inv_nonppn,
                              nonppn.status_transaksi AS status_trx_nonppn,
                              nonppn.status_transaksi AS status_trx_nonppn,
                              ppn.id_inv_ppn AS id_inv_ppn,
                              ppn.no_inv AS no_inv_ppn,
                              ppn.status_transaksi AS status_trx_ppn,
                              bum.id_inv_bum AS id_inv_bum,
                              bum.no_inv AS no_inv_bum,
                              bum.status_transaksi AS status_trx_bum, 
                              ft.no_tagihan,
                              -- tambahkan kolom nama_customer
                              cust.nama_cs  -- ganti 'nama_customer' sesuai dengan nama kolom di tb_customer
                          FROM finance AS fnc
                          LEFT JOIN inv_nonppn nonppn ON fnc.id_inv = nonppn.id_inv_nonppn
                          LEFT JOIN inv_ppn ppn ON fnc.id_inv = ppn.id_inv_ppn
                          LEFT JOIN inv_bum bum ON fnc.id_inv = bum.id_inv_bum
                          LEFT JOIN finance_tagihan ft ON fnc.id_tagihan = ft.id_tagihan
                          -- tambahkan join dengan tb_customer
                          LEFT JOIN spk_reg sr ON fnc.id_inv = sr.id_inv
                          LEFT JOIN tb_customer cust ON sr.id_customer = cust.id_cs
                          WHERE ($sort_option)
                              AND fnc.status_tagihan = '0'
                          GROUP BY nonppn.no_inv, ppn.no_inv, bum.no_inv
                          ORDER BY nonppn.no_inv, ppn.no_inv, bum.no_inv";

                    // Tambahkan ORDER BY setelah klausa WHERE
                    // $sql .= " ORDER BY fnc.status_tagihan ASC";
                    $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                    while ($data = mysqli_fetch_array($query)) {
                      $no_inv = finance::getNoInvoice($data['no_inv_nonppn'], $data['no_inv_ppn'], $data['no_inv_bum']);
                      $id_inv = finance::getTglPesanan($data['id_inv_nonppn'], $data['id_inv_ppn'], $data['id_inv_bum']);
                      $tgl_inv = $data['tgl_inv'];
                      $cs = $data['nama_cs'];
                      $cs_inv = $data['cs_inv'];
                      $date_now = date('Y-m-d');
                      $tgl_tempo_cek = $data['tgl_tempo'];
                      $tgl_tempo = $data['tgl_tempo_convert'];
                      $status_lunas = $data['status_lunas'];
                    ?>
                      <tr>
                        <td class="text-center">
                          <?php  
                            if ($data['status_tagihan'] == 0) {
                                ?>
                                  <input type="checkbox" name="inv_id[]" id="inv" value="<?php echo $id_inv ?>" data-customer="<?php echo $cs ?>" data-jenis="<?php echo $data['jenis_inv'] ?>" onclick="updateInputValue(this)">
                                <?php
                            }
                          ?>
                        </td>
                        <td class="text-center text-nowrap"><?php echo $no ?></td>
                        <td class="text-nowrap text-center">
                          <?php  
                            // Jalankan query
                            $sql_rev =  $connect->query("SELECT no_inv_revisi FROM inv_revisi 
                                                        WHERE id_inv = '$id_inv' 
                                                        GROUP BY no_inv_revisi");

                            // Hitung total data yang didapat
                            $total_data_rev =  mysqli_num_rows($sql_rev);

                            // Jika tidak ada data, tampilkan "Revisi"
                            if($total_data_rev == 0){
                                echo $no_inv; 
                            } else {
                                // Jika ada data, tampilkan nomor invoice yang relevan
                                while($data_rev = mysqli_fetch_array($sql_rev)){
                                    echo $data_rev['no_inv_revisi']; // Pastikan variabel yang di-echo adalah benar
                                }
                            }
                          ?>
                        </td>
                        <td class="text-nowrap text-center"><?php echo date('d/m/Y', strtotime($tgl_inv)) ?></td>
                        <td class="text-center text-nowrap"><?php echo strtoupper($data['jenis_inv'])?></td>
                        <td class="text-nowrap"><?php echo $cs ?></td>
                        <td class="text-nowrap"><?php echo $cs_inv ?></td>
                        <td class="text-nowrap text-center">
                          <?php 
                            if(!empty($tgl_tempo_cek)){
                              echo date('d/m/Y', strtotime($tgl_tempo));
                            } else {
                              echo "Tidak Ada Tempo";
                            }
                          ?>
                        </td>
                        <td class="text-nowrap text-end"><?php echo number_format($data['total_inv'])?></td>
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
                        <td class="text-center text-nowrap"> 
                            <?php
                              $cek_komplain = $connect->query("SELECT id_komplain FROM inv_komplain WHERE id_inv = '$id_inv'");
                              $total_data_komplain = mysqli_num_rows($cek_komplain);
                              while($data_komplain = mysqli_fetch_array($cek_komplain)){
                                $id_komplain = $data_komplain['id_komplain'];
                              ?>
                              <?php } ?>
                              <?php
                              if ($data['jenis_inv'] == 'nonppn') {
                                if ($total_data_komplain != 0) {
                                  echo '<a href="detail-fnc-nonppn-revisi.php?id=' . base64_encode($id_komplain) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                } else {
                                  echo '<a href="detail-fnc-nonppn.php?id=' . base64_encode($id_inv) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                }
                              } elseif ($data['jenis_inv'] == 'ppn') {
                                if ($total_data_komplain != 0) {
                                  echo '<a href="detail-fnc-ppn-revisi.php?id=' . base64_encode($id_komplain) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                } else {
                                  echo '<a href="detail-fnc-ppn.php?id=' . base64_encode($id_inv) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                }
                              } elseif ($data['jenis_inv'] == 'bum') {
                                  if ($total_data_komplain != 0) {
                                    echo '<a href="detail-fnc-bum-revisi.php?id=' . base64_encode($id_komplain) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                  } else {
                                    echo '<a href="detail-fnc-bum.php?id=' . base64_encode($id_inv) . '" class="btn btn-primary btn-sm" title="Lihat Data"><i class="bi bi-eye"></i></a>';
                                  }
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
            
            <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script> -->
            <script src="assets/js/export-excel.js"></script>
            <script>
                document.getElementById("export-button").addEventListener("click", function () {
                  // Inisialisasi DataTable
                  var table = $('#tableInv').DataTable();

                  // Mengambil data dari semua halaman DataTable
                  var data = [];
                  table.rows().every(function () {
                      var rowData = this.data();
                      // Mengambil hanya kolom yang diinginkan (mulai dari indeks 1)
                      var selectedColumns = rowData.slice(1, 11); // Sesuaikan dengan indeks kolom yang diinginkan
                      data.push(selectedColumns);
                  });

                  // Menambahkan header khusus di bagian atas
                  var header = ["No", "No. Invoice", "Tgl. Invoice", "Jenis Inv", "Customer", "Tgl. Tempo", "Total Tagihan", "Status Pembayaran", "Status Tempo", "Status Tagihan"];
                  data.unshift(header);

                  // Membuat worksheet baru
                  var worksheet = XLSX.utils.aoa_to_sheet(data);

                  // Set properti kolom untuk menjaga lebar default
                  var wscols = [];
                  for (var i = 0; i < header.length; i++) {
                      // Menetapkan lebar khusus untuk kolom "Customer"
                      if (i === 4) {
                          wscols.push({ wch: 35 }); // Sesuaikan dengan kebutuhan
                      } else {
                          wscols.push({ wch: header[i].length + 7 });
                      }
                  }
                  worksheet['!cols'] = wscols;

                  // Membuat workbook dan menambahkan worksheet ke dalamnya
                  var workbook = XLSX.utils.book_new();
                  XLSX.utils.book_append_sheet(workbook, worksheet, 'Data');

                  // Mengkonversi workbook ke dalam bentuk blob
                  var excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
                  var blob = new Blob([excelBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

                  // Buat object URL untuk mengunduh file Excel
                  var url = URL.createObjectURL(blob);

                  // Buat elemen anchor untuk mengunduh file dan klik secara otomatis
                  var a = document.createElement("a");
                  a.href = url;
                  a.download = "data-invoice-tagihan.xlsx";
                  a.click();

                  // Hapus object URL untuk membersihkan sumber daya
                  window.URL.revokeObjectURL(url);
              });
            </script>
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
<!-- Modal -->
<div class="modal fade" id="modalCs" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Modal Filter Customer</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger mt-2" id="errorNotification" style="display: none;"></div> <!-- Notifikasi kesalahan -->
          <form action="" method="GET" id="myForm">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="table3">
                    <thead>
                        <tr>
                            <th class="text-center col-1">Pilih</th>
                            <th class="text-center col-1">No</th>
                            <th class="text-center col-10">Nama Customer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  
                        $no = 1;
                        $sql_cs_nonppn = "SELECT subquery.*,
                                                  tb_customer.nama_cs,
                                                  tb_customer.alamat
                                          FROM (
                                              SELECT DISTINCT finance.*,
                                                      inv_nonppn.cs_inv AS cs_inv_nonppn,
                                                      inv_ppn.cs_inv AS cs_inv_ppn, 
                                                      inv_bum.cs_inv AS cs_inv_bum,
                                                      inv_nonppn.status_transaksi AS status_trx_nonppn,
                                                      inv_ppn.status_transaksi AS status_trx_ppn,
                                                      inv_bum.status_transaksi AS status_trx_bum,
                                                      spk_reg.id_inv AS id_inv_spk,
                                                      spk_reg.id_customer,
                                                      spk_reg.tgl_pesanan
                                              FROM finance 
                                              LEFT JOIN inv_nonppn ON finance.id_inv = inv_nonppn.id_inv_nonppn 
                                              LEFT JOIN inv_ppn ON finance.id_inv = inv_ppn.id_inv_ppn 
                                              LEFT JOIN inv_bum ON finance.id_inv = inv_bum.id_inv_bum
                                              LEFT JOIN spk_reg ON finance.id_inv = spk_reg.id_inv
                                          ) AS subquery
                                          LEFT JOIN tb_customer ON subquery.id_customer = tb_customer.id_cs
                                          GROUP BY tb_customer.nama_cs";
                        $query_cs_nonppn = mysqli_query($connect, $sql_cs_nonppn);
                        while($data_cs = mysqli_fetch_array($query_cs_nonppn)){
                        ?>
                        <tr>
                            <td class="text-center"><input type="checkbox" name="cs[]" id="checkboxCs" value="<?php echo $data_cs['nama_cs']; ?>"></td>
                            <td class="text-center"><?php echo $no; ?></td>
                            <td><?php echo $data_cs['nama_cs']; ?></td>
                        </tr>
                        <?php $no++ ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div> <!-- Tutup div modal-body -->
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" onclick="submitForm()">Oke</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Checkbox saat buat Bill -->
<script>
    const checkboxes = document.querySelectorAll('input[type="checkbox"][id^="inv"]');
    const createBill = document.getElementById("createBill");
    
    function updateButtonState() {
      const checkedCheckboxes = Array.from(checkboxes).filter(checkbox => checkbox.checked);
      const selectedCustomers = new Set(checkedCheckboxes.map(checkbox => checkbox.getAttribute("data-customer")));
      const selectedJenis = new Set(checkedCheckboxes.map(checkbox => checkbox.getAttribute("data-jenis")));

      if (checkedCheckboxes.length <= 30 && selectedCustomers.size === 1) {
          // Check if "nonppn" and "bum" are selected together, and "ppn" is not selected
          const isNonPPNSelected = selectedJenis.has("nonppn");
          const isBUMSelected = selectedJenis.has("bum");
          const isPPNSelected = selectedJenis.has("ppn");

          if ((isNonPPNSelected || isBUMSelected) && !isPPNSelected) {
              createBill.disabled = false;
          } else if ((isPPNSelected) && !isNonPPNSelected && !isBUMSelected){
              createBill.disabled = false;
          }else{
            createBill.disabled = true;
          }
      } else {
          createBill.disabled = true;
      }
    }


    function checkInitialCheckbox() {
        checkboxes.forEach(checkbox => {
            if (checkbox.getAttribute("data-customer") === "") {
                checkbox.checked = true;
            }
        });
        updateButtonState();
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            // Limit selection to a maximum of 20 checkboxes
            const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
            if (checkedCount > 30) {
                this.checked = false;
            }

            updateButtonState();
        });
    });

    checkInitialCheckbox();
</script>
<script>
  function updateInputValue(checkbox) {
      var inputValue = document.getElementById('idInv').value;
      var checkboxValue = encodeURIComponent(checkbox.value);
      var maxData = 30;

      // Cek apakah checkbox dicentang atau tidak
      if (checkbox.checked) {
          // Jika dicentang, tambahkan nilai checkbox ke nilai input jika belum mencapai batas
          var values = inputValue.split(',');
          if (values.length < maxData) {
              if (inputValue.length > 0) {
                  inputValue += ',' + checkboxValue;
              } else {
                  inputValue = checkboxValue;
              }
          } else {
              // Jika sudah mencapai batas, batasi pemilihan checkbox
              checkbox.checked = false;
              Swal.fire({
                  title: '<strong>Anda Sudah Mencapai Batas Maksimum Pemilihan</strong>',
                  icon: 'info',
                  html: 'Anda hanya dapat memilih maksimal 30 data.',
                  confirmButtonText: 'OK'
              });
          }
      } else {
          // Jika tidak dicentang, hapus nilai checkbox dari nilai input
          inputValue = inputValue.split(',').filter(function (value) {
              return value !== checkboxValue;
          }).join(',');
      }

      // Perbarui nilai input
      document.getElementById('idInv').value = inputValue;
  }
</script>

<script>
  function submitForm(action) {
      document.getElementById("invoiceForm").action = action;
      document.getElementById("invoiceForm").submit();
  }
</script>
<!-- End Checkbox Create Bill -->

<script>
  $(document).ready(function () {
    $("#tableInv").DataTable({
      lengthChange: false,
      ordering: false,
      autoWidth: false,
      language: {
        searchPlaceholder: "Cari data",
        search: "",
      },
    });
  });
</script>






