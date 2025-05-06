<?php
require_once "akses.php";
$page = 'br-masuk';
$page2 = 'br-masuk-set-ecat';
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
    <?php include "page/style-button-filterdate.php"; ?>
    <style>
        .custom-width{
            min-width: 285px;
        }
        .col-sm-5 {
            flex: 0 0 auto;
            width: 38.666667% !important;
        }
        @media (max-width: 767px) {

            /* Tambahkan aturan CSS khusus untuk tampilan mobile di bawah 767px */
            .col-12.col-md-2 {
                /* Contoh: Mengatur tinggi elemen select pada tampilan mobile */
                height: 50px;
            }

            .dropdown-menu-custom {
                min-width: 330px !important;
                padding: 17px !important;
            }
        }

        @media (max-width: 500px) {
            .dropdown-menu-custom {
                width: 100vw !important;
                padding: 17px !important;
            }

            .custom-width{
                min-width: 270px;
            }

            .col-sm-5 {
                flex: 0 0 auto;
                width: 100vw !important;
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
            <h1>Data Update Stock Set E-Cat</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Data update stock set E-Cat</li>
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->
        <section>
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row" style="margin-left: 2px;">
                            <div class="col-sm-2 mb-3">
                                <?php  
                                    if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Gudang") { 
                                        ?>
                                            <a href="input-set-in-ecat.php" class="btn btn-primary btn-md"><i class="bi bi-plus-circle"></i> Tambah data</a>
                                        <?php
                                    }
                                ?>
                            </div>
                            <div class="col-sm-5">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="fw-bold">Filter tanggal update stok set :</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle custom-width" data-bs-toggle="dropdown" aria-expanded="false">
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
                                                    <a class="dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'today' ? 'active' : ''; ?>" href="?date_range=today">Hari ini</a>
                                                    <a class="dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'weekly' ? 'active' : ''; ?>" href="?date_range=weekly">Minggu ini</a>
                                                    <a class="dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'monthly' ? 'active' : ''; ?>" href="?date_range=monthly">Bulan ini</a>
                                                    <a class="dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'lastMonth' ? 'active' : ''; ?>" href="?date_range=lastMonth">Bulan Kemarin</a>
                                                    <a class="dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'year' ? 'active' : ''; ?>" href="?date_range=year">Tahun ini</a>
                                                    <a class="dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'lastyear' ? 'active' : ''; ?>" href="?date_range=lastyear">Tahun Lalu</a>
                                                    <a class="dropdown-item rounded <?php echo isset($_GET['date_range']) && $_GET['date_range'] === 'pilihTanggal' ? 'active' : ''; ?>">Pilih Tanggal</a>
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
                                                    <a href="barang-masuk-set-reg.php?date_range=weekly" name="tampilkan" class="dropdown-item rounded bg-danger text-white" id="resetLink">Reset</a>
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

                                                            const newUrl = `barang-masuk-set-reg.php?${queryParams.toString()}`;

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
                            </div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-bordered" id="table1">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                        <td class="text-center p-3" style="width: 50px">No</td>
                                        <td class="text-center p-3" style="width: 120px">Kode Produk Set</td>
                                        <td class="text-center p-3" style="width: 250px">Nama Set Produk </td>
                                        <td class="text-center p-3" style="width: 100px">Merk</td>
                                        <td class="text-center p-3" style="width: 80px">Qty</td>
                                        <td class="text-center text-nowrap p-3" style="width: 80px">Keterangan Stock Masuk</td>
                                        <td class="text-center text-nowrap p-3" style="width: 120px">Dibuat Oleh</td>
                                        <?php  
                                            if ($role == "Super Admin" || $role == "Manager Gudang" ) { 
                                                ?>
                                                    <td class="text-center p-3" style="width: 100px">Aksi</td>
                                                <?php
                                            }
                                        ?>
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
                                            $sort_option = "DATE(STR_TO_DATE(tse.created_date, '%d/%m/%Y, %H:%i:%s')) = CURDATE()";
                                        }

                                        elseif($_GET['date_range'] == "weekly")
                                        {
                                            $sort_option = "
                                                            WEEK(STR_TO_DATE(tse.created_date, '%d/%m/%Y, %H:%i:%s')) = WEEK(CURDATE())
                                                            AND YEAR(STR_TO_DATE(tse.created_date, '%d/%m/%Y, %H:%i:%s')) = YEAR(CURDATE())
                                                        ";
                                        }

                                        elseif($_GET['date_range'] == "monthly")
                                        {

                                            $sort_option = "
                                                            MONTH(STR_TO_DATE(tse.created_date, '%d/%m/%Y, %H:%i:%s')) = MONTH(CURDATE())
                                                            AND YEAR(STR_TO_DATE(tse.created_date, '%d/%m/%Y, %H:%i:%s')) = YEAR(CURDATE())
                                                        "; 
                                            
                                        }

                                        elseif($_GET['date_range'] == "lastMonth")
                                        {
                                            $sort_option = " 
                                                            MONTH(STR_TO_DATE(tse.created_date, '%d/%m/%Y, %H:%i:%s')) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                                                            AND YEAR(STR_TO_DATE(tse.created_date, '%d/%m/%Y, %H:%i:%s')) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                                                            ";  
                                        }

                                        elseif($_GET['date_range'] == "year")
                                        {
                                            $sort_option = "YEAR(STR_TO_DATE(tse.created_date, '%d/%m/%Y, %H:%i:%s')) = YEAR(CURDATE())";
                                        }

                                        elseif($_GET['date_range'] == "lastyear")
                                        {
                                            $sort_option = "YEAR(STR_TO_DATE(tse.created_date, '%d/%m/%Y, %H:%i:%s')) = YEAR(CURDATE()) - 1";
                                        } else {
                                            ?>
                                                <script>
                                                    // Mengarahkan pengguna ke halaman 404.php
                                                    window.location.replace("404.php");
                                                </script>
                                            <?php
                                        }
                                    } 
                                    
                                    if (isset($_GET["start_date"]) && isset($_GET["end_date"])) {
                                    $dt1 = $_GET["start_date"];
                                    $dt2 = $_GET["end_date"];
                                    $format_dt1 = date('d/m/Y', strtotime($dt1));
                                    $format_dt2 = date('d/m/Y', strtotime($dt2));
                                    $sort_option = "STR_TO_DATE(tse.created_date, '%d/%m/%Y, %H:%i:%s') BETWEEN STR_TO_DATE('$format_dt1', '%d/%m/%Y') AND STR_TO_DATE('$format_dt2', '%d/%m/%Y')";
                                    // Lakukan sesuatu dengan $sort_option, misalnya memproses data dari database
                                    }
                                    $sql = "SELECT 
                                                tse.id_tr_set_ecat, 
                                                tse.qty,  
                                                STR_TO_DATE(tse.created_date, '%d/%m/%Y, %H:%i:%s')  AS created_date,
                                                tpse.nama_set_ecat, 
                                                tpse.kode_set_ecat, 
                                                tpse.id_merk, 
                                                ket_in.ket_in,
                                                mr.nama_merk,
                                                uc.nama_user AS user_created 
                                            FROM tr_set_ecat AS tse
                                            LEFT JOIN tb_produk_set_ecat tpse ON(tse.id_set_ecat = tpse.id_set_ecat)
                                            LEFT JOIN tb_merk mr ON(tpse.id_merk = mr.id_merk)
                                            LEFT JOIN keterangan_in ket_in ON(tse.id_ket_in = ket_in.id_ket_in) 
                                            LEFT JOIN $database2.user AS uc ON (tse.created_by = uc.id_user)
                                            WHERE $sort_option
                                            ORDER BY created_date DESC";
                                    $query = mysqli_query($connect, $sql);
                                    while ($data = mysqli_fetch_array($query)) {
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no; ?></td>
                                            <td><?php echo $data['kode_set_ecat'] ?></td>
                                            <td><?php echo $data['nama_set_ecat'] ?></td>
                                            <td class="text-center"><?php echo $data['nama_merk'] ?></td>
                                            <td class="text-end"><?php echo $data['qty'] ?></td>
                                            <td class="text-center"><?php echo $data['ket_in'] ?></td>
                                            <td class="text-center text-nowrap">
                                                <?php echo $data['user_created'] ?><br>
                                                (<?php echo date('d/m/Y, H:i', strtotime($data['created_date'])) ?>)
                                            </td>
                                            <?php  
                                                if ($role == "Super Admin" || $role == "Manager Gudang" ) { 
                                                    ?>
                                                        <td class="text-center">
                                                            <!-- Hapus Data -->
                                                            <a href="proses/proses-update-stock-set-ecat.php?hapus=<?php echo encrypt($data['id_tr_set_ecat'], $key_global); ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                                                        </td>
                                                    <?php
                                                }
                                            ?>
                                        </tr>
                                        <?php $no++; ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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