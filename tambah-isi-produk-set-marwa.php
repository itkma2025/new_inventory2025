<?php
require_once "akses.php";
$page = 'produk';
$page2 = 'data-produk-set-marwa';
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
            <h5>Tambah Isi Produk Set Marwa</h5>
          </div>
          <div class="card-body p-3">
            <form action="proses/proses-produk-set-marwa.php" method="post">
              <?php
                $id_set = $_GET['id-set'];
                $UUID = generate_uuid();
                $key = "KM@2024?SET";
                $decrypt_id = decrypt($id_set, $key);
              ?>
              <input type="hidden" class="form-control" name="id_isi_set_marwa" value="BR-SET-MRW-<?php echo $UUID ?>">
              <input type="hidden" class="form-control" name="id_set_marwa" value="<?php echo $decrypt_id ?>">
              <div class="mb-3">
                <div class="row">
                  <div class="col-sm-6">
                    <label>Nama Produk</label>
                    <input type="hidden" class="form-control" name="id_produk" id="idProduk">
                    <input type="text" class="form-control" name="nama_produk" id="namaProduk" placeholder="Pilih..." data-bs-toggle="modal" data-bs-target="#modalBarang" readonly>
                  </div>
                  <div class="col-sm-4">
                    <label>Merk</label>
                    <input type="text" class="form-control" name="merk" id="merkProduk" readonly>
                  </div>
                  <div class="col-sm-2">
                    <label>Qty</label>
                    <input type="text" class="form-control" name="qty" id="qtyInput" required>
                    <input type="hidden" class="form-control" name="id_user" value="<?php echo $_SESSION['tiket_id'] ?>" required>
                  </div>
                  <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn md" name="simpan-isi-set-marwa" id="simpan" disabled><i class="bx bx-save"></i> Simpan</button>
                    <a href="detail-isi-set-marwa.php?detail-id=<?php echo $id_set ?>" class="btn btn-secondary btn md"><i class="bi bi-x"></i> Batal</a>
                  </div>
                </div>
              </div>
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
        <h1 class="modal-title fs-5" id="exampleModalLabel">Data Barang</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="table2">
            <thead>
              <tr class="text-white" style="background-color: #051683;">
                <td class="text-center p-3" style="width: 50px">No</td>
                <td class="text-center p-3" style="width: 350px">Nama Produk</td>
                <td class="text-center p-3" style="width: 100px">Merk</td>
                <td class="text-center p-3" style="width: 80px">Stock</td>
              </tr>
            </thead>
            <tbody>
              <?php
              date_default_timezone_set('Asia/Jakarta');

              include "koneksi.php";
              $no = 1;
              $sql = "SELECT 
                          COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa) AS id_produk,
                          COALESCE(tpr.nama_produk, tpsm.nama_set_marwa) AS nama_produk,
                          COALESCE(mr_tpr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                          spr.id_stock_prod_reg,
                          spr.stock,
                          tkp.min_stock, 
                          tkp.max_stock,
                          SUBSTRING(COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa), 1, 2) AS substr_id_produk
                      FROM  stock_produk_reguler AS spr
                      LEFT JOIN tb_produk_reguler AS tpr ON (tpr.id_produk_reg = spr.id_produk_reg)
                      LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spr.id_kat_penjualan)
                      LEFT JOIN tb_produk_set_marwa AS tpsm ON (tpsm.id_set_marwa = spr.id_produk_reg)
                      LEFT JOIN tb_merk AS mr_tpr ON (tpr.id_merk = mr_tpr.id_merk)
                      LEFT JOIN tb_merk AS mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
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