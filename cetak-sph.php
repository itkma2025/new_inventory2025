<?php
include "akses.php";
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
</head>

<body>
  <div class="sph">
  <?php  
      include "koneksi.php";
      $id_sph = $_GET['id'];
    $id_sph_decode = decrypt($id_sph, $key_global);

      
       // Update status cetak
      $status_cetak = $connect->query("UPDATE sph SET status_cetak = '1' WHERE sph.id_sph = '$id_sph_decode'");

      $sph = " SELECT 
                      sph.id_sph, sph.no_sph, sph.tanggal, sph.up, sph.id_cs, sph.alamat, sph.ttd_oleh, sph.jabatan, sph.perihal, sph.note, cs.nama_cs
                FROM sph as sph
                LEFT JOIN tb_customer_sph cs ON (cs.id_cs = sph.id_cs) 
                WHERE sph.id_sph = '$id_sph_decode'";
      $query_sph = mysqli_query($connect, $sph);
      $data_sph = mysqli_fetch_array($query_sph);
      $id_sph = $data_sph['id_sph'];
      $ttd = $data_sph['ttd_oleh'];
      $jabatan = $data_sph['jabatan'];
    ?>
    <div class="sph-img">
      <img src="assets/img/header-sph.jpg" class="card-img-top">
    </div>
    <div class="sph-content mt-3">
      <div class="row">
          <div class="col text-left">
            <div class="row">
              <div class="col-content-1">Nomor</div>
              <div class="col-content-2">: <?php echo $data_sph['no_sph'] ?></div>
            </div>
            <div class="row">
              <div class="col-content-1">Hal</div>
              <div class="col-content-2">: <?php echo $data_sph['perihal'] ?></div>
            </div>
          </div>
          <div class="col text-right">
            <div class="col-content-3">Bekasi, <?php echo $data_sph['tanggal'] ?></div>
          </div>
      </div>

      <!-- Kepada Yth -->
      <div class="col-content-3 text-left mt-3">
        Kepada Yth,
      </div>
      <div class="content-yth text-left">
        <?php echo $data_sph['nama_cs'] ?>
      </div>
      <?php  
        if($data_sph['up'] == ''){
          }else{
              echo '<div class="content-yth text-left">
                      '.$data_sph['up'].'
                    </div>';
          }
      ?>
      <div class="content-alamat text-left">
        <?php echo $data_sph['alamat'] ?>
      </div>

      <!-- Dengan Hormat -->
      <div class="col-content-3 text-left mt-3">
        Dengan Hormat,
      </div>
      <div class="content-hormat text-left">
        Kami ingin memberikan penawaran harga untuk produk-produk kami sebagai berikut:
      </div>

      <!-- Table -->
      <div class="table-reponsive mt-3">
        <table class="table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Produk</th>
              <th class="text-nowrap">Merk - Asal Negara</th>
              <th colspan="2">Qty</th>
              <th>Harga</th>
              <th>Total Harga</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $no = 1;
              $grand_total = 0;
              $sql_trx = "SELECT DISTINCT
                              sr.id_sph,
                              tps.id_transaksi,
                              tps.id_produk,
                              tps.harga AS harga_produk_sph,
                              tps.qty,
                              tps.status_trx,
                              tps.created_date,
                              COALESCE(spr.stock, spe.stock) AS stock,
                              COALESCE(tpr.nama_produk, tpe.nama_produk, tpsm.nama_set_marwa, tpse.nama_set_ecat) AS nama_produk,
                              COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                              COALESCE(tpr.harga_produk, tpe.harga_produk, tpsm.harga_set_marwa, tpse.harga_set_ecat) AS harga_produk,
                              COALESCE(mr_produk.nama_merk, mr_produk_ecat.nama_merk, mr_set.nama_merk, mr_set_ecat.nama_merk) AS merk_produk -- Nama merk untuk produk
                          FROM transaksi_produk_sph AS tps
                          LEFT JOIN sph sr ON sr.id_sph = tps.id_sph
                          LEFT JOIN stock_produk_reguler spr ON tps.id_produk = spr.id_produk_reg
                          LEFT JOIN stock_produk_ecat spe ON tps.id_produk = spe.id_produk_ecat
                          LEFT JOIN tb_produk_reguler tpr ON tps.id_produk = tpr.id_produk_reg
                          LEFT JOIN tb_produk_ecat tpe ON tps.id_produk = tpe.id_produk_ecat
                          LEFT JOIN tb_produk_set_marwa tpsm ON tps.id_produk = tpsm.id_set_marwa
                          LEFT JOIN tb_produk_set_ecat tpse ON tps.id_produk = tpse.id_set_ecat
                          LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                          LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk ecat
                          LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                          LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                          WHERE sr.id_sph = '$id_sph_decode' AND tps.status_trx = '1' ORDER BY tps.created_date ASC";
              $query_trx = mysqli_query($connect, $sql_trx);
              while($data_trx = mysqli_fetch_array($query_trx)){
                $id_produk = $data_trx['id_produk'];
                $id_produk_substr = substr($id_produk, 0, 2);
                $total_harga = $data_trx['harga_produk'] * $data_trx['qty'];
                $grand_total += $total_harga;

            ?>
            <tr>
              <td><?php echo $no; ?></td>
              <td class="text-left" style="max-width: 300px;"><?php echo $data_trx['nama_produk'] ?></td>
              <td class="text-nowrap"><?php echo $data_trx['merk_produk'] ?> - Pakistan</td>
              <td class="text-right"><?php echo $data_trx['qty'] ?></td>
              <td class="text-nowrap">
                <?php 
                  if($id_produk_substr == 'BR'){
                    echo "Pcs";
                  } else {
                    echo "Set";
                  }
                ?>
              </td>
              <td class="text-nowrap text-right"><?php echo number_format($data_trx['harga_produk_sph']) ?></td>
              <td class="text-right text-nowrap"><?php echo number_format($total_harga) ?></td>
            </tr>
            <?php $no++ ?>
            <?php } ?>
            <tr>
              <td class="text-right" colspan="6"><b>GRAND TOTAL</b></td>
              <td class="text-right"><?php echo number_format($grand_total) ?></td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div class="content-alamat text-left">
        Keterangan Harga :
      </div>
      <div class="content-alamat text-left">
        <?php
            $note = $data_sph['note'];

            $items = explode("\n", trim($note));

            foreach ($items as $notes) {
                echo trim($notes) . '<br>';
            }
        ?>
      </div>
      <p>
      <div class="text-left">
        <p style=" text-align: justify;">Kami mengucapkan terima kasih atas waktu yang Anda luangkan untuk meninjau penawaran kami. Jika ada pertanyaan lebih lanjut atau perlu penjelasan tambahan, mohon jangan ragu untuk menghubungi tim kami. Kami selalu siap memberikan bantuan yang Anda butuhkan.</p>
      </div>

      <!-- TTD -->
      <div class="content-hormat text-left">
        Hormat Kami,<br>
        <b>PT. Karsa Mandiri Alkesindo</b>
      </div>
      <div class="content-img-ttd text-left">
        <img src="assets/img/ttd.png" alt="">
        <!-- <br><br><br><br> -->
      </div>
      <div class="content-hormat text-left">
        <b style="text-decoration: underline;">
          <?php echo $ttd ?>
        </b><br>
        <?php echo $jabatan ?>
      </div>
    </div>
  </div>
  <br>
</body>

</html>