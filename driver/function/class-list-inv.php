<?php
class listInvoice {
    public static function getNoInvoice($no_inv_nonppn, $no_inv_ppn, $no_inv_bum) {
        if(!empty($no_inv_nonppn)){
            return $no_inv_nonppn;
        } else if(!empty($no_inv_ppn)){
            return $no_inv_ppn;
        } else {
            return $no_inv_bum;
        }
    }
    public static function getTglPesanan($tgl_pesanan_nonppn, $tgl_pesanan_ppn, $tgl_pesanan_bum) {
        if(!empty($tgl_pesanan_nonppn)){
            return $tgl_pesanan_nonppn;
        } else if(!empty($tgl_pesanan_ppn)){
            return $tgl_pesanan_ppn;
        } else {
            return $tgl_pesanan_bum;
        }
    }

    public static function getCsInvoice($cs_inv_nonppn, $cs_inv_ppn, $cs_inv_bum) {
        if(!empty($cs_inv_nonppn)){
            return $cs_inv_nonppn;
        } else if(!empty($cs_inv_ppn)){
            return $cs_inv_ppn;
        } else {
            return $cs_inv_bum;
        }
    }

    public static function getAlamat($alamat_nonppn, $alamat_ppn, $alamat_bum) {
        if(!empty($alamat_nonppn)){
            return $alamat_nonppn;
        } else if(!empty($alamat_ppn)){
            return $alamat_ppn;
        } else {
            return $alamat_bum;
        }
    }

    public static function getStatusTrx($status_trx_nonppn, $status_trx_ppn, $status_trx_bum) {
        if(!empty($status_trx_nonppn)){
            return $status_trx_nonppn;
        } else if(!empty($status_trx_ppn)){
            return $status_trx_ppn;
        } else {
            return $status_trx_bum;
        }
    }
    public static function getCreated($created_date_nonppn, $created_date_ppn, $created_date_bum) {
        if(!empty($created_date_nonppn)){
            return $created_date_nonppn;
        } else if(!empty($created_date_ppn)){
            return $created_date_ppn;
        } else {
            return $created_date_bum;
        }
    }
}
?>