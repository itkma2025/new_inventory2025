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
                            $id_inv = htmlspecialchars($_GET['id']);
                            $id_inv_decrypt = decrypt($id_inv, $key);
                            $location = isset($_SESSION['display_name']) ? $_SESSION['display_name'] : '';
                            $location_encrypt = encrypt($location, $key);
                            // $location_parts = array_filter([$location]);
                            // $location = implode(", ", $location_parts);
                            $id_bukti_terima = "BKTI" . $month . $year . uuid() . $day;
                            $id_inv_penerima = "PNMR" . $month . $year . uuid() . $day;

                            require_once "query/detail-inv-ecat.php";
                            $data = $spk_trim->fetch_assoc();
                            $href = "";
                        
                        ?>
                        <form action="proses/invoice-reupload-ecat.php" method="post" enctype="multipart/form-data" id="form">
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
                            </div>
                            <?php  
                                require_once "../koneksi-ecat.php";
                                $sql_status_kirim = $connect_ecat->query("SELECT DISTINCT
                                                                        sk.jenis_penerima,
                                                                        eks.nama_ekspedisi,
                                                                        sk.no_resi,
                                                                        sk.jenis_ongkir,
                                                                        COALESCE(ecat.ongkir) AS ongkir,
                                                                        ip.nama_penerima
                                                                    FROM status_kirim AS sk
                                                                    LEFT JOIN ekspedisi eks ON sk.id_ekspedisi = eks.id_ekspedisi
                                                                    LEFT JOIN inv_ecat ecat ON sk.id_inv_ecat = ecat.id_inv_ecat
                                                                    LEFT JOIN inv_penerima ip ON sk.id_inv_ecat = ip.id_inv_ecat
                                                                    WHERE sk.id_inv_ecat = '$id_inv_decrypt'
                                                                    ");
                                $data_status_kirim = $sql_status_kirim->fetch_assoc();
                                $jenis_penerima =  $data_status_kirim['jenis_penerima'];
                              
                                if($jenis_penerima == 'Customer'){
                                   ?>
                                        <div class="mb-3">
                                            <label><b>Jenis Penerima</b></label>
                                            <input type="text" class="form-control bg-light" value="<?php echo $data_status_kirim['jenis_penerima']; ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label><b>Nama Penerima</b></label>
                                            <input type="text" class="form-control bg-light" value="<?php echo $data_status_kirim['nama_penerima']; ?>" readonly>
                                        </div>
                                   <?php
                                } else {
                                    ?>
                                        <div class="mb-3">
                                            <label><b>Jenis Penerima</b></label>
                                            <input type="text" class="form-control bg-light" value="<?php echo $data_status_kirim['jenis_penerima']; ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label><b>Nama Ekspedisi</b></label>
                                            <input type="text" class="form-control bg-light" value="<?php echo $data_status_kirim['nama_ekspedisi']; ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label><b>No Resi</b></label>
                                            <input type="text" class="form-control bg-light" value="<?php echo $data_status_kirim['no_resi']; ?>" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label><b>Ongkir</b></label>
                                            <input type="text" class="form-control bg-light" value="<?php echo number_format($data_status_kirim['ongkir'],0,'.','.'); ?>" readonly>
                                        </div>
                                    <?php
                                }
                            ?>
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


