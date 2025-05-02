<!-- Modal Tambah Data Persediaan Barang -->
<div class="modal fade" id="add" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="tutup-x"></button>
            </div>
            <div class="modal-body">
                <div class="text-center border-bottom">
                    <p class="pb-2 fw-bold fs-5">Tambah Data Kartu Stock</p>
                </div>
                <?php
                $action = "";
                if ($jenis_produk == 'reguler') {
                    $action = "proses/proses-kartu-stock.php";
                } else if ($jenis_produk == 'ecat') {
                    $action = "proses/proses-kartu-stock-ecat.php";
                } else if ($jenis_produk == 'set_reg') {
                    $action = "proses/proses-kartu-stock-set-reg.php";
                } else if ($jenis_produk == 'set_ecat') {
                    $action = "proses/proses-kartu-stock-set-ecat.php";
                }
                ?>
                <form action="<?php echo $action ?>" method="POST" autocomplete="off">
                    <?php
                    date_default_timezone_set('Asia/Jakarta');
                    require_once "../function/uuid.php";
                    $uuid = uuid();
                    $year = date('y');
                    $day = date('d');
                    $month = date('m');
                    $id_kartu_stock = "KS-" . $year . $month . $uuid . $day;
                    $id_history = "HIS-KS-" . $year . $month . $uuid . $day;
                    ?>
                    <input type="hidden" id="csrf_token" class="form-control" name="csrf_token" value="<?php echo $csrf_token ?>">
                    <input type="hidden" name="id_kartu_stock" class="form-control" value="<?php echo $id_kartu_stock ?>">
                    <input type="hidden" name="id_history" class="form-control" value="<?php echo $id_history ?>">
                    <input type="hidden" name="jenis_produk" class="form-control" value="<?php echo $jenis_produk ?>">
                    <input type="hidden" name="id_produk" class="form-control" value="<?php echo $id ?>">
                    <div class="mt-3">
                        <label class="fw-bold">Nama Produk : <?php echo $data_produk['nama_produk'] ?></label>
                    </div>
                    <div class="mt-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="in" value="0" required>
                            <label class="form-check-label">Barang Masuk</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="out" value="1" required>
                            <label class="form-check-label">Barang Keluar</label>
                        </div>
                    </div>
                    <div id="data_in" style="display: none;">
                        <div class="mt-3" id="jenisMasuk">
                            <label class="fw-bold">Pilih Jenis Barang Masuk :</label>
                            <select name="jenis_barang_masuk" id="jenis_br_in" class="form-select" required>
                                <option value="">Pilih Jenis...</option>
                                <?php
                                require_once "query/ket-in.php";
                                while ($data_ket_in = mysqli_fetch_array($query_ket_in)) {
                                ?>
                                    <option value="<?php echo $data_ket_in['ket_in'] ?>">
                                        <?php echo $data_ket_in['ket_in'] ?>
                                    </option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div id="data_out" style="display: none;">
                        <div class="mt-3" id="ket_out">
                            <label class="fw-bold" id="jenis-barang-label">Pilih Jenis Barang Keluar :</label>
                            <select name="jenis_barang_keluar" id="jenis_br_out" class="form-select" required>
                                <option value="">Pilih Jenis...</option>
                                <?php
                                require_once "query/ket-out.php";
                                while ($data_ket_out = mysqli_fetch_array($query_ket_out)) {
                                ?>
                                    <option value="<?php echo $data_ket_out['ket_out'] ?>">
                                        <?php echo $data_ket_out['ket_out'] ?>
                                    </option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mt-3" id="noSpk" style="display: none;">
                            <label class="fw-bold">Pilih No. SPK Penjualan :</label>
                            <select name="id_spk" class="form-select">
                                <option value="">Pilih....</option>
                                <?php
                                require_once "query/data-spk.php";
                                while ($data_spk = mysqli_fetch_array($query_spk)) {
                                ?>
                                    <option value="<?php echo $data_spk['id_transaksi'] . ',' . $data_spk['id_spk']; ?>">
                                        <?php echo $data_spk['no_spk'] . ' - ' . $data_spk['nama_cs']; ?>
                                    </option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div id="qtyKet" style="display: none;">
                        <div class="mt-3">
                            <label class="fw-bold">Qty :</label>
                            <input type="number" class="form-control" name="qty" required min="1" autocomplete="off">
                        </div>
                        <div class="mt-3" id="ket" style="display: block;">
                            <label class="fw-bold">Keterangan :</label>
                            <input type="text" class="form-control" name="keterangan" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="tutup">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="simpan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Membuat realtime permintaan refresh browser jika CSRF token sudah expired -->
    <!-- Ambil nilai expired date terlebih dahulu -->
    <script id="expiryDate" data-expiry="<?php echo $expired_token ?>"></script>
     
    <script>
        $(document).ready(function() {
            // Inisialisasi selectize di awal untuk #jenis_br_out
            $('#jenis_br_out').addClass('selectize-jenis').selectize();

            // Fungsi untuk reset dan sembunyikan selectize
            function resetSelectizeAndHide(id) {
                var selectElement = $('#' + id + ' select');
                var selectizeControl = selectElement[0]?.selectize;
                if (selectizeControl) {
                    selectizeControl.clear(); // Mengosongkan nilai yang dipilih
                    selectizeControl.destroy();
                }
                $('#' + id).hide();
                selectElement.removeClass('selectize-jenis').removeAttr('required');
                selectElement.val(''); // Mengatur nilai select menjadi kosong
            }

            // Event listener untuk select option jenis_barang_keluar
            $('#jenis_br_out').on('change', function() {
                var selectedValue = $(this).val();
                if (selectedValue === "Penjualan") {
                    $('#noSpk').show();
                    $('#ket').hide();
                    // Hapus atribut required dari #ket saat Penjualan dipilih
                    $('#ket').find('input, select, textarea').removeAttr('required');
                    $('#noSpk select').addClass('selectize-jenis').attr('required', true).selectize();
                } else {
                    // Jika bukan Penjualan, sembunyikan dan reset selectize
                    resetSelectizeAndHide('noSpk');
                    $('#ket').show();
                }
            });

            // Event listener untuk status masuk/keluar
            $('input[name="status"]').on('change', function() {
                if ($('#in').is(':checked')) {
                    $('#data_in').show();
                    $('#data_out').hide();
                    $('#qtyKet').show();
                    $('#noSpk').hide();

                    // Tambahkan Selectize ke #jenis_br_in saat status "in"
                    $('#jenis_br_in').addClass('selectize-jenis').attr('required', true).selectize();
                    // Reset Selectize di #jenis_br_in saat status "out"
                    resetSelectizeAndHide('data_out');
                } else if ($('#out').is(':checked')) {
                    $('#data_out').show();
                    $('#data_in').hide();
                    $('#qtyKet').show();
                    $('#noInv').hide();

                    // Tambahkan Selectize ke #jenis_br_out saat status "out"
                    $('#jenis_br_out').addClass('selectize-jenis').attr('required', true).selectize();
                    // Reset Selectize di #jenis_br_in saat status "out"
                    resetSelectizeAndHide('data_in');
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#add').on('show.bs.modal', function(e) {
                $.ajax({
                    url: '../ajax/get-token-csrf.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#csrf_token').val(response.token_csrf);
                        $('#expiryDate').attr('data-expiry', response.token_exp);
                        $.getScript('../assets/js/expired-token.js');
                    },
                    error: function() {
                        // Debugging
                        console.error('AJAX request failed');
                    }
                });
            });

        });
    </script>
    <!-- Reload page after close modal dialog -->
    <script>
        $('#tutup, #tutup-x').on('click', function() {
            location.reload();
        });
    </script>