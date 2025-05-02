<?php  
    $sql_inv = "SELECT 
                    bum.id_inv_bum AS id_inv, 
                    bum.no_inv,
                    bum.alamat_inv,
                    cs.alamat
                FROM inv_bum AS bum
                LEFT JOIN spk_reg spk ON bum.id_inv_bum = spk.id_inv
                LEFT JOIN tb_customer cs ON spk.id_customer = cs.id_cs
                WHERE bum.id_inv_bum = '$id_inv_decrypt'";
    // Data Untuk Menampilkan detail
    $detail = $connect->query($sql_inv . " GROUP BY bum.id_inv_bum");
?>