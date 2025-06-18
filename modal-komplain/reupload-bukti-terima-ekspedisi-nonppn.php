<style>
    .preview-image {
        max-width: 100%;
        height: auto;
    }
</style>
<div class="modal fade" id="reuploadEx" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Reupload Bukti Pengiriman</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                    $encrypt_image = encrypt($bukti_satu, $key_global);
                    $view_image = urlencode($encrypt_image);
                    $path = "image-history-revisi.php?file=$view_image";
                    $img = '';
                    if (file_exists("gambar-revisi/bukti1/" . $bukti_satu)) {
                        $img = $path;
                    } else {
                        $img = "assets/img/no_img.jpg";
                    }
                ?>
                <form action="proses/reupload-trx-rev-nonppn.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_komplain" value="<?php echo $id ?>">
                    <input type="hidden" name="id_status_kirim_revisi" value="<?php echo $id_status_kirim_revisi ?>">
                    <input type="hidden" name="id_inv" value="<?php echo $id_inv ?>">
                    <input type="hidden" name="id_inv_revisi" value="<?php echo $id_inv_revisi ?>">
                    <input type="hidden" name="id_bukti_terima" value="<?php echo $id_bukti_terima ?>">
                    <input type="hidden" name="bukti_sebelumnya" value="<?php echo $bukti_satu ?>">
                    <div class="mb-3">
                        <label id="labelResi">Jenis Pengiriman</label>
                        <input type="text" class="form-control" style="background-color: #f8f9fa;" value="<?php echo $data_cek_jenis_pengiriman['jenis_pengiriman'] ?>"readonly>
                    </div>
                    <div class="mb-3">
                        <label id="labelResi">Nama Ekspedisi</label>
                        <input type="text" class="form-control" style="background-color: #f8f9fa;" value="<?php echo $nama_penerima ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label id="labelResi">No. Resi</label>
                        <input type="text" class="form-control" style="background-color: #f8f9fa;" value="<?php echo $no_resi ?>" readonly>
                    </div>
                     <div class="mb-3">
                        <label id="labelResi">Jenis Ongkir</label>
                        <input type="text" class="form-control" style="background-color: #f8f9fa;" value="<?php echo ($jenis_ongkir == 0) ? 'Non COD' : 'COD'; ?>" readonly>
                    </div>
                    <div class="mb-3" id="ubah_ongkir" style="display: block;">
                        <div class="row">
                            <label>Nominal Ongkir</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" style="background-color: #f8f9fa;"  value="<?php echo number_format($nominal_ongkir,0,'.','.'); ?>" readonly>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" <?php echo ($free_ongkir == 0) ? '' : 'checked'; ?> readonly onclick="return false;">
                                    <label class="form-check-label" for="ubah_free_ongkir" id="ubah_free_ongkir_label">
                                        Free Ongkir
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label id="labelDikirimOleh">Dikirim Oleh</label>
                        <input type="text" class="form-control" style="background-color: #f8f9fa;" value="<?php echo $dikirim_oleh ?>">
                    </div>
                    <div class="mb-3">
                        <label id="labelPj">Penanggung Jawab</label>
                        <input type="text" class="form-control" style="background-color: #f8f9fa;" value="<?php echo $penanggung_jawab ?>">
                    </div>
                    <div class="mb-3" id="ubah_tanggal">
                        <label id="labelDate">Tanggal</label>
                        <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl"
                            id="ubah_date" required>
                    </div>
                    <!-- Preview Image -->
                    <div class="preview-image mb-3">
                        <img id="imagePreviewReupload" src="#" alt="Preview Image" style="display:none; width: 100%;">
                        <p id="imageSizeReupload" style="display:none;"></p>
                    </div>
                    <div class="d-flex justify-content-start">

                        <div class="mb-3 me-3">
                             <a href="<?php echo $img; ?>" data-fancybox="gallery" data-width="1600" data-height="1200">
                                <button type="button" class="btn btn-secondary"><i class="bi bi-image"></i> Gambar Sebelumnya</button>
                            </a>
                        </div>
                
                        <div class="mb-3">
                            <div class="input-group">
                                <div class="fileUpload btn btn-primary">
                                <span id="uploadButtonTextReupload"><i class="bi bi-upload"></i> Upload Bukti Terima</span>
                                <input class="upload" type="file" name="fileku" id="formFileReupload" accept=".jpg, .png, .jpeg" onchange="compressImage(event, 'Reupload')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            id="cancelDikirim">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="reupload-ex"> Reupload Bukti Terima</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

