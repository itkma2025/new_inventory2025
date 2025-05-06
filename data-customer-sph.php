<?php
require_once "akses.php";
$page = 'spcs';
$page2  = 'data-cs-sph';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Inventory KMA</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link rel="stylesheet" href="assets/css/wrap-text.css">
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
    <!-- End Loading -->
    <div class="pagetitle">
      <h1>Data Customer SPH</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Customer SPH</li>
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
            <?php  
              if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") { 
                ?>
                   <a href="#" class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#modal1"><i class="bi bi-plus-circle"></i> Tambah data customer SPH</a>
                <?php 
              }
            ?>
            <div class="table-responsive mt-3">
              <table class="table table-striped table-bordered" id="table1">
                <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <td class="text-center text-nowrap p-3 col-1">No</td>
                    <td class="text-center text-nowrap p-3 col-3">Nama Customer SPH</td>
                    <td class="text-center text-nowrap p-3 col-3">Alamat</td>
                    <td class="text-center text-nowrap p-3 col-2">Telepon</td>
                    <td class="text-center text-nowrap p-3 col-2">Email</td>
                    <?php  
                      if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") { 
                        ?>
                           <td class="text-center text-nowrap p-3 col-2">Aksi</td>
                        <?php 
                      }
                    ?>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  date_default_timezone_set('Asia/Jakarta');
                  include "koneksi.php";
                  $no = 1;
                  $sql = "SELECT * FROM tb_customer_sph ORDER BY nama_cs ASC";
                  $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                  while ($data = mysqli_fetch_array($query)) {
                    $id_cs = encrypt($data['id_cs'], $key_global);
                  ?>
                    <tr>
                      <td class="text-center"><?php echo $no ?></td>
                      <td class="text-nowrap"><?php echo $data['nama_cs']; ?></td>
                      <td class="wrap-text"><?php echo $data['alamat']; ?></td>
                      <td><?php echo $data['no_telp']; ?></td>
                      <td><?php echo $data['email']; ?></td>
                      <?php  
                        if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") { 
                          ?>
                            <td class="text-center text-nowrap">
                              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modal2" data-id="<?php echo $id_cs; ?>" data-nama="<?php echo $data['nama_cs']; ?>" data-alamat="<?php echo $data['alamat']; ?>" data-telp="<?php echo $data['no_telp']; ?>" data-email="<?php echo $data['email']; ?>">
                                <i class="bi bi-pencil"></i>
                              </button>
                              <a href="proses/proses-cs-sph.php?hapus-cs=<?php echo $id_cs ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                            </td>
                          <?php 
                        }
                      ?>
                      <!-- Modal Edit CS -->
                      <div class="modal" id="modal2" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h1 class="modal-title fs-5">Edit Data Customer SPH</h1>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="proses/proses-cs-sph.php" method="POST">
                              <div class="modal-body">
                                <div class="mb-3">
                                  <div class="mb-3">
                                    <label class="form-label">Nama Customer SPH</label>
                                    <input type="hidden" class="form-control" name="id_cs" id="id_cs">
                                    <input type="text" class="form-control" name="nama_cs" id="nama" required>
                                  </div>
                                  <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <input type="text" class="form-control" name="alamat_cs" id="alamat" required>
                                  </div>
                                  <div class="mb-3">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" class="form-control" name="telp_cs" id="telp" required>
                                  </div>
                                  <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="email">
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="submit" name="edit-cs" id="simpan" class="btn btn-primary btn-md" disabled><i class="bx bx-save"></i> Simpan Perubahan</button>
                                  <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal"><i class="bi bi-x"></i> Tutup</button>
                                </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <!-- End Modal Edit CS -->
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

  <?php include "page/script.php" ?>'

  <!-- Modal Add CS -->
  <div class="modal fade" id="modal1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5">Tambah Data Customer SPH</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="proses/proses-cs-sph.php" method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <?php
              $UUID = generate_uuid();
              ?>
              <div class="mb-3">
                <label class="form-label">Nama Custumer SPH</label>
                <input type="hidden" class="form-control" name="id_cs" value="CS-SPH<?php echo $UUID; ?>">
                <input type="text" class="form-control" name="nama_cs" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Alamat</label>
                <input type="text" class="form-control" name="alamat_cs" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Telepon</label>
                <input type="text" class="form-control" name="telp_cs" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email">
                <input type="hidden" class="form-control" name="created" value="<?php echo date('d/m/Y, G:i') ?>">
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="simpan-cs" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Simpan Data</button>
              <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal"><i class="bi bi-x"></i> Tutup</button>
            </div>
        </form>
      </div>
    </div>
  </div>
  <!-- End Modal Add CS -->
</body>

</html>

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


<script>
  // delete button
  $("#table1").on("click", ".delete-button", function() {
    $(this).closest("tr").remove();
    if ($("#table1 tbody tr").length === 0) {
      $("#table1 tbody").append("<tr><td colspan='5' align='center'>Data not found</td></tr>");
    }
  });
</script>

<!-- Modal edit -->

<script>
  $('#modal2').on('show.bs.modal', function(event) {
    // Mendapatkan data dari tombol yang ditekan
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var nama = button.data('nama');
    var alamat = button.data('alamat');
    var telp = button.data('telp');
    var email = button.data('email');
    var modal = $(this);
    var simpanBtn = modal.find('.modal-footer #simpan');
    var namaInput = modal.find('.modal-body #nama');
    var alamatInput = modal.find('.modal-body #alamat');
    var telpInput = modal.find('.modal-body #telp');
    var emailInput = modal.find('.modal-body #email');

    // Menampilkan data
    modal.find('.modal-body #id_cs').val(id);
    namaInput.val(nama);
    alamatInput.val(alamat);
    telpInput.val(telp);
    emailInput.val(email);

    // Pengecekan data, dan buttun disable or enable saat data di ubah
    // dan data kembali ke nilai awal
    var originalNama = namaInput.val();
    var originalAlamat = alamatInput.val();
    var originalTelp = telpInput.val();
    var originalEmail = emailInput.val();

    namaInput.on('input', function() {
      var currentNama = $(this).val();
      var currentAlamat = alamatInput.val();
      var currentTelp = telpInput.val();
      var currentEmail = emailInput.val();

      if (currentNama != originalNama || currentAlamat != originalAlamat || currentTelp != originalTelp || currentEmail != originalEmail) {
        simpanBtn.prop('disabled', false);
      } else {
        simpanBtn.prop('disabled', true);
      }
    });

    alamatInput.on('input', function() {
      var currentAlamat = $(this).val();
      var currentNama = namaInput.val();
      var currentTelp = telpInput.val();
      var currentEmail = emailInput.val();

      if (currentNama != originalNama || currentAlamat != originalAlamat || currentTelp != originalTelp || currentEmail != originalEmail) {
        simpanBtn.prop('disabled', false);
      } else {
        simpanBtn.prop('disabled', true);
      }
    });

    telpInput.on('input', function() {
      var currentTelp = $(this).val();
      var currentNama = namaInput.val();
      var currentAlamat = alamatInput.val();
      var currentEmail = emailInput.val();

      if (currentNama != originalNama || currentAlamat != originalAlamat || currentTelp != originalTelp || currentEmail != originalEmail) {
        simpanBtn.prop('disabled', false);
      } else {
        simpanBtn.prop('disabled', true);
      }
    });

    emailInput.on('input', function() {
      var currentEmail = $(this).val();
      var currentNama = namaInput.val();
      var currentAlamat = alamatInput.val();
      var currentTelp = telpInput.val();

      if (currentNama != originalNama || currentAlamat != originalAlamat || currentTelp != originalTelp || currentEmail != originalEmail) {
        simpanBtn.prop('disabled', false);
      } else {
        simpanBtn.prop('disabled', true);
      }
    });

    modal.find('form').on('reset', function() {
      simpanBtn.prop('disabled', true);
    });
  });
</script>