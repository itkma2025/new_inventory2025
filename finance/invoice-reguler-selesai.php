<?php
require_once "../akses.php";
$page = 'spk';
$page_nav = 'selesai'; 
require_once "../function/function-enkripsi.php";
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

    <style>
        @media (max-width: 767px) {

            /* Tambahkan aturan CSS khusus untuk tampilan mobile di bawah 767px */
            .col-12.col-md-2 {
                /* Contoh: Mengatur tinggi elemen select pada tampilan mobile */
                height: 50px;
            }

            #tablenonppn_wrapper,
            #tableppn_wrapper,
            #tablebum_wrapper .col-md-6:eq(0) .btn-group {
                display: block;
                margin-bottom: 10px;
            }

            #table7_wrapper,
            #table8_wrapper,
            #table9_wrapper .col-md-6:eq(0) .btn-group {
                display: block;
                margin-bottom: 10px;
            }

            #tablenonppn_filter,
            #tableppn_filter,
            #tablebum_filter {
                margin-top: 20px;
            }

            #table7_filter,
            #table8_filter,
            #table9_filter {
                margin-top: 20px;
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
        <!-- SWEET ALERT -->
        <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
        <!-- END SWEET ALERT -->
        <!-- Loading -->
        <div class="loader loader">
            <div class="loading">
                <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div>
        <!-- ENd Loading -->
        <div class="pagetitle">
            <h1>Data SPK</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">SPK</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section>
            <div class="card">
                <div class="mt-4">
                    <!-- Tampilkan navbar spk -->
                    <?php include "page/navbar-spk.php" ?>
                    <div class="card-body bg-body rounded mt-3">
                        <button class="btn btn-outline-dark mb-3" id="btnNonPpn">
                            Invoice Non PPN &nbsp;
                            <?php if ($total_inv_nonppn_selesai != 0) {
                                echo '<span class="badge text-bg-secondary">' . $total_inv_nonppn_selesai . '</span>';
                            } ?>
                        </button>

                        <button class="btn btn-outline-dark mb-3" id="btnPpn">
                            Invoice PPN &nbsp;
                            <?php if ($total_inv_ppn_selesai != 0) {
                                echo '<span class="badge text-bg-secondary">' . $total_inv_ppn_selesai . '</span>';
                            } ?>
                        </button>

                        <button class="btn btn-outline-dark mb-3" id="btnBum">
                            Invoice BUM &nbsp;
                            <?php if ($total_inv_bum_selesai != 0) {
                                echo '<span class="badge text-bg-secondary">' . $total_inv_bum_selesai . '</span>';
                            } ?>
                        </button>
                        <div class="d-none" id="nonppn">
                            <form id="invoiceForm" name="proses" onsubmit="filterData(); return false;" method="GET">
                                <div class="row mb-3 m-1">
                                    <div class="col-md-6 border p-3">
                                        <div class="row p-2">
                                            <div class="col-md-4 mb-3">
                                                <label for="start_date">Tanggal Awal:</label>
                                                <input type="text" name="start_date" id="start_date" class="form-control text-center" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>" placeholder="dd/mm/yyyy" required readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="end_date">Tanggal Akhir:</label>
                                                <input type="text" name="end_date" id="end_date" class="form-control text-center" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>" placeholder="dd/mm/yyyy" required readonly>
                                            </div>
                                            <div class="col-md-4 mb-1 text-center">
                                                <br>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <button type="submit" class="btn btn-primary" id="dateRange">Cari Data</button>
                                                    &nbsp;
                                                    <a href="invoice-reguler-selesai.php" class="btn btn-danger">Reset</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive" id="filteredData">
                                <table class="table table-bordered table-striped" id="tablenonppn">
                                    <thead>
                                        <tr class="text-white" style="background-color: navy;">
                                            <th class="text-center p-3 text-nowrap" style="width: 30px">No</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 150px">No. Invoice</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 150px">Tgl. Invoice</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 250px">Nama Customer</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 100px">Kat. Inv</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 100px">Total Invoice</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 100px">Status Pembayaran</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 150px">Jenis Pengiriman</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 80px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $month = date('m');
                                        $sql = "SELECT DISTINCT
                                                    nonppn.id_inv_nonppn,
                                                    nonppn.no_inv, 
                                                    STR_TO_DATE(nonppn.tgl_inv, '%d/%m/%Y') AS tgl_inv,
                                                    nonppn.cs_inv, 
                                                    nonppn.tgl_tempo, 
                                                    nonppn.sp_disc, 
                                                    nonppn.note_inv, 
                                                    nonppn.kategori_inv, 
                                                    nonppn.ongkir, 
                                                    nonppn.total_inv, 
                                                    nonppn.status_transaksi, 
                                                    sr.id_inv, 
                                                    sr.id_customer, 
                                                    sr.no_po, 
                                                    cs.nama_cs, cs.alamat, 
                                                    fn.status_pembayaran, fn.id_inv,
                                                    sk.jenis_pengiriman,
                                                    us.nama_user AS nama_driver,
                                                    ex.nama_ekspedisi,
                                                    ip.nama_penerima,
                                                    kmpl.id_komplain
                                                    FROM inv_nonppn AS nonppn
                                                    LEFT JOIN spk_reg sr ON(nonppn.id_inv_nonppn = sr.id_inv)
                                                    JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                                    JOIN finance fn ON (fn.id_inv = nonppn.id_inv_nonppn)
                                                    LEFT JOIN status_kirim sk ON (nonppn.id_inv_nonppn = sk.id_inv)
                                                    LEFT JOIN $database2.user us ON (sk.dikirim_driver = us.id_user)
                                                    LEFT JOIN ekspedisi ex ON (sk.dikirim_ekspedisi = ex.id_ekspedisi)
                                                    LEFT JOIN inv_penerima ip ON (nonppn.id_inv_nonppn = ip.id_inv)
                                                    LEFT JOIN inv_komplain kmpl ON (nonppn.id_inv_nonppn = kmpl.id_inv)
                                                    WHERE (nonppn.status_transaksi = 'Transaksi Selesai' OR nonppn.status_transaksi = 'Komplain Selesai') 
                                                        AND MONTH(STR_TO_DATE(nonppn.tgl_inv, '%d/%m/%Y')) = MONTH(CURRENT_DATE())
                                                        AND YEAR(STR_TO_DATE(nonppn.tgl_inv, '%d/%m/%Y')) = YEAR(CURRENT_DATE())
                                                    GROUP BY no_inv
                                                    ORDER BY no_inv DESC";
                                        $query = mysqli_query($connect, $sql);
                                        while ($data = mysqli_fetch_array($query)) {
                                            $status_trx = $data['status_transaksi'];
                                            $id_komplain = $data['id_komplain'];
                                            $status_pembayaran = $data['status_pembayaran'] == 0 ? 'Belum Bayar' : 'Sudah Bayar';
                                            $jenis_pengiriman =  $data['jenis_pengiriman'];
                                            $nama_pengiriman = [
                                                'Driver' => $data['nama_driver'],
                                                'Ekspedisi' => $data['nama_ekspedisi'],
                                                'Diambil Langsung' => $data['nama_penerima']
                                            ][$jenis_pengiriman];
                                        ?>
                                            <tr>
                                                <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                                <td class="text-nowrap text-center">
                                                    <?php echo $data['no_inv'] ?><br>
                                                    <?php
                                                    if (!empty($data['no_po'])) {
                                                        echo "(<b>" . $data['no_po'] . "</b>)";
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-nowrap text-center"><?php echo date('d/m/Y', strtotime($data['tgl_inv'])) ?></td>
                                                <td class="text-nowrap"><?php echo $data['nama_cs'] ?></td>
                                                <td class="text-nowrap text-center"><?php echo $data['kategori_inv'] ?></td>
                                                <td class="text-nowrap text-end"><?php echo number_format($data['total_inv']) ?></td>
                                                <td class="text-nowrap text-center"><?php echo $status_pembayaran; ?></td>
                                                <td class="text-nowrap text-center">
                                                    <?php
                                                    echo $jenis_pengiriman . '<br>';
                                                    echo "(" . $nama_pengiriman . ")";
                                                    ?>
                                                </td>
                                                <td class="text-center text-nowrap">
                                                    <?php  
                                                        if($status_trx == 'Komplain Selesai'){
                                                            ?>
                                                                <a href="detail-produk-selesai-revisi-nonppn.php?id=<?php echo base64_encode($data['id_komplain']) ?>" class="btn btn-primary btn-sm mb-2"><i class="bi bi-eye-fill"></i></a>
                                                            <?php
                                                        } else {
                                                            ?>
                                                                <a href="detail-produk-selesai.php?jenis=nonppn&&id=<?php echo encrypt($data['id_inv_nonppn'], $key_global) ?>" class="btn btn-primary btn-sm mb-2" title="Lihat Produk"><i class="bi bi-eye-fill"></i></a>
                                                            <?php
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php $no++; ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="d-none" id="ppn">
                            <form id="invoiceForm" name="proses_ppn" onsubmit="filterDataPPN(); return false;" method="GET">
                                <div class="row mb-3 m-1">
                                    <div class="col-md-6 border p-3">
                                        <div class="row p-2">
                                            <div class="col-md-4 mb-3">
                                                <label for="start_date">Tanggal Awal:</label>
                                                <input type="text" name="start_date_ppn" id="start_date_ppn" class="form-control text-center" value="<?php echo isset($_GET['start_date_ppn']) ? $_GET['start_date_ppn'] : ''; ?>" placeholder="dd/mm/yyyy" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="end_date">Tanggal Akhir:</label>
                                                <input type="text" name="end_date_ppn" id="end_date_ppn" class="form-control text-center" value="<?php echo isset($_GET['end_date_ppn']) ? $_GET['end_date_ppn'] : ''; ?>" placeholder="dd/mm/yyyy" readonly>
                                            </div>
                                            <div class="col-md-4 mb-1 text-center">
                                                <br>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <button type="submit" class="btn btn-primary" id="dateRangePPN">Cari Data</button>
                                                    &nbsp;
                                                    <a href="invoice-reguler-selesai.php" class="btn btn-danger">Reset</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive" id="filteredDataPPN">
                                <table class="table table-bordered table-striped" id="tableppn">
                                    <thead>
                                        <tr class="text-white" style="background-color: navy;">
                                            <th class="text-center p-3 text-nowrap" style="width: 30px">No</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 150px">No. Invoice</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 150px">Tgl. Invoice</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 250px">Nama Customer</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 100px">Kat. Inv</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 100px">Total Invoice</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 100px">Status Pembayaran</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 150px">Jenis Pengiriman</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 80px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $month = date('m');
                                        $sql = "SELECT 
                                                        ppn.id_inv_ppn,
                                                        ppn.no_inv, 
                                                        STR_TO_DATE(ppn.tgl_inv, '%d/%m/%Y') AS tgl_inv,
                                                        ppn.cs_inv, 
                                                        ppn.tgl_tempo, 
                                                        ppn.sp_disc, 
                                                        ppn.note_inv, 
                                                        ppn.kategori_inv, 
                                                        ppn.ongkir, 
                                                        ppn.total_inv, 
                                                        ppn.status_transaksi, 
                                                        sr.id_inv, 
                                                        sr.id_customer, 
                                                        sr.no_po, 
                                                        cs.nama_cs, cs.alamat, 
                                                        fn.status_pembayaran, fn.id_inv,
                                                        sk.jenis_pengiriman,
                                                        us.nama_user AS nama_driver,
                                                        ex.nama_ekspedisi,
                                                        ip.nama_penerima
                                                    FROM inv_ppn AS ppn
                                                    LEFT JOIN spk_reg sr ON(ppn.id_inv_ppn = sr.id_inv)
                                                    JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                                    JOIN finance fn ON (fn.id_inv = ppn.id_inv_ppn)
                                                    LEFT JOIN status_kirim sk ON (ppn.id_inv_ppn = sk.id_inv)
                                                    LEFT JOIN $database2.user us ON (sk.dikirim_driver = us.id_user)
                                                    LEFT JOIN ekspedisi ex ON (sk.dikirim_ekspedisi = ex.id_ekspedisi)
                                                    LEFT JOIN inv_penerima ip ON (ppn.id_inv_ppn = ip.id_inv)
                                                    WHERE (ppn.status_transaksi = 'Transaksi Selesai' OR ppn.status_transaksi = 'Komplain Selesai') 
                                                        AND MONTH(STR_TO_DATE(ppn.tgl_inv, '%d/%m/%Y')) = MONTH(CURRENT_DATE())
                                                        AND YEAR(STR_TO_DATE(ppn.tgl_inv, '%d/%m/%Y')) = YEAR(CURRENT_DATE())
                                                    GROUP BY no_inv
                                                    ORDER BY no_inv DESC";
                                        $query = mysqli_query($connect, $sql);
                                        while ($data = mysqli_fetch_array($query)) {
                                            $status_pembayaran = $data['status_pembayaran'] == 0 ? 'Belum Bayar' : 'Sudah Bayar';
                                            $jenis_pengiriman =  $data['jenis_pengiriman'];
                                            $nama_pengiriman = [
                                                'Driver' => $data['nama_driver'],
                                                'Ekspedisi' => $data['nama_ekspedisi'],
                                                'Diambil Langsung' => $data['nama_penerima']
                                            ][$jenis_pengiriman];
                                        ?>
                                            <tr>
                                                <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                                <td class="text-nowrap text-center">
                                                    <?php echo $data['no_inv'] ?><br>
                                                    <?php
                                                    if (!empty($data['no_po'])) {
                                                        echo "(<b>" . $data['no_po'] . "</b>)";
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-nowrap text-center"><?php echo date('d/m/Y', strtotime($data['tgl_inv'])) ?></td>
                                                <td class="text-nowrap"><?php echo $data['nama_cs'] ?></td>
                                                <td class="text-nowrap text-center"><?php echo $data['kategori_inv'] ?></td>
                                                <td class="text-nowrap text-end"><?php echo number_format($data['total_inv']) ?></td>
                                                <td class="text-nowrap text-center"><?php echo $status_pembayaran; ?></td>
                                                <td class="text-nowrap text-center">
                                                    <?php
                                                    echo $jenis_pengiriman . '<br>';
                                                    echo "(" . $nama_pengiriman . ")";
                                                    ?>
                                                </td>
                                                <td class="text-center text-nowrap">
                                                    <a href="detail-produk-selesai.php?jenis=ppn&&id=<?php echo encrypt($data['id_inv_ppn'], $key_global) ?>" class="btn btn-primary btn-sm mb-2" title="Lihat Produk"><i class="bi bi-eye-fill"></i></a>
                                                </td> 
                                            </tr>
                                            <?php $no++; ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="d-none" id="bum">
                            <form id="invoiceForm" name="proses" onsubmit="filterDataBUM(); return false;" method="GET">
                                <div class="row mb-3 m-1">
                                    <div class="col-md-6 border p-3">
                                        <div class="row p-2">
                                            <div class="col-md-4 mb-3">
                                                <label for="start_date">Tanggal Awal:</label>
                                                <input type="text" name="start_date_bum" id="start_date_bum" class="form-control text-center" value="<?php echo isset($_GET['start_date_bum']) ? $_GET['start_date_bum'] : ''; ?>" placeholder="dd/mm/yyyy" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="end_date">Tanggal Akhir:</label>
                                                <input type="text" name="end_date_bum" id="end_date_bum" class="form-control text-center" value="<?php echo isset($_GET['end_date_bum']) ? $_GET['end_date_bum'] : ''; ?>" placeholder="dd/mm/yyyy" readonly>
                                            </div>
                                            <div class="col-md-4 mb-1 text-center">
                                                <br>
                                                <div class="btn-group" role="group" aria-label="Basic example">
                                                    <button type="submit" class="btn btn-primary" id="dateRangeBUM">Cari Data</button>
                                                    &nbsp;
                                                    <a href="invoice-reguler-selesai.php" class="btn btn-danger">Reset</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive" id="filteredDataBUM">
                                <table class="table table-bordered table-striped" id="tablebum">
                                    <thead>
                                        <tr class="text-white" style="background-color: navy;">
                                            <th class="text-center p-3 text-nowrap" style="width: 30px">No</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 150px">No. Invoice</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 150px">Tgl. Invoice</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 250px">Nama Customer</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 100px">Kat. Inv</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 100px">Total Invoice</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 100px">Status Pembayaran</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 150px">Jenis Pengiriman</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 80px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $month = date('m');
                                        $sql = "SELECT 
                                                        bum.id_inv_bum,
                                                        bum.no_inv, 
                                                        STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y') AS tgl_inv,
                                                        bum.cs_inv, 
                                                        bum.tgl_tempo, 
                                                        bum.sp_disc, 
                                                        bum.note_inv, 
                                                        bum.kategori_inv, 
                                                        bum.ongkir, 
                                                        bum.total_inv, 
                                                        bum.status_transaksi, 
                                                        sr.id_inv, 
                                                        sr.id_customer, 
                                                        sr.no_po, 
                                                        cs.nama_cs, cs.alamat, 
                                                        fn.status_pembayaran, fn.id_inv,
                                                        sk.jenis_pengiriman,
                                                        us.nama_user AS nama_driver,
                                                        ex.nama_ekspedisi,
                                                        ip.nama_penerima
                                                    FROM inv_bum AS bum
                                                    LEFT JOIN spk_reg sr ON(bum.id_inv_bum = sr.id_inv)
                                                    JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                                    JOIN finance fn ON (fn.id_inv = bum.id_inv_bum)
                                                    LEFT JOIN status_kirim sk ON (bum.id_inv_bum = sk.id_inv)
                                                    LEFT JOIN $database2.user us ON (sk.dikirim_driver = us.id_user)
                                                    LEFT JOIN ekspedisi ex ON (sk.dikirim_ekspedisi = ex.id_ekspedisi)
                                                    LEFT JOIN inv_penerima ip ON (bum.id_inv_bum = ip.id_inv)
                                                    WHERE (bum.status_transaksi = 'Transaksi Selesai' OR bum.status_transaksi = 'Komplain Selesai') 
                                                        AND MONTH(STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y')) = MONTH(CURRENT_DATE())
                                                        AND YEAR(STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y')) = YEAR(CURRENT_DATE())
                                                    GROUP BY no_inv
                                                    ORDER BY no_inv DESC";
                                        $query = mysqli_query($connect, $sql);
                                        while ($data = mysqli_fetch_array($query)) {
                                            $status_pembayaran = $data['status_pembayaran'] == 0 ? 'Belum Bayar' : 'Sudah Bayar';
                                            $jenis_pengiriman =  $data['jenis_pengiriman'];
                                            $nama_pengiriman = [
                                                'Driver' => $data['nama_driver'],
                                                'Ekspedisi' => $data['nama_ekspedisi'],
                                                'Diambil Langsung' => $data['nama_penerima']
                                            ][$jenis_pengiriman];
                                        ?>
                                            <tr>
                                                <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                                <td class="text-nowrap text-center">
                                                    <?php echo $data['no_inv'] ?><br>
                                                    <?php
                                                    if (!empty($data['no_po'])) {
                                                        echo "(<b>" . $data['no_po'] . "</b>)";
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-nowrap text-center"><?php echo date('d/m/Y', strtotime($data['tgl_inv'])) ?></td>
                                                <td class="text-nowrap"><?php echo $data['nama_cs'] ?></td>
                                                <td class="text-nowrap text-center"><?php echo $data['kategori_inv'] ?></td>
                                                <td class="text-nowrap text-end"><?php echo number_format($data['total_inv']) ?></td>
                                                <td class="text-nowrap text-center"><?php echo $status_pembayaran; ?></td>
                                                <td class="text-nowrap text-center">
                                                    <?php
                                                    echo $jenis_pengiriman . '<br>';
                                                    echo "(" . $nama_pengiriman . ")";
                                                    ?>
                                                </td>
                                                <td class="text-center text-nowrap">
                                                    <a href="detail-produk-selesai.php?jenis=bum&&id=<?php echo encrypt($data['id_inv_bum'], $key_global) ?>" class="btn btn-primary btn-sm mb-2" title="Lihat Produk"><i class="bi bi-eye-fill"></i></a>
                                                </td>
                                            </tr>
                                            <?php $no++; ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- End Dalam Proses -->
                    <!-- ================================================ -->
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
<!-- Script untuk mengatur hide and show div -->
<script>
    // Ambil elemen tombol dan div
    const buttons = {
        btnNonPpn: document.getElementById('btnNonPpn'),
        btnPpn: document.getElementById('btnPpn'),
        btnBum: document.getElementById('btnBum')
    };

    const divs = {
        nonppn: document.getElementById('nonppn'),
        ppn: document.getElementById('ppn'),
        bum: document.getElementById('bum')
    };

    // Fungsi untuk menyembunyikan semua div dan menghapus kelas 'active' dari semua tombol
    function resetButtonsAndDivs() {
        Object.values(divs).forEach(div => div.classList.add('d-none'));
        Object.values(buttons).forEach(button => button.classList.remove('active'));
    }

    // Fungsi untuk menampilkan div yang sesuai dan mengaktifkan tombol yang sesuai
    function showDiv(button, div) {
        resetButtonsAndDivs(); // Reset semua div dan tombol
        div.classList.remove('d-none'); // Tampilkan div yang sesuai
        div.classList.add('d-block');
        button.classList.add('active'); // Aktifkan tombol yang diklik
    }

    // Tambahkan event listener ke setiap tombol
    buttons.btnNonPpn.addEventListener('click', function () {
        showDiv(buttons.btnNonPpn, divs.nonppn);
    });

    buttons.btnPpn.addEventListener('click', function () {
        showDiv(buttons.btnPpn, divs.ppn);
    });

    buttons.btnBum.addEventListener('click', function () {
        showDiv(buttons.btnBum, divs.bum);
    });
</script>

<!-- date picker with flatpick -->
<script type="text/javascript">
    flatpickr("#start_date", {
        dateFormat: "d/m/Y",
        onClose: function(selectedDates, dateStr, instance) {
            // Ambil tanggal awal yang dipilih
            var startDate = selectedDates[0];

            // Perbarui batas tanggal maksimal pada pemilih tanggal akhir
            var endDateInput = document.getElementById("end_date");
            var endDatePicker = flatpickr("#end_date", {
                dateFormat: "d/m/Y",
                minDate: startDate,
                maxDate: new Date(startDate.getTime() + 30 * 24 * 60 * 60 * 1000)
            });

            // Jika tanggal akhir saat ini berada di bawah tanggal awal, hapus nilainya
            var endDate = endDatePicker.selectedDates[0];
            if (endDate < startDate) {
                endDateInput.value = "";
            }
        }
    });

    flatpickr("#start_date_ppn", {
        dateFormat: "d/m/Y",
        onClose: function(selectedDates, dateStr, instance) {
            // Ambil tanggal awal yang dipilih
            var startDate = selectedDates[0];

            // Perbarui batas tanggal maksimal pada pemilih tanggal akhir
            var endDateInput = document.getElementById("end_date");
            var endDatePicker = flatpickr("#end_date_ppn", {
                dateFormat: "d/m/Y",
                minDate: startDate,
                maxDate: new Date(startDate.getTime() + 30 * 24 * 60 * 60 * 1000)
            });

            // Jika tanggal akhir saat ini berada di bawah tanggal awal, hapus nilainya
            var endDate = endDatePicker.selectedDates[0];
            if (endDate < startDate) {
                endDateInput.value = "";
            }
        }
    });

    flatpickr("#start_date_bum", {
        dateFormat: "d/m/Y",
        onClose: function(selectedDates, dateStr, instance) {
            // Ambil tanggal awal yang dipilih
            var startDate = selectedDates[0];

            // Perbarui batas tanggal maksimal pada pemilih tanggal akhir
            var endDateInput = document.getElementById("end_date");
            var endDatePicker = flatpickr("#end_date_bum", {
                dateFormat: "d/m/Y",
                minDate: startDate,
                maxDate: new Date(startDate.getTime() + 30 * 24 * 60 * 60 * 1000)
            });

            // Jika tanggal akhir saat ini berada di bawah tanggal awal, hapus nilainya
            var endDate = endDatePicker.selectedDates[0];
            if (endDate < startDate) {
                endDateInput.value = "";
            }
        }
    });

    flatpickr("#start_date_status", {
        dateFormat: "d/m/Y",
        onClose: function(selectedDates, dateStr, instance) {
            // Ambil tanggal awal yang dipilih
            var startDate = selectedDates[0];

            // Perbarui batas tanggal maksimal pada pemilih tanggal akhir
            var endDateInput = document.getElementById("end_date");
            var endDatePicker = flatpickr("#end_date_status", {
                dateFormat: "d/m/Y",
                minDate: startDate,
                maxDate: new Date(startDate.getTime() + 30 * 24 * 60 * 60 * 1000)
            });

            // Jika tanggal akhir saat ini berada di bawah tanggal awal, hapus nilainya
            var endDate = endDatePicker.selectedDates[0];
            if (endDate < startDate) {
                endDateInput.value = "";
            }
        }
    });
</script>
<!-- end date picker -->

<script>
    $(document).ready(function() {
        $("#select").change(function() {
            var open = $(this).data("isopen");
            if (open) {
                window.location.href = $(this).val();
            }
            //set isopen to opposite so next time when user clicks select box
            //it won't trigger this event
            $(this).data("isopen", !open);
        });
    });
</script>

<script>
    $(function() {
        const table = new DataTable("#tablenonppn", {
            lengthChange: false,
            autoWidth: false,
            language: {
                searchPlaceholder: "Cari data",
                search: "",
            },
        });
        table.on("draw.dt", function() {
            // Mengatur ulang nomor urut setelah menggambar ulang tabel
            table
                .column(0, {
                    search: "applied",
                    order: "applied"
                })
                .nodes()
                .each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
        });
    });


    $(function() {
        const table = new DataTable("#tableppn", {
            lengthChange: false,
            autoWidth: false,
            language: {
                searchPlaceholder: "Cari data",
                search: "",
            },
        });
        table.on("draw.dt", function() {
            // Mengatur ulang nomor urut setelah menggambar ulang tabel
            table
                .column(0, {
                    search: "applied",
                    order: "applied"
                })
                .nodes()
                .each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
        });
    });

    $(function() {
        const table = new DataTable("#tablebum", {
            lengthChange: false,
            autoWidth: false,
            language: {
                searchPlaceholder: "Cari data",
                search: "",
            },
        });
        table.on("draw.dt", function() {
            // Mengatur ulang nomor urut setelah menggambar ulang tabel
            table
                .column(0, {
                    search: "applied",
                    order: "applied"
                })
                .nodes()
                .each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
        });
    });
</script>

<script>
    function filterData() {
        var dateRangeValue = document.getElementById('dateRange').value;
        var startDate = document.getElementById('start_date').value;
        var endDate = document.getElementById('end_date').value;

        if (startDate === '' || endDate === '') {
            return; // Jika salah satu dari startDate atau endDate kosong, hentikan eksekusi filter data
        }

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('filteredData').innerHTML = this.responseText;
                $(function() {
                    const table = new DataTable("#tablenonppnfilter", {
                        lengthChange: false,
                        autoWidth: false,
                        language: {
                            searchPlaceholder: "Cari data",
                            search: "",
                        },
                    });
                    table.on("draw.dt", function() {
                        // Mengatur ulang nomor urut setelah menggambar ulang tabel
                        table
                            .column(0, {
                                search: "applied",
                                order: "applied"
                            })
                            .nodes()
                            .each(function(cell, i) {
                                cell.innerHTML = i + 1;
                            });
                    });
                });

                flatpickr("#start_date", {
                    dateFormat: "d/m/Y",
                    onClose: function(selectedDates, dateStr, instance) {
                        var startDate = selectedDates[0];
                        var endDateInput = document.getElementById("end_date");
                        var endDatePicker = flatpickr("#end_date", {
                            dateFormat: "d/m/Y",
                            minDate: startDate,
                            maxDate: new Date(startDate.getTime() + 30 * 24 * 60 * 60 * 1000)
                        });

                        var endDate = endDatePicker.selectedDates[0];
                        if (endDate < startDate) {
                            endDateInput.value = "";
                        }
                    }
                });

                document.getElementById('start_date').value = startDate;
                document.getElementById('end_date').value = endDate;
            }
        };

        var url = 'filter-date-trx-nonppn-selesai.php?start_date=' + startDate + '&end_date=' + endDate;
        xhttp.open('GET', url, true);
        xhttp.send();

        $('#nonppn').collapse('show');
    }

    // ================================================================================

    function filterDataPPN() {
        var dateRangePpnValue = document.getElementById('dateRangePPN').value;
        var startDate = document.getElementById('start_date_ppn').value;
        var endDate = document.getElementById('end_date_ppn').value;

        if (startDate === '' || endDate === '') {
            return; // Jika salah satu dari startDate atau endDate kosong, hentikan eksekusi filter data
        }

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('filteredDataPPN').innerHTML = this.responseText;
                $(function() {
                    const table = new DataTable("#tableppnfilter", {
                        lengthChange: false,
                        autoWidth: false,
                        language: {
                            searchPlaceholder: "Cari data",
                            search: "",
                        },
                    });
                    table.on("draw.dt", function() {
                        // Mengatur ulang nomor urut setelah menggambar ulang tabel
                        table
                            .column(0, {
                                search: "applied",
                                order: "applied"
                            })
                            .nodes()
                            .each(function(cell, i) {
                                cell.innerHTML = i + 1;
                            });
                    });
                });

                flatpickr("#start_date_ppn", {
                    dateFormat: "d/m/Y",
                    onClose: function(selectedDates, dateStr, instance) {
                        var startDate = selectedDates[0];
                        var endDateInput = document.getElementById("end_date");
                        var endDatePicker = flatpickr("#end_date_ppn", {
                            dateFormat: "d/m/Y",
                            minDate: startDate,
                            maxDate: new Date(startDate.getTime() + 30 * 24 * 60 * 60 * 1000)
                        });

                        var endDate = endDatePicker.selectedDates[0];
                        if (endDate < startDate) {
                            endDateInput.value = "";
                        }
                    }
                });

                document.getElementById('start_date_ppn').value = startDate;
                document.getElementById('end_date_ppn').value = endDate;
            }
        };

        var url = 'filter-date-trx-ppn-selesai.php?start_date_ppn=' + startDate + '&end_date_ppn=' + endDate;
        xhttp.open('GET', url, true);
        xhttp.send();

        $('#ppn').collapse('show');
    }

    // ================================================================================

    function filterDataBUM() {
        var dateRangeBumValue = document.getElementById('dateRangeBUM').value;
        var startDate = document.getElementById('start_date_bum').value;
        var endDate = document.getElementById('end_date_bum').value;

        if (startDate === '' || endDate === '') {
            return; // Jika salah satu dari startDate atau endDate kosong, hentikan eksekusi filter data
        }

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('filteredDataBUM').innerHTML = this.responseText;
                $(function() {
                    const table = new DataTable("#tablebumfilter", {
                        lengthChange: false,
                        autoWidth: false,
                        language: {
                            searchPlaceholder: "Cari data",
                            search: "",
                        },
                    });
                    table.on("draw.dt", function() {
                        // Mengatur ulang nomor urut setelah menggambar ulang tabel
                        table
                            .column(0, {
                                search: "applied",
                                order: "applied"
                            })
                            .nodes()
                            .each(function(cell, i) {
                                cell.innerHTML = i + 1;
                            });
                    });
                });

                flatpickr("#start_date_bum", {
                    dateFormat: "d/m/Y",
                    onClose: function(selectedDates, dateStr, instance) {
                        var startDate = selectedDates[0];
                        var endDateInput = document.getElementById("end_date");
                        var endDatePicker = flatpickr("#end_date_bum", {
                            dateFormat: "d/m/Y",
                            minDate: startDate,
                            maxDate: new Date(startDate.getTime() + 30 * 24 * 60 * 60 * 1000)
                        });

                        var endDate = endDatePicker.selectedDates[0];
                        if (endDate < startDate) {
                            endDateInput.value = "";
                        }
                    }
                });

                document.getElementById('start_date_bum').value = startDate;
                document.getElementById('end_date_bum').value = endDate;
            }
        };

        var url = 'filter-date-trx-bum-selesai.php?start_date_bum=' + startDate + '&end_date_bum=' + endDate;
        xhttp.open('GET', url, true);
        xhttp.send();

        $('#bum').collapse('show');
    }
</script>

<script>
    var collToggle = document.querySelectorAll('[data-bs-toggle="collapse"]');
    var collTargets = document.querySelectorAll('.collapse');

    collToggle.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            var target = toggle.getAttribute('href');
            var targetCollapse = document.querySelector(target);
            var isExpanded = targetCollapse.classList.contains('show');

            collTargets.forEach(function(collapse) {
                if (collapse !== targetCollapse) {
                    collapse.classList.remove('show');
                }
            });

            collToggle.forEach(function(toggle) {
                if (toggle !== this) {
                    toggle.classList.remove('active');
                }
            });

            if (!isExpanded) {
                targetCollapse.classList.add('show');
                toggle.classList.add('active');
            } else {
                targetCollapse.classList.remove('show');
                toggle.classList.remove('active');
            }
        });
    });
</script>