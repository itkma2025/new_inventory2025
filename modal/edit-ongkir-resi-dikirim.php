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

                        ?>
                        <form action="proses/proses-ubah-ongkir-resi.php" method="POST" enctype="multipart/form-data" id="formOngkir">
                            <input type="hidden" name="nonce_token" value="<?php echo $_SESSION['nonce_token']; ?>">
                            <input type="hidden" class="form-control" name="id_inv" value="<?php echo encrypt($id_inv, $key_global) ?>">
                            <input type="hidden" class="form-control" name="ekspedisi" value="<?php echo $dikirim_ekspedisi ?>">
                            <div class="mb-3">
                                <label id="labelResi">No. Resi</label>
                                <input type="text" class="form-control" name="edit_resi" id="resiEdit" value="<?php echo $no_resi ?>" autocomplete="off" required>
                            </div>
                            <div class="mb-3">
                                <label id="labelJenisOngkir">Jenis Ongkir</label>
                                <select id="jenis_ongkir_edit" name="jenis_ongkir_edit" class="form-select" required>
                                    <option value="">Pilih</option>
                                    <option value="0">Non COD</option>
                                    <option value="1">COD</option>
                                </select>
                            </div>
                            <div class="mb-3" id="ongkirDiv" style="display: none;">
                                <div class="row">
                                    <label id="labelOngkir">Nominal Ongkir</label>
                                    <div class="col">
                                        <input type="text" class="form-control" name="edit_ongkir" id="edit_ongkir" min="1" value="<?php echo number_format($ongkir) ?>">
                                        <small class="text-danger d-none" id="alertOngkir">Ongkir wajib diisi !</small>
                                    </div>
                                    <?php if (in_array('Pengiriman', $cashback_values)): ?>
                                        <?php 
                                            $checked = in_array('Pengiriman', $cashback_values) ? 'checked' : '';
                                        ?>
                                        <div class="col">
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" name="free_ongkir" type="checkbox" value="1"
                                                    id="free_ongkir" <?php echo $checked; ?> onclick="return false;">
                                                <label class="form-check-label" for="free_ongkir" id="free_ongkir_label">
                                                    Free Ongkir
                                                </label>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label id="labelBukti1">Bukti Terima 1</label>
                                <input type="file" name="fileku1" id="fileku1" accept=".jpg, .jpeg, .png" onchange="compressAndPreviewImageEx(event)" required>
                            </div>
                            <div class="mb-3 preview-image-3" id="imagePreviewEx"></div>

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