<?php
include "akses.php";
$page = 'br-masuk';
$page2 = 'br-masuk-reg';
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">

  <style>
    #table2 {
      cursor: pointer;
    }

    #table3 {
      cursor: pointer;
    }

    input[type="text"]:read-only {
      background: #e9ecef;
    }

    textarea[type="text"]:read-only {
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
    <!-- Loading -->
    <div class="loader loader">
      <div class="loading">
        <img src="img/loading.gif" width="200px" height="auto">
      </div>
    </div>
    <!-- ENd Loading -->
    <section>
      <!-- SWEET ALERT -->
      <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) {
                                              echo $_SESSION['info'];
                                            }
                                            unset($_SESSION['info']); ?>"></div>
      <!-- END SWEET ALERT -->
      <div class="container-fluid">
        <div class="card shadow p-3">
          <?php
          $id_act = decrypt($_GET['edit-act'], $key_global);
          $id_inv = decrypt($_GET['id_inv'], $key_global);
          $id = decrypt($_GET['id'], $key_global);
          $sql = "SELECT 
                    act.id_act_br_import,
                    act.id_isi_inv_br_import,
                    act.id_produk_reg,
                    act.qty_act,
                    iibi.id_isi_inv_br_import, 
                    iibi.id_inv_br_import,
                    iibi.qty,
                    pr.nama_produk,
                    mr.nama_merk
                  FROM act_br_import AS act
                  LEFT JOIN isi_inv_br_import iibi ON (act.id_isi_inv_br_import = iibi.id_isi_inv_br_import)
                  LEFT JOIN tb_produk_reguler pr ON (act.id_produk_reg = pr.id_produk_reg)
                  LEFT JOIN tb_merk mr ON (pr.id_merk = mr.id_merk)
                  WHERE act.id_act_br_import ='$id_act'";
          $query = mysqli_query($connect, $sql);
          $data = mysqli_fetch_array($query);
          ?>
          <form method="post" action="proses/proses-br-in-import.php" class="form">
            <div class="row">
              <input type="hidden" class="form-control" name="id_act_br_import" value="<?php echo encrypt($data['id_act_br_import'], $key_global) ?>">
              <input type="hidden" class="form-control" name="id_isi_inv_br_import" value="<?php echo encrypt($data['id_isi_inv_br_import'], $key_global) ?>">
              <input type="hidden" class="form-control" name="id_inv_br_import" value="<?php echo encrypt($data['id_inv_br_import'], $key_global) ?>">
              <div class="col-sm-5 mb-3">
                <label for="nama_produk">Nama Produk</label>
                <input type="hidden" class="form-control" name="id_produk" id="idProduk" value="<?php echo $data['id_produk_reg'] ?>">
                <input type="text" class="form-control" name="nama_produk" id="namaProduk" value="<?php echo $data['nama_produk'] ?>" data-bs-toggle="modal" data-bs-target="#modalBarang" readonly>
              </div>
              <div class="col-sm-3 mb-3">
                <label>Merk</label>
                <input type="text" class="form-control" id="merkProduk" value="<?php echo $data['nama_merk'] ?>" readonly>
              </div>
              <div class="col-sm-2 mb-3">
                <label>Qty Order</label>
                <input type="text" class="form-control" name="qty" value="<?php echo number_format($data['qty'], 0, '.', '.'); ?>" readonly>
              </div>
              <div class="col-sm-2 mb-3">
                <label>Qty Actual</label>
                <input type="text" class="form-control" name="qty_act" id="qtyInput" value="<?php echo number_format($data['qty_act'], 0, '.', '.'); ?>">
              </div>
            </div>
            <div class="text-end">
              <button type="submit" name="edit-act-br-import" id="submitButton" class="btn btn-primary" disabled><i class="bx bx-save" style="color: white; font-size: 18px;"></i> Simpan Data</button>
              <a href="list-act-br-import.php?id=<?php echo encrypt($id, $key_global) ?> && id_inv=<?php echo encrypt($id_inv, $key_global) ?>" class="btn btn-secondary"><i class="bi bi-arrow-left-square-fill" style="color: white; font-size: 18px;"></i> Tutup</a>
            </div>
          </form>
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

<!-- Generate UUID -->
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

<!-- Modal Barang -->
<div class="modal fade" id="modalBarang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Data Barang</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Pills Tabs -->
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Produk Reguler</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Produk ECat</button>
          </li>
        </ul>
        <div class="tab-content pt-2" id="myTabContent">
          <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="home-tab">
            <div class="table-responsive">
              <table class="table table-striped table-bordered" id="table2">
                <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <td class="text-center text-nowrap p-3">No</td>
                    <td class="text-center text-nowrap p-3">Kode Produk</td>
                    <td class="text-center text-nowrap p-3">Nama Produk</td>
                    <td class="text-center text-nowrap p-3">Merk</td>
                    <td class="text-center text-nowrap p-3">Stock</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  $sql = "SELECT pr.id_produk_reg, pr.kode_produk, pr.nama_produk, mr.nama_merk, spr.stock
                          FROM stock_produk_reguler as spr
                          LEFT JOIN tb_produk_reguler pr ON (spr.id_produk_reg = pr.id_produk_reg)
                          LEFT JOIN tb_merk mr ON (pr.id_merk = mr.id_merk)
                          WHERE pr.nama_produk IS NOT NULL
                          ORDER BY nama_produk ASC";
                  $query = mysqli_query($connect, $sql);
                  while ($data = mysqli_fetch_array($query)) {
                  ?>
                    <tr data-idprod="<?php echo $data['id_produk_reg']; ?>" data-namaprod="<?php echo $data['nama_produk']; ?>" data-merkprod="<?php echo $data['nama_merk']; ?>" data-bs-dismiss="modal">
                      <td class="text-center text-nowrap"><?php echo $no; ?></td>
                      <td class="text-center text-nowrap"><?php echo $data['kode_produk']; ?></td>
                      <td class="text-start text-nowrap"><?php echo $data['nama_produk']; ?></td>
                      <td class="text-center text-nowrap"><?php echo $data['nama_merk']; ?></td>
                      <td class="text-center text-nowrap"><?php echo $data['stock']; ?></td>
                    </tr>
                    <?php $no++; ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="table-responsive">
              <table class="table table-striped table-bordered" id="table3">
                <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <td class="text-center text-nowrap p-3">No</td>
                    <td class="text-center text-nowrap p-3">Kode Produk</td>
                    <td class="text-center text-nowrap p-3">Nama Produk</td>
                    <td class="text-center text-nowrap p-3">Merk</td>
                    <td class="text-center text-nowrap p-3">Stock</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  $sql = "SELECT pr.id_produk_ecat, pr.kode_produk, pr.nama_produk, mr.nama_merk, spr.stock
                          FROM stock_produk_ecat as spr
                          LEFT JOIN tb_produk_ecat pr ON (spr.id_produk_ecat = pr.id_produk_ecat)
                          LEFT JOIN tb_merk mr ON (pr.id_merk = mr.id_merk)
                          WHERE pr.nama_produk IS NOT NULL
                          ORDER BY nama_produk ASC";
                  $query = mysqli_query($connect, $sql);
                  while ($data = mysqli_fetch_array($query)) {
                  ?>
                    <tr data-idprod="<?php echo $data['id_produk_ecat']; ?>" data-namaprod="<?php echo $data['nama_produk']; ?>" data-merkprod="<?php echo $data['nama_merk']; ?>" data-bs-dismiss="modal">
                      <td class="text-center text-nowrap"><?php echo $no; ?></td>
                      <td class="text-center text-nowrap"><?php echo $data['kode_produk']; ?></td>
                      <td class="text-start text-nowrap"><?php echo $data['nama_produk']; ?></td>
                      <td class="text-center text-nowrap"><?php echo $data['nama_merk']; ?></td>
                      <td class="text-center text-nowrap"><?php echo $data['stock']; ?></td>
                    </tr>
                    <?php $no++; ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div><!-- End Pills Tabs -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- End Modal Barang -->


<script>
  // Deklarasi fungsi
  function enableSubmitButton() {
    $('#submitButton').prop('disabled', false);
  }

  function disableSubmitButton() {
    $('#submitButton').prop('disabled', true);
  }

  $(document).on('click input', '#table2 tbody tr, #qtyInput', function(e) {
    if (e.target.id !== 'qtyInput') {
      $('#idProduk').val($(this).data('idprod'));
      $('#namaProduk').val($(this).data('namaprod'));
      $('#merkProduk').val($(this).data('merkprod'));
      $('#modalBarang').modal('hide');
    }

    var cekQty = document.getElementById('qtyInput').value;
    var qtyAct = $('#qtyInput').val().replace(/\D/g, '');
    var qtyInput = $(this).val().replace(/\D/g, '');
    var qtyAwal = qtyInput ? parseInt(qtyInput) : 0;
    $(this).val(qtyAwal.toLocaleString('id-ID').replace(',', '.'));

    // console.log(qtyAwal.toLocaleString('id-ID').replace(',', '.'));
    // console.log(cekQty);
    // console.log(qtyAct);
    // console.log($('#idProduk').val());

    if ($('#table2').val() !== '' || $('#qtyInput').val() !== '') {
      if ($('#idProduk').val() != idProduk || qtyAwal.toLocaleString('id-ID').replace(',', '.') != qtyAct) {
        // Aktifkan button submit
        enableSubmitButton();
      } else {
        // Non AKtifkan button submit
        disableSubmitButton();
      }
    }
  });

  $(document).on('click input', '#table3 tbody tr, #qtyInput', function(e) {
    if (e.target.id !== 'qtyInput') {
      $('#idProduk').val($(this).data('idprod'));
      $('#namaProduk').val($(this).data('namaprod'));
      $('#merkProduk').val($(this).data('merkprod'));
      $('#modalBarang').modal('hide');
    }

    var cekQty = document.getElementById('qtyInput').value;
    var qtyAct = $('#qtyInput').val().replace(/\D/g, '');
    var qtyInput = $(this).val().replace(/\D/g, '');
    var qtyAwal = qtyInput ? parseInt(qtyInput) : 0;
    $(this).val(qtyAwal.toLocaleString('id-ID').replace(',', '.'));

    // console.log(qtyAwal.toLocaleString('id-ID').replace(',', '.'));
    // console.log(cekQty);
    // console.log(qtyAct);
    // console.log($('#idProduk').val());

    if ($('#table3').val() !== '' || $('#qtyInput').val() !== '') {
      if ($('#idProduk').val() != idProduk || qtyAwal.toLocaleString('id-ID').replace(',', '.') != qtyAct) {
        // Aktifkan button submit
        enableSubmitButton();
      } else {
        // Non AKtifkan button submit
        disableSubmitButton();
      }
    }
  });
</script>