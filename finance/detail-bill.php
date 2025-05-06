<?php
$page = 'list-tagihan';
$page2 = 'tagihan-penjualan';
require_once "../akses.php";
require_once 'function/class-finance.php';
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

    <style type="text/css">
        /* Gaya untuk indikator pemuatan (spinner) */
        #loadingSpinner {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }
        /* Gaya untuk efek blur */
        .blur {
            filter: blur(2px);
        }
        @media only screen and (max-width: 500px) {
            body {
                font-size: 14px;
            }
        }
        .btn-sm{
            padding: 2px 5px !important;
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
        <?php
            if (isset($_SESSION['info'])) {
                echo '<div class="info-data" data-infodata="' . $_SESSION['info'] . '"></div>';
                unset($_SESSION['info']);
            }
        ?>
        <!-- END SWEET ALERT -->
        <section>
            <div class="card shadow p-2">
                <?php  
                     // Generate a secure random token
                    $nonce = bin2hex(random_bytes(16));
                    $_SESSION['nonce_token'] = $nonce; 
                    $id_bill = decrypt($_GET['id'], $key_finance);
                    $sql_bill_cs = "SELECT 
                                        bill.id_tagihan,
                                        bill.total_tagihan,
                                        bill.tgl_tagihan,
                                        bill.no_tagihan,
                                        bill.status_cetak,
                                        bill.id_driver,
                                        bill.cs_tagihan,
                                        bill.jenis_faktur,
                                        fnc.id_finance,
                                        fnc.id_tagihan AS id_tagihan_finance,
                                        fnc.jenis_inv,
                                        spk.id_inv,
                                        spk.id_customer,
                                        cs.nama_cs
                                    FROM finance_tagihan AS bill
                                    JOIN finance fnc ON (fnc.id_tagihan = bill.id_tagihan)
                                    LEFT JOIN finance_bayar byr ON (bill.id_tagihan = byr.id_tagihan)
                                    LEFT JOIN spk_reg spk ON (spk.id_inv = fnc.id_inv)
                                    LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                    WHERE bill.id_tagihan = '$id_bill'";
                    $query_bill = mysqli_query($connect, $sql_bill_cs);
                    $data_bill_cs = mysqli_fetch_array($query_bill);
                    $nama_cs = $data_bill_cs['nama_cs'];
                    $tgl_tagihan = $data_bill_cs['tgl_tagihan'];
                    $no_tagihan = $data_bill_cs['no_tagihan'];
                    $status_cetak = $data_bill_cs['status_cetak'];
                    $id_driver = $data_bill_cs['id_driver'];
                    $id_customer = $data_bill_cs['id_customer'];
                    $jenis_faktur = $data_bill_cs['jenis_faktur'];
                    $cs_tagihan = $data_bill_cs['cs_tagihan'];
                    $id_tagihan = $data_bill_cs['id_tagihan'];
                    $jenis_inv = $data_bill_cs['jenis_inv'];
                    $id_finance = $data_bill_cs['id_finance'];
                    $inv_jenis = "";
                    if ($jenis_inv == "ppn"){
                        $inv_jenis_string = ['ppn'];
                        $inv_jenis = "'" . implode("','", $inv_jenis_string) . "'";
                    } else {
                        $inv_jenis_string = ['bum', 'nonppn'];
                        $inv_jenis = "'" . implode("','", $inv_jenis_string) . "'";
                    }
                ?>
                <div class="card-header text-center">
                    <h5><strong>DETAIL TAGIHAN</strong></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>No. Tagihan</label>
                            <input type="text" class="form-control" value="<?php echo $no_tagihan ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tgl. Tagihan</label>
                            <input type="text" class="form-control" value="<?php echo $tgl_tagihan ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Nama Customer</label>
                            <input type="text" class="form-control" value="<?php echo $nama_cs ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="card-body mt-2">
                    <?php  
                        $sql_bill_total = " SELECT  
                                                bill.id_tagihan, 
                                                bill.total_tagihan,
                                                fnc.id_finance, 
                                                fnc.total_cb AS total_cb,
                                                SUM(byr_cb.total_bayar) AS total_pembayaran_cb,
                                                SUM(byr.total_bayar) AS total_pembayaran,
                                                SUM(byr.total_potongan) AS total_potongan       
                                            FROM finance_tagihan AS bill
                                            LEFT JOIN finance_bayar byr ON (bill.id_tagihan = byr.id_tagihan)
                                            LEFT JOIN finance fnc ON (byr.id_finance = fnc.id_finance)
                                            LEFT JOIN finance_bayar_cb byr_cb ON (fnc.id_finance = byr_cb.id_finance)
                                            WHERE bill.id_tagihan = '$id_bill'";
                        $query_bill = mysqli_query($connect, $sql_bill_total);
                        while($data_bill_total = mysqli_fetch_array($query_bill)){
                            $total_tagihan = $data_bill_total['total_tagihan'];
                            $total_bayar = $data_bill_total['total_pembayaran'];
                            $total_potongan = $data_bill_total['total_potongan'];
                            $total_sisa_tagihan = $total_tagihan - $total_bayar - $total_potongan;
                            $total_cb = $data_bill_total['total_cb'];
                            $total_bayar_cb = $data_bill_total['total_pembayaran_cb'];
                            $total_sisa_cb = $total_cb - $total_bayar_cb;
                    ?>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label>Total Tagihan</label>
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping">Rp</span>
                                    <input type="text" class="form-control text-end" value="<?php echo number_format($total_tagihan,0,'.','.')?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label>Total Bayar</label>
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping">Rp</span>
                                    <input type="text" class="form-control text-end" value="<?php echo number_format($total_bayar,0,'.','.')?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label>Total Potongan</label>
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping">Rp</span>
                                    <input type="text" class="form-control text-end" value="<?php echo number_format($total_potongan,0,'.','.')?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label>Total Sisa Tagihan</label>
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping">Rp</span>
                                    <input type="text" class="form-control text-end" value="<?php echo number_format($total_sisa_tagihan,0,'.','.')?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tampil data -->
            <div class="card shadow">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <div class="text-start mb-3">
                            <?php  
                                if($total_sisa_tagihan == 0 && $total_sisa_cb == 0){
                                    ?>
                                        <a href="list-tagihan-penjualan-lunas.php?date_range=year" class="btn btn-warning btn-detail mb-2">
                                            <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                                        </a>

                                        <a href="cetak-tagihan.php?id=<?php echo encrypt($id_bill, $key_finance)?>" class="btn btn-secondary btn-detail mb-2">
                                            <i class="bi bi-printer"></i> Cetak Tagihan
                                        </a>
                                    <?php
                                } else {
                                    ?>
                                        <a href="list-tagihan-penjualan.php?date_range=year" class="btn btn-warning btn-detail mb-2">
                                            <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                                        </a>

                                        <!-- Button Edit Jenis Faktur -->
                                        <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#ubahJenisFaktur">
                                            <i class="bi bi-pencil"></i> Ubah Jenis Faktur Tagihan
                                        </button>

                                        <a href="cetak-tagihan.php?id=<?php echo encrypt($id_bill, $key_finance)?>" class="btn btn-secondary btn-detail mb-2">
                                            <i class="bi bi-printer"></i> Cetak Tagihan
                                        </a>
                                    <?php
                                    if($status_cetak == 1 && $id_driver == ''){
                                        ?>
                                            <button class="btn btn-primary btn-md mb-2" data-bs-toggle="modal" data-bs-target="#pilihDriver"><i class="bi bi-truck"></i> Pilih Driver</button>
                                        <?php
                                    }

                                    ?>
                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-secondary mb-2" data-bs-toggle="modal" data-bs-target="#addInv">
                                            <i class="bi bi-plus-circle"></i> Tambah Invoice
                                        </button>
                                    <?php
                                }
                            ?>
                            <?php  
                                
                            ?>
                            <?php 
                                if($total_sisa_tagihan == 0){
                                    
                                } else {
                                    
                                }
                            ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="text-white" style="background-color: navy;">
                                    <td class="text-center text-nowrap p-3">No</td>
                                    <td class="text-center text-nowrap p-3">No. Invoice</td>
                                    <td class="text-center text-nowrap p-3">Jenis Invoice</td>
                                    <td class="text-center text-nowrap p-3" style="width: 350px;">Cs. Invoice</td>
                                    <td class="text-center text-nowrap p-3">Status Pembayaran</td>
                                    <td class="text-center text-nowrap p-3">Total Invoice</td>
                                    <td class="text-center text-nowrap p-3">Total Cashback</td>
                                    <td class="text-center text-nowrap p-3">Status Lunas</td>
                                    <td class="text-center text-nowrap p-3">Aksi</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $no = 1;
                                    $grand_total_inv = 0;
                                    $sql = "SELECT  
                                                COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo) AS tgl_tempo,
                                                STR_TO_DATE(COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo), '%d/%m/%Y') AS tgl_tempo_convert,
                                                COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                                COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                                                COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                                                COALESCE(nonppn.total_inv, ppn.total_inv, bum.total_inv) AS total_inv,
                                                fnc.id_finance AS finance_id,
                                                fnc.id_inv AS inv_id,
                                                fnc.jenis_inv,
                                                fnc.status_pembayaran,
                                                fnc.status_lunas,
                                                byr.id_finance, 
                                                SUM(fnc.total_cb) AS total_cb, 
                                                SUM(byr_cb.total_bayar) AS total_pembayaran_cb,    
                                                SUM(byr.total_bayar) AS total_pembayaran,
                                                SUM(byr.total_potongan) AS total_potongan    
                                            FROM finance_tagihan AS bill 
                                            JOIN finance fnc ON (bill.id_tagihan = fnc.id_tagihan)
                                            LEFT JOIN finance_bayar byr ON (fnc.id_finance = byr.id_finance)
                                            LEFT JOIN finance_bayar_cb byr_cb ON (fnc.id_finance = byr_cb.id_finance)
                                            LEFT JOIN inv_nonppn nonppn ON (fnc.id_inv = nonppn.id_inv_nonppn)
                                            LEFT JOIN inv_ppn ppn ON (fnc.id_inv = ppn.id_inv_ppn)
                                            LEFT JOIN inv_bum bum ON (fnc.id_inv = bum.id_inv_bum)
                                            WHERE bill.id_tagihan = '$id_bill'
                                            GROUP BY fnc.id_finance
                                            ORDER BY no_inv ASC";
                                
                                    $query = mysqli_query($connect, $sql);
                                    while ($data = mysqli_fetch_array($query)){
                                        $id_finance = $data['finance_id'];
                                        $id_inv = $data['inv_id'];
                                        $no_inv = $data['no_inv'];
                                        $jenis_inv = $data['jenis_inv'];
                                        $tgl_tempo_cek = $data['tgl_tempo'];
                                        $tgl_tempo = $data['tgl_tempo_convert'];
                                        $date_now = date('Y-m-d');
                                        $total_inv = $data['total_inv'];
                                        $grand_total_inv += $total_inv;
                                        $total_bayar = $data['total_pembayaran'] + $data['total_potongan'];
                                        $sisa_tagihan = $total_inv - $total_bayar;
                                        $sisa_tagihan_cb = $data['total_cb'] - $data['total_pembayaran_cb'];
                                        $status_lunas = "";
                                        $history_payment = "";
                                        if($sisa_tagihan == '0' && $sisa_tagihan_cb == '0'){
                                            $status_lunas = '<button class="btn btn-success btn-sm mb-2"><i class="bi bi-check-circle"></i> Lunas</button>';
                                            $history_payment = '<button class="btn btn-info btn-sm view_data mb-2" data-bs-toggle="modal" data-bs-target="#history" data-id="' . encrypt($id_finance, $key_finance) . '"><i class="bi bi-card-checklist"></i> History Payment</button>';

                                        } else if ($sisa_tagihan != '0' && $sisa_tagihan_cb == '0'){
                                            $status_lunas = '<button class="btn btn-warning btn-sm mb-2"><i class="bi bi-info-circle"></i> Tagihan Belum Lunas</button>';
                                            $history_payment = '<button class="btn btn-info btn-sm view_data mb-2" data-bs-toggle="modal" data-bs-target="#history" data-id="' . encrypt($id_finance, $key_finance) . '"><i class="bi bi-card-checklist"></i> History Payment</button>';

                                        } else if ($sisa_tagihan == '0' && $sisa_tagihan_cb != '0') {
                                            $status_lunas = '<button class="btn btn-warning btn-sm mb-2"><i class="bi bi-info-circle"></i> Cashback Belum Lunas</button>';
                                            $history_payment = '<button class="btn btn-info btn-sm view_data mb-2" data-bs-toggle="modal" data-bs-target="#history" data-id="' . encrypt($id_finance, $key_finance) . '"><i class="bi bi-card-checklist"></i> History Payment</button>';
                                        } else {
                                            $status_lunas = '<button class="btn btn-warning btn-sm mb-2"><i class="bi bi-info-circle"></i> Cashback & Invoice Belum Lunas</button>';
                                        }
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $no; ?></td>
                                    <td class="text-center text-nowrap">
                                        <?php echo $no_inv; ?><br>
                                        (<?php echo $data['tgl_inv']; ?>)
                                    </td>
                                    <td class="text-center text-nowrap"><?php echo strtoupper($data['jenis_inv'])?></td>
                                    <td class="text-nowrap"><?php echo $data['cs_inv'] ?></td>
                                    <td class="text-center text-nowrap">
                                        <?php 
                                            if($data['status_pembayaran'] == 0) {
                                                echo "Belum Bayar";
                                            } else {
                                                echo "Sudah Bayar";
                                            }
                                           
                                        ?>
                                    </td>
                                    <td class="text-end text-nowrap"><?php echo number_format($total_inv,0,'.','.') ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data['total_cb'],0,'.','.') ?></td>
                                    <td class="text-center text-nowrap"><?php echo $status_lunas; ?></td>
                                    <td class="text-center text-nowrap">
                                        <?php
                                            $cek_komplain = $connect->query("SELECT id_komplain FROM inv_komplain WHERE id_inv = '$id_inv'");
                                            $total_data_komplain = mysqli_num_rows($cek_komplain);
                                            while($data_komplain = mysqli_fetch_array($cek_komplain)){
                                                $id_komplain = $data_komplain['id_komplain'];
                                            ?>
                                            <?php } ?>
                                            <?php
                                            if ($jenis_inv == 'nonppn') {
                                                if ($total_data_komplain != 0) {
                                                    echo '<a href="detail-fnc-nonppn-revisi.php?id=' . base64_encode($id_komplain) . '&bill=' . $_GET['id'] . '" class="btn btn-primary btn-sm mb-2" title="Lihat Data"><i class="bi bi-eye"></i> Detail Produk</a>';
                                                } else {
                                                    echo '<a href="detail-fnc-nonppn.php?id=' . base64_encode($id_inv) . '&bill=' . $_GET['id'] . '" class="btn btn-primary btn-sm mb-2" title="Lihat Data"><i class="bi bi-eye"></i> Detail Produk</a>';
                                                }
                                            } elseif ($jenis_inv == 'ppn') {
                                                if ($total_data_komplain != 0) {
                                                    echo '<a href="detail-fnc-ppn-revisi.php?id=' . base64_encode($id_komplain) . '&bill=' . $_GET['id'] . '" class="btn btn-primary btn-sm mb-2" title="Lihat Data"><i class="bi bi-eye"></i> Detail Produk</a>';
                                                } else {
                                                    echo '<a href="detail-fnc-ppn.php?id=' . base64_encode($id_inv) . '&bill=' . $_GET['id'] . '" class="btn btn-primary btn-sm mb-2" title="Lihat Data"><i class="bi bi-eye"></i> Detail Produk</a>';
                                                }
                                            } elseif ($jenis_inv == 'bum') {
                                                if ($total_data_komplain != 0) {
                                                    echo '<a href="detail-fnc-bum-revisi.php?id=' . base64_encode($id_komplain) . '&bill=' . $_GET['id'] . '" class="btn btn-primary btn-sm mb-2" title="Lihat Data"><i class="bi bi-eye"></i> Detail Produk</a>';
                                                } else {
                                                    echo '<a href="detail-fnc-bum.php?id=' . base64_encode($id_inv) . '&bill=' . $_GET['id'] . '" class="btn btn-primary btn-sm mb-2" title="Lihat Data"><i class="bi bi-eye"></i> Detail Produk</a>';
                                                }
                                            }
                                        ?>
                                        <?php  
                                            if ($sisa_tagihan == 0  && $sisa_tagihan_cb == '0') {
                                                echo $history_payment;
                                            } else {
                                                ?>
                                                    <a href="form-pembayaran-inv.php?id=<?php echo encrypt($id_bill, $key_finance) ?>&&jenis=<?php echo $jenis_inv ?>&&id_cs=<?php echo encrypt($id_customer, $key_finance) ?>&&id_inv=<?php echo encrypt($id_inv, $key_finance) ?>" class="btn btn-secondary btn-sm mb-2">
                                                        <i class="bi bi-cash-coin"> Bayar</i>
                                                    </a>
                                                    <br>
                                                <?php
                                                    echo $history_payment;
                                            }
                                        ?>
                                        <?php  
                                            if ($total_bayar == 0 && $data['total_pembayaran_cb'] == 0) {
                                                ?>  
                                                    <br>
                                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapus" data-id="<?php echo encrypt($id_finance, $key_finance) ?>" data-bill="<?php echo encrypt($id_bill, $key_finance) ?>" data-noinv="<?php echo $data['no_inv'] ?>" data-totaltagihan="<?php echo $total_tagihan ?>" data-totalinv="<?php echo $total_inv ?>">
                                                        <i class="bi bi-x-circle"></i> Hapus Invoice
                                                    </button>
                                                <?php
                                            } else if ($total_bayar > 0 ){
                                                ?>
                                                    <br>
                                                    <button class="btn btn-danger btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#hapusRefund" data-id="<?php echo encrypt($id_finance, $key_finance) ?>" data-bill="<?php echo encrypt($id_bill, $key_finance) ?>" data-idinv="<?php echo encrypt($id_inv, $key_finance) ?>" data-noinv="<?php echo $data['no_inv'] ?>" data-totaltagihan="<?php echo $total_tagihan ?>" data-totalinv="<?php echo $total_inv ?>">
                                                        <i class="bi bi-x-circle"></i> Hapus Refund
                                                    </button>
                                                <?php
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <?php $no++ ?>
                              <?php } ?> 
                            </tbody>
                        </table>
                        <!-- Update total tagihan jika tidak sesuai dengan data -->
                        <?php  
                            if ($total_tagihan != $grand_total_inv) {
                                $update_tagihan = $connect->query("UPDATE finance_tagihan SET total_tagihan = '$grand_total_inv' WHERE id_tagihan = '$id_bill'");
                                if ($update_tagihan) {
                                    echo '<script>
                                            setTimeout(function(){
                                                window.location.reload(true);
                                            }, 1000);
                                          </script>';
                                } else {
                                    echo '<script>
                                            setTimeout(function(){
                                                window.location.reload(true);
                                            }, 1000);
                                          </script>';
                                }
                            }
                            // Menutup koneksi
                            $connect->close();
                        ?>
                    </div>
                </div>
        </section>
    </main><!-- End #main -->
     <!-- Modal Ubah Jenis Faktur Tagihan -->
     <div class="modal fade" id="ubahJenisFaktur" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah customer tagihan dan jenis faktur</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="proses/bill.php" method="post">
                        <div class="mb-3">
                            <label>Jenis Faktur Tagihan</label>
                            <input type="hidden" name="id_bill" value="<?php echo encrypt($id_bill, $key_finance) ?>" readonly>
                            <input type="text" class="form-control" name="jenis_faktur" value="<?php echo $jenis_faktur ?>" maxlength="25" required>
                        </div>
                        <div class="mb-3">
                            <label>Customer Tagihan</label>
                            <input type="text" class="form-control" name="cs" value="<?php echo $cs_tagihan ?>" maxlength="40">
                        </div>
                        <div class="mb-3">
                            <label>Tgl. Tagihan</label>
                            <input type="text" class="form-control" name="tgl" id="date" value="<?php echo $tgl_tagihan ?>" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" name="ubah-jenis-faktur">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Ubah Jenis Faktur Tagihan -->

    <!-- Jika kode di hapus detail history tidak tampil -->
    <?php  
        include "../koneksi.php";
    ?>
    <!-- End -->

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

    <!-- Modal hapus invoice -->
    <div class="modal fade" id="hapus" tabindex="-1" aria-labelledby="historyLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyLabel">Hapus List Tagihan</h5>
                    <button type="button" class="btn-close tutup-hapus" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin hapus list tagihan (<b id="noInv"></b>)?</p>
                    <form action="proses/hapus-list-tagihan.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_finance" id="idFinance">
                        <input type="hidden" name="id_bill" id="idBill">
                        <input type="hidden" name="total_tagihan" id="totalTagihan">
                        <input type="hidden" name="total_inv" id="totalInv">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-danger" name="hapus-list">Ya, hapus list tagihan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal hapus invoice -->

    <!-- Modal hapus Refund -->
    <div class="modal fade" id="hapusRefund" data-bs-backdrop="static" tabindex="-1" aria-labelledby="historyLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyLabel">Hapus List Tagihan</h5>
                    <button type="button" class="btn-close tutup-refund" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php  
                        // Membuat nonce unik
                        $nonce = bin2hex(random_bytes(32));
                        // Menyimpan nonce dalam sesi
                        $_SESSION['nonce'] = $nonce;
                        $uuid = uuid();
                        $day = date('d');
                        $month = date('m');
                        $year = date('y');
                        $years = date('Y');
                        $id_refund = "REFUND" . $year . "" . $month . "" . $uuid . "" . $day ; 
                        $sql  = mysqli_query($connect, "SELECT 
                                                            CAST(MAX(CAST(SUBSTRING_INDEX(no_refund, '/', 1) AS UNSIGNED)) AS CHAR) AS maxID,
                                                            created_date
                                                        FROM 
                                                            finance_refund 
                                                        WHERE 
                                                            YEAR(created_date) = '$years'
                                                        ");
                        $data = mysqli_fetch_array($sql);
                        $kode = $data['maxID'];
                        $array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
                        $ket1 = "/REFUND/";
                        $bln = $array_bln[date('n')];
                        $ket2 = "/";
                        $ket3 = date("Y");
                        $urutkan = $kode; // Mengambil nilai maksimum langsung dari hasil query
                        $urutkan++;
                        $no_refund = sprintf("%03s", $urutkan) . $ket1 . $bln . $ket2 . $ket3;
                    ?>
                    <form action="proses/hapus-list-tagihan-refund.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="nonce" value="<?php echo $nonce; ?>">
                        <input type="hidden" name="id_refund" value="<?php echo $id_refund ?>">
                        <input type="hidden" name="no_refund" value="<?php echo $no_refund ?>">
                        <input type="hidden" name="id_finance" id="idFinance">
                        <input type="hidden" name="id_inv" id="idInv">
                        <input type="hidden" name="id_bill" id="idBill">
                        <input type="hidden" name="id_bill" id="idBill">
                        <input type="hidden" name="total_tagihan" id="totalTagihan">
                        <input type="hidden" name="total_inv" id="totalInv">
                        <input type="hidden" name="cs_tagihan" value="<?php echo $cs_tagihan ?>">
                        <label class="fw-bold">Pilih Jenis:</label>
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis" value="pengembalian_dana" required>
                                <label class="form-check-label">Pengembalian Dana</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis" value="hapus_list" required>
                                <label class="form-check-label">Hapus List Tagihan</label>
                            </div>
                        </div>
                        <div class="mb-3" id="alasanDiv" style="display:none;">
                            <label class="fw-bold">Alasan Refund:</label>
                            <input type="text" name="alasan" class="form-control" id="alasan">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger" name="refund">Ya, Hapus Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal hapus Refund -->

    <!-- Modal Add Invoice -->
    <div class="modal fade" id="addInv" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah data invoice</h1>
                    <button type="button" class="btn-close tutup "></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <!-- Loading spinner -->
                        <div id="loadingSpinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <!-- Form elements or content -->
                        <table id="table2" class="table table-striped nowrap" style="width:100%">
                            <thead>
                                <tr class="text-white" style="background-color: navy;">
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
                                $sql = "SELECT 
                                            -- finance
                                            fnc.id_finance,
                                            fnc.jenis_inv,
                                            fnc.status_pembayaran,
                                            fnc.status_tagihan,
                                            fnc.total_inv,
                                            fnc.status_lunas,
                                            STR_TO_DATE(COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv), '%d/%m/%Y') AS tgl_inv,
                                            COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo) AS tgl_tempo,
                                            STR_TO_DATE(COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo), '%d/%m/%Y') AS tgl_tempo_convert,
                                            COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                                            COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum ) AS id_inv,
                                            COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                            COALESCE(nonppn.status_transaksi, ppn.status_transaksi,  bum.status_transaksi) AS status_trx,
                                            ft.no_tagihan,
                                            sr.id_customer,
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
                                        WHERE sr.id_customer = '$id_customer' 
                                            AND fnc.id_tagihan = ''
                                            AND fnc.jenis_inv IN ($inv_jenis)
                                            AND fnc.status_tagihan = '0'
                                        GROUP BY fnc.id_finance
                                        ORDER BY nonppn.no_inv, ppn.no_inv, bum.no_inv";
                                $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                                while ($data = mysqli_fetch_array($query)) {
                                $no_inv = $data['no_inv'];
                                $id_inv = $data['id_inv'];
                                $id_finance = $data['id_finance'];
                                $tgl_inv = $data['tgl_inv'];
                                $cs = $data['nama_cs'];
                                $cs_inv = $data['cs_inv'];
                                $date_now = date('Y-m-d');
                                $tgl_tempo_cek = $data['tgl_tempo'];
                                $tgl_tempo = $data['tgl_tempo_convert'];
                                $status_lunas = $data['status_lunas'];
                                ?>
                                    <tr>
                                        <td class="text-center text-nowrap"><?php echo $no ?></td>
                                        <td class="text-nowrap text-center"><?php echo $no_inv ?></td>
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
                                            <button class="btn btn-primary btn-sm add" data-id="<?php echo encrypt($id_finance, $key_finance) ?>" data-bill="<?php echo encrypt($id_tagihan, $key_finance); ?>"><i class="bi bi-plus-circle"></i> Add</button>
                                        </td>
                                    </tr>
                                <?php $no++ ?>
                                <?php } ?>
                            </tbody>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End modal add inv -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>
</html>
<!-- Modal Pilih Driver-->
<div class="modal fade" id="pilihDriver" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Pilih Driver</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="proses/tagihan.php" method="POST">
            <input type="hidden" name="id_tagihan" value="<?php echo $id_bill ?>">
            <div class="mb-3">
                <select name="id_user" class="form-select" required>
                    <option value="">Pilih..</option>
                    <?php  
                    $sql_driver = " SELECT us.id_user, us.nama_user, rl.nama_role 
                                    FROM $database2.user AS us
                                    LEFT JOIN $database2.user_role rl ON(us.id_user_role = rl.id_user_role)
                                    WHERE rl.nama_role = 'Driver'";
                    $query_driver = mysqli_query($connect, $sql_driver);
                    while($data_driver = mysqli_fetch_array($query_driver)){
                    ?>
                    <option value="<?php echo $data_driver['id_user'] ?>"><?php echo $data_driver['nama_user'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary" name="update_driver">Simpan</button>
            </div>                                
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Pilih Rekening CS -->
<div class="modal fade" id="pilihRek" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Pilih Rekening Customer</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="table2">
                    <thead>
                        <tr class="text-white" style="background-color: navy;">
                            <th class="text-center text-nowrap p-3" style="width: 100px;">No</th>
                            <th class="text-center text-nowrap p-3" style="width: 350px;">Nama Customer</th>
                            <th class="text-center text-nowrap p-3" style="width: 150px;">Nama Bank</th>
                            <th class="text-center text-nowrap p-3" style="width: 250px;">No. Rekening</th>
                            <th class="text-center text-nowrap p-3" style="width: 350px;">Atas Nama</th>
                            <th class="text-center text-nowrap p-3" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  
                            $no = 1;
                            $sql_bank = "SELECT 
                                            csb.id_bank_cs, csb.id_bank, csb.no_rekening, csb.atas_nama,
                                            bk.nama_bank, cs.nama_cs
                                        FROM bank_cs AS csb
                                        LEFT JOIN bank bk ON (csb.id_bank = bk.id_bank)
                                        LEFT JOIN tb_customer cs ON (cs.id_cs = csb.id_cs)
                                        WHERE cs.id_cs = '$id_customer'
                                        ORDER BY cs.nama_cs ASC";
                            $query_bank = mysqli_query($connect, $sql_bank);
                            while($data_bank = mysqli_fetch_array($query_bank)){
                                $id_bank_cs = $data_bank['id_bank_cs'];
                        ?>
                        <tr>
                            <td class="text-nowrap text-center"><?php echo $no; ?></td>
                            <td class="text-nowrap"><?php echo $data_bank['nama_cs'] ?></td>
                            <td class="text-nowrap text-center"><?php echo $data_bank['nama_bank'] ?></td>
                            <td class="text-nowrap text-center"><?php echo $data_bank['no_rekening'] ?></td>
                            <td class="text-nowrap"><?php echo $data_bank['atas_nama'] ?></td>
                            <td class="text-nowrap text-center">
                                <button type="button" id="pilih" class="btn btn-primary btn-sm" data-id="<?php echo $id_bank_cs ?>" data-id-bank="<?php echo $data_bank['id_bank'] ?>"  data-bank="<?php echo $data_bank['nama_bank'] ?>" data-rek="<?php echo $data_bank['no_rekening'] ?>" data-an="<?php echo $data_bank['atas_nama'] ?>">
                                    Pilih
                                </button>
                            </td>
                        </tr>
                        <?php $no++ ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End Pilih Rek CS -->


<!-- Script untuk popup ganda tanpa close popup awal  -->
<script>
    $('#cekRek').on('show.bs.modal', function () {
        $('#sudahBayar').modal('hide');
    });

    $('#pilihRek').on('show.bs.modal', function () {
        // Check if sudahBayar is open, if yes, hide it
        if ($('#sudahBayar').hasClass('show')) {
            $('#sudahBayar').modal('hide');
        }
    });

    $('#sudahBayar').on('hide.bs.modal', function (e) {
        // Prevent #sudahBayar from closing
        e.preventDefault();
    });

    $('#pilihRek').on('hidden.bs.modal', function () {
        // Show sudahBayar when pilihRek is hidden
        $('#sudahBayar').modal('show');
    });
  
    // document.getElementById('btnTutup').addEventListener('click', function() {
    //   // Reload halaman
    //   location.reload();
    // });

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

            // console.log("Nilai input nama_pengirim:", namaPengirimValue);
            // console.log("Nilai input rek_pengirim:", rekPengirimValue);
            // console.log("Nilai input bank_pengirim:", bankPengirimValue);
        } else {
            // Jika salah satu atau lebih nilai tidak ada, ubah display menjadi none
            $('#bank_pengirim').css('display', 'none');
            $('#reset').css('display', 'none');
            
            // Tampilkan kembali elemen <div> dengan id "selectData"
            $('#selectData').css('display', 'block');
            $('#cari').css('display', 'block');

            // console.log("Salah satu atau lebih input tidak memiliki nilai.");
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

        // console.log("Nilai input nama_pengirim:", $('#nama_pengirim').val());
        // console.log("Nilai input rek_pengirim:", $('#rek_pengirim').val());
        // console.log("Nilai input bank_pengirim:", $('#bank_pengirim').val());
    });
</script>
<!-- End SCript -->

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
            var data_id = $(this).data("id")
            $.ajax({
                url: "convert-json-modal-history.php",
                method: "POST",
                data: {data_id: data_id},
                success: function(data){
                    $("#detail_id").html(data)
                    $("#history").modal('show')
                }
            })
        })
    })
</script>
<!--End Script Untuk Modal History -->

<!-- date picker with flatpick -->
<script type="text/javascript">
    flatpickr("#date", {
    dateFormat: "d/m/Y",
  });
</script>

<!-- end date picker -->

<!-- Untuk Menampilkan Data Bayar -->
<script>
        $(document).ready(function () {
        // Ambil semua tombol dengan atribut data-bs-target bernilai #sudahBayar
        $('[data-bs-target="#sudahBayar"]').on('click', function () {
            // Ambil nilai atribut data dari tombol yang diklik
            var idInv = $(this).data('id');
            var noInv = $(this).data('noinv');
            var jenisInv = $(this).data('jenis');
            var idFinance = $(this).data('finance');
            var totalInv = $(this).data('total');

            // Set nilai input field di modal
            $('#id_inv').val(idInv);
            $('#no_inv').val(noInv);
            $('#jenis_inv').val(jenisInv);
            $('#id_finance').val(idFinance);
            $('#total_tagihan').val(totalInv);
        });
    });
</script>
<!-- End Untuk Menampilkan Data Bayar -->
<!-- Script Untuk Hapus List Tagihan -->
<script>
    // untuk menampilkan data pada atribut <td>
    $('#hapus').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var bill = button.data('bill');
        var noInv = button.data('noinv');
        var totalTagihan = button.data('totaltagihan');
        var totalInv = button.data('totalinv');
        console.log(bill);
        
        var modal = $(this);
        modal.find('.modal-body #idFinance').val(id);
        modal.find('.modal-body #idBill').val(bill);
        modal.find('.modal-body #noInv').text(noInv);
        modal.find('.modal-body #totalTagihan').val(totalTagihan);
        modal.find('.modal-body #totalInv').val(totalInv);
    })
</script>

<!-- Script Untuk Hapus List Tagihan -->
<script>
    // Untuk menampilkan/menyembunyikan input alasan menggunakan jQuery
    $(document).ready(function() {
        $('input[name="jenis"]').on('change', function() {
            var alasanDiv = $('#alasanDiv');
            var alasanInput = $('#alasan');
            if ($(this).val() === 'pengembalian_dana') {
                alasanDiv.show();
                alasanInput.prop('required', true);
            } else {
                alasanDiv.hide();
                alasanInput.prop('required', false);
            }
        });

        // untuk menampilkan data pada atribut <td>
        $('#hapusRefund').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var idInv = button.data('idinv');
            var bill = button.data('bill');
            var noInv = button.data('noinv');
            var totalTagihan = button.data('totaltagihan');
            var totalInv = button.data('totalinv');

            var modal = $(this);
            modal.find('.modal-body #idFinance').val(id);
            modal.find('.modal-body #idInv').val(idInv);
            modal.find('.modal-body #idBill').val(bill);
            modal.find('.modal-body #noInv').text(noInv);
            modal.find('.modal-body #totalTagihan').val(totalTagihan);
            modal.find('.modal-body #totalInv').val(totalInv);
        });
    });
</script>

<!-- kode JS Dikirim -->
<?php include "page/upload-img.php";  ?>
<style>
    .preview-image {
        max-width: 100%;
        height: auto;
    }
</style>
<!-- kode JS Dikirim -->
<!-- End Modal Bukti Terima -->
<?php  
    function uuid() {
        $data = openssl_random_pseudo_bytes(16);
        assert(strlen($data) == 16);
    
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
        return vsprintf('%s%s%s', str_split(bin2hex($data), 4));
    }
?>
<!-- Kode untuk Add Invoice -->
<script>
    $(document).ready(function(){
        $(document).on('click', '.add', function() {
            console.log('Tombol Add diklik!'); // Pastikan ini tampil di Console
            var button = $(this);
            var dataId = button.data('id');
            var dataBill = button.data('bill');

            console.log('Data ID: ' + dataId);
            console.log('Data Bill: ' + dataBill);

            // Show loading spinner
            $('#loadingSpinner').show();

            $.ajax({
                url: 'ajax/add-tagihan.php', // Ganti dengan URL endpoint update di server Anda
                type: 'POST',
                data: {
                    id: dataId,
                    bill: dataBill
                },
                timeout: 1500, // Set timeout ke 10 detik
                success: function(response) {
                    console.log('Data berhasil disimpan.');

                    // Berikan jeda waktu 2 detik sebelum menyembunyikan spinner
                    setTimeout(function() {
                        // Sembunyikan indikator proses setelah selesai jeda waktu
                        $('#loadingSpinner').hide();

                        // Nonaktifkan tombol setelah update berhasil
                        button.prop('disabled', true);
                    }, 400); // Jeda waktu dalam milidetik (2 detik = 2000 milidetik)
                },
                error: function(xhr, status, error) {
                    if (status === 'timeout') {
                        console.error('Koneksi timeout setelah 7 detik.');
                        // Tindakan yang perlu diambil jika koneksi timeout
                    } else {
                        console.error('Terjadi kesalahan saat menyimpan data:', error);
                    }

                    // Sembunyikan indikator proses jika terjadi kesalahan atau timeout
                    $('#loadingSpinner').hide();
                },
                complete: function() {
                    // Sembunyikan indikator proses setelah selesai
                    $('#loadingSpinner').hide();
                }
            });
        });
    });
</script>
<script>
  document.querySelector('.tutup').addEventListener('click', function() {
    location.reload(); // Me-reload halaman
  });
  document.querySelector('.tutup-refund').addEventListener('click', function() {
    location.reload(); // Me-reload halaman
  });
</script>