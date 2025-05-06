<?php
require_once "akses.php";
$page = 'orderby';
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
            <h1>Data Order By</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Order By</li>
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
                        <a href="#" class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#modal1"><i class="bi bi-plus-circle"></i> Tambah data order by</a>
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-bordered" id="table1">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                        <td class="text-center p-3 col-1">No</td>
                                        <td class="text-center p-3 col-7">Order By</td>
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
                                                ord.*, 
                                                ord.created_date AS created, 
                                                us.nama_user 
                                            FROM tb_orderby AS ord
                                            LEFT JOIN $database2.user AS us ON (ord.id_user = us.id_user)
                                            ORDER BY order_by ASC";
                                    $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                                    while ($data = mysqli_fetch_array($query)) {
                                        $id_orderby = base64_encode($data['id_orderby']);
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no ?></td>
                                            <td><?php echo $data['order_by']; ?></td>
                                            <td><?php echo $data['created']; ?></td>
                                            <td class="text-center">
                                                <a href="proses/proses-orderby.php?hapus=<?php echo $id_orderby ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
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
                <form action="proses/proses-orderby.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <?php
                            $UUID = generate_uuid();
                            ?>
                            <div class="mb-3">
                                <label class="form-label">Order By</label>
                                <input type="hidden" class="form-control" name="id_orderby" value="ORDER<?php echo $UUID; ?>">
                                <input type="text" class="form-control" name="order_by" required>
                                <input type="hidden" class="form-control" name="created" value="<?php echo date('d/m/Y, G:i') ?>">
                                <input type="hidden" class="form-control" name="id_user" value="<?php echo $_SESSION['tiket_id']; ?>">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="simpan" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Simpan Data</button>
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