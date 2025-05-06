<?php
$page = 'list-cs';
$page2 = 'spk';
include "akses.php";
include "function/class-spk.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <?php include "page/head.php";?>

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
            transform: translate(-35px, 40px);
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
    <?php include "page/nav-header.php"?>
    <!-- end nav header -->

    <!-- sidebar -->
    <?php include "page/sidebar.php" ?>
    <!-- end sidebar -->

    <main id="main" class="main">
        <!-- Query -->
        <?php include "koneksi.php"; ?>
        <?php include "finance/query/query-fnc-cs.php" ?>
        <section>
            <div class="card shadow p-3">
                <div class="card-header text-center">
                    <h5>Penjualan Customer</h5>
                </div>
                <div class="card-body p-3">
                    <div class="col-sm-1">
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
                                        <a href="finance-customer.php?date_range=weekly" name="tampilkan" class="custom-dropdown-item dropdown-item rounded bg-danger text-white" id="resetLink">Reset</a>
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

                                                const newUrl = `finance-customer.php?${queryParams.toString()}`;

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
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm p-2">
                            <div class="text-center border p-3">
                                <p><b>Total Customer</b></p>
                                <p><?php echo $total_data; ?></p>
                            </div>
                        </div>
                        <?php  
                            $total = 0;
                            $total_pending = 0;
                            $total_bayar = 0;
                            $total_sisa_tagihan = 0;
                            while ($data2 = mysqli_fetch_array($query2)) {
                                $total += $data2['total_nominal_inv_selesai'];
                                $total_pending += $data2['total_nominal_inv_belum_selesai'];
                                $total_bayar += $data2['total_bayar'];
                                $sisa_belum_bayar = $total - $total_bayar;
                                $total_sisa_tagihan += $data2['selisih_bayar_dan_nominal'];
                            ?>
                        <?php } ?>
                        <div class="col-sm p-2">
                            <div class="text-center border p-3">
                                <p><b>Total Nominal Transaksi</b></p>
                                <p><?php echo number_format($total) ?></p>
                            </div>
                        </div>
                        <div class="col-sm p-2">
                            <div class="text-center border p-3">
                                <p><b>Total Pending Nominal</b></p>
                                <p><?php echo number_format($total_pending) ?></p>
                            </div>
                        </div>
                        <div class="col-sm p-2">
                            <div class="text-center border p-3">
                                <p><b>Total Tagihan Transaksi</b></p>
                                <p><?php echo number_format($total_sisa_tagihan)?></p>
                            </div>
                        </div>
                        <div class="col-sm p-2">
                            <div class="text-center border p-3">
                                <p><b>Total Belum Bayar</b></p>
                                <p><?php echo number_format($sisa_belum_bayar)?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="table2">
                            <thead>
                                <tr class="text-white"  style="background-color: navy;">
                                    <td class="text-center text-nowrap p-3">No</td>
                                    <td class="text-center text-nowrap p-3">Nama Customer</td>
                                    <td class="text-center text-nowrap p-3">Transaksi Selesai</td>
                                    <td class="text-center text-nowrap p-3">Transaksi Pending</td>
                                    <td class="text-center text-nowrap p-3">Total Nominal</td>
                                    <td class="text-center text-nowrap p-3">Total Pending</td>
                                    <td class="text-center text-nowrap p-3">Total Tagihan</td>
                                    <td class="text-center text-nowrap p-3">Total Belum Bayar</td>
                                    <td class="text-center text-nowrap p-3">Aksi</td>
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
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $no ?></td>
                                    <td class="text-nowrap"><?php echo $data['nama_cs'] ?></td>
                                    <td class="text-nowrap text-center"><?php echo $data['total_transaksi_selesai'] ?></td>
                                    <td class="text-nowrap text-center"><?php echo $data['total_transaksi_belum_selesai'] ?></td>
                                    <td class="text-nowrap text-end"><?php echo number_format($data['total_nominal_inv_selesai']) ?></td>
                                    <td class="text-nowrap text-end"><?php echo number_format($data['total_nominal_inv_belum_selesai']) ?></td>
                                    <td class="text-nowrap text-end"><?php echo number_format($sisa_tagihan) ?></td>
                                    <td class="text-nowrap text-end"><?php echo number_format($sisa_nominal_tagihan) ?></td>
                                    <td class="text-center text-nowrap">
                                        <a href="detail-invoice-customer.php?date_range=year&cs=<?php echo base64_encode($data['id_cs']) ?>" class="btn btn-primary btn-sm" title="Detail Invoice">
                                            <i class="bi bi-eye"></i>
                                        </a>
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
    </main>
    <!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php"?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <?php include "page/script.php"?>
</body>
</html>