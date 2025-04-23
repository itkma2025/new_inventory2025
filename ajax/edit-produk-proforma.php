<?php
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0"); // Proxies.

    // Hubungkan Koneksi
    require_once "../akses.php";

   // Penghubung Library
   require_once '../assets/vendor/autoload.php';

   // Library sanitasi input data
   require_once "../function/sanitasi_input.php";

   // Function Encrypt dan Decrypt
   require_once "../function/function-enkripsi.php";

    if (isset($_POST['id_trx'])) {
        // Sanitasi seluruh $_POST
        $sanitasi_post = sanitizeInput($_POST);
        $id_trx = decrypt($sanitasi_post['id_trx'], $key_global);
        $id_inv = decrypt($sanitasi_post['id_inv'], $key_global);
        $jenis_inv = $sanitasi_post['jenis_inv'];
        $kat_inv = $sanitasi_post['kat_inv'];
        $jenis_cb = $sanitasi_post['jenis_cb'];
        $id_cs = decrypt($sanitasi_post['cs'], $key_global);
        $no_inv = decrypt($sanitasi_post['noinv'], $key_global);

        $sql_trx_edit = $connect->query("SELECT id_produk, nama_produk_spk, harga, qty, disc, disc_cb FROM transaksi_produk_reg WHERE id_transaksi = '$id_trx'");
        $data_trx_edit = mysqli_fetch_assoc($sql_trx_edit);
        $id_produk = $data_trx_edit['id_produk'];
    }

    $action = "";
    if ($jenis_inv == 'nonppn') {
        $action = "proses/proses-invoice-nonppn.php";
    } else if ($jenis_inv == 'ppn') {
        $action = "proses/proses-invoice-ppn.php";
    } else if ($jenis_inv == 'bum') {
        $action = "proses/proses-invoice-bum.php";
    } else {
        ?>
            <script type="text/javascript">
                window.location.href = "../404.php";
            </script>
        <?php
    }
    $nonce = bin2hex(random_bytes(16)); // Generate a secure random token
    $_SESSION['nonce_token'] = $nonce; // Store in session for verification
?>
<form action="<?php echo $action ?>" method="POST">
    <input type="hidden" name="nonce_token" value="<?php echo $_SESSION['nonce_token']; ?>">
    <input type="hidden" name="id_trx" value="<?php echo encrypt($id_trx, $key_global); ?>">
    <input type="hidden" name="id_inv" value="<?php echo encrypt($id_inv, $key_global); ?>">
    <div class="mb-3">
        <label><strong>Nama Produk</strong></label>
        <input type="text" class="form-control" name="nama_produk" value="<?php echo $data_trx_edit['nama_produk_spk'] ?>" required>
    </div>
    <div class="mb-3">
        <label><strong>Harga</strong></label>
        <div class="input-group">
            <span class="input-group-text" id="basic-addon1">Rp</span>
            <input type="text" class="form-control text-end harga_produk" name="harga_produk" value="<?php echo number_format($data_trx_edit['harga'],0,'.','.') ?>" oninput="formatNumber(this)" data-index="<?php echo encrypt($id_produk, $key_global); ?>" required>
            <span class="input-group-text" id="referensiHarga"  title="Lihat Referensi Harga Produk" data-bs-toggle="modal" data-bs-target="#referensiHargaProduk" data-id='<?php echo encrypt($id_produk, $key_global); ?>' data-cs='<?php echo encrypt($id_cs, $key_global); ?>' data-noinv='<?php echo encrypt($no_inv, $key_global); ?>'>
                <i class="bi bi-eye-fill"></i>
            </span>
        </div>
    </div>
    <input type="hidden" class="form-control text-end harga_produk" name="qty" value="<?php echo number_format($data_trx_edit['qty'],0,'.','.') ?>"" required>
    <?php if ($kat_inv == "Diskon"): ?>
    <div class="mb-3">
        <label><b>Diskon Produk</b></label>
        <div class="input-group">
            <input type="text" class="form-control text-end" name="disc" id="disc" value="<?php echo $data_trx_edit['disc']?>" oninput="formatDiscount(this)" required>
            <span class="input-group-text" id="basic-addon1">%</span>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($jenis_cb == "Per Barang"): ?>
    <div class="mb-3">
        <label><b>Diskon CB</b></label>
        <div class="input-group">
            <input type="text" class="form-control text-end" name="disc_cb" id="disc" value="<?php echo $data_trx_edit['disc_cb']?>" oninput="formatDiscount(this)" required>
            <span class="input-group-text" id="basic-addon1">%</span>
        </div>
    </div>
    <?php endif; ?>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" name="edit-produk-proforma">Update Data</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnTutup">Close</button>
    </div>
</form>  
<script>
    $('#hargaRef').on('show.bs.modal', function () {
        $('#modalEditProduk').modal('hide');
    });

    $('#referensiHargaProduk').on('show.bs.modal', function () {
        if ($('#modalEditProduk').hasClass('show')) {
            $('#modalEditProduk').modal('hide');
        }
    });

    $('#modalEditProduk').on('hide.bs.modal', function (e) {
        e.preventDefault();
    });

    $('#referensiHargaProduk').on('hidden.bs.modal', function () {

        $('#modalEditProduk').modal('show');
    });
  
    document.getElementById('btnTutup').addEventListener('click', function() {
      // Reload halaman
      location.reload();
    });
</script>
