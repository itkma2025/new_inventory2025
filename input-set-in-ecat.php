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
                    <div class="card-header text-center">
                       <b style="color: black;">Tambah Stock Barang Set</b>
                    </div>
                    <div class="card-body p-3">
                        <form action="tampil-data-set-ecat.php" method="POST">
                            <?php  
                                // Memasukkan file koneksi
                                require_once 'koneksi.php';
                                // Memasukkan file kelas KeteranganIn
                                require_once 'class-php/ket-in.php';
                                // Membuat instance dari KeteranganIn dengan menyediakan objek koneksi
                                $keteranganIn = new KeteranganIn($connect);
                                // Mengambil data menggunakan metode read
                                $result = $keteranganIn->read();
                            ?>
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label>Nama Set</label>
                                        <input type="hidden" class="form-control" name="id_set" id="idSet" required>
                                        <input type="text" class="form-control bg-light" name="nama_set" id="namaSet" data-bs-toggle="modal" data-bs-target="#modalSet" readonly required>
                                    </div>
                                    <div class="col-2 mb-3">
                                        <label>Merk</label>
                                        <input type="text" class="form-control bg-light" id="merkSet" readonly required>
                                    </div>
                                    <div class="col-3 mb-3">
                                        <label>Keterangan</label>
                                        <select name="ket_in" class="form-select">
                                            <option value="">Pilih...</option>
                                            <?php  
                                                while($data = mysqli_fetch_array($result)){
                                                    ?>
                                                        <option value="<?php echo $data['id_ket_in'] ?>"><?php echo $data['ket_in'] ?></option>
                                                    <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-1 mb-3">
                                        <label>Qty</label>
                                        <input type="text" class="form-control" name="qty" id="qtyInput" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 text-end">
                                <button type="submit" class="btn btn-primary btn-md" name="simpan" id="simpan" disabled><i class="bi bi-save"></i> Buat Data Stock</button>
                                <a href="barang-masuk-set-ecat.php?date_range=year" class="btn btn-secondary btn-md"><i class="bi bi-x"></i> Tutup</a>
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

    <!-- Modal Produk Set -->
    <div class="modal fade" id="modalSet" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Pilih Data Barang Set</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table table-responsive">
                        <table class="table table-bordered table-striped" id="table2">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th class="text-center" style="width: 50px">No</th>
                                    <th class="text-center" style="width: 100px">Kode Barang</th>
                                    <th class="text-center" style="width: 300px">Nama Set</th>
                                    <th class="text-center" style="width: 80px">Merk</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                include "koneksi.php";
                                $sql = mysqli_query($connect, " SELECT tpsm.*, mr.nama_merk FROM tb_produk_set_ecat AS tpsm
                                                                LEFT JOIN tb_merk mr ON tpsm.id_merk = mr.id_merk
                                                                WHERE register_value = 1");
                                while ($data = mysqli_fetch_array($sql)) {
                                ?>
                                    <tr data-id="<?php echo htmlspecialchars($data['id_set_ecat']); ?>" data-nama="<?php echo htmlspecialchars($data['nama_set_ecat']); ?>" data-merk="<?php echo htmlspecialchars($data['nama_merk']); ?>">
                                        <td class="text-center"><?php echo $no; ?></td>
                                        <td><?php echo $data['kode_set_ecat']; ?></td>
                                        <td><?php echo $data['nama_set_ecat']; ?></td>
                                        <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                                    </tr>
                                    <?php $no++ ?>
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
    <!-- End Modal -->

    <!-- Select Data -->
    <script>
        // Fngsi Untuk membuat form input Qty menjadi enabled
        // select Produk Reguler
        $(document).on('click', '#table2 tbody tr', function(e) {
        // Mengatur nilai input dengan data yang dipilih
        $('#idSet').val($(this).data('id'));
        $('#namaSet').val($(this).data('nama'));
        $('#merkSet').val($(this).data('merk'));
        $('#modalSet').modal('hide');

        // Mengaktifkan tombol
        $('#simpan').prop('disabled', false);
});

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
</body>

</html>