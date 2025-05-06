<?php
require_once "akses.php";
$page = 'produk';
$page2 = 'grade';
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
            <h1>Grade Produk</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Grade Produk</li>
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
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal1"><i class="bi bi-plus-circle"></i> Tambah Data Grade Produk</button>
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-bordered" id="table1">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                        <td class="p-3 text-center" style="width: 70px">No</td>
                                        <td class="p-3 text-center" style="width: 250px">Grade Produk</td>
                                        <td class="p-3 text-center" style="width: 200px">Dibuat Oleh</td>
                                        <td class="p-3 text-center" style="width: 150px">Dibuat Tanggal</td>
                                        <td class="p-3 text-center" style="width: 100px">Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include "koneksi.php";
                                    $no = 1;
                                    $sql = "SELECT 
                                                gr.id_grade,
                                                gr.nama_grade,
                                                  DATE_FORMAT(gr.created_date, '%d/%m/%Y, %H:%i:%s') AS created_date,  -- Format tanggal Indonesia
                                                CASE 
                                                    WHEN gr.updated_date = '0000-00-00 00:00:00' THEN '-'
                                                    ELSE DATE_FORMAT(gr.updated_date, '%d/%m/%Y, %H:%i:%s')
                                                END AS updated_date, 
                                                uc.nama_user AS user_created, 
                                                uu.nama_user AS user_updated
                                            FROM tb_produk_grade AS gr 
                                            LEFT JOIN $database2.user AS uc ON (gr.created_by = uc.id_user)
                                            LEFT JOIN $database2.user AS uu ON (gr.updated_by = uu.id_user)";
                                    $query = mysqli_query($connect, $sql) or die(mysqli_error($connect, $sql));
                                    while ($data = mysqli_fetch_array($query)) {
                                        $id_grade = $data['id_grade'];
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no; ?></td>
                                            <td><?php echo $data['nama_grade']; ?></td>
                                            <td><?php echo $data['user_created']; ?></td>
                                            <td class="text-center"><?php echo $data['created_date']; ?></td>
                                            <td class="text-center">
                                                <!-- Button Edit -->
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modal2" data-id="<?php echo encrypt($data['id_grade'], $key_global)?>" data-grade="<?php echo $data['nama_grade']; ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="proses/proses-grade-produk.php?hapus-grade-produk=<?php echo encrypt($id_grade, $key_global); ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                                            </td>
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
        <!-- Modal Edit  -->
        <div class="modal fade" id="modal2" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Edit Data Grade Produk</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="proses/proses-grade-produk.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Grade Produk</label>
                                <input type="hidden" class="form-control" name="id_grade" id="id_grade">
                                <input type="text" class="form-control" name="grade" id="grade" required>
                            </div>
                            <div class="modal-footer">
                            <button type="submit" name="edit-grade-produk" id="simpan" class="btn btn-primary btn-md" disabled><i class="bx bx-save"></i> Simpan Perubahan</button>
                            <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal"><i class="bi bi-x"></i> Tutup</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Edit -->
    </main>
    <!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>

</html>

<!-- Modal Input-->
<div class="modal fade" id="modal1" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Tambah Data Grade Produk</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="proses/proses-grade-produk.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <?php
                        $UUID = generate_uuid();
                        ?>
                        <div class="mb-3">
                            <label class="form-label">Grade Produk</label>
                            <input type="hidden" class="form-control" name="id_grade" value="GRADE<?php echo $UUID; ?>">
                            <input type="text" class="form-control" name="grade" required>
                        </div>
                        <input type="hidden" class="form-control" name="id_user" value="<?php echo $_SESSION['tiket_id']; ?>">
                        <input type="hidden" class="form-control" name="created" id="datetime-input">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="simpan-grade-produk" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Simpan Data</button>
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
        var grade = button.data('grade');

        var modal = $(this);
        var simpanBtn = modal.find('.modal-footer #simpan');
        var gradeInput = modal.find('.modal-body #grade');

        // Menampilkan data
        modal.find('.modal-body #id_grade').val(id);
        gradeInput.val(grade);

        // Pengecekan data, dan buttun disable or enable saat data di ubah
        // dan data kembali ke nilai awal
        var originalGrade = gradeInput.val();

        gradeInput.on('input', function() {
            var currentGrade = $(this).val();
            if (currentGrade != originalGrade) {
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