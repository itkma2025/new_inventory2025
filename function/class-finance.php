<?php
class finance {
    public static function getNoInvoice($no_inv_nonppn, $no_inv_ppn, $no_inv_bum) {
        if(!empty($no_inv_nonppn)){
            return $no_inv_nonppn;
        } else if(!empty($no_inv_ppn)){
            return $no_inv_ppn;
        } else {
            return $no_inv_bum;
        }
    }
    public static function getTglPesanan($id_inv_nonppn, $id_inv_ppn, $id_inv_bum) {
        if(!empty($id_inv_nonppn)){
            return $id_inv_nonppn;
        } else if(!empty($id_inv_ppn)){
            return $id_inv_ppn;
        } else {
            return $id_inv_bum;
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

    public static function getTglInvoice($tgl_inv_nonppn, $tgl_inv_ppn, $tgl_inv_bum) {
        if(!empty($tgl_inv_nonppn)){
            return $tgl_inv_nonppn;
        } else if(!empty($tgl_inv_ppn)){
            return $tgl_inv_ppn;
        } else {
            return $tgl_inv_bum;
        }
    }

    public static function getTempoInvoice($tgl_tempo_nonppn, $tgl_tempo_ppn, $tgl_tempo_bum) {
        if(!empty($tgl_tempo_nonppn)){
            return $tgl_tempo_nonppn;
        } else if(!empty($tgl_tempo_ppn)){
            return $tgl_tempo_ppn;
        } else {
            return $tgl_tempo_bum;
        }
    }

    public static function getTotalInv($status_trx_nonppn, $status_trx_ppn, $status_trx_bum) {
        if(!empty($status_trx_nonppn)){
            return $status_trx_nonppn;
        } else if(!empty($status_trx_ppn)){
            return $status_trx_ppn;
        } else {
            return $status_trx_bum;
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
}
?>