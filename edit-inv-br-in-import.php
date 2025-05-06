<?php
require_once "akses.php";
$page = 'br-masuk';
$page2 = 'br-masuk-import';
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
                        <h5 class="text-center">Form Edit Data Barang Masuk Import</h5>
                    </div>
                    <div class="card-body p-3">
                        <form action="proses/proses-br-in-import.php" method="post">
                            <div class="mb-3">
                                <?php
                                $ide = decrypt($_GET['id'], $key_global);
                                $sql = "SELECT 
                                            ibi.id_inv_br_import,
                                            ibi.no_inv,
                                            ibi.tgl_inv,
                                            ibi.no_order,
                                            ibi.tgl_order,
                                            ibi.shipping_by,
                                            ibi.no_awb,
                                            ibi.tgl_kirim,
                                            ibi.tgl_est,
                                            sp.id_sp,
                                            sp.nama_sp
                                        FROM inv_br_import AS ibi 
                                        LEFT JOIN  tb_supplier sp ON (ibi.id_supplier = sp.id_sp)
                                        WHERE ibi.id_inv_br_import = '$ide'";
                                $query = mysqli_query($connect, $sql);
                                $data = mysqli_fetch_array($query);
                                ?>
                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <label>No. Invoice</label>
                                        <input type="hidden" class="form-control" name="id_inv_br_import" value="<?php echo encrypt($data['id_inv_br_import'], $key_global) ?>">
                                        <input type="text" class="form-control" id="no_inv" name="no_inv" value="<?php echo $data['no_inv'] ?>" required>
                                    </div>
                                    <div class=" col-sm-6 mb-3">
                                        <label>Tgl. Invoice</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white" id="date" name="tgl_inv" value="<?php echo $data['tgl_inv'] ?>" required>
                                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 mb-3">
                                        <label>Supplier</label>
                                        <input type="hidden" class="form-control" id="id" name="id_sp" value="<?php echo $data['id_sp'] ?>">
                                        <input type="text" class="form-control" id="sp" name="sp" data-bs-toggle="modal" data-bs-target="#modalSp" value="<?php echo $data['nama_sp'] ?>" readonly>
                                    </div>
                                    <div class="col-sm-8 mb-3">
                                        <label>Alamat</label>
                                        <textarea type="text" class="form-control" id="alamat" name="alamat" readonly><?php echo $data['alamat'] ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 mb-3">
                                        <label>No. Order</label>
                                        <input type="text" class="form-control" name="no_order" value="<?php echo $data['no_order'] ?>" required>
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <label>Tgl. Order</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white" id="date" name="tgl_order" value="<?php echo $data['tgl_order'] ?>" required>
                                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <label>No. AWB</label>
                                        <input type="text" class="form-control" name="no_awb" value="<?php echo $data['no_awb'] ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 mb-3">
                                        <label>Tgl. Kirim</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white" id="tgl_kirim" name="tgl_kirim" value="<?php echo $data['tgl_kirim'] ?>" required>
                                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <label>Shipping</label>
                                        <select class="form-control form-select" id="limit" name="ship" required>
                                            <option value="<?php echo $data['shipping_by'] ?>"><?php echo $data['shipping_by'] ?></option>
                                            <option value="10">Udara</option>
                                            <option value="30">Laut</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <label>Tgl. Estimasi</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-white" id="est" name="tgl_est" value="<?php echo date($data['tgl_est']) ?>" readonly>
                                            <span class="input-group-text" onclick="flatpickr('#est')"><i class="bi bi-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-primary btn-md m-2" name="edit-inv-br-in-import"><i class="bx bx-save"></i> Ubah Data</button>
                                    <a href="barang-masuk-reg-import.php" class="btn btn-secondary m-2"><i class="bi bi-x-circle"></i> Tutup</a>
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

<!-- Tanggal Estimasi Barang Sampai -->
<script type="text/javascript">
    // Menampilkan kalender JIka User ingin merubah tanggal Est
    flatpickr("#est", {
        enableTime: false,
        dateFormat: "d/m/Y",
        onClose: function(selectedDates, dateStr, instance) {
            // Update input value when a date is selected
            document.getElementById("est").value = dateStr;
        }
    });
    $(document).ready(function() {
        var lb = $('#ExpDate');
        var shipSelect = $('#limit');
        var tglKirimInput = $('#tgl_kirim');
        var tglEstInput = $('#est');

        // Set selected option as default value
        var selectedOption = shipSelect.find('option:selected');
        var defaultValue = selectedOption.val();
        var defaultText = selectedOption.text();
        shipSelect.val(defaultValue);
        lb.html(defaultText);

        tglKirimInput.on('change', function() {
            if (tglKirimInput.val()) {
                // Enable select element
                shipSelect.prop('disabled', false);

                if (shipSelect.val()) {
                    var limit = shipSelect.val();
                    var currentDate = moment(tglKirimInput.val(), "DD/MM/YYYY");
                    var adding = currentDate.add(Number(limit), 'days');
                    tglEstInput.val(adding.format("DD/MM/YYYY"));
                }
            } else {
                // Disable select element and set to default value
                shipSelect.prop('disabled', true);
                shipSelect.val(defaultValue);
                lb.html(defaultText);

                tglEstInput.val('');
            }
        });

        shipSelect.on('change', function() {
            if (tglKirimInput.val()) {
                var limit = $(this).val();
                lb.html(limit);
                var currentDate = moment(tglKirimInput.val(), "DD/MM/YYYY");
                var adding = currentDate.add(Number(limit), 'days');
                tglEstInput.val(adding.format("DD/MM/YYYY"));
            }
        });

        tglEstInput.on('change', function() {
            var estDate = $(this).val();
            // Lakukan hal yang Anda inginkan dengan tanggal estimasi yang baru, misalnya menyimpannya ke database atau menampilkan ke pengguna.
        });
    });
</script>
<!-- End Tanggal Estimasi Barang Sampai -->