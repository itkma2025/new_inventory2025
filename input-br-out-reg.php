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
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="container-fluid">
                <div class="card shadow p-3">
                    <?php
                        require_once "function/uuid.php";
                        $uuid = uuid();
                        $month = date('m');
                        $year = date('y');
                    ?>
                    <form method="post" action="proses/proses-br-out-reg.php" class="form">
                        <div class="row">
                            <input type="hidden" class="form-control" name="id_br" value="BR-OUT-<?php echo $year ?><?php echo $uuid ?><?php echo $month ?>">
                            <div class="col-sm-5 mb-3">
                                <label>Nama Produk</label>
                                <input type="hidden" class="form-control" name="id_produk" id="idProduk">
                                <input type="text" class="form-control" name="nama_produk" id="namaProduk" placeholder="Pilih..." data-bs-toggle="modal" data-bs-target="#modalBarang" readonly>
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label>Merk</label>
                                <input type="text" class="form-control" id="merkProduk" readonly>
                            </div>
                            <div class="col-sm-1 mb-3">
                                <label>Stock</label>
                                <input type="text" class="form-control text-end" id="stockProduk" readonly>
                            </div>
                            <div class="col-sm-1 mb-3">
                                <label>Qty</label>
                                <input type="text" class="form-control text-end" name="qty" id="qtyInput" autocomplete="off" disabled>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label>Keterangan</label>
                                <select class="form-select" name="keterangan" required>
                                    <option value="">Pilih...</option>
                                    <?php
                                    $sql = mysqli_query($connect, "SELECT * FROM keterangan_out");
                                    while ($data = mysqli_fetch_array($sql)) {
                                    ?>
                                        <option value="<?php echo $data['id_ket_out'] ?>"> <?php echo $data['ket_out'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <input type="hidden" class="form-control" name="created" id="datetime-input">
                        </div>
                        <div class="text-end">
                            <button type="submit" name="simpan" id="submitButton" class="btn btn-primary" disabled><i class="bx bx-save" style="color: white; font-size: 18px;"></i> Simpan Data</button>
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

<!-- Modal Barang -->
<div class="modal fade" id="modalBarang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Data Barang</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="produk-reg-tab" data-bs-toggle="pill" data-bs-target="#produk-reg" type="button" role="tab" aria-controls="produk-reg" aria-selected="true">Produk Reguler</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="produk-ecat-tab" data-bs-toggle="pill" data-bs-target="#produk-ecat" type="button" role="tab" aria-controls="produk-ecat" aria-selected="false">Produk E-Cat</button>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="produk-reg" role="tabpanel" aria-labelledby="produk-reg-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="table2">
                                <thead>
                                <tr class="text-white" style="background-color: #051683;">
                                    <td class="text-center text-nowrap p-3">No</td>
                                    <td class="text-center text-nowrap p-3">Kode Produk</td>
                                    <td class="text-center text-nowrap p-3">Nama Produk</td>
                                    <td class="text-center text-nowrap p-3">Merk</td>
                                    <td class="text-center text-nowrap p-3">Grade</td>
                                    <td class="text-center text-nowrap p-3">Stock</td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                date_default_timezone_set('Asia/Jakarta');
                                include "koneksi.php";
                                $no = 1;
                                $sql = "SELECT
                                            tpr.id_grade, 
                                            COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa) AS id_produk,
                                            COALESCE(tpr.kode_produk, tpsm.kode_set_marwa) AS kode_produk,
                                            COALESCE(tpr.nama_produk, tpsm.nama_set_marwa) AS nama_produk,
                                            COALESCE(mr_tpr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                                            spr.id_stock_prod_reg,
                                            spr.stock,
                                            tkp.min_stock, 
                                            tkp.max_stock,
                                            gr.nama_grade
                                        FROM stock_produk_reguler AS spr
                                        LEFT JOIN tb_produk_reguler AS tpr ON (tpr.id_produk_reg = spr.id_produk_reg)
                                        LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spr.id_kat_penjualan)
                                        LEFT JOIN tb_produk_set_marwa AS tpsm ON (tpsm.id_set_marwa = spr.id_produk_reg)
                                        LEFT JOIN tb_merk AS mr_tpr ON (tpr.id_merk = mr_tpr.id_merk)
                                        LEFT JOIN tb_merk AS mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
                                        LEFT JOIN tb_produk_grade gr ON (tpr.id_grade = gr.id_grade)
                                        WHERE SUBSTRING(COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa), 1, 2) = 'BR' AND spr.stock > 0
                                        ORDER BY nama_produk ASC ";
                                $query = mysqli_query($connect, $sql);
                                while ($data = mysqli_fetch_array($query)) {
                                    $stock = $data['stock'];
                                    $min_stock = $data['min_stock'];
                                ?>
                                    <tr data-idprod="<?php echo $data['id_produk']; ?>" data-namaprod="<?php echo $data['nama_produk']; ?>" data-merkprod="<?php echo $data['nama_merk']; ?>" data-stockprod="<?php echo $data['stock']; ?>" data-bs-dismiss="modal">
                                    <td class="text-center"><?php echo $no; ?></td>
                                    <td class="text-center"><?php echo $data['kode_produk']; ?></td>
                                    <td class="text-start"><?php echo $data['nama_produk']; ?></td>
                                    <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                                    <td class="text-center"><?php echo $data['nama_grade']; ?></td>
                                    <?php
                                    if ($stock < $min_stock) {
                                        echo "<td class='text-end text-white bg-danger'>" . $data['stock'] . "</td>";
                                    } else {
                                        echo "<td class='text-end' style='background-color: #7CFC00'>" . number_format($data['stock'], 0, '.', '.') . "</td>";
                                    }
                                    ?>
                                    </tr>
                                    <?php $no++; ?>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="produk-ecat" role="tabpanel" aria-labelledby="produk-ecat-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="table3">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                    <td class="text-center text-nowrap p-3">No</td>
                                    <td class="text-center text-nowrap p-3">Kode Produk</td>
                                    <td class="text-center text-nowrap p-3">Nama Produk</td>
                                    <td class="text-center text-nowrap p-3">Merk</td>
                                    <td class="text-center text-nowrap p-3">Grade</td>
                                    <td class="text-center text-nowrap p-3">Stock</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    date_default_timezone_set('Asia/Jakarta');

                                    include "koneksi.php";
                                    $no = 1;
                                    $sql = "SELECT
                                                tpr.id_grade, 
                                                COALESCE(tpr.id_produk_ecat, tpsm.id_set_ecat) AS id_produk,
                                                COALESCE(tpr.kode_produk, tpsm.kode_set_ecat) AS kode_produk,
                                                COALESCE(tpr.nama_produk, tpsm.nama_set_ecat) AS nama_produk,
                                                COALESCE(mr_tpr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                                                spr.id_stock_prod_ecat,
                                                spr.stock,
                                                tkp.min_stock, 
                                                tkp.max_stock,
                                                SUBSTRING(COALESCE(tpr.id_produk_ecat, tpsm.id_set_ecat), 1, 2) AS substr_id_produk,
                                                gr.nama_grade
                                            FROM stock_produk_ecat AS spr
                                            LEFT JOIN tb_produk_ecat AS tpr ON (tpr.id_produk_ecat = spr.id_produk_ecat)
                                            LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spr.id_kat_penjualan)
                                            LEFT JOIN tb_produk_set_ecat AS tpsm ON (tpsm.id_set_ecat = spr.id_produk_ecat)
                                            LEFT JOIN tb_merk AS mr_tpr ON (tpr.id_merk = mr_tpr.id_merk)
                                            LEFT JOIN tb_merk AS mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
                                            LEFT JOIN tb_produk_grade gr ON (tpr.id_grade = gr.id_grade)
                                            WHERE SUBSTRING(COALESCE(tpr.id_produk_ecat, tpsm.id_set_ecat), 1, 2) = 'BR' AND spr.stock > 0
                                            ORDER BY nama_produk ASC ";
                                    $query = mysqli_query($connect, $sql);
                                    while ($data = mysqli_fetch_array($query)) {
                                    $stock = $data['stock'];
                                    $min_stock = $data['min_stock'];
                                    ?>
                                    <tr data-idprod="<?php echo $data['id_produk']; ?>" data-namaprod="<?php echo $data['nama_produk']; ?>" data-merkprod="<?php echo $data['nama_merk']; ?>" data-stockprod="<?php echo $data['stock']; ?>" data-bs-dismiss="modal">
                                        <td class="text-center"><?php echo $no; ?></td>
                                        <td class="text-center"><?php echo $data['kode_produk']; ?></td>
                                        <td class="text-start"><?php echo $data['nama_produk']; ?></td>
                                        <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                                        <td class="text-center"><?php echo $data['nama_grade']; ?></td>
                                        <?php
                                        if ($stock < $min_stock) {
                                        echo "<td class='text-end text-white bg-danger'>" . $data['stock'] . "</td>";
                                        } else {
                                        echo "<td class='text-end' style='background-color: #7CFC00'>" . number_format($data['stock'], 0, '.', '.') . "</td>";
                                        }
                                        ?>
                                    </tr>
                                    <?php $no++; ?>
                                    <?php } ?>
                                </tbody>
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
<!-- End Modal Barang -->

<script>
    // Fngsi Untuk membuat form input Qty menjadi enabled
    function enableQtyActual() {
        $('#qtyInput').prop('disabled', false);
    }

    function formatNumber(number) {
        return number.toLocaleString('en-US');  // Menetapkan format angka menggunakan 'en-US'
    }

    // select Produk Reguler
    $(document).on('click', '#table2 tbody tr', function(e) {
        var stockValue = $(this).data('stockprod');
        var formattedStock = formatNumber(stockValue);

        console.log('Stock Value:', stockValue);
        console.log('Formatted Stock:', formattedStock);

        $('#idProduk').val($(this).data('idprod'));
        $('#namaProduk').val($(this).data('namaprod'));
        $('#merkProduk').val($(this).data('merkprod'));
        $('#stockProduk').val(formattedStock);
        $('#modalBarang').modal('hide');

        // Activate input qtyActual
        enableQtyActual();
    });

   
    // select Produk Reguler
    $(document).on('click', '#table3 tbody tr', function(e) {
        var stockValue = $(this).data('stockprod');
        var formattedStock = formatNumber(stockValue);

        console.log('Stock Value:', stockValue);
        console.log('Formatted Stock:', formattedStock);

        $('#idProduk').val($(this).data('idprod'));
        $('#namaProduk').val($(this).data('namaprod'));
        $('#merkProduk').val($(this).data('merkprod'));
        $('#stockProduk').val(formattedStock);
        $('#modalBarang').modal('hide');

        // Activate input qtyActual
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
<!-- Add the following script to your HTML file -->
<script>
    // Fungsi untuk menambahkan separator ribuan pada angka
    function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }


    $('#qtyInput').on('input', function() {
        // Dapatkan nilai numerik dari stok dan kuantitas
        var stockValue = parseFloat($('#stockProduk').val().replace(/,/g, '')) || 0;
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
        var formattedQty = numberWithCommas(qtyValue);

        // Tampilkan informasi di konsol
        // console.log('Stock Value:', stockValue);
        // console.log('Original Quantity Value:', $(this).val());
        // console.log('Parsed Quantity Value:', qtyValue);
        // console.log('Formatted Quantity:', formattedQty);

        // Perbarui nilai input dengan kuantitas yang diformat
        $(this).val(formattedQty);

        // Aktifkan atau nonaktifkan tombol berdasarkan nilai kuantitas
        if (qtyValue > 0) {
            $('#submitButton').prop('disabled', false);
        } else {
            $('#submitButton').prop('disabled', true);
        }
    });
</script>