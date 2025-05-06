<?php
require_once "akses.php";
$page = 'produk';
$page2 = 'data-produk';
?>
<!DOCTYPE text>
<text lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Inventory KMA</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <?php include "page/head.php"; ?>
  <style>
    #gambarProduk {
      width: 500px;
      height: 600px;
      object-fit: contain;
      object-position: top;
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
    <!-- Loading -->
    <div class="loader loader">
      <div class="loading">
        <img src="img/loading.gif" width="200px" height="auto">
      </div>
    </div>
    <!-- ENd Loading -->
    <div class="pagetitle">
      <h1>Data Produk E-Catalog</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Data Produk</li>
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
            <a href="tambah-data-produk.php" class="btn btn-primary btn-md ms-3"><i class="bi bi-plus-circle"></i> Tambah data produk</a>
            <div class="mt-3 ms-3">
              <a href="data-produk-reg.php" class="btn btn-outline-success"><i class="bi bi-box-seam"></i> Produk Reguler</a>
              <a href="#" class="btn btn-outline-success active"><i class="bi bi-box-seam-fill"></i> Produk E-Catalog</a>
            </div>
            <div class="table-responsive mt-3">
              <table class="table table-striped table-bordered" id="dataProduk">
                <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <td class="text-center text-nowrap p-3" style="width: 30px">No</td>
                    <td class="text-center text-nowrap p-3" style="width: 150px">Kode Produk</td>
                    <td class="text-center text-nowrap p-3" style="width: 400px">Nama Produk</td>
                    <td class="text-center text-nowrap p-3" style="width: 100px">satuan</td>
                    <td class="text-center text-nowrap p-3" style="width: 100px">Merk</td>
                    <td class="text-center text-nowrap p-3" style="width: 100px">Harga</td>
                    <td class="text-center text-nowrap p-3" style="width: 80px">Stock</td>
                    <td class="text-center text-nowrap p-3" style="width: 80px">Level</td>
                    <td class="text-center text-nowrap p-3" style="width: 50px">Aksi</td>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <?php  
    if ($role == "Super Admin" || $role == "Manager Gudang") { 
      ?>
          <!-- Modal Hapus -->
          <div class="modal fade" id="hapusData" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form action="proses/proses-produk-reg.php" method="POST" enctype="multipart/form-data">
                  <div class="modal-body">
                    Apakah anda yakin hapus data <b id="nama_produk"></b>-<b id="merk"></b>?
                      <input type="hidden" name="id_produk" id="id_produk">
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger" name="hapus-produk-ecat">Ya, Hapus data</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
      <?php 
    } else {
      ?>
        <!-- Modal Hapus -->
        <div class="modal fade" id="hapusData" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                  <div class="modal-body">
                     Maaf Anda Tidak Memiliki Akses Fitur Ini
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                  </div>
              </div>
            </div>
          </div>
      <?php
    }
  ?>

  <!-- Modal Detail -->
  <div class="modal fade" id="detailProduk" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-body">
          <div class="card">
            <div class="card-header text-center">
              <h4><strong>Detail Produk Ecat</strong></h4>
            </div>
            <div class="card-body p-3">
              <div class="row">
                <div class="col-md-5">
                  <img alt="Gambar Produk" id="gambarProduk" class="img-fluid">
                </div>
                <div class="col-md-7">
                  <table class="table table-bordered table-striped">
                    <tr>
                      <td class="col-md-5">Kode Produk</td>
                      <td id="kodeProduk"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">No Izin Edar</td>
                      <td id="izinEdar"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Nama Produk</td>
                      <td id="namaProduk"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Kode Katalog</td>
                      <td id="kodeKatalog"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Satuan</td>
                      <td id="satuan"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Merk Produk</td>
                      <td id="merkProduk"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Harga Produk</td>
                      <td id="hargaProduk"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Stock Produk</td>
                      <td id="stockProduk"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Kategori Produk</td>
                      <td id="katProduk"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Kategori Penjualan</td>
                      <td id="katPenjualan"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Kategori Penjualan</td>
                      <td id="katGrade"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Lokasi Produk</td>
                      <td id="katLokasi"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">No. Lantai</td>
                      <td id="lantaiLokasi"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Area</td>
                      <td id="areaLokasi"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">No. Rak</td>
                      <td id="rakLokasi"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Dibuat Tanggal</td>
                      <td id="created"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Dibuat Oleh</td>
                      <td id="userCreated"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Diubah Tanggal</td>
                      <td id="updated"></td>
                    </tr>
                    <tr>
                      <td class="col-md-5">Diubah Oleh</td>
                      <td id="userUpdated"></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Modal Detail -->
  
  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <!-- end modal detail -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>

</text>

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

<?php
function format_rupiah($angka)
{
  $rupiah = "Rp " . number_format($angka, 0, ',', '.');
  return $rupiah;
}
?>
                                                            

<script>
    // untuk menampilkan data pada atribut <td>
    $('#detailProduk').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var kodeProduk = button.data('kode-produk');
        var izinEdar = button.data('izin-edar');
        var namaProduk = button.data('nama-produk');
        var kodeKatalog = button.data('kode-katalog');
        var satuan = button.data('satuan');
        var merkProduk = button.data('merk-produk');
        var hargaProduk = button.data('harga-produk');
        var stockProduk = button.data('stock-produk');
        var katProduk = button.data('kategori-produk');
        var katPenjualan = button.data('kategori-penjualan');
        var katGrade = button.data('grade-produk');
        var katLokasi = button.data('lokasi-produk');
        var lantaiLokasi = button.data('lantai-produk');
        var areaLokasi = button.data('area-produk');
        var rakLokasi = button.data('rak-produk');
        var gambarProduk = button.data('gambar-produk');
        var created = button.data('created-produk');
        var userCreated = button.data('user-created');
        var updated = button.data('update-produk');
        var userUpdated = button.data('user-update');
        
        var modal = $(this);
        modal.find('.modal-body #kodeProduk').text(kodeProduk);
        modal.find('.modal-body #izinEdar').text(izinEdar);
        modal.find('.modal-body #namaProduk').text(namaProduk);
        modal.find('.modal-body #kodeKatalog').text(kodeKatalog);
        modal.find('.modal-body #satuan').text(satuan);
        modal.find('.modal-body #merkProduk').text(merkProduk);
        modal.find('.modal-body #hargaProduk').text(hargaProduk);
        modal.find('.modal-body #stockProduk').text(stockProduk);
        modal.find('.modal-body #katProduk').text(katProduk);
        modal.find('.modal-body #katPenjualan').text(katPenjualan);
        modal.find('.modal-body #katGrade').text(katGrade);
        modal.find('.modal-body #katLokasi').text(katLokasi);
        modal.find('.modal-body #lantaiLokasi').text(lantaiLokasi);
        modal.find('.modal-body #areaLokasi').text(areaLokasi);
        modal.find('.modal-body #rakLokasi').text(rakLokasi);
        modal.find('.modal-body #gambarProduk').attr('src', 'gambar/upload-produk-ecat/' + gambarProduk);
        modal.find('.modal-body #created').text(created);
        modal.find('.modal-body #userCreated').text(userCreated);
        modal.find('.modal-body #updated').text(updated);
        modal.find('.modal-body #userUpdated').text(userUpdated);
    })
</script>

<script>
    // untuk menampilkan data pada atribut <td>
    $('#modal3').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var user = button.data('user');
        var lokasi = button.data('lokasi');
        var lantai = button.data('lantai');
        var area = button.data('area');
        var rak = button.data('rak');
        var created = button.data('created');
        var updated = button.data('updated');
        var userupdated = button.data('userupdated');
        var modal = $(this);
        modal.find('.modal-body #id_lokasi').html(id);
        modal.find('.modal-body #user_created').html(user);
        modal.find('.modal-body #nama_lokasi').html(lokasi);
        modal.find('.modal-body #lantai').html(lantai);
        modal.find('.modal-body #area').html(area);
        modal.find('.modal-body #rak').html(rak);
        modal.find('.modal-body #created_date').html(created);
        modal.find('.modal-body #updated_date').html(updated);
        modal.find('.modal-body #user_updated').html(userupdated);
    })
</script>

<script>
    // untuk menampilkan data pada atribut <td>
    $('#hapusData').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nama = button.data('nama');
        var merk = button.data('merk');
        
        var modal = $(this);
        modal.find('.modal-body #id_produk').val(id);
        modal.find('.modal-body #nama_produk').text(nama);
        modal.find('.modal-body #merk').text(merk);
    })
</script>

<script>
    $(document).ready(function() {
        var table = $('#dataProduk').DataTable({
            "lengthChange": false,
            "processing": false,
            "serverSide": true,
            "ajax": {
                "url": "server-side/produk-ecat.php", // Sesuaikan dengan alamat skrip server-side Anda
                "type": "POST"
            },
            "columns": [
                { "data": "0" },
                { "data": "1" },
                { "data": "2" },
                { "data": "3" },
                { "data": "4" },
                { "data": "5" },
                { "data": "6" },
                { "data": "7" },
                { "data": "8" }
            ],
            language: {
              searchPlaceholder: "Cari data",
              search: ""
            }
        });
    });
</script>
<script>
    // Mengambil semua elemen gambar pada halaman
    var images = document.querySelectorAll('img');

    // Mengaitkan event listener untuk mencegah klik kanan pada gambar
    images.forEach(function (image) {
        image.addEventListener('contextmenu', function (event) {
            // Mencegah menu konteks muncul saat klik kanan
            event.preventDefault();

            // Menampilkan pesan khusus (opsional)
            alert('Right-clicking is not allowed!');
        });
    });

    // Menyembunyikan elemen <script> dari alat pengembang
    document.addEventListener('keydown', function (event) {
        // Check for F12 or Ctrl+Shift+I
        if (event.key === 'F12' || (event.ctrlKey && event.shiftKey && event.key === 'I')) {
            event.preventDefault();
            alert('Developer tools are disabled.');
        }

        // Check for PrintScreen key or Alt+PrintScreen
        if (event.key === 'PrintScreen' || (event.altKey && event.key === 'PrintScreen')) {
            event.preventDefault();
            alert('Screenshots are not allowed.');
        }
    });
</script>
 
