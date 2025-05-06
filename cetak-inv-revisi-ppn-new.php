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
    <link rel="stylesheet" href="assets/css/style-inv-ppn.css">
    <style>
          .center-container {
            margin-top: 15px;
            text-align: center;
        }

        .centered-div {
            width: 30%;
            margin: 0 auto; /* Ini membuat elemen berada di tengah dengan margin otomatis */
        }
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
        width: 100%;
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

        .button {
            background-color: #04AA6D; /* Green */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            transition-duration: 0.4s;
            cursor: pointer;
        }

        .button-outline {
            background-color: white; 
            color: black; 
            border: 2px solid #008CBA;
        }

        .button-outline:hover {
            background-color: #008CBA;
            color: white;
        }

        .button-outline.active {
            background-color: #008CBA;
            color: white;
        }
    </style>
</head>

<body>
    <div style="text-align: center; margin-top: 20px; margin-bottom:20px;" id="printButton">
        <a style="text-decoration: none;" href="cetak-inv-revisi-ppn-old.php?id=<?php echo urlencode($_GET['id']) ?>&&id_komplain=<?php echo urlencode($_GET['id_komplain'])?>">
            <button class="button button-outline" id="ppn-old">
                <i class="fas fa-file"></i> PPN Lama
            </button>
        </a>
        <a style="text-decoration: none;" href="#">
            <button class="button button-outline active" id="ppn-new">
                <i class="fas fa-file"></i> PPN Baru
            </button>
        </a>
        
    </div>

    <?php
    include "koneksi.php";
    $id_inv = decrypt($_GET['id'], $key_spk);
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
        <div style="text-align: center;">
            <button id="print" class="print-button">
                <i class="fas fa-print"></i> Print
            </button>

            <a href="detail-komplain-revisi-ppn.php?id=<?php echo $_GET['id_komplain'] ?>" style="text-decoration: none;" class="print-button-new" id="printButton"><i class="fas fa-arrow-left"></i> Halaman Sebelumnya</a>
        </div>
        <div class="center-container">
            <div class="centered-div">
                <?php
                    // Inisialisasi variabel $selectedOption
                    $selectedOption = isset($_POST['selectedOption']) ? $_POST['selectedOption'] : '';
                ?>
                <form method="post" action="" id="printButton">
                    <select class="form-select" name="selectedOption" onchange="this.form.submit()" style="text-align: center;">
                        <option value="" <?php echo empty($selectedOption) ? 'selected' : ''; ?>>Pilih Nomor Invoice</option>
                        <?php 
                            $sql_revisi = mysqli_query($connect, "  SELECT 
                                                                        id_inv,
                                                                        no_inv
                                                                    FROM (
                                                                        SELECT 
                                                                            ir.id_inv, 
                                                                           ppn.no_inv AS no_inv
                                                                        FROM inv_revisi AS ir
                                                                        LEFT JOIN inv_ppn ppn ON ir.id_inv = ppn.id_inv_ppn
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
                    $sql_rev = mysqli_query($connect, "SELECT id_inv, no_inv_revisi, pelanggan_revisi, alamat_revisi FROM inv_revisi WHERE id_inv = '$id_inv'");
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
        </div>
    <div class="invoice">
        <h2 align='right'><strong>INVOICE</strong></h2>
        <div class="invoice-header">
            <div class="col-header-1">
                <!-- Kolom pertama -->
                <img src="assets/img/header-kma.jpg" style="width: 460px; height: 70px;">
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
                    &nbsp;: <?php echo $no_inv ?> <br>
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
                ppn.id_inv_ppn, ppn.no_inv, ppn.tgl_inv, ppn.tgl_tempo, ppn.cs_inv,
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
                <?php echo $data_rev['pelanggan_revisi'] ?> <br>
                <?php echo $data_rev['alamat_revisi'] ?>
            </div>
        </div>
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
                    $sub_total = 0;
                    $grand_total = 0;
                    $sql_trx = "SELECT 
                                    ppn.id_inv_ppn,
                                    ppn.kategori_inv,
                                    ppn.sp_disc,
                                    ppn.note_inv,
                                    ppn.total_inv,
                                    trx.id_produk,
                                    trx.nama_produk AS nama_produk_rev,
                                    trx.harga,
                                    trx.qty AS total_qty,
                                    trx.disc,
                                    SUM(trx.total_harga) AS total_harga,
                                    trx.status_br_refund,
                                    trx.created_date,
                                    tpr.nama_produk,
                                    tpr.satuan
                                FROM inv_ppn AS ppn
                                LEFT JOIN inv_komplain ik ON ppn.id_inv_ppn = ik.id_inv
                                LEFT JOIN tmp_produk_komplain trx ON trx.id_inv = ppn.id_inv_ppn
                                LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                                LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                                WHERE ppn.id_inv_ppn = '$id_inv' AND trx.status_br_refund = '0'
                                GROUP BY trx.id_produk
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
                            <td><?php echo $data_trx['nama_produk_rev'] ?></td>
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
                
                if ($stmt->execute()) {
                    // echo "Data berhasil diperbarui.";
                    if($dpp == "0"){
                         // Mengambil nilai 'id' dan 'id_komplain' dari query string dan mengenkripsi URL
                        $id = urlencode($_GET['id']);
                        $id_komplain = urlencode($_GET['id_komplain']);

                        // Menggunakan JavaScript untuk melakukan redirect
                        echo "<script type='text/javascript'>
                                window.location.href = 'cetak-inv-revisi-ppn-new.php?id=$id&id_komplain=$id_komplain';
                            </script>";
                        exit;
                    }
                } else {
                    echo "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        ?>
        <br>
        <div class="invoice-footer">
            <img src="assets/img/footer-invoice.jpg" style="width: 800px;">
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

<script>
    // Fungsi untuk menampilkan dialog pencetakan
    function showPrintDialog() {
        window.print();
    }

    // Menambahkan event listener ke tombol cetak
    document.getElementById('print').addEventListener('click', showPrintDialog);
</script>
<!-- jquery 3.6.3 -->
<script src="assets/js/jquery.min.js"></script>
<script>

</script>


