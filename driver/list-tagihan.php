<?php
require_once "../akses.php";
$page = 'list-tagihan';
include "function/class-list-inv.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link rel="stylesheet" href="../assets/css/wrap-text.css">
    <?php include "page/head.php"; ?>
    <style>
        .text-nowrap-mobile {
            /* Gaya untuk tampilan mobile */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        @media (min-width: 768px) {
            .text-nowrap-mobile {
                /* Gaya untuk tampilan desktop */
                white-space: normal;
                overflow: visible;
                text-overflow: inherit;
                max-width: none;
            }
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
        <div class="pagetitle">
            <h1>List Invoice</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">List Invoice</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section>
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="mt-4">
                            <?php
                            $date = date('d-m-Y');
                            ?>
                             <p><b>Nama Driver : <?php echo ucfirst(decrypt($_SESSION['tiket_nama'], $key_global)); ?></b></p>
                            <p><b>Tanggal : <?php echo $date; ?></b></p>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-bordered" id="table2">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                        <td class="text-center p-3 col-1 text-nowrap">No</td>
                                        <td class="text-center p-3 col-2 text-nowrap">No Tagihan</td>
                                        <td class="text-center p-3 col-2 text-nowrap">Tgl. Tagihan</td>
                                        <td class="text-center p-3 col-2 text-nowrap">Nama Customer</td>
                                        <td class="text-center p-3 col-3 text-nowrap">Alamat</td>
                                        <td class="text-center p-3 col-2 text-nowrap">Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    date_default_timezone_set('Asia/Jakarta');
                                    include 'query/list-tagihan.php';
                                    $query = $connect->query($sql_tagihan . " GROUP BY bill.no_tagihan");
                                    $no = 1; // Definisikan $no di sini
                                    while ($data = mysqli_fetch_array($query)) {
                                        $id_tagihan = $data['id_tagihan'];
                                        $nama_penerima = $data['nama_penerima'];
                                    ?>

                                        <?php
                                        if (empty($nama_penerima)) {
                                        ?>
                                            <tr>
                                                <td class="text-center text-nowrap"><?php echo $no ?></td>
                                                <td class="text-nowrap text-center"><?php echo $data['no_tagihan'] ?></td>
                                                <td class="text-nowrap text-center"><?php echo $data['tgl_tagihan'] ?></td>
                                                <td class="text-nowrap"><?php echo $data['nama_cs'] ?></td>
                                                <td class="text-nowrap-mobille wrap-text"><?php echo $data['alamat'] ?></td>
                                                <td class="text-center text-nowrap">
                                                    <?php
                                                    if (empty($nama_penerima)) {
                                                    ?>
                                                        <button class="btn btn-primary btn-sm" data-id="<?php echo $id_tagihan ?>"
                                                            data-bs-toggle="modal" data-bs-target="#Diterima">
                                                            <i class="bi bi-arrow-repeat"></i> Perlu Diproses
                                                        </button>
                                                    <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php $no++; ?>
                                        <?php
                                        }
                                        ?>
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
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>

</html>
<!-- Modal Diterima-->
<div class="modal fade" id="Diterima" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Status</h1>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal"
                    aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form action="proses/tagihan.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_tagihan" id="id_tagihan">
                        <div class="mb-3">
                            <label id="labelPenerima">Nama Penerima</label>
                            <input type="text" class="form-control" name="nama_penerima" id="penerima"
                                autocomplete="off" required>
                        </div>
                        <div class="mb-3">
                            <label id="labelDate">Tanggal</label>
                            <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl"
                                id="date" required="required">
                        </div>
                        <div class="mb-3">
                            <label id="labelBukti1">Upload Bukti Terima :</label>
                            <br>
                            <input type="file" name="fileku1" id="fileku1" accept="image/*"
                                onchange="compressAndPreviewImage(event)">
                        </div>
                        <div class="mb-3" id="imagePreview"></div>
                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-secondary btn-md"
                                data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary btnnd" name="update-tagihan">Simpan
                                Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- kode JS Dikirim -->
    <?php include "page/upload-img.php";  ?>
    <?php include "page/cek-upload.php"; ?>
    <?php include "page/search-ekspedisi.php"; ?>
    <!-- End JS Dikirim -->
    <style>
        .preview-image {
            max-width: 100%;
            height: auto;
        }
    </style>
</div>
<!-- End Modal Diterima-->
<script>
    flatpickr("#date", {
        dateFormat: "d/m/Y"
    });
</script>
<script>
    $(document).ready(function() {
        // Tangkap nilai data-id dari button saat modal ditampilkan
        $('#Diterima').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Tombol yang memicu modal
            var id_tagihan = button.data('id'); // Ambil nilai data-id dari tombol
            var modal = $(this);

            // Isi nilai data-id ke dalam input "id_tagihan"
            modal.find('#id_tagihan').val(id_tagihan);
        });
    });
</script>