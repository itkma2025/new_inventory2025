<?php
$page = 'keterangan';
$page2 = 'cashback';
require_once "akses.php";
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
      <h1>Keterangan Cashback</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Katerangan Cashback</li>
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
            <a href="#" class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#modal1"><i
                class="bi bi-plus-circle"></i> Tambah Data Keterangan Cashback</a>
            <div class="table-responsive mt-3">
              <table class="table table-striped table-bordered" id="table1">
                <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <td class="text-center p-3">No</td>
                    <td class="text-center p-3" style="width: 300px">Nama Keterangan</td>
                    <td class="text-center p-3">Dibuat Tanggal</td>
                    <td class="text-center p-3">Dibuat Oleh</td>
                    <td class="text-center p-3">Diubah Tanggal</td>
                    <td class="text-center p-3">Diubah Oleh</td>
                    <td class="text-center p-3">Status</td>
                    <td class="text-center p-3">Aksi</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  date_default_timezone_set('Asia/Jakarta');
                  require_once "function/function-enkripsi.php";
                  $no = 1;
                  $sql = "SELECT 
                              ket_cb.id_ket_cashback,
                              ket_cb.ket_cashback,
                              ket_cb.status_aktif,
                              ket_cb.created_date,
                              ket_cb.created_by,
                              ket_cb.updated_date,
                              ket_cb.updated_by,
                              us_created.nama_user AS user_created,
                              us_updated.nama_user AS user_updated
                            FROM keterangan_cashback AS ket_cb
                            LEFT JOIN $database2.user us_created ON (ket_cb.created_by = us_created.id_user)
                            LEFT JOIN $database2.user us_updated ON (ket_cb.updated_by = us_updated.id_user)
                            ORDER BY ket_cashback ASC";
                  $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                  while ($data = mysqli_fetch_array($query)) {
                    $id_ket_cb = encrypt($data['id_ket_cashback'], $key_global);
                    $status = $data['status_aktif'];
                    $checked = ($status == 1) ? 'checked' : '';
                    $title_status = ($status == "1") ? 'Aktif' : 'Non Aktif';
                    $updated_date = "";
                    $updated_by = "";
                    if ($data['updated_date'] == '0000-00-00 00:00:00') {
                      $updated_date = "Tidak ada perubahan";
                      $updated_by = "Tidak ada perubahan";
                    } else {
                      $updated_date = date('d/m/Y H:i:s', strtotime($data['updated_date']));
                      $updated_by = $data['user_updated'];
                    }
                  
                  ?>
                    <tr>
                      <td class="text-center"><?php echo $no; ?></td>
                      <td class="text-start"><?php echo $data['ket_cashback'] ?></td>
                      <td class="text-center"><?php echo date('d/m/Y H:i:s', strtotime($data['created_date'])) ?></td>
                      <td class="text-center"><?php echo $data['user_created'] ?></td>
                      <td class="text-center"><?php echo $updated_date; ?></td>
                      <td class="text-center"><?php echo $updated_by; ?></td>
                      <td class="text-nowrap text-center">
                        <div class="form-check form-switch" style="display: inline-block !important;"
                          title="<?php echo $title_status  ?>">
                          <input class="form-check-input" type="checkbox" role="switch"
                            id="flexSwitchCheckChecked<?php echo $id_ket_cb; ?>" <?php echo $checked ?>
                            data-id="<?php echo $id_ket_cb; ?>">
                        </div>
                      </td>
                      <td class="text-center text-nowrap">
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modal2"
                          data-id="<?php echo encrypt($data['id_ket_cashback'], $key_global); ?>" data-nama="<?php echo $data['ket_cashback']; ?>">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <a href="proses/proses-ket-cashback.php?hapus-ket-cb=<?php echo encrypt($data['id_ket_cashback'], $key_global) ?>"
                          class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                      </td>
                      <!-- Modal Edit -->
                      <div class="modal fade" id="modal2" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h1 class="modal-title fs-5">Edit Data Kategori Produk</h1>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="proses/proses-ket-cashback.php" method="POST">
                              <div class="modal-body">
                                <div class="mb-3">
                                  <label class="form-label">Nama Kategori Produk</label>
                                  <input type="hidden" class="form-control" name="id_ket_cb" id="id_ket_cb">
                                  <input type="text" class="form-control" name="nama_ket_cb" id="ket_cb" required>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" name="edit-ket-cb" id="simpan" disabled
                                  class="btn btn-primary btn-md"><i class="bx bx-save"></i> Simpan Perubahan</button>
                                <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal"><i
                                    class="bi bi-x"></i> Tutup</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <!-- End Modal Edit -->
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
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>
</html>
<!-- Modal Add -->
<div class="modal fade" id="modal1" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5">Tambah Data Keterangan Cashback</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="proses/proses-ket-cashback.php" method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <?php
              $UUID = generate_uuid();
              ?>
              <div class="mb-3">
                <label class="form-label">Nama Keterangan Cashback</label>
                <input type="hidden" class="form-control" name="id_ket_cb" value="KET-CB-<?php echo $UUID; ?>">
                <input type="text" class="form-control" name="nama_ket_cb" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="simpan-ket-cb" class="btn btn-primary btn-md"><i class="bx bx-save"></i>
                Simpan Data</button>
              <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal"><i class="bi bi-x"></i>
                Tutup</button>
            </div>
        </form>
      </div>
    </div>
  </div>
  <!-- End Modal Add -->

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

<!-- Script untuk modal edit -->
<script>
  $('#modal2').on('show.bs.modal', function(event) {
    // Menampilkan data
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var nama = button.data('nama');
    var modal = $(this);
    var simpanBtn = modal.find('.modal-footer #simpan');
    var namaInput = modal.find('.modal-body #ket_cb');

    modal.find('.modal-body #id_ket_cb').val(id);
    namaInput.val(nama);

    // Pengecekan data, dan buttun disable or enable saat data di ubah
    // dan data kembali ke nilai awal
    var originalNama = namaInput.val();

    namaInput.on('input', function() {
      var currentNama = $(this).val();

      if (currentNama != originalNama) {
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
<!-- End Script modal edit -->

<script>
  $(document).ready(function() {
    // Inisialisasi DataTable
    $('#table1').DataTable();

    // Tangani perubahan pada checkbox
    $(document).on('change', '.form-check-input[type="checkbox"]', function() {
      var id = $(this).data('id');
      var isChecked = $(this).is(':checked') ? 1 : 0;

      console.log('Checkbox ID:', id);
      console.log('Checked Status:', isChecked);

      $.ajax({
        url: 'proses/update-status-ket-in.php',
        type: 'POST',
        data: {
          id_ket_cb: id,
          status_aktif: isChecked
        },
        success: function(response) {
          console.log('Status updated:', response);
        },
        error: function(xhr, status, error) {
          console.error('Error updating status:', error);
        }
      });
    });
  });
</script>