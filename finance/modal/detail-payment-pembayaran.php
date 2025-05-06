<!-- Modal Bayar -->
<div class="modal fade" id="sudahBayar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <style>
            .img-bank{
                height: 60px;
                width: 140px;
            }
        </style>
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Form Pembayaran <?php echo $nama_sp ?></h1>
            </div>
            <div class="modal-body">
                <form action="proses/bayar-pembelian-lokal.php" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <?php 
                            include "../function/uuid.php"; 
                            date_default_timezone_set('Asia/Jakarta');
                            $uuid = uuid();
                            $day = date('d');
                            $month = date('m');
                            $year = date('y');
                            $id_bayar = "BYR" . $year . "" . $month . "" . $uuid . "" . $day;
                            $id_bukti = "TF" . $year . "" . $month . "" . $uuid . "" . $day;
                            $id_bank_sp = "BANK-SP" . $year . "" . $month . "" . $uuid . "" . $day;
                        ?>
                        <input type="hidden" name="id_bayar" value="<?php echo $id_bayar ?>">
                        <input type="hidden" name="id_sp" value="<?php echo $id_sp ?>">
                        <input type="hidden" id="id_inv" name="id_inv">
                        <input type="hidden" id="jenis_inv" name="jenis_inv">
                        <input type="hidden" name="id_pembayaran" value="<?php echo $id_pembayaran ?>">
                        <div class="mb-3">
                            <label>Total Tagihan</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Rp</span>
                                <input type="text" class="form-control" name="total_tagihan"  id="total_tagihan"  readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Metode Pembayaran :</label>
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
                                <input type="hidden" name="id_bukti" value="<?php echo $id_bukti ?>">
                                <input type="hidden" name="id_bank_sp" value="<?php echo $id_bank_sp ?>">
                                <label>Pilih Bank :</label>
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
                                <label>Nama Penerima(*)</label>
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
                                <label>Rekening Penerima(*)</label>
                                <input type="number" class="form-control" name="rek_pengirim" id="rek_pengirim">
                            </div>
                            <div class="mb-3">
                                <label>Bank Penerima(*)</label>
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
                                <label>Bukti Transfer :</label>
                            </div>
                            <div class="mb-3">
                                <input type="file" name="fileku1" id="fileku1" accept=".jpg, .jpeg, .png" onchange="compressAndPreviewImage(event)">
                            </div>
                            <div class="mb-3 preview-image" id="imagePreview"></div>
                        </div>
                        <div id="nominalDisplay" style="display: none">
                            <div id="date-picker-wrapper" class="mb-3">
                                <label>Tanggal Bayar</label>
                                <input type="text" class="form-control" name="tgl_bayar" id="date">
                            </div>
                            <div class="mb-3">
                                <label>Keterangan Bayar(*)</label>
                                <input type="text" class="form-control" name="keterangan_bayar">
                            </div>
                            <div class="mb-3">
                                <label>Nominal</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                    <input type="text" class="form-control" name="nominal" id="nominal" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Sisa Tagihan</label>
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
                </form>
                <p><b>NB: Jika data Bank kosong maka button simpan tidak dapat digunakan</b></p>
            </div>
        </div>
    </div>
    <script>
        function checkRadio() {
            var idBankRadios = document.getElementsByName("id_bank_pt");
            var transferCheck = document.getElementById('transfer');
            var cashCheck = document.getElementById('cash');
            var namaPengirim = document.getElementById('nama_pengirim');
            var rekPengirim = document.getElementById('rek_pengirim');
            var bankPengirim = document.getElementById('bank_pengirim');
            var fileku = document.getElementById('fileku1');
            var metode = document.getElementById('metode');
            var nominalDisplay = document.getElementById('nominalDisplay');
            var totalTagihanInput = document.getElementById('total_tagihan');
            var sisaTagihanInput = document.getElementById('sisa_tagihan');
            var tombolSimpan = document.querySelector('button[name="simpan-pembayaran"]');
            var nominalInput = document.getElementById('nominal');

            // Jika "cash" dicentang, set status checked pada setiap "id_bank_pt" menjadi false
            if (cashCheck.checked) {
                // console.log("Cash selected, clearing id_bank_pt selection.");
                for (var i = 0; i < idBankRadios.length; i++) {
                    idBankRadios[i].checked = false;
                    idBankRadios[i].required = false;
                }
                metode.style.display = 'none';
                nominalDisplay.style.display = 'block';
                namaPengirim.value = '';
                // namaPengirim.removeAttribute('required');
                rekPengirim.value = '';
                // rekPengirim.removeAttribute('required');
                bankPengirim.value = '';
                // bankPengirim.removeAttribute('required');
                sisaTagihanInput.value = totalTagihanInput.value;
                fileku.removeAttribute('required');
                if (parseFloat(sisaTagihanInput.value) < 0) {
                    tombolSimpan.disabled = true;
                }
            }

            // Jika "transfer" dicentang, jalankan loop untuk mengecek status setiap "id_bank_pt"
            else if (transferCheck.checked) {
                // console.log("Transfer selected!");
                for (var i = 0; i < idBankRadios.length; i++) {
                    console.log("id_bank_pt" + (i + 1) + " checked:", idBankRadios[i].checked);
                    idBankRadios[i].required = true;
                }
                metode.style.display = 'block';
                nominalDisplay.style.display = 'block';
                sisaTagihanInput.value = totalTagihanInput.value; // Set nilai sisa tagihan sama dengan total tagihan
                // namaPengirim.setAttribute('required', 'true'); // Membuat Atribut Required
                // rekPengirim.setAttribute('required', 'true'); // Membuat Atribut Required
                fileku.setAttribute('required', 'true'); // Membuat Atribut Required
                // Menonaktifkan tombol "Simpan" jika nilai sisa tagihan negatif
                if (parseFloat(sisaTagihanInput.value) < 0) {
                    tombolSimpan.disabled = true;
                }
                nominalInput.value = ''; // Reset nilai input nominal saat beralih ke opsi "Transfer"
            }
        }
    </script>

    <script>
        // Get the input elements
        const totalTagihanInput = document.getElementById("total_tagihan");
        const nominalInput = document.getElementById("nominal");
        const sisaTagihanInput = document.getElementById("sisa_tagihan");
        const btnTutup = document.getElementById("btnTutup");

        // Function to format number with Indonesian format
        function formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(number);
        }

        // Function to parse Indonesian formatted number into a valid number
        function parseNumber(str) {
        const parsedValue = parseFloat(str.replace(/\./g, "").replace(",", "."));
        return isNaN(parsedValue) ? 0 : parsedValue;
        }

        // Function to update input "nominal" with formatted number
        function formatInputNominal() {
        let value = nominalInput.value;
        value = value.replace(/\./g, ""); // Remove all dots as thousand separators
        value = value.replace(/,/g, "."); // Replace comma with dot as decimal separator
        nominalInput.value = formatNumber(value);
        }

        // Function to perform subtraction and update the result
        function calculateSisaTagihan() {
        const totalTagihan = parseNumber(totalTagihanInput.value);
        let nominal = parseNumber(nominalInput.value);

        // Ensure nominal does not exceed totalTagihan
        if (nominal > totalTagihan) {
            nominal = totalTagihan;
            nominalInput.value = formatNumber(nominal);
        }

        const sisaTagihan = totalTagihan - nominal;

        // Update the "sisa_tagihan" input value with the result formatted in Indonesian format
        sisaTagihanInput.value = formatNumber(sisaTagihan);
        }

        // Function to reload modal content
        btnTutup.addEventListener("click", () => {
        location.reload(); // Reload the page
        });

        // Function to attach event listener to the "nominal" input
        function initializeModal() {
        // Attach event listener to the "nominal" input to trigger calculation and format on input change
        nominalInput.addEventListener("input", () => {
            // Get the input value
            let inputValue = nominalInput.value;

            // Remove non-numeric characters using a regular expression
            inputValue = inputValue.replace(/[^\d]/g, "");

            // Update the input value with the sanitized value
            nominalInput.value = inputValue;

            // Format input "nominal"
            formatInputNominal();

            // Perform calculation
            calculateSisaTagihan();
        });
        }

        // Initialize modal when the script is loaded
        initializeModal();
    </script>
    <!-- Untuk Menampilkan Data Bayar -->
    <script>
        // Wait for the DOM to be ready
        document.addEventListener('DOMContentLoaded', function () {
            // Get all buttons with the data-bs-target attribute equal to #sudahBayar
            var totalButtons = document.querySelectorAll('[data-bs-target="#sudahBayar"]');

            // Iterate through each button and attach an event listener
            totalButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    // Get the data-id attribute value from the clicked button
                    var idInv = button.getAttribute('data-id');
                    var jenisInv = button.getAttribute('data-jenis');
                    var totalInv = button.getAttribute('data-total');

                    // Set the value of the id_inv input field in the modal
                    document.getElementById('id_inv').value = idInv;
                    document.getElementById('jenis_inv').value = jenisInv;
                    document.getElementById('total_tagihan').value = totalInv;
                });
            });
        });
    </script>
<!-- End Untuk Menampilkan Data Bayar -->
</div>
<!-- End Modal Bayar -->

<!-- Modal Pilih Rekening SP -->
<div class="modal fade" id="pilihRek" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Pilih Rekening Supplier</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="table2">
                    <thead>
                        <tr class="text-white" style="background-color: navy;">
                            <th class="text-center text-nowrap p-3" style="width: 100px;">No</th>
                            <th class="text-center text-nowrap p-3" style="width: 350px;">Nama Customer</th>
                            <th class="text-center text-nowrap p-3" style="width: 150px;">Nama Bank</th>
                            <th class="text-center text-nowrap p-3" style="width: 250px;">No. Rekening</th>
                            <th class="text-center text-nowrap p-3" style="width: 350px;">Atas Nama</th>
                            <th class="text-center text-nowrap p-3" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  
                            include "koneksi.php";
                            $no = 1;
                            $sql_bank = "SELECT 
                                            spb.id_bank_sp, spb.id_bank, spb.no_rekening, spb.atas_nama,
                                            bk.nama_bank, sp.nama_sp
                                        FROM bank_sp AS spb
                                        LEFT JOIN bank bk ON (spb.id_bank = bk.id_bank)
                                        LEFT JOIN tb_supplier sp ON (sp.id_sp = spb.id_sp)
                                        WHERE sp.id_sp = '$id_sp'
                                        ORDER BY sp.nama_sp ASC";
                            $query_bank = mysqli_query($connect, $sql_bank);
                            while($data_bank = mysqli_fetch_array($query_bank)){
                                $id_bank_sp = $data_bank['id_bank_sp'];
                        ?>
                        <tr>
                            <td class="text-nowrap text-center"><?php echo $no; ?></td>
                            <td class="text-nowrap"><?php echo $data_bank['nama_sp'] ?></td>
                            <td class="text-nowrap text-center"><?php echo $data_bank['nama_bank'] ?></td>
                            <td class="text-nowrap text-center"><?php echo $data_bank['no_rekening'] ?></td>
                            <td class="text-nowrap"><?php echo $data_bank['atas_nama'] ?></td>
                            <td class="text-nowrap text-center">
                                <button type="button" id="pilih" class="btn btn-primary btn-sm" data-id="<?php echo $id_bank_sp ?>" data-id-bank="<?php echo $data_bank['id_bank'] ?>"  data-bank="<?php echo $data_bank['nama_bank'] ?>" data-rek="<?php echo $data_bank['no_rekening'] ?>" data-an="<?php echo $data_bank['atas_nama'] ?>">
                                    Pilih
                                </button>
                            </td>
                        </tr>
                        <?php $no++ ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Script untuk popup ganda tanpa close popup awal  -->
    <script>
        $('#cekRek').on('show.bs.modal', function () {
            $('#sudahBayar').modal('hide');
        });

        $('#pilihRek').on('show.bs.modal', function () {
            // Check if sudahBayar is open, if yes, hide it
            if ($('#sudahBayar').hasClass('show')) {
                $('#sudahBayar').modal('hide');
            }
        });

        $('#sudahBayar').on('hide.bs.modal', function (e) {
            // Prevent #sudahBayar from closing
            e.preventDefault();
        });

        $('#pilihRek').on('hidden.bs.modal', function () {
            // Show sudahBayar when pilihRek is hidden
            $('#sudahBayar').modal('show');
        });
    
        document.getElementById('btnTutup').addEventListener('click', function() {
        // Reload halaman
        location.reload();
        });

        // select data bank CS
        $(document).on('click', '#pilih', function (e) {
            var atasNama = $(this).data('an');
            var noRek = $(this).data('rek');
            var idBank = $(this).data('id-bank');
            var namaBank = $(this).data('bank');
            // Trigger event input setelah mengubah nilai
            $('#nama_pengirim').val(atasNama).trigger('input'); 
            $('#rek_pengirim').val(noRek).trigger('input'); 
            $('#id_bank_pengirim').val(idBank).trigger('input'); 
            $('#bank_pengirim').val(namaBank).trigger('input'); 

            // Memeriksa nilai elemen input setelah diatur
            var namaPengirimValue = $('#nama_pengirim').val();
            var rekPengirimValue = $('#rek_pengirim').val();
            var idBankPengirimValue = $('#id_bank_pengirim').val();
            var bankPengirimValue = $('#bank_pengirim').val();

            if (namaPengirimValue && rekPengirimValue && bankPengirimValue) {
                // Jika semua nilai ada, ubah display menjadi block
                $('#bank_pengirim').css('display', 'block');

                $('#reset').css('display', 'block');
                
                // Sembunyikan elemen <div> dengan id "selectData"
                $('#selectData').css('display', 'none');

                $('#cari').css('display', 'none');

                // console.log("Nilai input nama_pengirim:", namaPengirimValue);
                // console.log("Nilai input rek_pengirim:", rekPengirimValue);
                // console.log("Nilai input bank_pengirim:", bankPengirimValue);
            } else {
                // Jika salah satu atau lebih nilai tidak ada, ubah display menjadi none
                $('#bank_pengirim').css('display', 'none');
                $('#reset').css('display', 'none');
                
                // Tampilkan kembali elemen <div> dengan id "selectData"
                $('#selectData').css('display', 'block');
                $('#cari').css('display', 'block');

                // console.log("Salah satu atau lebih input tidak memiliki nilai.");
            }

            $('#pilihRek').modal('hide');
        });

        // Reset Button
        $(document).on('click', '#reset', function (e) {
            // Mengosongkan nilai input
            $('#nama_pengirim').val('').trigger('input');
            $('#rek_pengirim').val('').trigger('input');
            $('#id_bank_pengirim').val('').trigger('input');
            $('#bank_pengirim').val('').trigger('input');

            // Menampilkan kembali elemen <div> dengan id "selectData"
            $('#selectData').css('display', 'block');
            $('#cari').css('display', 'block');

            // Sembunyikan elemen <div> dengan id "bank_pengirim"
            $('#bank_pengirim').css('display', 'none');
            $('#reset').css('display', 'none');

            // console.log("Nilai input nama_pengirim:", $('#nama_pengirim').val());
            // console.log("Nilai input rek_pengirim:", $('#rek_pengirim').val());
            // console.log("Nilai input bank_pengirim:", $('#bank_pengirim').val());
        });
    </script>
    <!-- End SCript -->
</div>
<!-- End Pilih Rek SP -->

<!-- kode JS Dikirim -->
<?php include "../page/upload-img.php";  ?>
<style>
    .preview-image {
        max-width: 100%;
        height: auto;
    }
</style>
<!-- kode JS Dikirim -->


