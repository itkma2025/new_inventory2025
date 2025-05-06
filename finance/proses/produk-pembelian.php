<?php
require_once "../../akses.php";

if (isset($_POST['simpan-trx'])) {
    // Mendapatkan data yang dikirimkan melalui form
    $id_trx = $_POST['id_trx'];
    $id_inv_encode = base64_encode($_POST['id_inv']);
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $qty = $_POST['qty_trx']; 
    $disc = $_POST['disc'];

    mysqli_begin_transaction($connect);

    try {
        // Melakukan perulangan untuk menyimpan setiap data
        for ($i = 0; $i < count($id_trx); $i++) {
            $id = $id_trx[$i];
            $nama_produk_array = $nama_produk[$i];
            $newHarga = str_replace(',', '', $harga[$i]); // Menghapus tanda ribuan (,)
            $newHarga = intval($newHarga); // Mengubah string harga menjadi integer
            $newQtyInt = str_replace(',', '', $qty[$i]); // Menghapus tanda ribuan (,)
            $newQtyInt = intval($newQtyInt); // Mengubah string harga menjadi integer
            $disc_array = $disc[$i];
            $disc_array = floatval($disc_array);
            $total_sebelum_disc = $newHarga * $newQtyInt;
            $total_setelah_disc =  $total_sebelum_disc - ($total_sebelum_disc * $disc_array / 100);

            // Lakukan proses penyimpanan data qty ke dalam database sesuai dengan ID transaksi
            // Contoh: Lakukan kueri SQL untuk memperbarui data qty dalam tabel transaksi menggunakan ID transaksi
            $sql = "UPDATE trx_produk_pembelian SET nama_produk = '$nama_produk_array', harga = '$newHarga', qty = '$newQtyInt', disc = '$disc_array', status_trx = '1', total_harga = '$total_setelah_disc' WHERE id_trx_produk = '$id'";
            $query = mysqli_query($connect, $sql);

            // Periksa apakah query berhasil dijalankan
            if (!$query) {
                throw new Exception("Terjadi kesalahan saat menyimpan data.");
            }
        }

        // Jika tidak terjadi kesalahan, commit transaksi
        mysqli_commit($connect);
        $_SESSION['info'] = "Disimpan";
        header("Location:../detail-produk-pembelian-lokal.php?id='$id_inv_encode'");
    } catch (Exception $e) {
        // Jika terjadi kesalahan, rollback transaksi
        mysqli_rollback($connect);
        $_SESSION['info'] = "Data Gagal Disimpan";
        header("Location:../detail-produk-pembelian-lokal.php?id='$id_inv_encode'");
    }
} else if (isset($_POST['edit'])) {
    $id_trx = $_POST['id_trx'];
    $id_inv = $_POST['id_inv'];
    $nama_produk = htmlspecialchars($_POST['nama_produk_edit']);
    $harga_edit = $_POST['harga_edit'];
    $harga =  str_replace(',', '', $harga_edit);
    $harga = intval($harga);
    $qty_edit = $_POST['qty_edit'];
    $qty =  str_replace(',', '', $qty_edit);
    $qty = intval($qty);
    $disc = $_POST['disc_edit'];
    $disc_array = floatval($disc);
    $total_sebelum_disc = $harga * $qty;
    $total_setelah_disc =  $total_sebelum_disc - ($total_sebelum_disc * $disc_array / 100);
    $id_inv_encode = base64_encode($id_inv);
    $updated_by = $_SESSION['tiket_nama'];
    $time = date('d-m-Y, H:i:s');

    $update = mysqli_query($connect, "UPDATE trx_produk_pembelian SET nama_produk = '$nama_produk', harga = '$harga', qty = '$qty', disc = '$disc', total_harga = '$total_setelah_disc', updated_date = '$time', updated_by = '$updated_by' WHERE id_trx_produk = '$id_trx'");
    if ($update) {
        $_SESSION['info'] = "Diupdate";
        header("Location:../detail-produk-pembelian-lokal.php?id='$id_inv_encode'");
    }
} else if (isset($_GET['hapus_trx'])) {
    $id_trx = base64_decode($_GET['hapus_trx']);
    $id_inv_pembelian = $_GET['id_inv'];

    $delete = mysqli_query($connect, "DELETE FROM trx_produk_pembelian WHERE id_trx_produk = '$id_trx'");

    if($delete){
        $_SESSION['info'] = "Dihapus";
        header("Location:../detail-produk-pembelian-lokal.php?id='$id_inv_pembelian'");
    } else {
        $_SESSION['info'] = "Data Gagal Dihapus";
        header("Location:../detail-produk-pembelian-lokal.php?id='$id_inv_pembelian'");
    }
}