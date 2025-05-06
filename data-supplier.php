<?php
$page = 'spcs';
$page2 = 'data-sp';
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
    <div class="loader loader">
      <div class="loading">
        <img src="img/loading.gif" width="200px" height="auto">
      </div>
    </div>
    <!-- ENd Loading -->
    <div class="pagetitle">
      <h1>Data Supplier</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Supplier</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section>
      <!-- SWEET ALERT -->
      <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) {echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
      <!-- END SWEET ALERT -->
      <div class="container-fluid">
        <div class="card">
          <div class="card-body p-3">
            <?php  
              if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") { 
                ?>
                  <a href="#" class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#modal1"><i class="bi bi-plus-circle"></i> Tambah data supllier</a>
                <?php 
              }
            ?>
            <div class="table-responsive mt-3">
              <table class="table table-striped table-bordered" id="table1">
                <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <td class="text-center p-3 col-1">No</td>
                    <td class="text-center text-nowrap p-3 col-3">Nama Supplier</td>
                    <td class="text-center p-3 col-3">Alamat</td>
                    <td class="text-center p-3 col-2">Telepon</td>
                    <td class="text-center p-3 col-2">Email</td>
                    <?php
                      if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") { 
                        ?>
                        <td class="text-center p-3 col-2">Aksi</td>
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
                  $sql = "SELECT 
                            sp.id_sp,
                            sp.nama_sp, 
                            sp.email, 
                            sp.no_telp, 
                            sp.alamat, 
                            DATE_FORMAT(sp.created_date, '%d/%m/%Y, %H:%i:%s') AS created_date,  -- Format tanggal Indonesia
                            sp.created_by,
                            CASE 
                                WHEN sp.updated_date = '0000-00-00 00:00:00' THEN '-'
                                ELSE DATE_FORMAT(sp.updated_date, '%d/%m/%Y, %H:%i:%s')
                            END AS updated_date,
                            sp.updated_by,
                            uc.nama_user AS user_created, 
                            uu.nama_user AS user_updated
                          FROM tb_supplier AS sp 
                          LEFT JOIN $database2.user AS uc ON (sp.created_by = uc.id_user)
                          LEFT JOIN $database2.user AS uu ON (sp.updated_by = uu.id_user)
                          ORDER BY sp.nama_sp ASC";
                  $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                  while ($data = mysqli_fetch_array($query)) {
                    $id_sp = base64_encode($data['id_sp']);
                    $updated_date = !empty($data['updated_date']) ? $data['updated_date'] : '-';
                    $user_updated = !empty($data['user_updated']) ? $data['user_updated'] : '-';
                  ?>
                    <tr>
                      <td class="text-center"><?php echo $no ?></td>
                      <td class="text-nowrap"><?php echo $data['nama_sp']; ?></td>
                      <td><?php echo $data['alamat']; ?></td>
                      <td><?php echo $data['no_telp']; ?></td>
                      <td><?php echo $data['email']; ?></td>
                      <?php
                        if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") { 
                          ?>
                            <td class="text-center">
                              <!-- Button  modal detail -->
                              <button type="button" class="btn btn-primary btn-sm btn-detail" data-bs-toggle="modal" data-bs-target="#detailSp" title="Detail" data-cs="<?php echo $data['nama_sp']; ?>" data-email="<?php echo $data['email']; ?>" data-telp="<?php echo $data['no_telp']; ?>" data-alamat="<?php echo $data['alamat']; ?>" data-createdby="<?php echo $data['user_created']; ?>" data-created="<?php echo $data['created_date']; ?>" data-updated="<?php echo $updated_date; ?>" data-updatedby="<?php echo $user_updated; ?>">
                                <i class="bi bi-eye-fill"></i>
                              </button>
                              <!-- End Modal Detail -->
                              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modal2" data-id="<?php echo $data['id_sp']; ?>" data-nama="<?php echo $data['nama_sp']; ?>" data-alamat="<?php echo $data['alamat']; ?>" data-telp="<?php echo $data['no_telp']; ?>" data-email="<?php echo $data['email']; ?>">
                                <i class="bi bi-pencil"></i>
                              </button>
                              <a href="proses/proses-sp.php?hapus-sp=<?php echo $id_sp ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                            </td>
                          <?php 
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
       <!-- Modal Detail -->
       <div class="modal fade" id="detailSp" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="staticBackdropLabel">Detail Supplier</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                      <tr>
                        <td class="col-4">Nama Customer</td>
                        <td id="dataCs"></td>
                      </tr>
                      <tr>
                        <td class="col-4">Email</td>
                        <td id="dataEmail"></td>
                      </tr>
                      <tr>
                        <td class="col-4">No. Telepon</td>
                        <td id="dataTelp"></td>
                      </tr>
                      <tr>
                        <td class="col-4">Alamat</td>
                        <td id="dataAlamat"></td>
                      </tr>
                      <tr>
                        <td class="col-4">Dibuat Oleh</td>
                        <td id="dataCreatedBy"></td>
                      </tr>
                      <tr>
                        <td class="col-4">Dibuat Tanggal</td>
                        <td id="dataCreated"></td>
                      </tr>
                      <tr>
                        <td class="col-4">Diubah Oleh</td>
                        <td id="dataUpdatedBy"></td>
                      </tr>
                      <tr>
                        <td class="col-4">Diubah Tanggal</td>
                        <td id="dataUpdated"></td>
                      </tr>
                    </table>
               </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
      <!-- End Modal Detail -->

       <!-- Modal Edit SP -->
       <div class="modal fade" id="modal2" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5">Edit Data Supplier</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="proses/proses-sp.php" method="POST">
                <div class="modal-body">
                  <div class="mb-3">
                    <div class="mb-3">
                      <label class="form-label">Nama Supplier</label>
                      <input type="hidden" class="form-control" name="id_sp" id="id_sp">
                      <input type="text" class="form-control" name="nama_sp" id="nama" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Alamat</label>
                      <input type="text" class="form-control" name="alamat_sp" id="alamat" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Telepon</label>
                      <input type="text" class="form-control" name="telp_sp" id="telp" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Email</label>
                      <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <input type="hidden" class="form-control" name="updated" value="<?php echo date('d/m/Y, G:i') ?>">
                  </div>
                  <div class="modal-footer">
                    <button type="submit" name="edit-sp" id="simpan" class="btn btn-primary btn-md" disabled><i class="bx bx-save"></i> Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal"><i class="bi bi-x"></i> Tutup</button>
                  </div>
              </form>
            </div>
          </div>
        </div>
        <!-- End Modal Edit SP -->
    </section>
  </main><!-- End #main -->
  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>

</html>
<!-- Modal Add SP -->
<div class="modal fade" id="modal1" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Tambah Data Supplier</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="proses/proses-sp.php" method="POST">
        <div class="modal-body">
          <div class="mb-3">
            <?php
            $UUID = generate_uuid();
            ?>
            <div class="mb-3">
              <label class="form-label">Nama Supplier</label>
              <input type="hidden" class="form-control" name="id_sp" value="SP<?php echo $UUID; ?>">
              <input type="text" class="form-control" name="nama_sp" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Alamat</label>
              <input type="text" class="form-control" name="alamat_sp" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Telepon</label>
              <input type="text" class="form-control" name="telp_sp" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <input type="hidden" class="form-control" name="created" value="<?php echo date('d/m/Y, G:i') ?>">
          </div>
          <div class="modal-footer">
            <button type="submit" name="simpan-sp" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Simpan Data</button>
            <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal" id="btn-close"><i class="bi bi-x"></i> Tutup</button>
          </div>
      </form>
    </div>
  </div>
</div>
<!-- End Modal Add SP -->

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

<!-- reset form modal -->
<script>
  $(document).ready(function() {
    // Event handler saat tombol "Tutup" di klik
    $('#btn-close').click(function() {
      // Mengosongkan semua input dalam modal
      $('#modal1').find('input[type=text], input[type=email]').val('');
    });
  });
</script>
<!-- End reset form modal -->


<!-- Menampilkan data Modal edit dan disable or enable button -->
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
    modal.find('.modal-body #id_sp').val(id);
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

<script>
  $(document).ready(function() {
    // Inisialisasi DataTables
    var table = $('#table1').DataTable();
    // Event handler untuk mengisi modal saat tombol .btn-detail diklik
    $('.btn-detail').click(function() {
      var cs = $(this).data('cs');
      var email = $(this).data('email');
      var telp = $(this).data('telp');
      var alamat = $(this).data('alamat');
      var createdby = $(this).data('createdby');
      var created = $(this).data('created');
      var updatedby = $(this).data('updatedby');
      var updated = $(this).data('updated');

      $('#dataCs').text(cs);
      $('#dataEmail').text(email);
      $('#dataTelp').text(telp);
      $('#dataAlamat').text(alamat);
      $('#dataCreatedBy').text(createdby);
      $('#dataCreated').text(created);
      $('#dataUpdatedBy').text(updatedby);
      $('#dataUpdated').text(updated);

      $('#detailSp').modal('show'); // Menggunakan ID modal yang benar
    });
    // Event handler untuk memperbarui modal saat DataTables menggambar ulang (pindah halaman)
    table.on('draw.dt', function() {
      // Memperbarui event handler .btn-detail untuk data yang baru dimuat
      $('.btn-detail').off('click'); // Menghapus event handler yang ada
      $('.btn-detail').on('click', function() {
        var cs = $(this).data('cs');
        var email = $(this).data('email');
        var telp = $(this).data('telp');
        var alamat = $(this).data('alamat');
        var createdby = $(this).data('createdby');
        var created = $(this).data('created');
        var updatedby = $(this).data('updatedby');
        var updated = $(this).data('updated');

        $('#dataCs').text(cs);
        $('#dataEmail').text(email);
        $('#dataTelp').text(telp);
        $('#dataAlamat').text(alamat);
        $('#dataCreatedBy').text(createdby);
        $('#dataCreated').text(created);
        $('#dataUpdatedBy').text(updatedby);
        $('#dataUpdated').text(updated);

        $('#detailSp').modal('show'); // Menggunakan ID modal yang benar
      });
    });
  });
</script>
