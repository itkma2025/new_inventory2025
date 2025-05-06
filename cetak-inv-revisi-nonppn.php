<?php 
    require_once "akses.php";
    require_once "function/function-enkripsi.php";
?>
<!DOCTYPE html>
<html>

<head>
    <title>Invoice</title>
    <link href="assets/img/logo-kma.png" rel="icon">
    <link href="assets/img/logo-kma.png" rel="apple-touch-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style-inv-nonppn.css">
    <style>
        .print-button-new {
            display: inline-block;
            padding: 8px 16px; /* Sesuaikan dengan kebutuhan Anda */
            font-size: 14px; /* Sesuaikan dengan kebutuhan Anda */
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid #007bff; /* Sesuaikan dengan warna border yang diinginkan */
            color: #007bff; /* Sesuaikan dengan warna teks yang diinginkan */
            border-radius: 4px; /* Sesuaikan dengan kebutuhan Anda */
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .print-button-new:hover {
            background-color: #007bff; /* Sesuaikan dengan warna latar belakang yang diinginkan saat hover */
            color: #fff; /* Sesuaikan dengan warna teks yang diinginkan saat hover */
        }

        /* Style untuk elemen select */
        .form-select {
        display: block;
        width: 30%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        /* Style untuk option dalam select */
        .form-select option {
        color: #495057;
        background-color: #fff;
        }
    </style>
</head>
<body>
    <?php
    include "koneksi.php";
    $id_komplain = $_GET['id_komplain'];
    $id_inv = decrypt($_GET['id'], $key_spk);
    $sql = "SELECT 
            nonppn.id_inv_nonppn, nonppn.no_inv, nonppn.kategori_inv, nonppn.tgl_inv, cs_inv, tgl_tempo,
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
        <div style="text-align: center;">
            <button id="printButton" class="print-button-new">
                <i class="fas fa-print"></i> Print
            </button>

            <a href="detail-komplain-revisi-nonppn.php?id=<?php echo $id_komplain ?>" style="text-decoration: none;" class="print-button-new" id="printButton"><i class="fas fa-arrow-left"></i> Halaman Sebelumnya</a>
            <!-- Elemen Select -->
            <!-- Elemen Select -->
            <?php
                // Inisialisasi variabel $selectedOption
                $selectedOption = isset($_POST['selectedOption']) ? $_POST['selectedOption'] : '';
            ?>

            <form method="post" action="" id="printButton">
                <select class="form-select" name="selectedOption" onchange="this.form.submit()">
                    <option value="" <?php echo empty($selectedOption) ? 'selected' : ''; ?>>Pilih Nomor Invoice</option>
                    <?php 
                        $sql_revisi = mysqli_query($connect, "  SELECT 
                                                                    id_inv,
                                                                    no_inv
                                                                FROM (
                                                                    SELECT 
                                                                        ir.id_inv, 
                                                                        nonppn.no_inv AS no_inv
                                                                    FROM inv_revisi AS ir
                                                                    LEFT JOIN inv_nonppn nonppn ON ir.id_inv = nonppn.id_inv_nonppn
                                                                    WHERE ir.id_inv = '$id_inv'
                                                                    
                                                                    UNION
                                                                    
                                                                    SELECT 
                                                                        id_inv, 
                                                                        no_inv_revisi
                                                                    FROM inv_revisi
                                                                    WHERE id_inv = '$id_inv'
                                                                ) AS merged_result");
                                                                    while($data_inv_revisi = mysqli_fetch_array($sql_revisi)) {
                                                                        $no_inv = $data_inv_revisi['no_inv'];
                    ?>
                    <option value="<?php echo $no_inv ?>" <?php echo ($selectedOption == $no_inv) ? 'selected' : ''; ?>><?php echo $no_inv ?></option>
                    <?php } ?>
                </select>
            </form>

            <!-- PHP Code -->
            <?php 
                $sql_rev = mysqli_query($connect, "SELECT id_inv, no_inv_revisi, pelanggan_revisi, alamat_revisi FROM inv_revisi WHERE id_inv = '$id_inv' ORDER BY no_inv_revisi DESC LIMIT 1");
                $data_rev = mysqli_fetch_array($sql_rev);
                $total_data = mysqli_num_rows($sql_rev);
                // Inisialisasi $no_inv
                $no_inv = "";
                if($total_data == 0){
                    $no_inv = $no_inv;
                } else {
                    $no_inv = $data_rev['no_inv_revisi'];
                }

                // Periksa apakah ada data yang dikirimkan dari formulir
                if (isset($_POST['selectedOption'])) {
                $no_inv = $_POST['selectedOption'];
                }
            ?>
        </div>
        <h2 align='right'><strong>INVOICE</strong></h2>
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
                    &nbsp;: <?php echo $no_inv ?> <br>
                    &nbsp;: <?php echo $tgl_inv_format ?> <br>
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
            <div class="col-header-2">
                <!-- Kolom kedua -->
                Kepada : <br>
                <?php echo $data_rev['pelanggan_revisi'] ?> <br>
                <?php echo $data_rev['alamat_revisi'] ?>
            </div>
        </div>
        <!-- Kolom kedua -->
        <?php
        $query2 = mysqli_query($connect, $sql);
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
                        <th style="width: 300px;">Nama Produk</th>
                        <th style="width: 40px;">Qty</th>
                        <th style="width: 60px;">Harga</th>
                        <?php
                        if ($data['kategori_inv'] == 'Diskon') {
                            echo '<th style="width: 60px;">Disc</th>';
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
                    $no = 1;
                    $grand_total = 0;
                    $sub_total_spdisc = 0;
                    $sql_trx = "SELECT
                                    nonppn.id_inv_nonppn,
                                    nonppn.kategori_inv,
                                    nonppn.sp_disc,
                                    nonppn.note_inv,
                                    nonppn.total_inv,
                                    trx.id_produk,
                                    trx.nama_produk AS nama_produk_rev,
                                    trx.harga,
                                    SUM(trx.qty) AS total_qty,
                                    trx.disc,
                                    trx.total_harga,
                                    trx.status_br_refund,
                                    trx.created_date,
                                    tpr.nama_produk,
                                    tpr.satuan
                                FROM inv_nonppn AS nonppn
                                LEFT JOIN inv_komplain ik ON nonppn.id_inv_nonppn = ik.id_inv
                                LEFT JOIN tmp_produk_komplain trx ON trx.id_inv = nonppn.id_inv_nonppn
                                LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                                LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                                WHERE nonppn.id_inv_nonppn = '$id_inv' AND trx.status_br_refund = '0'
                                GROUP BY trx.id_produk
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
                        $sub_total = round($total * $qty);
                        $sub_total_fix = round($sub_total - $sub_total_spdisc);
                        $grand_total += round($sub_total_fix);
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
                            <td><?php echo $data_trx['nama_produk_rev'] ?></td>
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
            $sql_inv = mysqli_query($connect, "SELECT 
                                                    nonppn.id_inv_nonppn, 
                                                    nonppn.sp_disc,
                                                    kmpl.id_komplain, 
                                                    rsk.ongkir 
                                                FROM 
                                                    inv_nonppn AS nonppn
                                                LEFT JOIN inv_komplain kmpl ON (nonppn.id_inv_nonppn = kmpl.id_inv)
                                                LEFT JOIN revisi_status_kirim rsk ON (kmpl.id_komplain = rsk.id_komplain)
                                                WHERE nonppn.id_inv_nonppn = '$id_inv'");
            $data_inv = mysqli_fetch_array($sql_inv);
            $sp_disc = $data_inv['sp_disc'] / 100;
            $ongkir = $data_inv['ongkir'];
            $sub_total_spdisc = $grand_total * $sp_disc;
            $grand_total_fix = round($grand_total - $sub_total_spdisc + $ongkir);
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
            mysqli_query($connect, "UPDATE inv_nonppn SET total_inv = '$grand_total_fix' WHERE id_inv_nonppn = '$id_inv'");
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


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js" integrity="sha512-OpVm6PQGWfDksMIZ0iigU4S+hRC3MM9J90Yij1EC5Bp7ZoABK/zZxttCzA3iTL4vYNY41FqCMLaE0WvjgiywFQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        // Function to generate the PDF
        function generatePDF() {
            const element = document.querySelector('.invoice');
            const options = {
                margin: 0.5,
                filename: 'invoice.pdf',
                image: { type: 'jpeg', quality: 1 },
                html2canvas: { scale: 4 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(options).from(element).save();
        }

        // Call the generatePDF function when the page is loaded
        window.addEventListener('load', generatePDF);
    </script>

<script>
    // Fungsi untuk menampilkan dialog pencetakan
    function showPrintDialog() {
        window.print();
    }

    // Menambahkan event listener ke tombol cetak
    document.getElementById('printButton').addEventListener('click', showPrintDialog);
</script>

<script>
    // Fungsi untuk mengupdate nilai $no_inv saat pilihan dipilih
    function updateNoInv() {
      var selectedOption = document.getElementById('selectOption').value;

      // Kirim data ke PHP menggunakan AJAX
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'cetak-inv-revisi-nonppn.php', true);
      xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
          // Tanggapi dari server (jika diperlukan)
        }
      };
      xhr.send('selectedOption=' + selectedOption);
    }
  </script>

</body>
</html>





