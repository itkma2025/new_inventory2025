<?php
$page = 'list-tagihan';
$page2 = 'tagihan-penjualan';
include "akses.php";
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
        @media only screen and (max-width: 500px) {
            body {
                font-size: 14px;
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
        <!-- SWEET ALERT -->
        <section>
            <div class="card shadow p-2">
                <?php  
                    include "koneksi.php";
                    $id_bill = base64_decode($_GET['id']);
                    $sql_bill_cs = "SELECT 
                                        bill.total_tagihan,
                                        bill.tgl_tagihan,
                                        bill.no_tagihan,
                                        bill.status_cetak,
                                        bill.id_driver,
                                        fnc.id_tagihan AS id_tagihan_finance,
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
                    include "koneksi.php";
                    $id_bill = base64_decode($_GET['id']);
                    $sql_bill_total = "SELECT   bill.id_tagihan, 
                                                bill.total_tagihan,
                                        
                                                byr.id_finance, 
                                                SUM(byr.total_bayar) AS total_pembayaran       
                                        FROM finance_tagihan AS bill
                                        LEFT JOIN finance_bayar byr ON (bill.id_tagihan = byr.id_tagihan)
                                        LEFT JOIN finance fnc ON (byr.id_finance = fnc.id_finance)
                                        WHERE bill.id_tagihan = '$id_bill'";
                    $query_bill = mysqli_query($connect, $sql_bill_total);
                    while($data_bill_total = mysqli_fetch_array($query_bill)){
                        $total_bayar = $data_bill_total ['total_pembayaran'];
                        $total_tagihan = $data_bill_total['total_tagihan'];
                        $total_sisa_tagihan = $total_tagihan - $total_bayar;
                ?>
                <?php } ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Total Tagihan</label>
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping">Rp</span>
                                    <input type="text" class="form-control text-end" value="<?php echo number_format($total_tagihan,0,'.','.')?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Total Bayar</label>
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping">Rp</span>
                                    <input type="text" class="form-control text-end" value="<?php echo number_format($total_bayar,0,'.','.')?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                            <a href="list-tagihan-penjualan.php?date_range=weekly" class="btn btn-warning btn-detail mb-2">
                                <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                            </a>
                            <?php 
                                if($total_sisa_tagihan == 0){
                                    echo '
                                    <button type="button" class="btn btn-secondary mb-2">
                                        <i class="bi bi-check-circle"></i> Tagihan Sudah Lunas
                                    </button>';
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
                                    <td class="text-center text-nowrap p-3">Cs. Invoice</td>
                                    <td class="text-center text-nowrap p-3">Tgl. Invoice</td>
                                    <td class="text-center text-nowrap p-3">Tgl. Tempo</td>
                                    <td class="text-center text-nowrap p-3">Status Pembayaran</td>
                                    <td class="text-center text-nowrap p-3">Total Invoice</td>
                                    <td class="text-center text-nowrap p-3">Total Pembayaran</td>
                                    <td class="text-center text-nowrap p-3">Sisa Tagihan</td>
                                    <td class="text-center text-nowrap p-3">Status Lunas</td>
                                    <td class="text-center text-nowrap p-3">Status Tempo</td>
                                    <td class="text-center text-nowrap p-3">Aksi</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  
                                    include "koneksi.php";
                                    $id_bill = base64_decode($_GET['id']);
                                    $no = 1;
                                    $sql = "SELECT  COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo) AS tgl_tempo,
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
                                                    SUM(byr.total_bayar) AS total_pembayaran   
                                            FROM finance_tagihan AS bill 
                                            JOIN finance fnc ON (bill.id_tagihan = fnc.id_tagihan)
                                            LEFT JOIN finance_bayar byr ON (fnc.id_finance = byr.id_finance)
                                        
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
                                        $jenis_inv = $data['jenis_inv'];
                                        $tgl_tempo_cek = $data['tgl_tempo'];
                                        $tgl_tempo = $data['tgl_tempo_convert'];
                                        $date_now = date('Y-m-d');
                                        $total_inv = $data['total_inv'];
                                        $total_bayar = $data['total_pembayaran'];
                                        $sisa_tagihan = $total_inv - $total_bayar;
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $no; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data['no_inv'] ?></td>
                                    <td class="text-center text-nowrap"><?php echo strtoupper($data['jenis_inv'])?></td>
                                    <td class="text-center text-nowrap"><?php echo $data['cs_inv'] ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data['tgl_inv'] ?></td>
                                    <td class="text-center text-nowrap">
                                        <?php
                                            if (!empty($tgl_tempo_cek)) {
                                                echo $tgl_tempo_cek;
                                            } else {
                                                echo "Tidak Ada Tempo";
                                            }
                                        ?>
                                    </td>
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
                                    <td class="text-end text-nowrap"><?php echo number_format($total_bayar,0,'.','.') ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($sisa_tagihan,0,'.','.') ?></td>
                                    <td class="text-center text-nowrap">
                                        <?php 
                                            if($data['status_lunas'] == 0) {
                                                echo "Belum Lunas";
                                            } else {
                                                echo "Lunas";
                                            }
                                           
                                        ?>
                                    </td>
                                    <?php  
                                        if (!empty($tgl_tempo_cek)) {
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
                                        echo '<td class="text-end text-nowrap">Tidak Ada Tempo</td>';
                                        }
                                    ?> 
                                    <td class="text-center text-nowrap">
                                       <?php  
                                            if ($sisa_tagihan == 0) {
                                                ?>
                                                    <button class="btn btn-secondary btn-sm mb-2"><i class="bi bi-check-circle"></i> Lunas</button>
                                                    <br>
                                                    <button class="btn btn-primary btn-sm view_data" data-bs-toggle="modal" data-bs-target="#history" data-id="<?php echo $id_finance ?>"><i class="bi bi-info-circle"></i> History Payment</button>
                                                <?php
                                            } else {
                                                ?>
                                                    <button class="btn btn-primary btn-sm view_data" data-bs-toggle="modal" data-bs-target="#history" data-id="<?php echo $id_finance ?>">
                                                        <i class="bi bi-info-circle"></i> History Payment
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
                    </div>
                </div>
        </section>
    </main><!-- End #main -->

    <!-- Modal Bayar -->
    <div class="modal fade" id="sudahBayar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <style>
                .img-bank{
                    height: 60px;
                    width: 140px;
                }
            </style>
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Form Pembayaran <?php echo $nama_cs ?></h1>
                </div>
                <div class="modal-body">
                    <form action="proses/bayar.php" method="POST" enctype="multipart/form-data">
                        <div class="card-body">
                            <input type="hidden" name="id_cs" value="<?php echo $id_customer ?>">
                            <input type="hidden" id="id_inv" name="id_inv">
                            <input type="hidden" id="jenis_inv" name="jenis_inv">
                            <input type="hidden" name="user" value="<?php echo $_SESSION['tiket_nama'] ?>">
                            <input type="hidden" name="id_bill" value="<?php echo $id_bill ?>">
                            <input type="hidden" name="id_finance" id="id_finance">
                            <div class="mb-3">
                                <label>Total Tagihan</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                    <input type="text" class="form-control" name="total_tagihan"  id="total_tagihan"  readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Metode Pembayaran :</label>
                                <div class="row">
                                    <div class="col">
                                        <div class="input-group">
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" type="radio" id="cash" name="metode_pembayaran" value="cash" onclick="checkRadio()">
                                            </div>
                                            <input type="text" class="form-control" value="Cash" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group">
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" type="radio" id="transfer" name="metode_pembayaran" value="transfer" onclick="checkRadio()">
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
                                                include "koneksi.php";
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
                                                                <input class="form-check-input mt-3" type="radio" id="id_bank_<?php echo $row['id_bank']; ?>" name="id_bank_pt" value="<?php echo $data_bank['id_bank_pt']; ?>">
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
                                    <label>Nama Pengirim(*)</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name="nama_pengirim" id="nama_pengirim">
                                        <button class="btn btn-primary" type="button" id="cari" data-bs-toggle="modal" data-bs-target="#pilihRek" style="display: block;">
                                            <i class="bi bi-search"></i>
                                        </button>
                                        <button class="btn btn-danger" id="reset" type="button" style="display: none;">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Rekening Pengirim(*)</label>
                                    <input type="number" class="form-control" name="rek_pengirim" id="rek_pengirim">
                                </div>
                                <div class="mb-3">
                                    <label>Bank Pengirim(*)</label>
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
                            <div class="mb-3">
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnTutup">Tutup</button>
                                    <?php  
                                        if($total_data_bank != 0){
                                            ?>
                                                <button type="submit" class="btn btn-primary" name="simpan-pembayaran">Simpan</button>
                                            <?php
                                        }else{
                                            ?>
                                                <button type="submit" class="btn btn-primary" name="simpan-pembayaran" disabled>Simpan</button>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </form>
                    <p><b>NB: Jika data Bank kosong maka button simpan tidak dapat digunakan</b></p>
                </div>
            </div>
        </div>
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
                var totalTagihanInput = document.getElementById('total_tagihan');
                var sisaTagihanInput = document.getElementById('sisa_tagihan');
                var tombolSimpan = document.querySelector('button[name="simpan-pembayaran"]');
                var nominalInput = document.getElementById('nominal');

                // Jika "cash" dicentang, set status checked pada setiap "id_bank_pt" menjadi false
                if (cashCheck.checked) {
                    // console.log("Cash selected, clearing id_bank_pt selection.");
                    for (var i = 0; i < idBankRadios.length; i++) {
                        idBankRadios[i].checked = false;
                        idBankRadios[i].required = false;
                    }
                    metode.style.display = 'none';
                    nominalDisplay.style.display = 'block';
                    namaPengirim.value = '';
                    // namaPengirim.removeAttribute('required');
                    rekPengirim.value = '';
                    // rekPengirim.removeAttribute('required');
                    bankPengirim.value = '';
                    // bankPengirim.removeAttribute('required');
                    sisaTagihanInput.value = totalTagihanInput.value;
                    fileku.removeAttribute('required');
                    if (parseFloat(sisaTagihanInput.value) < 0) {
                        tombolSimpan.disabled = true;
                    }
                }

                // Jika "transfer" dicentang, jalankan loop untuk mengecek status setiap "id_bank_pt"
                else if (transferCheck.checked) {
                    // console.log("Transfer selected!");
                    for (var i = 0; i < idBankRadios.length; i++) {
                        console.log("id_bank_pt" + (i + 1) + " checked:", idBankRadios[i].checked);
                        idBankRadios[i].required = true;
                    }
                    metode.style.display = 'block';
                    nominalDisplay.style.display = 'block';
                    sisaTagihanInput.value = totalTagihanInput.value; // Set nilai sisa tagihan sama dengan total tagihan
                    // namaPengirim.setAttribute('required', 'true'); // Membuat Atribut Required
                    // rekPengirim.setAttribute('required', 'true'); // Membuat Atribut Required
                    fileku.setAttribute('required', 'true'); // Membuat Atribut Required
                    // Menonaktifkan tombol "Simpan" jika nilai sisa tagihan negatif
                    if (parseFloat(sisaTagihanInput.value) < 0) {
                        tombolSimpan.disabled = true;
                    }
                    nominalInput.value = ''; // Reset nilai input nominal saat beralih ke opsi "Transfer"
                }
            }
        </script>

        <script>
            // Get the input elements
            const totalTagihanInput = document.getElementById("total_tagihan");
            const nominalInput = document.getElementById("nominal");
            const sisaTagihanInput = document.getElementById("sisa_tagihan");
            const btnTutup = document.getElementById("btnTutup");

            // Function to format number with Indonesian format
            function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
            }

            // Function to parse Indonesian formatted number into a valid number
            function parseNumber(str) {
            const parsedValue = parseFloat(str.replace(/\./g, "").replace(",", "."));
            return isNaN(parsedValue) ? 0 : parsedValue;
            }

            // Function to update input "nominal" with formatted number
            function formatInputNominal() {
            let value = nominalInput.value;
            value = value.replace(/\./g, ""); // Remove all dots as thousand separators
            value = value.replace(/,/g, "."); // Replace comma with dot as decimal separator
            nominalInput.value = formatNumber(value);
            }

            // Function to perform subtraction and update the result
            function calculateSisaTagihan() {
            const totalTagihan = parseNumber(totalTagihanInput.value);
            let nominal = parseNumber(nominalInput.value);

            // Ensure nominal does not exceed totalTagihan
            if (nominal > totalTagihan) {
                nominal = totalTagihan;
                nominalInput.value = formatNumber(nominal);
            }

            const sisaTagihan = totalTagihan - nominal;

            // Update the "sisa_tagihan" input value with the result formatted in Indonesian format
            sisaTagihanInput.value = formatNumber(sisaTagihan);
            }

            // Function to reload modal content
            btnTutup.addEventListener("click", () => {
            location.reload(); // Reload the page
            });

            // Function to attach event listener to the "nominal" input
            function initializeModal() {
            // Attach event listener to the "nominal" input to trigger calculation and format on input change
            nominalInput.addEventListener("input", () => {
                // Get the input value
                let inputValue = nominalInput.value;

                // Remove non-numeric characters using a regular expression
                inputValue = inputValue.replace(/[^\d]/g, "");

                // Update the input value with the sanitized value
                nominalInput.value = inputValue;

                // Format input "nominal"
                formatInputNominal();

                // Perform calculation
                calculateSisaTagihan();
            });
            }

            // Initialize modal when the script is loaded
            initializeModal();
        </script>
    </div>
    <!-- End Modal Bayar -->

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
                    include 'koneksi.php';
                    $sql_driver = " SELECT us.id_user, us.nama_user, rl.role 
                                    FROM user AS us
                                    LEFT JOIN user_role rl ON(us.id_user_role = rl.id_user_role)
                                    WHERE rl.role = 'Driver'";
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
                            include "koneksi.php";
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
  
    document.getElementById('btnTutup').addEventListener('click', function() {
      // Reload halaman
      location.reload();
    });

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
<!-- Untuk menampilkan data Histori pad amodal -->
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
    defaultDate: new Date(),
  });
</script>

<!-- end date picker -->

<!-- Untuk Menampilkan Data Bayar -->
<script>
    // Wait for the DOM to be ready
    document.addEventListener('DOMContentLoaded', function () {
        // Get all buttons with the data-bs-target attribute equal to #sudahBayar
        var totalButtons = document.querySelectorAll('[data-bs-target="#sudahBayar"]');

        // Iterate through each button and attach an event listener
        totalButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                // Get the data-id attribute value from the clicked button
                var idInv = button.getAttribute('data-id');
                var jenisInv = button.getAttribute('data-jenis');
                var idFinance = button.getAttribute('data-finance');
                var totalInv = button.getAttribute('data-total');

                // Set the value of the id_inv input field in the modal
                document.getElementById('id_inv').value = idInv;
                document.getElementById('jenis_inv').value = jenisInv;
                document.getElementById('id_finance').value = idFinance;
                document.getElementById('total_tagihan').value = totalInv;
            });
        });
    });
</script>

</script>
<!-- End Untuk Menampilkan Data Bayar -->

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