<?php
$page = 'perubahan-merk';
$page2  = 'ganti-merk';
include "akses.php";
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

    #table3 {
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
      <!-- SWEET ALERT -->
      <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info'];} unset($_SESSION['info']); ?>"></div>
      <!-- END SWEET ALERT -->
      <div class="container-fluid">
        <div class="card">
          <div class="card-header text-center">Form Perubahan Merk Produk</div>
          <div class="shadow card-body">
            <form action="proses/proses-ganti-merk.php" method="POST">
              <?php
              $UUID = generate_uuid();
              ?>
              <div class="mb-3 mt-3">
                <div class="col-md-3">
                  <label>Nama Petugas</label>
                  <input type="text" class="form-control" name="nama_petugas" required>
                </div>
              </div>
              <div class="mb-3 mt-3">
                <!-- Merk Awal -->
                <p style="text-decoration: underline;">Merk Awal</p>
                <div class="row">
                  <div class="col-sm-5">
                    <label>Nama Produk</label>
                    <input type="hidden" class="form-control" name="id_ganti_merk_out" value="MERK-UPDATE-<?php echo $UUID; ?>">
                    <input type="hidden" class="form-control" name="id_produk_awal" id="idProdukAwal">
                    <input type="text" class="form-control" id="namaProdukAwal" placeholder="Pilih..." data-bs-toggle="modal" data-bs-target="#merkAwal" readonly>
                  </div>
                  <div class="col-sm-2">
                    <label>Merk Produk</label>
                    <input type="text" class="form-control" name="merk_awal" id="merkProdukAwal" readonly>
                  </div>
                  <div class="col-sm-3">
                    <label>Grade Produk</label>
                    <input type="text" class="form-control" id="gradeProdukAwal" readonly>
                  </div>
                  <div class="col-sm-1">
                    <label>Stock</label>
                    <input type="text" class="form-control" name="stock_awal" id="stockAwal" readonly>
                  </div>
                  <div class="col-sm-1">
                    <label>Qty</label>
                    <input type="text" class="form-control" name="qty_awal" id="qtyAwal" required>
                  </div>
                </div>
                <br>
                <!-- Merk Akhir -->
                <p style="text-decoration: underline;">Merk Akhir</p>
                <div class="row">
                  <div class="col-sm-5">
                    <label>Nama Produk</label>
                    <input type="hidden" class="form-control" name="id_ganti_merk_in" value="MERK-UPDATE-<?php echo $UUID; ?>">
                    <input type="hidden" class="form-control" name="id_produk_akhir" id="idProdukAkhir">
                    <input type="text" class="form-control" id="namaProdukAkhir" placeholder="Pilih..." data-bs-toggle="modal" data-bs-target="#merkAkhir" readonly>
                  </div>
                  <div class="col-sm-2">
                    <label>Merk Produk</label>
                    <input type="text" class="form-control" name="merk_akhir" id="merkProdukAkhir" readonly>
                  </div>
                  <div class="col-sm-3">
                    <label>Grade Produk</label>
                    <input type="text" class="form-control" id="gradeProdukAkhir" readonly>
                  </div>
                  <div class="col-sm-1">
                    <label>Stock</label>
                    <input type="text" class="form-control" name="stock_akhir" id="stockAkhir" readonly>
                  </div>
                  <div class="col-sm-1">
                    <label>Qty</label>
                    <input type="text" class="form-control" name="qty_akhir" id="qtyAkhir" readonly>
                  </div>
                  <input type="hidden" class="form-control" name="id_user" value="<?php echo $_SESSION['tiket_id']; ?>">
                  <input type="hidden" class="form-control" name="created" id="datetime-input">
                </div>
                <div class="text-center mt-3">
                  <button type="submit" class="btn btn-primary btn-md m-2" name="simpan-reg"><i class="bx bx-save"></i> Simpan Data</button>
                  <a href="ganti-merk-reg.php" class="btn btn-secondary m-2"><i class="bi bi-x-circle"></i> Tutup</a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->
  <!-- Modal Merk Awal -->
  <div class="modal fade" id="merkAwal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Merk Awal</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Pills Tabs -->
          <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#merk-awal-reg" type="button" role="tab" aria-controls="merk-awal-reg" aria-selected="true">Produk Reguler</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#merk-awal-ecat" type="button" role="tab" aria-controls="merk-awal-ecat" aria-selected="false">Produk E-Cat</button>
            </li>
          </ul>
          <div class="tab-content pt-2" id="myTabContent">
            <div class="tab-pane fade show active" id="merk-awal-reg" role="tabpanel" aria-labelledby="home-tab">
              <div class="table-responsive">
                <table class="table table-striped table-bordered" id="table2">
                  <thead>
                    <tr class="text-white" style="background-color: #051683;">
                      <td class="text-center text-nowrap p-3">No</td>
                      <td class="text-center text-nowrap p-3">Kode Produk</td>
                      <td class="text-center text-nowrap p-3">Nama Produk</td>
                      <td class="text-center text-nowrap p-3">Merk</td>
                      <td class="text-center text-nowrap p-3">Grade</td>
                      <td class="text-center text-nowrap p-3">Stock</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    date_default_timezone_set('Asia/Jakarta');

                    include "koneksi.php";
                    $no = 1;
                    $sql = "SELECT
                                tpr.id_grade, 
                                COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa) AS id_produk,
                                COALESCE(tpr.kode_produk, tpsm.kode_set_marwa) AS kode_produk,
                                COALESCE(tpr.nama_produk, tpsm.nama_set_marwa) AS nama_produk,
                                COALESCE(mr_tpr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                                spr.id_stock_prod_reg,
                                spr.stock,
                                tkp.min_stock, 
                                tkp.max_stock,
                                SUBSTRING(COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa), 1, 2) AS substr_id_produk,
                                gr.nama_grade
                            FROM stock_produk_reguler AS spr
                            LEFT JOIN tb_produk_reguler AS tpr ON (tpr.id_produk_reg = spr.id_produk_reg)
                            LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spr.id_kat_penjualan)
                            LEFT JOIN tb_produk_set_marwa AS tpsm ON (tpsm.id_set_marwa = spr.id_produk_reg)
                            LEFT JOIN tb_merk AS mr_tpr ON (tpr.id_merk = mr_tpr.id_merk)
                            LEFT JOIN tb_merk AS mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
                            LEFT JOIN tb_produk_grade gr ON (tpr.id_grade = gr.id_grade)
                            WHERE SUBSTRING(COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa), 1, 2) = 'BR' AND spr.stock > 0
                            ORDER BY nama_produk ASC ";
                    $query = mysqli_query($connect, $sql);
                    while ($data = mysqli_fetch_array($query)) {
                      $stock = $data['stock'];
                      $min_stock = $data['min_stock'];
                    ?>
                      <tr data-idprod="<?php echo $data['id_produk']; ?>" data-namaprod="<?php echo $data['nama_produk']; ?>" data-merkprod="<?php echo $data['nama_merk']; ?>" data-gradeprod="<?php echo $data['nama_grade'] ?>" data-stockprod="<?php echo $data['stock'] ?>" data-bs-dismiss="modal">
                        <td class="text-center"><?php echo $no; ?></td>
                        <td class="text-center"><?php echo $data['kode_produk']; ?></td>
                        <td class="text-start"><?php echo $data['nama_produk']; ?></td>
                        <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                        <td class="text-center"><?php echo $data['nama_grade']; ?></td>
                        <?php
                        if ($stock < $min_stock) {
                          echo "<td class='text-end text-white bg-danger'>" . $data['stock'] . "</td>";
                        } else {
                          echo "<td class='text-end' style='background-color: #7CFC00'>" . number_format($data['stock'], 0, '.', '.') . "</td>";
                        }
                        ?>
                      </tr>
                      <?php $no++; ?>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="tab-pane fade" id="merk-awal-ecat" role="tabpanel" aria-labelledby="profile-tab">
              <div class="table-responsive">
                  <table class="table table-striped table-bordered" id="table3">
                    <thead>
                      <tr class="text-white" style="background-color: #051683;">
                        <td class="text-center text-nowrap p-3">No</td>
                        <td class="text-center text-nowrap p-3">Kode Produk</td>
                        <td class="text-center text-nowrap p-3">Nama Produk</td>
                        <td class="text-center text-nowrap p-3">Merk</td>
                        <td class="text-center text-nowrap p-3">Grade</td>
                        <td class="text-center text-nowrap p-3">Stock</td>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      date_default_timezone_set('Asia/Jakarta');

                      include "koneksi.php";
                      $no = 1;
                      $sql = "SELECT
                                  tpr.id_grade, 
                                  COALESCE(tpr.id_produk_ecat, tpsm.id_set_ecat) AS id_produk,
                                  COALESCE(tpr.kode_produk, tpsm.kode_set_ecat) AS kode_produk,
                                  COALESCE(tpr.nama_produk, tpsm.nama_set_ecat) AS nama_produk,
                                  COALESCE(mr_tpr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                                  spr.id_stock_prod_ecat,
                                  spr.stock,
                                  tkp.min_stock, 
                                  tkp.max_stock,
                                  SUBSTRING(COALESCE(tpr.id_produk_ecat, tpsm.id_set_ecat), 1, 2) AS substr_id_produk,
                                  gr.nama_grade
                              FROM stock_produk_ecat AS spr
                              LEFT JOIN tb_produk_ecat AS tpr ON (tpr.id_produk_ecat = spr.id_produk_ecat)
                              LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spr.id_kat_penjualan)
                              LEFT JOIN tb_produk_set_ecat AS tpsm ON (tpsm.id_set_ecat = spr.id_produk_ecat)
                              LEFT JOIN tb_merk AS mr_tpr ON (tpr.id_merk = mr_tpr.id_merk)
                              LEFT JOIN tb_merk AS mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
                              LEFT JOIN tb_produk_grade gr ON (tpr.id_grade = gr.id_grade)
                              WHERE SUBSTRING(COALESCE(tpr.id_produk_ecat, tpsm.id_set_ecat), 1, 2) = 'BR' AND spr.stock > 0
                              ORDER BY nama_produk ASC ";
                      $query = mysqli_query($connect, $sql);
                      while ($data = mysqli_fetch_array($query)) {
                        $stock = $data['stock'];
                        $min_stock = $data['min_stock'];
                      ?>
                        <tr data-idprod="<?php echo $data['id_produk']; ?>" data-namaprod="<?php echo $data['nama_produk']; ?>" data-merkprod="<?php echo $data['nama_merk']; ?>" data-gradeprod="<?php echo $data['nama_grade'] ?>" data-stockprod="<?php echo $data['stock'] ?>" data-bs-dismiss="modal">
                          <td class="text-center"><?php echo $no; ?></td>
                          <td class="text-center"><?php echo $data['kode_produk']; ?></td>
                          <td class="text-start"><?php echo $data['nama_produk']; ?></td>
                          <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                          <td class="text-center"><?php echo $data['nama_grade']; ?></td>
                          <?php
                          if ($stock < $min_stock) {
                            echo "<td class='text-end text-white bg-danger'>" . $data['stock'] . "</td>";
                          } else {
                            echo "<td class='text-end' style='background-color: #7CFC00'>" . number_format($data['stock'], 0, '.', '.') . "</td>";
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
          <!-- End Pills Tabs -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Modal Merk Awal -->

  <!-- Modal Merk Akhir -->
  <div class="modal fade" id="merkAkhir" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Merk Akhir</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Pills Tabs -->
          <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#merk-akhir-reg" type="button" role="tab" aria-controls="merk-akhir-reg" aria-selected="true">Produk Reguler</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#merk-akhir-ecat" type="button" role="tab" aria-controls="merk-akhir-ecat" aria-selected="false">Produk E-Cat</button>
            </li>
          </ul>
          <div class="tab-content pt-2" id="myTabContent">
            <div class="tab-pane fade show active" id="merk-akhir-reg" role="tabpanel" aria-labelledby="home-tab">
              <div class="table-responsive">
                <table class="table table-striped table-bordered" id="table4">
                  <thead>
                    <tr class="text-white" style="background-color: #051683;">
                      <td class="text-center text-nowrap p-3">No</td>
                      <td class="text-center text-nowrap p-3">Kode Produk</td>
                      <td class="text-center text-nowrap p-3">Nama Produk</td>
                      <td class="text-center text-nowrap p-3">Merk</td>
                      <td class="text-center text-nowrap p-3">Grade</td>
                      <td class="text-center text-nowrap p-3">Stock</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    date_default_timezone_set('Asia/Jakarta');

                    include "koneksi.php";
                    $no = 1;
                    $sql = "SELECT
                                tpr.id_grade, 
                                COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa) AS id_produk,
                                COALESCE(tpr.kode_produk, tpsm.kode_set_marwa) AS kode_produk,
                                COALESCE(tpr.nama_produk, tpsm.nama_set_marwa) AS nama_produk,
                                COALESCE(mr_tpr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                                spr.id_stock_prod_reg,
                                spr.stock,
                                tkp.min_stock, 
                                tkp.max_stock,
                                SUBSTRING(COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa), 1, 2) AS substr_id_produk,
                                gr.nama_grade
                            FROM stock_produk_reguler AS spr
                            LEFT JOIN tb_produk_reguler AS tpr ON (tpr.id_produk_reg = spr.id_produk_reg)
                            LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spr.id_kat_penjualan)
                            LEFT JOIN tb_produk_set_marwa AS tpsm ON (tpsm.id_set_marwa = spr.id_produk_reg)
                            LEFT JOIN tb_merk AS mr_tpr ON (tpr.id_merk = mr_tpr.id_merk)
                            LEFT JOIN tb_merk AS mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
                            LEFT JOIN tb_produk_grade gr ON (tpr.id_grade = gr.id_grade)
                            WHERE SUBSTRING(COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa), 1, 2) = 'BR'
                            ORDER BY nama_produk ASC ";
                    $query = mysqli_query($connect, $sql);
                    while ($data = mysqli_fetch_array($query)) {
                      $stock = $data['stock'];
                      $min_stock = $data['min_stock'];
                    ?>
                      <tr data-idprod="<?php echo $data['id_produk']; ?>" data-namaprod="<?php echo $data['nama_produk']; ?>" data-merkprod="<?php echo $data['nama_merk']; ?>" data-gradeprod="<?php echo $data['nama_grade'] ?>" data-stockprod="<?php echo $data['stock'] ?>" data-bs-dismiss="modal">
                        <td class="text-center"><?php echo $no; ?></td>
                        <td class="text-center"><?php echo $data['kode_produk']; ?></td>
                        <td class="text-start"><?php echo $data['nama_produk']; ?></td>
                        <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                        <td class="text-center"><?php echo $data['nama_grade']; ?></td>
                        <?php
                        if ($stock < $min_stock) {
                          echo "<td class='text-end text-white bg-danger'>" . $data['stock'] . "</td>";
                        } else {
                          echo "<td class='text-end' style='background-color: #7CFC00'>" . number_format($data['stock'], 0, '.', '.') . "</td>";
                        }
                        ?>
                      </tr>
                      <?php $no++; ?>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="tab-pane fade" id="merk-akhir-ecat" role="tabpanel" aria-labelledby="profile-tab">
              <div class="table-responsive">
                  <table class="table table-striped table-bordered" id="table5">
                    <thead>
                      <tr class="text-white" style="background-color: #051683;">
                        <td class="text-center text-nowrap p-3">No</td>
                        <td class="text-center text-nowrap p-3">Kode Produk</td>
                        <td class="text-center text-nowrap p-3">Nama Produk</td>
                        <td class="text-center text-nowrap p-3">Merk</td>
                        <td class="text-center text-nowrap p-3">Grade</td>
                        <td class="text-center text-nowrap p-3">Stock</td>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      date_default_timezone_set('Asia/Jakarta');

                      include "koneksi.php";
                      $no = 1;
                      $sql = "SELECT
                                  tpr.id_grade, 
                                  COALESCE(tpr.id_produk_ecat, tpsm.id_set_ecat) AS id_produk,
                                  COALESCE(tpr.kode_produk, tpsm.kode_set_ecat) AS kode_produk,
                                  COALESCE(tpr.nama_produk, tpsm.nama_set_ecat) AS nama_produk,
                                  COALESCE(mr_tpr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                                  spr.id_stock_prod_ecat,
                                  spr.stock,
                                  tkp.min_stock, 
                                  tkp.max_stock,
                                  SUBSTRING(COALESCE(tpr.id_produk_ecat, tpsm.id_set_ecat), 1, 2) AS substr_id_produk,
                                  gr.nama_grade
                              FROM stock_produk_ecat AS spr
                              LEFT JOIN tb_produk_ecat AS tpr ON (tpr.id_produk_ecat = spr.id_produk_ecat)
                              LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spr.id_kat_penjualan)
                              LEFT JOIN tb_produk_set_ecat AS tpsm ON (tpsm.id_set_ecat = spr.id_produk_ecat)
                              LEFT JOIN tb_merk AS mr_tpr ON (tpr.id_merk = mr_tpr.id_merk)
                              LEFT JOIN tb_merk AS mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
                              LEFT JOIN tb_produk_grade gr ON (tpr.id_grade = gr.id_grade)
                              WHERE SUBSTRING(COALESCE(tpr.id_produk_ecat, tpsm.id_set_ecat), 1, 2) = 'BR'
                              ORDER BY nama_produk ASC ";
                      $query = mysqli_query($connect, $sql);
                      while ($data = mysqli_fetch_array($query)) {
                        $stock = $data['stock'];
                        $min_stock = $data['min_stock'];
                      ?>
                        <tr data-idprod="<?php echo $data['id_produk']; ?>" data-namaprod="<?php echo $data['nama_produk']; ?>" data-merkprod="<?php echo $data['nama_merk']; ?>" data-gradeprod="<?php echo $data['nama_grade'] ?>" data-stockprod="<?php echo $data['stock'] ?>" data-bs-dismiss="modal">
                          <td class="text-center"><?php echo $no; ?></td>
                          <td class="text-center"><?php echo $data['kode_produk']; ?></td>
                          <td class="text-start"><?php echo $data['nama_produk']; ?></td>
                          <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                          <td class="text-center"><?php echo $data['nama_grade']; ?></td>
                          <?php
                          if ($stock < $min_stock) {
                            echo "<td class='text-end text-white bg-danger'>" . $data['stock'] . "</td>";
                          } else {
                            echo "<td class='text-end' style='background-color: #7CFC00'>" . number_format($data['stock'], 0, '.', '.') . "</td>";
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
          <!-- End Pills Tabs -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Modal Merk Akhir -->

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>

</html>

<!-- Numeral JS CDN-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

<script>
  // select Produk Reguler
  $(document).on('click', '#table2 tbody tr', function(e) {
    $('#idProdukAwal').val($(this).data('idprod'));
    $('#namaProdukAwal').val($(this).data('namaprod'));
    $('#merkProdukAwal').val($(this).data('merkprod'));
    $('#gradeProdukAwal').val($(this).data('gradeprod'));
    var stockAwal = parseFloat($(this).data('stockprod'));
    $('#stockAwal').val(numeral($(this).data('stockprod')).format('0,0').replace(',', '.'));
    $('#modalAwal').modal('hide');
  });

  $(document).on('click', '#table3 tbody tr', function(e) {
    $('#idProdukAwal').val($(this).data('idprod'));
    $('#namaProdukAwal').val($(this).data('namaprod'));
    $('#merkProdukAwal').val($(this).data('merkprod'));
    $('#gradeProdukAwal').val($(this).data('gradeprod'));
    var stockAwal = parseFloat($(this).data('stockprod'));
    $('#stockAwal').val(numeral($(this).data('stockprod')).format('0,0').replace(',', '.'));
    $('#modalAwal').modal('hide');
  });

  $(document).on('click', '#table4 tbody tr', function(e) {
    $('#idProdukAkhir').val($(this).data('idprod'));
    $('#namaProdukAkhir').val($(this).data('namaprod'));
    $('#merkProdukAkhir').val($(this).data('merkprod'));
    $('#gradeProdukAkhir').val($(this).data('gradeprod'));
    $('#stockAkhir').val($(this).data('stockprod'));
    $('#modalAkhir').modal('hide');
  });

  $(document).on('click', '#table5 tbody tr', function(e) {
    $('#idProdukAkhir').val($(this).data('idprod'));
    $('#namaProdukAkhir').val($(this).data('namaprod'));
    $('#merkProdukAkhir').val($(this).data('merkprod'));
    $('#gradeProdukAkhir').val($(this).data('gradeprod'));
    $('#stockAkhir').val($(this).data('stockprod'));
    $('#modalAkhir').modal('hide');
  });

  // format number pada input qtyAwal
  document.getElementById('qtyAwal').addEventListener('input', function() {
    var value = parseFloat(this.value.replace(/\D/g, ''));
    this.value = value.toLocaleString('id-ID');

    var qty = parseInt(this.value.replace(/\./g, ''));
    document.getElementById('qtyAkhir').value = qty.toLocaleString('id-ID');
  });

  $(document).on('input', '#qtyAwal', function(e) {
    var stockAwal = parseInt($('#stockAwal').val().replace(/\D/g, ''));
    var qtyAwal = parseInt($(this).val().replace(/\D/g, ''));

    if (qtyAwal > stockAwal) {
      $(this).val(stockAwal.toLocaleString('id-ID'));
      $('#qtyAkhir').val(stockAwal.toLocaleString('id-ID'));
    }
  });

  $(document).on('input', '#qtyAkhir', function(e) {
    var stockAwal = parseInt($('#stockAwal').val().replace(/\D/g, ''));
    var qtyAkhir = parseInt($(this).val().replace(/\D/g, ''));

    if (qtyAkhir > stockAwal) {
      $(this).val(stockAwal.toLocaleString('id-ID'));
    }
  });
</script>

<!-- auto input qty -->
<script type="text/javascript">
  // $("#qtyAwal").keyup(function(){
  //     var qty = parseInt($("#qtyAwal").val());
  //     $("#qtyAkhir").val(qty);
  // });
</script>

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

<!-- Clock js -->
<script>
  function inputDateTime() {
    // Get current date and time
    let currentDate = new Date();

    // Format date and time as yyyy-mm-ddThh:mm:ss
    let year = currentDate.getFullYear();
    let month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
    let day = currentDate.getDate().toString().padStart(2, '0');
    let hours = currentDate.getHours();
    let minutes = currentDate.getMinutes().toString().padStart(2, '0');
    let seconds = currentDate.getSeconds().toString().padStart(2, '0');
    let formattedDateTime = `${day}/${month}/${year}, ${hours}:${minutes}`;

    // Set value of input field to current date and time
    document.getElementById("datetime-input").setAttribute('value', formattedDateTime);

  }

  // Call updateDateTime function every second
  setInterval(inputDateTime, 1000);
</script>