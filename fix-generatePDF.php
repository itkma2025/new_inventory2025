<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>
    <link href="assets/img/logo-kma.png" rel="icon">
    <link href="assets/img/logo-kma.png" rel="apple-touch-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Lato', sans-serif;
            letter-spacing: 0.8px;
        }

        .invoice {
            width: 85%;
            max-width: 800px;
            margin: 0 auto;
            padding-top: 0.3cm;
            padding-bottom: 0.3cm;
            padding-left: 1.5cm;
            padding-right: 1.5cm;
            background-color: #ffffff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            font-size: 14.5px;
        }

        .invoice-header {
            text-align: left;
            display: grid;
            margin-bottom: 0.5cm;
            grid-template-columns: 3fr 1fr 1fr 5fr;
            grid-gap: 0.5cm;
        }

        .invoice-header h1 {
            font-size: 20px;
            margin: 0;
        }

        .col-header-1 {
            grid-column: 1 / span 3;
            padding: 0.1cm;
            display: flex;
            justify-content: left;
            align-items: flex-start;
        }

        .col-header-2 {
            grid-column: 4;
            border: 1px solid Black;
            padding: 0.1cm;
            text-align: left;
            align-self: flex-end;
        }


        .col-header-3 {
            grid-column: 1 / span 4;
            border: 1px solid Black;
            padding: 0.1cm;
            text-align: left;
            align-self: flex-start;
        }

        .ket-inv-1 {
            grid-column: 2;
            border: 1px solid Black;
            padding: 0.1cm;
            display: flex;
            align-items: flex-start;
        }

        .ket-inv-2 {
            grid-column: 2;
            border: 1px solid Black;
            padding: 0.1cm;
            display: flex;
            align-items: flex-start;
        }

        .invoice-body {
            margin-bottom: 0.2cm;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-table th,
        .invoice-table td {
            padding: 0.1cm;
            border: 1px solid Black;
            font-size: 14.5px;
        }

        .invoice-payment {
            text-align: left;
            display: grid;
            margin-top: 0.5cm;
            grid-template-columns: 3fr 1fr 1fr 4fr;
            grid-gap: 0.5cm;
        }

        .col-payment-1 {
            grid-column: 1 / span 3;
            padding: 0.1cm;
            text-align: left;
            align-self: flex-start;
        }

        .col-payment-2 {
            grid-column: 4;
            display: flex;
            padding: 0.1cm;
            justify-content: space-between;
        }

        .grand-total {
            text-align: right;
        }

        .amount {
            text-align: right;
        }

        .invoice-footer {
            font-weight: bold;
            text-align: center;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 2fr;
            grid-gap: 0.5cm;
        }

        .col1 {
            grid-column: 1;
        }

        .col2 {
            grid-column: 2;
        }

        .col3 {
            grid-column: 3;
        }

        .col4 {
            grid-column: 4;
            text-align: left;
        }

        @media print {
            @page {
                size: letter;
                margin: 0;
            }

            body {
                margin: 1cm; /* Add margin to avoid content being too close to the edge */
            }

            .invoice {
                page-break-inside: avoid; /* Prevent the invoice from being split across pages */
            }
        }
    </style>
</head>

<body>
    <?php
    include "koneksi.php";
    $id_inv = base64_decode($_GET['id']);
    $sql = "SELECT 
            bum.*, 
            sr.id_user, sr.id_customer, sr.id_inv, sr.no_spk, sr.no_po, sr.tgl_pesanan,
            cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
            FROM inv_bum AS bum
            JOIN spk_reg sr ON (bum.id_inv_bum = sr.id_inv)
            JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
            JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
            JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
            WHERE bum.id_inv_bum = '$id_inv'";
    $query = mysqli_query($connect, $sql);
    $data = mysqli_fetch_array($query);
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
    $dateStringTempo = $data['tgl_tempo'];
    ?>
    <div class="invoice">
        <h3 align='right'><strong>INVOICE</strong></h3>
        <div class="invoice-header">
            <div class="col-header-1">
                <!-- Kolom pertama -->
                <div class="ket-in-1">
                    No. Invoice <br>
                    Tgl. Invoice <br>
                    <?php
                    if (!empty($dateStringTempo)) {
                        echo "Tgl.Jatuh Tempo";
                    }
                    ?>
                </div>

                <div class="ket-in-2">
                    &nbsp;: <?php echo $data['no_inv'] ?> <br>
                    &nbsp;: <?php echo $tgl_inv_format ?> <br>
                    <?php
                    if (!empty($dateStringTempo)) {
                        $datePartsTempo = explode('/', $dateStringTempo);
                        $dayTempo = $datePartsTempo[0];
                        $monthTempo = $datePartsTempo[1];
                        $yearTempo = $datePartsTempo[2];

                        $tgl_tempo_format = $dayTempo . ' ' . $bulan[$monthTempo] . ' ' . $yearTempo;
                        echo "&nbsp;: " . $tgl_tempo_format;
                    }
                    ?>
                </div>
            </div>
            <div class="col-header-2">
                <!-- Kolom kedua -->
                Kepada : <br>
                <?php echo $data['nama_cs'] ?> <br>
                <?php echo $data['alamat'] ?>
            </div>
        </div>
        <!-- Kolom kedua -->
        <?php
        $sql2 = "SELECT 
                bum.*, 
                sr.id_user, sr.id_customer, sr.id_inv, sr.no_spk, sr.no_po, sr.tgl_pesanan,
                cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
                FROM inv_bum AS bum
                JOIN spk_reg sr ON (bum.id_inv_bum = sr.id_inv)
                JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                WHERE bum.id_inv_bum = '$id_inv'";
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

        <div class="invoice-body">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th style="width: 200px;">Nama Produk</th>
                        <th style="width: 40px;">Qty</th>
                        <th style="width: 80px;">Harga</th>
                        <?php
                        if ($data['kategori_inv'] == 'Diskon') {
                            echo '<th style="width: 40px;">Disc</th>';
                        }
                        ?>
                        <th style="width: 80px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Detail produk -->
                    <?php
                    include "koneksi.php";
                    $year = date('y');
                    $day = date('d');
                    $month = date('m');
                    $id_bum_decode = base64_decode($_GET['id']);
                    $no = 1;
                    $grand_total = 0;
                    $sub_total_spdisc = 0;
                    $sql_trx = "SELECT
                                    bum.id_inv_bum, bum.kategori_inv, bum.sp_disc, bum.note_inv, bum.total_inv,
                                    sr.id_inv, sr.no_spk,
                                    SUM(trx.qty) AS total_qty,
                                    trx.*,
                                    spr.stock,
                                    tpr.nama_produk,
                                    tpr.harga_produk, mr.*
                                FROM inv_bum AS bum
                                JOIN spk_reg sr ON (bum.id_inv_bum = sr.id_inv)
                                JOIN transaksi_produk_reg trx ON(sr.id_spk_reg = trx.id_spk)
                                JOIN stock_produk_reguler spr ON(trx.id_produk = spr.id_produk_reg)
                                JOIN tb_produk_reguler tpr ON(trx.id_produk = tpr.id_produk_reg)
                                JOIN tb_merk mr ON (tpr.id_merk = mr.id_merk)
                                WHERE bum.id_inv_bum = '$id_bum_decode' AND disc = '0'
                                GROUP BY id_produk
                            
                                UNION ALL
                            
                                SELECT
                                    bum.id_inv_bum, bum.kategori_inv, bum.sp_disc, bum.note_inv, bum.total_inv,
                                    sr.id_inv, sr.no_spk,
                                    trx.qty AS total_qty,
                                    trx.*,
                                    spr.stock,
                                    tpr.nama_produk,
                                    tpr.harga_produk, mr.*
                                FROM inv_bum AS bum
                                JOIN spk_reg sr ON (bum.id_inv_bum = sr.id_inv)
                                JOIN transaksi_produk_reg trx ON(sr.id_spk_reg = trx.id_spk)
                                JOIN stock_produk_reguler spr ON(trx.id_produk = spr.id_produk_reg)
                                JOIN tb_produk_reguler tpr ON(trx.id_produk = tpr.id_produk_reg)
                                JOIN tb_merk mr ON (tpr.id_merk = mr.id_merk)
                                WHERE bum.id_inv_bum = '$id_bum_decode' AND disc != '0'
                                ORDER BY no_spk ASC";
                    $trx_produk_reg = mysqli_query($connect, $sql_trx);
                    while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                        $id_inv_update = $data_trx['id_inv_bum'];
                        $total_inv = $data_trx['total_inv'];
                        $note_inv = $data_trx['note_inv'];
                        $kat_inv = $data_trx['kategori_inv'];
                        $qty = $data_trx['total_qty'];
                        $harga = $data_trx['harga_produk'];
                        $disc = $data_trx['disc'] / 100;
                        $tampil_disc = $data_trx['disc'];
                        $tampil_spdisc = $data_trx['sp_disc'];
                        $harga_disc = $harga * $disc;
                        $total = $harga - $harga_disc;
                        $sub_total = floor($total * $qty);
                        $sub_total_fix = floor($sub_total - $sub_total_spdisc);
                        $grand_total += floor($sub_total_fix);
                    ?>
                        <tr>
                            <td align="center"><?php echo $no; ?></td>
                            <td><?php echo $data_trx['nama_produk'] ?></td>
                            <td align="right"><?php echo number_format($data_trx['total_qty'], 0, '.', '.') ?></td>
                            <td align="right"><?php echo number_format($data_trx['harga_produk'], 0, '.', '.') ?></td>
                            <?php
                            if ($data_trx['kategori_inv'] == 'Diskon') {
                                echo '<td align="right">' . $tampil_disc . ' %</td>';
                            }
                            ?>
                            <td align="right"><?php echo number_format($sub_total_fix, 0, '.', '.') ?></td>
                        </tr>
                        <?php $no++ ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="invoice-payment">
            <?php
            $sql_inv = mysqli_query($connect, "SELECT id_inv_bum, sp_disc, ongkir FROM inv_bum WHERE id_inv_bum = '$id_bum_decode'");
            $data_inv = mysqli_fetch_array($sql_inv);
            $sp_disc = $data_inv['sp_disc'] / 100;
            $ongkir = $data_inv['ongkir'];
            $sub_total_spdisc = $grand_total * $sp_disc;
            $grand_total_fix = $grand_total - $sub_total_spdisc + $ongkir;
            ?>
            <div class="col-payment-1">
                <!-- Kolom pertama -->
                Terbilang :<br>
                <?php echo terbilang($grand_total_fix) ?>

            </div>
            <div class="col-payment-2">
                <!-- Kolom kedua -->
                <div class="grand-total">

                    <?php
                    if ($kat_inv == 'Spesial Diskon' && $sp_disc != 0) {
                        echo 'Spesial Diskon :';
                        echo "<br>";
                    }
                    ?>

                    <?php
                    if ($ongkir != 0) {
                        echo "Ongkir (Rp):";
                        echo "<br>";
                    }
                    ?>

                    Grand total (Rp):
                </div>
                <div class="amount">

                    <?php
                    if ($kat_inv == 'Spesial Diskon' && $sp_disc != 0) {
                        echo $data_inv['sp_disc'] . '(%)';
                        echo "<br>";
                    }
                    ?>

                    <?php
                    if ($ongkir != 0) {
                        echo number_format($ongkir, 0, '.', '.');
                        echo "<br>";
                    }
                    ?>

                    <?php echo number_format($grand_total_fix, 0, '.', '.') ?>
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
        if ($total_inv != $grand_total_fix) {
            mysqli_query($connect, "UPDATE inv_bum SET total_inv = '$grand_total_fix' WHERE id_inv_bum = '$id_inv'");
        }

        ?>
        <div class="invoice-footer">
            <div class="col1">
                <!-- Kolom pertama -->
                <p>Disetujui oleh:</p>
                <br>
                <p>_____________</p>
            </div>
            <div class="col2">
                <!-- Kolom kedua -->
                <p>Diantar oleh:</p>
                <br>
                <p>_____________</p>
            </div>
            <div class="col3">
                <!-- Kolom ketiga -->
                <p>Diterima oleh:</p>
                <br>
                <p>_____________</p>
            </div>
            <div class="col4">
                <!-- Kolom keempat -->
                <p></p>
                METODE PEMBAYARAN :<br>
                TRANSFER BANK BCA <br>
                NO. REK : 521 134 7105 <br>
                ATAS NAMA : LASINO <br>
            </div>
        </div>
    </div>
</body>

</html>

<?php
function penyebut($nilai)
{
    $nilai = abs($nilai);
    $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " " . $huruf[$nilai];
    } else if ($nilai < 20) {
        $temp = penyebut($nilai - 10) . " Belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai / 10) . " Puluh" . penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " Seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai / 100) . " Ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai / 1000) . " Ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai / 1000000) . " Juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai / 1000000000) . " Milyar" . penyebut(fmod($nilai, 1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = penyebut($nilai / 1000000000000) . " Trilyun" . penyebut(fmod($nilai, 1000000000000));
    }
    return $temp;
}

function terbilang($nilai)
{
    if ($nilai < 0) {
        $hasil = "minus " . trim(penyebut($nilai));
    } else {
        $hasil = trim(penyebut($nilai));
    }
    return $hasil;
}
?>


<script src="assets/js/html2pdf.bundle.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    // Function to generate the PDF
    function generatePDF() {
        const element = document.querySelector('.invoice');
        const options = {
            margin: 0,
            filename: `D:/folder_tujuan/invoice_${Date.now()}.pdf`,
            image: { type: 'jpeg', quality: 1 },
            html2canvas: { scale: 4 },
            jsPDF: { unit: 'mm', format: 'letter', orientation: 'portrait' }
        };

        html2pdf().set(options).from(element).save();
    }

    // Call the generatePDF function when the page is loaded
    window.addEventListener('load', generatePDF);
</script>
