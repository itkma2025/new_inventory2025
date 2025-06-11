<?php
$page = 'produk';
$page2 = 'data-merk';
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
    <!-- <div class="loader loader">
      <div class="loading">
        <img src="img/loading.gif" width="200px" height="auto">
      </div>
    </div> -->
    <!-- ENd Loading -->
    <div class="pagetitle">
      <h1>Merk Produk</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Merk Produk</li>
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
            <a href="#" class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#modal1"><i class="bi bi-plus-circle"></i> Tambah data merk</a>
            <div class="table-responsive mt-3">
              <table class="table table-striped table-bordered" id="table1">
                <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <td class="text-center p-3 col-1">No</td>
                    <td class="text-center p-3 col-4">Jenis Merk</td>
                    <td class="text-center p-3 col-4">Nama Merk</td>
                    <td class="text-center p-3 col-3">Dibuat Oleh</td>
                    <td class="text-center p-3 col-2">Dibuat Tanggal</td>
                    <td class="text-center p-3 col-2">Aksi</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  date_default_timezone_set('Asia/Jakarta');
                  include "koneksi.php";
                  $no = 1;
                  $sql = "SELECT 
                            mr.id_merk,
                            mr.jenis_merk,
                            mr.nama_merk,
                            mr.created_date,
                            mr.created_by,
                            DATE_FORMAT(mr.created_date, '%d/%m/%Y, %H:%i:%s') AS created_date,  -- Format tanggal Indonesia
                            CASE 
                              WHEN mr.updated_date = '0000-00-00 00:00:00' THEN '-'
                              ELSE DATE_FORMAT(mr.updated_date, '%d/%m/%Y, %H:%i:%s')
                            END AS updated_date,
                            uc.nama_user AS user_created, 
                            uu.nama_user AS user_updated
                          FROM tb_merk AS mr
                          LEFT JOIN $database2.user AS uc ON (mr.created_by = uc.id_user)
                          LEFT JOIN $database2.user AS uu ON (mr.updated_by = uu.id_user)
                          ORDER BY nama_merk";
                  $query = mysqli_query($connect, $sql) or die(mysqli_error($connect, $sql));
                  while ($data = mysqli_fetch_array($query)) {
                    $id_merk = $data['id_merk'];
                  ?>
                    <tr>
                      <td class="text-center"><?php echo $no; ?></td>
                      <td class="text-nowrap text-center"><?php echo ucfirst($data['jenis_merk']); ?></td>
                      <td class="text-nowrap"><?php echo $data['nama_merk']; ?></td>
                      <td class="text-nowrap"><?php echo $data['user_created']; ?></td>
                      <td class="text-nowrap text-center"><?php echo $data['created_date']; ?></td>
                      <td class="text-center text-nowrap">
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modal2" data-id="<?php echo encrypt($data['id_merk'], $key_global); ?>" data-jenis="<?php echo $data['jenis_merk']; ?>" data-nama="<?php echo $data['nama_merk']; ?>">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <a href="proses/proses-merk.php?hapus-merk=<?php echo encrypt($id_merk, $key_global); ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                      </td>
                    </tr>
                    <?php $no++ ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Modal Edit SP -->
    <div class="modal fade" id="modal2" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5">Edit Data Merk</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
            <div class="modal-body">
            <form action="proses/proses-merk.php" method="POST">
              <label class="form-label fw-bold">Jenis Merk</label>
              <div class="mb-3">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="jenis_merk" id="inlineRadio1" value="local">
                  <label class="form-check-label" for="inlineRadio1">Local</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="jenis_merk" id="inlineRadio2" value="import">
                  <label class="form-check-label" for="inlineRadio2">Import</label>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Nama Merk</label>
                <input type="hidden" class="form-control" name="id_merk" id="id_merk">
                <input type="text" class="form-control" name="nama_merk" id="nama_merk" required>
              </div>
              <div class="modal-footer">
                <button type="submit" name="edit-merk" id="simpan" class="btn btn-primary btn-md" disabled><i class="bx bx-save"></i> Simpan Perubahan</button>
                <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal"><i class="bi bi-x"></i> Tutup</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal Edit SP -->
  </main><!-- End #main -->
  <!-- Modal SP -->
  <div class="modal fade" id="modal1" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5">Tambah Data Merk</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="proses/proses-merk.php" method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <?php
                $UUID = generate_uuid();
              ?>
              <label class="form-label fw-bold">Jenis Merk</label>
              <div class="mb-3">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="jenis_merk" id="inlineRadio1" value="local">
                  <label class="form-check-label" for="inlineRadio1">Local</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="jenis_merk" id="inlineRadio2" value="import">
                  <label class="form-check-label" for="inlineRadio2">Import</label>
                </div>
              </div>
              <div class="mb-3 fw-bold">
                <label class="form-label">Nama Merk</label>
                <input type="hidden" class="form-control" name="id_merk" value="MERK<?php echo $UUID; ?>">
                <input type="hidden" class="form-control" name="id_user" value="<?php echo $_SESSION['tiket_id']; ?>">
                <input type="hidden" class="form-control" name="created" value="<?php echo date('d/m/Y, G:i') ?>">
                <input type="text" class="form-control" name="nama_merk" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="simpan-merk" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Simpan Data</button>
              <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal"><i class="bi bi-x"></i> Tutup</button>
            </div>
        </form>
      </div>
    </div>
  </div>
  <!-- End Modal SP -->

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
      $("#table1 tbody").append("<tr><td colspan='5' align='center'>Data not found</td></tr>");
    }
  });
</script>

<!-- Script untuk modal edit -->
<script>
  $('#modal2').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var jenis = button.data('jenis');
    var nama = button.data('nama');

    var modal = $(this);
    var simpanBtn = modal.find('.modal-footer #simpan');
    var namaInput = modal.find('.modal-body #nama_merk');

    // Set nilai awal
    modal.find('.modal-body #id_merk').val(id);
    namaInput.val(nama);

    // Set radio button sesuai jenis
    modal.find('input[name="jenis_merk"]').prop('checked', false);
    modal.find('input[name="jenis_merk"][value="' + jenis + '"]').prop('checked', true);

    // Simpan nilai awal untuk perbandingan
    var originalNama = nama;
    var originalJenis = jenis;

    function checkIfChanged() {
      var currentNama = namaInput.val();
      var currentJenis = modal.find('input[name="jenis_merk"]:checked').val();

      if (currentNama !== originalNama || currentJenis !== originalJenis) {
        simpanBtn.prop('disabled', false);
      } else {
        simpanBtn.prop('disabled', true);
      }
    }

    // Event listener untuk input nama_merk
    namaInput.off('input').on('input', function() {
      checkIfChanged();
    });

    // Event listener untuk perubahan radio button
    modal.find('input[name="jenis_merk"]').off('change').on('change', function() {
      checkIfChanged();
    });

    // Reset tombol saat form di-reset
    modal.find('form').off('reset').on('reset', function() {
      simpanBtn.prop('disabled', true);
    });

    // Set default tombol simpan ke disabled
    simpanBtn.prop('disabled', true);
  });
</script>
<!-- End Script modal edit -->