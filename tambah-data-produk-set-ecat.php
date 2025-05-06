<?php
require_once "akses.php";
$page = 'produk';
$page2 = 'data-produk-set-ecat';
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
    #table2{
      cursor: pointer;
    }
 
    #table3{
      cursor: pointer;
    }

    .fileUpload {
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .fileUpload input.upload {
        position: unset;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 20px;
        width: 10px;
        cursor: pointer;
        opacity: 0;
        filter: alpha(opacity=0);
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
            <h4>Tambah Data Produk Set E-Cat</h4>
          </div>
          <div class="card-body p-3">
            <form action="proses/proses-produk-set-ecat.php" method="POST" enctype="multipart/form-data">
              <?php 
                  $UUID = generate_uuid();
              ?>
              <input type="hidden" class="form-control" name="id_set_ecat" value="SETECAT<?php echo $UUID; ?>">
              <div class="mb-3">
                <div class="row">
                  <div class="col-sm-8 mb-3">
                    <label class="form-label"><strong>Kode Produk Set</strong></label>
                    <input type="text" class="form-control" name="kode_barang" required>
                  </div>
                  <div class="col-sm-4 mb-3">
                    <label class="form-label"><strong>No. Batch</strong></label>
                    <input type="text" class="form-control" name="no_batch">
                  </div>
                </div>
              </div>
              <div class="mb-3">
                  <label class="form-label"><strong>Nama Produk Set</strong></label>
                  <input type="text" class="form-control" name="nama_set_ecat" required>
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
                    <label class="form-label"><strong>Kategori Produk - No Izin Edar</strong></label>
                    <select class="selectize-js form-select" name="kategori_produk" required>
                    <option value="">Pilih...</option>
                    <?php 
                        include "koneksi.php";
                        $sql = "SELECT id_kat_produk, nama_kategori, no_izin_edar FROM tb_kat_produk ";
                        $query = mysqli_query($connect,$sql) or die (mysqli_error($connect));
                        while ($data = mysqli_fetch_array($query)){?>
                        <option value="<?php echo $data['id_kat_produk']; ?>"><?php echo $data['nama_kategori'] ?> - <?php echo $data['no_izin_edar'] ?></option>
                    <?php } ?>
                    </select>
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
                  <div class="col-sm mb-3">
                    <label class="form-label"><strong>Harga Produk Set</strong></label>
                    <input type="text" class="form-control" name="harga" id="inputBudget" required>
                  </div>
                  <input type="hidden" class="form-control" name="id_user" value="<?php echo base64_decode($_SESSION['tiket_id']) ?>" required>
                  <input type="hidden" class="form-control" name="created_date" id="datetime-input" required>
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
                      <input class="upload" type="file" name="fileku" id="formFile" accept="image/jpeg, image/png, image/jpg" onchange="compressImage(event)" required>
                    </div>
                    <div class="fileUpload btn btn-danger" id="resetButton">
                      <span><i class="bi bi-arrow-repeat"></i> Reset File</span>
                      <input class="upload" type="button">
                    </div>
                  </div>
                </div>
                <div class="mb-3 pt-3 text-end">
                    <button type="submit" name="simpan-set-ecat" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Simpan Data</button>
                    <a href="data-produk-set-ecat.php" class="btn btn-secondary btn-md"><i class="bi bi-x"></i> Tutup</a>
                </div>
            </form>
        </div>  
    </section>
  </main><!-- End #main -->
  
  <!-- Modal SP -->
  <div class="modal fade" id="modal1" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5">Tambah Data Produk Set</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
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

 
<!-- Modal Lokasi -->
<?php include "modal/lokasi.php" ?>
<!-- End Modal Lokasi -->

<!-- Select Data -->
<script src="assets/js/select-data.js"></script>

<!-- Generat UUID -->
<?php
  function generate_uuid() {
  return sprintf( '%04x%04x%04x',
    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
    mt_rand( 0, 0xffff ),
    mt_rand( 0, 0x0fff ) | 0x4000,
    mt_rand( 0, 0x3fff ) | 0x8000,
    mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
  );
}
?>
<!-- End Generate UUID -->

<!-- Format nominal Indo -->
<script>
   const inputBudget = document.getElementById('inputBudget');
  
  inputBudget.addEventListener('input', () => {
    // Remove any non-digit characters
    let input = inputBudget.value.replace(/[^\d]/g, '');
    // Convert to a number and format with "Rp" prefix and "." and "," separator
    let formattedInput = Number(input).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
    // Remove trailing ",00" if present
    formattedInput = formattedInput.replace(",00", "");
    // Update the input value with the formatted number
    inputBudget.value = formattedInput;
  });
</script>

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

<!-- Script untuk menjalankan fungsi previewImage() dan resetForm() -->
<script>
  function compressImage() {
  var file = document.querySelector('#formFile').files[0];
  var reader = new FileReader();
  var consoleOutput = document.getElementById('console-output');

  // Empty the console output
  consoleOutput.innerHTML = '';

  reader.onload = function() {
    var img = new Image();
    img.src = reader.result;

    img.onload = function() {
      var canvas = document.createElement('canvas');
      var ctx = canvas.getContext('2d');
      var maxWidth = 650;
      var maxHeight = 650;
      var width = img.width;
      var height = img.height;

      // Calculate new dimensions
      if (width > height) {
        if (width > maxWidth) {
          height *= maxWidth / width;
          width = maxWidth;
        }
      } else {
        if (height > maxHeight) {
          width *= maxHeight / height;
          height = maxHeight;
        }
      }

      // Set canvas dimensions
      canvas.width = width;
      canvas.height = height;

      // Compress image
      ctx.drawImage(img, 0, 0, width, height);
      canvas.toBlob(function(blob) {
        // Get compressed file size
        var compressedSize = blob.size / 1024; // convert to KB
        // console.log('Compressed file size:', compressedSize + ' KB');

        // Get original file size
        var originalSize = file.size / 1024; // convert to KB
        // console.log('Original file size:', originalSize + ' KB');

        // Display console log output in HTML
        var consoleOutput = document.getElementById('console-output');
        consoleOutput.innerHTML += 'File size: ' + compressedSize.toFixed(2) + ' KB<br>';
        // consoleOutput.innerHTML += 'Original file size: ' + originalSize.toFixed(2) + ' KB<br>';


        // Set compressed image preview
        var preview = document.querySelector('#imagePreview');
        preview.src = URL.createObjectURL(blob);
        preview.style.display = 'block';
        preview.style.width = '300px';
        preview.style.height = '300px';
      }, file.type);
    };
  };

  if (file) {
    reader.readAsDataURL(file);
  }
  }

  function resetForm() {
    document.getElementById('formFile').value = '';
    var preview = document.querySelector('#imagePreview');
    var console = document.querySelector('#console-output');
    preview.style.display = 'none';
    console.style.display = 'block';
    preview.src = '#';
    console.innerHTML = '';
  }

  document.querySelector('#resetButton').addEventListener('click', resetForm);
</script>

<!-- Kode untuk menjalankan span pada file upload -->
<script>
  const fileUpload = document.getElementById('fileUpload');
  const fileInput = document.getElementById('formFile');

  fileUpload.addEventListener('click', function() {
  fileInput.click();
  });
</script>
<!-- Textarea deskripsi produk menggunakan CKEditor 5 -->
<script src="assets/vendor/CKEditor5/ckeditor.js"></script>
<script src="assets/js/CKEditor-deskripsi.js"></script>
