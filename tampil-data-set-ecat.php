<?php
require_once "akses.php";
$page = 'br-masuk';
$page2 = 'br-masuk-set-ecat';
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
        <section>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <form action="proses/proses-update-stock-set-ecat.php" method="POST">
                                <?php
                                // Koneksi ke database
                                include 'koneksi.php';
                                // Menerima nilai dari permintaan
                                $id = $_POST['id_set'];
                                $ket_in = $_POST['ket_in'];
                                $qty = $_POST['qty'];
                                $qty = intval(preg_replace("/[^0-9]/", "", $qty));
                                // Generate UUID
                                $uuid = generate_uuid();
                                $month = date('m');
                                $year = date('y');
                                $sql_ket_in =  $connect->query("SELECT ket_in FROM keterangan_in WHERE id_ket_in = '$ket_in'");
                                $sql = mysqli_query($connect, "SELECT 
                                                                    tpse.id_set_ecat, 
                                                                    tpse.kode_set_ecat,
                                                                    tpse.nama_set_ecat,
                                                                    mr.nama_merk 
                                                                FROM tb_produk_set_ecat tpse 
                                                                LEFT JOIN tb_merk mr ON (tpse.id_merk = mr.id_merk) 
                                                                WHERE tpse.id_set_ecat = '$id'");
                                $row = mysqli_fetch_array($sql);
                                $row2 = mysqli_fetch_array($sql_ket_in);
                                ?>
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-5">
                                            <label for="">Nama Set</label>
                                            <input type="hidden" name="id_set" class="form-control" value="<?php echo encrypt($id, $key_global) ?>" readonly>
                                            <input type="hidden" name="id_ket_in" class="form-control" value="<?php echo encrypt($ket_in, $key_global) ?>" readonly>
                                            <input type="text" class="form-control" value="<?php echo $row['nama_set_ecat'] ?>" readonly>
                                        </div>
                                        <div class="col-2">
                                            <label for="">Merk</label>
                                            <input type="text" class="form-control" value="<?php echo $row['nama_merk'] ?>" readonly>
                                        </div>
                                        <div class="col-4">
                                            <label for="">Keterangan Stock Masuk</label>
                                            <input type="text" class="form-control" value="<?php echo $row2['ket_in'] ?>" readonly>
                                        </div>
                                        <div class="col-1">
                                            <label for="">Jumlah Set</label>
                                            <input type="text" class="form-control" name="qty_set" value="<?php echo number_format($qty) ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <table class="table">
                                    <thead>
                                        <tr class="bg-primary text-white">
                                            <th class="text-center p-3" style="width: 50px;">No</th>
                                            <th class="text-center p-3" style="width: 150px;">Kode Produk</th>
                                            <th class="text-center p-3" style="width: 300px;">Nama Produk</th>
                                            <th class="text-center p-3" style="width: 100px;">Merk Set</th>
                                            <td class="text-center p-3" style="width: 100px">Stock</td>
                                            <th class="text-center p-3" style="width: 100px;">Qty</th>
                                            <th class="text-center p-3" style="width: 100px;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Menerima nilai dari permintaan
                                        $no = 1;
                                        $id = $_POST['id_set'];
                                        $qty = $_POST['qty'];
                                        $qty = intval(preg_replace("/[^0-9]/", "", $qty));
                                        $month = date('m');
                                        $year = date('dy');

                                        $sql = mysqli_query($connect, " SELECT 
                                                                            ipse.id_isi_set_ecat, 
                                                                            ipse.id_set_ecat, 
                                                                            ipse.id_produk, 
                                                                            ipse.qty,  
                                                                            COALESCE(tpr.kode_produk, tpe.kode_produk) AS kode_produk, 
                                                                            COALESCE(tpr.nama_produk, tpe.nama_produk) AS nama_produk, 
                                                                            COALESCE(tpr.harga_produk, tpe.harga_produk) AS harga_produk,
                                                                            COALESCE(mr.nama_merk, mr_reg.nama_merk) AS nama_merk,
                                                                            COALESCE(spr.stock, spe.stock) AS stock_produk
                                                                        FROM isi_produk_set_ecat ipse 
                                                                        LEFT JOIN tb_produk_ecat tpe ON (ipse.id_produk = tpe.id_produk_ecat)
                                                                        LEFT JOIN tb_produk_set_ecat tpse ON (ipse.id_set_ecat = tpse.id_set_ecat)
                                                                        LEFT JOIN tb_merk mr ON (tpe.id_merk = mr.id_merk)
                                                                        LEFT JOIN stock_produk_ecat spe ON (spe.id_produk_ecat = ipse.id_produk)
                                                                        LEFT JOIN tb_produk_reguler tpr ON (ipse.id_produk = tpr.id_produk_reg)
                                                                        LEFT JOIN tb_merk mr_reg ON (tpr.id_merk = mr_reg.id_merk)
                                                                        LEFT JOIN stock_produk_reguler spr ON (spr.id_produk_reg = ipse.id_produk)
                                                                        WHERE ipse.id_set_ecat = '$id'");
                                        $total_data = mysqli_num_rows($sql);
                                        // Hitung total data yang ditampilkan
                                        while ($data = mysqli_fetch_array($sql)) {
                                            $total = $data['qty'] * $qty;
                                            $tampil_stock = number_format($data['stock_produk']);
                                            // Generate UUID 
                                            $uuid = generate_uuid();
                                        ?>
                                            <tr>
                                                <input type="hidden" name="id_set_isi[]" class="form-control bg-light text-center" value="<?php echo $id ?>" readonly>
                                                <input type="hidden" name="id_produk[]" class="form-control bg-light text-center" value="<?php echo $data['id_produk'] ?>" readonly>
                                                <input type="hidden" name="id_tr_set_isi[]" class="form-control bg-light text-center" value="TR-ISI-SET-ECAT-<?php echo $year ?><?php echo $uuid ?><?php echo $month ?>" readonly>
                                                <td><input type="text" class="form-control bg-light text-center" value="<?php echo $no ?>" readonly></td>
                                                <td><input type="text" class="form-control bg-light" value="<?php echo $data['kode_produk']; ?>" readonly></td>
                                                <td><input type="text" class="form-control bg-light" value="<?php echo $data['nama_produk']; ?>" readonly></td>
                                                <td><input type="text" class="form-control bg-light" value="<?php echo $data['nama_merk']; ?>" readonly></td>
                                                <td><input type="text" name="stock[]" class="form-control bg-light text-end" value="<?php echo $tampil_stock; ?>" readonly></td>
                                                <td><input type="text" class="form-control bg-light text-end" value="<?php echo $data['qty']; ?>" readonly></td>
                                                <td><input type="text" name="qty[]" class="form-control bg-light text-end" value="<?php echo number_format($total); ?>" readonly></td>
                                                </td>
                                            </tr>
                                            <?php $no++; ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <div class="text-start">
                                    <p><strong>NB: barang di atas akan mengurangi barang stok yang ada untuk non set</strong></p>
                                </div>
                                <div class="mb-3 mt-3 text-end">
                                    <input type="hidden" name="id_tr_set" class="form-control bg-light text-center" value="TR-SET-MRW-<?php echo $year ?><?php echo $uuid ?><?php echo $month ?>" readonly>
                                    <input type="hidden" name="nama_user" value="<?php echo $_SESSION['tiket_nama'] ?>">
                                    <input type="hidden" class="form-control" name="created" id="datetime-input">
                                    <?php  
                                        if($total_data != 0){
                                            ?>
                                                <button type="submit" id="simpan" class="btn btn-primary btn-md" name="simpan" disabled><i class="bi bi-save"></i> Simpan Data</button>
                                            <?php
                                        }
                                    ?>
                                    <a href="input-set-in-ecat.php" class="btn btn-secondary btn-md"><i class="bi bi-x"></i> Kembali</a>
                                </div>
                            </form>
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

    <?php include "page/script.php" ?>

    <script>
        // Fungsi untuk memeriksa apakah ada produk dengan stock kosong
        function checkStock() {
            var stockInputs = document.getElementsByName("stock[]");
            var qtyInputs = document.getElementsByName("qty[]");
            var saveButton = document.getElementById("simpan");
            var disableButton = false;

            for (var i = 0; i < stockInputs.length; i++) {
                var stock = parseFloat(stockInputs[i].value.replace(/,/g, ''));
                var qty = parseFloat(qtyInputs[i].value.replace(/,/g, ''));

                if (stock === 0 || stock < qty) {
                    disableButton = true;
                    stockInputs[i].classList.add("bg-danger", "text-white");
                    stockInputs[i].classList.remove("bg-light");
                } else {
                    stockInputs[i].classList.remove("bg-danger", "text-white");
                    stockInputs[i].classList.add("bg-light");
                }
            }
            saveButton.disabled = disableButton;
        }

        // Panggil fungsi checkStock saat halaman selesai dimuat
        window.addEventListener("DOMContentLoaded", function() {
            checkStock();
        });
    </script>


</body>

</html>

<!-- Generate UUID -->
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