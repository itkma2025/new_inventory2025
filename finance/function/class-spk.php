<?php
class detailSpkFnc {
    public static function getDetail($nama_produk, $nama_set_marwa) {
        if(!empty($nama_produk)){
            return $nama_produk;
        } else {
            return $nama_set_marwa;
        }
    }
    public static function getSatuan($id_produk) {
        $id_produk_substr = substr($id_produk, 0, 2);
        $pcs = 'Pcs';
        $set = 'Set';

        if($id_produk_substr == 'BR'){
            return $pcs;
        } else {
            return $set;
        }
    }

    public static function getMerk($merk_produk, $merk_set) {
        if(!empty($merk_produk)){
            return $merk_produk;
        } else {
            return $merk_set;
        }
    }

    public static function getHarga($harga_produk, $harga_set_marwa) {
        if(!empty($harga_produk)){
            return $harga_produk;
        } else {
            return $harga_set_marwa;
        }
    }
}
?>