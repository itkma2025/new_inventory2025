<?php
ob_start(); // Memulai output buffering
require_once "../akses.php";
require_once "../koneksi-ecat.php";
require_once "../function/function-enkripsi.php";
ob_clean(); // Membersihkan buffer

if (isset($_POST['id_trx'])) {
    $firstPart = htmlspecialchars($_POST['id_trx']);

    $query = $connect->query("SELECT qty FROM transaksi_produk_reg WHERE id_transaksi = '$firstPart'");
    if ($query) {
        $result = mysqli_fetch_array($query);
        if ($result) {
            $qty = $result['qty'];
            echo $qty; // Hanya mengirimkan angka qty tanpa HTML tambahan
        } else {
            echo "0"; // Default jika data tidak ditemukan
        }
    } else {
        $query_ecat = $connect->query("SELECT qty FROM $db_ecat.transaksi_produk_ecat WHERE id_transaksi_ecat = '$firstPart'");
        if ($query_ecat) {
            $result = mysqli_fetch_array($query_ecat);
            if ($result) {
                $qty = $result['qty'];
                echo $qty; // Hanya mengirimkan angka qty tanpa HTML tambahan
            } else {
                echo "0"; // Default jika data tidak ditemukan
            }
        } else {
            $query_pl = $connect->query("SELECT qty FROM $db_ecat.transaksi_produk_pl WHERE id_transaksi_pl = '$firstPart'");
            if ($query_pl) {
                $result = mysqli_fetch_array($query_pl);
                if ($result) {
                    $qty = $result['qty'];
                    echo $qty; // Hanya mengirimkan angka qty tanpa HTML tambahan
                } else {
                    echo "0"; // Default jika data tidak ditemukan
                }
            } else {
                echo "0"; // Default jika query gagal  
            }
        }
    }
} else {
    echo "0"; // Default jika tidak ada data dikirim
}
?>
