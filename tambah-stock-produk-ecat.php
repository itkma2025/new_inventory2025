<?php
$page = 'produk';
$page2 = 'data-stock-ecat';
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
    input[type="text"]:read-only {
      background: #e9ecef;
    }

    table {
      cursor: pointer;
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
            <h4>Tambah Data Stock Produk Marwa</h4>
          </div>
          <div class="card-body p-3">
            <form action="proses/proses-stock-ecat.php" method="POST">
              <?php
              $UUID = generate_uuid();
              ?>

              <input type="hidden" class="form-control" name="id_stock_ecat" value="STOCKREG<?php echo $UUID; ?>">
              <div class="row">
                <div class="col-sm-6 mb-3">
                  <label>Nama Produk</label>
                  <input type="hidden" class="form-control" name="id_produk" id="idProduk">
                  <input type="text" class="form-control" name="nama_produk" id="namaProduk" placeholder="Pilih..." data-bs-toggle="modal" data-bs-target="#modalBarang" readonly>
                </div>
                <div class="col-sm-4 mb-3">
                  <label>Merk</label>
                  <input type="hidden" class="form-control" name="id_kat_jual" id="idKatJual" readonly>
                  <input type="text" class="form-control" name="merk" id="merkProduk" readonly>
                </div>
                <div class="col-sm-2 mb-3">
                  <label>Stock Awal</label>
                  <input type="text" class="form-control" name="stock" id="stock" required>
                </div>
              </div>
              <input type="hidden" class="form-control" name="nama_user" value="<?php echo $_SESSION['tiket_nama'] ?>" required>
              <input type="hidden" class="form-control" name="created_date" id="datetime-input" required>
              <div class="mb-3 pt-3 text-end">
                <button type="submit" name="simpan-stock-ecat" class="btn btn-primary btn-md" id="simpan" disabled><i class="bx bx-save"></i> Simpan Data</button>
                <a href="stock-produk-ecat.php" class="btn btn-secondary btn-md"><i class="bi bi-x"></i> Tutup</a>
              </div>
            </form>
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
        <h1 class="modal-title fs-5" id="exampleModalLabel">Data Produk</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="card-body">
          <!-- Pills Tabs -->
          <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Produk Satuan</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Produk Set</button>
            </li>
          </ul>
          <div class="tab-content pt-2" id="myTabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="home-tab">
              <div class="table-responsive">
                <table class="table table-striped table-bordered" id="table2">
                  <thead>
                    <tr class="text-white" style="background-color: #051683;">
                      <td class="text-center text-nowrap p-3" style="width: 50px">No</td>
                      <td class="text-center text-nowrap p-3">Kode Produk</td>
                      <td class="text-center text-nowrap p-3">Nama Produk</td>
                      <td class="text-center text-nowrap p-3">Merk</td>
                      <td class="text-center text-nowrap p-3">Stock</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    date_default_timezone_set('Asia/Jakarta');

                    include "koneksi.php";
                    $no = 1;
                    $sql = "SELECT  pr.id_produk_ecat AS 'produk_id',
                                    pr.kode_produk,
                                    pr.nama_produk,
                                    pr.id_merk,
                                    pr.id_kat_penjualan,
                                    mr.nama_merk,
                                    spr.stock
                            FROM tb_produk_ecat as pr
                            LEFT JOIN tb_merk mr ON (pr.id_merk = mr.id_merk)
                            LEFT JOIN stock_produk_ecat spr ON (pr.id_produk_ecat = spr.id_produk_ecat)
                            WHERE register_value = '0' ";
                    $query = mysqli_query($connect, $sql);
                    while ($data = mysqli_fetch_array($query)) {
                    ?>
                      <tr data-idprod="<?php echo $data['produk_id']; ?>" data-namaprod="<?php echo $data['nama_produk']; ?>" data-merkid="<?php echo $data['id_merk']; ?>" data-katjualid="<?php echo $data['id_kat_penjualan']; ?>" data-merkprod="<?php echo $data['nama_merk']; ?>" data-bs-dismiss="modal">
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
                        <td class="text-center text-nowrap p-3">Kode Produk Set</td>
                        <td class="text-center text-nowrap p-3">Nama Produk Set</td>
                        <td class="text-center text-nowrap p-3">Merk</td>
                        <td class="text-center text-nowrap p-3">Stock</td>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      date_default_timezone_set('Asia/Jakarta');

                      include "koneksi.php";
                      $no = 1;
                      $sql_set = "SELECT 
                                    tpsm.id_set_ecat AS 'produk_id',
                                    tpsm.kode_set_ecat,
                                    tpsm.nama_set_ecat,
                                    tpsm.id_merk,
                                    tpsm.id_kat_penjualan,
                                    mr.nama_merk,
                                    spr.stock
                                FROM tb_produk_set_ecat as tpsm
                                LEFT JOIN tb_merk mr ON (tpsm.id_merk = mr.id_merk)
                                LEFT JOIN stock_produk_ecat spr ON (tpsm.id_set_ecat = spr.id_produk_ecat)
                                WHERE register_value = '0' ";
                      $query_set = mysqli_query($connect, $sql_set);
                      while ($data_set = mysqli_fetch_array($query_set)) {
                      ?>
                        <tr data-idprod="<?php echo $data_set['produk_id']; ?>" data-namaprod="<?php echo $data_set['nama_set_ecat']; ?>" data-merkid="<?php echo $data_set['id_merk']; ?>" data-katjualid="<?php echo $data_set['id_kat_penjualan']; ?>" data-merkprod="<?php echo $data_set['nama_merk']; ?>" data-bs-dismiss="modal">
                          <td class="text-center text-nowrap"><?php echo $no; ?></td>
                          <td class="text-center text-nowrap"><?php echo $data_set['kode_set_ecat']; ?></td>
                          <td class="text-start text-nowrap"><?php echo $data_set['nama_set_ecat']; ?></td>
                          <td class="text-center text-nowrap"><?php echo $data_set['nama_merk']; ?></td>
                          <td class="text-center text-nowrap"><?php echo $data_set['stock']; ?></td>
                        </tr>
                        <?php $no++; ?>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
          </div><!-- End Pills Tabs -->
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

<script>
  // select Produk Reguler
  $(document).on('click', '#table2 tbody tr', function(e) {
    $('#idProduk').val($(this).data('idprod'));
    $('#namaProduk').val($(this).data('namaprod'));
    $('#idKatJual').val($(this).data('katjualid'));
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
    $('#idKatJual').val($(this).data('katjualid'));
    $('#merkProduk').val($(this).data('merkprod'));
    $('#modalBarang').modal('hide');

    // Mengaktifkan tombol
    $('#simpan').prop('disabled', false);
  });
</script>

<script>
  $(document).on('input', '#stock', function(e) {
    var qtyAwal = parseInt($(this).val().replace(/\D/g, ''));
    $(this).val(qtyAwal.toLocaleString('id-ID').replace(',', '.'));
  });
</script>