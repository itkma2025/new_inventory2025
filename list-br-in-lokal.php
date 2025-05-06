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

        .modal-footer{
            text-align: center !important;
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
                <?php  
                    include "koneksi.php";
                    $id = base64_decode($_GET['id']);
                    $no = 1;
                    $sql = "SELECT
                                COALESCE(pr.id_produk_reg, tpsm.id_set_marwa) AS id_produk,
                                COALESCE(pr.nama_produk, tpsm.nama_set_marwa) AS nama_produk,
                                COALESCE(mr_pr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                                SUBSTRING(COALESCE(pr.id_produk_reg, tpsm.id_set_marwa), 1, 2) AS substr_id_produk,
                                pr.satuan,
                                iibil.qty,
                                iibil.harga,
                                iibil.id_isi_inv_br_in_lokal,
                                iibil.id_inv_br_in_lokal,
                                ibil.id_sp,
                                ibil.ongkir  
                            FROM inv_br_in_lokal AS ibil
                            LEFT JOIN isi_inv_br_in_lokal iibil ON (ibil.id_inv_br_in_lokal = iibil.id_inv_br_in_lokal)
                            LEFT JOIN tb_produk_reguler pr ON (iibil.id_produk_reg = pr.id_produk_reg)
                            LEFT JOIN tb_produk_set_marwa tpsm ON (iibil.id_produk_reg = tpsm.id_set_marwa)
                            LEFT JOIN tb_merk mr_pr ON (pr.id_merk = mr_pr.id_merk)
                            LEFT JOIN tb_merk mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
                            WHERE iibil.id_inv_br_in_lokal = '$id' AND SUBSTRING(COALESCE(pr.id_produk_reg, tpsm.id_set_marwa), 1, 2) = 'BR'";
                    $query = mysqli_query($connect, $sql);

                    $sql_ongkir = mysqli_query($connect, " SELECT 
                                                            ibil.id_sp, ibil.id_inv_br_in_lokal, ibil.no_inv, ibil.ongkir, ibil.tgl_inv, ibil.bukti_pembelian, sp.nama_sp 
                                                        FROM inv_br_in_lokal AS ibil
                                                        LEFT JOIN tb_supplier sp ON (sp.id_sp = ibil.id_sp)
                                                        WHERE id_inv_br_in_lokal = '$id'");
                    $data_ongkir = mysqli_fetch_array($sql_ongkir);
                    $no_inv = $data_ongkir['no_inv'];
                    $ongkir = $data_ongkir['ongkir'];
                    $nama_sp = $data_ongkir['nama_sp'];
                    $tgl_inv = $data_ongkir['tgl_inv'];
                    $bukti_pembelian = $data_ongkir['bukti_pembelian'];
                ?>
                <div class="card">
                    <div class="card-body mt-3">
                        <div class="text-start">
                            <?php $id_inv = $_GET['id']; ?>
                            <a href="barang-masuk-lokal.php" class="btn btn-secondary"><i class="bi bi-arrow-left-square-fill" style="color: white; font-size: 18px;"></i> Kembali</a>
                            <a href="input-isi-inv-br-in-lokal.php?id=<?php echo $id_inv ?>" class="btn btn-primary"><i class="bi bi-plus-circle" style="color: white; font-size: 18px;"></i> Tambah Data Produk</a>
                            <?php  
                                if($bukti_pembelian != ''){
                                    ?>
                                        <button class="btn btn-info p-2" data-bs-toggle="modal" data-bs-target="#bukti"><i class="bi bi-image"></i> Bukti Pembelian</button>
                                    <?php
                                } else {
                                    ?>
                                    <button class="btn btn-warning p-2" data-bs-toggle="modal" data-bs-target="#upload"><i class="bi bi-upload"></i> Upload Invoice</button>
                                    <?php
                                }
                            ?>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-striped" id="table1">
                                <thead>
                                    <tr class="text-white" style="background-color: navy;">
                                        <td class="text-center text-nowrap p-3 col-1">No</td>
                                        <td class="text-center text-nowrap p-3 col-4">Nama Barang</td>
                                        <td class="text-center text-nowrap p-3 col-2">Merk</td>
                                        <td class="text-center text-nowrap p-3 col-1">Satuan</td>
                                        <td class="text-center text-nowrap p-3 col-1">Harga</td>
                                        <td class="text-center text-nowrap p-3 col-1">Qty</td>
                                        <td class="text-center text-nowrap p-3 col-1">Total Harga</td>
                                        <td class="text-center text-nowrap p-3 col-3">Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sub_total= 0;
                                    while ($data = mysqli_fetch_array($query)) {
                                        $id_produk = $data['id_produk'];
                                        $satuan = $data['satuan'];
                                        $id_produk_substr = substr($id_produk, 0, 2);
                                            if ($id_produk_substr == 'BR') {
                                                $satuan_produk = $satuan;
                                            } else {
                                                $satuan_produk = 'Set';
                                            }
                                        $total = $data['qty'] * $data['harga'];
                                        $sub_total += $total;  
                                    ?>
                                       
                                        <tr>
                                            <td class="text-center"><?php echo $no; ?></td>
                                            <td class="text-nowrap"><?php echo $data['nama_produk']; ?></td>
                                            <td class="text-nowrap text-center"><?php echo $data['nama_merk']; ?></td>
                                            <td class="text-nowrap text-center"><?php echo $satuan_produk ?></td>
                                            <td class="text-end"><?php echo number_format($data['harga'], 0, '.', '.'); ?></td>
                                            <td class="text-end"><?php echo number_format($data['qty'], 0, '.', '.'); ?></td>
                                            <td class="text-end"><?php echo number_format($total, 0, '.', '.'); ?></td>
                                            <td class="text-center">
                                                <a href="edit-isi-br-in-lokal.php?id=<?php echo base64_encode($data['id_isi_inv_br_in_lokal']) ?>" class="btn btn-warning btn-sm rounded"><i class="bi bi-pencil" style="font-size: 14px;"></i></a>
                                                <a href="proses/proses-br-in-lokal.php?hapus_isi=<?php echo base64_encode($data['id_isi_inv_br_in_lokal']) ?> && id_inv=<?php echo base64_encode($data['id_inv_br_in_lokal']) ?> " class="btn btn-danger delete-data btn-sm rounded"><i class="bi bi-trash" style="font-size: 14px;"></i></a>
                                            </td>
                                        </tr>
                                        <?php $no++ ?>
                                    <?php } ?>
                                    <?php 
                                        $grand_total = $sub_total + $ongkir;
                                        $update_inv = mysqli_query($connect, "UPDATE inv_br_in_lokal SET total_pembelian = '$grand_total' WHERE id_inv_br_in_lokal = '$id'");
                                    ?>
                                </tbody>
                            </table>
                        </div>
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
</body>

</html>
<!-- Modal Upload-->
<div class="modal fade" id="upload" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Upload Invoice</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="proses/proses-br-in-lokal.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label><b>Upload Invoice :</b></label>
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                        <input type="hidden" name="tgl_inv" value="<?php echo $tgl_inv ?>">
                        <input type="hidden" name="nama_sp" value="<?php echo $nama_sp ?>">
                        <input type="file" class="form-control" name="fileku1" accept=".jpg, .jpeg, .png" onchange="compressAndPreviewImage(event)" required>
                    </div>
                    <div class="mb-3" id="imagePreview" ></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" name="upload"><i class="bi bi-upload"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
    <!-- kode JS Dikirim -->
    <?php include "page/upload-img.php";  ?>
    <?php include "page/cek-upload.php"; ?>
    <style>
        .preview-image {
            max-width: 100%;
            height: auto;
        }
    </style>
</div> 

<!-- Modal Bukti-->
<div class="modal fade" id="bukti" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Bukti Pembelian</h1>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <?php
                        $gambar_bukti = "gambar/pembelian/$nama_sp/$bukti_pembelian";
                    ?>
                    <div class="mb-3">
                        <h6>Nama Supplier : <?php echo $nama_sp ?></h6>
                        <h6>No. Invoice : <?php echo $no_inv ?></h6>
                        <h6>Tgl. Pembelian : <?php echo $tgl_inv ?></h6>
                    </div>
                    <input type="hidden" name="id" value="<?php echo $id ?>">
                    <input type="hidden" name="bukti" value="<?php echo $bukti_pembelian ?>">
                    <div id="carouselExample" class="carousel slide">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="<?php echo $gambar_bukti ?>" class="d-block w-100">
                                <div class="modal-footer d-flex justify-content-center">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <button type="button" class="btn btn-danger delete-data" name="hapus-bukti" data-bs-toggle="modal" data-bs-target="#hapus"><i class="bi bi-trash"></i> Hapus</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- kode JS Dikirim -->
    <?php include "page/upload-img.php";  ?>
    <?php include "page/cek-upload.php"; ?>
    <style>
        .preview-image {
            max-width: 100%;
            height: auto;
        }
    </style>
</div> 

<div class="modal fade" id="hapus" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body mt-3">
                <form action="proses/proses-br-in-lokal.php" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                        <input type="hidden" name="bukti_pembelian" value="<?php echo $bukti_pembelian ?>">
                        <input type="hidden" name="nama_sp" value="<?php echo $nama_sp ?>">
                            <p><b>Apakah anda yakin ingin hapus bukti pembelian?</b></p>
                            <div class="modal-footer d-flex justify-content-center">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> Batal</button>
                                <button type="submit" class="btn btn-danger" name="hapus-bukti"></i> Ya, Hapus Bukti</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Fungsi untuk mereset input file
        function resetInputFile() {
            var inputFile = $("input[name='fileku']");
            inputFile.val('');  // Reset nilai input file
        }

        // Menangani klik pada tombol "Tutup"
        $(".btn-secondary").on('click', function() {
            resetInputFile();
        });

        // Jika modal dihilangkan (dismissed) juga reset input file
        $('#upload').on('hidden.bs.modal', function (e) {
            resetInputFile();
        });
    });
</script>