<div class="modal fade" id="ubahJenisPengiriman" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Update Jenis Pengiriman</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="proses/proses-ubah-status-trx-rev-ppn.php" method="POST"
                    enctype="multipart/form-data">
                    <input type="hidden" name="id_komplain" value="<?php echo $id ?>">
                    <input type="hidden" name="id_status_kirim_revisi" value="<?php echo $id_status_kirim_revisi ?>">
                    <input type="hidden" name="id_inv" value="<?php echo $id_inv ?>">
                    <input type="hidden" name="id_inv_revisi" value="<?php echo $id_inv_revisi ?>">
                    <input type="hidden" name="id_bukti_terima" value="<?php echo $id_bukti_terima ?>">
                    <input type="hidden" name="bukti_sebelumnya" value="<?php echo $bukti_satu ?>">
                    <div id="trxKirim">
                        <div class="mb-3">
                            <label>Jenis Pengiriman</label>
                            <select class="form-select" name="jenis_pengiriman" id="ubah_jenis_pengiriman" required>
                                <option value="">Pilih</option>
                                <option value="Driver">Driver</option>
                                <option value="Ekspedisi">Expedisi</option>
                                <option value="Diambil Langsung">Diambil Langsung</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3" id="ubah_jenis_driver" style="display: none;">
                        <label id="labelDriver">Pilih Driver</label>
                        <select id="ubah-pengirim" name="pengirim" class="form-select" required>
                            <option value="">Pilih...</option>
                            <?php
                            include "koneksi.php";
                            $sql_driver = mysqli_query($koneksi2, "SELECT us.id_user_role, us.id_user, us.nama_user, rl.nama_role FROM $database2.user AS us JOIN user_role rl ON (us.id_user_role = rl.id_user_role) WHERE rl.nama_role = 'Driver'");
                            while ($data_driver_rev = mysqli_fetch_array($sql_driver)) {
                            ?>
                            <option value="<?php echo $data_driver_rev['id_user'] ?>">
                                <?php echo $data_driver_rev['nama_user'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3" id="ubah_jenis_ekspedisi" style="display: none;">
                        <div class="mb-3">
                            <label id="labelEkspedisi">Pilih Ekspedisi</label>
                            <select id="ekspedisi-ubah" name="ekspedisi" class="form-select selectize-js">
                                <option value="">Pilih...</option>
                                <?php
                                include "koneksi.php";
                                $sql_ekspedisi = mysqli_query($connect, "SELECT * FROM ekspedisi");
                                while ($data_ekspedisi = mysqli_fetch_array($sql_ekspedisi)) {
                                ?>
                                <option value="<?php echo $data_ekspedisi['id_ekspedisi'] ?>">
                                    <?php echo $data_ekspedisi['nama_ekspedisi'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label id="labelResi">No. Resi</label>
                            <input type="text" class="form-control" name="resi" id="ubah_resi">
                        </div>
                        <div class="mb-3">
                            <label id="labelJenisOngkir">Jenis Ongkir</label>
                            <select id="ubah_jenis_ongkir" name="jenis_ongkir" class="form-select">
                                <option>Pilih</option>
                                <option value="0">Non COD</option>
                                <option value="1">COD</option>
                            </select>
                        </div>
                        <div class="mb-3" id="ubah_ongkir" style="display: block;">
                            <div class="row">
                                <label>Nominal Ongkir</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" style="background-color: #f8f9fa;"
                                        name="ongkir" id="ubah_ongkos_kirim" readonly>
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" name="free_ongkir" type="checkbox" value="1"
                                            id="ubah_free_ongkir">
                                        <label class="form-check-label" for="ubah_free_ongkir" id="ubah_free_ongkir_label">
                                            Free Ongkir
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label id="labelDikirimOleh">Dikirim Oleh</label>
                            <input type="text" class="form-control" name="dikirim" id="ubah_dikirim_oleh">
                        </div>
                        <div class="mb-3">
                            <label id="labelPj">Penanggung Jawab</label>
                            <input type="text" class="form-control" name="pj" id="ubah_penanggung_jawab">
                        </div>
                    </div>
                    <div class="mb-3" id="ubah_jenis_diambil" style="display: none;">
                        <label id="labelDiambil">Diambil Oleh</label>
                        <input type="text" name="diambil_oleh" id="ubah_diambil" class="form-control">
                    </div>
                    <div class="mb-3" id="alasanCancel">
                        <label id="labelResi">Alasan Perubahan</label>
                        <input type="text" class="form-control" name="alasan_ubah" id="alasan_ubah" required>
                    </div>
                    <div class="mb-3" id="ubah_tanggal">
                        <label id="labelDate">Tanggal</label>
                        <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl"
                            id="ubah_date" required>
                    </div>
                    <div class="mb-3" id="buktiTerimaUbah" style="display: none;">
                        <div class="preview-image mb-3">
                            <img id="imagePreviewEdit" src="#" alt="Preview Image" style="display:none;">
                            <p id="imageSizeEdit" style="display:none;"></p>
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <div class="fileUpload btn btn-primary">
                                    <span id="uploadButtonTextEdit"><i class="bi bi-upload"></i> Upload Bukti Terima</span>
                                    <input class="upload" type="file" name="fileku" id="formFileEdit" accept=".jpg, .png, .jpeg" onchange="compressImage(event, 'Edit')">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            id="cancelDikirim">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="ubah-pengiriman"> Ubah Status</button>
                    </div>
                </form>
            </div>
            <style>
            .preview-image {
                max-width: 100%;
                height: auto;
            }
            </style>
            <!-- Selectize JS -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var jenisPengiriman = document.getElementById('ubah_jenis_pengiriman');
                    var jenisEkspedisi = document.getElementById('ubah_jenis_ekspedisi');
                    var jenisDriver = document.getElementById('ubah_jenis_driver');
                    var jenisDiambil = document.getElementById('ubah_jenis_diambil');
                    var ekspedisiSelectize = document.getElementById('ekspedisi-ubah');
                    var pengirim = document.getElementById('ubah-pengirim');
                    var resi = document.getElementById('ubah_resi');
                    var jenis_ongkir = document.getElementById('ubah_jenis_ongkir');
                    var bukti = document.getElementById('buktiTerimaUbah');
                    var diambil = document.getElementById('ubah_diambil');
                    var ongkos_kirim = document.getElementById('ubah_ongkos_kirim');
                    var penanggung_jawab = document.getElementById('ubah_penanggung_jawab');
                    var dikirim_oleh = document.getElementById('ubah_dikirim_oleh');
                    var imgPreview = document.getElementById('imagePreviewEdit');
                    var fileku = document.getElementById('formFileEdit');
                    var alasanUbah = document.getElementById('alasan_ubah');
                    var freeOngkirCheckbox = document.getElementById('ubah_free_ongkir');
                    var freeOngkirLabel = document.getElementById('ubah_free_ongkir_label');
                    
                    jenisPengiriman.addEventListener('change', function() {
                        if (this.value === 'Driver') {
                            jenisDriver.style.display = 'block';
                            jenisEkspedisi.style.display = 'none';
                            jenisDiambil.style.display = 'none';
                            ekspedisiSelectize.selectize.clear();
                            ekspedisiSelectize.removeAttribute('required');
                            resi.value = '';
                            resi.removeAttribute('required');
                            ongkos_kirim.value = '';
                            jenis_ongkir.value = '';
                            jenis_ongkir.removeAttribute('required');
                            freeOngkirCheckbox.checked = false; // Hilangkan centang jika ada
                            diambil.value = '';
                            diambil.removeAttribute('required');
                            dikirim_oleh.value = '';
                            penanggung_jawab.value = '';
                        
                            imgPreview.style.display = 'none';
                            fileku.value = '';
                            fileku.removeAttribute('required');
                            bukti.style.display = 'none';
                            tanggal.removeAttribute('required');
                            tanggal.style.display = 'block';
                            pengirim.style.display = 'block';
                            pengirim.setAttribute('required', 'true');
                            alasanUbah.value = '';
                        } else if (this.value === 'Ekspedisi') {
                            jenisEkspedisi.style.display = 'block';
                            jenisDriver.style.display = 'none';
                            pengirim.value = '';
                            pengirim.removeAttribute('required');
                            ekspedisiSelectize.setAttribute('required', 'true');
                            resi.setAttribute('required', 'true');
                            jenis_ongkir.setAttribute('required', 'true');
                            tanggal.removeAttribute('required');
                            ongkos_kirim.removeAttribute('required');
                            penanggung_jawab.removeAttribute('required');
                            jenisDiambil.style.display = 'none';
                            diambil.value = '';
                            diambil.removeAttribute('required');
                            bukti.style.display = 'block';
                            dikirim_oleh.removeAttribute('required');
                            fileku.setAttribute('required', 'true');
                            alasanUbah.value = '';
                        } else if (this.value === 'Diambil Langsung') {
                            jenisDriver.style.display = 'none';
                            jenisDiambil.style.display = 'block';
                            diambil.setAttribute('required', 'true');
                            pengirim.removeAttribute('required');
                            jenisEkspedisi.style.display = 'none';
                            ekspedisiSelectize.selectize.clear();
                            ekspedisiSelectize.removeAttribute('required');
                            resi.value = '';
                            resi.removeAttribute('required');
                            ongkos_kirim.value = '';
                            jenis_ongkir.value = '';
                            jenis_ongkir.removeAttribute('required');
                            freeOngkirCheckbox.checked = false; // Hilangkan centang jika ada
                            dikirim_oleh.value = '';
                            penanggung_jawab.value = '';
                            tanggal.removeAttribute('required');
                            pengirim.style.display = 'none';
                            tanggal.style.display = 'block';
                            bukti.style.display = 'block';
                            fileku.setAttribute('required', 'true');
                            alasanUbah.value = '';
                        } else {
                            jenisEkspedisi.style.display = 'none';
                            jenisDriver.style.display = 'none';
                            ekspedisi.selectize.clear();
                            ekspedisiSelectize.removeAttribute('required');
                            bukti.removeAttribute('required');
                            pengirim.removeAttribute('required');
                            tanggal.style.display = 'none';
                            bukti.style.display = 'none';
                            jenisDiambil.style.display = 'none';
                            diambil.value = '';
                            diambil.removeAttribute('required');
                        }
                    });

                    jenis_ongkir.addEventListener('change', function() {
                        if (this.value === '0') {
                            ongkos_kirim.style.display = 'block';
                            ongkos_kirim.style.backgroundColor = '';
                            ongkos_kirim.removeAttribute('readonly');
                            ongkos_kirim.setAttribute('required', 'true');
                            freeOngkirCheckbox.style.display = 'block'; // Hilangkan centang jika ada
                            freeOngkirLabel.style.display = 'block'; // Hilangkan centang jika ada
                        } else {
                            ongkos_kirim.style.display = 'block';
                            ongkos_kirim.style.backgroundColor = '#f8f9fa';
                            ongkos_kirim.removeAttribute('required');
                            ongkos_kirim.setAttribute('readonly', 'true');
                            ongkos_kirim.value = '0';
                            freeOngkirCheckbox.style.display = 'none'; // Hilangkan centang jika ada
                            freeOngkirLabel.style.display = 'none'; // Hilangkan centang jika ada
                        }
                    });

                    ongkos_kirim.addEventListener('input', function() {
                        formatNumber(ongkos_kirim);
                    });

                    function formatNumber(input) {
                        var value = input.value.replace(/\D/g, ''); // Menghapus karakter non-digit
                        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Menambahkan pemisah ribuan dengan titik
                        input.value = value;
                    }


                    const cancelButton = document.getElementById('cancelDikirim');
                        cancelButton.addEventListener('click', function() {
                        location.reload();
                    });

                    flatpickr("#ubah_date", {
                        dateFormat: "d/m/Y",
                        defaultDate: "today"
                    });
                });
            </script>

        </div>
    </div>
</div>