<?php
require_once "akses.php";
$page = 'data';
$page2 = 'data-produk-set-ecat';
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
  <style>
    #table2 {
      cursor: pointer;
    }

    input[type="text"]:read-only {
      background: #e9ecef;
    }
  </style>
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
      <div class="container-fluid">
        <div class="card">
          <div class="card-header text-center">
            <h5>Edit Isi Produk Set Marwa</h5>
          </div>
          <div class="card-body p-3">
            <form action="proses/proses-produk-set-ecat.php" method="post">
              <?php
                $key = "KM@2024?SET";
                $id = $_GET['edit-id'];
                $decrypt_id = decrypt($id, $key);
                $sql = "  SELECT
                            ipse.id_isi_set_ecat,
                            ipse.id_set_ecat, 
                            ipse.id_produk, 
                            ipse.qty, 
                            COALESCE(tpr.nama_produk,  tpe.nama_produk) AS nama_produk, 
                            COALESCE(tpr.harga_produk,  tpe.harga_produk) AS harga_produk,
                            tm.nama_merk
                          FROM isi_produk_set_ecat ipse
                          LEFT JOIN tb_produk_reguler tpr ON (ipse.id_produk = tpr.id_produk_reg)
                          LEFT JOIN tb_produk_ecat tpe ON (ipse.id_produk = tpe.id_produk_ecat)
                          LEFT JOIN tb_merk AS tm ON (COALESCE(tpr.id_merk,  tpe.id_merk) = tm.id_merk)
                          WHERE ipse.id_isi_set_ecat = '$decrypt_id'";
                $query = mysqli_query($connect, $sql);
                $data = mysqli_fetch_array($query);
                $id_set_ecat = $data['id_set_ecat'];
                $encrypt_id = encrypt($id_set_ecat, $key);
                $cek_data = mysqli_num_rows($query);
                if($cek_data > 0){
                  ?>
                    <input type="hidden" class="form-control" name="id_isi_set_ecat" value="<?php echo $data['id_isi_set_ecat'] ?>">
                    <input type="hidden" class="form-control" name="id_set_ecat" value="<?php echo $data['id_set_ecat'] ?>">
                    <div class="mb-3">
                      <div class="row">
                        <div class="col-sm-6">
                          <label>Nama Produk</label>
                          <input type="hidden" class="form-control" name="id_produk" id="idProduk" value="<?php echo $data['id_produk'] ?>">
                          <input type="text" class="form-control" name="nama_produk" id="namaProduk" value="<?php echo $data['nama_produk'] ?>" data-bs-toggle="modal" data-bs-target="#modalBarang" readonly>
                        </div>
                        <div class="col-sm-4">
                          <label>Merk</label>
                          <input type="text" class="form-control" name="merk" id="merkProduk" value="<?php echo $data['nama_merk'] ?>" readonly>
                        </div>
                        <div class="col-sm-2">
                          <label>Qty</label>
                          <input type="text" class="form-control" name="qty" value="<?php echo $data['qty'] ?>" required>
                          <input type="hidden" class="form-control" name="id_user" value="<?php echo $_SESSION['tiket_id'] ?>" required>
                        </div>
                        <div class="mt-3">
                          <button type="submit" class="btn btn-primary btn md" name="edit-isi-set-ecat"><i class="bx bx-save"></i> Simpan</button>
                          <a href="detail-isi-set-ecat.php?detail-id=<?php echo $encrypt_id; ?>" class="btn btn-secondary btn md"><i class="bi bi-x"></i> Batal</a>
                        </div>
                      </div>
                    </div>
                  <?php
                } else {
                  ?>
                    <script>
                        // Mengarahkan pengguna ke halaman 404.php
                        window.location.replace("404.php");
                    </script>
                  <?php
                }
              ?>
            </form>
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

<!-- Modal Barang -->
<div class="modal fade" id="modalBarang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Pilih Data Barang</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="produk-ecat-tab" data-bs-toggle="pill" data-bs-target="#produk-ecat" type="button" role="tab" aria-controls="produk-ecat" aria-selected="false">Produk E-Cat</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="produk-reg-tab" data-bs-toggle="pill" data-bs-target="#produk-reg" type="button" role="tab" aria-controls="produk-reg" aria-selected="true">Produk Reguler</button>
          </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
          <div class="tab-pane fade show active" id="produk-ecat" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
            <div class="table-responsive">
              <table class="table table-striped table-bordered" id="table2">
              <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <td class="text-center p-3" style="width: 50px">No</td>
                    <td class="text-center p-3" style="width: 180px">Kode Produk</td>
                    <td class="text-center p-3" style="width: 400px">Nama Produk</td>
                    <td class="text-center p-3" style="width: 100px">Merk</td>
                    <td class="text-center p-3" style="width: 50px">Stock</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  date_default_timezone_set('Asia/Jakarta');

                  include "koneksi.php";
                  $no = 1;
                  $sql = "SELECT 
                              COALESCE(tpe.id_produk_ecat, tpse.id_set_ecat) AS id_produk,
                              COALESCE(tpe.kode_produk, tpse.kode_set_ecat) AS kode_produk,
                              COALESCE(tpe.nama_produk, tpse.nama_set_ecat) AS nama_produk,
                              COALESCE(mr_tpe.nama_merk, mr_tpse.nama_merk) AS nama_merk,
                              spe.id_stock_prod_ecat,
                              spe.stock,
                              tkp.min_stock, 
                              tkp.max_stock,
                              SUBSTRING(COALESCE(tpe.id_produk_ecat, tpse.id_set_ecat), 1, 2) AS substr_id_produk
                          FROM tb_produk_ecat AS tpe
                          LEFT JOIN stock_produk_ecat AS spe ON (tpe.id_produk_ecat = spe.id_produk_ecat)
                          LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spe.id_kat_penjualan)
                          LEFT JOIN tb_produk_set_ecat AS tpse ON (tpse.id_set_ecat = spe.id_produk_ecat)
                          LEFT JOIN tb_merk AS mr_tpe ON (tpe.id_merk = mr_tpe.id_merk)
                          LEFT JOIN tb_merk AS mr_tpse ON (tpse.id_merk = mr_tpse.id_merk)
                          WHERE SUBSTRING(COALESCE(tpe.id_produk_ecat, tpse.id_set_ecat), 1, 2) = 'BR'
                          ORDER BY nama_produk ASC";
                  $query = mysqli_query($connect, $sql);
                  while ($data = mysqli_fetch_array($query)) {
                    $id_stock = base64_encode($data['id_stock_prod_ecat']);
                    $id_produk = base64_encode($data['id_produk']);
                    $stock = $data['stock'];
                    $min_stock = $data['min_stock'];
                    $max_stock = $data['max_stock'];
                    $low = $min_stock * 0.25;
                    $low_lev = $min_stock - $low;
                    $med_lev = $min_stock + $low;
                    $high = $max_stock * 0.25;
                    $high_lev = $max_stock - $high;
                    $stock_status = '';
                    $tampil_stock = number_format($data['stock'], 0, '.', '.');
                  ?>
                    <tr data-idprod="<?php echo $data['id_produk']; ?>" data-namaprod="<?php echo $data['nama_produk']; ?>" data-merkprod="<?php echo $data['nama_merk']; ?>" data-bs-dismiss="modal">
                      <td class="text-center"><?php echo $no; ?></td>
                      <td class="text-center"><?php echo $data['kode_produk']; ?></td>
                      <td><?php echo $data['nama_produk']; ?></td>
                      <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                      <?php
                      if ($stock <= $low_lev) {
                        echo "<td class='text-end text-white' style='background-color: #cc0000'>" . ($tampil_stock) . "</td>";
                      } else if ($stock >= $low_lev && $stock <= $min_stock) {
                        echo "<td class='text-end' style='background-color: #ff4500'>" . ($tampil_stock) . "</td>";
                      } else if ($stock >= $min_stock && $stock <= $high_lev) {
                        echo "<td class='text-end' style='background-color: #ffff00'>" . ($tampil_stock) . "</td>";
                      } else if ($stock >= $high_lev && $stock <= $max_stock) {
                        echo "<td class='text-end text-white' style='background-color: #469536'>" . ($tampil_stock) . "</td>";
                      } else if ($stock > $max_stock) {
                        echo "<td class='text-end text-white' style='background-color: #006600'>" . ($tampil_stock) . "</td>";
                      }
                      ?>
                    </tr>
                    <?php $no++; ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane fade" id="produk-reg" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
            <div class="table-responsive">
              <table class="table table-striped table-bordered" id="table3">
                <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <td class="text-center p-3" style="width: 50px">No</td>
                    <td class="text-center p-3" style="width: 180px">Kode Produk</td>
                    <td class="text-center p-3" style="width: 400px">Nama Produk</td>
                    <td class="text-center p-3" style="width: 100px">Merk</td>
                    <td class="text-center p-3" style="width: 50px">Stock</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  date_default_timezone_set('Asia/Jakarta');

                  include "koneksi.php";
                  $no = 1;
                  $sql = "SELECT 
                              COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa) AS id_produk,
                              COALESCE(tpr.kode_produk, tpsm.kode_set_marwa) AS kode_produk,
                              COALESCE(tpr.nama_produk, tpsm.nama_set_marwa) AS nama_produk,
                              COALESCE(mr_tpr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                              spe.id_stock_prod_reg,
                              spe.stock,
                              tkp.min_stock, 
                              tkp.max_stock,
                              SUBSTRING(COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa), 1, 2) AS substr_id_produk
                          FROM tb_produk_reguler AS tpr
                          LEFT JOIN stock_produk_reguler AS spe ON (tpr.id_produk_reg = spe.id_produk_reg)
                          LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spe.id_kat_penjualan)
                          LEFT JOIN tb_produk_set_marwa AS tpsm ON (tpsm.id_set_marwa = spe.id_produk_reg)
                          LEFT JOIN tb_merk AS mr_tpr ON (tpr.id_merk = mr_tpr.id_merk)
                          LEFT JOIN tb_merk AS mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
                          WHERE SUBSTRING(COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa), 1, 2) = 'BR'
                          ORDER BY nama_produk ASC";
                  $query = mysqli_query($connect, $sql);
                  while ($data = mysqli_fetch_array($query)) {
                    $id_stock = base64_encode($data['id_stock_prod_reg']);
                    $id_produk = base64_encode($data['id_produk']);
                    $stock = $data['stock'];
                    $min_stock = $data['min_stock'];
                    $max_stock = $data['max_stock'];
                    $low = $min_stock * 0.25;
                    $low_lev = $min_stock - $low;
                    $med_lev = $min_stock + $low;
                    $high = $max_stock * 0.25;
                    $high_lev = $max_stock - $high;
                    $stock_status = '';
                    $tampil_stock = number_format($data['stock'], 0, '.', '.');
                  ?>
                    <tr data-idprod="<?php echo $data['id_produk']; ?>" data-namaprod="<?php echo $data['nama_produk']; ?>" data-merkprod="<?php echo $data['nama_merk']; ?>" data-bs-dismiss="modal">
                      <td class="text-center"><?php echo $no; ?></td>
                      <td class="text-center"><?php echo $data['kode_produk']; ?></td>
                      <td><?php echo $data['nama_produk']; ?></td>
                      <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                      <?php
                      if ($stock <= $low_lev) {
                        echo "<td class='text-end text-white' style='background-color: #cc0000'>" . ($tampil_stock) . "</td>";
                      } else if ($stock >= $low_lev && $stock <= $min_stock) {
                        echo "<td class='text-end' style='background-color: #ff4500'>" . ($tampil_stock) . "</td>";
                      } else if ($stock >= $min_stock && $stock <= $high_lev) {
                        echo "<td class='text-end' style='background-color: #ffff00'>" . ($tampil_stock) . "</td>";
                      } else if ($stock >= $high_lev && $stock <= $max_stock) {
                        echo "<td class='text-end text-white' style='background-color: #469536'>" . ($tampil_stock) . "</td>";
                      } else if ($stock > $max_stock) {
                        echo "<td class='text-end text-white' style='background-color: #006600'>" . ($tampil_stock) . "</td>";
                      }
                      ?>
                    </tr>
                    <?php $no++; ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- End Modal Barang -->

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
  // select Produk Reguler
  $(document).on('click', '#table2 tbody tr', function(e) {
    $('#idProduk').val($(this).data('idprod'));
    $('#namaProduk').val($(this).data('namaprod'));
    $('#merkProduk').val($(this).data('merkprod'));
    $('#modalBarang').modal('hide');

    // Mengaktifkan tombol
    $('#simpan').prop('disabled', false);
  });
</script>
<script>
  // select Produk Reguler
  $(document).on('click', '#table3 tbody tr', function(e) {
    $('#idProduk').val($(this).data('idprod'));
    $('#namaProduk').val($(this).data('namaprod'));
    $('#merkProduk').val($(this).data('merkprod'));
    $('#modalBarang').modal('hide');

    // Mengaktifkan tombol
    $('#simpan').prop('disabled', false);
  });
</script>

<!-- Number Format -->
<script>
  $(document).on('input', '#qtyInput', function(e) {
    var qtyInput = $(this).val().replace(/\D/g, '');
    var qtyAwal = qtyInput ? parseInt(qtyInput) : 0;
    $(this).val(qtyAwal.toLocaleString('id-ID').replace(',', '.'));

    console.log(qtyAwal.toLocaleString('id-ID').replace(',', '.'));

    // mendapatkan tombol dengan id "submitButton"
    var submitButton = document.getElementById("submitButton");

    // memeriksa apakah nilai qty sudah diisi atau tidak
    if ($(this).val().trim() !== '' && parseInt($(this).val().replace(/\D/g, '')) > 0) {
      submitButton.disabled = false;
    } else {
      submitButton.disabled = true;
    }
  });
</script>