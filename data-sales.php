<?php
require_once "akses.php";
$page = 'sales';
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
        <!-- End Loading -->
        <div class="pagetitle">
            <h1>Data Sales</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Sales</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section>
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body p-2">
                        <a href="#" class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#modal1"><i class="bi bi-plus-circle"></i> Tambah data sales</a>
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-bordered" id="table1">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                        <td class="text-center p-3 col-1">No</td>
                                        <td class="text-center p-3 col-5">Nama Sales</td>
                                        <td class="text-center p-3 col-2">Telepon</td>
                                        <td class="text-center p-3 col-2">Dibuat Tanggal</td>
                                        <td class="text-center p-3 col-2">Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    date_default_timezone_set('Asia/Jakarta');
                                    include "koneksi.php";
                                    $no = 1;
                                    $sql = "SELECT 
                                                sl.*, 
                                                sl.created_date AS created, 
                                                us.nama_user
                                            FROM tb_sales AS sl
                                             LEFT JOIN $database2.user AS us ON (sl.id_user = us.id_user)
                                            ORDER BY nama_sales ASC";
                                    $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                                    while ($data = mysqli_fetch_array($query)) {
                                        $id_sales = base64_encode($data['id_sales']);
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no ?></td>
                                            <td><?php echo $data['nama_sales']; ?></td>
                                            <td><?php echo $data['no_telp']; ?></td>
                                            <td class="text-center"><?php echo $data['created']; ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modal2" data-id="<?php echo $data['id_sales']; ?>" data-nama="<?php echo $data['nama_sales']; ?>" data-telp="<?php echo $data['no_telp']; ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="proses/proses-sales.php?hapus-sales=<?php echo $id_sales ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                                            </td>
                                            <!-- Modal Edit Sales -->
                                            <div class="modal" id="modal2" tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5">Edit Data Customer</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="proses/proses-sales.php" method="POST">
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Nama Supplier</label>
                                                                        <input type="hidden" class="form-control" name="id_sales" id="id_sales">
                                                                        <input type="text" class="form-control" name="nama_sales" id="nama" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Telepon</label>
                                                                        <input type="text" class="form-control" name="telp_sales" id="telp" required>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit" name="edit-sales" id="simpan" class="btn btn-primary btn-md" disabled><i class="bx bx-save"></i> Simpan Perubahan</button>
                                                                    <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal"><i class="bi bi-x"></i> Tutup</button>
                                                                </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Modal Edit Sales -->
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

    <?php include "page/script.php" ?>'

    <!-- Modal Add Sales -->
    <div class="modal fade" id="modal1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Tambah Data Sales</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="proses/proses-sales.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <?php
                            $UUID = generate_uuid();
                            ?>
                            <div class="mb-3">
                                <label class="form-label">Nama Sales</label>
                                <input type="hidden" class="form-control" name="id_sales" value="SL<?php echo $UUID; ?>">
                                <input type="text" class="form-control" name="nama_sales" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" class="form-control" name="telp" required>
                                <input type="hidden" class="form-control" name="created" value="<?php echo date('d/m/Y, G:i') ?>">
                                <input type="hidden" class="form-control" name="id_user" value="<?php echo $_SESSION['tiket_id']; ?>">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="simpan-sales" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Simpan Data</button>
                            <button type="button" class="btn btn-secondary btn-md" data-bs-dismiss="modal"><i class="bi bi-x"></i> Tutup</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal Add Sales -->
</body>

</html>

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
    // delete button
    $("#table1").on("click", ".delete-button", function() {
        $(this).closest("tr").remove();
        if ($("#table1 tbody tr").length === 0) {
            $("#table1 tbody").append("<tr><td colspan='5' align='center'>Data not found</td></tr>");
        }
    });
</script>

<!-- Modal edit -->

<script>
    $('#modal2').on('show.bs.modal', function(event) {
        // Mendapatkan data dari tombol yang ditekan
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nama = button.data('nama');
        var telp = button.data('telp');
        var modal = $(this);
        var simpanBtn = modal.find('.modal-footer #simpan');
        var namaInput = modal.find('.modal-body #nama');
        var telpInput = modal.find('.modal-body #telp');

        // Menampilkan data
        modal.find('.modal-body #id_sales').val(id);
        namaInput.val(nama);
        telpInput.val(telp);

        // Pengecekan data, dan buttun disable or enable saat data di ubah
        // dan data kembali ke nilai awal
        var originalNama = namaInput.val();
        var originalTelp = telpInput.val();

        namaInput.on('input', function() {
            var currentNama = $(this).val();
            var currentTelp = telpInput.val();

            if (currentNama != originalNama || currentTelp != originalTelp) {
                simpanBtn.prop('disabled', false);
            } else {
                simpanBtn.prop('disabled', true);
            }
        });


        telpInput.on('input', function() {
            var currentTelp = $(this).val();
            var currentNama = namaInput.val();

            if (currentNama != originalNama || currentTelp != originalTelp) {
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