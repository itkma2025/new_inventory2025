<!-- Modal Tambah Data Persediaan Barang -->
<div class="modal fade" id="editIn" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit data barang</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="tutup-x"></button>
            </div>
            <div class="modal-body">
                <div class="text-center border-bottom fw-bold fs-5">
                    <label>Edit Data Barang Masuk</label>
                </div>
                <?php
                date_default_timezone_set('Asia/Jakarta');
                require_once "function/uuid.php";
                $uuid = uuid();
                $year = date('y');
                $day = date('d');
                $month = date('m');
                $id_history = "HIS-KS-" . $year . $month . $uuid . $day;
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
                    ?>
                    <input type="hidden" id="csrf_token_editIn" class="form-control" name="csrf_token">
                    <input type="hidden" name="id_kartu_stock" id="id_kartu_stock" class="form-control">
                    <input type="hidden" name="id_history" id="id_history" class="form-control" value="<?php echo $id_history ?>">
                    <input type="hidden" name="jenis_produk" class="form-control" value="<?php echo $jenis_produk ?>">
                    <input type="hidden" name="id_produk" class="form-control" value="<?php echo $id ?>">
                    <div class="mt-3">
                        <label class="fw-bold">Keterangan :</label>
                        <input type="text" class="form-control" name="keterangan" id="keterangan" required>
                    </div>
                    <div class="mt-3">
                        <label class="fw-bold">Qty :</label>
                        <input type="number" class="form-control" name="qty" id="qty_in" required min="1" autocomplete="off">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="tutup">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="edit-in" id="edit-in">Simpan Perubahan</button>
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
            $('#editIn').on('show.bs.modal', function(event) {
                // Mendapatkan data dari tombol yang ditekan
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var keterangan = button.data('keterangan');
                var qtyIn = button.data('qty');

                var modal = $(this);
                var simpanBtn = modal.find('.modal-footer #edit');
                var ketInput = modal.find('.modal-body #keterangan');
                var qtyInput = modal.find('.modal-body #qty_in');

                // Menampilkan data
                modal.find('.modal-body #id_kartu_stock').val(id);
                ketInput.val(keterangan);
                qtyInput.val(qtyIn);

                $.ajax({
                    url: 'ajax/get-token-csrf.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#csrf_token_editIn').val(response.token_csrf);
                        $('#expiryDate').attr('data-expiry', response.token_exp);
                        $.getScript('assets/js/expired-token.js');
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