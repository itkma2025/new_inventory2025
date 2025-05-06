<?php  
    require_once "akses.php";
    require_once "function/format-tanggal.php";
    require_once "function/function-enkripsi.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Inventory KMA</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" crossorigin="anonymous">
  <?php include "page/head.php"; ?>
  <?php include "function/terbilang.php" ?>
  <style>
    body{
        width: 1000px;
        margin: 0 auto;
        margin-top: 20px;
        background-color: white;
        font-family: Arial, Helvetica, sans-serif;
        color: black;
        letter-spacing: 0.5px;
    }
    table{
        font-size: 16px;
        color: black !important;
        border-collapse: collapse;
    }

    th.col-3, td.col-3 {
        width: 18%; /* Adjust the value based on your design */
    }

    th.col-1, td.col-1 {
        width: 1%; /* Adjust the value based on your design */
    }

    .parallelogram {
        width: 300px;
        height: 75px;
        border: 2px solid black;
        transform: skew(-20deg);
        margin: 20px;
        color: black;
        font-weight: bold;
        padding: 5px;
        box-sizing: border-box;
        line-height: 50px;
        display: flex; /* Menambahkan display flex */
        justify-content: center; /* Menengahkan secara horizontal */
        align-items: center; /* Menengahkan secara vertikal */
    }

    .container {
        display: flex;
        flex-direction: column;
    }
    .input-row {
        display: flex;
        align-items: center;
    }
    .input-row input {
        border: none;
        padding: 1px;
        font-weight: bold;
        text-align: left;
        flex: 1; /* Ensure inputs take up available space */
        box-sizing: border-box; /* Include padding in the element's width */
    }
    .separator {
        border-bottom: 2px solid black;
        width: 0; /* Ensure separator spans full width */
    }
    
    @media print {
        #printButton {
            display: none;
        }

        .print-no-placeholder::placeholder {
            color: transparent;
        }
    }
  </style>
</head>

<body>
    <div class="text-center">
        <button id="printButton" class="btn btn-primary">
            <i class="fas fa-print"></i> Print
        </button>
    </div>
    <div class="card-body p-2">
        <?php  
            $id_inv = decrypt($_GET['id_inv'], $key_global);

           
            $id_inv_substr = substr($id_inv, 0, 3);

            include 'koneksi.php';
            $sql_surat_jalan = " SELECT
                                    sk.tgl_kirim, 
                                    COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                                    COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                    COALESCE(nonppn.total_inv, ppn.total_inv, bum.total_inv) AS total_inv,
                                    spk.no_po,
                                    spk.id_inv,
                                    cs.alamat
                                FROM spk_reg AS spk
                                LEFT JOIN inv_nonppn nonppn ON spk.id_inv = nonppn.id_inv_nonppn
                                LEFT JOIN inv_ppn ppn ON spk.id_inv = ppn.id_inv_ppn
                                LEFT JOIN inv_bum bum ON spk.id_inv = bum.id_inv_bum
                                LEFT JOIN status_kirim sk ON spk.id_inv = sk.id_inv
                                LEFT JOIN tb_customer cs ON spk.id_customer = cs.id_cs
                                WHERE spk.id_inv = '$id_inv'";
            $query_surat_jalan = mysqli_query($connect, $sql_surat_jalan);
            $data_surat_jalan = mysqli_fetch_array($query_surat_jalan);
            $tgl_kirim = $data_surat_jalan['tgl_kirim'];
            $cs_inv = $data_surat_jalan['cs_inv'];
            $alamat = $data_surat_jalan['alamat'];
            $no_po = $data_surat_jalan['no_po'];
            $no_inv = $data_surat_jalan['no_inv'];
            $total_inv = round($data_surat_jalan['total_inv']);
            $total_inv_terbilang = terbilang($total_inv);
        ?>
        <div class="row p-3">
            <div class="col-sm-8">
                <?php  
                    if ($id_inv_substr == 'PPN'){
                        ?>
                            <img class="img-fluid" src="assets/img/header-kma.jpg">
                        <?php
                    }
                ?>
            </div>
            <div class="col-sm-4 border border-dark">
                <p class="text-center mt-3" style="font-size: 27px;">
                    <b>KWITANSI</b>
                </p>
            </div>
        </div>
        <div class="row p-3">
            <div class="col-sm-12" style="font-size: 16px;">
                <table>
                    <tr>
                        <th class="col-3 p-1">Telah Terima Dari</th>
                        <th class="col-1 p-1 text-end">:</th>
                        <th class="col-8 p-1"><?php echo $cs_inv ?></th>
                    </tr>
                    <tr>
                        <th class="col-3 p-1">Alamat</th>
                        <th class="col-1 p-1 text-end">:</th>
                        <th class="col-8 p-1"><?php echo $alamat ?></th>
                    </tr>
                    <tr>
                        <th class="col-3 p-1">Sebesar</th>
                        <th class="col-1 p-1 text-end">:</th>
                        <th class="col-8 p-1"><?php echo ucfirst($total_inv_terbilang) ?> Rupiah</th>
                    </tr>
                    <tr>
                        <th class="col-3 p-1">Untuk Pembayaran</th>
                        <th class="col-1 p-1 text-end">:</th>
                        <th class="col-8 p-1">No. Invoice : <?php echo $no_inv ?></th>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-1"></div>
                <div class="col-sm-3">
                    <div class="parallelogram">
                        <p style="margin: 0;">Jumlah : Rp. <?php echo number_format($total_inv,0,'.','.') ?></p>
                    </div>
                </div>
            <div class="col-sm-3"></div>
            <div class="col-sm-4">
                <?php  
                    $date_now = date('Y/m/d');
                ?>
                <p class="text-Left mt-1">
                    <b>Bekasi, <?php echo formatTanggalIndonesia($date_now, false) ?></b>
                </p>
                <br><br><br><br>
                <div class="container">
                    <div class="input-row">
                        <input type="text" class="print-no-placeholder" id="nama" placeholder="Silahkan Isi Nama">
                    </div>
                    <div class="separator"></div>
                    <div class="input-row">
                        <input type="text" class="print-no-placeholder" id="jabatan" placeholder="Silahkan Isi Jabatan">
                    </div>
                </div>
            </div>
            <div class="col-sm-1"></div>
        </div>
    </div>
    <?php include "page/script.php" ?>
</body>
</html>
<script>
    // Fungsi untuk menampilkan dialog pencetakan
    function showPrintDialog() {
        window.print();
    }

    // Menambahkan event listener ke tombol cetak
    document.getElementById('printButton').addEventListener('click', showPrintDialog);
</script>
<script>
    function adjustSeparator() {
        const namaInput = document.getElementById('nama');
        const jabatanInput = document.getElementById('jabatan');
        const separator = document.querySelector('.separator');

        // Create a temporary span to calculate text width
        const span = document.createElement('span');
        span.style.position = 'absolute';
        span.style.visibility = 'hidden';
        span.style.fontWeight = 'bold';
        document.body.appendChild(span);

        // Calculate the width of both inputs' text
        span.textContent = namaInput.value || namaInput.placeholder;
        const namaWidth = span.offsetWidth;

        span.textContent = jabatanInput.value || jabatanInput.placeholder;
        const jabatanWidth = span.offsetWidth;

        document.body.removeChild(span);

        // Set the separator width to the maximum of both widths
        separator.style.width = `${Math.max(namaWidth, jabatanWidth)}px`;
    }

    // Add event listeners to inputs to adjust separator width on input
    document.getElementById('nama').addEventListener('input', adjustSeparator);
    document.getElementById('jabatan').addEventListener('input', adjustSeparator);

    // Adjust separator width on page load
    window.addEventListener('load', adjustSeparator);
</script>