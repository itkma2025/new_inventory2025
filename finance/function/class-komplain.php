<?php
class komplain {
    public static function getKondisi($komplain) {
        $alasan0 = "Faktur sesuai, tetapi barang yang diterima adalah jenis yang salah.";
        $alasan1 = "Faktur sesuai, namun jumlah barang yang diterima kurang dari yang diharapkan.";
        $alasan2 = "Faktur sesuai, tetapi pelanggan meminta revisi harga.";
        $alasan3 = "Faktur dan barang sesuai, tetapi barang yang diterima rusak, cacat, atau memiliki masalah kualitas sehingga tidak berfungsi sesuai yang diharapkan.";
        $alasan4 = "Faktur tidak sesuai, tetapi barang dan jumlahnya cocok dengan pesanan.";
        $alasan5 = "Pelanggan meminta pengembalian barang/uang karena ketidakcocokan pesanan.";
        if ($komplain == 5) {
            echo $alasan5;
        } elseif ($komplain == 4) {
            echo $alasan4;
        } elseif ($komplain == 3) {
            echo $alasan3;
        } elseif ($komplain == 2) {
            echo $alasan2;
        } elseif ($komplain == 1) {
            echo $alasan1;
        } else {
            echo $alasan0;
        }
    }
}
?>
