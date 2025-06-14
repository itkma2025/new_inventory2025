<?php
include '../akses.php'; // Pastikan koneksi database tersedia
require_once __DIR__ . "/../function/function-enkripsi.php";

if (isset($_POST['id'])) {
    $id_kat_produk = decrypt($_POST['id'], $key_global); // Dekripsi ID
    $id_kat_produk = mysqli_real_escape_string($connect, $id_kat_produk);

    $sql_kat_produk = $connect->query("SELECT 
                                            tkp.id_kat_produk,
                                            tkp.nama_kategori,
                                            tkp.jenis_kategori,
                                            tkp.id_merk,
                                            tkp.status_nie,
                                            tkp.no_izin_edar,
                                            tkp.tgl_terbit,
                                            tkp.berlaku_sampai,
                                            DATE_FORMAT(STR_TO_DATE(tkp.berlaku_sampai, '%d/%m/%Y'), '%Y-%m-%d') AS tanggal_berlaku_sampai,
                                            tkp.file_nie,
                                            tkp.created_date,
                                            tkp.updated_date,
                                            mr.nama_merk
                                        FROM 
                                            tb_kat_produk AS tkp
                                        LEFT JOIN tb_merk AS mr ON tkp.id_merk = mr.id_merk
                                        WHERE tkp.id_kat_produk = '$id_kat_produk'
                                        ORDER BY tkp.nama_kategori ASC
                                        ");
    $data = mysqli_fetch_assoc($sql_kat_produk);
    $jenis_kategori_local = ($data['jenis_kategori'] == 'local') ? 'checked' : '';
    $jenis_kategori_import = ($data['jenis_kategori'] == 'import') ? 'checked' : '';
    $status_nie_ada = ($data['status_nie'] ==  '1') ? 'checked' : '';
    $status_nie_tidak_ada = ($data['status_nie'] == '0') ? 'checked' : '';
    $id_merk = $data['id_merk'];
    
    if ($data) {
        $file_nie = $data['file_nie'];
        $nama_kategori = $data['nama_kategori'];
        $_SESSION['data_file_nie'] = $file_nie;
        $_SESSION['data_nama_kategori'] = $nama_kategori;
        ?>
            <form action="proses/proses-kat-produk.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori Produk</label>
                    <input type="hidden" class="form-control" name="id_kat_produk" id="id_kat_produk" value="<?php echo $_POST['id'] ?>">
                    <input type="text" class="form-control" name="nama_kat_produk" id="nama_kategori" value="<?php echo $data['nama_kategori'] ?>" required>
                </div>
                <label class="form-label">Jenis Kategori</label>
                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_kategori" id="inlineRadio1" value="local" <?php echo $jenis_kategori_local; ?> required>
                        <label class="form-check-label" for="inlineRadio1">Local</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_kategori" id="inlineRadio2" value="import" <?php $jenis_kategori_import; ?> required>
                        <label class="form-check-label" for="inlineRadio2">Import</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Merk</label>
                    <select class="form-select" name="merk" id="merkSelect" data-selected="<?= $data['id_merk'] ?>" required>
                        <!-- Data dari ajax -->
                    </select>
                </div>
                <label class="form-label">Status NIE</label>
                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status_nie" id="statusNie1" value="1" <?php echo $status_nie_ada ?> required>
                        <label class="form-check-label" for="statusNie1">Ada</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status_nie" id="statusNie2" value="0" <?php echo $status_nie_tidak_ada ?> required>
                        <label class="form-check-label" for="statusNie2">Tidak</label>
                    </div>
                </div>
                <div id="divNie" class="d-none">
                    <div class="mb-3">
                        <label class="form-label">Nomor Izin Edar</label>
                        <input type="text" class="form-control" name="no_izin_edar" id="nie" value="<?php echo $data['no_izin_edar'] ?>" required>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label">Tgl. Terbit</label>
                        <div class="input-group flex-nowrap">
                        <input type="text" class="form-control" name="tgl_terbit" id="terbit" value="<?php echo $data['tgl_terbit'] ?>">
                        <button type="button" class="input-group-text bg-danger text-white" id="resetTerbit"> X </button>
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label">Tgl. Berlaku Sampai</label>
                        <div class="input-group flex-nowrap">
                        <input type="text" class="form-control" name="exp_date" id="exp" value="<?php echo $data['berlaku_sampai'] ?>">
                        <button type="button" class="input-group-text bg-danger text-white" id="resetExp"> X </button>
                        </div>
                    </div>
                    <div class="upload-container">
                        <div class="drop-zone" id="dropZoneEdit">
                            <i class="bi bi-cloud-upload"></i>
                            <p>Drag and Drop here</p>
                            <p>or</p>
                            <label class="btn-upload" for="fileInputEdit">Select file</label>
                        </div>
                        <input type="file" id="fileInputEdit" name="fileku" accept="image/png, image/jpg, image/jpeg, application/pdf" style="display: none;" >

                        <div class="file-info" id="fileInfoEdit" style="display: none;"></div>
                        <button type="button" id="resetButtonEdit">Reset File</button>
                    </div>

                    <!-- Fancybox PDF Container -->
                    <div style="display: none;">
                        <div id="pdf-container-edit">
                            <embed id="pdfEmbedEdit" src="" type="application/pdf" width="100%" height="500px"/>
                        </div>
                    </div>
                    <!-- Kontainer Show File Saat ini -->
                    <div style="display: none;">
                        <div id="pdf-container">
                            <embed id="pdf-embed" type="application/pdf" style="width:100%; height:600px;">
                            <script>
                                fetch("view-nie.php")
                                    .then(response => response.blob())
                                    .then(blob => {
                                        const url = URL.createObjectURL(blob);
                                        document.getElementById("pdf-embed").src = url;
                                    })
                                    .catch(error => console.error("Gagal memuat PDF:", error));
                            </script>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="edit-kat-produk" id="simpan" class="btn btn-primary btn-md"><i class="bx bx-save"></i> Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary btn-md" id="tutupModalDetail"><i class="bi bi-x"></i> Tutup</button>
                </div>
            </form>
        <?php
    } else {
        echo "<div class='alert alert-danger text-center'>Data tidak ditemukan.</div>";
    }
}
?>

<!-- Compress image  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
<script>
    // Untuk edit
    const existingFile = "<?php echo $file_nie; ?>";
    // Menghapus bagian 'files/' dari string path
    const filePathWithoutFiles = existingFile.replace('files/', '');
    const dropZoneEdit = document.getElementById("dropZoneEdit");
    const fileInputEdit = document.getElementById("fileInputEdit");
    const resetButtonEdit = document.getElementById("resetButtonEdit");
    const uploadButtonEdit = document.getElementById("uploadButtonEdit");
    const pdfEmbedEdit = document.getElementById("pdfEmbedEdit");

    const MAX_FILE_SIZE_MB_Edit = 5;
    const MAX_COMPRESSION_RATIO_Edit = 0.8;

    // Handle File Input
    fileInputEdit.addEventListener("change", async (event) => {
        let file = event.target.files[0];
        if (!file) return;
        handleFileEdit(file);
    });

    // Drag & Drop Events
    dropZoneEdit.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropZoneEdit.classList.add("dragover");
    });

    dropZoneEdit.addEventListener("dragleave", () => {
        dropZoneEdit.classList.remove("dragover");
    });

    dropZoneEdit.addEventListener("drop", (e) => {
        e.preventDefault();
        dropZoneEdit.classList.remove("dragover");

        let file = e.dataTransfer.files[0];
        if (file) {
            fileInputEdit.files = e.dataTransfer.files;
            handleFileEdit(file);
        }
    });

     // Menampilkan bagian HTML tergantung apakah filePathWithoutFiles kosong atau tidak
    if (filePathWithoutFiles !== "") {
        dropZoneEdit.innerHTML = `<p>
            <a data-fancybox data-src="#pdf-container" href="javascript:;">
                📂 ${filePathWithoutFiles}
            </a>
        </p>`;

        if (resetButtonEdit) resetButtonEdit.style.display = "inline-block";
        if (uploadButtonEdit) uploadButtonEdit.style.display = "inline-block";
    }

    

    // Fungsi Menangani File
    async function handleFileEdit(file) {
        let fileSizeMB = file.size / (1024 * 1024);
        let fileType = file.type;

        console.log("File dipilih:", file.name, "| Size:", fileSizeMB.toFixed(2), "MB");

        if (fileSizeMB > MAX_FILE_SIZE_MB_Edit) {
            if (fileType === "application/pdf") {
                let compressedFile = await forceCompressPDFEdit(file);
                updateFileInputEdit(compressedFile, file.name);
            } else {
                alert("File terlalu besar dan tidak bisa dikompresi!");
            }
        } else {
            updateFileInputEdit(file, file.name);
        }
    }

    // Fungsi Perbarui File di Input
    function updateFileInputEdit(file, originalName) {
        fileInputEdit.files = createFileListEdit(file);
        displayFileEdit(file, originalName);
    }

    // Fungsi Menampilkan File yang Dipilih
    function displayFileEdit(file) {
        let fileSizeMB = file.size / (1024 * 1024);

        // Buat URL Blob dengan nama file yang benar
        let blob = new Blob([file], { type: file.type });
        let fileURL = URL.createObjectURL(blob);

        // Buat ID unik berdasarkan timestamp
        let uniqueId = Date.now(); // Contoh: 1700000000000
        let uniqueFileName = `${uniqueId}_${file.name}`;
          
        // Jika filePathWithoutFiles kosong, tampilkan file yang dipilih
        dropZoneEdit.innerHTML = `<p>
            <a href="${fileURL}" data-fancybox="pdf" data-type="iframe" data-title="${uniqueFileName}" id="pdfLinkEdit">
                📂 ${file.name} (${fileSizeMB.toFixed(2)} MB)
            </a>
        </p>`;
        

        // Set PDF embed dengan URL yang sesuai
        pdfEmbedEdit.src = fileURL;

        if (resetButtonEdit) resetButtonEdit.style.display = "inline-block";
        if (uploadButtonEdit) uploadButtonEdit.style.display = "inline-block";
    }

    // Event listener untuk memastikan setiap Fancybox memiliki nama unik
    document.addEventListener("DOMContentLoaded", function () {
        Fancybox.bind("[data-fancybox]", {
            beforeLoad: (instance, slide) => {
                let link = slide.$trigger;
                let uniqueName = link.dataset.title;

                // Set judul Fancybox agar sesuai dengan nama file unik
                slide.caption = uniqueName;
            }
        });
    });

    // Fungsi Buat FileList dari File yang Dikompresi
    function createFileListEdit(file) {
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        return dataTransfer.files;
    }

    // Fungsi Paksa Kompresi PDF Hingga 10%
    async function forceCompressPDFEdit(file) {
        try {
            let currentFile = file;
            let originalSize = file.size;
            let targetSize = originalSize * MAX_COMPRESSION_RATIO_Edit;

            while (currentFile.size > targetSize) {
                let compressedFile = await compressPDFEdit(currentFile, file.name);
                if (compressedFile.size >= currentFile.size) {
                    console.warn("Kompresi tambahan tidak efektif, menggunakan hasil terbaik.");
                    break;
                }
                currentFile = compressedFile;
            }

            return currentFile;
        } catch (error) {
            console.error("Kompresi PDF gagal:", error);
            alert("Terjadi kesalahan saat mengompresi PDF!");
            return file;
        }
    }

    // Fungsi Kompresi PDF (Gunakan PDF-LIB)
    async function compressPDFEdit(file, originalName) {
        const reader = new FileReader();
        reader.readAsArrayBuffer(file);

        return new Promise((resolve) => {
            reader.onloadend = async function () {
                const pdfBytes = reader.result;
                const pdfDoc = await PDFLib.PDFDocument.load(pdfBytes);

                pdfDoc.setTitle("");
                pdfDoc.setAuthor("");
                pdfDoc.setSubject("");

                const pages = pdfDoc.getPages();
                pages.forEach((page) => {
                    let { width, height } = page.getSize();
                    page.scale(0.8, 0.8);
                });

                const compressedPdfBytes = await pdfDoc.save({
                    useObjectStreams: false,
                    compress: true,
                });

                const compressedFile = new File([compressedPdfBytes], originalName, { type: "application/pdf" });

                console.log("PDF dikompresi:", compressedFile.name, "| Size:", (compressedFile.size / (1024 * 1024)).toFixed(2), "MB");

                resolve(compressedFile);
            };
        });
    }

    // Fungsi Reset Form
    resetButtonEdit.addEventListener("click", (e) => {
        e.preventDefault();
        resetUploadEdit();
    });

    function resetUploadEdit() {
        fileInputEdit.value = "";
        dropZoneEdit.innerHTML = `<i class="bi bi-cloud-upload"></i>
            <p>Drag and Drop here</p>
            <p>or</p>
            <label class="btn-upload" for="fileInputEdit">Select file</label>`;

        pdfEmbedEdit.src = ""; // Hapus preview PDF
        if (resetButtonEdit) resetButtonEdit.style.display = "none";
        if (uploadButtonEdit) uploadButtonEdit.style.display = "none";
    }
</script>

<script>
    // $(document).ready(function() {
    //     // Simpan nilai awal dari input
    //     var originalFile = $("#fileInputEdit")[0].files[0]; // Simpan file awal, jika ada
    //     var originalNama = $("#nama_kategori").val();
    //     var originalMerk = $("#merk").val();
    //     var originalNie = $("#nie").val();
    //     var originalTerbit = $("#terbit").val();
    //     var originalExp = $("#exp").val();

    //     // Tombol simpan
    //     var simpanBtn = $("#simpan");

    //     // Fungsi untuk memeriksa perubahan dan mengaktifkan/menonaktifkan tombol simpan
    //     function checkChanges() {
    //         var currentFile = $("#fileInputEdit")[0].files[0]; // Ambil file yang dipilih saat ini
    //         var currentNama = $("#nama_kategori").val();
    //         var currentMerk = $("#merk").val();
    //         var currentNie = $("#nie").val();
    //         var currentTerbit = $("#terbit").val();
    //         var currentExp = $("#exp").val();

    //         // Periksa apakah ada perubahan pada input atau file
    //         if (currentFile !== originalFile || currentNama !== originalNama || currentMerk !== originalMerk || currentNie !== originalNie || currentTerbit !== originalTerbit || currentExp !== originalExp) {
    //             simpanBtn.prop('disabled', false); // Aktifkan tombol simpan jika ada perubahan
    //         } else {
    //             simpanBtn.prop('disabled', true); // Nonaktifkan tombol simpan jika tidak ada perubahan
    //         }
    //     }

    //     // Deteksi perubahan pada input form
    //     $("#nama_kategori, #merk, #nie, #terbit, #exp").on('input', function() {
    //         checkChanges(); // Cek perubahan setiap kali input berubah
    //     });

    //     // Reset nilai "Tgl. Terbit" dan "Tgl. Berlaku Sampai"
    //     $("#resetTerbit").on('click', function() {
    //         // Reset input "Tgl. Terbit" ke nilai awal
    //         $("#terbit").val('');  
    //         checkChanges(); // Cek perubahan setelah reset
    //     });

    //     $("#resetExp").on('click', function() {
    //         // Reset input "Tgl. Berlaku Sampai" ke nilai awal
    //         $("#exp").val('');  
    //         checkChanges(); // Cek perubahan setelah reset
    //     });

    //     $('#tutupModalDetail').on('click', function() {
    //         // Reload halaman
    //         location.reload();
    //     });

    //     // Deteksi perubahan pada file input
    //     $("#fileInputEdit").on('change', function() {
    //         var file = this.files[0];
    //         if (file) {
    //             // Menampilkan informasi file
    //             // $("#fileInfoEdit").text(file.name).show();
    //         } else {
    //             $("#fileInfoEdit").hide();
    //         }
    //         checkChanges(); // Cek perubahan setelah file dipilih
    //     });

    //     // Reset file
    //     $("#resetButtonEdit").on('click', function() {
    //         // Reset input file
    //         $("#fileInputEdit").val('');
    //         $("#fileInfoEdit").hide(); // Sembunyikan info file
    //         originalFile = null; // Set file asli ke null setelah reset
    //         checkChanges(); // Cek perubahan setelah reset

    //         // Memperbarui originalFile ke null setelah reset
    //         originalFile = $("#fileInputEdit")[0].files[0]; // Pastikan originalFile kosong setelah reset
    //     });

    //     // Inisialisasi dengan nilai awal
    //     checkChanges();
    // });
    flatpickr("#exp", {
        dateFormat: "d/m/Y"
    });
    flatpickr("#terbit", {
        dateFormat: "d/m/Y"
    });
</script>
<script>
    $(document).ready(function () {
        const selectedMerkId = $('#merkSelect').data('selected');
        let isFirstLoad = true;

        function loadMerk(jenis, merkId = null) {
            $.ajax({
                url: 'ajax/get-merk.php',
                type: 'GET',
                data: { jenis: jenis },
                success: function (data) {
                    $('#merkSelect').html(data);

                    // Jika ada merkId, maka tandai option yang sesuai
                    if (merkId) {
                        $('#merkSelect').val(merkId);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Terjadi kesalahan: ' + error);
                }
            });
        }

        // Saat user mengubah jenis_kategori
        $('input[name="jenis_kategori"]').on('change', function () {
            const jenis = $(this).val();
            // Pada saat user mengubah, kita TIDAK ingin memilih merk awal
            loadMerk(jenis); // tanpa merkId
        });

        // Saat halaman pertama kali dimuat (edit)
        const jenisTerpilih = $('input[name="jenis_kategori"]:checked').val();
        if (jenisTerpilih) {
            loadMerk(jenisTerpilih, selectedMerkId);
        }
    });

    $(document).ready(function () {
        let divNie = $('#divNie');
        let nieInput = $('#nie');
        let terbitInput = $('#terbit');
        let expInput = $('#exp');
        let fileInput = $('#fileInput');

        function toggleNieFields(statusNie) {
            if (statusNie === '1') {
                divNie.removeClass('d-none');
                nieInput.prop('required', true);
                terbitInput.prop('required', true);
                expInput.prop('required', true);
                fileInput.prop('required', true);
            } else {
                divNie.addClass('d-none');
                nieInput.prop('required', false);
                terbitInput.prop('required', false);
                expInput.prop('required', false);
                fileInput.prop('required', false);
                nieInput.val('');
                terbitInput.val('');
                expInput.val('');
                fileInput.val('');
            }
        }

        // Ambil nilai awal radio yang dicentang (dari PHP)
        let statusNieInit = $('input[name="status_nie"]:checked').val();
        toggleNieFields(statusNieInit); // Inisialisasi saat halaman dimuat

        // Event listener saat radio button diubah
        $('input[name="status_nie"]').on('change', function () {
            let statusNie = $(this).val();
            toggleNieFields(statusNie);
        });
    });

    $('#tutupModalDetail').on('click', function () {
        location.reload();
    });
</script>