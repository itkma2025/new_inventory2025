<?php 
    require_once "akses.php";
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" crossorigin="anonymous">
    <?php include "page/head.php"; ?>
    <style>
        body{
            width: 1000px;
            margin: 0 auto;
            margin-top: 20px;
            background-color: white;
            font-family: "PT Sans", sans-serif;
            color: black;
            letter-spacing: 0.5px;
        }
        .table-custom{
            border-collapse: collapse;
            width: 100%;
            font-size: 15px;
            color: black !important;
        }

        .p1-custom{
            padding: 2px !important;
        }
        .no-border {
            border: none;
            outline: none; /* Menghilangkan outline saat input aktif */
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
            $id_inv = decrypt($_GET['id_inv'],$key_global);
            $id_inv_substr = substr($id_inv, 0, 3);
            $sql_surat_jalan = " SELECT
                                    sk.tgl_kirim, 
                                    COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                                    COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
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
            <div class="col-sm-4 border border-dark p-2">
                <div class="text-center" style="font-size: 20px; text-decoration: underline;">
                    <b>SURAT JALAN</b><br>
                </div>
                <div style="font-size: 16px;">No. Invoice : <?php echo $no_inv ?></div>
                <div style="font-size: 16px;">Tanggal Kirim : <?php echo $tgl_kirim ?></div>
            </div>
        </div>
        <div class="row p-3">
            <div class="col-sm-7" style="font-size: 16px;">
                <b>Kepada :</b><br>
                <b><?php echo $cs_inv ?></b><br>
                <b>
                    <?php echo $alamat ?>		
                </b>
            </div>
            <div class="col-sm-1"></div>
            <div class="col-sm-4">
                <?php  
                    if ($no_po != ''){
                        ?>
                            <p class="border border-dark p-2 text-center" style="font-size: 16px;">
                                <b>No.PO : <?php echo $no_po ?></b>
                            </p>
                        <?php
                    }
                ?>
            </div>
        </div>
        <table class="table-custom table-bordered border-dark">
            <thead>
                <tr>
                    <th class="text-center p-2">No</th>
                    <th class="text-center p-2">Nama Produk</th>
                    <th class="text-center p-2">Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $no = 1;
                    $sql_trx = "SELECT 
                                    COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                                    trx.id_transaksi,
                                    trx.id_produk,
                                    trx.nama_produk_spk,
                                    trx.qty,
                                    trx.status_trx,
                                    trx.created_date,
                                    COALESCE(tpr.nama_produk, tpsm.nama_set_marwa) AS nama_produk,
                                    tpr.satuan
                                FROM spk_reg AS spk
                                LEFT JOIN inv_nonppn nonppn ON spk.id_inv = nonppn.id_inv_nonppn
                                LEFT JOIN inv_ppn ppn ON spk.id_inv = ppn.id_inv_ppn
                                LEFT JOIN inv_bum bum ON spk.id_inv = bum.id_inv_bum
                                LEFT JOIN transaksi_produk_reg trx ON trx.id_spk = spk.id_spk_reg
                                LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                                LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                                WHERE nonppn.id_inv_nonppn = '$id_inv' OR ppn.id_inv_ppn = '$id_inv' OR bum.id_inv_bum = '$id_inv' AND status_trx = '1' ORDER BY trx.created_date ASC";
                    $trx_produk_reg = mysqli_query($connect, $sql_trx);
                    while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                        $id_produk = $data_trx['id_produk'];
                        $satuan = $data_trx['satuan'];
                        $id_produk_substr = substr($id_produk, 0, 2);
                        if ($id_produk_substr == 'BR') {
                            $satuan_produk = $satuan;
                        } else {
                            $satuan_produk = 'Set';
                        }
                ?>
                <tr>
                    <td class="text-center p1-custom"><?php echo $no ?></td>
                    <td class="p1-custom"><?php echo $data_trx['nama_produk_spk'] ?></td>
                    <td class="text-center p1-custom"><?php echo $data_trx['qty']. '&nbsp;' .$satuan_produk. '' ?></td>
                </tr>
                <?php $no++ ?>
                <?php } ?>
            </tbody>
        </table>
        <br>
        <b style="font-size: 17px;">Keterangan :</b>
        <div class="col-sm-12 border border-dark p-1" style="font-size: 16px;">
            1. Barang tersebut diatas telah diterima dalam keadaan baik dan bagus. <br>
            2. Barang tersebut di atas apabila  dikembalikan/retur dalam keadaan baik  dan berfungsi dalam waktu 7 hari terhitung dari tanggal penerimaan barang. 
        </div>

        <div class="row mt-4">
            <div class="col-sm-1"></div>
            <div class="col-sm-3 border border-dark">
                <p class="text-center mt-1">
                    <b>Disetujui Oleh :</b>
                </p>
                <br><br>
                <div class="text-center mb-1">
                    <input type="text" class="text-center no-border print-no-placeholder" placeholder="Silahkan isi nama">
                </div>
            </div>
            <div class="col-sm-4"></div>
            <div class="col-sm-3 border border-dark">
                <p class="text-center mt-1">
                    <b>Diterima Oleh :</b>
                </p>
                <br><br>
                <p class="text-center">
                    
                </p>
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