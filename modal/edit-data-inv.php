<div class="modal fade" id="editPelanggan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Data Detail</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <?php
                $action = "";
                if ($_GET['jenis'] == 'nonppn') {
                    $action = "proses/proses-invoice-nonppn.php";
                    $sql_status_cb = $connect->query("SELECT status_cb FROM cashback_nonppn WHERE id_inv = '$id_inv'");
                    if($sql_status_cb->num_rows > 0){
                        $data_status_cb = mysqli_fetch_array($sql_status_cb);
                        $tampil_status_cb = $data_status_cb['status_cb'];
                    }
                } else if ($_GET['jenis'] == 'ppn') {
                    $action = "proses/proses-invoice-ppn.php";
                    $sql_status_cb = $connect->query("SELECT status_cb FROM cashback_ppn WHERE id_inv = '$id_inv'");
                    if($sql_status_cb->num_rows > 0){
                        $data_status_cb = mysqli_fetch_array($sql_status_cb);
                        $tampil_status_cb = $data_status_cb['status_cb'];
                    }
                } else if ($_GET['jenis'] == 'bum') {
                    $action = "proses/proses-invoice-bum.php";
                    $sql_status_cb = $connect->query("SELECT status_cb FROM cashback_bum WHERE id_inv = '$id_inv'");
                    if($sql_status_cb->num_rows > 0){
                        $data_status_cb = mysqli_fetch_array($sql_status_cb);
                        $tampil_status_cb = $data_status_cb['status_cb'];
                    }
                } else {
                    ?>
                        <script type="text/javascript">
                            window.location.href = "../404.php";
                        </script>
                    <?php
                }
            ?>
            <form action="<?php echo $action ?>" method="POST">
                <input type="hidden" name="id_inv" value="<?php echo encrypt($id_inv, $key_global) ?>">
                <div class="mb-3">
                    <label class="fw-bold">Customer Invoice</label>
                    <input type="text" class="form-control" name="cs_inv" value="<?php echo $data_inv['cs_inv'] ?>">
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Alamat</label>
                    <textarea class="form-control" name="alamat" cols="30" rows="3"><?php echo $data_inv['alamat'] ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">No PO</label>
                    <input type="text" class="form-control" name="no_po" value="<?php echo $data_inv['no_po'] ?>">
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Tambahan Invoice</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="kwitansi"  value="1" <?php echo ($data_inv['kwitansi'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label">Kwitansi</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="surat_jalan" value="1" <?php echo ($data_inv['surat_jalan'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label">Surat Jalan</label>
                    </div>
                </div>
                <?php  
                     if($sql_status_cb->num_rows > 0){
                        ?>
                            <div class="mb-3" id="status_cb">
                                <label class="fw-bold">Status Cashback</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status_cb" id="inlineRadio1" value="1" <?php echo ($tampil_status_cb == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="inlineRadio1">Cashback</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status_cb" id="inlineRadio2" value="0" <?php echo ($tampil_status_cb == 0) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="inlineRadio2">Non Cashback</label>
                                </div>
                            </div>
                        <?php
                    }
                ?>
                <div class="modal-footer">
                    <button type="submit" name="ubah-cs-inv" class="btn btn-primary">Edit Data Detail</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form> 
        </div>
    </div>
</div>