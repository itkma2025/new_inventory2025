<?php
include "akses.php";
include "function/class-spk.php";
require_once "function/function-enkripsi.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <title>Inventory KMA</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link rel="stylesheet" href="assets/css/cetak-sph.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" crossorigin="anonymous">
  <style>
    .col-content-1 {
        width: 25%;
    }

    .col-content-2 {
        width: 75%;
    }
    .col-content-ttd-1 {
        width: 7%;
    }
    .col-content-ttd-2 {
        width: 75%;
    }
    /* Tombol cetak dengan warna biru */
    .print-button {
      background-color: #0074e4; /* Warna biru */
      color: #ffffff; /* Warna teks putih */
      padding: 10px 20px; /* Ruang bantalan dalam tombol */
      border: none; /* Tanpa border */
      border-radius: 5px; /* Tampilan sudut tombol */
      cursor: pointer; /* Kursor tangan saat mengarahkan ke tombol */
    }

    /* Tombol cetak dengan warna biru */
    .back-button {
      background-color: #FFC107; /* Warna biru */
      color: #000000; /* Warna teks putih */
      padding: 10px 20px; /* Ruang bantalan dalam tombol */
      border: none; /* Tanpa border */
      border-radius: 5px; /* Tampilan sudut tombol */
      cursor: pointer; /* Kursor tangan saat mengarahkan ke tombol */
    }

    /* Efek hover saat kursor berada di atas tombol */
    .print-button:hover {
      background-color: #005bbb; /* Warna biru yang berbeda saat di hover */
    }

    @media print {
      #printButton {
        display: none;
      }
      #backButton {
        display: none;
      }
    }
  </style>
</head>

<body style="font-size: 14px;">
  <div class="sph">
    <div style="text-align: center;">
        <button id="backButton" class="back-button" onclick="goBack()">
            <i class="fas fa-arrow-left"></i> Halaman Sebelumnya
        </button>
        <button id="printButton" class="print-button">
            <i class="fas fa-print"></i> Print
        </button>
    </div>
    <?php
        include "koneksi.php";
        $id_spk = decrypt($_GET['id'], $key_spk);
        $sql = " SELECT 
                    sr.no_spk, 
                    sr.no_po, 
                    DATE_FORMAT(STR_TO_DATE(sr.tgl_spk, '%d/%m/%Y, %H:%i'), '%d %M %Y') AS tgl_spk, -- Mengubah format tgl_spk
                    sr.note, 
                    cs.nama_cs, 
                    cs.alamat, 
                    ordby.order_by, 
                    sl.nama_sales  
                  FROM spk_reg AS sr
                  JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                  JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                  JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                  WHERE sr.id_spk_reg = '$id_spk'";
        $query = mysqli_query($connect, $sql);
        $data = mysqli_fetch_array($query);
        // Tampilkan tanggal dengan nama bulan dalam bahasa Indonesia
        // Tampilkan tanggal dengan nama bulan dalam bahasa Indonesia menggunakan fungsi kustom
        $tanggal_spk = $data['tgl_spk'];
        $tanggal_spk_indonesia = formatTanggal($tanggal_spk);

        // Fungsi untuk mengonversi nama bulan dalam bahasa Inggris menjadi bahasa Indonesia
        function formatTanggal($englishDate) {
            $bulan = [
                'January' => 'Januari',
                'February' => 'Februari',
                'March' => 'Maret',
                'April' => 'April',
                'May' => 'Mei',
                'June' => 'Juni',
                'July' => 'Juli',
                'August' => 'Agustus',
                'September' => 'September',
                'October' => 'Oktober',
                'November' => 'November',
                'December' => 'Desember'
            ];

            $timestamp = strtotime($englishDate);
            $day = date('d', $timestamp);
            $month = $bulan[date('F', $timestamp)];
            $year = date('Y', $timestamp);

            return "$day $month $year";
        }
    ?>
    <div class="sph-img">
        <br>
        <h2><b>SURAT PERINTAH KERJA</b></h2>
    </div>
    <div class="sph-content mt-3">
      <div class="row">
          <div class="col text-left">
            <div class="row">
              <div class="col-content-1">No. Spk</div>
              <div class="col-content-2">: <?php echo $data['no_spk'] ?></div>
            </div>
            <div class="row">
              <div class="col-content-1">No. PO</div>
              <div class="col-content-2">: <?php echo $data['no_po'] ?></div>
            </div>
            <div class="row">
              <div class="col-content-1">Customer</div>
              <div class="col-content-2">: <?php echo $data['nama_cs'] ?></div>
            </div>
            <div class="row">
              <div class="col-content-1">Sales</div>
              <div class="col-content-2">: <?php echo $data['nama_sales'] ?></div>
            </div>
            <div class="row">
              <div class="col-content-1">Order By</div>
              <div class="col-content-2">: <?php echo $data['order_by'] ?></div>
            </div>
          </div>
          <div class="col text-right">
            <div class="col-content-3">Bekasi, <?php echo $tanggal_spk_indonesia; ?></div>
          </div>
      </div> <!-- Kepada Yth -->

      <!-- Table -->
      <div class="table-reponsive mt-3">
        <table class="table">
            <thead>
                <tr>
                    <td class="text-center p-3 text-nowrap" style="width: 50px">No</td>
                    <td class="text-center p-3 text-nowrap" style="width: 350px">Nama Produk</td>
                    <td class="text-center p-3 text-nowrap" style="width: 100px">Qty Order</td>
                    <td class="text-center p-3 text-nowrap" style="width: 100px">Satuan</td>
                    <td class="text-center p-3 text-nowrap" style="width: 100px">Merk</td>
                </tr>
            </thead>
            <tbody>
                <?php
                include "koneksi.php";
                $year = date('y');
                $day = date('d');
                $month = date('m');
                $id_spk_decode = base64_decode($_GET['id']);
                $no = 1;
                $sql_trx = "SELECT 
                                sr.id_spk_reg,
                                sr.id_inv,
                                tps.id_tmp,
                                tps.id_produk,
                                tps.qty,
                                tps.status_tmp, 
                                tps.created_date,
                                spr.stock, 
                                COALESCE(tpr.nama_produk, tpe.nama_produk, tpsm.nama_set_marwa, tpse.nama_set_ecat) AS nama_produk,
                                COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                                COALESCE(tpr.harga_produk, tpe.harga_produk, tpsm.harga_set_marwa, tpse.harga_set_ecat) AS harga_produk ,            
                                COALESCE(mr_produk.nama_merk, mr_produk_ecat.nama_merk, mr_set.nama_merk, mr_set_ecat.nama_merk) AS merk_produk              
                            FROM tmp_produk_spk AS tps
                            LEFT JOIN spk_reg sr ON sr.id_spk_reg = tps.id_spk
                            LEFT JOIN stock_produk_reguler spr ON tps.id_produk = spr.id_produk_reg
                            LEFT JOIN tb_produk_reguler tpr ON tps.id_produk = tpr.id_produk_reg
                            LEFT JOIN tb_produk_ecat tpe ON tps.id_produk = tpe.id_produk_ecat
                            LEFT JOIN tb_produk_set_marwa tpsm ON tps.id_produk = tpsm.id_set_marwa
                            LEFT JOIN tb_produk_set_ecat tpse ON tps.id_produk = tpse.id_set_ecat
                            LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                            LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk reguler
                            LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                            LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                            WHERE sr.id_spk_reg = '$id_spk' AND tps.status_tmp = '1' ORDER BY tps.created_date ASC";
                $trx_produk_reg = mysqli_query($connect, $sql_trx);
                $total_rows = mysqli_num_rows($trx_produk_reg);
                while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                    $id_produk = $data_trx['id_produk'];
                    $satuan = $data_trx['satuan'];
                    $stock_edit = $data_trx['qty'] + $data_trx['stock'];
                    $namaProduk =$data_trx['nama_produk'];
                    $nama_merk = $data_trx['merk_produk'];
                    $harga = $data_trx['harga_produk'];
                    $satuan_produk = '';
                    $id_produk_substr = substr($id_produk, 0, 2);
                    if ($id_produk_substr == 'BR') {
                        $satuan_produk = $satuan;
                    } else {
                        $satuan_produk = 'Set';
                    }
                ?>
                    <tr>
                        <td class="text-nowrap"><?php echo $no; ?></td>
                        <td class="text-left"><?php echo $namaProduk ?></td>
                        <td class="text-nowrap"><?php echo number_format($data_trx['qty']) ?></td>
                        <td class="text-center text-nowrap"><?php echo $satuan_produk ?></td>
                        <td class="text-nowrap"><?php echo $nama_merk ?></td>
                    </tr>
                    <?php $no++; ?>
                <?php } ?>
            </tbody>
        </table>
        <?php  
          if($total_rows > 0) {
              $update = mysqli_query($connect, "UPDATE spk_reg SET status_cetak = 1 WHERE id_spk_reg = '$id_spk'");
          }
        ?>
      </div>
      <div class="content-alamat text-left">
        <b>Note :</b>
      </div>
      <div class="content-alamat text-left">
        <?php
            $note = $data['note'];

            $items = explode("\n", trim($note));

            foreach ($items as $notes) {
                echo trim($notes) . '<br>';
            }
        ?>
      </div>
      <br>
      <div class="sph-content mt-3">
        <!-- TTD -->
        <div class="row">
            <div class="col">
                <div class="content-hormat text-left">
                    Mengetahui,<br>
                </div>
                <div class="content-img-ttd text-left">
                    <br><br><br>
                </div>
                <div class="content-hormat text-left">
                    <b style="text-decoration: underline;">
                    Purwono
                    </b><br>
                    Kepala Gudang
                </div>
            </div>
            <div class="col">
                <div class="content-hormat text-left">
                    Mengetahui,<br>
                </div>
                <div class="content-img-ttd text-left">
                    <br><br><br>
                </div>
                <div class="content-hormat text-left">
                    <b style="text-decoration: underline;">
                    Lisa
                    </b><br>
                    Penanggung Jawab Teknis
                </div>
            </div>
            &nbsp;&nbsp;&nbsp;
            <div class="col" style="border: 1px solid black; padding: 10px;">
                <div class="content-hormat text-left">
                    <b>Nama Petugas :</b><br>
                </div>
                <div class="content-hormat text-left">
                    <!-- <ol>
                      <li></li>
                    </ol> -->
                </div>
            </div>
        </div>
      </div>
    </div>
    <br><br>
  </div>
</body>
</html>
<script>
    // Fungsi untuk menampilkan dialog pencetakan
    function showPrintDialog() {
        window.print();
    }

    // Menambahkan event listener ke tombol cetak
    document.getElementById('printButton').addEventListener('click', showPrintDialog);

     //Fungsi untuk kembali ke halaman sebelumnya
     function goBack() {
        window.history.back();
    }
</script>