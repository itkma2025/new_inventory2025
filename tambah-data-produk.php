<?php
require_once "akses.php";
$page = 'produk';
$page2 = 'data-produk';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Inventory KMA</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link rel="stylesheet" href="assets/css/file-upload.css">
  <?php include "page/head.php"; ?>

  <style>
    #table2{
      cursor: pointer;
    }

    #table3{
      cursor: pointer;
    }

    input[type="text"]:read-only {
      background: #e9ecef;
      }

      #charCount {
          font-size: 14px;
          margin-top: 5px;
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
        <!-- Loading -->
        <div class="loader loader">
          <div class="loading">
              <img src="img/loading.gif" width="200px" height="auto">
          </div>
        </div>
        <!-- End Loading -->
        <div class="container">
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) {echo $_SESSION['info'];}unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="card">
                <div class="card-header text-center">
                    <h5>Form Tambah Data Produk</h5>
                </div>
                <div class="card-body">
                  <form action="proses/proses-produk-reg.php" method="POST" enctype="multipart/form-data">
                    <?php 
                      date_default_timezone_set('Asia/Jakarta');
                      include "function/uuid.php";
                      $UUID = uuid();
                      require_once 'function/CSRFToken.php';
                      $csrf = new CSRFToken();
                      $csrf_token = $csrf->generateToken();
                      $expired_time = time() + 600;
                      $expired_token = date('Y-m-d H:i:s', $expired_time);
                      $_SESSION['token_csrf'] = $csrf_token;
                      $_SESSION['token_exp'] = $expired_token;
                      $token_csrf = $_SESSION['token_csrf'];
                      $exp_token = $_SESSION['token_exp'];
                      // echo "Token: " . $token_csrf . "<br>";
                      // echo "Expired: " . $exp_token . "<br>";
                    ?>
                    <input type="hidden" id="csrf_token" class="form-control" name="csrf_token" value="<?php echo $token_csrf ?>">
                    <div class="mb-3 mt-2">
                      <label><strong>Jenis Produk</strong></label><br>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_produk" id="inlineRadio1" value="reg" required>
                        <label class="form-check-label" for="inlineRadio1">Reguler</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_produk" id="inlineRadio2" value="ecat" required>
                        <label class="form-check-label" for="inlineRadio2">E-Catalog</label>
                      </div>
                    </div>
                    <input type="hidden" class="form-control" name="id_produk" id="id_produk">
                    <div class="mb-3">
                      <div class="row">
                        <div class="col-sm-8 mb-3">
                          <label class="form-label"><strong>Kode Produk</strong></label>
                          <input type="text" class="form-control" name="kode_produk" required>
                        </div>
                        <div class="col-sm-4 mb-3">
                          <label class="form-label"><strong>No. Batch</strong></label>
                          <input type="text" class="form-control" name="no_batch">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-8 mb-3">
                          <label class="form-label"><strong>Nama Produk</strong></label>
                          <input type="text" class="form-control" name="nama_produk" required>
                      </div>
                      <div class="col-sm-4 mb-3">
                          <label class="form-label"><strong>Kode Katalog</strong></label>
                          <input type="text" class="form-control" name="kode_katalog" required>
                      </div>
                    </div>
                    <div class="mb-3">
                      <div class="row">
                        <div class="col mb-3">
                          <label class="form-label"><strong>Satuan</strong></label>
                          <select name="satuan" class="form-control">
                            <option value="">Pilih...</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Set">Set</option>
                          </select>
                        </div>
                        <div class="col-sm mb-3">
                          <label class="form-label"><strong>Merk</strong></label>
                          <select class="selectize-js form-select" name="merk" required>
                          <option value="">Pilih...</option>
                          <?php 
                              include "koneksi.php";
                              $sql = "SELECT * FROM tb_merk ";
                              $query = mysqli_query($connect,$sql) or die (mysqli_error($connect));
                              while ($data = mysqli_fetch_array($query)){?>
                              <option value="<?php echo $data['id_merk']; ?>"><?php echo $data['nama_merk']; ?></option>
                          <?php } ?>
                          </select>
                        </div>
                        <div class="col-sm">
                          <label class="form-label"><strong>Harga Produk</strong></label>
                          <input type="text" class="form-control" name="harga" id="inputBudget" required>
                        </div>
                      </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                          <div class="col-sm mb-3">
                            <label class="form-label"><strong>Lokasi Produk</strong></label>
                            <input type="hidden" class="form-control" name="id_lokasi" id="id_lokasi">
                            <input  type="text" class="form-control" name="lokasi" id="nama_lokasi" placeholder="Pilih..." data-bs-toggle="modal" data-bs-target="#modalLokasi" readonly>
                          </div>
                          <div class="col-sm mb-3">
                            <label class="form-label"><strong>No. Lantai</strong></label>
                            <input disabled type="text" class="form-control" name="no_lantai" id="no_lantai" readonly>
                          </div>
                          <div class="col-sm mb-3">
                            <label class="form-label"><strong>Area</strong></label>
                            <input disabled type="text" class="form-control" name="area" id="area" readonly>
                          </div>
                          <div class="col-sm">
                            <label class="form-label"><strong>No. Rak</strong></label>
                            <input disabled type="text" class="form-control" name="no_rak" id="no_rak" readonly>
                          </div>
                        </div>
                    </div>
                    <div class="mb-3">
                      <div class="row">
                        <div class="col-sm mb-3">
                          <label class="form-label"><strong>Kategori Produk</strong></label>
                          <input type="hidden" class="form-control" name="kategori_produk" id="idKatProduk">
                          <input type="text" placeholder="Pilih..." class="form-control" name="nama_kat_produk" id="namaKatProduk" data-bs-toggle="modal" data-bs-target="#modalkatprod" readonly>
                        </div>
                        <div class="col-sm mb-3">
                          <label class="form-label"><strong>Kategori Penjualan</strong></label>
                          <select class="selectize-js form-select" name="kategori_penjualan" required>
                          <option value="">Pilih...</option>
                          <?php 
                              include "koneksi.php";
                              $sql = "SELECT * FROM tb_kat_penjualan ";
                              $query = mysqli_query($connect,$sql) or die (mysqli_error($connect));
                              while ($data = mysqli_fetch_array($query)){?>
                              <option value="<?php echo $data['id_kat_penjualan']; ?>"><?php echo $data['nama_kategori']; ?></option>
                          <?php } ?>
                          </select>
                        </div>
                        <div class="col-sm mb-3">
                          <label class="form-label"><strong>Grade Produk</strong></label>
                          <select class="selectize-js form-select" name="grade" required>
                          <option value="">Pilih...</option>
                          <?php 
                              include "koneksi.php";
                              $sql = "SELECT * FROM tb_produk_grade ";
                              $query = mysqli_query($connect,$sql) or die (mysqli_error($connect));
                              while ($data = mysqli_fetch_array($query)){?>
                              <option value="<?php echo $data['id_grade']; ?>"><?php echo $data['nama_grade']; ?></option>
                          <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label>Deskripsi Produk</label>
                      <textarea name="deskripsi" id="deskripsi" maxlength="500"></textarea>
                      <div id="charCount"></div>
                    </div>
                    <div class="preview-image">
                      <img id="imagePreview" src="#" alt="Preview Image" style="display:none;">
                    </div>
                    <div id="console-output"></div>
                    <div class="mb-3">
                      <div class="input-group">
                        <div class="fileUpload btn btn-primary" id="fileUpload">
                          <span><i class="bi bi-upload"></i> Upload Gambar</span>
                          <input class="upload" type="file" name="fileku" id="formFile" accept="image/*" onchange="compressImage(event)" required>
                        </div>
                        <div class="fileUpload btn btn-danger" id="resetButton">
                          <span><i class="bi bi-arrow-repeat"></i> Reset File</span>
                          <input type="text" class="upload">
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="simpan-produk-reg" class="btn btn-primary btn-md m-1"><i class="bx bx-save"></i> Simpan Data</button>
                        <a href="data-produk-reg.php" class="btn btn-secondary btn-md m-1"><i class="bi bi-x-circle"></i> Tutup</a>
                    </div>
                  </form>
                </div>
            </div>
        </div>
        <script>
          document.addEventListener("DOMContentLoaded", function() {
            var regRadio = document.getElementById("inlineRadio1");
            var ecatRadio = document.getElementById("inlineRadio2");
            var idProdukInput = document.getElementById("id_produk");

            regRadio.addEventListener("change", function() {
              if (regRadio.checked) {
                idProdukInput.value = "BR-REG<?php echo $UUID;?>";
                // console.log("Nilai id_produk diubah menjadi: " + idProdukInput.value);
              }
            });

            ecatRadio.addEventListener("change", function() {
              if (ecatRadio.checked) {
                idProdukInput.value = "BR-ECAT<?php echo $UUID;?>";
                // console.log("Nilai id_produk diubah menjadi: " + idProdukInput.value);
              }
            });
          });
        </script>
    </section>
  </main><!-- End #main -->
 
  

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>
</html>

<!-- Compress image  -->
<script src="assets/js/compress-img.js"></script>

<!-- Reset Gambar -->
<script src="assets/js/reset-gambar.js"></script>

<!-- Button file upload JS -->
<script src="assets/js/button-file-upload.js"></script>


<!-- Modal Lokasi Produk -->
<?php include "modal/lokasi.php" ?>
<script src="assets/js/select-data-lokasi.js"></script>

<!-- Modal Kategori -->
<?php include "modal/kategori-produk.php" ?>
<script src="assets/js/select-kategori-produk.js"></script>

<!-- Format Rupiah -->
<script src="assets/js/format-rupiah.js"></script>

<!-- Membuat realtime permintaan refresh browser jika CSRF token sudah expired -->
<!-- Ambil nilai expired date terlebih dahulu -->
<script id="expiryDate" data-expiry="<?php echo $expired_token ?>"></script>
<script src="assets/js/expired-token.js"></script>

<!-- Textarea deskripsi produk menggunakan CKEditor 5 -->
<script src="assets/vendor/CKEditor5/ckeditor.js"></script>
<script src="assets/js/CKEditor-deskripsi.js"></script>

 