<?php  
    $sql_inv = "SELECT 
                    ppn.id_inv_ppn AS id_inv, 
                    ppn.no_inv,
                    ppn.alamat_inv,
                    cs.alamat
                FROM inv_ppn AS ppn
                LEFT JOIN spk_reg spk ON ppn.id_inv_ppn = spk.id_inv
                LEFT JOIN tb_customer cs ON spk.id_customer = cs.id_cs
                WHERE ppn.id_inv_ppn = '$id_inv_decrypt'";
    // Data Untuk Menampilkan detail
    $detail = $connect->query($sql_inv . " GROUP BY ppn.id_inv_ppn");
?>