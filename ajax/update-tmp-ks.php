<?php
require_once "../akses.php";
require_once "../function/function-enkripsi.php";

if (isset($_POST['id']) && isset($_POST['qty'])) {
    // Dekripsi id_trx
    $id_trx = htmlspecialchars(decrypt($_POST['id'], $key_gudang));

    // Simpan $id_trx ke dalam file post_ajax.txt
    // $file = 'post_ajax.txt'; // Pastikan file ini memiliki izin tulis
    // file_put_contents($file, $id_trx . PHP_EOL, FILE_APPEND);

    // Hapus pemisah ribuan dari qty
    $qty = str_replace('.', '', $_POST['qty']); // Hapus semua titik dari qty

    // Pastikan qty adalah angka
    $qty = is_numeric($qty) ? (int)$qty : 0;

    // Melakukan pengecekan untuk menentukan keterangan KS
    $cek_spk = $connect->query("SELECT id_spk, id_produk, qty FROM transaksi_produk_reg WHERE id_transaksi = '$id_trx'");
    $data_spk = mysqli_fetch_array($cek_spk);
    $trx_spk_id = $data_spk['id_spk'];
    $trx_id_produk = $data_spk['id_produk'];
    $trx_qty = $data_spk['qty'];

    // Logika untuk keterangan KS
    $keterangan_ks = ($qty == $trx_qty) ? "1" : "0";

    // Update database
    $stmt = $connect->prepare("UPDATE tmp_kartu_stock SET qty_ks = ?, keterangan_ks = ? WHERE id_transaksi = ?");
    $stmt->bind_param("iis", $qty, $keterangan_ks, $id_trx);
    $update_tmp = $stmt->execute();

    if ($update_tmp) {
        echo json_encode(['status' => 'success', 'id_trx' => $id_trx]);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit();
}
