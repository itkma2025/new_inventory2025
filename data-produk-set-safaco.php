<?php
require_once "akses.php";
$page = 'produk';
$page2 = 'data-produk-set-safaco';
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
    <!-- Loading -->
    <div class="loader loader">
      <div class="loading">
        <img src="img/loading.gif" width="200px" height="auto">
      </div>
    </div>
    <!-- ENd Loading -->
    <div class="pagetitle">
      <h1>Data Produk Set Ecat</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dasboard.php">Home</a></li>
          <li class="breadcrumb-item active">Data Produk</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section>
      <!-- SWEET ALERT -->
      <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
      <!-- END SWEET ALERT -->
      <div class="container-fluid">
        <div class="card">
          <div class="card-body p-3">
            <a href="tambah-data-produk-set-safaco.php" class="btn btn-primary btn-md"><i class="bi bi-plus-circle"></i> Tambah data produk set</a>
            <div class="table-responsive mt-3">
              <table class="table table-striped table-bordered" id="tableExport">
                <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <td class="text-center text-nowrap p-3" style="width: 50px">No</td>
                    <td class="text-center text-nowrap p-3" style="width: 120px">Kode Produk Set</td>
                    <td class="text-center text-nowrap p-3" style="width: 250px">Nama Set Produk </td>
                    <td class="text-center text-nowrap p-3" style="width: 100px">Merk</td>
                    <td class="text-center text-nowrap p-3" style="width: 100px">Kat Penjualan</td>
                    <td class="text-center text-nowrap p-3" style="width: 100px">Harga Modal</td>
                    <td class="text-center text-nowrap p-3" style="width: 100px">Harga Jual</td>
                    <td class="text-center text-nowrap p-3" style="width: 100px">Aksi</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $key = "KM@2024?SET";
                  $no = 1;
                  $sql = "SELECT 
                            prs.id_set_safaco,
                            prs.kode_set_safaco,
                            prs.nama_set_safaco,
                            prs.harga_set_safaco,
                            kj.nama_kategori as nama_kat,
                            mr.nama_merk
                          FROM tb_produk_set_safaco as prs
                          LEFT JOIN tb_merk mr ON (prs.id_merk = mr.id_merk)
                          LEFT JOIN tb_kat_penjualan kj ON (prs.id_kat_penjualan = kj.id_kat_penjualan)
                          ORDER BY prs.nama_set_safaco ASC";
                  $query = mysqli_query($connect, $sql) or die(mysqli_error($connect, $sql));
                  while ($data = mysqli_fetch_array($query)) {
                    $id_set_safaco = $data['id_set_safaco'];
                    $encrypt_id_set_safaco = encrypt($id_set_safaco, $key);
                  ?>
                    <tr>
                      <td class="text-center text-nowrap align-middle"><?php echo $no; ?></td>
                      <td class="text-nowrap align-middle"><?php echo $data['kode_set_safaco']; ?></td>
                      <td class="text-nowrap align-middle"><?php echo $data['nama_set_safaco']; ?></td>
                      <td class="text-center text-nowrap align-middle"><?php echo $data['nama_merk']; ?></td>
                      <td class="text-center text-nowrap align-middle"><?php echo $data['nama_kat']; ?></td>
                      <?php
                        $id = $data['id_set_safaco'];
                        $grand_total = 0;
                        $sql_data = "SELECT 
                                        ipsm.id_isi_set_safaco, 
                                        ipsm.id_set_safaco, 
                                        ipsm.qty, 
                                        COALESCE(tpr.harga_produk , tpe.harga_produk) AS harga_produk
                                        FROM isi_produk_set_safaco ipsm
                                        LEFT JOIN tb_produk_reguler tpr ON (ipsm.id_produk = tpr.id_produk_reg)
                                        LEFT JOIN tb_produk_safaco tpe ON (ipsm.id_produk = tpe.id_produk_safaco)
                                        WHERE ipsm.id_set_safaco =  '$id'";
                        $query_data = mysqli_query($connect, $sql_data) or die(mysqli_error($connect, $sql_data));
                        while ($row = mysqli_fetch_array($query_data)) {
                            $harga = $row['harga_produk'];
                            $qty = $row['qty'];
                            $jumlah = $qty * $harga;
                            $grand_total += $jumlah;
                      ?>
                      <?php } ?>

                      <td class="text-end text-nowrap align-middle"><?php echo number_format($grand_total, 0, '.', '.'); ?></td>
                      <td class="text-end text-nowrap align-middle"><?php echo number_format($data['harga_set_safaco'], 0, '.', '.'); ?></td>
                      <td class="text-center text-nowrap align-middle">
                        <!-- Lihat Data -->
                        <a href="detail-isi-set-safaco.php?detail-id=<?php echo $encrypt_id_set_safaco ?>" class="btn btn-primary btn-sm"><i class="bi bi-eye-fill"></i></a>
                        <?php
                        if ($role == "Super Admin" || $data_role['role'] == "Manager Gudang") {
                          ?>
                            <!-- QR code -->
                            <a class="btn btn-info btn-sm" href="cetak-qr-code-set-safaco.php?id=<?php echo $encrypt_id_set_safaco ?>">
                              <i class="bi bi-qr-code-scan"></i>
                            </a>
                            <br>
                            <!-- Edit Data -->
                            <a href="edit-data-set-safaco.php?edit-set-safaco=<?php echo $encrypt_id_set_safaco ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                            <!-- Hapus Data -->
                            <a href="proses/proses-produk-set-safaco.php?hapus-set-safaco=<?php echo $encrypt_id_set_safaco ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                          <?php
                        }
                        ?>
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

<!-- Generat UUID -->
<?php
function generate_uuid()
{
  return sprintf(
    '%04x%04x%04x',
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0x0fff) | 0x4000,
    mt_rand(0, 0x3fff) | 0x8000,
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff)
  );
}
?>
<!-- End Generate UUID -->

<script>
  // delete button
  $("#table1").on("click", ".delete-button", function() {
    $(this).closest("tr").remove();
    if ($("#table1 tbody tr").length === 0) {
      $("#table1 tbody").append("<tr><td colspan='9' align='center'>Data not found</td></tr>");
    }
  });
</script>