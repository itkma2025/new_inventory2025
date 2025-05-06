<?php
    require_once "akses.php";
    $submit = "";
    if ($_GET['jenis'] == 'nonppn') {
        $proses_diterima_expedisi = "proses/proses-invoice-nonppn-diterima.php";
        $proses_diambil_oleh = "proses/proses-diambil-oleh-nonppn.php";
    } else if ($_GET['jenis'] == 'ppn') {
        $proses_diterima_expedisi = "proses/proses-invoice-ppn-diterima.php";
        $proses_diambil_oleh = "proses/proses-diambil-oleh-ppn.php";
    } else if ($_GET['jenis'] == 'bum') {
        $proses_diterima_expedisi = "proses/proses-invoice-bum-diterima.php";
        $proses_diambil_oleh = "proses/proses-diambil-oleh-bum.php";
    } else {
        ?>
            <script type="text/javascript">
                window.location.href = "../404.php";
            </script>
        <?php
    }
?>
<!-- Modal dikirim Ekspedisi -->
<div class="modal fade" id="DiterimaEx" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Ubah Status</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <?php  
                    if($no_resi == ''){
                        echo "Silahkan isi no resi terlebih dahulu agar dapat melakukan proses diterima";
                    } else {
                        ?>
                            <div class="card-body">
                                <form action="<?php echo $proses_diterima_expedisi; ?>" method="POST"
                                    enctype="multipart/form-data">
                                    <input type="hidden" name="nonce_token" value="<?php echo $_SESSION['nonce_token']; ?>">
                                    <input type="hidden" name="id_inv" value="<?php echo encrypt($id_inv, $key_global); ?>">
                                    <input type="hidden" name="alamat" value="<?php echo $data_cek['alamat']; ?>">
                                    <div class="mb-3">
                                        <label>Nama Penerima</label>
                                        <input type="text" class="form-control" name="nama_penerima" autocomplete="off" required>
                                    </div>
                                    <div class="mb-3">
                                        <label id="labelDate">Tanggal</label>
                                        <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl"
                                            id="date" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary" name="diterima_ekspedisi"><i class="bi bi-arrow-left-right"></i> Ubah Status</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelEkspedisi"><i class="bi bi-x-circle"></i> Cancel</button>
                                    </div>
                                </form>
                            </div>
                        <?php
                    }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Diambil Oleh -->
<div class="modal fade" id="diambil" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Diambil Oleh</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php 
                $sql_jenis_reject = $connect->query("SELECT jenis_reject FROM inv_bukti_terima WHERE id_inv = '$id_inv'");
                $data_jenis_reject = $sql_jenis_reject->fetch_assoc();
                $data_jenis_reject['jenis_reject'];
                $status_inv; 
                $bg = "";
                $display = "";

                if($status_inv == 'New'){
                    $bg = "";
                    $display = "required";
                } else if ($status_inv == 'Reject') {
                    if($data_jenis_reject['jenis_reject'] == '2'){
                        // Bisa edit
                        $bg = '';
                        $display = "required";
                    } else {
                        // Hanya lihat
                        $bg = 'bg-light';
                        $display = "readonly";
                    }
                } 
               
            ?>
            <form action="<?php echo $proses_diambil_oleh ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="nonce_token" value="<?php echo $_SESSION['nonce_token']; ?>">
                    <input type="hidden" name="id_inv" value="<?php echo encrypt($id_inv, $key_global); ?>">
                    <div class="mb-3">
                        <label>Diambil Oleh</label>
                        <input type="text" name="diambil_oleh" class="form-control <?php echo $bg ?>" value="<?php echo $nama_penerima ?>" <?php echo $display ?>>
                    </div>
                    <div class="mb-3">
                        <label>Diambil Tanggal</label>
                        <input type="text" id="date" name="diambil_tanggal" class="form-control">
                    </div>
                        <div class="mb-3">
                        <label id="labelBukti1">Bukti Terima 1</label>
                        <br>
                        <input type="file" name="fileku1" id="fileku1" accept=".jpg, .jpeg, .png" onchange="compressAndPreviewImage(event)" required title="Pilih File">
                    </div>
                    <div class="mb-3 preview-image-2" id="imagePreview"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">tutup</button>
                    <button type="submit" class="btn btn-primary" name="diambil-oleh">Update Data</button>
                </div>
            </form>                                         
        </div>
    </div>
    <?php include "page/upload-img.php";  ?>
    <?php include "page/cek-upload.php"; ?>
    <style>
        .preview-image {
            max-width: 100%;
            height: auto;
        }
    </style>
</div>