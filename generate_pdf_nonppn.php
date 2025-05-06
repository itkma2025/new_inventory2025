<?php include "akses.php" ?>
<?php require_once "function/function-enkripsi.php"; ?>
<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>
    <link href="assets/img/logo-kma.png" rel="icon">
    <link href="assets/img/logo-kma.png" rel="apple-touch-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-generate-nonppn.css">
</head>

<body>
    <?php
    $id_inv = decrypt($_GET['id'], $key_global);
    $sql = "SELECT 
            nonppn.id_inv_nonppn, nonppn.no_inv, nonppn.tgl_inv, nonppn.tgl_tempo, nonppn.kategori_inv,
            sr.id_user, sr.id_customer, sr.id_inv, sr.no_spk, sr.no_po, sr.tgl_pesanan,
            cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
            FROM inv_nonppn AS nonppn
            JOIN spk_reg sr ON (nonppn.id_inv_nonppn = sr.id_inv)
            JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
            JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby) 
            JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
            WHERE nonppn.id_inv_nonppn = '$id_inv'";
    $query = mysqli_query($connect, $sql);
    $data = mysqli_fetch_array($query);
    $no_inv = $data['no_inv'];
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
                nonppn.*, 
                sr.id_user, sr.id_customer, sr.id_inv, sr.no_spk, sr.no_po, sr.tgl_pesanan,
                cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
                FROM inv_nonppn AS nonppn
                JOIN spk_reg sr ON (nonppn.id_inv_nonppn = sr.id_inv)
                JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                WHERE nonppn.id_inv_nonppn = '$id_inv'";
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
                        <th style="width: 350px;">Nama Produk</th>
                        <th style="width: 60px;">Qty</th>
                        <th style="width: 75px;">Harga</th>
                        <?php
                        if ($data['kategori_inv'] == 'Diskon') {
                            echo '<th style="width: 60px;">Disc</th>';
                        }
                        ?>
                        <th style="width: 85px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Detail produk -->
                    <?php
                    $year = date('y');
                    $day = date('d');
                    $month = date('m');
                    $no = 1;
                    $grand_total = 0;
                    $sub_total_spdisc = 0;
                    $sql_trx = "SELECT
                                    nonppn.id_inv_nonppn,
                                    nonppn.kategori_inv,
                                    nonppn.sp_disc,
                                    nonppn.note_inv,
                                    nonppn.total_inv,
                                    spk.id_inv, 
                                    spk.no_spk,
                                    trx.id_transaksi,
                                    trx.id_produk,
                                    trx.nama_produk_spk,
                                    trx.harga,
                                    SUM(trx.qty) AS total_qty,
                                    trx.disc,
                                    trx.total_harga,
                                    trx.status_trx,
                                    trx.created_date,
                                    COALESCE(tpr.nama_produk, tpsm.nama_set_marwa, tpe.nama_produk, tpse.nama_set_ecat) AS nama_produk, 
                                    COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                                    COALESCE(mr_produk.nama_merk, mr_set.nama_merk, mr_produk_ecat.nama_merk, mr_set_ecat.nama_merk) AS merk_produk
                                FROM inv_nonppn AS nonppn
                                LEFT JOIN spk_reg spk ON (nonppn.id_inv_nonppn = spk.id_inv)
                                LEFT JOIN transaksi_produk_reg trx ON trx.id_spk = spk.id_spk_reg
                                LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                                LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                                LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                LEFT JOIN tb_produk_ecat tpe ON trx.id_produk = tpe.id_produk_ecat
                                LEFT JOIN tb_produk_set_ecat tpse ON trx.id_produk = tpse.id_set_ecat
                                LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk reguler
                                LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                                WHERE nonppn.id_inv_nonppn = '$id_inv'
                                GROUP BY trx.id_produk, tpsm.nama_set_marwa, trx.nama_produk_spk, mr_set.nama_merk, mr_produk.nama_merk
                                ORDER BY trx.created_date ASC";
                    $trx_produk_reg = mysqli_query($connect, $sql_trx);
                    while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                        $id_inv_update = $data_trx['id_inv_nonppn'];
                        $total_inv = $data_trx['total_inv'];
                        $note_inv = $data_trx['note_inv'];
                        $kat_inv = $data_trx['kategori_inv'];
                        $qty = $data_trx['total_qty'];
                        $harga = $data_trx['harga'];
                        $satuan = $data_trx['satuan'];
                        $disc = $data_trx['disc'] / 100;
                        $tampil_disc = $data_trx['disc'];
                        $tampil_spdisc = $data_trx['sp_disc'];
                        $harga_disc = $harga * $disc;
                        $total = $harga - $harga_disc;
                        $sub_total = ceil($total * $qty);
                        $sub_total_fix = ceil($sub_total - $sub_total_spdisc);
                        $grand_total += ceil($sub_total_fix);
                        $id_produk = $data_trx['id_produk'];
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
                            <td align="right"> <?php echo number_format($data_trx['total_qty'], 0, '.', '') . ' ' . $satuan_produk; ?></td>
                            <td align="right"><?php echo number_format($data_trx['harga'], 0, '.', '.') ?></td>
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
            $sql_inv = mysqli_query($connect, "SELECT id_inv_nonppn, sp_disc, ongkir FROM inv_nonppn WHERE id_inv_nonppn = '$id_inv'");
            $data_inv = mysqli_fetch_array($sql_inv);
            $sp_disc = $data_inv['sp_disc'] / 100;
            $ongkir = $data_inv['ongkir'];
            $sub_total_spdisc = $grand_total * $sp_disc;
            $grand_total_fix = ceil($grand_total - $sub_total_spdisc + $ongkir);
            ?>
            <div class="col-payment-1">
                <!-- Kolom pertama -->
                Terbilang :<br>
                <?php echo terbilang($grand_total_fix) ?> Rupiah

            </div>
            <div class="col-payment-2">
                <!-- Kolom kedua -->
                <div class="grand-total">

                    <?php
                    if ($kat_inv == 'Spesial Diskon' && $sp_disc != 0) {
                        echo 'Spesial Diskon &nbsp;' . $data_inv['sp_disc'] . '%:';
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
                        echo number_format($sub_total_spdisc, 0, '.', '.');
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
            mysqli_query($connect, "UPDATE inv_nonppn SET total_inv = '$grand_total_fix' WHERE id_inv_nonppn = '$id_inv'");
        }

        ?>
        <div class="invoice-footer">
            <div class="col1">
                <!-- Kolom pertama -->
                <p>Mengetahui:</p>
                <br>
                <p>H. Lasino</p>
            </div>
            <div class="col2">
                <!-- Kolom kedua -->
                <p>Disetujui oleh:</p>
                <br>
                <p>Lisa Ayu F</p>
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
    // Function to generate the PDF and download it automatically without filename popup
    function generatePDF() {
        const element = document.querySelector('.invoice'); // Select the element to convert to PDF
        const options = {
            margin: 0,
            filename: 'NONPPN_<?php echo $no_inv; ?>.pdf', // Set the filename automatically
            image: { type: 'jpeg', quality: 1 }, // Image quality
            html2canvas: { scale: 4 }, // High-resolution canvas rendering
            jsPDF: { unit: 'mm', format: 'letter', orientation: 'portrait' } // PDF settings
        };

        // Generate and automatically save the PDF without user interaction
        html2pdf().set(options).from(element).save(); // Automatically saves with the given filename
    }

    // Call the generatePDF function when the page is fully loaded
    window.addEventListener('load', generatePDF);
</script>