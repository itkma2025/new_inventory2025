<?php
$page = 'list-tagihan';
$page2 = 'tagihan-penjualan';
require_once "../akses.php";
require_once "../function/function-enkripsi.php";
require_once "../function/uuid.php";
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
        .img-bank{
            height: 60px;
            width: 140px;
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
        <?php 
            // Generate a secure random token nonce
            $nonce = bin2hex(random_bytes(16));
            $_SESSION['nonce_token'] = $nonce; 
            // Get id bill
            $id_bill = decrypt($_GET['id'], $key_finance);
            // Get id customer
            $id_customer = decrypt($_GET['id_cs'], $key_finance);
            // Get jenis inv
            $jenis_inv = $_GET['jenis'];
            $id_inv = decrypt($_GET['id_inv'], $key_finance);
            require_once '../query/jenis-cb-proforma.php'; 

            $sql_cs = $connect->query("SELECT nama_cs FROM tb_customer WHERE id_cs = '$id_customer'");
            $data_cs = mysqli_fetch_array( $sql_cs);
            $nama_cs = $data_cs['nama_cs'];

            $grand_total_inv = 0;
            $sql = "SELECT 
                        COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo) AS tgl_tempo,
                        STR_TO_DATE(COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo), '%d/%m/%Y') AS tgl_tempo_convert,
                        COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                        COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                        COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                        COALESCE(nonppn.ongkir_free, ppn.ongkir_free, bum.ongkir_free) AS ongkir_free,
                        COALESCE(nonppn.total_inv, ppn.total_inv, bum.total_inv) AS total_inv,
                        ppn.total_ppn AS total_ppn,
                        fnc.id_finance AS finance_id,
                        fnc.id_inv AS inv_id,
                        fnc.jenis_inv,
                        fnc.status_pembayaran,
                        fnc.status_lunas,
                        fnc.total_cb,
                        byr.id_finance, 
                        SUM(byr.total_bayar) AS total_pembayaran,
                        SUM(byr.total_potongan) AS total_potongan,
                        SUM(byr_cb.total_bayar) AS total_pembayaran_cb        
                    FROM finance_tagihan AS bill 
                    JOIN finance fnc ON (bill.id_tagihan = fnc.id_tagihan)
                    LEFT JOIN finance_bayar byr ON (fnc.id_finance = byr.id_finance)
                    LEFT JOIN finance_bayar_cb byr_cb ON (fnc.id_finance = byr_cb.id_finance)
                    LEFT JOIN inv_nonppn nonppn ON (fnc.id_inv = nonppn.id_inv_nonppn)
                    LEFT JOIN inv_ppn ppn ON (fnc.id_inv = ppn.id_inv_ppn)
                    LEFT JOIN inv_bum bum ON (fnc.id_inv = bum.id_inv_bum)
                    WHERE bill.id_tagihan = '$id_bill' AND fnc.id_inv = '$id_inv'
                    GROUP BY fnc.id_finance
                    ORDER BY no_inv ASC";
            $query = mysqli_query($connect, $sql);
            while($data = mysqli_fetch_array($query)){
                $id_finance = $data['finance_id'];
                $id_inv = $data['inv_id'];
                $no_inv = $data['no_inv'];
                $jenis_inv = $data['jenis_inv'];
                $tgl_tempo_cek = $data['tgl_tempo'];
                $tgl_tempo = $data['tgl_tempo_convert'];
                $date_now = date('Y-m-d');
                $free_ongkir = $data['free_ongkir'];
                $ongkir_free = $data['ongkir_free'];
                $total_inv = $data['total_inv'];
                $total_ppn = $data['total_ppn'];
                $grand_total_inv += $total_inv;
                $total_bayar = $data['total_pembayaran'] + $data['total_potongan'];
                $sisa_tagihan = $total_inv - $total_bayar;
                $total_pembayaran_cb = $data['total_pembayaran_cb'];
            ?>
        <?php } ?>
        <?php
            // query untuk menampilkan data transaksi
            $total_cb_per_produk = 0;
            $sql_trx = $connect->query("SELECT 
                                            spk.id_spk_reg,
                                            spk.id_inv,
                                            trx.total_cb
                                        FROM spk_reg AS spk
                                        LEFT JOIN transaksi_produk_reg trx ON (spk.id_spk_reg = trx.id_spk)
                                        WHERE spk.id_inv = '$id_inv'");
            while($data_trx = mysqli_fetch_array($sql_trx)){
                $total_cb_per_produk += $data_trx['total_cb'];
            }
        ?>
        <section>
            <div class="container">
                <div class="card">
                    <div class="card-header text-center">
                        <h5>Form Pembayaran <?php echo $nama_cs ?></h5>
                    </div>
                    <div class="card-body mt-3">
                        <div class="mb-3">
                            <label class="fw-bold mb-1">Tujuan Pembayaran</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tujuan_pembayaran" id="inlineRadio1" value="0" required>
                                <label class="form-check-label" for="inlineRadio1">Pembayaran Transaksi</label>
                            </div>
                            <div class="form-check form-check-inline d-none" id="pb_cashback">
                                <input class="form-check-input" type="radio" name="tujuan_pembayaran" id="inlineRadio2" value="1" required>
                                <label class="form-check-label" for="inlineRadio2">Pembayaran Cashback</label>
                            </div>
                        </div>
                        <div class="d-none" id="bayar_trx">
                            <div class="d-none" id="tagihan_belum_lunas">
                                <form action="proses/bayar.php" method="POST" enctype="multipart/form-data">
                                    <?php  
                                        $uuid = uuid();
                                        $day = date('d');
                                        $month = date('m');
                                        $year = date('y');
                                        $id_bayar = "BYR" . $year . "" . $month . "" . $uuid . "" . $day ;
                                    ?>
                                    <input type="hidden" class="form-control" name="nonce_token" value="<?php echo $_SESSION['nonce_token']; ?>">
                                    <input type="hidden" class="form-control" name="id_bayar" value="<?php echo encrypt($id_bayar, $key_finance)?>">
                                    <input type="hidden" class="form-control" name="id_cs" value="<?php echo encrypt($id_customer, $key_finance) ?>">
                                    <input type="hidden" class="form-control" name="id_inv" value="<?php echo encrypt($id_inv, $key_finance) ?>">
                                    <input type="hidden" class="form-control" name="id_bill" value="<?php echo encrypt($id_bill, $key_finance) ?>">
                                    <input type="hidden" class="form-control" name="id_finance" value="<?php echo encrypt($id_finance, $key_finance) ?>">
                                    <input type="hidden" class="form-control" name="jenis_inv" value="<?php echo $jenis_inv ?>">
                                    <div class="mb-3">
                                        <label class="fw-bold">Metode Pembayaran :</label>
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
                                            <label class="fw-bold">Pilih Bank :</label>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-evenly flex-wrap p-3">
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
                                            <label class="fw-bold">Nama Pengirim(*)</label>
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
                                            <label class="fw-bold">Rekening Pengirim(*)</label>
                                            <input type="number" class="form-control" name="rek_pengirim" id="rek_pengirim">
                                        </div>
                                        <div class="mb-3">
                                            <label class="fw-bold">Bank Pengirim(*)</label>
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
                                            <label class="fw-bold">Bukti Transfer :</label>
                                        </div>
                                        <div class="mb-3">
                                            <input type="file" name="fileku1" id="fileku1" accept=".jpg, .jpeg, .png" onchange="compressAndPreviewImage(event)">
                                            <br>
                                            <small><b>Format yang di izinkan :</b> .jpg, .jpeg, .png</small>
                                        </div>
                                        <div class="mb-3 preview-image" id="imagePreview"></div>
                                    </div>
                                    <div id="nominalDisplay" style="display: none">
                                        <div id="date-picker-wrapper" class="mb-3">
                                            <label class="fw-bold">Tanggal Bayar</label>
                                            <input type="text" class="form-control" name="tgl_bayar" id="date">
                                        </div>
                                        <div class="mb-3">
                                            <label class="fw-bold">Keterangan Bayar(*)</label>
                                            <input type="text" class="form-control" name="keterangan_bayar">
                                        </div>
                                        <div class="mb-3">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="fw-bold">No. Invoice</label>
                                                    <input type="text" class="form-control bg-light" id="no_inv" value="<?php echo $no_inv; ?>" readonly>     
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="fw-bold">Total Tagihan Invoice</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="basic-addon1">Rp</span>
                                                        <input type="text" class="form-control bg-light" name="total_tagihan" id="total_tagihan" value="<?php echo number_format($sisa_tagihan,0,'.','.');; ?>" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="fw-bold mb-1">Pilih Jenis Pembayaran</label><br>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="pilih_nominal" id="inlineRadio1" value="0" required>
                                                <label class="form-check-label" for="inlineRadio1">Full</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="pilih_nominal" id="inlineRadio2" value="1" required>
                                                <label class="form-check-label" for="inlineRadio2">Custom</label>
                                            </div>
                                        </div>
                                        <div class="d-none" id="status_potongan_cb">
                                            <div class="mb-3">
                                                <label class="fw-bold" mb-1>Status Potongan Cashback</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="status_potongan" id="statusPotongan" value="1" required>
                                                    <label class="form-check-label">Ada</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="status_potongan" id="statusPotongan" value="2" required>
                                                    <label class="form-check-label">Tidak Ada</label>
                                                </div>
                                            </div>
                                            <div class="d-none" id="potongan">
                                                <div class="row">
                                                    <div class="col-sm-4 mb-3">
                                                        <label class="fw-bold">Jumlah Potongan</label>
                                                        <div class="input-group mb-3">
                                                            <input type="text" class="form-control text-end" name="jumlah_potongan" id="jumlah_potongan" oninput="formatDiscount(this)" required>
                                                            <span class="input-group-text" id="basic-addon1">%</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-8 mb-3">
                                                        <label class="fw-bold">Nominal Potongan</label>
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text" id="basic-addon1">Rp</span>
                                                            <input type="text" class="form-control" name="nominal_potongan" id="nominal_potongan" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="fw-bold">Nominal Pembayaran</label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Rp</span>
                                                <input type="text" class="form-control" name="nominal_bayar" id="nominal" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="fw-bold">Sisa Tagihan</label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Rp</span>
                                                <input type="text" class="form-control" name="sisa_tagihan" id="sisa_tagihan" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-end">
                                        <a href="detail-bill.php?id=<?php echo encrypt($id_bill, $key_finance) ?>" class="btn btn-secondary">Tutup</a>
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
                                </form>
                            </div>
                            <div class="d-none" id="tagihan_lunas">
                                <h5 class="fw-bold">Tagihan sudah Lunas</h5>
                            </div>
                        </div>
                        <!-- Pembayaran Cashback -->
                        <div class="d-none" id="bayar_cb">
                            <div class="d-none" id="bayar_cb_belum_lunas">
                                <form action="proses/bayar-cb.php" method="POST" enctype="multipart/form-data">
                                        <?php  
                                            $uuid = uuid();
                                            $day = date('d');
                                            $month = date('m');
                                            $year = date('y');
                                            $id_bayar = "BYR-CB-" . $year . "" . $month . "" . $uuid . "" . $day ;
                                        ?>
                                        <input type="hidden" class="form-control" name="nonce_token" value="<?php echo $_SESSION['nonce_token']; ?>">
                                        <input type="hidden" class="form-control" name="id_bayar" value="<?php echo encrypt($id_bayar, $key_finance)?>">
                                        <input type="hidden" class="form-control" name="id_cs" value="<?php echo encrypt($id_customer, $key_finance) ?>">
                                        <input type="hidden" class="form-control" name="id_inv" value="<?php echo encrypt($id_inv, $key_finance) ?>">
                                        <input type="hidden" class="form-control" name="id_bill" value="<?php echo encrypt($id_bill, $key_finance) ?>">
                                        <input type="hidden" class="form-control" name="id_finance" value="<?php echo encrypt($id_finance, $key_finance) ?>">
                                        <input type="hidden" class="form-control" name="jenis_inv" value="<?php echo $jenis_inv ?>">
                                    <?php 
                                        if ($_GET['jenis'] == 'nonppn') {
                                            $action = "proses/proses-invoice-nonppn.php";
                                            $sql_status_cb = $connect->query("SELECT status_cb, jenis_cb, cb_total_inv, cb_pajak FROM cashback_nonppn WHERE id_inv = '$id_inv'");
                                            $data_status_cb = mysqli_fetch_array($sql_status_cb);
                                            $tampil_status_cb = $data_status_cb['status_cb'];
                                        } else if ($_GET['jenis'] == 'ppn') {
                                            $action = "proses/proses-invoice-ppn.php";
                                            $sql_status_cb = $connect->query("SELECT status_cb, jenis_cb, cb_total_inv, cb_pajak FROM cashback_ppn WHERE id_inv = '$id_inv'");
                                            $data_status_cb = mysqli_fetch_array($sql_status_cb);
                                            $tampil_status_cb = $data_status_cb['status_cb'];
                                        } else if ($_GET['jenis'] == 'bum') {
                                            $action = "proses/proses-invoice-bum.php";
                                            $sql_status_cb = $connect->query("SELECT status_cb, jenis_cb, cb_total_inv, cb_pajak FROM cashback_bum WHERE id_inv = '$id_inv'");
                                            $data_status_cb = mysqli_fetch_array($sql_status_cb);
                                            $tampil_status_cb = $data_status_cb['status_cb'];
                                        } else {
                                            ?>
                                                <script type="text/javascript">
                                                    window.location.href = "../404.php";
                                                </script>
                                            <?php
                                        } 
                                        $cashback_values = [];
                                        while($data_ket_cb =  mysqli_fetch_array($ket_cb)){
                                            $cashback_values[] = $data_ket_cb['ket_cashback']; // Menyimpan setiap nilai ke dalam array
                                        }
                                        $cek_ket_cb = implode(", ", $cashback_values); // Menggabungkan semua nilai menjadi satu string, dipisahkan dengan koma
                                        $cek_status_cb = "";
                                        if($status_cb == '1'){
                                            $cek_status_cb = 1;
                                        } else {
                                            $cek_status_cb = 0;
                                        }
                                    ?>
                                    <div class="mb-3">
                                        <label class="fw-bold">Jenis Cashback</label><br>
                                        <?php  
                                            // Contoh data yang diambil dari field jenis_cb tabel cashback_nonppn
                                            $jenis_cb = $data_status_cb['jenis_cb'];
                                            $jenis_cb_array = []; // Array untuk menyimpan semua $ket_cb
                                            $jenis_cb_array[] = $jenis_cb; // Tambahkan ke array

                                            // Langkah 1: Pisahkan string berdasarkan koma menjadi array
                                            $jenis_cb_array = explode(",", $jenis_cb);

                                            // Langkah 2: Tambahkan tanda kutip pada setiap elemen array
                                            $jenis_cb_quoted = array_map(function($item) {
                                                return "'" . $item . "'";  // Menambahkan tanda kutip di sekitar setiap elemen
                                            }, $jenis_cb_array);

                                            // Langkah 3: Gabungkan array dengan koma
                                            $jenis_cb_final = implode(", ", $jenis_cb_quoted);

                                            // Kode untuk menampilkan nama keterangan cashback
                                            $ket_cb_array = []; // Array untuk menyimpan semua $ket_cb
                                            $sql_nama_ket_cb = $connect->query("SELECT ket_cashback FROM keterangan_cashback WHERE id_ket_cashback IN ($jenis_cb_final)");
                                            while($nama_kat_cb = mysqli_fetch_array($sql_nama_ket_cb)){
                                                $ket_cb_array[] = $nama_kat_cb['ket_cashback'];
                                            }
                                            

                        
                                            // Pisahkan menjadi array berdasarkan koma
                                            $cashback_ids = explode(',', $jenis_cb);
                        
                                            // Enkripsi setiap ID cashback secara terpisah
                                            $encrypted_cashback_ids = array_map(function($id) use ($key_global) {
                                                return encrypt($id, $key_global);
                                            }, $cashback_ids);
                        
                                            // Gabungkan seluruh ID terenkripsi menjadi satu string
                                            $all_encrypted_ids = implode(',', $encrypted_cashback_ids);
                        
                                            // Debug: Tampilkan hasil enkripsi dari tiap ID
                                            foreach ($encrypted_cashback_ids as $encrypted_id) {
                                                // echo $encrypted_id . "<br>";
                                            }
                        
                                            // Query untuk mendapatkan semua opsi checkbox
                                            $sql_cb = $connect->query("SELECT id_ket_cashback, ket_cashback FROM keterangan_cashback ORDER BY created_date");
                                            while ($data_cb = mysqli_fetch_array($sql_cb)) {
                                                // Enkripsi id_ket_cashback agar sesuai dengan format dalam array $encrypted_cashback_ids
                                                $id_ket_cashback = encrypt($data_cb['id_ket_cashback'], $key_global);
                                                $ket_cb = $data_cb['ket_cashback'];
                                            ?>
                                                <?php if (in_array($id_ket_cashback, $encrypted_cashback_ids)): ?>
                                                    <div class="form-check me-3 d-block d-md-inline-block mr-md-3">
                                                        <input class="form-check-input" type="checkbox" 
                                                            value="<?php echo $id_ket_cashback ?>" 
                                                            id="inlineCheckbox2-<?php echo $id_ket_cashback ?>"
                                                            checked readonly>
                                                        <label class="form-check-label" for="inlineCheckbox2-<?php echo $id_ket_cashback ?>">
                                                            <?php echo $data_cb['ket_cashback'] ?>
                                                        </label>
                                                    </div>
                                                    <!-- Kode untuk mencegah uncheck -->
                                                    <script>
                                                        document.getElementById("inlineCheckbox2-<?php echo $id_ket_cashback ?>").addEventListener('click', function (e) {
                                                            e.preventDefault(); // Mencegah perubahan status checkbox
                                                        });
                                                    </script>
                                                <?php endif; ?>
                                        <?php } ?>
                                    </div>
                                    <div class="mt-3">
                                        <div class="row">
                                            <div class="col-md-6 mb-3 d-none" id="div_cb_per_barang">
                                                <label><strong>Cashback Per Produk</strong></label>
                                                <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                    <input type="text" id="cb_per_barang" name="cb_per_barang" value="<?php echo number_format($total_cb_per_produk,0,'.','.'); ?>" class="form-control" max="100" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3 d-none" id="div_cb_pengiriman">
                                                <label><strong>Cashback Pengiriman</strong></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" id="cb_pengiriman" name="cb_pengiriman" value="<?php echo number_format($ongkir_free,0,'.','.'); ?>" class="form-control" max="100" readonly>
                                                </div>
                                                <small style="color:brown">Tidak termasuk perhitungan</small>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3 d-none" id="div_cb_total_inv">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label><strong>Cashback Total Invoice</strong></label>
                                                        <div class="input-group">
                                                            <input type="text" id="cb_total_inv" name="cb_total_inv" value="<?php echo $data_status_cb['cb_total_inv']; ?>" class="form-control text-end" max="100" readonly>
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label><strong>Nominal Cashback Total Invoice</strong></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text" id="nominal_cb_total_inv" class="form-control text-end" max="100" readonly>
                                                        </div>    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3 d-none" id="div_cb_pajak">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label><strong>Cashback Pajak</strong></label>
                                                        <div class="input-group">
                                                            <input type="text" id="cb_pajak" name="cb_pajak" value="<?php echo $data_status_cb['cb_pajak']; ?>" class="form-control text-end" max="100" readonly>
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label><strong>Nominal Cashback Pajak</strong></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text" id="nominal_cb_pajak" class="form-control text-end" max="100" readonly>
                                                        </div>   
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Metode Pembayaran :</label>
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group">
                                                    <div class="input-group-text">
                                                        <input class="form-check-input mt-0" type="radio" id="cash_cb" name="metode_pembayaran" value="cash" onclick="checkRadioCb()" required>
                                                    </div>
                                                    <input type="text" class="form-control" value="Cash" readonly>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="input-group">
                                                    <div class="input-group-text">
                                                        <input class="form-check-input mt-0" type="radio" id="transfer_cb" name="metode_pembayaran" value="transfer" onclick="checkRadioCb()" required>
                                                    </div>
                                                    <input type="text" class="form-control" value="Transfer" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="fw-bold">No. Invoice</label>
                                                <input type="text" class="form-control bg-light" id="no_inv_cb" value="<?php echo $no_inv; ?>" readonly>     
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="fw-bold">Total Pembayaran Cashback</label>
                                                <div class="input-group">
                                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                                    <input type="text" class="form-control bg-light" name="total_tagihan" id="total_tagihan_cb" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="metode_cb" style="display: none;">
                                        <div class="mb-3">
                                            <label>Pilih Bank :</label>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-evenly flex-wrap p-3">
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
                                                                        <input class="form-check-input mt-3" type="radio" id="id_bank_<?php echo $data_bank['id_bank_pt_cb']; ?>" name="id_bank_pt_cb" value="<?php echo $data_bank['id_bank_pt']; ?>">
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
                                                <input type="text" class="form-control" name="nama_pengirim_cb" id="nama_pengirim_cb">
                                                <button class="btn btn-primary" type="button" id="cari_cb" data-bs-toggle="modal" data-bs-target="#pilihRekCb" style="display: block;">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                                <button class="btn btn-danger" id="reset_cb" type="button" style="display: none;">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label>Rekening Penerima(*)</label>
                                            <input type="number" class="form-control" name="rek_pengirim_cb" id="rek_pengirim_cb">
                                        </div>
                                        <div class="mb-3">
                                            <label>Bank Penerima(*)</label>
                                            <input type="hidden" class="form-control bg-light" name="id_bank_pengirim_cb" id="id_bank_pengirim_cb">
                                            <input type="text" class="form-control bg-light" name="bank_pengirim_cb" id="bank_pengirim_cb" style="display: none;">
                                        </div>
                                        <div id="selectDataCb" class="mb-3" style="display: block;">
                                            <select name="id_bank_select_cb" class="form-select selectize-js">
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
                                            <input type="file" name="fileku1" id="fileku1_cb" accept=".jpg, .jpeg, .png" onchange="compressAndPreviewImageCb(event)">
                                            <br>
                                            <small><b>Format yang di izinkan :</b> .jpg, .jpeg, .png</small>
                                        </div>
                                        <div class="mb-3 preview-image-cb" id="imagePreviewCb"></div>
                                    </div>
                                    <div id="nominalDisplay_cb" style="display: none">
                                        <div id="date-picker-wrapper" class="mb-3">
                                            <label>Tanggal Bayar</label>
                                            <input type="text" class="form-control" name="tgl_bayar_cb" id="date">
                                        </div>
                                        <div class="mb-3">
                                            <label>Keterangan Bayar(*)</label>
                                            <input type="text" class="form-control" name="keterangan_bayar_cb" id="keterangan_bayar_cb">
                                        </div>
                                        <div class="mb-3">
                                            <label>Nominal</label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Rp</span>
                                                <input type="text" class="form-control" name="nominal_cb" id="nominal_cb" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label>Sisa Tagihan</label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">Rp</span>
                                                <input type="text" class="form-control bg-light" name="sisa_bayar_cb" id="sisa_bayar_cb" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 text-end">
                                        <a href="detail-bill.php?id=<?php echo encrypt($id_bill, $key_finance) ?>" class="btn btn-secondary">Tutup</a>
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
                                </form>
                            </div>
                            <div class="d-none" id="bayar_cb_lunas">
                                <h5 class="fw-bold">Cashback sudah Lunas</h5>
                            </div>
                        </div>    
                        <p id="nb"><b>NB: Jika data Bank kosong maka button simpan tidak dapat digunakan</b></p>
                    </div>
                </div>
            </div>
        </section>
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
    </main><!-- End #main -->
    <!-- Modal Pilih Rekening CS untuk CB -->
    <div class="modal fade" id="pilihRekCb" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                        <button type="button" id="pilihCb" class="btn btn-primary btn-sm" data-id="<?php echo $id_bank_cs ?>" data-id-bank="<?php echo $data_bank['id_bank'] ?>"  data-bank="<?php echo $data_bank['nama_bank'] ?>" data-rek="<?php echo $data_bank['no_rekening'] ?>" data-an="<?php echo $data_bank['atas_nama'] ?>">
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
    </div>
    <!-- End Pilih Rek CS -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>
</html>

<?php include "page/upload-img.php";  ?>
<style>
    .preview-image {
        max-width: 100%;
        height: auto;
    }

    .preview-image-cb {
        max-width: 100%;
        height: auto;
    }
</style>

<script>
    $(document).ready(function () {
        // Ambil data ket_cb dalam bentuk array dari PHP
        const ketCbArray = <?php echo json_encode($ket_cb_array); ?>;
        // console.log(ketCbArray);  // Cek nilai array yang diterima di JavaScript

        // Loop untuk memproses setiap elemen dalam array
        ketCbArray.forEach((ketCb) => {
            // console.log(ketCb);  // Cek setiap elemen dalam array
            if (ketCb === 'Per Barang') {
                const divCbTotalInvEdit = $('#div_cb_per_barang');
                divCbTotalInvEdit.removeClass('d-none'); 
            }

            if (ketCb === 'Pengiriman') {
                const divCbTotalInvEdit = $('#div_cb_pengiriman');
                divCbTotalInvEdit.removeClass('d-none'); 
            }
            // Tangani kondisi untuk 'Total Invoice'
            if (ketCb === 'Total Invoice') {
                const divCbTotalInvEdit = $('#div_cb_total_inv');
                divCbTotalInvEdit.removeClass('d-none'); 
            }
            // Tangani kondisi untuk 'Pajak'
            if (ketCb === 'Pajak') {
                const divCbPajakEdit = $('#div_cb_pajak');
                divCbPajakEdit.removeClass('d-none');
            }
        });
    });
</script>
<script>
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

<script src="../function-js/format-diskon.js"></script>  
<!-- Kode untuk pembayaran transaksi -->
<script>
    $(document).ready(function () {
        var statusCbInv = '<?php echo $tampil_status_cb ?>';
        var statusTagihan = '<?php echo $sisa_tagihan ?>';
        console.log(statusTagihan);
        if (statusTagihan === "0"){
            $('#tagihan_lunas').removeClass('d-none');
            $('#tagihan_belum_lunas').addClass('d-none');
        } else {
            $('#tagihan_belum_lunas').removeClass('d-none');
            $('#tagihan_lunas').addClass('d-none');
        }
        // Periksa status cb terlebih dahulu
        if (statusCbInv === "1"){
            $('#pb_cashback').removeClass('d-none');
        } else {
            $('#pb_cashback').addClass('d-none');
        }
        // console.log(statusCbInv);
        $('input[name="tujuan_pembayaran"]').on('change', function () {
            if ($(this).val() === "0") {
                $('#bayar_trx').removeClass('d-none'); // Hapus class d-none
                $('#bayar_cb').addClass('d-none'); // Tambahkan class d-none
                $('#nominal').val(0); // Hapus nilai pada input
            } else {
                $('#bayar_trx').addClass('d-none'); // Tambahkan class d-none
                $('#bayar_cb').removeClass('d-none'); // Hapus class d-none
                $('input[name="status_potongan"]').removeAttr('required');
            }
        });

        $('input[name="status_potongan"]').on('change', function () {
            if ($(this).val() === "1") {
                $('#potongan').removeClass('d-none'); // Hapus class d-none
                $('#nominal').val(formatNumberInd('<?php echo $sisa_tagihan ?>')); // Tampilkan sisa tagihan
                $('#sisa_tagihan').val(0); // Tampilkan sisa tagihan
            } else if ($(this).val() === "2"){
                $('#potongan').addClass('d-none'); // Tambahkan class d-none
                $('#jumlah_potongan').val('0'); // Hapus nilai pada input
                $('#nominal_potongan').val('0'); // Hapus nilai pada input
                $('#nominal').val(formatNumberInd('<?php echo $sisa_tagihan ?>')); // Tampilkan sisa tagihan
                $('#sisa_tagihan').val(0); // Tampilkan sisa tagihan
            }
        });
    });

    function checkRadio() {
        var $idBankRadios = $('input[name="id_bank_pt"]');
        var $transferCheck = $('#transfer');
        var $cashCheck = $('#cash');
        var $namaPengirim = $('#nama_pengirim');
        var $rekPengirim = $('#rek_pengirim');
        var $bankPengirim = $('#bank_pengirim');
        var $fileku = $('#fileku1');
        var $metode = $('#metode');
        var $nominalDisplay = $('#nominalDisplay');
        var $totalTagihanInput = $('#total_tagihan');
        var $sisaTagihanInput = $('#sisa_tagihan');
        var $tombolSimpan = $('button[name="simpan-pembayaran"]');
        var $nominalInput = $('#nominal');

        if ($cashCheck.is(':checked')) {
            $idBankRadios.prop('checked', false).prop('required', false);
            $metode.hide();
            $nominalDisplay.show();
            $namaPengirim.val('');
            $rekPengirim.val('');
            $bankPengirim.val('');
            $fileku.removeAttr('required');
            $sisaTagihanInput.val($totalTagihanInput.val());
            if (parseFloat($sisaTagihanInput.val()) < 0) {
                $tombolSimpan.prop('disabled', true);
            }
        } else if ($transferCheck.is(':checked')) {
            $idBankRadios.prop('required', true);
            $metode.show();
            $nominalDisplay.show();
            $fileku.prop('required', true);
            $sisaTagihanInput.val($totalTagihanInput.val());
            if (parseFloat($sisaTagihanInput.val()) < 0) {
                $tombolSimpan.prop('disabled', true);
            }
            $nominalInput.val('');
        }
    }

    $(document).ready(function() {
        var totalTagihan = <?php echo $sisa_tagihan ?>; // Total tagihan, bisa diganti sesuai dengan nilai yang didapatkan

        // Fungsi untuk memformat angka menjadi format Rupiah
        function formatNumberInd(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Fungsi untuk menghitung nominal potongan dan mengupdate nominal pembayaran serta sisa tagihan
        function updateNominalAndSisa() {
            const jenisPembayaran = $('input[name="pilih_nominal"]:checked').val();
            const statusCb = $('input[name="status_potongan"]:checked').val();
            const jumlahPotongan = parseFloat($('#jumlah_potongan').val()) || 0;
            const nominalPotongan = (jumlahPotongan / 100) * totalTagihan; // Menghitung potongan berdasarkan persen
            let nominalBayar = totalTagihan; // Default nominal bayar adalah total tagihan

            // Jika pembayaran custom dan potongan ada
            if (jenisPembayaran === "1") {
                if (jumlahPotongan > 0) {
                    nominalBayar = totalTagihan - nominalPotongan; // Mengurangi nominal pembayaran dengan potongan
                } else {
                    // Untuk custom tanpa potongan, biarkan nominal bayar dikendalikan oleh user
                    nominalBayar = parseFloat($('#nominal').val().replace(/\./g, '').replace(',', '.')) || totalTagihan; // Ambil nominal yang diinputkan user
                }
            }

            // Update nominal pembayaran dan nominal potongan
            $('#nominal_potongan').val(formatNumberInd(nominalPotongan));

            if (jenisPembayaran === "0") {
                // Jika jenis pembayaran full
                $('#nominal').prop('readonly', true); // Jadikan readonly
                $('#nominal').val(formatNumberInd(totalTagihan)); // Set nilai nominal ke total tagihan
                $('#sisa_tagihan').val("0"); // Tidak ada sisa tagihan
                // console.log('Jenis Pembayaran: Full');

                // Tampilkan elemen potongan
                $('#status_potongan_cb').removeClass('d-none');
                $('input[name="status_potongan"]').prop('required', true);
                $('input[name="jumlah_potongan"]').prop('required', true);
                $('input[name="status_potongan"][value="1"]').prop('checked', false);

                $('#jumlah_potongan').val('0'); // Hapus nilai pada input
                $('#nominal_potongan').val('0'); // Hapus nilai pada input

            } else if (jenisPembayaran === "1") {
                // Jika jenis pembayaran custom
                $('#nominal').removeAttr('readonly'); // Hapus readonly
                $('#nominal').val(0); // Set nilai nominal ke total tagihan
                $('#sisa_tagihan').val(formatNumberInd(totalTagihan)); // Tidak ada sisa tagihan
                // console.log('Jenis Pembayaran: Custom');
                // console.log('Sisa Tagihan (Set awal):', formatNumberInd(totalTagihan)); // Debuging

                // Sembunyikan elemen potongan
                $('#status_potongan_cb').addClass('d-none');
                $('input[name="status_potongan"]').prop('required', false);
                $('input[name="jumlah_potongan"]').prop('required', false);
                $('#jumlah_potongan').val('');
                $('#nominal_potongan').val('');
                $('input[name="status_potongan"][value="1"]').prop('checked', true);

                $('#jumlah_potongan').val('0'); // Hapus nilai pada input
                $('#nominal_potongan').val('0'); // Hapus nilai pada input
            }
        }

        // Fungsi untuk menampilkan atau menyembunyikan potongan cashback
        function togglePotongan() {
            const statusPotongan = $('input[name="status_potongan"]:checked').val();
            $('#potongan').toggleClass('d-none', statusPotongan !== "1");
        }

        // Event listener untuk perubahan pada radio button jenis pembayaran
        $('input[name="pilih_nominal"]').change(updateNominalAndSisa);

        // Event listener untuk input potongan
        $('#jumlah_potongan').on('input', function() {
            const jumlahPotongan = parseFloat($(this).val()) || 0; // Ambil nilai potongan (dalam persen)
            
            // Menghitung nominal potongan berdasarkan persen
            const nominalPotongan = (jumlahPotongan / 100) * totalTagihan;
            let nominalBayar = totalTagihan; // Nilai default nominal pembayaran adalah total tagihan

            // Jika ada potongan, kurangi total tagihan dengan nominal potongan
            if (jumlahPotongan > 0) {
                nominalBayar = totalTagihan - nominalPotongan; // Mengurangi total tagihan dengan nominal potongan
            }

            // Update nilai nominal pembayaran dan nominal potongan
            $('#nominal').val(formatNumberInd(nominalBayar));
            $('#nominal_potongan').val(formatNumberInd(nominalPotongan));

            // Update sisa tagihan
            const sisaTagihan = totalTagihan - nominalBayar - nominalPotongan; // Sisa tagihan = total tagihan - nominal bayar
            $('#sisa_tagihan').val(formatNumberInd(sisaTagihan)); // Update sisa tagihan
        });

        // Event listener untuk status potongan cashback
        $('input[name="status_potongan"]').change(togglePotongan);

        // Format input manual nominal dengan format angka (Rupiah)
        $('#nominal').on('input', function() {
            let value = $(this).val();
            value = value.replace(/[^\d]/g, ''); // Hapus karakter non-digit
            $(this).val(new Intl.NumberFormat('id-ID').format(value));

            // Cek nilai input user dan pastikan tidak lebih besar dari total tagihan
            let nominalBayar = parseFloat(value.replace(/\./g, '').replace(',', '.')) || 0;
            if (nominalBayar > totalTagihan) {
                nominalBayar = totalTagihan; // Batasi ke total tagihan
                $(this).val(new Intl.NumberFormat('id-ID').format(nominalBayar));
            }

            // Update nominal dan sisa tagihan hanya berdasarkan input manual
            const sisaTagihan = totalTagihan - nominalBayar;
            $('#sisa_tagihan').val(formatNumberInd(sisaTagihan));
        });



        // Inisialisasi awal
        updateNominalAndSisa();
        togglePotongan();
    });
</script>
<!-- Kode untuk pembayaran CB -->
<script>
    $(document).ready(function() {
        window.checkRadioCb = function() { // Ekspor fungsi ke global scope
            var idBankRadios = $("input[name='id_bank_pt_cb']");
            var transferCheck = $("#transfer_cb");
            var cashCheck = $("#cash_cb");
            var namaPengirim = $("#nama_pengirim_cb");
            var rekPengirim = $("#rek_pengirim_cb");
            var bankPengirim = $("#bank_pengirim_cb");
            var fileku = $("#fileku1_cb");
            var metode = $("#metode_cb");
            var nominalDisplay = $("#nominalDisplay_cb");
            var totalTagihanInput = $("#pb_bayar_cb");
            var sisaTagihanCb = $("#sisa_tagihan_cb");
            var tombolSimpan = $("button[name='simpan-pembayaran_cb']");
            var ketCb = $('#keterangan_bayar_cb');
            var nominalInputCb = $("#nominal_cb");
            var totalTagihan = '<?php echo $total_inv ?>';
            var totalPpn = '<?php echo $total_ppn ?>';
            var cbPerBarang = $("#cb_per_barang").val().replace(".", "");
            var cbPengiriman = $("#cb_pengiriman").val().replace(".", "");
            var cbPajak = $("#cb_pajak").val();
            var cbTotalInv = $("#cb_total_inv").val();
            var totalBayarCb = '<?php echo $total_pembayaran_cb ?>';

            // Proses perhitungan CB
            var totalTagihanCb = totalTagihan - totalPpn;
            var hasilCbPajak = totalPpn * (cbPajak / 100);
            var hasilCbTotalInv = totalTagihanCb * (cbTotalInv / 100);
            var tagihanCbBayar = Number(hasilCbPajak) + Number(hasilCbTotalInv) + Number(cbPerBarang) - Number(totalBayarCb);

            $('#nominal_cb_total_inv').val(formatNumberInd(hasilCbTotalInv));
            $('#nominal_cb_pajak').val(formatNumberInd(hasilCbPajak));
            $('#total_tagihan_cb').val(formatNumberInd(tagihanCbBayar));
            $("#sisa_bayar_cb").val(formatNumberInd(tagihanCbBayar));

            // console.log(sisaTagihanCb);
            


            if (cashCheck.is(":checked")) {
                idBankRadios.each(function() {
                    $(this).prop("checked", false).prop("required", false);
                });
                metode.hide();
                nominalDisplay.show();
                namaPengirim.val('');
                rekPengirim.val('');
                bankPengirim.val('');
                sisaTagihanCb.val(totalTagihanInput.val());
                fileku.removeAttr("required");
                if (parseFloat(sisaTagihanCb.val()) < 0) {
                    tombolSimpan.prop("disabled", true);
                }
                ketCb.val('');
                nominalInputCb.val('');
            } else if (transferCheck.is(":checked")) {
                idBankRadios.each(function() {
                    $(this).prop("required", true);
                });
                metode.show();
                nominalDisplay.show();
                sisaTagihanCb.val(totalTagihanInput.val());
                fileku.attr("required", true);
                if (parseFloat(sisaTagihanCb.val()) < 0) {
                    tombolSimpan.prop("disabled", true);
                }
                $('#keterangan_bayar_cb').val('');
                nominalInputCb.val('');
            }
        };
        // Panggil fungsi saat halaman dimuat
        checkRadioCb();


        // Ambil nilai awal dari sisa bayar dan konversi menjadi angka
        let totalTagihan = parseInt($('#sisa_bayar_cb').val().replace(/\D/g, '')) || 0;

        // Format dan tampilkan totalTagihan di sisa bayar
        $('#sisa_bayar_cb').val(totalTagihan.toLocaleString('id-ID'));

        if (totalTagihan == 0) {
            $('#bayar_cb_lunas').removeClass('d-none');
            $('#bayar_cb_belum_lunas').addClass('d-none');
            $('#nb').addClass('d-none');
        } else {
            $('#bayar_cb_belum_lunas').removeClass('d-none');
        }
    
        // Event handler ketika nilai pada input nominal_cb berubah
        $('#nominal_cb').on('input', function () {
            // Ambil nilai input nominal_cb
            let nominal = parseInt($(this).val().replace(/\D/g, '')) || 0; // Hapus format non-angka

            // Cegah input melebihi sisa bayar
            if (nominal > totalTagihan) {
                nominal = totalTagihan;
            }

            // Tampilkan nilai nominal dalam format Indonesia (Rupiah)
            $(this).val(nominal.toLocaleString('id-ID'));

            // Hitung sisa bayar baru
            let sisaBayar = totalTagihan - nominal;

            // Tampilkan sisa bayar dalam format Rupiah
            $('#sisa_bayar_cb').val(sisaBayar.toLocaleString('id-ID'));
        });
    });
</script>
<script>
    // select data bank CS
    $(document).on('click', '#pilihCb', function (e) {
        var atasNama = $(this).data('an');
        var noRek = $(this).data('rek');
        var idBank = $(this).data('id-bank');
        var namaBank = $(this).data('bank');
        // Trigger event input setelah mengubah nilai
        $('#nama_pengirim_cb').val(atasNama).trigger('input'); 
        $('#rek_pengirim_cb').val(noRek).trigger('input'); 
        $('#id_bank_pengirim_cb').val(idBank).trigger('input'); 
        $('#bank_pengirim_cb').val(namaBank).trigger('input'); 

        // Memeriksa nilai elemen input setelah diatur
        var namaPengirimValue = $('#nama_pengirim_cb').val();
        var rekPengirimValue = $('#rek_pengirim_cb').val();
        var idBankPengirimValue = $('#id_bank_pengirim_cb').val();
        var bankPengirimValue = $('#bank_pengirim_cb').val();

        if (namaPengirimValue && rekPengirimValue && bankPengirimValue) {
            // Jika semua nilai ada, ubah display menjadi block
            $('#bank_pengirim_cb').css('display', 'block');

            $('#reset_cb').css('display', 'block');
            
            // Sembunyikan elemen <div> dengan id "selectData"
            $('#selectDataCb').css('display', 'none');

            $('#cari_cb').css('display', 'none');

            // console.log("Nilai input nama_pengirim:", namaPengirimValue);
            // console.log("Nilai input rek_pengirim:", rekPengirimValue);
            // console.log("Nilai input bank_pengirim:", bankPengirimValue);
        } else {
            // Jika salah satu atau lebih nilai tidak ada, ubah display menjadi none
            $('#bank_pengirim_cb').css('display', 'none');
            $('#reset_cb').css('display', 'none');
            
            // Tampilkan kembali elemen <div> dengan id "selectData"
            $('#selectDataCb').css('display', 'block');
            $('#cari_cb').css('display', 'block');

            // console.log("Salah satu atau lebih input tidak memiliki nilai.");
        }

        $('#pilihRekCb').modal('hide');
    });

    // Reset Button
    $(document).on('click', '#reset_cb', function (e) {
        // Mengosongkan nilai input
        $('#nama_pengirim_cb').val('').trigger('input');
        $('#rek_pengirim_cb').val('').trigger('input');
        $('#id_bank_pengirim_cb').val('').trigger('input');
        $('#bank_pengirim_cb').val('').trigger('input');

        // Menampilkan kembali elemen <div> dengan id "selectData"
        $('#selectDataCb').css('display', 'block');
        $('#cari_cb').css('display', 'block');

        // Sembunyikan elemen <div> dengan id "bank_pengirim"
        $('#bank_pengirim_cb').css('display', 'none');
        $('#reset_cb').css('display', 'none');
    });
</script>

