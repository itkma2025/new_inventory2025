<?php
require_once "akses.php";
$page  = 'transaksi';
$page2 = 'spk';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">   
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <?php include "page/head.php"; ?>
</head>

<body>
    <!-- nav header -->
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include "page/sidebar.php"; ?>
    <!-- end sidebar -->

    <main id="main" class="main">
        <div class="container-fluid">
            <!-- Form simpan ke table invoice -->
            <div class="card">
                <div class="card-header text-center">
                    <h5>Form Invoice BUM</h5>
                </div>
                <div class="card-body">
                    <form action="proses/proses-invoice-bum.php" method="POST" id="myForm">
                        <?php
                        require_once "function/function-enkripsi.php";
                        require_once "function/uuid.php";
                        // Mendapatkan data dari form sebelumnya
                        if (isset($_POST['spk_id'])) {
                            $selectedSpkIds = $_POST['spk_id']; 

                            // Lakukan sesuatu dengan data yang dipilih
                            // Misalnya, tampilkan daftar ID SPK yang dipilih
                            foreach ($selectedSpkIds as $spkId) {
                                $spkId_decrypt = decrypt($spkId, $key_global);
                                echo '<input type="hidden" name="id_spk[]" value="' . $spkId . '">';
                                $sql = mysqli_query($connect, " SELECT sr.id_customer, sr.no_po, cs.nama_cs, cs.alamat
                                                                    FROM spk_reg AS sr
                                                                    JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                                                    WHERE id_spk_reg = '$spkId_decrypt'");
                                $data_spk = mysqli_fetch_array($sql);
                            }
                        }

                        // UUID
                        $uuid = uuid();
                        $year = date('y');
                        $day = date('d');
                        $month = date('m');

                        include "koneksi.php";
                        $thn  = date('Y');
                        $sql  = mysqli_query($connect, "SELECT 
                                                CAST(MAX(CAST(SUBSTRING_INDEX(no_inv, '/', 1) AS UNSIGNED)) AS CHAR) AS maxID,
                                                STR_TO_DATE(tgl_inv, '%d/%m/%Y') AS tgl 
                                                FROM 
                                                inv_bum 
                                                WHERE YEAR(STR_TO_DATE(tgl_inv, '%d/%m/%Y')) = '$thn'");
                        $data = mysqli_fetch_array($sql);

                        $array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
                        $kode = $data['maxID'];
                        $ket1 = "/BUM/";
                        $bln = $array_bln[date('n')];
                        $ket2 = "/";
                        $ket3 = date("Y");
                        $urutkan = $kode;
                        $urutkan++;
                        $no_inv = sprintf("%03s", $urutkan) . $ket1 . $bln . $ket2 . $ket3;

                        // Generate a unique nonce
                        $nonce_token = bin2hex(random_bytes(32));  // Menghasilkan token 64 karakter
                        $_SESSION['nonce_token'] = $nonce_token;  // Simpan token dalam session
                        ?>
                        <div class="row mt-3">
                            <div class="col-sm-6">
                                <input type="hidden" name="nonce_token" value="<?php echo $nonce_token; ?>">
                                <input type="hidden" name="id_inv_bum" value="BUM-<?php echo $year ?><?php echo $month ?><?php echo $uuid ?><?php echo $day ?>">
                                <input type="hidden" name="id_cb_bum" value="CB-BUM-<?php echo $year ?><?php echo $month ?><?php echo $uuid ?><?php echo $day ?>">
                                <div class="mt-3">
                                    <label><strong>No Invoice Nonppn</strong></label>
                                    <input type="text" class="form-control" name="no_inv_bum" value="<?php echo $no_inv ?>" readonly>
                                </div>
                                <div class="mt-3">
                                    <label><strong>Nama Pelanggan</strong></label>
                                    <input type="text" class="form-control bg-light" name="cs" value="<?php echo $data_spk['nama_cs']; ?>" required>
                                </div>
                                <div class="mt-3">
                                    <label><strong>Pelanggan Invoice</strong></label>
                                    <input type="text" class="form-control" name="cs_inv" value="<?php echo $data_spk['nama_cs']; ?>" required>
                                </div>
                                <div class="mt-3">
                                    <label><strong>Tanggal Invoice</strong></label>
                                    <input type="text" id="date" class="form-control" name="tgl_inv">
                                </div>
                                <div class="mt-3">
                                    <label><strong>Tanggal Tempo</strong></label>
                                    <div class="input-group flex-nowrap">
                                        <input type="text" id="tempo" class="form-control" name="tgl_tempo">
                                        <span class="input-group-text" id="clear-search"><i class="bi bi-x-circle"></i></span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label><strong>No. PO</strong></label>
                                    <input type="text" class="form-control" name="no_po" value="<?php echo $data_spk['no_po']; ?>">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mt-3">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <label><strong>Jenis Invoice</strong></label>
                                            <select name="jenis_inv" id="select" class="form-select" onchange="enabled()">
                                                <option value="Reguler">Reguler</option>
                                                <option value="Diskon">Diskon</option>
                                                <option value="Spesial Diskon">Spesial Diskon</option>
                                            </select>  
                                        </div>
                                        <div class="col-sm-4">
                                            <label><strong>Spesial Diskon</strong></label>
                                            <div class="input-group">
                                                <input type="text" id="sp_disc" name="sp_disc" value="0" class="form-control bg-light text-end" readonly>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3" id="status_cb">
                                    <label><strong>Status Cashback</strong></label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status_cb" id="inlineRadio1" value="1" required>
                                        <label class="form-check-label" for="inlineRadio1">Cashback</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status_cb" id="inlineRadio2" value="0" required>
                                        <label class="form-check-label" for="inlineRadio2">Non Cashback</label>
                                    </div>
                                </div>
                                <div class="mt-3 d-none" id="cashback-container">
                                    <label><strong>Jenis Cashback</strong></label><br>
                                    <?php  
                                        $sql_cb = $connect->query("SELECT id_ket_cashback, ket_cashback FROM keterangan_cashback ORDER BY created_date");
                                        while($data_cb = mysqli_fetch_array($sql_cb)){
                                            $id_ket_cashback = encrypt($data_cb['id_ket_cashback'], $key_global);
                                    ?>
                                        <div class="form-check me-3 d-block d-md-inline-block mr-md-3">
                                            <!-- Value diubah menjadi id_ket_cashback -->
                                            <input class="form-check-input" type="checkbox" name="cashback" value="<?php echo $id_ket_cashback ?>" id="inlineCheckbox2-<?php echo $id_ket_cashback ?>">
                                            <label class="form-check-label" for="inlineCheckbox2-<?php echo $id_ket_cashback ?>"><?php echo $data_cb['ket_cashback'] ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                                <!-- Input fields untuk menampilkan nilai ID yang dipilih -->
                                <input type="hidden" id="selected-values" name="selected_cashback" class="form-control mt-3" readonly>
                                <div class="mt-3">
                                    <div class="row">
                                        <div class="col-sm-6 d-none" id="div_cb_total_inv">
                                            <label><strong>Cashback Total Invoice</strong></label>
                                            <div class="input-group">
                                                <input type="text" id="cb_total_inv" name="cb_total_inv" value="0" class="form-control text-end" max="100" readonly>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 d-none" id="div_cb_pajak">
                                            <label><strong>Cashback Pajak</strong></label>
                                            <div class="input-group">
                                                <input type="text" id="cb_pajak" name="cb_pajak" value="0" class="form-control text-end" max="100" readonly>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label><strong>Tambahan Invoice</strong></label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="inlineCheckbox1" name="kwitansi" value="1">
                                        <label class="form-check-label" for="inlineCheckbox1">Kwitansi</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="inlineCheckbox2" name="surat_jalan" value="1">
                                        <label class="form-check-label" for="inlineCheckbox2">Surat Jalan</label>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label><strong>Note Invoice</strong></label>
                                    <textarea type="text" class="form-control" style="height: 150px;" name="note_inv"></textarea>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary btn-md" name="simpan-inv">Simpan Data</button>
                                <a href="spk-siap-kirim.php?sort=baru" class="btn btn-secondary">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End simpan ke table invoice -->
        <!-- End update ke table spk  -->
    </main><!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>

</html>
<script>
    function enabled() {
        var select = document.getElementById("select");
        var spDiscInput = document.getElementById("sp_disc");

        if (select.value === "Spesial Diskon") {
            spDiscInput.removeAttribute("readonly");
            spDiscInput.setAttribute("required", true);
            spDiscInput.classList.remove("bg-light");
        } else {
            spDiscInput.setAttribute("readonly", true);
            spDiscInput.removeAttribute("required");
            spDiscInput.classList.add("bg-light");
            spDiscInput.value = "0"; // Reset nilai input menjadi 0
        }
    }
</script>


<script type="text/javascript">
    var dateInput = document.getElementById('date');
    var tempoInput = document.getElementById('tempo');
    var datepickerInstance;

    // Mendapatkan tanggal hari ini dari sistem operasi
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();
    var todayFormatted = dd + '/' + mm + '/' + yyyy;

    // Mengatur tanggal invoice sebagai tanggal hari ini dari sistem operasi
    dateInput.value = todayFormatted;
    tempoInput.value = '';

    // Kode untuk mengatur batasan tanggal invoice 7 hari kebelakang dan 3 hari kedepan
    // Mendapatkan tanggal awal dan akhir bulan ini
    var firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    var lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    // Mendapatkan tanggal 7 hari sebelumnya dan 7 hari ke depan dari hari ini
    var sevenDaysAgo = new Date(today);
    sevenDaysAgo.setDate(today.getDate() - 7);

    var sevenDaysLater = new Date(today);
    sevenDaysLater.setDate(today.getDate() + 3);

    // Menentukan minDate dan maxDate untuk rentang yang diizinkan
    var minDate = new Date(
        Math.max(firstDayOfMonth, sevenDaysAgo) // Mengambil tanggal terbesar dari antara tanggal awal bulan ini dan 7 hari yang lalu
    );
    var maxDate = new Date(
        Math.min(lastDayOfMonth, sevenDaysLater) // Mengambil tanggal terkecil dari antara tanggal akhir bulan ini dan 7 hari ke depan
    );

    flatpickr("#date", {
        dateFormat: "d/m/Y",
        minDate: minDate,
        maxDate: maxDate,
        onClose: function(selectedDates, dateStr) {
            if (selectedDates[0]) {
                // Menghapus dan menghancurkan instance datepicker sebelumnya, jika ada
                if (datepickerInstance) {
                    datepickerInstance.destroy();
                }

                // Mengatur tanggal tempo sebagai tanggal invoice yang baru dipilih atau tanggal invoice jika sebelumnya
                var selectedDate = new Date(selectedDates[0]);
                var tempoDate = (selectedDate < today) ? selectedDate : today;
                var tempoDateFormatted = String(tempoDate.getDate()).padStart(2, '0') + '/' + String(tempoDate.getMonth() + 1).padStart(2, '0') + '/' + tempoDate.getFullYear();
                tempoInput.value = tempoDateFormatted;

                // Menonaktifkan tanggal sebelum hari ini pada tanggal tempo
                var disableDates = [{
                    from: new Date(0, 0, 1),
                    to: today
                }];

                // Menerapkan datepicker pada tanggal tempo
                datepickerInstance = flatpickr("#tempo", {
                    dateFormat: "d/m/Y",
                    disable: disableDates,
                    defaultDate: tempoDateFormatted
                });
            }
        }
    });

    flatpickr("#tempo", {
        dateFormat: "d/m/Y",
        minDate: todayFormatted // Mengatur tanggal minimum pada tanggal tempo menjadi hari ini
    });

    // Validasi tanggal invoice agar tidak boleh kurang dari tanggal hari ini
    dateInput.addEventListener('blur', function() {
        var selectedDateParts = dateInput.value.split('/');
        var selectedDay = parseInt(selectedDateParts[0]);
        var selectedMonth = parseInt(selectedDateParts[1]) - 1;
        var selectedYear = parseInt(selectedDateParts[2]);
        var selectedDate = new Date(selectedYear, selectedMonth, selectedDay);

        if (selectedDate < today) {
            dateInput.value = todayFormatted; // Mengatur kembali tanggal invoice sebagai tanggal hari ini
            tempoInput.value = todayFormatted; // Mengatur kembali tanggal tempo sebagai tanggal hari ini
        } else {
            tempoInput.value = dateInput.value; // Mengatur tanggal tempo sesuai dengan tanggal invoice yang baru dipilih
        }
    });

    var tempoInput = document.getElementById('tempo');
    var clearSearchBtn = document.getElementById('clear-search');

    // Fungsi untuk menghapus isi input 'tempo'
    function clearSearch() {
        tempoInput.value = '';
    }

    // Menambahkan event listener pada tombol 'Clear Search'
    clearSearchBtn.addEventListener('click', clearSearch);
</script>

<!-- end date picker -->
<script>
    function submitForm(event) {
        event.preventDefault(); // Mencegah pengiriman form secara otomatis

        var dateInput = document.getElementById('date');

        if (dateInput.value.trim() === '') {
            alert('Tanggal Invoice harus diisi!');
            return;
        }

        // Lanjutkan dengan mengirimkan form
        document.getElementById('myForm').submit();
    }
</script>

<!-- Untuk menanganin form cashback -->
<script>
    $(document).ready(function() {
        // Fungsi untuk menangani input pada Diskon SP
        $('#sp_disc').on('input', function() {
            let value = $(this).val();

            // Menghapus karakter yang tidak berupa angka atau titik (mengizinkan satu titik sebagai desimal)
            value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\..*/g, '$1'); // Mengizinkan hanya satu titik

            // Membatasi satu angka di belakang titik desimal
            const parts = value.split('.');
            if (parts.length > 1) {
                // Jika ada bagian desimal, ambil hanya satu angka setelah titik
                value = parts[0] + '.' + parts[1].charAt(0);
            }

            // Jika lebih dari 100, set nilai menjadi 100
            if (parseFloat(value) > 100) {
                value = '100';
            }

            // Set nilai kembali ke input
            $(this).val(value);
        });

        // Fungsi untuk menangani input pada Cashback Total Invoice
        $('#cb_total_inv').on('input', function() {
            let value = $(this).val();

            // Menghapus karakter yang tidak berupa angka atau titik (mengizinkan satu titik sebagai desimal)
            value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\..*/g, '$1'); // Mengizinkan hanya satu titik

            // Membatasi satu angka di belakang titik desimal
            const parts = value.split('.');
            if (parts.length > 1) {
                // Jika ada bagian desimal, ambil hanya satu angka setelah titik
                value = parts[0] + '.' + parts[1].charAt(0);
            }

            // Jika lebih dari 100, set nilai menjadi 100
            if (parseFloat(value) > 100) {
                value = '100';
            }

            // Set nilai kembali ke input
            $(this).val(value);
        });

        // Fungsi untuk menangani input pada Cashback Pajak
        $('#cb_pajak').on('input', function() {
            let value = $(this).val();

            // Menghapus karakter yang tidak berupa angka atau titik (mengizinkan satu titik sebagai desimal)
            value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\..*/g, '$1'); // Mengizinkan hanya satu titik

            // Membatasi satu angka di belakang titik desimal
            const parts = value.split('.');
            if (parts.length > 1) {
                // Jika ada bagian desimal, ambil hanya satu angka setelah titik
                value = parts[0] + '.' + parts[1].charAt(0);
            }

            // Jika lebih dari 100, set nilai menjadi 100
            if (parseFloat(value) > 100) {
                value = '100';
            }

            // Set nilai kembali ke input
            $(this).val(value);
        });

        // Event status cb
        $('#status_cb input[type="radio"]').change(function() {
            var statusCb =  $(this).val(); // Ambil value dari radio (status_cb))
            var divJenisCb = document.getElementById("cashback-container");
            cb_total_inv
            if(statusCb == 1){
                divJenisCb.classList.remove("d-none");
               
            } else {
                divJenisCb.classList.add("d-none");
                // Hilangkan checked pada semua checkbox di dalam cashback-container
                $('#cashback-container input[type="checkbox"]').prop('checked', false);
                $('#div_cb_total_inv').addClass("d-none");
                $('#div_cb_pajak').addClass("d-none");
                $('#div_cb_total_inv').attr('readonly', 'readonly'); // Menetapkan atribut 'readonly'
                $('#div_cb_pajak').attr('readonly', 'readonly'); // Menetapkan atribut 'readonly'
                $('#cb_total_inv').val('0'); // Kosongkan jika checkbox tidak dicentang
                $('#cb_pajak').val('0'); // Kosongkan jika checkbox tidak dicentang
            }
        });

        // Ketika checkbox di dalam #cashback-container diubah
        $('#cashback-container .form-check-input').on('change', function() {
            // Array untuk menampung nilai yang dipilih
            let selectedValues = [];

            // Iterasi setiap checkbox yang diceklis
            $('#cashback-container .form-check-input:checked').each(function() {
                selectedValues.push($(this).val());
            });

            // Gabungkan nilai yang dipilih dengan koma, lalu tampilkan di input #selected-values
            $('#selected-values').val(selectedValues.join(', '));
        });
        // Event change untuk checkbox
        $('#cashback-container input[type="checkbox"]').change(function() {
            var checkboxValue = $(this).val(); // Ambil value dari checkbox (id_ket_cashback)
            var checkboxLabel = $(this).next('label').text(); // Ambil text dari label setelah checkbox
            var cb_total_inv = document.getElementById("cb_total_inv");
            var cb_pajak = document.getElementById("cb_pajak");
            var div_cb_total_inv = document.getElementById("div_cb_total_inv");
            var div_cb_pajak = document.getElementById("div_cb_pajak");

            // Cek nama checkbox (labelnya) untuk menentukan input yang sesuai
            if (checkboxLabel === 'Per Barang') {
                if ($(this).is(':checked')) {
                    $('#per_barang').val(checkboxValue); // Set ID keterangan ke input
                } else {
                    $('#per_barang').val(''); // Kosongkan jika checkbox tidak dicentang
                }
            }
            if (checkboxLabel === 'Total Invoice') {
                if ($(this).is(':checked')) {
                    $('#total_invoice').val(checkboxValue);
                    cb_total_inv.removeAttribute("readonly");
                    cb_total_inv.setAttribute("required", true);
                    cb_total_inv.classList.remove("bg-light");
                    div_cb_total_inv.classList.remove("d-none");
                } else {
                    $('#total_invoice').val('');
                    cb_total_inv.setAttribute("readonly", true);
                    cb_total_inv.removeAttribute("required");
                    cb_total_inv.classList.add("bg-light");
                    div_cb_total_inv.classList.add("d-none");
                    cb_total_inv.value = "0"; // Reset nilai input menjadi 0
                }
            }
            if (checkboxLabel === 'Pajak') {
                if ($(this).is(':checked')) {
                    $('#pajak').val(checkboxValue);
                    cb_pajak.removeAttribute("readonly");
                    cb_pajak.setAttribute("required", true);
                    cb_pajak.classList.remove("bg-light");
                    div_cb_pajak.classList.remove("d-none");
                } else {
                    $('#pajak').val('');
                    cb_pajak.setAttribute("readonly", true);
                    cb_pajak.removeAttribute("required");
                    cb_pajak.classList.add("bg-light");
                    div_cb_pajak.classList.add("d-none");
                    cb_pajak.value = "0"; // Reset nilai input menjadi 0
                }
            }
            if (checkboxLabel === 'Pengiriman') {
                if ($(this).is(':checked')) {
                    $('#pengiriman').val(checkboxValue);
                } else {
                    $('#pengiriman').val('');
                }
            }
        });
    });
</script>
