<div class="modal fade" id="ubahJenisCb"  data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Jenis Cashback</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <?php
                $action = "";
                if ($_GET['jenis'] == 'nonppn') {
                    $action = "proses/proses-invoice-nonppn.php";
                    $sql_status_cb = $connect->query("SELECT status_cb, jenis_cb, cb_total_inv, cb_pajak FROM cashback_nonppn WHERE id_inv = '$id_inv'");
                    $data_status_cb = mysqli_fetch_array($sql_status_cb);
                    $tampil_status_cb = $data_status_cb['status_cb'];
                } else if ($_GET['jenis'] == 'ppn') {
                    $action = "proses/proses-invoice-ppn.php";
                    $sql_status_cb = $connect->query("SELECT status_cb, jenis_cb, cb_total_inv, cb_pajak FROM cashback_ppn WHERE id_inv = '$id_inv'");
                    $data_status_cb = mysqli_fetch_array($sql_status_cb);
                    $tampil_status_cb = $data_status_cb['status_cb'];
                } else if ($_GET['jenis'] == 'bum') {
                    $action = "proses/proses-invoice-bum.php";
                    $sql_status_cb = $connect->query("SELECT status_cb, jenis_cb, cb_total_inv, cb_pajak FROM cashback_bum WHERE id_inv = '$id_inv'");
                    $data_status_cb = mysqli_fetch_array($sql_status_cb);
                    $tampil_status_cb = $data_status_cb['status_cb'];
                } else {
                    ?>
                        <script type="text/javascript">
                            window.location.href = "../404.php";
                        </script>
                    <?php
                }
            ?>
            <div class="mt-3">
                <label><strong>Jenis Cashback Saat Ini</strong></label><br>
                <?php
                    // Contoh data yang diambil dari field jenis_cb tabel cashback_nonppn
                    $jenis_cb = $data_status_cb['jenis_cb'];

                    // Pisahkan menjadi array berdasarkan koma
                    $cashback_ids = explode(',', $jenis_cb);

                    // Enkripsi setiap ID cashback secara terpisah
                    $encrypted_cashback_ids = array_map(function($id) use ($key_global) {
                        return encrypt($id, $key_global);
                    }, $cashback_ids);

                    // Gabungkan seluruh ID terenkripsi menjadi satu string
                    $all_encrypted_ids = implode(',', $encrypted_cashback_ids);

                    // Debug: Tampilkan hasil enkripsi dari tiap ID
                    foreach ($encrypted_cashback_ids as $encrypted_id) {
                        // echo $encrypted_id . "<br>";
                    }

                    // Query untuk mendapatkan semua opsi checkbox
                    $sql_cb = $connect->query("SELECT id_ket_cashback, ket_cashback FROM keterangan_cashback ORDER BY created_date");
                    while ($data_cb = mysqli_fetch_array($sql_cb)) {
                        // Enkripsi id_ket_cashback agar sesuai dengan format dalam array $encrypted_cashback_ids
                        $id_ket_cashback = encrypt($data_cb['id_ket_cashback'], $key_global);
                    ?>
                        <?php if (in_array($id_ket_cashback, $encrypted_cashback_ids)): ?>
                            <div class="form-check me-3 d-block d-md-inline-block mr-md-3">
                                <input class="form-check-input" type="checkbox" 
                                    value="<?php echo $id_ket_cashback ?>" 
                                    id="inlineCheckbox2-<?php echo $id_ket_cashback ?>"
                                    checked disabled>
                                <label class="form-check-label" for="inlineCheckbox2-<?php echo $id_ket_cashback ?>">
                                    <?php echo $data_cb['ket_cashback'] ?>
                                </label>
                            </div>
                            
                            <!-- Script to Show div_cb_total_inv_edit if "Total Invoice" is checked -->
                            <?php if ($data_cb['ket_cashback'] === 'Total Invoice'): ?>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const divCbTotalInvEdit = document.getElementById('div_cb_total_inv_edit');
                                        const checkboxTotalInvoice = document.getElementById('inlineCheckbox2-<?php echo $id_ket_cashback; ?>');

                                        if (checkboxTotalInvoice && checkboxTotalInvoice.checked) {
                                            divCbTotalInvEdit.classList.remove('d-none'); // Show div if checkbox is checked
                                        }
                                    });
                                </script>
                            <?php endif; ?>

                            <!-- Script to Show div_cb_total_inv_edit if "Total Invoice" is checked -->
                            <?php if ($data_cb['ket_cashback'] === 'Pajak'): ?>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const divCbTotalInvEdit = document.getElementById('div_cb_pajak_edit');
                                        const checkboxTotalInvoice = document.getElementById('inlineCheckbox2-<?php echo $id_ket_cashback; ?>');

                                        if (checkboxTotalInvoice && checkboxTotalInvoice.checked) {
                                            divCbTotalInvEdit.classList.remove('d-none'); // Show div if checkbox is checked
                                        }
                                    });
                                </script>
                            <?php endif; ?>
                        <?php endif; ?>
                <?php } ?>
                <?php  
                    if($jenis_cb == ''){
                        echo 'Jenis Cashback Belum Dipilih';
                    }
                ?>
            </div>
            <div class="mt-3">
                <div class="row">
                    <div class="col-sm-6 d-none" id="div_cb_total_inv_edit">
                        <label><strong>Cashback Total Invoice</strong></label>
                        <div class="input-group">
                            <input type="text" id="cb_total_inv" name="cb_total_inv_edit" value="<?php echo $data_status_cb['cb_total_inv']; ?>" class="form-control text-end" max="100" readonly>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-sm-6 d-none" id="div_cb_pajak_edit">
                        <label><strong>Cashback Pajak</strong></label>
                        <div class="input-group">
                            <input type="text" id="cb_pajak" name="cb_pajak_edit" value="<?php echo $data_status_cb['cb_pajak']; ?>" class="form-control text-end" max="100" readonly>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>
            <form action="<?php echo $action ?>" method="POST">
                <input type="hidden" name="id_inv" class="form-control" value="<?php echo encrypt($id_inv, $key_global) ?>">
                <div class="border-top mt-3 mb-3"></div>
                <div class="mt-3" id="cashback-container">
                    <label><strong>Ubah Jenis Cashback</strong></label><br>
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
                                <input type="text" id="cb_total_inv" name="cb_total_inv" value="0" class="form-control text-end" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-sm-6 d-none" id="div_cb_pajak">
                            <label><strong>Cashback Pajak</strong></label>
                            <div class="input-group">
                                <input type="text" id="cb_pajak" name="cb_pajak" value="0" class="form-control text-end" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer mt-3">
                    <button type="submit" name="ubah-jenis-cb" class="btn btn-primary">Edit Data CB</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form> 
        </div>
    </div>
</div>

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