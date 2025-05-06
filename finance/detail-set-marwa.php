<?php
$page = 'data';
$page2 = 'data-produk-set-marwa';
require_once "../akses.php";
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
</head>

<body>
  <!-- nav header -->
  <?php include "page/nav-header.php" ?>
  <!-- end nav header -->

  <!-- sidebar  -->
  <?php include "page/sidebar.php"; ?>
  <!-- end sidebar -->

  <main id="main" class="main">
    <section>
      <!-- SWEET ALERT -->
      <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) {
                                              echo $_SESSION['info'];
                                            }
                                            unset($_SESSION['info']); ?>"></div>
      <!-- END SWEET ALERT -->
      <div class="container-fluid">
        <div class="card">
          <div class="card-body p-3">
            <?php
            $id = base64_decode($_GET['detail-id']);
            $sql = "SELECT * FROM tb_produk_set_marwa AS tbsm 
                      LEFT JOIN tb_lokasi_produk AS lk ON (tbsm.id_lokasi = lk.id_lokasi)
                      WHERE id_set_marwa = '$id'";
            $query = mysqli_query($connect, $sql) or die(mysqli_error($connect, $sql));
            $data = mysqli_fetch_array($query);
            ?>
            <div class="row">
              <div class="col-sm-8">
                <p>Kode set : <?php echo $data['kode_set_marwa']; ?></p>
                <p>Nama set : <?php echo $data['nama_set_marwa']; ?></p>
                <?php
                include "koneksi.php";
                $no = 1;
                $grand_total = 0;
                $id = base64_decode($_GET['detail-id']);
                $sql_data = "SELECT ipsm.*, tpsm.*, tpr.* FROM isi_produk_set_marwa ipsm
                             LEFT JOIN tb_produk_reguler tpr ON (ipsm.id_produk = tpr.id_produk_reg)
                             LEFT JOIN tb_produk_set_marwa tpsm ON (ipsm.id_set_marwa = tpsm.id_set_marwa)
                             WHERE ipsm.id_set_marwa = '$id'";
                $query_data = mysqli_query($connect, $sql_data) or die(mysqli_error($connect, $sql_data));
                while ($row = mysqli_fetch_array($query_data)) {
                  $harga = $row['harga_produk'];
                  $qty = $row['qty'];
                  $jumlah = $qty * $harga;
                  $grand_total += $jumlah;
                ?>
                <?php } ?>
                <p>Harga Modal : <?php echo number_format($grand_total, 0, '.', '.'); ?></p>
                <p>Harga Jual : <?php echo number_format($data['harga_set_marwa'], 0, '.', '.'); ?></p>
                <p>Lokasi : <?php echo $data['nama_lokasi']; ?> / <?php echo $data['no_lantai']; ?> / <?php echo $data['nama_area']; ?> / <?php echo $data['no_rak']; ?></p>
              </div>
              <div class="col-sm-4 text-end">
                <a href="tambah-isi-produk-set-marwa.php?id-set=<?php echo base64_encode($data['id_set_marwa']) ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah produk</a>
                <a href="data-produk-set-marwa.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <th class="text-center p-3" style="width: 50px">No</th>
                    <th class="text-center p-3" style="width: 450px">Nama Produk</th>
                    <th class="text-center p-3" style="width: 50px">Qty</th>
                    <th class="text-center p-3" style="width: 80px">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  include "koneksi.php";
                  $no = 1;
                  $grand_total = 0;
                  $id = base64_decode($_GET['detail-id']);
                  $sql_data = "SELECT ipsm.*, tpsm.*, tpr.* FROM isi_produk_set_marwa ipsm
                               LEFT JOIN tb_produk_reguler tpr ON (ipsm.id_produk = tpr.id_produk_reg)
                               LEFT JOIN tb_produk_set_marwa tpsm ON (ipsm.id_set_marwa = tpsm.id_set_marwa)
                               WHERE ipsm.id_set_marwa = '$id'";
                  $query_data = mysqli_query($connect, $sql_data) or die(mysqli_error($connect, $sql_data));
                  while ($row = mysqli_fetch_array($query_data)) {
                    $harga = $row['harga_produk'];
                    $qty = $row['qty'];
                    $jumlah = $qty * $harga;
                    $grand_total += $jumlah;
                  ?>
                    <tr>
                      <td class="text-center"><?php echo $no; ?></td>
                      <td><?php echo $row['nama_produk']; ?></td>
                      <td class="text-end"><?php echo $qty; ?></td>
                      <td class="text-center">
                        <a href="edit-isi-produk-set-marwa.php?edit-id=<?php echo base64_encode($row['id_isi_set_marwa']) ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                        <a href="proses/proses-produk-set-marwa.php?hapus-isi-set=<?php echo base64_encode($row['id_isi_set_marwa']) ?>&kode=<?php echo base64_encode($row['id_set_marwa']) ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                      </td>
                    </tr>
                    <?php $no++; ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>

</html>