 <?php  
    require_once '../akses.php';
 ?>
 <!-- Modal Bayar -->
 <div class="modal fade" id="sudahBayar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <style>
            .img-bank{
                height: 60px;
                width: 140px;
            }
        </style>
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Form Pembayaran <?php echo $nama_cs ?></h1>
            </div>
            <div class="modal-body">
                <form action="proses/bayar.php" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <?php  
                            $uuid = uuid();
                            $day = date('d');
                            $month = date('m');
                            $year = date('y');
                            $id_bayar = "BYR" . $year . "" . $month . "" . $uuid . "" . $day ;
                        ?>
                        <input type="hidden" class="form-control" name="nonce_token" value="<?php echo $_SESSION['nonce_token']; ?>">
                        <input type="hidden" class="form-control" name="id_bayar" value="<?php echo encrypt($id_bayar, $key_finance)?>">
                        <input type="hidden" class="form-control" name="id_cs" value="<?php echo encrypt($id_customer, $key_finance) ?>">
                        <input type="hidden" class="form-control" id="id_inv" name="id_inv">
                        <input type="hidden" class="form-control" name="id_bill" value="<?php echo encrypt($id_bill, $key_finance) ?>">
                        <input type="hidden" class="form-control" name="id_finance" id="id_finance">
                        <input type="hidden" class="form-control" id="jenis_inv" name="jenis_inv">
                        <div class="mb-3">
                            <label class="fw-bold mb-1">Tujuan Pembayaran</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tujuan_pembayaran" id="inlineRadio1" value="0" required>
                                <label class="form-check-label" for="inlineRadio1">Pembayaran Transaksi</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tujuan_pembayaran" id="inlineRadio2" value="1" required>
                                <label class="form-check-label" for="inlineRadio2">Pembayaran Cashback</label>
                            </div>
                        </div>
                        <div class="d-none" id="bayar_trx">
                            <div class="mb-3">
                                <label class="fw-bold">Metode Pembayaran :</label>
                                <div class="row">
                                    <div class="col">
                                        <div class="input-group">
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" type="radio" id="cash" name="metode_pembayaran" value="cash" onclick="checkRadio()">
                                            </div>
                                            <input type="text" class="form-control" value="Cash" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group">
                                            <div class="input-group-text">
                                                <input class="form-check-input mt-0" type="radio" id="transfer" name="metode_pembayaran" value="transfer" onclick="checkRadio()">
                                            </div>
                                            <input type="text" class="form-control" value="Transfer" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="metode" style="display: none;">
                                <div class="mb-3">
                                    <label class="fw-bold">Pilih Bank :</label>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between flex-wrap p-3">
                                            <?php  
                                                include "koneksi.php";
                                                $no = 1;
                                                $sql_bank ="SELECT 
                                                                bt.id_bank_pt,
                                                                bt.no_rekening,
                                                                bt.atas_nama,
                                                                bk.nama_bank,
                                                                bk.logo
                                                            FROM bank_pt AS bt
                                                            LEFT JOIN bank bk ON (bk.id_bank = bt.id_bank)
                                                            ORDER BY nama_bank ASC";
                                                $query_bank = mysqli_query($connect, $sql_bank);
                                                $total_data_bank = mysqli_num_rows($query_bank);
                                                while($data_bank = mysqli_fetch_array($query_bank)){
                                                    $no_rek = $data_bank['no_rekening'];
                                                    $atas_nama = $data_bank['atas_nama'];
                                                    $logo = $data_bank['logo'];
                                                    $logo_img = "logo-bank/$logo";
                                                ?>
                                                <div class="card" style="width: 20rem;">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-10">
                                                                <img src="<?php echo $logo_img ?>" class="img-bank" alt="...">
                                                            </div>
                                                            <div class="col-2 text-end">
                                                                <input class="form-check-input mt-3" type="radio" id="id_bank_<?php echo $data_bank['id_bank_pt']; ?>" name="id_bank_pt" value="<?php echo $data_bank['id_bank_pt']; ?>">
                                                            </div>
                                                        </div>
                                                        <p class="card-text">
                                                            <?php echo $no_rek; ?><br>
                                                            <b><?php echo $atas_nama ?></b>
                                                        </p>
                                                    </div>
                                                </div> 
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">Nama Pengirim(*)</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name="nama_pengirim" id="nama_pengirim">
                                        <button class="btn btn-primary" type="button" id="cari" data-bs-toggle="modal" data-bs-target="#pilihRek" style="display: block;">
                                            <i class="bi bi-search"></i>
                                        </button>
                                        <button class="btn btn-danger" id="reset" type="button" style="display: none;">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">Rekening Pengirim(*)</label>
                                    <input type="number" class="form-control" name="rek_pengirim" id="rek_pengirim">
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">Bank Pengirim(*)</label>
                                    <input type="hidden" class="form-control bg-light" name="id_bank_pengirim" id="id_bank_pengirim">
                                    <input type="text" class="form-control bg-light" name="bank_pengirim" id="bank_pengirim" style="display: none;">
                                </div>
                                <div id="selectData" class="mb-3" style="display: block;">
                                    <select name="id_bank_select" class="form-select selectize-js">
                                        <option value=""></option>
                                        <?php  
                                            $sql_bank = "SELECT id_bank, nama_bank FROM bank ORDER BY nama_bank ASC";
                                            $query_bank = mysqli_query($connect, $sql_bank);
                                            while($data_bank = mysqli_fetch_array($query_bank)){
                                                $id_bank = $data_bank['id_bank'];
                                                $nama_bank = $data_bank['nama_bank'];
                                        ?>
                                            <option value="<?php echo $id_bank ?>"><?php echo $nama_bank ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="fw-bold">Bukti Transfer :</label>
                                </div>
                                <div class="mb-3">
                                    <input type="file" name="fileku1" id="fileku1" accept=".jpg, .jpeg, .png" onchange="compressAndPreviewImage(event)">
                                    <br>
                                    <small><b>Format yang di izinkan :</b> .jpg, .jpeg, .png</small>
                                </div>
                                <div class="mb-3 preview-image" id="imagePreview"></div>
                            </div>
                            <div id="nominalDisplay" style="display: none">
                                <div id="date-picker-wrapper" class="mb-3">
                                    <label class="fw-bold">Tanggal Bayar</label>
                                    <input type="text" class="form-control" name="tgl_bayar" id="date">
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">Keterangan Bayar(*)</label>
                                    <input type="text" class="form-control" name="keterangan_bayar">
                                </div>
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">No. Invoice</label>
                                            <input type="text" class="form-control bg-light" id="no_inv" readonly>     
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="fw-bold">Total Tagihan</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">Rp</span>
                                                <input type="text" class="form-control bg-light" name="total_tagihan" id="total_tagihan" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold mb-1">Pilih Jenis Pembayaran</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="pilih_nominal" id="inlineRadio1" value="0" required>
                                        <label class="form-check-label" for="inlineRadio1">Full</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="pilih_nominal" id="inlineRadio2" value="1" required>
                                        <label class="form-check-label" for="inlineRadio2">Custom</label>
                                    </div>
                                </div>
                                <div class="d-none" id="status_potongan_cb">
                                    <div class="mb-3">
                                        <label class="fw-bold" mb-1>Status Potongan Cashback</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status_potongan" id="statusPotongan" value="0" required>
                                            <label class="form-check-label">Ada</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status_potongan" id="statusPotongan" value="1" required>
                                            <label class="form-check-label">Tidak Ada</label>
                                        </div>
                                    </div>
                                    <div class="d-none" id="potongan">
                                        <div class="row">
                                            <div class="col-sm-4 mb-3">
                                                <label class="fw-bold">Jumlah Potongan</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control text-end" name="jumlah_potongan" id="jumlah_potongan" oninput="formatDiscount(this)" required>
                                                    <span class="input-group-text" id="basic-addon1">%</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-8 mb-3">
                                                <label class="fw-bold">Nominal Potongan</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                                    <input type="text" class="form-control" name="nominal_potongan" id="nominal_potongan" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">Nominal Pembayaran</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">Rp</span>
                                        <input type="text" class="form-control" name="nominal_bayar" id="nominal" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">Sisa Tagihan</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">Rp</span>
                                        <input type="text" class="form-control" name="sisa_tagihan" id="sisa_tagihan" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnTutup">Tutup</button>
                                    <?php  
                                        if($total_data_bank != 0){
                                            ?>
                                                <button type="submit" class="btn btn-primary" name="simpan-pembayaran">Simpan</button>
                                            <?php
                                        }else{
                                            ?>
                                                <button type="submit" class="btn btn-primary" name="simpan-pembayaran" disabled>Simpan</button>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="d-none" id="bayar_cb">
                            <div class="mb-3">
                                <label>Jenis Cashback</label>
                                <input type="text" class="form-control bg-light" readonly>
                            </div>
                        </div>    
                    </div>
                </form>
                <p><b>NB: Jika data Bank kosong maka button simpan tidak dapat digunakan</b></p>
            </div>
        </div>
    </div>
    <!-- jquery 3.6.3 -->
    <script src="../assets/js/jquery.min.js"></script>  
    <script src="../function-js/format-diskon.js"></script>  
    <script>
        $(document).ready(function () {
            $('input[name="tujuan_pembayaran"]').on('change', function () {
                if ($(this).val() === "0") {
                    $('#bayar_trx').removeClass('d-none'); // Hapus class d-none
                    $('#bayar_cb').addClass('d-none'); // Tambahkan class d-none
                } else {
                    $('#bayar_trx').addClass('d-none'); // Tambahkan class d-none
                    $('#bayar_cb').removeClass('d-none'); // Hapus class d-none
                }
            });

            $('input[name="pilih_nominal"]').on('change', function () {
                if ($(this).val() === "0") {
                    $('#status_potongan_cb').removeClass('d-none'); // Hapus class d-none
                    $('input[name="status_potongan"]').prop('required', true); // Tambahkan required
                    $('input[name="jumlah_potongan"]').prop('required', true); // Tambahkan required
                    $('input[name="status_potongan"][value="1"]').prop('checked', false); // Set radio button 'Tidak Ada' menjadi checked
                } else {
                    $('#status_potongan_cb').addClass('d-none'); // Tambahkan class d-none
                    $('input[name="status_potongan"]').prop('required', false); // Hapus required
                    $('input[name="jumlah_potongan"]').prop('required', false); // Hapus required
                    $('#jumlah_potongan').val(''); // Hapus nilai pada input
                    $('#nominal_potongan').val(''); // Hapus nilai pada input
                    $('input[name="status_potongan"][value="1"]').prop('checked', true); // Set radio button 'Tidak Ada' menjadi checked
                }
            });


            $('input[name="status_potongan"]').on('change', function () {
                if ($(this).val() === "0") {
                    $('#potongan').removeClass('d-none'); // Hapus class d-none
                } else {
                    $('#potongan').addClass('d-none'); // Tambahkan class d-none
                    $('#jumlah_potongan').val(''); // Hapus nilai pada input
                    $('#nominal_potongan').val(''); // Hapus nilai pada input
                }
            });
        });

        function checkRadio() {
            var $idBankRadios = $('input[name="id_bank_pt"]');
            var $transferCheck = $('#transfer');
            var $cashCheck = $('#cash');
            var $namaPengirim = $('#nama_pengirim');
            var $rekPengirim = $('#rek_pengirim');
            var $bankPengirim = $('#bank_pengirim');
            var $fileku = $('#fileku1');
            var $metode = $('#metode');
            var $nominalDisplay = $('#nominalDisplay');
            var $totalTagihanInput = $('#total_tagihan');
            var $sisaTagihanInput = $('#sisa_tagihan');
            var $tombolSimpan = $('button[name="simpan-pembayaran"]');
            var $nominalInput = $('#nominal');

            if ($cashCheck.is(':checked')) {
                $idBankRadios.prop('checked', false).prop('required', false);
                $metode.hide();
                $nominalDisplay.show();
                $namaPengirim.val('');
                $rekPengirim.val('');
                $bankPengirim.val('');
                $fileku.removeAttr('required');
                $sisaTagihanInput.val($totalTagihanInput.val());
                if (parseFloat($sisaTagihanInput.val()) < 0) {
                    $tombolSimpan.prop('disabled', true);
                }
            } else if ($transferCheck.is(':checked')) {
                $idBankRadios.prop('required', true);
                $metode.show();
                $nominalDisplay.show();
                $fileku.prop('required', true);
                $sisaTagihanInput.val($totalTagihanInput.val());
                if (parseFloat($sisaTagihanInput.val()) < 0) {
                    $tombolSimpan.prop('disabled', true);
                }
                $nominalInput.val('');
            }
        }

        // Fungsi untuk memformat angka dalam format ID
        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Fungsi untuk mengonversi string menjadi angka
        function parseNumber(str) {
            const parsedValue = parseFloat(str.replace(/\./g, "").replace(",", "."));
            return isNaN(parsedValue) ? 0 : parsedValue;
        }

        // Fungsi untuk menghitung nominal potongan dan sisa tagihan
        function calculateNominalPotongan() {
            const totalTagihan = parseNumber($('#total_tagihan').val()); // Ambil total tagihan
            let potonganPersen = $('#jumlah_potongan').val(); // Ambil persen potongan

            let nominalPotongan = 0;

            // Jika ada nilai pada jumlah_potongan, hitung nominal potongan
            if (potonganPersen > 0) {
                nominalPotongan = (potonganPersen / 100) * totalTagihan;
                $('#nominal_potongan').val(formatNumber(nominalPotongan)); // Tampilkan nominal potongan
            } else {
                $('#nominal_potongan').val(''); // Kosongkan jika tidak ada potongan
            }

            // Ambil nilai nominal yang dimasukkan
            const nominal = parseNumber($('#nominal').val()); // Ambil nilai nominal

            // Hitung sisa tagihan
            calculateSisaTagihan(totalTagihan, nominalPotongan, nominal);
        }

        // Fungsi untuk menghitung sisa tagihan
        function calculateSisaTagihan(totalTagihan, nominalPotongan, nominal) {
            const sisaTagihan = totalTagihan - nominalPotongan - nominal; // Rumus total_tagihan - nominal_potongan - nominal
            $('#sisa_tagihan').val(formatNumber(sisaTagihan)); // Tampilkan sisa tagihan
        }

        $(document).ready(function () {
            // Event listener saat input jumlah potongan berubah
            $('#jumlah_potongan').on('input', function () { 
                // Hanya mengizinkan angka
                let inputPotongan = $(this).val(); // Hapus karakter non-angka
                $(this).val(inputPotongan); // Set nilai input
                calculateNominalPotongan(); // Hitung dan tampilkan nominal potongan dan sisa tagihan
            });

            // Fungsi untuk memformat input nominal agar memiliki pemisah ribuan
            $('#nominal').on('input', function () {
                let inputValue = $(this).val().replace(/[^\d,\.]/g, ""); // Mengizinkan angka, koma, dan titik
                let parsedValue = parseNumber(inputValue); // Konversi input menjadi angka
                
                // Ambil nilai sisa tagihan
                const totalTagihan = parseNumber($('#total_tagihan').val());
                const nominalPotongan = parseNumber($('#nominal_potongan').val());
                const sisaTagihan = totalTagihan - nominalPotongan ;

                // Jika input nominal melebihi sisa tagihan, batalkan input
                if (parsedValue > sisaTagihan) {
                    parsedValue = sisaTagihan; // Set nilai nominal ke sisa tagihan jika lebih besar
                }

                $(this).val(formatNumber(parsedValue)); // Tampilkan hasil format dengan pemisah ribuan
                calculateNominalPotongan(); // Hitung dan tampilkan nominal potongan dan sisa tagihan
            });

            // Inisialisasi jika sudah ada value yang terisi
            calculateNominalPotongan();
        });
    </script>

</div>
<!-- End Modal Bayar -->