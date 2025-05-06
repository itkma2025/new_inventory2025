<?php
require_once "akses.php";
$page = 'data';
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
  <?php include "page/head.php"; ?>
  <style>
    #table2 {
      cursor: pointer;
    }

    #table3 {
      cursor: pointer;
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

  <?php  
    if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") { 
      ?>
        <main id="main" class="main">
          <section>
            <div class="container">
              <div class="card">
                <div class="card-header text-center">
                  <h5>Edit Produk ecatuler</h5>
                </div>
                <div class="card-body">
                  <form action="proses/proses-produk-reg.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                      <div class="mb-3">
                        <?php
                         require_once "function/function-enkripsi.php";
                         //tangkap URL dengan $_GET
                         $ide = decrypt($_GET['edit-data'], $key_global);

                        //mengambil nama gambar yang terkait
                        $sql = "SELECT 
                                  pr.id_produk_ecat, pr.kode_produk, pr.nama_produk, pr.kode_katalog, pr.satuan, pr.harga_produk, pr.gambar, pr.deskripsi,
                                  mr.id_merk, mr.nama_merk,
                                  kp.id_kat_produk, kp.nama_kategori as kat_prod,
                                  kj.id_kat_penjualan, kj.nama_kategori as kat_penj,
                                  gr.id_grade, gr.nama_grade,
                                  lok.id_lokasi, lok.nama_lokasi,
                                  lok.no_lantai, lok.nama_area, lok.no_rak
                                FROM tb_produk_ecat as pr
                                LEFT JOIN tb_merk mr ON (pr.id_merk = mr.id_merk)
                                LEFT JOIN tb_kat_produk kp ON (pr.id_kat_produk = kp.id_kat_produk)
                                LEFT JOIN tb_kat_penjualan kj ON (pr.id_kat_penjualan = kj.id_kat_penjualan)
                                LEFT JOIN tb_produk_grade gr ON (pr.id_grade = gr.id_grade)
                                LEFT JOIN tb_lokasi_produk lok ON (pr.id_lokasi = lok.id_lokasi)
                                WHERE pr.id_produk_ecat = '$ide'";
                        $result = mysqli_query($connect, $sql);
                        $row = mysqli_fetch_array($result);
                        $img = $row['gambar'];
                        $no_img = $row["gambar"] == "" ? "gambar/upload-produk-ecat/no-image.png" : "gambar/upload-produk-ecat/$img";
                        ?>
                        <div class="mb-3">
                          <label class="form-label"><strong>Kode Produk</strong></label>
                          <input type="hidden" class="form-control" name="id_produk" value="<?php echo $row['id_produk_ecat']; ?>">
                          <input type="text" class="form-control" name="kode_produk" value="<?php echo $row['kode_produk'] ?>" readonly>
                        </div>
                        <div class="row">
                          <div class="col-8 mb-3">
                            <label class="form-label"><strong>Nama Produk</strong></label>
                            <input type="text" class="form-control" name="nama_produk" value="<?php echo $row['nama_produk'] ?>" required>
                          </div>
                          <div class="col-4 mb-3">
                            <label class="form-label"><strong>Kode Katalog</strong></label>
                            <input type="text" class="form-control" name="kode_katalog" value="<?php echo $row['kode_katalog'] ?>" required>
                          </div>
                        </div>
                        <div class="mb-3">
                          <div class="row">
                            <div class="col mb-3">
                              <label class="form-label"><strong>Satuan</strong></label>
                              <select name="satuan" class="form-control">
                                <option value="<?php echo $row['satuan'] ?>"><?php echo $row['satuan'] ?></option>
                                <option value="Pcs">Pcs</option>
                                <option value="Set">Set</option>
                              </select>
                            </div>
                            <div class="col-sm mb-3">
                              <label class="form-label"><strong>Merk</strong></label>
                              <select class="form-select" name="merk" required>
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
                            <div class="col-sm">
                              <label class="form-label"><strong>Harga Produk</strong></label>
                              <input type="text" class="form-control" name="harga" id="inputBudget" value="<?php echo $row['harga_produk'] ?>" required>
                            </div>
                          </div>
                        </div>
                        <div class="mb-3">
                          <div class="row">
                            <div class="col-sm mb-3">
                              <label class="form-label"><strong>Lokasi Produk</strong></label>
                              <input type="hidden" class="form-control" name="id_lokasi" id="id_lokasi" value="<?php echo $row['id_lokasi'] ?>">
                              <input type="text" class="form-control" name="lokasi" id="nama_lokasi" data-bs-toggle="modal" data-bs-target="#modalLokasi" value="<?php echo $row['nama_lokasi'] ?>" readonly>
                            </div>
                            <div class="col-sm mb-3">
                              <label class="form-label"><strong>No. Lantai</strong></label>
                              <input type="text" class="form-control" name="no_lantai" id="no_lantai" value="<?php echo $row['no_lantai'] ?>" readonly>
                            </div>
                            <div class="col-sm mb-3">
                              <label class="form-label"><strong>Area</strong></label>
                              <input type="text" class="form-control" name="area" id="area" value="<?php echo $row['nama_area'] ?>" readonly>
                            </div>
                            <div class="col-sm">
                              <label class="form-label"><strong>No. Rak</strong></label>
                              <input type="text" class="form-control" name="no_rak" id="no_rak" value="<?php echo $row['no_rak'] ?>" readonly>
                            </div>
                          </div>
                        </div>
                        <div class="mb-3">
                          <div class="row">
                            <div class="col-sm mb-3">
                              <label class="form-label"><strong>Kategori Produk</strong></label>
                              <input type="hidden" class="form-control" name="id_kat_produk" id="idKatProduk" value="<?php echo $row['id_kat_produk'] ?>">
                              <input type="text" class="form-control" name="nama_kat_produk" id="namaKatProduk" data-bs-toggle="modal" data-bs-target="#modalkatprod" value="<?php echo $row['kat_prod'] ?> - <?php echo $row['nama_merk'] ?>" readonly>
                            </div>
                            <div class="col-sm mb-3">
                              <label class="form-label"><strong>Kategori Penjualan</strong></label>
                              <select class="form-select" name="kategori_penjualan" required>
                                <option value="<?php echo $row['id_kat_penjualan']; ?>"><?php echo $row['kat_penj']; ?></option>
                                <?php
                                include "koneksi.php";
                                $sql = "SELECT * FROM tb_kat_penjualan ";
                                $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                                while ($data = mysqli_fetch_array($query)) { ?>
                                  <option value="<?php echo $data['id_kat_penjualan']; ?>"><?php echo $data['nama_kategori']; ?></option>
                                <?php } ?>
                              </select>
                            </div>
                            <div class="col-sm">
                              <label class="form-label"><strong>Grade Produk</strong></label>
                              <select class="form-select" name="grade" required>
                                <option value="<?php echo $row['id_grade']; ?>"><?php echo $row['nama_grade']; ?></option>
                                <?php
                                include "koneksi.php";
                                $sql = "SELECT * FROM tb_produk_grade ";
                                $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                                while ($data = mysqli_fetch_array($query)) { ?>
                                  <option value="<?php echo $data['id_grade']; ?>"><?php echo $data['nama_grade']; ?></option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="mb-3">
                          <label>Deskripsi Produk</label>
                          <textarea name="deskripsi" id="deskripsi"><?php echo $row['deskripsi'] ?></textarea>
                          <div id="charCount"></div>
                        </div>
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
                        <input type="hidden" class="form-control" name="id_user" value="<?php echo $_SESSION['tiket_id']; ?>">
                        <input type="hidden" class="form-control" name="updated" id="datetime-input">
                      </div>
                      <div class="modal-footer">
                        <button type="submit" name="edit-produk-ecat" id="ubahData" class="btn btn-primary btn-md m-1" onclick="ubahData()"><i class="bx bx-save"></i> Ubah Data</button>
                        <a href="data-produk-ecat.php" class="btn btn-secondary btn-md m-1"><i class="bi bi-x"></i> Tutup</a>
                      </div>
                  </form>
                </div>
              </div>
            </div>
          </section>
        </main><!-- End #main -->
      <?php
    }else {
      ?>
        <!-- Sweet Alert -->
        <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
        <script src="assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Error!",
                text: "Maaf Anda Tidak Memiliki Akses Fitur Ini",
                icon: "error",
            }).then(function() {
                window.location.href = "data-produk-ecat.php";
            });
            });
        </script>
      <?php
    }
  ?>
    <!-- Modal Lokasi -->
    <?php include "modal/lokasi.php" ?>
    <!-- End Modal Lokasi -->

  <!-- Modal Kategori Produk -->
  <div class="modal fade" id="modalkatprod" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5">Pilih Data</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="card">
          <div class="card-body table-responsive mt-3">
            <table class="table table-bordered table-striped katProd" id="table3">
              <thead>
                <tr class="text-white" style="background-color: #051683;">
                  <td class="text-center p-3" style="width: 80px">No</td>
                  <td class="text-center p-3" style="width: 200px">Nama Kategori</td>
                  <td class="text-center p-3" style="width: 200px">Merk</td>
                  <td class="text-center p-3" style="width: 200px">Nomor Izin Edar</td>
                </tr>
              </thead>
              <tbody>
                <?php
                date_default_timezone_set('Asia/Jakarta');
                include "koneksi.php";
                $no = 1;
                $sql = "SELECT * FROM tb_kat_produk AS tkp
                                  JOIN tb_merk AS m ON (tkp.id_merk = m.id_merk)
                                  ORDER BY nama_kategori ASC";
                $query = mysqli_query($connect, $sql) or die(mysqli_error($connect, $sql));
                while ($data = mysqli_fetch_array($query)) {
                ?>
                  <tr data-idkat="<?php echo $data['id_kat_produk']; ?>" data-namakatprod="<?php echo $data['nama_kategori'] ?> - <?php echo $data['nama_merk'] ?>" data-bs-dismiss="modal">
                    <td class="text-center"><?php echo $no; ?></td>
                    <td class="text-center"><?php echo $data['nama_kategori']; ?></td>
                    <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                    <td class="text-center"><?php echo $data['no_izin_edar']; ?></td>
                  </tr>
                  <?php $no++; ?>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Modal  -->

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>

</html>

<!-- Clock JS  -->
<script src="assets/js/clock.js"></script>

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

<!-- Textarea deskripsi produk menggunakan CKEditor 5 -->
<script id="deskripsi" data-deskripsi="<?php echo $deskripsi ?>"></script>
<script src="assets/vendor/CKEditor5/ckeditor.js"></script>
<script src="assets/js/CKEditor-deskripsi.js"></script>