<?php
$page = 'br-masuk';
$page2 = 'br-masuk-reg';
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">

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

        textarea[type="text"]:read-only {
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
        <!-- Loading -->
        <div class="loader loader">
            <div class="loading">
                <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div>
        <!-- ENd Loading -->
        <section>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-center">Edit Invoice Barang Masuk Lokal</h5>
                    </div>
                    <div class="card-body p-3">
                        <form action="proses/proses-br-in-lokal.php" method="post">
                            <div class="mb-3">
                                <?php
                                $ide = base64_decode($_GET['id']);
                                $sql = "SELECT ibil.*, ibil.created_date AS created, us.*, sp.*
                                            FROM inv_br_in_lokal AS ibil
                                            LEFT JOIN user us ON (ibil.id_user = us.id_user)
                                            LEFT JOIN tb_supplier sp ON (ibil.id_sp = sp.id_sp)
                                            WHERE id_inv_br_in_lokal = '$ide'";
                                $query = mysqli_query($connect, $sql);
                                $data = mysqli_fetch_array($query);
                                ?>
                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <label>No. Invoice</label>
                                        <input type="hidden" class="form-control" name="id_inv_br_in_lokal" value="<?php echo $data['id_inv_br_in_lokal']; ?>">
                                        <input type="text" class="form-control" id="no_inv" name="no_inv" value="<?php echo $data['no_inv']; ?>" required>
                                    </div>
                                    <div class=" col-sm-6 mb-3">
                                        <label>Tgl. Invoice</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white" id="date" name="tgl_inv" value="<?php echo $data['tgl_inv']; ?>" required>
                                            <span class=" input-group-text"><i class="bi bi-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 mb-3">
                                        <label>Supplier</label>
                                        <input type="hidden" class="form-control" id="id" name="id_sp" value="<?php echo $data['id_sp']; ?>">
                                        <input type="text" class="form-control" id="sp" name="sp" value="<?php echo $data['nama_sp']; ?>" data-bs-toggle="modal" data-bs-target="#modalSp" readonly>
                                    </div>
                                    <div class="col-sm-8 mb-3">
                                        <label>Alamat</label>
                                        <textarea type="text" class="form-control" id="alamat" name="alamat" readonly><?php echo $data['alamat']; ?></textarea>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-primary btn-md m-2" name="edit-br-in-lokal"><i class="bx bx-save"></i> Simpan Data</button>
                                    <a href="barang-masuk-lokal.php" class="btn btn-secondary m-2"><i class="bi bi-x-circle"></i> Tutup</a>
                                </div>
                            </div>
                        </form>
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
</body>

</html>
<!-- Modal SP -->
<div class="modal fade" id="modalSp" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="table2">
                        <thead>
                            <tr class="text-white" style="background-color: navy;">
                                <td class="text-center p-2">Nama Supplier</td>
                                <td class="text-center p-2">Alamat</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "koneksi.php";
                            $sql = "SELECT * FROM tb_supplier ORDER BY nama_sp";
                            $query = mysqli_query($connect, $sql);
                            while ($data = mysqli_fetch_array($query)) {
                            ?>
                                <tr data-id="<?php echo $data['id_sp'] ?>" data-nama="<?php echo $data['nama_sp'] ?>" data-alamat="<?php echo $data['alamat'] ?>">
                                    <td><?php echo $data['nama_sp']; ?></td>
                                    <td><?php echo $data['alamat']  ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal SP -->

<!-- Script Select Data Supplier -->
<script>
    $(document).on('click', '#table2 tbody tr', function(e) {
        $('#id').val($(this).data('id'));
        $('#sp').val($(this).data('nama'));
        $('#alamat').val($(this).data('alamat'));
        $('#modalSp').modal('hide');
    });
</script>

<!-- date picker with flatpick -->
<script type="text/javascript">
    flatpickr("#date", {
        dateFormat: "d/m/Y",
    });

    flatpickr("#tgl_kirim", {
        dateFormat: "d/m/Y",
    });
</script>
<!-- end date picker -->