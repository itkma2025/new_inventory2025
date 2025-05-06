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
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) {
                                                        echo $_SESSION['info'];
                                                    }
                                                    unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="container-fluid">
                <div class="card shadow p-3">
                    <div class="card-header">
                        <h5 class="text-center text-dark">Edit Data Produk Masuk Lokal</h5>
                    </div>
                    <?php
                    $id = base64_decode($_GET['id']);
                    $sql = mysqli_query($connect, "SELECT
                                                        COALESCE(pr.id_produk_reg, tpsm.id_set_marwa) AS id_produk,
                                                        COALESCE(pr.nama_produk, tpsm.nama_set_marwa) AS nama_produk,
                                                        COALESCE(mr_pr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                                                        COALESCE(pr.harga_produk, tpsm.harga_set_marwa) AS harga,
                                                        SUBSTRING(COALESCE(pr.id_produk_reg, tpsm.id_set_marwa), 1, 2) AS substr_id_produk,
                                                        iibil.qty,
                                                        iibil.id_isi_inv_br_in_lokal,
                                                        iibil.id_inv_br_in_lokal,
                                                        iibil.harga
                                                    FROM inv_br_in_lokal AS ibil
                                                    LEFT JOIN isi_inv_br_in_lokal iibil ON (ibil.id_inv_br_in_lokal = iibil.id_inv_br_in_lokal)
                                                    LEFT JOIN tb_produk_reguler pr ON (iibil.id_produk_reg = pr.id_produk_reg)
                                                    LEFT JOIN tb_produk_set_marwa tpsm ON (iibil.id_produk_reg = tpsm.id_set_marwa)
                                                    LEFT JOIN tb_merk AS mr_pr ON (pr.id_merk = mr_pr.id_merk)
                                                    LEFT JOIN tb_merk AS mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
                                                    WHERE id_isi_inv_br_in_lokal = '$id' AND SUBSTRING(COALESCE(pr.id_produk_reg, tpsm.id_set_marwa), 1, 2) = 'BR'");
                    $data = mysqli_fetch_array($sql);
                    ?>
                    <form method="post" action="proses/proses-br-in-lokal.php" class="form">
                        <div class="row">
                            <input type="hidden" class="form-control" name="id_isi_inv_br_in_lokal" value="<?php echo $data['id_isi_inv_br_in_lokal'] ?>">
                            <input type="hidden" class="form-control" name="id_inv_br_in_lokal" value="<?php echo $data['id_inv_br_in_lokal'] ?>">
                            <div class="col-sm-7 mb-3">
                                <label for="nama_produk">Nama Produk</label>
                                <input type="hidden" class="form-control" name="id_produk" id="idProduk" value="<?php echo $data['id_produk'] ?>">
                                <input type="text" class="form-control" name="nama_produk" id="namaProduk" value="<?php echo $data['nama_produk'] ?>" data-bs-toggle="modal" data-bs-target="#modalBarang" readonly>
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label>Merk</label>
                                <input type="text" class="form-control" id="merkProduk" value="<?php echo $data['nama_merk'] ?>" readonly>
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label>Harga</label>
                                <input type="text" class="form-control" id="qtyInput" value="<?php echo number_format($data['harga']) ?>" required>
                            </div>
                            <div class="col-sm-1 mb-3">
                                <label>Qty</label>
                                <input type="text" class="form-control" name="qty" id="qtyInput" value="<?php echo number_format($data['qty']) ?>">
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" name="edit-isi-br-in-lokal" id="submitButton" class="btn btn-primary" disabled><i class="bx bx-save" style="color: white; font-size: 18px;"></i> Ubah Data</button>
                            <a href="list-br-in-lokal.php?id=<?php echo base64_encode($data['id_inv_br_in_lokal']) ?>" class="btn btn-secondary"><i class="bi bi-arrow-left-square-fill" style="color: white; font-size: 18px;"></i> Tutup</a>
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

<!-- Modal Barang -->
<div class="modal fade" id="modalBarang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Data Barang</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="table2">
                        <thead>
                            <tr class="text-white" style="background-color: #051683;">
                                <td class="text-center p-3" style="width: 50px">No</td>
                                <td class="text-center p-3" style="width: 350px">Nama Produk</td>
                                <td class="text-center p-3" style="width: 100px">Merk</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            date_default_timezone_set('Asia/Jakarta');

                            include "koneksi.php";
                            $no = 1;
                            $sql = "SELECT pr.*,  
                        mr.*
                        FROM tb_produk_reguler as pr
                        LEFT JOIN tb_merk mr ON (pr.id_merk = mr.id_merk)
                        ";
                            $query = mysqli_query($connect, $sql);
                            while ($data = mysqli_fetch_array($query)) {
                            ?>
                                <tr data-idprod="<?php echo $data['id_produk_reg']; ?>" data-namaprod="<?php echo $data['nama_produk']; ?>" data-merkprod="<?php echo $data['nama_merk']; ?>" data-bs-dismiss="modal">
                                    <td class="text-center"><?php echo $no; ?></td>
                                    <td><?php echo $data['nama_produk']; ?></td>
                                    <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                                </tr>
                                <?php $no++; ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Barang -->

<script>
    // Fngsi Untuk membuat form input Qty menjadi enabled
    function enableQtyActual() {
        $('#qtyInput').prop('disabled', false);
    }

    // select Produk Reguler
    $(document).on('click', '#table2 tbody tr', function(e) {
        $('#idProduk').val($(this).data('idprod'));
        $('#namaProduk').val($(this).data('namaprod'));
        $('#merkProduk').val($(this).data('merkprod'));
        $('#modalBarang').modal('hide');

        // Aktifkan input qtyActual
        enableQtyActual();
    });
</script>

<!-- Clock js -->
<script>
    function inputDateTime() {
        // Get current date and time
        let currentDate = new Date();

        // Format date and time as yyyy-mm-ddThh:mm:ss
        let year = currentDate.getFullYear();
        let month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        let day = currentDate.getDate().toString().padStart(2, '0');
        let hours = currentDate.getHours();
        let minutes = currentDate.getMinutes().toString().padStart(2, '0');
        let seconds = currentDate.getSeconds().toString().padStart(2, '0');
        let formattedDateTime = `${day}/${month}/${year}, ${hours}:${minutes}`;

        // Set value of input field to current date and time
        document.getElementById("datetime-input").setAttribute('value', formattedDateTime);

    }
    // Call updateDateTime function every second
    setInterval(inputDateTime, 1000);
</script>

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

<script>
    // Deklarasi fungsi
    function enableSubmitButton() {
        $('#submitButton').prop('disabled', false);
    }

    function disableSubmitButton() {
        $('#submitButton').prop('disabled', true);
    }

    $(document).on('click input', '#table2 tbody tr, #qtyInput', function(e) {
        if (e.target.id !== 'qtyInput') {
            $('#idProduk').val($(this).data('idprod'));
            $('#namaProduk').val($(this).data('namaprod'));
            $('#merkProduk').val($(this).data('merkprod'));
            $('#modalBarang').modal('hide');
        }

        var cekQty = document.getElementById('qtyInput').value;
        var qtyAct = $('#qtyInput').val().replace(/\D/g, '');
        var qtyInput = $(this).val().replace(/\D/g, '');
        var qtyAwal = qtyInput ? parseInt(qtyInput) : 0;
        $(this).val(qtyAwal.toLocaleString('id-ID').replace(',', '.'));

        console.log(qtyAwal.toLocaleString('id-ID').replace(',', '.'));
        console.log(cekQty);
        console.log(qtyAct);
        console.log($('#idProduk').val());

        if ($('#table2').val() !== '' || $('#qtyInput').val() !== '') {
            if ($('#idProduk').val() != idProduk || qtyAwal.toLocaleString('id-ID').replace(',', '.') != qtyAct) {
                // Aktifkan button submit
                enableSubmitButton();
            } else {
                // Non AKtifkan button submit
                disableSubmitButton();
            }
        }
    });
</script>