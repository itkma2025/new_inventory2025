<?php
require_once "akses.php";
$page = 'br-keluar';
$page2 = 'br-keluar-reg';
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
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) {echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="container-fluid">
                <div class="card shadow p-3">
                    <?php
                        $id = decrypt($_GET['id'], $key_global);
                        $sql = "SELECT  
                                    ibor.id_isi_br_out_reg,
                                    ibor.id_produk_reg,
                                    ibor.qty,
                                    ibor.id_ket_out,
                                    ibor.created_date AS created, 
                                    COALESCE(pr.nama_produk, ecat.nama_produk) AS nama_produk,
                                    COALESCE(spr.stock, spe.stock) AS stock,
                                    mr.nama_merk, 
                                    ket.ket_out
                                FROM isi_br_out_reg AS ibor
                                LEFT JOIN tb_produk_reguler pr ON (ibor.id_produk_reg = pr.id_produk_reg)
                                LEFT JOIN tb_produk_ecat ecat ON (ibor.id_produk_reg = ecat.id_produk_ecat)
                                LEFT JOIN stock_produk_reguler spr ON (spr.id_produk_reg = ibor.id_produk_reg)
                                LEFT JOIN stock_produk_ecat spe ON (spe.id_produk_ecat = ibor.id_produk_reg)  
                                LEFT JOIN tb_merk mr ON (mr.id_merk = pr.id_merk OR mr.id_merk = ecat.id_merk)
                                LEFT JOIN keterangan_out ket ON (ibor.id_ket_out = ket.id_ket_out)
                                WHERE id_isi_br_out_reg = '$id'";
                        $query = mysqli_query($connect, $sql);
                        $data = mysqli_fetch_array($query);
                        $total_stock = $data['stock'] + $data['qty'];
                        $qty = $data['qty'];
                    ?>
                    <form method="post" action="proses/proses-br-out-reg.php" class="form">
                        <div class="row">
                            <input type="hidden" class="form-control" name="id_br" value="<?php echo encrypt($data['id_isi_br_out_reg'], $key_global) ?>">
                            <div class="col-sm-5 mb-3">
                                <label>Nama Produk</label>
                                <input type="hidden" class="form-control" name="id_produk" id="idProduk" value="<?php echo $data['id_produk_reg'] ?>">
                                <input type="text" class="form-control" name="nama_produk" id="namaProduk" value="<?php echo $data['nama_produk'] ?>" readonly>
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label>Merk</label>
                                <input type="text" class="form-control text-center" id="merkProduk" value="<?php echo $data['nama_merk'] ?>" readonly>
                            </div>
                            <div class="col-sm-1 mb-3">
                                <label>Stock</label>
                                <input type="text" class="form-control text-end" id="stock" readonly>
                            </div>
                            <div class="col-sm-1 mb-3">
                                <label>Qty</label>
                                <input type="text" class="form-control text-end" name="qty" id="qtyInput" value="<?php echo number_format($data['qty']) ?>">
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label>Keterangan</label>
                                <select class="form-select" name="keterangan" id="ket">
                                    <option value="<?php echo $data['id_ket_out'] ?>"> <?php echo $data['ket_out'] ?></option>
                                    <?php
                                    $sql = mysqli_query($connect, "SELECT * FROM keterangan_out");
                                    while ($data = mysqli_fetch_array($sql)) {
                                    ?>
                                        <option value="<?php echo $data['id_ket_out'] ?>"> <?php echo $data['ket_out'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" name="edit" id="submitButton" class="btn btn-primary"><i class="bx bx-save" style="color: white; font-size: 18px;" disabled></i> Ubah Data</button>
                            <a href="barang-keluar-reg.php" class="btn btn-secondary"><i class="bi bi-arrow-left-square-fill" style="color: white; font-size: 18px;"></i> Tutup</a>
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
    // $(document).on('input', '#qtyInput', function(e) {
    //     var qtyInput = $(this).val().replace(/\D/g, '');
    //     var qtyAwal = qtyInput ? parseInt(qtyInput) : 0;
    //     $(this).val(qtyAwal.toLocaleString('id-ID').replace(',', '.'));

    //     console.log(qtyAwal.toLocaleString('id-ID').replace(',', '.'));

    //     // mendapatkan tombol dengan id "submitButton"
    //     var submitButton = document.getElementById("submitButton");

    //     // memeriksa apakah nilai qty sudah diisi atau tidak
    //     if ($(this).val().trim() !== '' && parseInt($(this).val().replace(/\D/g, '')) > 0) {
    //         submitButton.disabled = false;
    //     } else {
    //         submitButton.disabled = true;
    //     }
    // });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mendapatkan nilai stock dan qty dari PHP
        var stockValue = <?php echo $total_stock ?>;

        // Menampilkan nilai sebelum diformat di console
        console.log("Nilai sebelum diformat:", stockValue);

        // Memformat nilai numerik dengan koma sebagai pemisah ribuan
        var formattedStockValue = stockValue.toLocaleString();

        // Menampilkan nilai setelah diformat di console
        console.log("Nilai setelah diformat:", formattedStockValue);

        // Menetapkan nilai yang diformat pada elemen input dengan ID "stock"
        document.getElementById("stock").value = formattedStockValue;

        $('#submitButton').prop('disabled', true);
    });
</script>
<script>
    // Fungsi untuk menambahkan separator ribuan pada angka
    function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }


    $('#qtyInput').on('input', function() {
        // Mendapatkan nilai stock dan qty dari PHP
        var qty = <?php echo $qty ?>;

        // Menampilkan nilai sebelum diformat di console
        // console.log("Nilai sebelum diformat:", qty);

        // Memformat nilai numerik dengan koma sebagai pemisah ribuan
        var formattedQty = qty.toLocaleString();

        // Menampilkan nilai setelah diformat di console
        // console.log("Nilai Original setelah diformat:", formattedQty);

        // Dapatkan nilai numerik dari stok dan kuantitas
        var stockValue = parseFloat($('#stock').val().replace(/,/g, '')) || 0;
        var qtyValue = parseFloat($(this).val().replace(/,/g, '')) || 0;

        // Setel nilai default jika qtyValue adalah NaN
        if (isNaN(qtyValue)) {
            qtyValue = 1;
        }

        // Periksa apakah kuantitas lebih besar dari stok
        if (qtyValue > stockValue) {
            qtyValue = stockValue;
        }

        // Format kuantitas dengan tanda koma
        var formattedQtyValue = numberWithCommas(qtyValue);

        // Tampilkan informasi di konsol
        // console.log('Stock Value:', stockValue);
        // console.log('Original Quantity Value:', formattedQtyValue);
        // console.log('Parsed Quantity Value:', qtyValue);
        // console.log('Formatted Quantity:', formattedQtyValue);

        // Perbarui nilai input dengan kuantitas yang diformat
        $(this).val(formattedQtyValue);

        // Aktifkan atau nonaktifkan tombol berdasarkan nilai kuantitas
        if (qtyValue > 0 && qtyValue !== qty) {
            $('#submitButton').prop('disabled', false);
        } else {
            $('#submitButton').prop('disabled', true);
        }
    });
</script>
