<?php
    require_once "akses.php";
    $submit = "";
    if ($_GET['jenis'] == 'nonppn') {
        $submit = "ubah-ongkir-nonppn";
    } else if ($_GET['jenis'] == 'ppn') {
        $submit = "ubah-ongkir-ppn";
    } else if ($_GET['jenis'] == 'bum') {
        $submit = "ubah-ongkir-bum";
    } else {
        ?>
            <script type="text/javascript">
                window.location.href = "../404.php";
            </script>
        <?php
    }
?>
<!-- Modal Edit Ongkir-->
<div class="modal fade" id="editOngkir" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Resi dan Ongkir</h1>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <?php  
                        $status_kirim = $connect->query("SELECT 
                                                            sk.jenis_ongkir, 
                                                            sk.dikirim_ekspedisi,
                                                            ibt.jenis_reject,
                                                            eks.nama_ekspedisi
                                                        FROM status_kirim AS sk
                                                        LEFT JOIN inv_bukti_terima ibt ON sk.id_inv = ibt.id_inv
                                                        LEFT JOIN ekspedisi eks ON sk.dikirim_ekspedisi = eks.id_ekspedisi
                                                        WHERE sk.id_inv = '$id_inv'");
                        $data_sk = $status_kirim->fetch_assoc();
                        $jenis_ongkir = $data_sk['jenis_ongkir'];
                        $jenis_reject = $data_sk['jenis_reject'];
                        $dikirim_ekspedisi = $data_sk['dikirim_ekspedisi'];
                        $nama_ekspedisi = $data_sk['nama_ekspedisi'];

                        // Kondisi form input dan select
                        $select_attr = '';
                        $input_text_attr = '';
                        $display_select = '';
                        $display_input = '';
                        $bg = '';

                        if($jenis_reject == '2'){
                            // Bisa edit
                            $select_attr = 'required';
                            $input_text_attr = '';
                            $display_select = 'block';
                            $display_input = 'd-none';
                            $bg = '';
                        } else {
                            // Hanya lihat
                            $select_attr = 'disabled';
                            $input_text_attr = 'readonly';
                            $display_select = 'd-none';
                            $display_input = 'block';
                            $bg = 'bg-light';
                        }
                    ?>
                    <form action="proses/proses-ubah-ongkir-resi.php" method="POST" enctype="multipart/form-data" id="formOngkir">
                        <input type="hidden" name="nonce_token" value="<?php echo $_SESSION['nonce_token']; ?>">
                        <input type="hidden" name="id_inv" value="<?php echo encrypt($id_inv, $key_global) ?>">

                        <!-- Pilih Ekspedisi -->
                        <div class="mb-3 <?php echo $display_select ?>">
                            <label>Pilih Ekspedisi</label>
                            <select name="ekspedisi" id="pilihEkspedisi" class="form-select selectize-js" <?php echo $select_attr ?>>
                                <option value="<?php echo $dikirim_ekspedisi ?>" selected><?php echo $nama_ekspedisi ?: 'Pilih...'; ?></option>
                                <?php
                                    $sql_ekspedisi = mysqli_query($connect, "SELECT id_ekspedisi, nama_ekspedisi FROM ekspedisi WHERE id_ekspedisi != '$dikirim_ekspedisi'");
                                    while ($data_ekspedisi = mysqli_fetch_array($sql_ekspedisi)) {
                                ?>
                                <option value="<?php echo $data_ekspedisi['id_ekspedisi'] ?>"><?php echo $data_ekspedisi['nama_ekspedisi'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Ekspedisi Readonly -->
                        <div class="mb-3 <?php echo $display_input ?>">
                            <label>Ekspedisi</label>
                            <input type="text" class="form-control <?php echo $bg ?>" value="<?php echo $nama_ekspedisi ?>" readonly>
                            <input type="hidden" name="ekspedisi_reupload" value="<?php echo $dikirim_ekspedisi ?>">
                        </div>

                        <!-- Nomor Resi -->
                        <div class="mb-3">
                            <label>No. Resi</label>
                            <input type="text" class="form-control <?php echo $bg ?>" name="edit_resi" id="resiEdit" value="<?php echo $no_resi ?>" <?php echo $input_text_attr ?>>
                        </div>

                        <!-- Jenis Ongkir -->
                        <div class="mb-3 <?php echo $display_select ?>">
                            <label>Jenis Ongkir</label>
                            <select id="jenis_ongkir_edit" name="jenis_ongkir_edit" class="form-select" <?php echo $select_attr ?>>
                                <option value="">Pilih</option>
                                <option value="0" <?php echo ($jenis_ongkir == '0') ? 'selected' : ''; ?>>Non COD</option>
                                <option value="1" <?php echo ($jenis_ongkir == '1') ? 'selected' : ''; ?>>COD</option>
                            </select>
                        </div>

                        <div class="mb-3 <?php echo $display_input ?>">
                            <label>Jenis Ongkir</label>
                            <input type="text" class="form-control <?php echo $bg ?>" value="<?php echo ($jenis_ongkir == '1') ? 'COD' : 'Non COD'; ?>" readonly>
                            <input type="hidden" name="jenis_ongkir_reupload" value="<?php echo $jenis_ongkir ?>">
                        </div>

                        <!-- Ongkir -->
                        <div class="mb-3" id="ongkirDiv" style="display: <?php echo ($jenis_ongkir == '0') ? 'block' : 'none'; ?>">
                            <div class="row">
                                <label>Nominal Ongkir</label>
                                <div class="col">
                                    <input type="text" class="form-control <?php echo $bg ?>" name="edit_ongkir" id="edit_ongkir" value="<?php echo number_format($ongkir,0,'.','.') ?>" <?php echo $input_text_attr ?>>
                                    <small class="text-danger d-none" id="alertOngkir">Ongkir wajib diisi!</small>
                                </div>
                                <?php if (in_array('Pengiriman', $cashback_values)): ?>
                                <div class="col">
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" name="free_ongkir" type="checkbox" value="1" id="free_ongkir" checked onclick="return false;">
                                        <label class="form-check-label" for="free_ongkir">
                                            Free Ongkir
                                        </label>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Bukti Terima -->
                        <div class="mb-3">
                            <label>Bukti Terima</label>
                            <input type="file" name="fileku1" id="fileku1" accept=".jpg, .jpeg, .png" onchange="compressAndPreviewImageEx(event)" required>
                        </div>

                        <div class="mb-3 preview-image-3" id="imagePreviewEx"></div>

                        <!-- Buttons -->
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="<?php echo $submit ?>" id="btnUbah"><i class="bi bi-arrow-left-right"></i> Ubah Data</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelEdit"><i class="bi bi-x-circle"></i> Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- kode JS Dikirim -->
    <?php include "page/upload-img.php"; ?>
    <?php include "page/cek-upload.php"; ?>

    <style>
        .preview-image {
            max-width: 100%;
            height: auto;
        }
    </style>
    <script>
        var input = document.getElementById('resiEdit');

        input.addEventListener('input', function() {
            // Memperbolehkan huruf, angka, serta karakter '/' dan '-'
            var sanitizedValue = input.value.replace(/[^A-Za-z0-9/\-]/g, '');
            input.value = sanitizedValue;
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var jenisOngkirEdit = document.getElementById('jenis_ongkir_edit');
            var editOngkir = document.getElementById('edit_ongkir');
            var ongkirDiv = document.getElementById('ongkirDiv');
            var cancelEdit = document.getElementById('cancelEdit');
            var formOngkir = document.getElementById("formOngkir");
            var btnUbah = document.getElementById("btnUbah");
            var alertOngkir = document.getElementById("alertOngkir");

            jenisOngkirEdit.addEventListener('change', function () {
                if (this.value === '0') {
                    ongkirDiv.style.display = 'block';
                    editOngkir.style.backgroundColor = '';
                    editOngkir.setAttribute('required', 'true');
                    editOngkir.removeAttribute('readonly');
                    checkOngkirValue();
                } else if (this.value === '1') {
                    ongkirDiv.style.display = 'none';
                    editOngkir.setAttribute('readonly', 'true');
                    editOngkir.value = 0; // Set value editOngkir menjadi 0
                    btnUbah.removeAttribute('disabled'); 
                } else if (this.value === '') {
                    ongkirDiv.style.display = 'none';
                    editOngkir.removeAttribute('required');
                }
            });

            cancelEdit.addEventListener('click', function () {
                location.reload();
            });

            // Menambahkan event listener untuk input edit_ongkir
            editOngkir.addEventListener("input", function () {
                // Menghapus karakter selain angka
                let rawValue = editOngkir.value.replace(/[^\d]/g, '');

                // Memastikan nilai tidak melebihi 100 juta
                let numericValue = Number(rawValue);
                if (numericValue > 100000000) { // Batas maksimum adalah 100 juta
                    numericValue = 100000000;
                }

                // Memformat nilai ke dalam format angka dengan pemisah titik (ribuan)
                const formattedValue = numericValue.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                });

                // Menetapkan nilai yang diformat ke input
                editOngkir.value = formattedValue;

                // Periksa nilai ongkir setelah input berubah
                checkOngkirValue();
            });

            // Fungsi untuk memeriksa nilai ongkir dan mengatur tombol Ubah
            function checkOngkirValue() {
                // Ambil nilai dari editOngkir, hilangkan format ribuan, dan konversi ke angka
                let rawValue = editOngkir.value.replace(/[^\d]/g, '');
                let numericValue = Number(rawValue);

                // Periksa apakah nilai lebih besar dari 0
                if (numericValue > 0) {
                    btnUbah.removeAttribute('disabled');
                    alertOngkir.classList.add('d-none');
                } else {
                    btnUbah.setAttribute('disabled', 'true');
                    alertOngkir.classList.remove('d-none');
                }
            }
        });
    </script>
</div>
<!-- End Modal Edit Ongkir -->