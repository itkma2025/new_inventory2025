<?php
$page  = 'transaksi';
$page2 = 'spk';
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
                <div class="card shadow p-2">
                    <div class="card-header text-center">
                        <h5><strong>FORM EDIT DETAIL</strong></h5>
                    </div>
                    <form action="proses/proses-spk-reg.php" method="POST">
                        <?php
                        $id = base64_decode($_GET['edit_id']);
                        $sql = "SELECT sr.*, cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
                        FROM spk_reg AS sr
                        JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                        JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                        JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                        WHERE sr.id_spk_reg = '$id'";
                        $query = mysqli_query($connect, $sql);
                        $data = mysqli_fetch_array($query);
                        ?>

                        <div class="card-body">
                            <div class="row mt-3">
                                <div class="col-sm-6">
                                    <div class="card-body">
                                        <div class="mt-3">
                                            <label for="no_spk" class="form-label">No. SPK</label>
                                            <input type="hidden" name="id_spk_reg" value="<?php echo $id ?>">
                                            <input type="text" class="form-control bg-light" id="no_spk" name="no_spk" value="<?php echo $data['no_spk'] ?>" readonly>
                                        </div>
                                        <div class="mt-3">
                                            <label for="tgl_spk" class="form-label">Tanggal SPK</label>
                                            <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl_spk" value="<?php echo $data['tgl_spk'] ?>" readonly>
                                        </div>
                                        <div class="mt-3">
                                            <label for="no_po" class="form-label">NO. PO</label>
                                            <input type="text" class="form-control" id="no_po" name="no_po" value="<?php echo $data['no_po'] ?>">
                                        </div>
                                        <div class="mt-3">
                                            <label for="tgl_pesan" class="form-label">Tanggal Pesanan</label>
                                            <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl_pesan" id="date" value="<?php echo $data['tgl_pesanan'] ?>" required>
                                        </div>
                                        <div class="mt-3">
                                            <label for="order_via" class="form-label">Order Via</label>
                                            <select class="selectize-js form-select" name="order_by" required>
                                                <option value="<?php echo $data['id_orderby'] ?>"><?php echo $data['order_by'] ?></option>
                                                <?php
                                                include "koneksi.php";
                                                $sql2 = "SELECT * FROM tb_orderby";
                                                $query2 = mysqli_query($connect, $sql2);
                                                while ($data2 = mysqli_fetch_array($query2)) {
                                                ?>
                                                    <option value="<?php echo $data2['id_orderby'] ?>"><?php echo $data2['order_by'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card-body">
                                        <div class="mt-3">
                                            <label for="sales" class="form-label">Sales</label>
                                            <select class="selectize-js form-select" name="sales" required>
                                                <option value="<?php echo $data['id_sales'] ?>"><?php echo $data['nama_sales'] ?></option>
                                                <?php
                                                include "koneksi.php";
                                                $sql_sales = "SELECT * FROM tb_sales";
                                                $query_sales = mysqli_query($connect, $sql_sales);
                                                while ($data3 = mysqli_fetch_array($query_sales)) {
                                                ?>
                                                    <option value="<?php echo $data3['id_sales'] ?>"><?php echo $data3['nama_sales'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="mt-3">
                                            <label for="Pelanggan" class="form-label">Pelanggan</label>
                                            <input type="hidden" class="form-control" id="id" name="id_cs" value="<?php echo $data['id_customer'] ?>">
                                            <input type="text" class="form-control bg-light" id="cs" name="pelanggan" value="<?php echo $data['nama_cs'] ?>" readonly>
                                        </div>
                                        <div class="mt-3">
                                            <label for="alamat" class="form-label">Alamat</label>
                                            <textarea type="text" class="form-control bg-light" id="alamat" name="alamat" rows="3" readonly><?php echo $data['alamat'] ?></textarea>
                                        </div>
                                        <div class="mt-3">
                                            <label for="note" class="form-label">Note</label>
                                            <textarea type="text" class="form-control" id="note" name="note"><?php echo $data['note'] ?></textarea>
                                        </div>
                                        <input type="hidden" class="bg-white form-control" name="updated" id="datetime-input">
                                        <input type="hidden" name="id_user" value="<?php echo $_SESSION['tiket_id'] ?>">
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <button type="submit" name="edit" class="btn btn-primary btn-md m-2"><i class="bx bx-save"></i> Simpan Perubahan</button>
                                    <a href="detail-produk-spk-reg-dalam-proses.php?id=<?php echo base64_encode($id) ?>" class="btn btn-secondary m-2"><i class="bi bi-x-circle"></i> Batal</a>
                                </div>
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