<?php  
    require_once '../akses.php';
    $id_user = decrypt($_SESSION['tiket_id'], $key_global);
    $sql_tagihan = "SELECT
                        COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                        cs.nama_cs,
                        cs.alamat,
                        bill.id_tagihan,
                        bill.no_tagihan, 
                        bill.tgl_tagihan, 
                        bill.total_tagihan, 
                        bill.id_driver,
                        bill.nama_penerima
                    FROM spk_reg AS spk
                    LEFT JOIN inv_nonppn nonppn ON (spk.id_inv = nonppn.id_inv_nonppn)
                    LEFT JOIN inv_ppn ppn ON (spk.id_inv = ppn.id_inv_ppn)
                    LEFT JOIN inv_bum bum ON (spk.id_inv = bum.id_inv_bum)
                    LEFT JOIN finance fnc ON (spk.id_inv = fnc.id_inv)
                    LEFT JOIN finance_tagihan bill ON (fnc.id_tagihan = bill.id_tagihan)
                    LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                    WHERE id_driver = '$id_user' AND bill.nama_penerima = ''";
?>