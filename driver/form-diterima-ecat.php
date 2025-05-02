<?php
$page = 'list-inv';
require_once "../akses.php";
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
    <style>
        #captureModalButton {
            display: none; /* Sembunyikan tombol capture secara default */
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            z-index: 9999;
            text-align: center;
            padding-top: 200px;
            font-size: 24px;
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
        <section class="section">
            <div class="container">
                <div class="card">
                    <div class="card-body p-3">
                        <?php
                            require_once "../function/function-enkripsi.php";
                            require_once "../function/uuid.php";
                            $day = date('d');
                            $month = date('m');
                            $year = date('Y');
                            $key = "Driver2024?";
                            $id_inv = $_GET['id'];
                            $id_inv_decrypt = decrypt($id_inv, $key);
                            $location = isset($_SESSION['display_name']) ? $_SESSION['display_name'] : '';
                            $location_encrypt = encrypt($location, $key);
                            $id_bukti_terima = "BKTI" . $month . $year . uuid() . $day;
                            $id_inv_penerima = "PNMR" . $month . $year . uuid() . $day;
                            require_once "query/detail-inv-ecat.php";
                            $data = $spk_trim->fetch_assoc();
                        ?>
                        <form action="proses/invoice-ecat-diterima.php" method="post" enctype="multipart/form-data" id="form">
                            <input type="hidden" class="form-control" name="location" value="<?php echo $location_encrypt; ?>">
                            <input type="hidden" class="form-control" name="id_bukti_terima" value="<?php echo $id_bukti_terima; ?>">
                            <input type="hidden" class="form-control" name="id_inv_penerima" value="<?php echo $id_inv_penerima; ?>">
                            <input type="hidden" class="form-control" name="id_inv" value="<?php echo $id_inv; ?>">
                            <input type="hidden" class="form-control" name="alamat" value="<?php echo $data['alamat']; ?>">
                            <div class="mb-3">
                                <div class="mb-3">
                                    <label><b>No. Invoice</b></label>
                                    <input type="text" class="form-control bg-light" value="<?php echo $data['no_inv_ecat']; ?>" readonly>
                                </div>
                                <label id="labelJenisPenerima"><b>Diterima Oleh</b></label>
                                <select name="diterima_oleh" id="jenis-penerima" class="form-select" required>
                                    <option value="">Pilih...</option>
                                    <option value="Customer">Customer</option>
                                    <option value="Ekspedisi">Ekspedisi</option>
                                </select>
                            </div>
                            <div id="formCustomer" style="display: none;">
                                <div class="mb-3">
                                    <label id="labelPenerima"><b>Nama Penerima</b></label>
                                    <input type="text" class="form-control" name="nama_penerima" id="penerima" autocomplete="off" required>
                                </div>
                            </div>
                            <div id="formExpedisi" style="display: none;">
                                <div class="mb-3">
                                    <label id="labelPenerima"><b>Pilih Ekspedisi</b></label>
                                    <select name="id_ekspedisi" class="form-select selectize-js" id="selectEkspedisi">
                                        <option value="">Pilih Ekspedisi...</option>
                                        <?php 
                                            $sql_ekspedisi = $connect->query("SELECT id_ekspedisi, nama_ekspedisi, kategori FROM ekspedisi WHERE kategori = '0'");
                                            while($data_ekspedisi = mysqli_fetch_array($sql_ekspedisi)){      
                                        ?>
                                            <option value="<?php echo $data_ekspedisi['id_ekspedisi'] ?>"><?php echo $data_ekspedisi['nama_ekspedisi'] ?></option>
                                        <?php } ?>

                                    </select>
                                    <div id="errorMessageEx" class="text-danger" style="display:none;">Expedisi Wajib Diisi!</div>
                                </div>
                                <div class="mb-3">
                                    <label id="labelResi"><b>No. Resi</b></label>
                                    <input type="text" class="form-control" name="resi" id="resi" autocomplete="off" required>
                                </div>
                                <div class="mb-3">
                                    <label id="labelJenisOngkir"><b>Jenis Ongkir</b></label>
                                    <select id="jenis_ongkir" name="jenis_ongkir" class="form-select" required>
                                        <option value="">Pilih Jenis Ongkir...</option>
                                        <option value="0">Non COD</option>
                                        <option value="1">COD</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label id="labelOngkir"><b>Ongkir</b></label>
                                    <input type="text" class="form-control" name="ongkir" id="ongkir" required oninput="formatNumber(this)">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label><b>Tanggal Terima</b></label>
                                <input type="date" class="form-control" name="tgl" id="date" required>
                            </div>
                            <div class="mb-3">
                                <div class="card-body card-custom">
                                    <img id="photo" class="card-img-top card-img-preview mb-3" style="display:none;" alt="Image Preview">
                                </div>
                                <input type="hidden" name="image" id="image">
                                <button id="upload-bukti" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#captureModal"><i class="bi bi-camera me-1"></i> Ambil Gambar</button>
                                <div id="errorMessage" class="text-danger" style="display:none;">Gambar harus diambil terlebih dahulu!</div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary me-2" name="diterima" id="diterima"><i class="bi bi-arrow-left-right"></i> Ubah Status</button>
                                <a href="detail-invoice.php?id=<?php echo $id_inv ?>&&jenis_inv=<?php echo $jenis_inv ?>" class="btn btn-secondary me-2"><i class="bi bi-x-circle"></i> Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section> 
    </main><!-- End #main -->
    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <?php include "page/script.php" ?>
    <?php include "page/kondisi-diterima.php"; ?>
    <!-- End Footer -->
    <script src="assets/js/camera.js"></script>
    <script>
        document.getElementById('form').addEventListener('submit', function(e) {
            // Tampilkan overlay loading
            document.getElementById('loading-overlay').style.display = 'block';
        });
    </script>
</body>
</html>
<!-- Modal Camera -->
<div class="modal fade" id="captureModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="captureModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="captureModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <video id="videoModal" autoplay class="w-100"></video>
      </div>
      <div class="modal-footer text-center">
        <button id="captureModalButton" type="button" class="btn btn-secondary mt-3 mx-auto"><i class="bi bi-camera2 fs-3"></i></button>
      </div>
    </div>
  </div>
</div>

<div id="loading-overlay" class="loading-overlay">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p>Loading, please wait...</p>
</div>


