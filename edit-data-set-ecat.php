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
    #table2 {
      cursor: pointer;
    }

    #table3 {
      cursor: pointer;
    }

    input[type="text"]:read-only {
      background: #e9ecef;
    }

    .fileUpload {
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .fileUpload input.upload {
      position: absolute;
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
            <h5>Edit Data Produk Set E-Cat</h5>
          </div>
          <div class="card-body p-3">
            <form action="proses/proses-produk-set-ecat.php" method="POST" enctype="multipart/form-data">
                <?php
                    $id = $_GET['edit-set-ecat'];
                    $key = "KM@2024?SET";
                    $ide = decrypt($id, $key);
                    // Menampilkan data
                    $sql = "SELECT 
                              prs.id_set_ecat,
                              prs.kode_set_ecat,
                              prs.no_batch,
                              prs.nama_set_ecat,
                              prs.harga_set_ecat,
                              DATE_FORMAT(prs.created_date, '%d/%m/%Y, %H:%i:%s') AS produk_created,  -- Format tanggal Indonesia
                              DATE_FORMAT(prs.updated_date, '%d/%m/%Y, %H:%i:%s') AS produk_updated,  -- Format tanggal Indonesia  
                              prs.gambar,
                              prs.deskripsi,
                              uc.nama_user as user_created, 
                              uu.nama_user as user_updated,
                              kj.id_kat_penjualan,
                              kj.nama_kategori as nama_kat,
                              kp.id_kat_produk,
                              kp.nama_kategori as nama_kat_produk,
                              kp.no_izin_edar,
                              mr.id_merk,
                              mr.nama_merk,
                              lok.id_lokasi,
                              lok.nama_lokasi,
                              lok.no_lantai,
                              lok.nama_area,
                              lok.no_rak
                            FROM tb_produk_set_ecat as prs
                            LEFT JOIN $database2.user uc ON (prs.created_by = uc.id_user)
                            LEFT JOIN $database2.user uu ON (prs.updated_by = uu.id_user)
                            LEFT JOIN tb_merk mr ON (prs.id_merk = mr.id_merk)
                            LEFT JOIN tb_kat_penjualan kj ON (prs.id_kat_penjualan = kj.id_kat_penjualan)
                            LEFT JOIN tb_kat_produk kp ON (prs.id_kat_produk = kp.id_kat_produk)
                            LEFT JOIN tb_lokasi_produk lok ON (prs.id_lokasi = lok.id_lokasi)
                            WHERE prs.id_set_ecat = '$ide'";
                $query = mysqli_query($connect, $sql) or die(mysqli_error($connect, $sql));
                $row = mysqli_fetch_array($query);
                $cek_data = mysqli_num_rows($query);
                $img = $row['gambar'];
                $no_img = $row["gambar"] == "" ? "gambar/upload-produk-set-ecat/no-image.png" : "gambar/upload-produk-set-ecat/$img";
                ?>
                <input type="hidden" class="form-control" name="id_set_ecat" value="<?php echo $row['id_set_ecat']; ?>">
                <div class="mb-3">
                    <div class="row">
                    <div class="col-sm-8 mb-3">
                        <label class="form-label"><strong>Kode Produk Set</strong></label>
                        <input type="text" class="form-control" name="kode_barang" value="<?php echo $row['kode_set_ecat'] ?>">
                    </div>
                    <div class="col-sm-4 mb-3">
                        <label class="form-label"><strong>No. Batch</strong></label>
                        <input type="text" class="form-control" name="no_batch" value="<?php echo $row['no_batch'] ?>">
                    </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Nama Produk Set</strong></label>
                    <input type="text" class="form-control" name="nama_set_ecat" value="<?php echo $row['nama_set_ecat'] ?>" required>
                </div>
                <div class="mb-3">
                    <div class="row">
                    <div class="col-sm mb-3">
                        <label class="form-label"><strong>Lokasi Produk</strong></label>
                        <input type="hidden" class="form-control" name="id_lokasi" id="id_lokasi" value="<?php echo $row['id_lokasi'] ?>">
                        <input type="text" class="form-control" name="lokasi" id="nama_lokasi" value="<?php echo $row['nama_lokasi'] ?>" data-bs-toggle="modal" data-bs-target="#modalLokasi" readonly>
                    </div>
                    <div class="col-sm mb-3">
                        <label class="form-label"><strong>No. Lantai</strong></label>
                        <input disabled type="text" class="form-control" name="no_lantai" id="no_lantai" value="<?php echo $row['no_lantai'] ?>" readonly>
                    </div>
                    <div class="col-sm mb-3">
                        <label class="form-label"><strong>Area</strong></label>
                        <input disabled type="text" class="form-control" name="area" id="area" value="<?php echo $row['nama_area'] ?>" readonly>
                    </div>
                    <div class="col-sm">
                        <label class="form-label"><strong>No. Rak</strong></label>
                        <input disabled type="text" class="form-control" name="no_rak" id="no_rak" value="<?php echo $row['no_rak'] ?>" readonly>
                    </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-sm mb-3">
                          <label class="form-label"><strong>Kategori Produk</strong></label>
                          <select class="selectize-js form-select" name="kategori_produk">
                          <option value="<?php echo $row['id_kat_produk'] ?>"><?php echo $row['nama_kat_produk'] ?> - <?php echo $row['no_izin_edar']; ?></option>
                          <?php 
                              include "koneksi.php";
                              $sql = "SELECT id_kat_produk, nama_kategori, no_izin_edar FROM tb_kat_produk";
                              $query = mysqli_query($connect,$sql) or die (mysqli_error($connect));
                              while ($data = mysqli_fetch_array($query)){?>
                              <option value="<?php echo $data['id_kat_produk']; ?>"><?php echo $data['nama_kategori']; ?> - <?php echo $data['no_izin_edar']; ?></option>
                          <?php } ?>
                          </select>
                        </div>
                        <div class="col-sm mb-3">
                            <label class="form-label"><strong>Kategori Penjualan</strong></label>
                            <select class="selectize-js form-select" name="kategori_penjualan" required>
                            <option value="<?php echo $row['id_kat_penjualan'] ?>"><?php echo $row['nama_kat'] ?></option>
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
                            <label class="form-label"><strong>Merk</strong></label>
                            <select class="selectize-js form-select" name="merk" required>
                            <option value="<?php echo $row['id_merk'] ?>"><?php echo $row['nama_merk'] ?></option>
                            <?php
                            include "koneksi.php";
                            $sql = "SELECT * FROM tb_merk ";
                            $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                            while ($data = mysqli_fetch_array($query)) { ?>
                                <option value="<?php echo $data['id_merk']; ?>"><?php echo $data['nama_merk']; ?></option>
                            <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm mb-3">
                            <label class="form-label"><strong>Harga Produk Set</strong></label>
                            <input type="text" class="form-control" name="harga" id="inputBudget" value="<?php echo $row['harga_set_ecat'] ?>" required>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                  <label>Deskripsi Produk</label>
                  <textarea name="deskripsi" id="deskripsi"><?php echo $row['deskripsi'] ?></textarea>
                  <div id="charCount"></div>
                </div>
                <div class="mb-3">
                  <div class="mb-3 col-sm-6">
                    <img id="imagePreview" src="<?php echo $no_img; ?>" id="output" height="300" width="300">
                    <div id="console-output"></div>
                  </div>
                  <div class="mb-3 col-sm-6">
                    <div class="input-group">
                      <div class="fileUpload btn btn-primary" id="fileUpload">
                        <span><i class="bi bi-upload"></i> Ubah Gambar</span>
                        <input class="upload" type="file" name="fileku" id="formFile" accept="image/*" onchange="compressImage(event)">
                      </div>
                      <div class="fileUpload btn btn-danger" id="resetButton">
                        <span><i class="bi bi-arrow-repeat"></i> Reset File</span>
                        <input class="upload" type="button">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mb-3 pt-3 text-end">
                    <button type="submit" name="edit-set-ecat" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Ubah Data</button>
                    <a href="data-produk-set-ecat.php" class="btn btn-secondary btn-md"><i class="bi bi-x"></i> Tutup</a>
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

<!-- Modal Lokasi -->
<?php require_once "modal/lokasi.php" ?>
<!-- End Modal Lokasi -->

<!-- Select Data -->
<script src="assets/js/select-data.js"></script>

<!-- Format nominal Indo -->
<script>
  const inputBudget = document.getElementById('inputBudget');
  let formattedValue = Number(inputBudget.value).toLocaleString('id-ID', {
    style: 'currency',
    currency: 'IDR'
  });
  inputBudget.value = formattedValue.replace(",00", "");

  inputBudget.addEventListener('input', () => {
    // Remove any non-digit characters
    let input = inputBudget.value.replace(/[^\d]/g, '');
    // Convert to a number and format with "Rp" prefix and "." and "," separator
    let formattedInput = Number(input).toLocaleString('id-ID', {
      style: 'currency',
      currency: 'IDR'
    });
    // Remove trailing ",00" if present
    formattedInput = formattedInput.replace(",00", "");
    // Update the input value with the formatted number
    inputBudget.value = formattedInput;
  });
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

<script>
  const fileUpload = document.getElementById('fileUpload');
  const fileInput = document.getElementById('formFile');

  fileUpload.addEventListener('click', function() {
    fileInput.click();
  });
</script>

<!-- Textarea deskripsi produk menggunakan CKEditor 5 -->
<script id="deskripsi" data-deskripsi="<?php echo $deskripsi ?>"></script>
<script src="assets/vendor/CKEditor5/ckeditor.js"></script>
<script src="assets/js/CKEditor-deskripsi.js"></script>