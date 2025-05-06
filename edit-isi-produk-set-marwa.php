<?php
require_once "akses.php";
$page = 'data';
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
            <h5>Edit Isi Produk Set Marwa</h5>
          </div>
          <div class="card-body p-3">
            <form action="proses/proses-produk-set-marwa.php" method="post">
              <?php
                $key = "KM@2024?SET";
                $id = $_GET['edit-id'];
                $decrypt_id = decrypt($id, $key);
                $sql = "SELECT 
                          ipsm.id_isi_set_marwa,
                          ipsm.id_set_marwa,
                          ipsm.id_produk,
                          ipsm.qty,
                          tpr.nama_produk,
                          tm.nama_merk
                        FROM isi_produk_set_marwa AS ipsm
                        LEFT JOIN tb_produk_reguler AS tpr ON(ipsm.id_produk = tpr.id_produk_reg)
                        LEFT JOIN tb_merk AS tm ON (tpr.id_merk = tm.id_merk)
                        WHERE id_isi_set_marwa = '$decrypt_id'";
                $query = mysqli_query($connect, $sql);
                $data = mysqli_fetch_array($query);
                $id_set_marwa = $data['id_set_marwa'];
                $encrypt_id = encrypt($id_set_marwa, $key);
                $cek_data = mysqli_num_rows($query);
              ?>
              <?php  
                if ($cek_data > 0) {
                  ?>  
                    <input type="hidden" class="form-control" name="id_isi_set_marwa" value="<?php echo $data['id_isi_set_marwa'] ?>">
                    <input type="hidden" class="form-control" name="id_set_marwa" value="<?php echo $data['id_set_marwa'] ?>">
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
                          <button type="submit" class="btn btn-primary btn md" name="edit-isi-set-marwa"><i class="bx bx-save"></i> Simpan</button>
                          <a href="detail-isi-set-marwa.php?detail-id=<?php echo $encrypt_id ; ?>" class="btn btn-secondary btn md"><i class="bi bi-x"></i> Batal</a>
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
        <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
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
              </tr>
            </thead>
            <tbody>
              <?php
              date_default_timezone_set('Asia/Jakarta');

              include "koneksi.php";
              $no = 1;
              $sql = "SELECT pr.*,  
                        mr.*
                        FROM tb_produk_reguler as pr
                        LEFT JOIN tb_merk mr ON (pr.id_merk = mr.id_merk)
                        ";
              $query = mysqli_query($connect, $sql);
              while ($data = mysqli_fetch_array($query)) {
              ?>
                <tr data-idprod="<?php echo $data['id_produk_reg']; ?>" data-namaprod="<?php echo $data['nama_produk']; ?>" data-merkprod="<?php echo $data['nama_merk']; ?>" data-bs-dismiss="modal">
                  <td class="text-center"><?php echo $no; ?></td>
                  <td><?php echo $data['nama_produk']; ?></td>
                  <td class="text-center"><?php echo $data['nama_merk']; ?></td>
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
  });
</script>