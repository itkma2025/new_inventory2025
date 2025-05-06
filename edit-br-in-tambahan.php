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
        <!-- <div class="loader loader">
            <div class="loading">
                <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div> -->
        <!-- ENd Loading -->
        <section>
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="container-fluid">
                <div class="card shadow p-3">
                    <?php
                    $id = decrypt($_GET['id'], $key_global);
                    $sql = "SELECT 
                                ibt.id_isi_br_tambahan, 
                                ibt.id_produk_reg,
                                ibt.qty,
                                pr.nama_produk, 
                                mr.nama_merk, 
                                ket_in.id_ket_in,
                                ket_in.ket_in
                            FROM isi_br_tambahan AS ibt
                            LEFT JOIN tb_produk_reguler pr ON(ibt.id_produk_reg = pr.id_produk_reg)
                            LEFT JOIN tb_merk mr ON(mr.id_merk = pr.id_merk)
                            LEFT JOIN keterangan_in ket_in ON(ibt.id_ket_in = ket_in.id_ket_in)
                            WHERE id_isi_br_tambahan = '$id'";
                    $query = mysqli_query($connect, $sql);
                    $data = mysqli_fetch_array($query);
                    ?>
                    <form method="post" action="proses/proses-br-in-tambahan.php" class="form">
                        <div class="row">
                            <input type="hidden" class="form-control" name="id_br" value="<?php echo encrypt($data['id_isi_br_tambahan'], $key_global) ?>">
                            <div class="col-sm-4 mb-3">
                                <label for="nama_produk">Nama Produk</label>
                                <input type="hidden" class="form-control" name="id_produk" id="idProduk" value="<?php echo encrypt($data['id_produk_reg'], $key_global) ?>">
                                <input type="text" class="form-control" name="nama_produk" id="namaProduk" value="<?php echo $data['nama_produk'] ?>" readonly>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label>Merk</label>
                                <input type="text" class="form-control" id="merkProduk" value="<?php echo $data['nama_merk'] ?>" readonly>
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label>Qty</label>
                                <input type="text" class="form-control" name="qty" id="qtyInput" value="<?php echo number_format($data['qty'], 0, '.', '.') ?>">
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label>Keterangan</label>
                                <select class="form-select" name="keterangan" id="ket">
                                    <option value="<?php echo $data['id_ket_in'] ?>"> <?php echo $data['ket_in'] ?></option>
                                    <?php
                                    $sql = mysqli_query($connect, "SELECT * FROM keterangan_in");
                                    while ($data = mysqli_fetch_array($sql)) {
                                    ?>
                                        <option value="<?php echo $data['id_ket_in'] ?>"> <?php echo $data['ket_in'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" name="edit" id="submitButton" class="btn btn-primary"><i class="bx bx-save" style="color: white; font-size: 18px;"></i> Ubah Data</button>
                            <a href="barang-masuk-tambahan.php" class="btn btn-secondary"><i class="bi bi-arrow-left-square-fill" style="color: white; font-size: 18px;"></i> Tutup</a>
                        </div>
                    </form>
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

<!-- Number Format -->
<script>
    $(document).on('input', '#qtyInput', function(e) {
        var qtyInput = $(this).val().replace(/\D/g, '');
        var qtyAwal = qtyInput ? parseInt(qtyInput) : 0;
        $(this).val(qtyAwal.toLocaleString('id-ID').replace(',', '.'));

        console.log(qtyAwal.toLocaleString('id-ID').replace(',', '.'));

        // mendapatkan tombol dengan id "submitButton"
        var submitButton = document.getElementById("submitButton");

        // memeriksa apakah nilai qty sudah diisi atau tidak
        if ($(this).val().trim() !== '' && parseInt($(this).val().replace(/\D/g, '')) > 0) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    });
</script>