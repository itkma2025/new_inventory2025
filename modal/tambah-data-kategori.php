<div class="modal fade" id="modal1" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Tambah Data Kategori Produk</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="proses/proses-kat-produk.php" method="POST" id="uploadForm" enctype="multipart/form-data">
                    <?php
                    require_once "function/uuid.php";
                    $uuid = uuid();
                    ?>
                    <label class="form-label">Jenis Kategori</label>
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kategori" id="inlineRadio1" value="local" required>
                            <label class="form-check-label" for="inlineRadio1">Local</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="jenis_kategori" id="inlineRadio2" value="import" required>
                            <label class="form-check-label" for="inlineRadio2">Import</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori Produk</label>
                        <input type="hidden" class="form-control" name="id_kat_produk" value="KATPROD<?php echo $uuid; ?>">
                        <input type="text" class="form-control" name="nama_kat_produk" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Merk</label>
                        <select class="form-select" name="merk" id="merkSelect" required>
                            <option value="">Pilih Merk...</option>
                        </select>
                    </div>
                    <label class="form-label">Status NIE</label>
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status_nie" id="statusNie1" value="1" required>
                            <label class="form-check-label" for="statusNie1">Ada</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status_nie" id="statusNie2" value="0" required>
                            <label class="form-check-label" for="statusNie2">Tidak</label>
                        </div>
                    </div>
                    <div id="divNie" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Nomor Izin Edar</label>
                            <input type="text" class="form-control" name="nie" id="nie">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tgl. Terbit</label>
                            <input type="date" class="form-control" name="tgl_terbit" id="terbit">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Berlaku Sampai</label>
                            <input type="date" class="form-control" name="expired_date" id="exp">
                        </div>
                        <div class="upload-container">
                            <div class="drop-zone" id="dropZone">
                                <i class="bi bi-cloud-upload"></i>
                                <p>Drag and Drop here</p>
                                <p>or</p>
                                <label class="btn-upload" for="fileInput">Select file</label>
                            </div>
                            <input type="file" id="fileInput" name="fileku" accept="image/png, image/jpg, image/jpeg, application/pdf" style="display: none;">

                            <div class="file-info" id="fileInfo" style="display: none;"></div>
                            <button type="button" id="resetButton">Reset File</button>
                        </div>

                        <!-- Fancybox PDF Container -->
                        <div style="display: none;">
                            <div id="pdf-container">
                                <embed id="pdfEmbed" src="" type="application/pdf" width="100%" height="500px"/>
                            </div>
                        </div>  
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="simpan-kat-produk" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Simpan Data</button>
                        <button type="button" class="btn btn-secondary btn-md" onclick="location.reload()"><i class="bi bi-x"></i> Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('input[name="jenis_kategori"]').on('change', function() {
            const jenis = $(this).val();

            $.ajax({
                url: 'ajax/get-merk.php',
                type: 'GET',
                data: { jenis: jenis },
                success: function(data) {
                    $('#merkSelect').html(data);
                },
                error: function(xhr, status, error) {
                    console.error('Terjadi kesalahan: ' + error);
                }
            });
        });
    });

  $(document).ready(function() {
    // Saat salah satu radio button diklik
    $('input[name="status_nie"]').on('change', function() {
        let statusNie = $('input[name="status_nie"]:checked').val();
        let divNie = $('#divNie');
        let nieInput = $('#nie');
        let terbitInput = $('#terbit');
        let expInput = $('#exp');
        let fileInput = $('#fileInput');
        if(statusNie == '1'){
            divNie.removeClass('d-none');
            nieInput.prop('required', true);  // Tambahkan required
            terbitInput.prop('required', true);
            expInput.prop('required', true);
            fileInput.prop('required', true);
        } else {
            divNie.addClass('d-none');
            nieInput.prop('required', false);  // Tambahkan required
            terbitInput.prop('required', false);
            expInput.prop('required', false);
            fileInput.prop('required', false);
        }
    });
});
</script>