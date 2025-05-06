<?php
$page = 'produk';
$page2 = 'lokasi';
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

    <style>
        .text-center button {
            margin-right: 5px; /* Atur jarak kanan antara tombol-tombol */
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
            <h1>Lokasi Produk</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Lokasi Produk</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body mt-3">
                        <button class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#modal1" style="margin-left: 12px;">
                            <i class="bi bi-plus-circle"></i> Tambah data lokasi produk
                        </button>
                        <div class="table-responsive mt-3">
                            <button class="btn" id="export"></button>
                            <table class="table table-bordered table-striped" id="tableNew">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                        <td class="text-center text-nowrap p-3" style="width: 80px">No</td>
                                        <td class="text-center text-nowrap p-3" style="width: 200px">Lokasi</td>
                                        <td class="text-center text-nowrap p-3" style="width: 200px">No. Lantai</td>
                                        <td class="text-center text-nowrap p-3" style="width: 300px">Area</td>
                                        <td class="text-center text-nowrap p-3" style="width: 150px">No. Rak</td>
                                        <td class="text-center text-nowrap p-3" style="width: 150px">Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                        <tr>    
                                        <?php  
                                            if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") { 
                                            ?>
                                                    <!-- Modal Edit  -->
                                                <div class="modal fade" id="modal2" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5">Edit Data Kategori Penjualan</h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="proses/proses-lokasi-produk.php" method="POST">
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Lokasi</label>
                                                                        <input type="hidden" class="form-control" name="id_lokasi_produk" id="id_lokasi">
                                                                        <input type="text" class="form-control" name="lokasi" id="nama_lokasi" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">No. Lantai</label>
                                                                        <input type="text" class="form-control" name="no_lantai" id="lantai" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Area</label>
                                                                        <input type="text" class="form-control" name="area" id="area" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">No. Rak</label>
                                                                        <input type="text" class="form-control" name="no_rak" id="rak" required>
                                                                    </div>
                                                                    <input type="hidden" class="form-control" name="updated" id="datetime-edit">
                                                                    <input type="hidden" class="form-control" name="user_updated" value="<?php echo $_SESSION['tiket_id'] ?>">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" name="edit-lokasi-produk" id="simpan" class="btn btn-primary btn-md" disabled><i class="bx bx-save"></i> Simpan Perubahan</button>
                                                                    <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal"><i class="bi bi-x"></i> Tutup</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Modal Edit -->
                                            <?php 
                                            } else {
                                                ?>
                                                    <!-- Modal Hapus -->
                                                    <div class="modal fade" id="modal2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                                        </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Form Input -->
    </main>
    <!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>

</html>
 <!-- Modal Info -->
 <div class="modal fade" id="modal3" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Informasi Lengkap</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <td class="col-4 text-start">Lokasi</td>
                            <td class="col-8 text-start" id="nama_lokasi"></td>
                        </tr>
                        <tr>
                            <td class="col-4 text-start">No. Lantai</td>
                            <td class="col-8 text-start" id="lantai"></td>
                        </tr>
                        <tr>
                            <td class="col-4 text-start">Area</td>
                            <td class="col-8 text-start" id="area"></td>
                        </tr>
                        <tr>
                            <td class="col-4 text-start">No. Rak</td>
                            <td class="col-8 text-start" id="rak"></td>
                        </tr>
                        <tr>
                            <td class="col-4 text-start">Dibuat Oleh</td>
                            <td class="col-8 text-start" id="user_created"></td>
                        </tr>
                        <tr>
                            <td class="col-4 text-start">Dibuat Tanggal</td>
                            <td class="col-8 text-start" id="created_date"></td>
                        </tr>
                        <tr>
                            <td class="col-4 text-start">Diubah Oleh</td>
                            <td class="col-8 text-start" id="user_updated"></td>
                        </tr>
                        <tr>
                            <td class="col-4 text-start">Diubah Tanggal</td>
                            <td class="col-8 text-start" id="updated_date"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Info -->


<?php  
    if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") { 
      ?>
        <!-- Modal Hapus-->
        <div class="modal fade" id="hapusData" tabindex="-1" data-bs-backdrop="static" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <form action="proses/proses-lokasi-produk.php" method="POST">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="id_lokasi_produk" id="id_lokasi">
                        <p>Apakah anda ingin hapus data lokasi <br><b id="nama_lokasi"></b> - <b id="lantai"></b> - <b id="area"></b> - <b id="rak"></b> ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-danger" name="hapus-lokasi-produk">
                            Ya, Hapus data
                        </button>
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

<!-- Modal Input-->
<div class="modal fade" id="modal1" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Tambah Data Kategori Penjualan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="proses/proses-lokasi-produk.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <?php
                        $UUID = generate_uuid();
                        ?>
                        <div class="mb-3">
                            <label class="form-label">Lokasi</label>
                            <input type="hidden" class="form-control" name="id_lokasi_produk" value="LOK<?php echo $UUID; ?>">
                            <input type="text" class="form-control" name="lokasi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Lantai</label>
                            <input type="text" class="form-control" name="no_lantai" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Area</label>
                            <input type="text" class="form-control" name="area" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Rak</label>
                            <input type="text" class="form-control" name="no_rak" value="" required>
                        </div>
                        <input type="hidden" class="form-control" name="id_user" value="<?php echo $_SESSION['tiket_id']; ?>">
                        <input type="hidden" class="form-control" name="created" id="datetime-input">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="simpan-lokasi-produk" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Simpan Data</button>
                        <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal"><i class="bi bi-x"></i> Tutup</button>
                    </div>
            </form>
        </div>
    </div>
</div>
<!-- End Modal Input-->

<!-- Clock js -->
<script src="assets/js/clock.js"></script>



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
    $('#modal2').on('show.bs.modal', function(event) {
        // Mendapatkan data dari tombol yang ditekan
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var lokasi = button.data('lokasi');
        var lantai = button.data('lantai');
        var area = button.data('area');
        var rak = button.data('rak');
        var modal = $(this);
        var simpanBtn = modal.find('.modal-footer #simpan');
        var lokasiInput = modal.find('.modal-body #nama_lokasi');
        var lantaiInput = modal.find('.modal-body #lantai');
        var areaInput = modal.find('.modal-body #area');
        var rakInput = modal.find('.modal-body #rak');

        // Menampilkan data
        modal.find('.modal-body #id_lokasi').val(id);
        lokasiInput.val(lokasi);
        lantaiInput.val(lantai);
        areaInput.val(area);
        rakInput.val(rak);

        // Pengecekan data, dan buttun disable or enable saat data di ubah
        // dan data kembali ke nilai awal

        var originalLokasi = lokasiInput.val();
        var originalLantai = lantaiInput.val();
        var originalArea = areaInput.val();
        var originalRak = rakInput.val();

        lokasiInput.on('input', function() {
            var currentLokasi = $(this).val();
            var currentLantai = lantaiInput.val();
            var currentArea = areaInput.val();
            var currentRak = rakInput.val();
            if (currentLokasi != originalLokasi || currentLantai != originalLantai || currentArea != originalArea || currentRak != originalRak) {
                simpanBtn.prop('disabled', false);
            } else {
                simpanBtn.prop('disabled', true);
            }
        });

        lantaiInput.on('input', function() {
            var currentLantai = $(this).val();
            var currentLokasi = lokasiInput.val();
            var currentArea = areaInput.val();
            var currentRak = rakInput.val();
            if (currentLokasi != originalLokasi || currentLantai != originalLantai || currentArea != originalArea || currentRak != originalRak) {
                simpanBtn.prop('disabled', false);
            } else {
                simpanBtn.prop('disabled', true);
            }
        });

        areaInput.on('input', function() {
            var currentArea = $(this).val();
            var currentLokasi = lokasiInput.val();
            var currentLantai = lantaiInput.val();
            var currentRak = rakInput.val();
            if (currentLokasi != originalLokasi || currentLantai != originalLantai || currentArea != originalArea || currentRak != originalRak) {
                simpanBtn.prop('disabled', false);
            } else {
                simpanBtn.prop('disabled', true);
            }
        });

        rakInput.on('input', function() {
            var currentRak = $(this).val();
            var currentLokasi = lokasiInput.val();
            var currentArea = areaInput.val();
            var currentLantai = lantaiInput.val();
            if (currentLokasi != originalLokasi || currentLantai != originalLantai || currentArea != originalArea || currentRak != originalRak) {
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
        var lokasi = button.data('lokasi');
        var lantai = button.data('lantai');
        var area = button.data('area');
        var rak = button.data('rak');
        
        var modal = $(this);
        modal.find('.modal-body #id_lokasi').val(id);
        modal.find('.modal-body #nama_lokasi').text(lokasi);
        modal.find('.modal-body #lantai').text(lantai);
        modal.find('.modal-body #area').text(area);
        modal.find('.modal-body #rak').text(rak);
    })
</script>

<script>
    $(document).ready(function() {
        const table = $('#tableNew').DataTable({
            "processing": false,
            "serverSide": true,
            "ajax": {
                "url": "server-side/lokasi.php",
                "type": "POST",
                "error": function(xhr, error, thrown) {
                    console.log("Error:", error);
                    console.log("Thrown:", thrown);
                    alert("Terjadi kesalahan saat memuat data. Silakan coba lagi.");
                }
            },
            "columns": [
                { "data": 0 },
                { "data": 1 },
                { "data": 2 },
                { "data": 3 },
                { "data": 4 },
                { "data": 5, "orderable": false }
            ],
            "order": [[0, 'asc']],
            "lengthChange": false,
            "language": {
                searchPlaceholder: "Cari data",
                search: "",
            }
        });

        // Inisialisasi tombol ekspor
        new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: "print",
                    title: "",
                    text: '<i class="bi bi-printer-fill"></i>',
                    titleAttr: "Print",
                    exportOptions: {
                        columns: ":not(:last-child)", // Mengabaikan kolom terakhir
                        modifier: {
                            selected: false // Export hanya data yang ditampilkan
                        }
                    },
                },
                {
                    extend: "excelHtml5",
                    title: "",
                    text: '<i class="bi bi-file-earmark-excel"></i>',
                    titleAttr: "Excel",
                    exportOptions: {
                        columns: ":not(:last-child)", // Mengabaikan kolom terakhir
                        modifier: {
                            selected: false // Export hanya data yang ditampilkan
                        }
                    },
                },
                {
                    extend: "csvHtml5",
                    title: "",
                    text: '<i class="bi bi-file-text"></i>',
                    titleAttr: "CSV",
                    exportOptions: {
                        columns: ":not(:last-child)", // Mengabaikan kolom terakhir
                        modifier: {
                            page: "all",
                            search: "none"
                        }
                    },
                },
                {
                    extend: "pdfHtml5",
                    title: "",
                    text: '<i class="bi bi-file-pdf"></i>',
                    titleAttr: "PDF",
                    exportOptions: {
                        columns: ":not(:last-child)", // Mengabaikan kolom terakhir
                        modifier: {
                            selected: false // Export hanya data yang ditampilkan
                        }
                    },
                }
            ]
        });

        // Tempatkan tombol ekspor di dalam elemen #exportButtons
        table.buttons().container().appendTo($('#export'));
    });
</script>
