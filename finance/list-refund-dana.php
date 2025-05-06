<?php
  $page = 'list-tagihan';
  $page2 = 'tagihan-refund';
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
            <section>
                <!-- Alert -->
                <?php
                    if (isset($_SESSION['info'])) {
                        echo '<div class="info-data" data-infodata="' . $_SESSION['info'] . '"></div>';
                        unset($_SESSION['info']);
                    }
                ?>
                <?php
                    if (isset($_SESSION['gagal'])) { 
                        ?>
                            <script>
                                Swal.fire({
                                    title: "Error!",
                                    text: "<?php echo $_SESSION['gagal']; ?>",
                                    icon: "error",
                                }).then(function() {
                                    <?php unset($_SESSION["gagal"]); ?>
                                });
                            </script>
                        <?php
                    } 
                ?>
                <!-- End alert -->
                <div class="card">
                    <div class="card-header text-center">
                        <h5>Refund Dana</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-start border-top flex-wrap">
                            <div style="margin: 11px;">
                                <p class="fw-bold mt-3" style="font-size: 18px;">Filter Tanggal :</p>
                                <div class="dropdown" style="width: 300px;">
                                    <button id="dropdownButton" class="btn btn-secondary dropdown-toggle form-control" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Pilih Filter
                                    </button>
                                    <ul class="dropdown-menu form-control p-3">
                                        <button type="button" class="btn btn-outline-secondary form-control mb-2 dropdown-item-list" id="hari_ini">Hari Ini</button>
                                        <button type="button" class="btn btn-outline-secondary form-control mb-2 dropdown-item-list" id="minggu_ini">Minggu Ini</a>
                                        <button type="button" class="btn btn-outline-secondary form-control mb-2 dropdown-item-list" id="bulan_ini">Bulan Ini</button>
                                        <button type="button" class="btn btn-outline-secondary form-control mb-2 dropdown-item-list" id="bulan_kemarin">Bulan Kemarin</button>
                                        <button type="button" class="btn btn-outline-secondary form-control mb-2 dropdown-item-list" id="tahun_kemarin">Tahun Kemarin</button>
                                        <li id="dateRangePicker" class="date-range-picker">
                                            <input type="text" id="dateRange" class="form-control mb-2 text-center" placeholder="">
                                            <!-- <button type="button" class="btn btn-primary form-control" id="applyDateRange">Apply</button> -->
                                        </li>
                                        <button type="button" class="btn btn-danger form-control" id="reset">Reset</button>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tableExport">
                                <thead>
                                    <tr style="background-color: navy;">
                                        <th class="text-nowrap text-center p-3">No</th>
                                        <th class="text-nowrap text-center p-3">No Refund</th>
                                        <th class="text-nowrap text-center p-3">No Invoice</th>
                                        <th class="text-nowrap text-center p-3">Tgl.Invoice</th>
                                        <th class="text-nowrap text-center p-3">Nama Customer</th>
                                        <th class="text-nowrap text-center p-3">Nama Customer Invoice</th>
                                        <th class="text-nowrap text-center p-3">Total Invoice</th>
                                        <th class="text-nowrap text-center p-3">Total Pembayaran Cs</th>
                                        <th class="text-nowrap text-center p-3">Total Pembayaran Refund</th>
                                        <th class="text-nowrap text-center p-3">Sisa Pembayaran Refund</th>
                                        <th class="text-nowrap text-center p-3">Alasan Refund</th>
                                        <th class="text-nowrap text-center p-3">Status Refund</th>
                                        <th class="text-nowrap text-center p-3">Tangal Input</th>
                                        <th class="text-nowrap text-center p-3" style="width: 150px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php  
                                    require_once "../function/status-refund.php";
                                    $key = "Fin@nce2024?";
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
                                            $sort_option = "DATE(fnc.created_date) = CURDATE()";
                                        }

                                        elseif($_GET['date_range'] == "weekly")
                                        {
                                            $sort_option = "
                                                            WEEK(fnc.created_date) = WEEK(CURDATE())
                                                            AND YEAR(fnc.created_date) = YEAR(CURDATE())
                                                        ";
                                        }

                                        elseif($_GET['date_range'] == "monthly")
                                        {

                                            $sort_option = "
                                                            MONTH(fnc.created_date) = MONTH(CURDATE())
                                                            AND YEAR(fnc.created_date) = YEAR(CURDATE())
                                                        "; 
                                            
                                        }

                                        elseif($_GET['date_range'] == "lastMonth")
                                        {
                                            $sort_option = " 
                                                            MONTH(fnc.created_date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                                                            AND YEAR(fnc.created_date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                                                            ";  
                                        }

                                        elseif($_GET['date_range'] == "year")
                                        {
                                            $sort_option = "YEAR(fnc.created_date) = YEAR(CURDATE())";
                                        }

                                        elseif($_GET['date_range'] == "lastyear")
                                        {
                                            $sort_option = "YEAR(fnc.created_date) = YEAR(CURDATE()) - 1";
                                        } else {
                                            ?>
                                                <script>
                                                    // Mengarahkan pengguna ke halaman 404.php
                                                    window.location.replace("../404.php");
                                                </script>
                                            <?php
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
                                                refund.id_refund,
                                                refund.no_refund,
                                                refund.total_inv,
                                                refund.alasan_refund,
                                                refund.status_refund,
                                                refund.created_date,
                                                refund.created_by,
                                                fnc.id_finance,
                                                fnc.status_lunas,
                                                fnc.jenis_inv,
                                                COALESCE(byr.total_bayar, 0) AS total_pembayaran,
                                                COALESCE(byr_refund.total_bayar_refund, 0) AS total_pembayaran_refund,
                                                COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv, 
                                                COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,  
                                                COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,  
                                                COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                                us.nama_user                         
                                            FROM finance_refund AS refund
                                            LEFT JOIN $database2.user us ON (refund.created_by = us.id_user)
                                            LEFT JOIN finance fnc ON (refund.id_refund = fnc.id_tagihan)
                                            LEFT JOIN (
                                                SELECT id_tagihan, SUM(total_bayar) AS total_bayar
                                                FROM finance_bayar
                                                GROUP BY id_tagihan
                                            ) byr ON (refund.id_refund = byr.id_tagihan)
                                             LEFT JOIN (
                                                SELECT id_refund, SUM(total_bayar) AS total_bayar_refund
                                                FROM finance_bayar_refund
                                                GROUP BY id_refund
                                            ) byr_refund ON (refund.id_refund = byr_refund.id_refund)
                                            LEFT JOIN inv_nonppn nonppn ON (fnc.id_inv = nonppn.id_inv_nonppn)
                                            LEFT JOIN inv_ppn ppn ON (fnc.id_inv = ppn.id_inv_ppn)
                                            LEFT JOIN inv_bum bum ON (fnc.id_inv = bum.id_inv_bum)
                                            WHERE $sort_option
                                            GROUP BY refund.id_refund
                                            ORDER BY refund.no_refund ASC";
                                    $query = mysqli_query($connect, $sql);
                                    while ($data = mysqli_fetch_array($query)) {
                                        $id_refund = $data['id_refund'];
                                        $total_bayar = $data['total_pembayaran'];
                                        $total_bayar_refund = $data['total_pembayaran_refund'];
                                        $total_tagihan = $data['total_inv'];
                                        $total_sisa_pembayaran = $total_bayar - $total_bayar_refund;
                                        $no_tagihan = $data['no_refund'];
                                        $id_inv = $data['id_inv'];
                                        $id_inv_encrypt = encrypt($id_inv, $key);
                                        $jenis_inv = $data['jenis_inv'];
                                        $no_refund = $data['no_refund'];
                                        $no_inv = $data['no_inv'];
                                        $tgl_refund = date("d/m/Y", strtotime($data['created_date']));
                                        $tgl_inv = $data['tgl_inv'];
                                        $cs_inv = $data['cs_inv'];
                                        $id_refund = encrypt($data['id_refund'], $key);
                                        $id_finance = $data['id_finance'];
                                        $display = ($total_sisa_pembayaran == '0') ? 'none' : 'block';
                                        $status = $data['status_refund'];
                                    ?>
                                    <!-- Query untuk menampilkan Jenis Pengiriman -->
                                    <?php 
                                        $sql_status_kirim = $connect->query("SELECT 
                                                                                sk.jenis_pengiriman,
                                                                                sk.dikirim_driver,
                                                                                sk.dikirim_ekspedisi,
                                                                                ip.nama_penerima,
                                                                                ip.alamat,
                                                                                us.nama_user,
                                                                                eks.nama_ekspedisi
                                                                            FROM status_kirim AS sk
                                                                            LEFT JOIN inv_penerima ip ON (sk.id_inv = ip.id_inv)
                                                                            LEFT JOIN $database2.user us ON (sk.dikirim_driver = us.id_user)
                                                                            LEFT JOIN ekspedisi eks ON (sk.dikirim_ekspedisi = eks.id_ekspedisi)
                                                                            WHERE sk.id_inv = '$id_inv'");
                                        $data_status_kirim = mysqli_fetch_array($sql_status_kirim);
                                        $alamat = $data_status_kirim['alamat'];  
                                        $nama = "";
                                        $jenis_pengiriman = "";

                                        if($data_status_kirim['jenis_pengiriman'] == "Driver"){
                                            $jenis_pengiriman = $data_status_kirim['jenis_pengiriman'];
                                            $nama = $data_status_kirim['nama_user'];
                                        } else if($jenis_pengiriman == "Ekspedisi"){
                                            $jenis_pengiriman = $data_status_kirim['jenis_pengiriman'];
                                            $nama = $data_status_kirim['nama_ekspedisi'];
                                        } else if ($jenis_pengiriman == "Diambil Langsung") {
                                            $jenis_pengiriman = $data_status_kirim['jenis_pengiriman'];
                                            $nama = $data_status_kirim['nama_penerima'];
                                        } else {
                                            echo "Not Found";
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-nowrap text-center"><?php echo $no; ?></td>
                                        <td class="text-nowrap text-center"><?php echo $no_refund ?></td>
                                        <td class="text-nowrap text-center"><?php echo $no_inv ?></td>
                                        <td class="text-nowrap text-center"><?php echo $data['tgl_inv']; ?></td>
                                        <td class="text-nowrap">
                                            <?php 
                                                $sql_cs = $connect->query("SELECT cs.id_cs, cs.nama_cs
                                                                            FROM spk_reg AS spk
                                                                            LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                                                            WHERE spk.id_inv = '$id_inv'");
                                                $data_cs = mysqli_fetch_array($sql_cs);
                                                $id_cs = encrypt($data_cs['id_cs'], $key);
                                                $nama_cs = $data_cs['nama_cs'];
                                                echo $nama_cs;  
                                            ?>
                                        </td>
                                        <td class="text-nowrap"><?php echo $data['cs_inv']; ?></td>
                                        <td class="text-nowrap text-end"><?php echo number_format($data['total_inv']); ?></td>
                                        <td class="text-nowrap text-end"><?php echo number_format($total_bayar); ?></td>
                                        <td class="text-nowrap text-end"><?php echo number_format($total_bayar_refund); ?></td>
                                        <td class="text-nowrap text-end"><?php echo number_format($total_sisa_pembayaran); ?></td>
                                        <td class="text-nowrap"><?php echo $data['alasan_refund']; ?></td> 
                                        <td class="text-nowrap"><?php echo  getStatusRefund($status) ?></td> 
                                        <td class="text-nowrap text-center">
                                            <?php echo $data['created_date']; ?><br>
                                            (<?php echo $data['nama_user']; ?>)
                                        </td>
                                        <td class="text-nowrap text-center">
                                            <div class="d-flex justify-content-center flex-wrap" style="width: 100px;">
                                                <button class="btn btn-secondary btn-sm view_data m-1" title="History Payment" data-bs-toggle="modal" data-bs-target="#history" data-id="<?php echo $id_refund ?>"><i class="bi bi-card-checklist"></i></button>

                                                <button class="btn btn-primary btn-sm view_bukti m-1" title="Bukti Kirim" data-bs-toggle="modal" data-bs-target="#buktiKirim"  data-id="<?php echo $id_finance ?>"><i class="bi bi-card-image"></i></button>

                                                <button class="btn btn-info btn-sm m-1" style="display: <?php echo $display ?>;" title="Lanjut Pembayaran" data-bs-toggle="modal" data-bs-target="#lanjutPembayaran" data-id="<?php echo $id_finance ?>" data-idinv="<?php echo $id_inv_encrypt ?>"
                                                data-idrefund="<?php echo $id_refund ?>"
                                                data-jenisinv="<?php echo $jenis_inv ?>" data-no="<?php echo $no_refund ?>" data-tgl="<?php echo $tgl_refund ?>" data-tglinv="<?php echo $tgl_inv ?>" data-noinv="<?php echo $no_inv ?>" data-idcs="<?php echo $id_cs ?>" data-cs="<?php echo $nama_cs ?>" data-csinv="<?php echo $cs_inv ?>" data-alamat="<?php echo $alamat ?>" data-jenis="<?php echo $jenis_pengiriman; ?>" data-nama="<?php echo $nama ?>" data-bayar="<?php echo $total_sisa_pembayaran; ?>"><i class="bi bi-credit-card"></i></button>

                                                <button class="btn btn-warning btn-sm m-1" title="Komplain Tagihan" data-bs-toggle="modal" data-bs-target="#komplain" data-id="<?php echo $id_finance ?>" data-idinv="<?php echo $id_inv ?>" data-no="<?php echo $no_refund ?>" data-noinv="<?php echo $no_inv ?>" data-cs="<?php echo $nama_cs ?>"><i class="bi bi-exclamation-circle-fill"></i></button>

                                                <button class="btn btn-danger btn-sm m-1" title="Batal Refund" data-bs-toggle="modal" data-bs-target="#batalRefund" data-id="<?php echo encrypt($id_finance, $key) ?>" data-idrefund="<?php echo $id_refund ?>" data-no="<?php echo $no_refund ?>" data-noinv="<?php echo $no_inv ?>" data-cs="<?php echo $nama_cs ?>"><i class="bi bi-x"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $no++; ?>
                                    <?php } ?>
                                </tbody>
                            </table> 
                        </div>
                    </div>
                </div>
                <!-- Modal History -->
                <div class="modal fade" id="history" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">History Pembayaran Refund</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="history-x"></button>
                            </div>
                            <div class="modal-body">
                                <div class="card">
                                    <div class="card-body" id="detail_id">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End History -->
                <!-- Modal gambar History -->
                <div class="modal fade" id="bukti" data-bs-keyboard="false" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imageModalLabel">Bukti Transfer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="bukti-x"></button>
                            </div>
                            <div class="modal-body" id="detail_bukti">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal gambar History -->
            </section>
        </main>
        <!-- Modal Bukti Kirim -->
        <div class="modal fade" id="buktiKirim" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Bukti Kirim</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="buktiKirim-x"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card-body" id="bukti_kirim">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Bukti Kirim -->
        <!-- Modal Komplain -->
        <div class="modal fade" id="komplain" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Konfirmasi Komplain Tagihan</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="komplain-invoice.php" method="POST">
                            <p>
                                Apakah anda yakin untuk melakukan komplain dengan<br>
                                <b>No.Refund: <span id="refund_no"></span></b> dan <b>Invoice: <span id="invoice_no"></span> (<span id="nama_cs"></span>)</b> ?
                            </p>
                            <input type="hidden" class="form-control" id="id_invoice" name="id_inv" readonly>
                            <div class="modal-footer mt-3">
                                <button type="submit" class="btn btn-primary" name="komplain">Ya, Lanjutkan</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Komplain -->
        <!-- Modal Pembayaran Refund -->
        <div class="modal fade" id="lanjutPembayaran" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"">
                <div class="modal-content">
                    <style>
                        .img-bank{
                            height: 60px;
                            width: 140px;
                        }
                    </style>
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Pembayaran Refund</h1>
                        <a href="list-refund-dana.php?date_range=year" class="btn btn-secondary btn-close"></a>
                    </div>
                    <div class="modal-body">
                        <div class="card border">
                            <div class="card-body m-2 border">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-nowrap" style="width: 22%">No. Refund</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 76%" id="pb_refund_no"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width: 22%">Tgl. Refund</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 76%" id="pb_refund_tgl"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width: 22%">No. Invoice</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 76%" id="pb_invoice_no"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width: 22%">Tgl. Invoice</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 76%" id="pb_inv_tgl"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="card-body m-2 border">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-nowrap" style="width: 22%">Nama Cs</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 76%" id="pb_nama_cs"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width: 22%">Nama Cs Inv</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 76%" id="pb_cs_inv"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width: 22%">Alamat Pengiriman</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-wrap" style="width: 76%" id="pb_alamat"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width: 22%">Jenis Pengiriman</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 76%"><span id="pb_jenis_pengiriman"></span> (<b id="pb_nama"></b>)</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <form action="proses/bayar-refund.php" method="POST" enctype="multipart/form-data">
                            <div class="card-body">
                                <?php  
                                    date_default_timezone_set('Asia/Jakarta');
                                    include "../function/uuid.php";
                                    $uuid = uuid();
                                    $day = date('d');
                                    $month = date('m');
                                    $year = date('y');
                                    $id_bayar = "BYR" . $year . "" . $month . "" . $uuid . "" . $day ;
                                    $id_bayar_encrypt = encrypt($id_bayar, $key);
                                ?>
                                <input type="hidden" name="id_bayar" value="<?php echo $id_bayar_encrypt ?>" readonly>
                                <input type="hidden" name="id_cs" id="pb_id_cs_val" readonly>
                                <input type="hidden" name="id_inv" id="pb_id_invoice" readonly>
                                <input type="hidden" name="jenis_inv" id="pb_jenis_invoice" readonly>
                                <input type="hidden" name="id_refund" id="pb_id_refund" readonly>
                                <div class="mb-3">
                                    <label>Total Tagihan</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">Rp</span>
                                        <input type="text" class="form-control" name="total_tagihan" id="pb_bayar" readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Metode Pembayaran :</label>
                                    <div class="row">
                                        <div class="col">
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <input class="form-check-input mt-0" type="radio" id="cash" name="metode_pembayaran" value="cash" onclick="checkRadio()" required>
                                                </div>
                                                <input type="text" class="form-control" value="Cash" readonly>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <input class="form-check-input mt-0" type="radio" id="transfer" name="metode_pembayaran" value="transfer" onclick="checkRadio()" required>
                                                </div>
                                                <input type="text" class="form-control" value="Transfer" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="metode" style="display: none;">
                                    <div class="mb-3">
                                        <label>Pilih Bank :</label>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between flex-wrap p-3">
                                                <?php  
                                                    $no = 1;
                                                    $sql_bank ="SELECT 
                                                                    bt.id_bank_pt,
                                                                    bt.no_rekening,
                                                                    bt.atas_nama,
                                                                    bk.nama_bank,
                                                                    bk.logo
                                                                FROM bank_pt AS bt
                                                                LEFT JOIN bank bk ON (bk.id_bank = bt.id_bank)
                                                                ORDER BY nama_bank ASC";
                                                    $query_bank = mysqli_query($connect, $sql_bank);
                                                    $total_data_bank = mysqli_num_rows($query_bank);
                                                    while($data_bank = mysqli_fetch_array($query_bank)){
                                                        $no_rek = $data_bank['no_rekening'];
                                                        $atas_nama = $data_bank['atas_nama'];
                                                        $logo = $data_bank['logo'];
                                                        $logo_img = "logo-bank/$logo";
                                                    ?>
                                                    <div class="card" style="width: 20rem;">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-10">
                                                                    <img src="<?php echo $logo_img ?>" class="img-bank" alt="...">
                                                                </div>
                                                                <div class="col-2 text-end">
                                                                    <input class="form-check-input mt-3" type="radio" id="id_bank_<?php echo $data_bank['id_bank_pt']; ?>" name="id_bank_pt" value="<?php echo $data_bank['id_bank_pt']; ?>">
                                                                </div>
                                                            </div>
                                                            <p class="card-text">
                                                                <?php echo $no_rek; ?><br>
                                                                <b><?php echo $atas_nama ?></b>
                                                            </p>
                                                        </div>
                                                    </div> 
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label>Nama Penerima(*)</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" name="nama_pengirim" id="nama_pengirim">
                                            <button class="btn btn-primary" type="button" id="openPilihRek" style="display: block;">
                                                <i class="bi bi-search"></i>
                                            </button>
                                            <button class="btn btn-danger" id="reset" type="button" style="display: none;">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label>Rekening Penerima(*)</label>
                                        <input type="number" class="form-control" name="rek_pengirim" id="rek_pengirim">
                                    </div>
                                    <div class="mb-3">
                                        <label>Bank Penerima(*)</label>
                                        <input type="hidden" class="form-control bg-light" name="id_bank_pengirim" id="id_bank_pengirim">
                                        <input type="text" class="form-control bg-light" name="bank_pengirim" id="bank_pengirim" style="display: none;">
                                    </div>
                                    <div id="selectData" class="mb-3" style="display: block;">
                                        <select name="id_bank_select" class="form-select selectize-js">
                                            <option value=""></option>
                                            <?php  
                                                $sql_bank = "SELECT id_bank, nama_bank FROM bank ORDER BY nama_bank ASC";
                                                $query_bank = mysqli_query($connect, $sql_bank);
                                                while($data_bank = mysqli_fetch_array($query_bank)){
                                                    $id_bank = $data_bank['id_bank'];
                                                    $nama_bank = $data_bank['nama_bank'];
                                            ?>
                                                <option value="<?php echo $id_bank ?>"><?php echo $nama_bank ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label>Bukti Transfer :</label>
                                    </div>
                                    <div class="mb-3">
                                        <input type="file" name="fileku1" id="fileku1" accept=".jpg, .jpeg, .png" onchange="compressAndPreviewImage(event)">
                                    </div>
                                    <div class="mb-3 preview-image" id="imagePreview"></div>
                                </div>
                                <div id="nominalDisplay" style="display: none">
                                    <div id="date-picker-wrapper" class="mb-3">
                                        <label>Tanggal Bayar</label>
                                        <input type="text" class="form-control" name="tgl_bayar" id="date">
                                    </div>
                                    <div class="mb-3">
                                        <label>Keterangan Bayar(*)</label>
                                        <input type="text" class="form-control" name="keterangan_bayar">
                                    </div>
                                    <div class="mb-3">
                                        <label>Nominal</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">Rp</span>
                                            <input type="text" class="form-control" name="nominal" id="nominal" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label>Sisa Tagihan</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">Rp</span>
                                            <input type="text" class="form-control" name="sisa_tagihan" id="sisa_tagihan" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="list-refund-dana.php?date_range=year" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary" name="simpan-pembayaran">Simpan Pembayaran</button>
                            </div>
                        </form>
                        <p><b>NB: Jika data Bank kosong maka button simpan tidak dapat digunakan</b></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Pembayaran Refund -->
        <!-- Modal Pilih Rekening CS -->
        <div class="modal fade" id="pilihRek" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Pilih Rekening Customer</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="tableBody">
                    </div>
                </div>
            </div>
        </div>
        <!-- End Pilih Rek CS -->
    </div>
    <!-- Modal Cancel Refund -->
    <div class="modal fade" id="batalRefund" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5 fw-bold" id="staticBackdropLabel">Konfirmasi Batal Refund</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="proses/batal-refund.php" method="POST">
                            <p>
                                Apakah anda yakin untuk melakukan pembatalan refund dana dengan<br>
                                <b>No.Refund: <span id="batal_refund_no"></span></b> dan <b>Invoice: <span id="batal_invoice_no"></span> (<span id="batal_nama_cs"></span>)</b> ?
                            </p>
                            <input type="hidden" class="form-control" id="batal_id_refund" name="id_refund" readonly>
                            <input type="hidden" class="form-control" id="batal_id_finance" name="id_finance" readonly>
                            <div class="modal-footer mt-3">
                                <button type="submit" class="btn btn-primary" name="hapus_refund">Ya, Lanjutkan</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Cancel Refund -->
    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
    <!-- Script untuk lighbox -->
    <script src="../assets/vendor/lightbox/dist/js/picturefill.min.js"></script>
    <script src="../assets/vendor/lightbox/dist/js/lightgallery-all.min.js"></script>
    <script src="../assets/vendor/lightbox/lib/jquery.mousewheel.min.js"></script>
</body>
</html>
<?php include "page/upload-img.php";  ?>
<style>
    .preview-image {
        max-width: 100%;
        height: auto;
    }
</style>
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
<!-- Untuk menampilkan data Histori pad amodal -->
<script>
    $(document).ready(function(){
        $('.view_data').click(function(){
            var data_id = $(this).data("id");
            $.ajax({
                url: "history-pembayaran-refund.php",
                method: "POST",
                data: {data_id: data_id},
                success: function(data){
                    $("#detail_id").html(data);
                    $("#history").modal('show');
                }
            });
        });
    });
</script>
<!--End Script Untuk Modal History -->

<!-- Untuk menampilkan data Histori pad amodal -->
<script>
    $(document).ready(function(){
        $('.view_bukti').click(function(){
            var data_id = $(this).data("id");
            $.ajax({
                url: "bukti-kirim.php",
                method: "POST",
                data: {data_id: data_id},
                success: function(data){
                    $("#bukti_kirim").html(data);
                    $("#buktiKirim").modal('show');
                }
            });
        });
    });
</script>
<!--End Script Untuk Modal History -->

<script>
    document.getElementById('history-x').addEventListener('click', function() {
        location.reload();
    });
</script>

<!-- Komplain -->
<script>
    $(document).ready(function() {
        $('#komplain').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Tombol yang memicu modal
            var idFinance = button.data('id'); // Ambil nilai dari data-id
            var idInv = button.data('idinv'); // Ambil nilai dari data-id
            var noRefund = button.data('no'); // Ambil nilai dari data-no
            var noInv = button.data('noinv'); // Ambil nilai dari data-cs
            var namaCS = button.data('cs'); // Ambil nilai dari data-cs

            // Tempatkan nilai-nilai ini di dalam modal
            $('#id_finance').text(idFinance);
            $('#id_invoice').val(idInv);
            $('#refund_no').text(noRefund);
            $('#invoice_no').text(noInv);
            $('#nama_cs').text(namaCS);
            // Jika perlu, Anda dapat menambahkan nama CS ke dalam modal juga
        });
    });
</script>

<!-- Pembayaran refund -->
<script>
    function checkRadio() {
        var idBankRadios = document.getElementsByName("id_bank_pt");
        var transferCheck = document.getElementById('transfer');
        var cashCheck = document.getElementById('cash');
        var namaPengirim = document.getElementById('nama_pengirim');
        var rekPengirim = document.getElementById('rek_pengirim');
        var bankPengirim = document.getElementById('bank_pengirim');
        var fileku = document.getElementById('fileku1');
        var metode = document.getElementById('metode');
        var nominalDisplay = document.getElementById('nominalDisplay');
        var totalTagihanInput = document.getElementById('pb_bayar');
        var sisaTagihanInput = document.getElementById('sisa_tagihan');
        var tombolSimpan = document.querySelector('button[name="simpan-pembayaran"]');
        var nominalInput = document.getElementById('nominal');

        if (cashCheck.checked) {
            for (var i = 0; i < idBankRadios.length; i++) {
                idBankRadios[i].checked = false;
                idBankRadios[i].required = false;
            }
            metode.style.display = 'none';
            nominalDisplay.style.display = 'block';
            namaPengirim.value = '';
            rekPengirim.value = '';
            bankPengirim.value = '';
            sisaTagihanInput.value = totalTagihanInput.value;
            fileku.removeAttribute('required');
            if (parseFloat(sisaTagihanInput.value) < 0) {
                tombolSimpan.disabled = true;
            }
        } else if (transferCheck.checked) {
            for (var i = 0; i < idBankRadios.length; i++) {
                idBankRadios[i].required = true;
            }
            metode.style.display = 'block';
            nominalDisplay.style.display = 'block';
            sisaTagihanInput.value = totalTagihanInput.value;
            fileku.setAttribute('required', 'true');
            if (parseFloat(sisaTagihanInput.value) < 0) {
                tombolSimpan.disabled = true;
            }
            nominalInput.value = '';
        }
    }
    $(document).ready(function() {
        $('#lanjutPembayaran').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var idFinance = button.data('id');
            var idInv = button.data('idinv');
            var idRefund = button.data('idrefund');
            var jenisInv = button.data('jenisinv');
            var noRefund = button.data('no');
            var tglRefund = button.data('tgl');
            var tglInv = button.data('tglinv');
            var noInv = button.data('noinv');
            var idCs = button.data('idcs');
            var namaCs = button.data('cs');
            var namaCsInv = button.data('csinv');
            var jenisPengiriman = button.data('jenis');
            var nama = button.data('nama');
            var alamat = button.data('alamat');
            var bayar = button.data('bayar');

            $('#pb_id_finance').text(idFinance);
            $('#pb_id_invoice').val(idInv);
            $('#pb_id_refund').val(idRefund);
            $('#pb_jenis_invoice').val(jenisInv);
            $('#pb_refund_no').text(noRefund);
            $('#pb_refund_tgl').text(tglRefund);
            $('#pb_inv_tgl').text(tglInv);
            $('#pb_invoice_no').text(noInv);
            $('#pb_id_cs_val').val(idCs);
            $('#pb_id_cs').text(idCs);
            $('#pb_nama_cs').text(namaCs);
            $('#pb_cs_inv').text(namaCsInv);
            $('#pb_jenis_pengiriman').text(jenisPengiriman);
            $('#pb_nama').text(nama);
            $('#pb_alamat').text(alamat);
            $('#pb_bayar').val(bayar.toLocaleString('id-ID'));

            // Fungsi untuk memuat data tabel
            function loadTable(id_cs) {
                $.ajax({
                    url: 'ajax/data-rekening-cs.php',
                    type: 'POST',
                    data: { id_cs: id_cs },
                    success: function(response) {
                        $('#tableBody').html(response);
                    }
                });
            }

            // Muat data tabel saat modal dibuka
            $('#pilihRek').on('show.bs.modal', function (event) {
                loadTable(idCs);
            });

        });

        const totalTagihanInput = document.getElementById("pb_bayar");
        const nominalInput = document.getElementById("nominal");
        const sisaTagihanInput = document.getElementById("sisa_tagihan");

        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        function parseNumber(str) {
            const parsedValue = parseFloat(str.replace(/\./g, "").replace(",", "."));
            return isNaN(parsedValue) ? 0 : parsedValue;
        }

        function formatInputNominal() {
            let value = nominalInput.value;
            value = value.replace(/\./g, "");
            value = value.replace(/,/g, ".");
            nominalInput.value = formatNumber(value);
        }

        function calculateSisaTagihan() {
            const totalTagihan = parseNumber(totalTagihanInput.value);
            let nominal = parseNumber(nominalInput.value);

            if (nominal > totalTagihan) {
                nominal = totalTagihan;
                nominalInput.value = formatNumber(nominal);
            }

            const sisaTagihan = totalTagihan - nominal;
            sisaTagihanInput.value = formatNumber(sisaTagihan);
        }

        function initializeModal() {
            nominalInput.addEventListener("input", () => {
                let inputValue = nominalInput.value;
                inputValue = inputValue.replace(/[^\d]/g, "");
                nominalInput.value = inputValue;
                formatInputNominal();
                calculateSisaTagihan();
            });
        }

        initializeModal();

        // select data bank CS
        $(document).on('click', '#pilih', function (e) {
            var atasNama = $(this).data('an');
            var noRek = $(this).data('rek');
            var idBank = $(this).data('id-bank');
            var namaBank = $(this).data('bank');
            // Trigger event input setelah mengubah nilai
            $('#nama_pengirim').val(atasNama).trigger('input'); 
            $('#rek_pengirim').val(noRek).trigger('input'); 
            $('#id_bank_pengirim').val(idBank).trigger('input'); 
            $('#bank_pengirim').val(namaBank).trigger('input'); 

            // Memeriksa nilai elemen input setelah diatur
            var namaPengirimValue = $('#nama_pengirim').val();
            var rekPengirimValue = $('#rek_pengirim').val();
            var idBankPengirimValue = $('#id_bank_pengirim').val();
            var bankPengirimValue = $('#bank_pengirim').val();

            if (namaPengirimValue && rekPengirimValue && bankPengirimValue) {
                // Jika semua nilai ada, ubah display menjadi block
                $('#bank_pengirim').css('display', 'block');
                $('#reset').css('display', 'block');
                
                // Sembunyikan elemen <div> dengan id "selectData"
                $('#selectData').css('display', 'none');
                $('#cari').css('display', 'none');
            } else {
                // Jika salah satu atau lebih nilai tidak ada, ubah display menjadi none
                $('#bank_pengirim').css('display', 'none');
                $('#reset').css('display', 'none');
                
                // Tampilkan kembali elemen <div> dengan id "selectData"
                $('#selectData').css('display', 'block');
                $('#cari').css('display', 'block');
            }

            $('#pilihRek').modal('hide');
        });

        // Reset Button
        $(document).on('click', '#reset', function (e) {
            // Mengosongkan nilai input
            $('#nama_pengirim').val('').trigger('input');
            $('#rek_pengirim').val('').trigger('input');
            $('#id_bank_pengirim').val('').trigger('input');
            $('#bank_pengirim').val('').trigger('input');

            // Menampilkan kembali elemen <div> dengan id "selectData"
            $('#selectData').css('display', 'block');
            $('#cari').css('display', 'block');

            // Sembunyikan elemen <div> dengan id "bank_pengirim"
            $('#bank_pengirim').css('display', 'none');
            $('#reset').css('display', 'none');
        });
    });
</script>

<script>
    // Reference to modals
    var lanjutPembayaran = new bootstrap.Modal(document.getElementById('lanjutPembayaran'));
    var pilihRek = new bootstrap.Modal(document.getElementById('pilihRek'));

    // Open inner modal without hiding the outer modal
    document.getElementById('openPilihRek').addEventListener('click', function () {
        pilihRek.show();
    });

    // Ensure body class `modal-open` remains if any modal is open
    document.getElementById('pilihRek').addEventListener('show.bs.modal', function () {
        var openModals = document.querySelectorAll('.modal.show');
        if (openModals.length > 0) {
            document.body.classList.add('modal-open');
        }
    });

    // Restore body class `modal-open` to ensure proper scrolling behavior
    document.getElementById('pilihRek').addEventListener('hidden.bs.modal', function () {
        var openModals = document.querySelectorAll('.modal.show');
        if (openModals.length > 0) {
            document.body.classList.add('modal-open');
        } else {
            document.body.classList.remove('modal-open');
        }
    });
</script>

<!-- Hapus Refund -->
<script>
    $(document).ready(function() {
        $('#batalRefund').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Tombol yang memicu modal
            var idFinance = button.data('id'); // Ambil nilai dari data-id
            var idRefund = button.data('idrefund'); // Ambil nilai dari data-id
            var noRefund = button.data('no'); // Ambil nilai dari data-no
            var noInv = button.data('noinv'); // Ambil nilai dari data-cs
            var namaCS = button.data('cs'); // Ambil nilai dari data-cs

            // Tempatkan nilai-nilai ini di dalam modal
            $('#batal_id_finance').val(idFinance);
            $('#batal_id_refund').val(idRefund);
            $('#batal_refund_no').text(noRefund);
            $('#batal_invoice_no').text(noInv);
            $('#batal_nama_cs').text(namaCS);
            // Jika perlu, Anda dapat menambahkan nama CS ke dalam modal juga
        });
    });
</script>


