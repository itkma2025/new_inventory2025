<style>
    .preview-image {
        max-width: 100%;
        height: auto;
    }
</style>
<div class="modal fade" id="reuploadDiambil" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                <form action="proses/reupload-trx-rev-ppn.php" method="POST" enctype="multipart/form-data">
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
                        <label id="labelResi">Diambil Oleh</label>
                        <input type="text" class="form-control" style="background-color: #f8f9fa;" value="<?php echo $diambil_oleh ?>" readonly>
                    </div>
                    <div class="mb-3" id="ubah_tanggal">
                        <label id="labelDate">Tanggal</label>
                        <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl"
                            id="ubah_date" required>
                    </div>
                    <!-- Preview Image -->
                    <div class="preview-image mb-3">
                        <img id="imagePreviewDiambil" src="#" alt="Preview Image" style="display:none; width: 100%;">
                        <p id="imageSizeDiambil" style="display:none;"></p>
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
                                <span id="uploadButtonTextDiambil"><i class="bi bi-upload"></i> Upload Bukti Terima</span>
                                <input class="upload" type="file" name="fileku" id="formFileDiambil" accept=".jpg, .png, .jpeg" onchange="compressImageDiambilLangsung(event, 'DiambilLangsung')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDikirim">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="reupload"> Reupload Bukti Terima</button>
                    </div>
                </form>
            </div>    
        </div>
    </div>
</div>
<script>
    function compressImageDiambilLangsung(event) {
        const fileInput = event.target;
        const file = fileInput.files[0];

        const imagePreviewId = "imagePreviewDiambil";
        const imageSizeId = "imageSizeDiambil";
        const fileInputId = "formFileDiambil";

        // console.log(">> Kondisi 'DiambilLangsung' dipilih");
        // console.log("Preview ID:", imagePreviewId);
        // console.log("Size ID:", imageSizeId);
        // console.log("File Input ID:", fileInputId);

        const imageSizeElement = document.getElementById(imageSizeId);
        if (file) {
            const fileSizeInKB = (file.size / 1024).toFixed(2);
            // console.log("Original File Size:", fileSizeInKB + " KB");
            if (imageSizeElement) {
            imageSizeElement.textContent = `File Size: ${fileSizeInKB} KB`;
            imageSizeElement.style.display = "none";
            }
        }

        if (file && file.size > 1 * 1024 * 1024) {
            const reader = new FileReader();
            reader.onload = function (e) {
            const img = new Image();
            img.src = e.target.result;

            img.onload = function () {
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");

                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0, img.width, img.height);

                canvas.toBlob(
                function (blob) {
                    const compressedFile = new File([blob], file.name, {
                    type: file.type,
                    });

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    fileInput.files = dataTransfer.files;

                    const previewURL = URL.createObjectURL(compressedFile);
                    console.log("Compressed File Preview URL:", previewURL);
                    const previewElement = document.getElementById(imagePreviewId);
                    if (previewElement) {
                    previewElement.src = previewURL;
                    previewElement.style.display = "block";
                    }
                },
                file.type,
                0.7
                );
            };
            };
            reader.readAsDataURL(file);
        } else if (file) {
            const previewURL = URL.createObjectURL(file);
            // console.log("Original File Preview URL:", previewURL);
            const previewElement = document.getElementById(imagePreviewId);
            if (previewElement) {
            previewElement.src = previewURL;
            previewElement.style.display = "block";
            }
        }
    }
</script>