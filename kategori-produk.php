<?php
require_once "akses.php";
$page = 'produk';
$page2 = 'data-kat-prod';
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
  <!-- FancyBox CSS -->
  <link rel="stylesheet" href="assets/vendor/fancybox/fancybox.css">
  <link rel="stylesheet" href="assets/css/dropzone.css">
</head>
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
      <h1>Kategori Produk</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Kategori Produk</li>
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
               if ($role == "Super Admin" || $role == "Manager Gudang") { 
                ?>
                  <a href="#" class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#modal1"><i class="bi bi-plus-circle"></i> Tambah data kategori produk</a>
                <?php 
               }
            ?>
            <div class="table-responsive mt-3">
              <table class="table table-striped table-bordered" id="table1">
                <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <td class="text-center p-3 text-nowrap">No</td>
                    <td class="text-center p-3 text-nowrap">Nama Kategori Produk</td>
                    <td class="text-center p-3 text-nowrap">Merk</td>
                    <td class="text-center p-3 text-nowrap">Nomor Izin Edar</td>
                    <td class="text-center p-3 text-nowrap">Tgl. Terbit</td>
                    <td class="text-center p-3 text-nowrap">Tgl. Berlaku Sampai</td>
                    <td class="text-center p-3 text-nowrap">Sisa Waktu Perpanjangan</td>
                    <?php  
                      if ($role == "Super Admin" || $role == "Manager Gudang") { 
                        ?>
                            <td class="text-center p-3 text-nowrap">Aksi</td>
                        <?php 
                       }
                    ?>
                  
                  </tr>
                </thead>
                <tbody>
                  <?php
                  date_default_timezone_set('Asia/Jakarta');
                  require_once __DIR__ . "/function/sisa-waktu-perpanjangan.php";
                  $no = 1;
                  $sql = "  SELECT 
                                tkp.id_kat_produk, 
                                tkp.nama_kategori, 
                                tkp.no_izin_edar, 
                                tkp.tgl_terbit,
                                tkp.berlaku_sampai,
                                DATE_FORMAT(STR_TO_DATE(tkp.berlaku_sampai, '%d/%m/%Y'), '%Y-%m-%d') AS tanggal_berlaku_sampai,
                                mr.nama_merk,
                                tkp.file_nie
                            FROM 
                                tb_kat_produk AS tkp
                            LEFT JOIN 
                                tb_merk AS mr ON tkp.id_merk = mr.id_merk
                            ORDER BY 
                                tkp.nama_kategori ASC";
                  $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                  while ($data = mysqli_fetch_array($query)) {
                    $id_kat = encrypt($data['id_kat_produk'], $key_global);
                    $tanggal_sekarang = date('Y-m-d');
                    $tanggal_awal = new DateTime();
                    $tanggal_awal->setTime(0, 0, 0);  // Set waktu ke 00:00:00


                    if ($data['berlaku_sampai'] == '') {
                        $selisih = "Tanggal Berlaku Tidak Ada";
                    } else {
                      // Tanggal akhir dari data yang diambil dari database
                      $tanggal_berlaku_sampai = DateTime::createFromFormat('Y-m-d', $data['tanggal_berlaku_sampai']);
                      $tanggal_berlaku_sampai->setTime(0, 0, 0);  // Set waktu ke 00:00:00

                      // Menghitung selisih waktu
                      $selisih = $tanggal_awal->diff($tanggal_berlaku_sampai, true); // Menggunakan parameter true untuk mengaktifkan selisih waktu negatif

                      // Menyimpan selisih ke dalam variabel dengan nama yang diinginkan
                      $sisa_tahun = $selisih->y;
                      $sisa_bulan = $selisih->m;
                      $sisa_hari = $selisih->d;
                    }


                  ?>
                    <tr>
                      <td class="text-center text-nowrap"><?php echo $no; ?></td>
                      <td class="text-nowrap"><?php echo $data['nama_kategori'] ?></td>
                      <td class="text-center text-nowrap"><?php echo $data['nama_merk'] ?></td>
                      <td class="text-center text-nowrap"><?php echo $data['no_izin_edar'] ?></td>
                      <td class="text-center text-nowrap">
                        <?php
                          if($data['tgl_terbit'] == ''){
                            echo 'Tanggal Terbit Tidak Ada';
                          } else {
                            echo $data['tgl_terbit'];
                          }
                        ?>
                      </td>
                      <td class="text-center text-nowrap">
                        <?php 
                          if ($data['berlaku_sampai'] == '') {
                            echo 'Tanggal Berlaku Tidak Ada';
                          } else {
                            echo $data['berlaku_sampai'];
                          }
                        ?>
                      
                      </td>
                      <?php echo tampilkanStatusBerlaku($data, $tanggal_sekarang, $sisa_tahun, $sisa_bulan, $sisa_hari); ?>
                      <?php  
                        if ($role == "Super Admin" || $role == "Manager Gudang") { 
                          ?>
                            <td class="text-center text-nowrap">
                              <button class="btn btn-primary btn-sm btnDetail" data-bs-toggle="modal" data-bs-target="#modalDetail" data-id="<?php echo encrypt($data['id_kat_produk'], $key_global); ?>">
                                <i class="bi bi-eye"></i>
                              </button>
                              <button class="btn btn-warning btn-sm btnEdit" data-bs-toggle="modal" data-bs-target="#modalEdit" data-id="<?php echo encrypt($data['id_kat_produk'], $key_global); ?>">
                                <i class="bi bi-pencil"></i>
                              </button>
                              <a href="proses/proses-kat-produk.php?hapus-kat-produk=<?php echo $id_kat ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
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
    </section>
  </main><!-- End #main -->
  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
  <!-- Fancybox -->
  <script src="assets/vendor/fancybox/fancybox.umd.js"></script>
</body>
</html>
<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1"  data-bs-backdrop="static" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailLabel">Detail Kategori Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Data akan dimuat di sini -->
            </div>
        </div>
    </div>
</div>
<!-- End Modal Detail -->

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1"  data-bs-backdrop="static" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailLabel">Edit Kategori Produk</h5>
            </div>
            <div class="modal-body" id="editContent">
                <!-- Data akan dimuat di sini -->
            </div>
        </div>
    </div>
</div>
<!-- End Modal Edit -->



<!-- Modal pilih merk -->
<div class="modal fade" id="ubahMerk" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Pilih Merk</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="table2">
                <thead>
                    <tr>
                        <th>Merk</th>
                    </tr>
                </thead>
                <tbody>
                    <?php  
                      include "koneksi.php";
                      $no = 1;
                      $sql_merk = mysqli_query($connect, "SELECT nama_merk FROM tb_merk");
                      while($data_merk = mysqli_fetch_array($sql_merk)){
                    ?>
                    <tr data-merk="<?php echo $data_merk['nama_merk']; ?>">
                        <td><?php echo $data_merk['nama_merk']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Input -->
<?php require_once __DIR__ . "/modal/tambah-data-kategori.php" ?>
<!-- End Modal input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>

<!-- Script Untuk Modal Detail -->
<script>
  $(document).ready(function () {
    $(document).on('click', '.btnDetail', function() {
        var id = $(this).data("id");

        $.ajax({
            url: "ajax/detail-kat-produk.php", 
            type: "POST",
            data: { id: id },
            success: function (response) {
                $("#detailContent").html(response);
            },
            error: function () {
                $("#detailContent").html('<p class="text-danger">Gagal mengambil data.</p>');
            }
        });
    });
  });
</script>

<!-- Script Untuk Modal Edit -->
<script>
  $(document).ready(function () {
    $(document).on('click', '.btnEdit', function() {
        var id = $(this).data("id");

        $.ajax({
            url: "ajax/edit-kat-produk.php", 
            type: "POST",
            data: { id: id },
            success: function (response) {
                $("#editContent").html(response);
            },
            error: function () {
                $("#editContent").html('<p class="text-danger">Gagal mengambil data.</p>');
            }
        });
    });
  });
</script>

<!-- Script Untuk Modal Edit -->
<script>
    $('#ubahMerk').on('show.bs.modal', function () {
        // Check if modal2 is open, if yes, hide it
        if ($('#modalDedail').hasClass('show')) {
            $('#modalDedail').modal('hide');
        }
    });

    $('#modalDedail').on('hide.bs.modal', function (e) {
        // Prevent #modalDedail from closing
        e.preventDefault();
    });

    $('#ubahMerk').on('hidden.bs.modal', function () {
        // Show modalDedail when ubahMerk is hidden
        $('#modalDedail').modal('show');
    });

   
   

    // select lokasi
    $(document).on('click', '#modalDedail tbody tr', function (e) {
      var selectedMerk = $(this).data('merk');
      $('#merk').val(selectedMerk).trigger('input'); // Trigger event input setelah mengubah nilai
      $('#ubahMerk').modal('hide');
    });
</script>


<script>
   flatpickr("#exp", {
        dateFormat: "d/m/Y"
    });
    flatpickr("#terbit", {
        dateFormat: "d/m/Y"
    });
</script>

<!-- Compress image  -->
<script src="assets/js/upload-file-pdf.js"></script>



