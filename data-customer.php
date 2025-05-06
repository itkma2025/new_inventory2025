<?php
$page = 'spcs';
$page2  = 'data-cs';
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
  <link rel="stylesheet" href="assets/css/wrap-text.css">
  <link rel="stylesheet" href="assets/css/button-file-upload.css">
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
      <h1>Data Customer</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Customer</li>
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
              <a href="#" class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#modal1"><i class="bi bi-plus-circle"></i> Tambah data customer</a>
            <?php
            }
            ?>

            <div class="table-responsive mt-3">
              <table class="table table-striped table-bordered" id="table1">
                <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <td class="text-center text-nowrap p-3" style="width: 60px;">No</td>
                    <td class="text-center text-nowrap p-3" style="width: 350px;">Nama Customer</td>
                    <td class="text-center text-nowrap p-3" style="width: 450px;">Alamat</td>
                    <td class="text-center text-nowrap p-3" style="width: 180px;">Telepon</td>
                    <td class="text-center text-nowrap p-3" style="width: 250px;">Email</td>
                    <td class="text-center text-nowrap p-3" style="width: 200px;">No. NPWP</td>
                    <?php
                      if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") {
                        ?>
                          <td class="text-center text-nowrap p-3" style="width: 150px;">Status</td>
                          <td class="text-center text-nowrap p-3" style="width: 120px;">Aksi</td>
                        <?php
                      }
                    ?>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  date_default_timezone_set('Asia/Jakarta');
                  require_once "function/encrypt-decrypt-file.php";
                  $no = 1;
                  $sql = "SELECT 
                            cs.jenis_usaha,
                            cs.id_cs,
                            cs.nama_cs, 
                            cs.email, 
                            cs.nama_cp,
                            cs.no_telp, 
                            cs.alamat,  
                            cs.npwp,
                            cs.npwp_img,
                            cs.status_aktif,
                            DATE_FORMAT(cs.created_date, '%d/%m/%Y, %H:%i:%s') AS created_date,  -- Format tanggal Indonesia
                            CASE 
                                WHEN cs.updated_date = '0000-00-00 00:00:00' THEN '-'
                                ELSE DATE_FORMAT(cs.updated_date, '%d/%m/%Y, %H:%i:%s')
                            END AS updated_date,
                            uc.nama_user AS user_created, 
                            uu.nama_user AS user_updated
                          FROM tb_customer AS cs 
                          LEFT JOIN $database2.user AS uc ON (cs.created_by = uc.id_user)
                          LEFT JOIN $database2.user AS uu ON (cs.updated_by = uu.id_user)
                          ORDER BY cs.nama_cs ASC";
                  $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                  while ($data = mysqli_fetch_array($query)) {
                    $id_cs = base64_encode($data['id_cs']);
                    $nama_cs = $data['nama_cs'];
                    $email = !empty($data['email']) ? $data['email'] : '-';
                    $npwp = !empty($data['npwp']) ? $data['npwp'] : '-';
                    $status = $data['status_aktif'];
                    $checked = ($status == 1) ? 'checked' : '';
                    $title_status = ($status == "1") ? 'Aktif' : 'Non Aktif';
                    $updated_date = !empty($data['updated_date']) ? $data['updated_date'] : '-';
                    $user_updated = !empty($data['user_updated']) ? $data['user_updated'] : '-';
                    $img_npwp = $data['npwp_img']; // Nama file gambar
                    $img_src = 'display_npwp.php?file=' . urlencode($img_npwp) . '&customer=' . urlencode($nama_cs); // URL dinamis untuk gambar   

                  ?>
                    <tr>
                      <td class="text-center"><?php echo $no ?></td>
                      <td class="text-nowrap"><?php echo $data['nama_cs']; ?></td>
                      <td class="wrap-text"><?php echo $data['alamat']; ?></td>
                      <td class="text-nowrap"><?php echo $data['no_telp']; ?></td>
                      <td class="text-nowrap text-center"><?php echo $email ?></td>
                      <td class="text-nowrap text-center"><?php echo $npwp; ?></td>
                      <?php
                        if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") {
                          ?>
                            <td class="text-nowrap text-center">
                              <div class="form-check form-switch" style="display: inline-block !important;" title="<?php echo $title_status  ?>">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked<?php echo $id_cs; ?>" <?php echo $checked ?> data-id="<?php echo $id_cs; ?>">
                              </div>
                            </td>
                            <td class="text-center text-nowrap">
                              <!-- Button  modal detail -->
                              <button type="button" class="btn btn-primary btn-sm btn-detail" data-bs-toggle="modal" data-bs-target="#detailCs" title="Detail" data-jenis="<?php echo $data['jenis_usaha']; ?>" data-cs="<?php echo $data['nama_cs']; ?>" data-email="<?php echo $email; ?>" data-cp="<?php echo $data['nama_cp']; ?>" data-telp="<?php echo $data['no_telp']; ?>" data-alamat="<?php echo $data['alamat']; ?>" data-npwp="<?php echo $npwp; ?>" data-npwp-img="<?php echo $data['npwp_img']; ?>" data-createdby="<?php echo $data['user_created']; ?>" data-created="<?php echo $data['created_date']; ?>" data-updated="<?php echo  $updated_date; ?>" data-updatedby="<?php echo $user_updated; ?>">
                                <i class="bi bi-eye-fill"></i>
                              </button>
                              <!-- End Modal Detail -->
                              <!-- Modal Edit -->
                              <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modal2" data-jenis="<?php echo $data['jenis_usaha']; ?>" data-id="<?php echo $data['id_cs']; ?>" data-nama="<?php echo $data['nama_cs']; ?>" data-alamat="<?php echo $data['alamat']; ?>" data-cp="<?php echo $data['nama_cp']; ?>" data-telp="<?php echo $data['no_telp']; ?>" data-email="<?php echo $data['email']; ?>" data-npwp="<?php echo $data['npwp']; ?>" data-npwp-img="<?php echo $data['npwp_img']; ?>" title="Edit Data">
                                <i class="bi bi-pencil"></i>
                              </button>
                              <!-- End Modal Edit -->

                              <?php
                              if ($role == "Super Admin") {
                              ?>
                                <a href="proses/proses-cs.php?hapus-cs=<?php echo $id_cs ?>" class="btn btn-danger btn-sm delete-data" data-bs-delay="0" title="Hapus Data"><i class="bi bi-trash"></i></a>
                              <?php
                              }
                              ?>
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
      <div class="modal fade animate__animated animate__zoomInDown" id="detailCs" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="staticBackdropLabel">Detail Customer</h1>
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
                    <td class="col-4">Jenis Usaha</td>
                    <td id="jenisUsaha"></td>
                  </tr>
                  <tr>
                    <td class="col-4">Email</td>
                    <td id="dataEmail"></td>
                  </tr>
                  <tr>
                    <td class="col-4">Nama Contact Person</td>
                    <td id="namaCp"></td>
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
                    <td class="col-4">No. NPWP</td>
                    <td id="npwp"></td>
                  </tr>
                  <tr>
                    <td class="col-4">Kartu NPWP</td>
                    <td>
                      <button id="viewNpwp" class="btn btn-secondary btn-sm ms-2">
                        <i class="bi bi-eye"></i> Lihat Kartu NPWP
                      </button>
                      <span id="npwpMessage" style="display: none; color: orange;">File tidak ada</span>
                      <div id="npwpImageContainer" style="display: none;">
                        <img id="npwpImage" src="" alt="NPWP Image" class="img-fluid mt-2" oncontextmenu="return false;">
                      </div>
                    </td>
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
      <!-- Modal Edit CS -->
      <div class="modal fade fade animate__animated animate__zoomIn" data-bs-backdrop="static" data-bs-keyboard="false" id="modal2">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5">Edit Data Customer</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form action="proses/proses-cs.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" class="form-control" id="id">
                <div class="mb-3">
                  <label for="jenisUsaha" class="form-label">Jenis Usaha :</label><br>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_usaha" id="jenisUsaha" value="Perorangan">
                    <label class="form-check-label">Perorangan</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_usaha" id="jenisUsaha" value="Perusahaan">
                    <label class="form-check-label">Perusahaan</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_usaha" id="jenisUsaha" value="Toko">
                    <label class="form-check-label">Toko</label>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Nama Peusahaan / Toko</label>
                  <input type="hidden" class="form-control" name="id_cs" id="id">
                  <input type="text" class="form-control" name="nama_cs" id="nama" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Alamat</label>
                  <input type="text" class="form-control" name="alamat_cs" id="alamat" required>
                </div>
                <div class="mb3">
                  <label>Nama Contact Person</label>
                  <input type="text" class="form-control" name="nama_cp" id="namaCp">
                </div>
                <div class="mb-3">
                  <label class="form-label">Telepon</label>
                  <input type="text" class="form-control" name="telp_cs" id="telp" maxlength="13" required>
                  <small class="form-text text-muted">Masukkan hanya angka (maksimal 13 digit).</small>
                </div>
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" name="email" id="email">
                  <input type="hidden" class="form-control" name="updated" value="<?php echo date('d/m/Y, G:i') ?>">
                </div>
                <div class="mb-3">
                  <label>No. NPWP</label>
                  <input type="text" class="form-control" id="npwp" name="npwp" maxlength="20">
                </div>
                <div class="preview-image mb-3">
                  <input type="hidden" id="npwpImageEdit" name="ket_img" value="Tidak Diubah">
                  <input type="hidden" id="imgNpwp" name="img_npwp">
                  <img id="imagePreviewEdit" alt="Preview Image" style="display:none;">
                  <p id="imageSizeEdit" style="display:none;"></p>
                </div>
                <div class="mb-3">
                  <div class="input-group">
                    <div class="fileUpload btn btn-primary">
                      <span id="uploadButtonTextEdit"><i class="bi bi-upload"></i> Upload NPWP</span>
                      <input class="upload" type="file" name="fileku" id="formFileEdit" accept=".jpg, .png, .jpeg" onchange="compressImage(event, 'Edit')">
                    </div>
                    <div class="fileUpload btn btn-danger" id="resetButtonEdit">
                      <span><i class="bi bi-arrow-repeat"></i> Reset File</span>
                      <input type="text" class="upload">
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" name="edit-cs" id="edit" class="btn btn-primary btn-md" disabled><i class="bx bx-save"></i> Simpan Perubahan</button>
                  <a href="data-customer.php" class="btn btn-secondary btn-md"><i class="bi bi-x"></i> Tutup</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- End Modal Edit CS -->
    </section>
  </main><!-- End #main -->

  <!-- Footer -->
  <?php include "page/footer.php" ?>  
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>

</html>
<!-- Modal Add CS -->
<div class="modal fade animate__animated animate__fadeInUpBig" id="modal1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Tambah Data Customer</h1>
      </div>
      <form action="proses/proses-cs.php" method="POST" id="uploadForm" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="mb-3">
            <?php
            $UUID = generate_uuid();
            ?>
            <input type="hidden" class="form-control" name="id_cs" value="CS<?php echo $UUID; ?>">
            <div class="mb-3">
              <label class="fw-bold">Jenis Usaha :</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="jenis_usaha" value="Perorangan" required>
                <label class="form-check-label">Perorangan</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="jenis_usaha" value="Perusahaan" required>
                <label class="form-check-label">Perusahaan</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="jenis_usaha" value="Toko" required>
                <label class="form-check-label">Toko</label>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Nama Perusahaan / Toko</label>
              <input type="text" class="form-control" name="nama_cs" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Alamat</label>
              <input type="text" class="form-control" name="alamat_cs" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Nama Contact Person</label>
              <input type="text" class="form-control" name="nama_cp" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Telepon</label>
              <input type="text" class="form-control" name="telp_cs" id="telp_cs" pattern="\d*" maxlength="13" required>
              <small class="form-text text-muted">Masukkan hanya angka (maksimal 13 digit).</small>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Email</label>
              <input type="email" class="form-control" name="email">
              <input type="hidden" class="form-control" name="created" value="<?php echo date('d/m/Y, G:i') ?>">
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">No. NPWP</label>
              <input type="text" class="form-control" name="npwp" maxlength="20">
            </div>
            <div class="preview-image mb-3">
              <img id="imagePreviewAdd" src="#" alt="Preview Image" style="display:none;">
              <p id="imageSizeAdd" style="display:none;"></p>
            </div>
            <div class="mb-3">
              <div class="input-group">
                <div class="fileUpload btn btn-primary">
                  <span id="uploadButtonTextAdd"><i class="bi bi-upload"></i> Upload NPWP</span>
                  <input class="upload" type="file" name="fileku" id="formFileAdd" accept=".jpg, .png, .jpeg" onchange="compressImage(event, 'Add')">
                </div>
                <div class="fileUpload btn btn-danger" id="resetButtonAdd">
                  <span><i class="bi bi-arrow-repeat"></i> Reset File</span>
                  <input type="text" class="upload">
                </div>
              </div>
            </div>
          </div>
          <div id="loading">Compressing...</div>
          <div class="modal-footer">
            <button type="submit" name="simpan-cs" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Simpan Data</button>
            <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal" id="btn-close"><i class="bi bi-x"></i> Tutup</button>
          </div>
      </form>
    </div>
  </div>
</div>
<!-- End Modal Add CS -->


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
  var inputElement = document.getElementById('telp_cs');

  // Menambahkan event listener untuk memeriksa panjang input
  inputElement.addEventListener('input', function(event) {
    // Menghapus karakter selain angka
    this.value = this.value.replace(/\D/g, '');

    // Memeriksa panjang input
    if (this.value.length < 9) {
      inputElement.setCustomValidity('Nomor telepon harus minimal 9 angka.');
    } else {
      inputElement.setCustomValidity('');
    }

    // Memastikan panjang input tidak melebihi 13 karakter
    if (this.value.length > 13) {
      this.value = this.value.slice(0, 13);
    }
  });
</script>
<script>
  var inputElement = document.getElementById('telp');

  // Menambahkan event listener untuk memeriksa panjang input
  inputElement.addEventListener('input', function(event) {
    // Menghapus karakter selain angka
    this.value = this.value.replace(/\D/g, '');

    // Memeriksa panjang input
    if (this.value.length < 9) {
      inputElement.setCustomValidity('Nomor telepon harus minimal 9 angka.');
    } else {
      inputElement.setCustomValidity('');
    }

    // Memastikan panjang input tidak melebihi 13 karakter
    if (this.value.length > 13) {
      this.value = this.value.slice(0, 13);
    }
  });
</script>


<script>
  $('#modal2').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var jenis = button.data('jenis');
    var id = button.data('id');
    var nama = button.data('nama');
    var alamat = button.data('alamat');
    var cp = button.data('cp');
    var telp = button.data('telp');
    var email = button.data('email');
    var npwp = button.data('npwp');
    var npwpImg = button.data('npwp-img');

    var modal = $(this);
    var editBtn = modal.find('.modal-footer #edit');
    var jenisInput = modal.find('.modal-body input[name="jenis_usaha"]');
    var idInput = modal.find('.modal-body #id');
    var namaInput = modal.find('.modal-body #nama');
    var alamatInput = modal.find('.modal-body #alamat');
    var cpInput = modal.find('.modal-body #namaCp');
    var telpInput = modal.find('.modal-body #telp');
    var emailInput = modal.find('.modal-body #email');
    var npwpInput = modal.find('.modal-body #npwp');
    var imgNpwp = modal.find('.modal-body #imagePreviewEdit');
    var npwpImageEdit = modal.find('.modal-body #npwpImageEdit');
    var cekImg = modal.find('.modal-body #imgNpwp');

    // Menampilkan data
    jenisInput.filter('[value="' + jenis + '"]').prop('checked', true);
    idInput.val(id);
    namaInput.val(nama);
    alamatInput.val(alamat);
    cpInput.val(cp);
    telpInput.val(telp);
    emailInput.val(email);
    npwpInput.val(npwp);
    cekImg.val(npwpImg);

    // Menampilkan gambar NPWP jika ada
    if (npwpImg) {
      var imageUrl = 'display_npwp.php?file=' + encodeURIComponent(npwpImg) + '&customer=' + encodeURIComponent(nama);
      imgNpwp.attr('src', imageUrl);
      imgNpwp.show(); // Tampilkan gambar
    } else {
      imgNpwp.attr('src', '#'); // Mengatur src menjadi '#' jika tidak ada gambar
      imgNpwp.hide(); // Sembunyikan gambar jika tidak ada
    }

    // Mendapatkan elemen gambar dan menyimpan nilai display original
    var image = document.getElementById('imagePreviewEdit');
    var originalDisplay = window.getComputedStyle(image).display;

    // Fungsi untuk memeriksa gaya display
    function checkDisplayStyle() {
      var displayStyle = window.getComputedStyle(image).display;

      // Update value npwpImageEdit jika display berubah
      if (displayStyle !== originalDisplay) {
        npwpImageEdit.val('Diubah');
      } else {
        npwpImageEdit.val('Tidak Diubah');
      }
    }

    // Inisialisasi MutationObserver untuk memantau perubahan gaya
    var observer = new MutationObserver((mutationsList) => {
      for (var mutation of mutationsList) {
        if (mutation.attributeName === 'style') {
          checkDisplayStyle();
          checkInputChanges();
        }
      }
    });

    // Mulai mengamati perubahan atribut 'style'
    observer.observe(image, {
      attributes: true
    });

    // Fungsi untuk memeriksa perubahan pada input
    function checkInputChanges() {
      var currentJenis = jenisInput.filter(':checked').val();
      var currentNama = namaInput.val();
      var currentAlamat = alamatInput.val();
      var currentTelp = telpInput.val();
      var currentEmail = emailInput.val();
      var currentCp = cpInput.val();
      var currentNpwp = npwpInput.val();
      var currentDisplay = window.getComputedStyle(image).display;

      // Membandingkan nilai current dengan nilai original
      if (
        currentJenis !== originalJenis ||
        currentNama !== originalNama ||
        currentAlamat !== originalAlamat ||
        currentTelp !== originalTelp ||
        currentEmail !== originalEmail ||
        currentCp !== originalCp ||
        currentNpwp !== originalNpwp ||
        currentDisplay !== originalDisplay
      ) {
        editBtn.prop('disabled', false);
      } else {
        editBtn.prop('disabled', true);
      }

      // Perbarui npwpImageEdit berdasarkan perubahan
      checkDisplayStyle();
    }

    // Mendapatkan nilai original dari setiap input
    var originalJenis = jenisInput.filter(':checked').val();
    var originalNama = namaInput.val();
    var originalAlamat = alamatInput.val();
    var originalTelp = telpInput.val();
    var originalEmail = emailInput.val();
    var originalCp = cpInput.val();
    var originalNpwp = npwpInput.val();

    // Menambahkan elemen input jenis ke dalam array inputFields
    var inputFields = [jenisInput, namaInput, alamatInput, telpInput, emailInput, cpInput, npwpInput];

    // Menambahkan event listener untuk setiap input
    inputFields.forEach(function(field) {
      field.on('input', function() {
        checkInputChanges();
      });
    });

    // Menampilkan log awal
    checkDisplayStyle();

    modal.find('form').on('reset', function() {
      editBtn.prop('disabled', true);
    });
  });
</script>





<!-- Script Detail Customer -->
<script>
  $(document).ready(function() {
    $(document).on('contextmenu', 'img', function(e) {
      e.preventDefault();
    });
    var table = $('#table1').DataTable();

    // Event handler untuk mengisi modal saat tombol .btn-detail diklik
    $('.btn-detail').click(function() {
      var jenis = $(this).data('jenis');
      var cs = $(this).data('cs');
      var email = $(this).data('email');
      var cp = $(this).data('cp');
      var telp = $(this).data('telp');
      var alamat = $(this).data('alamat');
      var npwp = $(this).data('npwp');
      var npwpImg = $(this).data('npwp-img'); // Ambil data-npwp-img

      // Debug log
      console.log('npwpImg:', npwpImg);

      var createdby = $(this).data('createdby');
      var created = $(this).data('created');
      var updatedby = $(this).data('updatedby');
      var updated = $(this).data('updated');

      $('#dataCs').text(cs);
      $('#jenisUsaha').text(jenis);
      $('#dataEmail').text(email);
      $('#namaCp').text(cp);
      $('#dataTelp').text(telp);
      $('#dataAlamat').text(alamat);
      $('#npwp').text(npwp);
      $('#dataCreatedBy').text(createdby);
      $('#dataCreated').text(created);
      $('#dataUpdatedBy').text(updatedby);
      $('#dataUpdated').text(updated);

      // Menampilkan atau menyembunyikan tombol View Kartu NPWP
      if (npwpImg) {
        var imageUrl = 'display_npwp.php?file=' + encodeURIComponent(npwpImg) + '&customer=' + encodeURIComponent(cs);
        $('#viewNpwp').data('npwp-img', npwpImg).show();
        $('#npwpMessage').hide();
      } else {
        $('#viewNpwp').hide();
        $('#npwpMessage').show();
      }

      $('#detailCs').modal('show'); // Menampilkan modal detail
    });

    // Event handler untuk tombol View NPWP di dalam modal
    $(document).on('click', '#viewNpwp', function() {
      var npwpImg = $(this).data('npwp-img'); // Ambil data-npwp-img
      if (npwpImg) {
        var imageUrl = 'display_npwp.php?file=' + encodeURIComponent(npwpImg) + '&customer=' + encodeURIComponent($('#dataCs').text());

        // Buat modal baru untuk gambar
        var imageModal = `
              <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                          <div class="modal-header">
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body text-center">
                              <img src="${imageUrl}" class="img-fluid" alt="NPWP Image"/>
                          </div>
                      </div>
                  </div>
              </div>
          `;

        // Tambahkan modal ke body
        $('body').append(imageModal);

        // Tampilkan modal
        $('#imageModal').modal('show');

        // Hapus modal setelah ditutup
        $('#imageModal').on('hidden.bs.modal', function() {
          $(this).remove();
        });
      } else {
        console.error('Gambar NPWP tidak ditemukan.');
      }
    });

    // Event handler untuk memperbarui modal saat DataTables menggambar ulang (pindah halaman)
    table.on('draw.dt', function() {
      $('.btn-detail').off('click');
      $('.btn-detail').on('click', function() {
        var jenis = $(this).data('jenis');
        var cs = $(this).data('cs');
        var email = $(this).data('email');
        var cp = $(this).data('cp');
        var telp = $(this).data('telp');
        var alamat = $(this).data('alamat');
        var npwp = $(this).data('npwp');
        var npwpImg = $(this).data('npwp-img');

        // Debug log
        console.log('npwpImg:', npwpImg);

        var createdby = $(this).data('createdby');
        var created = $(this).data('created');
        var updatedby = $(this).data('updatedby');
        var updated = $(this).data('updated');

        $('#dataCs').text(cs);
        $('#jenisUsaha').text(jenis);
        $('#dataEmail').text(email);
        $('#namaCp').text(cp);
        $('#dataTelp').text(telp);
        $('#dataAlamat').text(alamat);
        $('#npwp').text(npwp);
        $('#dataCreatedBy').text(createdby);
        $('#dataCreated').text(created);
        $('#dataUpdatedBy').text(updatedby);
        $('#dataUpdated').text(updated);

        // Menampilkan atau menyembunyikan tombol View Kartu NPWP
        if (npwpImg) {
          var imageUrl = 'display_npwp.php?file=' + encodeURIComponent(npwpImg) + '&customer=' + encodeURIComponent(cs);
          $('#viewNpwp').data('npwp-img', npwpImg).show();
          $('#npwpMessage').hide();
        } else {
          $('#viewNpwp').hide();
          $('#npwpMessage').show();
        }

        $('#detailCs').modal('show'); // Menampilkan modal detail
      });
    });
  });
</script>




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

<!-- Compress image  -->
<script src="assets/js/new-compress-image.js"></script>

<!-- Reset Gambar -->
<script src="assets/js/reset-gambar-new.js"></script>

<script>
  $(document).ready(function() {
    // Inisialisasi DataTable
    $('#table1').DataTable();

    // Tangani perubahan pada checkbox
    $(document).on('change', '.form-check-input[type="checkbox"]', function() {
      var id = $(this).data('id');
      var isChecked = $(this).is(':checked') ? 1 : 0;

      // console.log('Checkbox ID:', id);
      // console.log('Checked Status:', isChecked);

      $.ajax({
        url: 'proses/update-status-cs.php',
        type: 'POST',
        data: {
          id_cs: id,
          status_aktif: isChecked
        },
        success: function(response) {
          // console.log('Status updated:', response);
        },
        error: function(xhr, status, error) {
          // console.error('Error updating status:', error);
        }
      });
    });
  });
</script>