<div class="modal fade" id="ubahStatus">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Status Transaksi Komplain</h1>
            </div>
            <div class="modal-body">
                <form action="proses/proses-ubah-status-trx-rev-bum.php" method="POST"
                    enctype="multipart/form-data">
                    <input type="hidden" name="id_komplain" value="<?php echo $id ?>">
                    <input type="hidden" name="id_inv" value="<?php echo $id_inv ?>">
                    <input type="hidden" name="no_inv" value="<?php echo $no_inv_fix ?>">
                    <input type="hidden" name="cs_inv" value="<?php echo $data_detail['cs_inv'] ?>">
                    <input type="hidden" name="alamat" value="<?php echo $data_detail['alamat'] ?>">
                    <input type="hidden" name="total_inv" value="<?php echo $grand_total_revisi ?>">
                    <input type="hidden" name="jenis_inv" value="<?php echo $jenis_inv ?>">
                    <div class="mb-3">
                        <p>Pilih aksi yang akan dilakukan untuk komplain pelanggan ini</p>
                    </div>
                    <div class="mb-3">
                        <?php  
                            // Menentukan apakah radio button "Dikirim" dan "Transaksi Selesai" harus ditampilkan atau disembunyikan
                            if ($total_data_rev != '0' && $status_kmpl == '0') {
                                $status_pengiriman = $data_rev['status_pengiriman'];
                                $status_trx_komplain = $data_rev['status_trx_komplain'];
                                $status_trx_selesai = $data_rev['status_trx_selesai'];
                                
                                $show_dikirim = "";
                                $show_selesai = "";
                                $show_cancel = "";

                                if($status_pengiriman == "1" && $status_trx_komplain == "1" && $status_trx_selesai == "1"){
                                    $show_dikirim = "none";
                                    $show_selesai = "none";
                                    $show_cancel = "none";
                                } else if($status_pengiriman == "1" && $status_trx_komplain == "1" && $status_trx_selesai == "0"){
                                    $show_dikirim = "none";
                                    $show_selesai = "inline-block";
                                    $show_cancel = "inline-block";
                                } else if ($status_pengiriman == '1' && $status_trx_komplain == '0' && $status_trx_selesai == '0') {
                                    $show_dikirim = "inline-block";
                                    $show_selesai = "inline-block";
                                    $show_cancel = "inline-block";
                                } else if ($status_pengiriman == "0" && $status_trx_komplain == "0" && $status_trx_selesai == "0") {
                                    $show_dikirim = "inline-block";
                                    $show_selesai = "inline-block";
                                    $show_cancel = "inline-block";
                                } else {
                                    $show_dikirim = "none";
                                    $show_selesai = "none";
                                    $show_cancel = "inline-block";
                                }
                            } else if ($total_data_rev == '0' && $status_kmpl == '0'){
                                $show_dikirim = "inline-block";
                                $show_selesai = "inline-block";
                                $show_cancel = "inline-block";
                            } else {
                                $show_dikirim = "none";
                                $show_selesai = "none";
                                $show_cancel = "inline-block";
                            }
                        ?>
                        <!-- Display Dikirim -->
                        <div class="form-check form-check-inline" style="display: <?php echo $show_dikirim ?>;">
                            <input class="form-check-input" type="radio" name="status_kirim" id="dikirim" value="dikirim">
                            <label class="form-check-label" for="dikirim">Dikirim</label>
                        </div>

                        <!-- Display Transaksi Selesai -->
                        <div class="form-check form-check-inline" style="display: <?php echo $show_selesai ?>;">
                            <input class="form-check-input" type="radio" name="status_kirim" id="selesai" value="selesai">
                            <label class="form-check-label" for="selesai">Transaksi Selesai</label>
                        </div>

                        <!-- Display Transaksi Cancel (Always visible) -->
                        <div class="form-check form-check-inline" style="display: <?php echo $show_cancel ?>;">
                            <input class="form-check-input" type="radio" name="status_kirim" id="cancel" value="cancel">
                            <label class="form-check-label" for="cancel">Transaksi Cancel</label>
                        </div>
                    </div>
                    <div id="trxKirim" style="display: none;">
                        <div class="mb-3">
                            <label>Jenis Pengiriman</label>
                            <select class="form-select" name="jenis_pengiriman" id="jenis_pengiriman">
                                <option value="">Pilih</option>
                                <option value="Driver">Driver</option>
                                <option value="Ekspedisi">Expedisi</option>
                                <option value="Diambil Langsung">Diambil Langsung</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3" id="jenis_driver" style="display: none;">
                        <label id="labelDriver">Pilih Driver</label>
                        <select id="pengirim" name="pengirim" class="form-select" required>
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
                    <div class="mb-3" id="jenis_ekspedisi" style="display: none;">
                        <div class="mb-3">
                            <label id="labelEkspedisi">Pilih Ekspedisi</label>
                            <select id="ekspedisi" name="ekspedisi" class="form-select selectize-js" required>
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
                            <input type="text" class="form-control" name="resi" id="resi">
                        </div>
                        <div class="mb-3">
                            <label id="labelJenisOngkir">Jenis Ongkir</label>
                            <select id="jenis_ongkir" name="jenis_ongkir" class="form-select">
                                <option>Pilih</option>
                                <option value="0">Non COD</option>
                                <option value="1">COD</option>
                            </select>
                        </div>
                        <div class="mb-3" id="ongkir" style="display: block;">
                            <div class="row">
                                <label>Nominal Ongkir</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" style="background-color: #f8f9fa;"
                                        name="ongkir" id="ongkos_kirim" readonly>
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" name="free_ongkir" type="checkbox" value="1"
                                            id="free_ongkir">
                                        <label class="form-check-label" for="free_ongkir" id="free_ongkir_label">
                                            Free Ongkir
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label id="labelDikirimOleh">Dikirim Oleh</label>
                            <input type="text" class="form-control" name="dikirim" id="dikirim_oleh">
                        </div>
                        <div class="mb-3">
                            <label id="labelPj">Penanggung Jawab</label>
                            <input type="text" class="form-control" name="pj" id="penanggung_jawab">
                        </div>
                    </div>
                    <div class="mb-3" id="jenis_diambil" style="display: none;">
                        <label id="labelDiambil">Diambil Oleh</label>
                        <input type="text" name="diambil_oleh" id="diambil" class="form-control">
                    </div>
                    <div class="mb-3" id="alasanCancel" style="display: none;">
                        <label id="labelResi">Alasan Cancel</label>
                        <input type="text" class="form-control" name="alasan_cancel" id="alasan_cancel">
                    </div>
                    <div class="mb-3" id="tanggal" style="display: none;">
                        <label id="labelDate">Tanggal</label>
                        <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl"
                            id="date" required>
                    </div>
                    <div class="mb-3" id="buktiTerima" style="display: none;">
                        <div class="preview-image mb-3">
                            <img id="imagePreviewAdd" src="#" alt="Preview Image" style="display:none;">
                            <p id="imageSizeAdd" style="display:none;"></p>
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <div class="fileUpload btn btn-primary">
                                    <span id="uploadButtonTextAdd"><i class="bi bi-upload"></i> Upload Bukti Terima</span>
                                    <input class="upload" type="file" name="fileku" id="formFileAdd" accept=".jpg, .png, .jpeg" onchange="compressImage(event, 'Add')">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            id="cancelDikirim">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="ubah-status"> Ubah Status</button>
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
                    var dikirim = document.getElementById('dikirim');
                    var selesai = document.getElementById('selesai');
                    var cancel = document.getElementById('cancel');
                    var trxKirim = document.getElementById('trxKirim');
                    var jenisPengiriman = document.getElementById('jenis_pengiriman');
                    var jenisEkspedisi = document.getElementById('jenis_ekspedisi');
                    var jenisDriver = document.getElementById('jenis_driver');
                    var jenisDiambil = document.getElementById('jenis_diambil');
                    var ekspedisiSelectize = document.getElementById('ekspedisi-selectized');
                    var pengirim = document.getElementById('pengirim');
                    var resi = document.getElementById('resi');
                    var jenis_ongkir = document.getElementById('jenis_ongkir');
                    var bukti = document.getElementById('buktiTerima');
                    var diambil = document.getElementById('diambil');
                    var alasanCancel = document.getElementById('alasan_cancel');
                    var tanggal = document.getElementById('tanggal');
                    var ongkos_kirim = document.getElementById('ongkos_kirim');
                    var freeOngkirCheckbox = document.getElementById('free_ongkir');
                    var freeOngkirLabel = document.getElementById('free_ongkir_label');
                    var penanggung_jawab = document.getElementById('penanggung_jawab');
                    var dikirim_oleh = document.getElementById('dikirim_oleh');
                    var imgPreview = document.getElementById('imagePreviewAdd');
                    var fileku = document.getElementById('formFileAdd');

                    dikirim.addEventListener('change', updateVisibility);
                    selesai.addEventListener('change', updateVisibility);
                    cancel.addEventListener('change', updateVisibility);

                    function updateVisibility() {
                        if (dikirim.checked) {
                            trxKirim.style.display = 'block';
                            jenisPengiriman.style.display = 'block';
                            jenisPengiriman.setAttribute('required', 'true');
                            tanggal.style.display = 'none';
                            alasanCancel.style.display = 'none';
                            alasanCancel.removeAttribute('required');
                            jenisDiambil.style.display = 'none';
                        } else if (selesai.checked) {
                            trxKirim.style.display = 'none';
                            jenisPengiriman.style.display = 'none';
                            jenisPengiriman.value = '';
                            jenisDriver.style.display = 'none';
                            jenisEkspedisi.style.display = 'none';
                            diambil.value = '';
                            diambil.removeAttribute('required');
                            imgPreview.src = '#';
                            imgPreview.style.display = 'none';
                            fileku.value = '';
                            fileku.removeAttribute('required');
                            bukti.style.display = 'none';
                            tanggal.style.display = 'block';
                            alasanCancel.style.display = 'none';
                            alasanCancel.removeAttribute('required');
                            jenisPengiriman.removeAttribute('required');
                            ekspedisiSelectize.removeAttribute('required');
                            pengirim.removeAttribute('required');
                            jenisDiambil.style.display = 'none';
                        } else if (cancel.checked) {
                            trxKirim.style.display = 'none';
                            jenisPengiriman.style.display = 'none';
                            jenisPengiriman.value = '';
                            jenisDriver.style.display = 'none';
                            jenisEkspedisi.style.display = 'none';
                            diambil.value = '';
                            diambil.removeAttribute('required');
                            imgPreview.src = '#';
                            imgPreview.style.display = 'none';
                            fileku.value = '';
                            fileku.removeAttribute('required');
                            bukti.style.display = 'none';
                            tanggal.style.display = 'block';
                            alasanCancel.style.display = 'block';
                            alasanCancel.setAttribute('required', 'true');
                            jenisPengiriman.removeAttribute('required');
                            ekspedisiSelectize.removeAttribute('required');
                            pengirim.removeAttribute('required');
                            jenisDiambil.style.display = 'none';
                        }
                    }
                    jenisPengiriman.addEventListener('change', function() {
                        if (this.value === 'Driver') {
                            jenisDriver.style.display = 'block';
                            jenisEkspedisi.style.display = 'none';
                            jenisDiambil.style.display = 'none';
                            ekspedisi.selectize.clear();
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
                            imgPreview.src = '#';
                            imgPreview.style.display = 'none';
                            fileku.value = '';
                            fileku.removeAttribute('required');
                            bukti.style.display = 'none';
                            tanggal.removeAttribute('required');
                            tanggal.style.display = 'block';
                            pengirim.style.display = 'block';
                            pengirim.setAttribute('required', 'true');
                        } else if (this.value === 'Ekspedisi') {
                            jenisEkspedisi.style.display = 'block';
                            jenisDriver.style.display = 'none';
                            pengirim.value = '';
                            pengirim.removeAttribute('required');
                            ekspedisiSelectize.setAttribute('required', 'true');
                            resi.setAttribute('required', 'true');
                            jenis_ongkir.setAttribute('required', 'true');
                            tanggal.removeAttribute('required');
                            tanggal.style.display = 'block';
                            ongkos_kirim.removeAttribute('required');
                            penanggung_jawab.removeAttribute('required');
                            jenisDiambil.style.display = 'none';
                            diambil.value = '';
                            diambil.removeAttribute('required');
                            bukti.style.display = 'block';
                            dikirim_oleh.removeAttribute('required');
                            fileku.setAttribute('required', 'true');
                        } else if (this.value === 'Diambil Langsung') {
                            jenisDriver.style.display = 'none';
                            jenisDiambil.style.display = 'block';
                            diambil.setAttribute('required', 'true');
                            pengirim.removeAttribute('required');
                            jenisEkspedisi.style.display = 'none';
                            ekspedisi.selectize.clear();
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
                });
                flatpickr("#date", {
                    dateFormat: "d/m/Y",
                    defaultDate: "today"
                });
            </script>
        </div>
    </div>
</div>