<?php 
    ob_start();
    require_once "akses.php";
    require_once "function/function-enkripsi.php";
    require_once "function/terbilang.php";
?>
<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>
    <link href="assets/img/logo-kma.png" rel="icon">
    <link href="assets/img/logo-kma.png" rel="apple-touch-icon">
    <link rel="stylesheet" href="assets/css/style-inv-ppn.css">
</head>

<body>
    <?php
    $id_inv = decrypt($_GET['id'], $key_global);
    $sql = "SELECT 
            ppn.*, 
            sr.id_user, sr.id_customer, sr.id_inv, sr.no_spk, sr.no_po, sr.tgl_pesanan,
            cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
            FROM inv_ppn AS ppn
            JOIN spk_reg sr ON (ppn.id_inv_ppn = sr.id_inv)
            JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
            JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
            JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
            WHERE ppn.id_inv_ppn = '$id_inv'";
    $query = mysqli_query($connect, $sql);
    $data = mysqli_fetch_array($query);
    $no_inv = $data['no_inv'];
    $kwitansi = $data['kwitansi'];
    $surat_jalan = $data['surat_jalan'];
    // Ubah Format Tanggal
    $bulan = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
    // Untuk tgl Invoice
    $dateString = $data['tgl_inv'];
    $dateParts = explode('/', $dateString);
    $day = $dateParts[0];
    $month = $dateParts[1];
    $year = $dateParts[2];
    $tgl_inv_format = $day . ' ' . $bulan[$month] . ' ' . $year;

    // Untuk tgl Tempo
    $dateStrinTempo = $data['tgl_tempo'];
    $dateParts = explode('/', $dateString);
    $day = $dateParts[0];
    $month = $dateParts[1];
    $year = $dateParts[2];

    $tgl_tempo_format = $day . ' ' . $bulan[$month] . ' ' . $year;

    ?>
    <div id="printButton">
        <div id="cetakInv" style="text-align: center;">
            <button id="print" class="print-button">
                <i class="fas fa-print"></i> Print
            </button>
            <button id="downloadPdf" class="print-button" style="background-color: #228B22;">
                <i class="far fa-file-pdf"></i> Download PDF
            </button>

            <?php
                if ($kwitansi == '1' || $surat_jalan == '1') {
                    if ($kwitansi == '1') {
                        ?>
                            <!-- Cetak Kwitansi -->
                            <button id="cetakKwitansi" class="print-button" style="background-color: #FF4500;">
                                    <i class="fas fa-file-invoice"></i>  Cetak Kwitansi
                            </button>
                        <?php
                    }
                    if ($surat_jalan == '1') {
                        ?>
                            <!-- Cetak Surat Jalan -->
                            <button id="cetakSuratJalan" class="print-button" style="background-color: #C71585;">
                                <i class="fas fa-newspaper"></i>  Cetak Surat Jalan
                            </button>
                        <?php
                    }
                }
            ?>
        </div>
    </div>
    <br>
    <div class="invoice" id="divInv">
        <h2 align='right'><strong>INVOICE</strong></h2>
        <div class="invoice-header">
            <div class="col-header-1">
                <!-- Kolom pertama -->
                <img src="assets/img/header-kma.jpg" class="img-header">
            </div>
            <div class="col-header-2">
                <!-- Kolom kedua -->
                <div class="col-ket-in-1">
                    Tgl. Invoice <br>
                    No. Invoice <br>
                    <?php
                    if (!empty($dateStringTempo)) {
                        echo "Tgl.Jatuh Tempo";
                    }
                    ?>
                </div>

                <div class="col-ket-in-2">
                    &nbsp;: <?php echo $tgl_inv_format ?> <br>
                    &nbsp;: <?php echo $data['no_inv'] ?> <br>
                    <?php
                    if (!empty($dateStringTempo)) {
                        $datePartsTempo = explode('/', $dateStringTempo);
                        $dayTempo = $datePartsTempo[0];
                        $monthTempo = $datePartsTempo[1];
                        $yearTempo = $datePartsTempo[2];

                        $tgl_tempo_format = $dayTempo . ' ' . $bulan[$monthTempo] . ' ' . $yearTempo;
                        echo "&nbsp;:" . $tgl_tempo_format;
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="invoice-header">
            <div class="col-header-1">
                <!-- Kolom pertama -->
                <?php
                $sql2 = "SELECT 
                ppn.*, 
                sr.id_user, sr.id_customer, sr.id_inv, sr.no_spk, sr.no_po, sr.tgl_pesanan,
                cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
                FROM inv_ppn AS ppn
                JOIN spk_reg sr ON (ppn.id_inv_ppn = sr.id_inv)
                JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                WHERE ppn.id_inv_ppn = '$id_inv' GROUP BY sr.no_po";
                $query2 = mysqli_query($connect, $sql2);
                $rowIndex = 0;
                $totalRows = mysqli_num_rows($query2);
                $dataCount = 0;
                $output = ''; // Variabel untuk menyimpan hasil output
                while ($data2 = mysqli_fetch_array($query2)) {
                    $dataCount++;
                    // Periksa jika nilai no_po tidak kosong
                    if (!empty($data2['no_po'])) {
                        // Tampilkan nilai kolom pada setiap baris
                        $output .= $data2['no_po'];

                        // Tambahkan koma jika bukan baris terakhir
                        if ($rowIndex < $totalRows - 1 && $dataCount < $totalRows) {
                            $output .= ', ';
                        }
                    }

                    $rowIndex++;
                }

                // Tambahkan tanda titik di akhir data
                if (!empty($output)) {
                    $output .= '.';

                    // Tampilkan hanya jika ada data yang ditampilkan
                    echo "<div class='invoice-header'><div class='col-header-3'>No.PO :  <br>"  . $output . "</div></div>";
                }
                ?>

            </div>
            <div class="col-header-2">
                <!-- Kolom kedua -->
                Kepada : <br>
                <?php echo $data['cs_inv'] ?> <br>
                <?php 
                    if($data['alamat_inv'] == ''){
                        echo $data['alamat'];
                    } else {
                        echo $data['alamat_inv']; 
                    }
                ?>
            </div>
        </div>
        <div class="invoice-body">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th style="width: 300px;">Nama Produk</th>
                        <!-- <th style="width: 100px;">No Batch</th> -->
                        <th style="width: 60px;">Qty</th>
                        <th style="width: 75px;">Harga</th>
                        <?php
                        if ($data['kategori_inv'] == 'Diskon') {
                            echo '<th style="width: 60px;">Disc</th>';
                        }
                        ?>
                        <th style="width: 90px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Detail produk -->
                    <?php
                    $year = date('y');
                    $day = date('d');
                    $month = date('m');
                    $no = 1;
                    $sub_total = 0;
                    $grand_total = 0;
                    $sql_trx = "SELECT
                                    ppn.id_inv_ppn,
                                    ppn.kategori_inv,
                                    ppn.note_inv,
                                    ppn.total_inv,
                                    spk.id_inv, 
                                    spk.no_spk,
                                    trx.id_transaksi,
                                    trx.id_produk,
                                    trx.nama_produk_spk,
                                    trx.harga,
                                    SUM(trx.qty) AS total_qty,
                                    trx.disc,
                                    SUM(trx.total_harga) AS total_harga,
                                    trx.status_trx,
                                    trx.created_date,
                                    COALESCE(tpr.nama_produk, tpsm.nama_set_marwa, tpe.nama_produk, tpse.nama_set_ecat) AS nama_produk,
                                    COALESCE(tpr.no_batch, tpsm.no_batch, tpe.no_batch, tpse.no_batch) AS no_batch, 
                                    COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                                    COALESCE(mr_produk.nama_merk, mr_set.nama_merk, mr_produk_ecat.nama_merk, mr_set_ecat.nama_merk) AS merk_produk
                                FROM inv_ppn AS ppn
                                LEFT JOIN spk_reg spk ON (ppn.id_inv_ppn = spk.id_inv)
                                LEFT JOIN transaksi_produk_reg trx ON trx.id_spk = spk.id_spk_reg
                                LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                                LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                                LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                LEFT JOIN tb_produk_ecat tpe ON trx.id_produk = tpe.id_produk_ecat
                                LEFT JOIN tb_produk_set_ecat tpse ON trx.id_produk = tpse.id_set_ecat
                                LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk reguler
                                LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                                WHERE ppn.id_inv_ppn = '$id_inv'
                                GROUP BY trx.id_produk, tpsm.nama_set_marwa, trx.nama_produk_spk, mr_set.nama_merk, mr_produk.nama_merk
                                ORDER BY trx.created_date ASC";
                    $trx_produk_reg = mysqli_query($connect, $sql_trx);
                    while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                        $id_produk = $data_trx['id_produk'];
                        $id_inv_update = $data_trx['id_inv_ppn'];
                        $total_inv = $data_trx['total_inv'];
                        $note_inv = $data_trx['note_inv'];
                        $kat_inv = $data_trx['kategori_inv'];
                        $satuan = $data_trx['satuan'];
                        $harga = $data_trx['harga'];
                        $qty = $data_trx['total_qty'];
                        $disc = $data_trx['disc'];
                        $total = $data_trx['total_harga'];
                        $sub_total += $total;
                        // Kondisi satuan
                        $satuan_produk = '';
                        $id_produk_substr = substr($id_produk, 0, 2);
                        if ($id_produk_substr == 'BR') {
                            $satuan_produk = $satuan;
                        } else {
                            $satuan_produk = 'Set';
                        }
                    ?>
                        <tr>
                            <td align="center"><?php echo $no; ?></td>
                            <td><?php echo $data_trx['nama_produk_spk'] ?></td>
                            <!-- <td align="center">No Batch</td> -->
                            <td align="right"> <?php echo number_format($data_trx['total_qty'], 0, '.', '') . ' ' . $satuan_produk; ?></td>
                            <td align="right"><?php echo number_format($data_trx['harga'], 0, '.', '.') ?></td>
                            <?php
                            if ($data_trx['kategori_inv'] == 'Diskon') {
                                echo '<td align="right">' . $disc . ' %</td>';
                            }
                            ?>
                            <td align="right"><?php echo number_format($total, 0, '.', '.') ?></td>
                        </tr>
                        <?php $no++ ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="invoice-payment">
            <?php
                $sql_inv = mysqli_query($connect, "SELECT id_inv_ppn, sp_disc, ongkir, total_inv, ppn_dpp, ppn, nominal_dpp FROM inv_ppn WHERE id_inv_ppn = '$id_inv'");
                $data_inv = mysqli_fetch_array($sql_inv);
                
                $ppn_dpp = ($data_inv['ppn_dpp'] == '') ? '11/12' : $data_inv['ppn_dpp']; 
                
                // Pastikan $ppn adalah angka
                $ppn = ($data_inv['ppn'] == 0) ? 12 : floatval($data_inv['ppn']);
                $sp_disc = floatval($data_inv['sp_disc']) / 100;
                $ongkir = floatval($data_inv['ongkir']);

                // Perhitungan Sub total - Spesial Discount
                $sub_total_spdisc = round(floatval($sub_total) * (1 - $sp_disc)); 
                $nominal_sp_disc = round(floatval($sub_total) * $sp_disc); 

                // Perhitungan DPP 
                if (strpos($ppn_dpp, '/') !== false) {
                    list($num, $den) = explode('/', $ppn_dpp);
                    $ppn_dpp = floatval($num) / floatval($den);
                } else {
                    $ppn_dpp = floatval($ppn_dpp);
                }
                $dpp = round(floatval($ppn_dpp) * floatval($sub_total_spdisc));

                // Mendapatkan nominal PPN DPP
                $nominal_ppn_dpp = floatval($sub_total_spdisc) - floatval($dpp);

                // Perhitungan Nominal PPN
                $nominal_ppn = round(floatval($dpp) * (floatval($ppn) / 100));

                // Perhitungan grand total
                $grand_total = floatval($sub_total_spdisc) + floatval($ongkir) + floatval($nominal_ppn);
            ?>
            <div class="col-payment-1">
                <!-- Kolom pertama -->
                Terbilang :<br>
                <?php echo terbilang($grand_total) ?> Rupiah

            </div>
            <div class="col-payment-2">
                <!-- Kolom kedua -->
                <div class="grand-total">
                    Sub total (Rp):<br>

                    <?php
                    if ($kat_inv == 'Spesial Diskon' && $sp_disc != 0) {
                        echo 'Spesial Diskon (' . $data_inv['sp_disc'] . '%):';
                        echo "<br>";
                    }
                    ?>
                    
                    DPP (Rp): <br>

                    PPN <?php echo $ppn ?>% :<br>
                    <?php
                    if ($ongkir != 0) {
                        echo "Ongkir (Rp):";
                        echo "<br>";
                    }
                    ?>
                    Grand Total (Rp):
                </div>
                <div class="amount">
                    <?php echo number_format($sub_total, 0, '.', '.') ?>
                    <br>
                    <?php
                    if ($kat_inv == 'Spesial Diskon' && $sp_disc != 0) {
                        echo number_format($nominal_sp_disc, 0, '.', '.');
                        echo "<br>";
                    }
                    ?>
                    <?php echo number_format($dpp, 0, '.', '.') ?>
                    <br>
                    <?php echo number_format($nominal_ppn, 0, '.', '.') ?>
                    <br>
                    <?php
                    if ($ongkir != 0) {
                        echo number_format($ongkir, 0, '.', '.');
                        echo "<br>";
                    }
                    ?>
                    <?php echo number_format($grand_total, 0, '.', '.') ?>
                </div>
            </div>
        </div>
        <!-- Tampilan Note -->
        <?php
        if ($note_inv != '') {
            echo "<div class='col-header-2'>";
            echo "Note : <br>";
            echo $note_inv;
            echo "</div>";
        }
        ?>

        <!-- Kode untuk update total harga -->
        <?php
            if ($data_inv['ppn_dpp'] == '' || $data_inv['nominal_dpp'] != $nominal_sp_disc || $data_inv['ppn'] == '' || $data_inv['total_inv'] != $grand_total) {
                $stmt = $connect->prepare("UPDATE inv_ppn SET sub_total = ?, total_inv = ?, nominal_spdisc = ?, ppn_dpp = '11/12', nominal_ppn_dpp = ?, nominal_dpp = ?, ppn = '12', total_ppn = ? WHERE id_inv_ppn = ?");
                $stmt->bind_param("iiiiiis", $sub_total, $grand_total, $nominal_sp_disc, $nominal_ppn_dpp, $dpp, $nominal_ppn, $id_inv);
                $stmt->execute();
                
                // if ($stmt->execute()) {
                //     echo "Data berhasil diperbarui.";
                // } else {
                //     echo "Error: " . $stmt->error;
                // }
                $stmt->close();
            }
        ?>
        <br>
        <div class="invoice-footer">
            <img src="assets/img/footer-invoice.jpg" class="img-footer">
        </div>
    </div>
</body>

</html>
<?php ob_end_flush(); ?>
<!-- jquery 3.6.3 -->
<script src="assets/js/jquery.min.js"></script>
<script>
    // Fungsi untuk menampilkan dialog pencetakan
    function showPrintDialog() {
        window.print();
    }

    // Menambahkan event listener ke tombol cetak
    document.getElementById('print').addEventListener('click', showPrintDialog);
</script>
<script src="assets/js/html2pdf.bundle.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    // Function to generate the PDF and download it automatically
    function generatePDF() {
        const element = document.querySelector('.invoice'); // Select the element to convert to PDF
        const options = {
            margin: [0, 0, 0, 0], // Set margin: [atas, kanan, bawah, kiri]
            filename: 'PPN_<?php echo $no_inv; ?>.pdf', // Set the filename automatically
            image: { type: 'jpeg', quality: 1 }, // Image quality
            html2canvas: { scale: 4 }, // High-resolution canvas rendering
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' } // PDF settings
        };

        // Generate and automatically save the PDF without user interaction
        html2pdf().set(options).from(element).save(); // Automatically saves with the given filename
    }

    // Add event listener to the button to trigger PDF download on click
    document.getElementById('downloadPdf').addEventListener('click', generatePDF);
</script>
<script>
    // Untuk href button kwitansi
    $('#cetakKwitansi').on('click', function(){
        window.location.href = 'cetak-kwitansi.php?id_inv=<?php echo encrypt($id_inv, $key_global) ?>';
    });
    // Untuk href button surat jalan
    $('#cetakSuratJalan').on('click', function(){
        window.location.href = 'cetak-surat-jalan.php?id_inv=<?php echo encrypt($id_inv, $key_global) ?>';
    });
</script>