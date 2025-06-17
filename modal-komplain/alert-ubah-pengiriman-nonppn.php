<div class="modal fade" id="alertUbahJenisPengiriman" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Konfirmasi Ubah Jenis Pengiriman </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="proses/alert-ubah-pengiriman.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_komplain" value="<?php echo $id ?>">
                    <input type="hidden" name="id_status_kirim_revisi" value="<?php echo $id_status_kirim_revisi ?>">
                    <input type="hidden" name="id_inv" value="<?php echo $id_inv ?>">
                    <input type="hidden" name="id_inv_revisi" value="<?php echo $id_inv_revisi ?>">
                    <input type="hidden" name="id_bukti_terima" value="<?php echo $id_bukti_terima ?>">
                    <input type="hidden" name="bukti_sebelumnya" value="<?php echo $bukti_satu ?>">
                    <div id="trxKirim">
                        <div class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label id="labelResi">Jenis Pengiriman</label>
                                    <input type="text" class="form-control bg-light" name="jenis_pengiriman" id="ubah_jenis_pengiriman" value="<?php echo $jenis_pengiriman ?>" readonly>
                                </div>
                                <?php  
                                    if ($jenis_pengiriman == 'Ekspedisi') {
                                        ?>
                                            <div class="col" id="ubah_jenis_ekspedisi">
                                                <label id="labelResi">Ekspedisi</label>
                                                <input type="text" class="form-control bg-light" id="ekspedisi-ubah" name="ekspedisi" value="<?php echo $nama_ekspedisi ?>" readonly>
                                            </div> 
                                       <?php
                                    } else if ($jenis_pengiriman == 'Driver') {
                                        ?>
                                            <div class="col" id="ubah_jenis_driver">
                                                <label id="labelResi">Driver</label>
                                                <input type="text" class="form-control bg-light" id="ubah-pengirim" name="pengirim" value="<?php echo $nama_driver_asli ?>" readonly>
                                            </div>
                                        <?php
                                    } else if ($jenis_pengiriman == 'Diambil Langsung') {
                                        ?>
                                            <div class="col" id="ubah_jenis_diambil">
                                                <label id="labelDiambil">Diambil Oleh</label>
                                                <input type="text" name="diambil_oleh" id="ubah_diambil" class="form-control bg-light">
                                            </div>
                                        <?php
                                    } else {
                                        header("Location: ../404.php");
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="border-top text-center mt-2">
                      <h4 class="fw-bold mt-3">Apakah anda yakin ingin mengubah pengiriman ini?</h4>
                    </div>
                    <!-- Modal footer -->
                    <div class="mt-4 text-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDikirim">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="alert-ubah-pengiriman-nonppn"> Ubah Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>